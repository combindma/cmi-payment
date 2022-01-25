<?php

return [
    /*
     * Identifiant du marchand (attribué par le CMI)
     * */
    'clientId' => env('CMI_CLIENT_ID', ''),

    /*
     * clé du magasin (configurée dans votre espace back office de la plate-forme CMI)
     * */
    'storeKey' => env('CMI_STORE_KEY', ''),

    /*
     * Modèle du paiement du marchand
     * */
    'storeType' => '3D_PAY_HOSTING',

    /*
     * Type de la transaction
     * */
    'tranType' => 'PreAuth',

    /*
    * La langue utilisée lors de l’affichage des pages de paiement. Valeurs possibles : ar, fr, en
    * */
    'lang' => env('CMI_DEFAULT_LANG', 'fr'),

    /*
    * Code ISO de la devise par défaut de la transaction
    * */
    'currency' => env('CMI_DEFAULT_CURRENCY', '504'),

    /*
     * Gateway de paiement en mode web (attribué par le CMI)
     * */
    'baseUri' => env('CMI_BASE_URI', 'https://testpayment.cmi.co.ma/fim/est3Dgate'),

    /*
     * L’URL utilisée pour rediriger le client vers le site marchand en cas d’autorisation de paiement acceptée.
     * */
    'okUrl' => env('CMI_OK_URL', ''),

    /*
     * L’URL utilisée pour rediriger le client vers le site marchand en cas d’autorisation de paiement échouée.
     * */
    'failUrl' => env('CMI_FAIL_URL', ''),

    /*
     * L'URL de retour vers laquelle le client est redirigé lorsqu'il clique sur le bouton "Annuler" affiché sur la page de paiement.
     * */
    'shopUrl' => env('CMI_SHOP_URL', ''),

    /*
     * L’URL utilisée dans la requête de confirmation de paiement en mode server-to-server
     * */
    'callbackUrl' => env('CMI_CALLBACK_URL', ''),

    /*
     * Activer/Désactiver la requête de confirmation de paiement en mode server-to-server
     * */
    'callbackResponse' => env('CMI_CALLBACK_RESPONSE', true),

    /*
     * Version du hachage
     * */
    'hashAlgorithm' => 'ver3',

    /*
     * Encodage des données de la requête de paiement
     * */
    'encoding' => 'UTF-8',

    /*
     * Utilisé pour rediriger le client automatiquement vers le site marchand lorsque la transaction de paiement en ligne est traitée.
     * */
    'autoRedirect' => env('CMI_AUTO_REDIRECT', true),

    /*
     * Permet de définir le délai d'expiration de la session de la page de paiement (en secondes).
     * */
    'sessionTimeout' => env('CMI_SESSION_TIMEOUT', '1800'),
];
