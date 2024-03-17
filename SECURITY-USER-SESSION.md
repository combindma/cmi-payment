# Security Policy

## Supported Versions
Si vous souhaitez garder votre utilisateur connecté après le paiement, n'oubliez pas d'implémenter le token JWT de https://github.com/tymondesigns/jwt-auth. Ceci vous permettra de récupérer le token de session de la personne connectée 
avant de le paiement. Suivez les étapes suivantes pour le faire

- Installer le package : composer require tymon/jwt-auth
- Configurer votre fichier "config/app.php" suivants les recommandations du package
- Implémentez JWTSubject dans votre modèle User
- Créer le token en faisant : str_replace('=','aqwxszWZ',base64_encode(urlencode(JWTAuth::fromUser(Auth::user()))))
- Passez ce token dans le okUrl du CMI
- Et récupérez le token dans la fonction okUrl. $token = new Token(urldecode(base64_decode(str_replace('aqwxszWZ','=',$token))));
- Récupérez l'utilisateur en faisant :  $user = JWTAuth::decode($_usertoken)->toArray(); puis  $user = User::find($_user['sub']);
- Connectez-le à nouveau :Auth::loginUsingId($user->id);
