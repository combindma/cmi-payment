# Package Laravel pour communiquer avec la plateforme de paiement CMI

[![Latest Version on Packagist](https://img.shields.io/packagist/v/combindma/cmi-payment.svg?style=flat-square)](https://packagist.org/packages/combindma/cmi-payment)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/combindma/cmi-payment/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/combindma/cmi-payment/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/combindma/cmi-payment/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/combindma/cmi-payment/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/combindma/cmi-payment.svg?style=flat-square)](https://packagist.org/packages/combindma/cmi-payment)


## Installation

Vous pouvez installer le package via composer :

```bash
composer require combindma/cmi-payment
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="cmi-payment-config"
```

Optionnellement, vous pouvez publier les vues en utilisant :

```bash
php artisan vendor:publish --tag="cmi-payment-views"
```

## Configuration

Vous devez fournir toutes les informations d'identification requises dans votre fichier .env :

```php
CMI_CLIENT_ID= //Identifiant du marchand (attribué par le CMI)
CMI_STORE_KEY= //clé du magasin (configurée dans votre espace back office de la plate-forme CMI)
CMI_BASE_URI= //Gateway de paiement en mode web (attribué par le CMI). Exemple de test: https://testpayment.cmi.co.ma/fim/est3Dgate
CMI_OK_URL= //L’URL utilisée pour rediriger le client vers le site marchand en cas d’autorisation de paiement acceptée.
CMI_FAIL_URL= //L’URL utilisée pour rediriger le client vers le site marchand en cas d’autorisation de paiement échouée.
CMI_SHOP_URL= //L'URL de retour vers laquelle le client est redirigé lorsqu'il clique sur le bouton "Annuler" affiché sur la page de paiement.
CMI_CALLBACK_URL= //L’URL utilisée dans la requête de confirmation de paiement en mode server to server
```
Voir ci-dessous comment vous pouvez récupérer les okUrl, failUrl, shopUrl et callbackUrl.

## Utilisation

Par exemple, imaginez que vous avez un site de commerce électronique, et dans votre CheckoutController, vous voulez ajouter CMI Gateway. Vous devez donc ajouter la trait CmiGateway à ce contrôleur :

```php
class CheckoutController extends Controller {

    use \Combindma\Cmi\Traits\CmiGateway;
    
    /*
     * utilisez ceci comme exemple de test de paiement
     * */
    public function testCmiPayment()
    {
        $cmiClient = new Cmi();
        $cmiClient->setOid(date('dmY').rand(10, 1000));
        $cmiClient->setAmount(10.25);
        $cmiClient->setBillToName('name');
        $cmiClient->setEmail('email@domaine.com');
        $cmiClient->setTel('0021201020304');
        $cmiClient->setCurrency('504');
        $cmiClient->setDescription('ceci est un exemple à utiliser');
        $cmiClient->disableCallbackRespense(); //Désactivez les réponses de rappel, si vous ne voulez pas traiter la callbackResponse.
        $otherData = [
            'billToStreet1' => 'Street Fighter',
            'billToCity' => 'Casanegra',
            'billToCountry' => 'Morocco',
            //etc...
        ];

        return $this->requestPayment($cmiClient, $otherData);
    }
}
```

Dans votre fichier de routes, listez les routes nécessaires à CMI comme ceci :

```php
Route::post('/cmi/callback', [CheckoutController::class, 'callback'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class); //notez que vous pouvez utiliser le chemin que vous voulez, mais vous devez utiliser la méthode de rappel (callback) implémentée dans la trait CmiGateway
Route::post('/cmi/okUrl', [CheckoutController::class, 'okUrl'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);// dans la trait CmiGateway, cette méthode est vide pour que vous puissiez implémenter votre propre processus après un paiement réussi
Route::post('/cmi/failUrl', [CheckoutController::class, 'failUrl'])->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);// la fail url redirigera l'utilisateur vers shopUrl avec une erreur pour que l'utilisateur puisse essayer de payer à nouveau
Route::get('/url-of-checkout', [CheckoutController::class, 'yourMethod']);// Par exemple, c'est la route où l'utilisateur cliquera sur "Payer maintenant. "( Nous recommandons de l'utiliser comme shopUrl, afin de pouvoir rediriger l'utilisateur en cas d'échec du paiement)


```

Maintenant, vous pouvez spécifier correctement le fichier de configuration :

```dotenv
CMI_OK_URL=https://www.domain.com/cmi/okurl
CMI_FAIL_URL=https://www.domain.com/cmi/failurl
CMI_SHOP_URL=https://www.domain.com/url-of-checkout
CMI_CALLBACK_URL=https://www.domain.com/cmi/callback
```

Dans votre CheckoutController, vous devez implémenter votre propre processus une fois que le paiement a été traité avec succès :

```php
class CheckoutController extends Controller {
    public function okUrl(Request $request)
    {
        // Cherchez dans la base de données des commandes pour l'enregistrement identifié par la valeur du paramètre "oid" envoyé par la plateforme CMI dans la requête. Traitez votre commande comme vous le souhaitez.
    }
}
```

Après cela, vous êtes prêt à accepter les paiements.


Voici les méthodes disponibles :

```php
$cmiClient = new Cmi();
// Dans Cmi, vous devez fournir un identifiant de la commande, sauf que dans la plupart des cas la commande est créée après le paiement de l'utilisateur
// donc à la place, vous pouvez utiliser soit un identifiant de transaction ou l'identifiant du panier et ajouter 3 nombres aléatoires, et récupérer le panier actuel dans le callback en supprimant les 3 derniers chiffres.
// La valeur de oid doit être unique pour chaque transaction. Parce que si l'utilisateur clique sur revenir en arrière sans payer. Vous ne pouvez pas utiliser le même identifiant de transaction (Allez comprendre)
$cmiClient->setOid($cart->id.rand(100,900));

//ajouter email client 
$cmiClient->setEmail('email@domain.com'); 

//ajouter nom client
$cmiClient->setBillToName('Nom client'); 

//ajouter tel client
$cmiClient->setTel('0021201020304');

//La langue utilisée lors de l’affichage des pages de paiement. Valeurs possibles : ar, fr, en.
$cmiClient->setLang('fr');

//Code ISO de la devise par défaut de la transaction
$cmiClient->setCurrency('504');

//Utilisé pour afficher (ou non) la liste des devises de change dans les pages de paiement. (Voir documentation CMI)
$cmiClient->enableCurrenciesList();

//La conversion du montant dans une devise étrangère, à montrer au client dans la page de paiement. (Voir documentation CMI)
$cmiClient->setAmountCur(10.25); 

//Symbole de la devise de conversion à afficher dans la page de paiement avec la valeur du paramètre "amountCur". (Voir documentation CMI)
$cmiClient->setSymbolCur('EUR');

//Description envoyée à l’MPI
$cmiClient->setDescription('add your description');

//Remplacer L’URL par défaut utilisée pour rediriger le client vers le site marchand en cas d’autorisation de paiement acceptée.
$cmiClient->setOkUrl('https://domain.com/cmi/okurl');

//Remplacer L’URL par défaut  utilisée pour rediriger le client vers le site marchand en cas d’autorisation de paiement échouée.
$cmiClient->setFailUrl('https://domain.com/cmi/failurl');

//Remplacer L’URL par défaut de retour vers laquelle le client est redirigé lorsqu'il clique sur le bouton "Annuler" affiché sur la page de paiement.
$cmiClient->setShopUrl('https://domain.com/cmi/shopurl'); 

//Remplacer L’URL utilisée dans la requête de confirmation de paiement en mode server-to-server
$cmiClient->setCallbackUrl('https://domain.com/callback'); 

//Activer la redirection automatiquement du client vers le site marchand lorsque la transaction de paiement en ligne est traitée. (par défaut activé)
$cmiClient->enableAutoRedirect();

//Désactiver la redirection automatiquement du client vers le site marchand lorsque la transaction de paiement en ligne est traitée. (par défaut activé)
$cmiClient->disableAutoRedirect();

//Activer la requête de confirmation de paiement en mode server-to-server (par défaut activé)
$cmiClient->enableCallbackRespense(); 

//Désactiver la requête de confirmation de paiement en mode server-to-server (par défaut activé)
$cmiClient->disableCallbackRespense(); 

//Permet de définir le délai d'expiration de la session de la page de paiement (en secondes). La valeur minimale autorisée est 30 secondes et la valeur maximale est 2700 secondes.
$cmiClient->setSessionTimeout(1800);
```

## Testing

```bash
composer test
```


## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Combind](https://github.com/combindma)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
