# Laravel package to communicate with the CMI payment plateform

[![Latest Version on Packagist](https://img.shields.io/packagist/v/combindma/cmi-payment.svg?style=flat-square)](https://packagist.org/packages/combindma/cmi-payment)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/combindma/cmi-payment/run-tests?label=tests)](https://github.com/combindma/cmi-payment/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/combindma/cmi-payment/Check%20&%20fix%20styling?label=code%20style)](https://github.com/combindma/cmi-payment/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/combindma/cmi-payment.svg?style=flat-square)](https://packagist.org/packages/combindma/cmi-payment)


## Installation

You can install the package via composer:

```bash
composer require combindma/cmi-payment
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="cmi-payment-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="cmi-payment-views"
```
## Configuration

You must provide all the required credentials in your .env file:

```php
CMI_CLIENT_ID= //Identifiant du marchand (attribué par le CMI)
CMI_STORE_KEY= //clé du magasin (configurée dans votre espace back office de la plate-forme CMI)
CMI_BASE_URI= //Gateway de paiement en mode web (attribué par le CMI). Exemple de test: https://testpayment.cmi.co.ma/fim/est3Dgate
CMI_OK_URL= //L’URL utilisée pour rediriger le client vers le site marchand en cas d’autorisation de paiement acceptée.
CMI_FAIL_URL= //L’URL utilisée pour rediriger le client vers le site marchand en cas d’autorisation de paiement échouée.
CMI_SHOP_URL= //L'URL de retour vers laquelle le client est redirigé lorsqu'il clique sur le bouton "Annuler" affiché sur la page de paiement.
CMI_CALLBACK_URL= //L’URL utilisée dans la requête de confirmation de paiement en mode server-to-server
```
See below how you can retrieve the okUrl, failUrl, shopUrl and callbackUrl.

## Usage

As an example, imagine you have an ecommerce website, and in your CheckoutController you want to add CMI Gateway. So you must add CmiGateway trait to that controller:

```php
class CheckoutController extends Controller {

    use \Combindma\Cmi\Traits\CmiGateway;
    
    /*
     * use this as an example of testing payment
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
        $cmiClient->disableCallbackRespense(); //Disable the call back responses, if you don't want to deal with callbackResponse.
        $otherData = [
            'billToStreet1' => 'Street Fighter', //be sure that the first letter of the key is not uppercase
            'billToCity' => 'Casanegra',
            'billToCountry' => 'Morocco',
            //etc...
        ];

        return $this->requestPayment($cmiClient, $otherData);
    }
}
```

In your routes file, list the routes needed by CMI like this:

```php
Route::get('/cmi/callback', [CheckoutController::class, 'callback']); //keep in mind you can use the path you want, but you should use the callback method implemented in CmiGateway Trait
Route::get('/cmi/okUrl', [CheckoutController::class, 'okUrl']);// in CmiGateway trait this method is empty so that you can implement your process after successful payment 
Route::get('/cmi/failUrl', [CheckoutController::class, 'failUrl']);// the fail url will redirect user to shopUrl with an error so that user can try to pay again 
Route::get('/url-of-checkout', [CheckoutController::class, 'yourMethod']);// as an example, this is the route where the user will click pay now (We recommand to use it as shopUrl, so we can redirect user back in failure)
```

Now, you can specify the configuration file correctly:

```dotenv
CMI_OK_URL=https://www.domain.com/cmi/okurl
CMI_FAIL_URL=https://www.domain.com/cmi/failurl
CMI_SHOP_URL=https://www.domain.com/url-of-checkout
CMI_CALLBACK_URL=https://www.domain.com/cmi/callback
```
In your checkoutController you should implement your own process after the payment is being handled well:

```php
class CheckoutController extends Controller {
    public function okUrl(Request $request)
    {
        //Look, in the orders’ DB for the record identified by the value of the "oid" parameter sent by the CMI platform in the request. And trait your order as you want.
    }
}
```

After that You are good to go.


Those are the available methods:

```php
$cmiClient = new Cmi();
// Dans Cmi, vous devez fournir un identifiant de la commande, sauf que dans la plupart des cas la commande est créée après le paiement de l'utilisateur
// donc à la place, vous pouvez utiliser soit un identifant de transaction ou l'identifiant du panier et ajouter 3 nombres aléatoires, et récupérer le panier actuel dans le callback en supprimant les 3 derniers chiffres.
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
$cmiClient->setOkUrl('https://domain.com/okurl');

//Remplacer L’URL par défaut  utilisée pour rediriger le client vers le site marchand en cas d’autorisation de paiement échouée.
$cmiClient->setFailUrl('https://domain.com/failurl');

//Remplacer L’URL par défaut de retour vers laquelle le client est redirigé lorsqu'il clique sur le bouton "Annuler" affiché sur la page de paiement.
$cmiClient->setShopUrl('https://domain.com/shopurl'); 

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
