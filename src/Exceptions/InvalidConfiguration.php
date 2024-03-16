<?php

namespace Combindma\Cmi\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function storeKeyNotSpecified(): static
    {
        return new static('Aucune clé de magasin (storeKey) n\'a été renseigné. Vous devez fournir une clé de magasin valide (configurée dans votre espace back office de la plate-forme CMI).');
    }

    public static function storeKeyInvalid(): static
    {
        return new static('La clé de magasin (storeKey) renseignée n\'est pas valide. Veuillez renseigner une clé de magasin qui ne contient aucun espace ou une chaîne de caractère vide.');
    }

    public static function clientIdNotSpecified(): static
    {
        return new static('Aucun identifiant du marchand (clientId) n\'a été renseigné. Vous devez fournir identifiant du marchand valide (attribué par le CMI)).');
    }

    public static function clientIdInvalid(): static
    {
        return new static('L\'identifiant du marchand (clientId) renseigné n\'est pas valide. Veuillez renseigner un identifiant du marchand qui ne contient aucun espace ou une chaîne de caractère vide.');
    }

    public static function attributeNotSpecified(string $attribute): static
    {
        return new static('Aucun(e) '.$attribute.' n\'a été renseigné(e). Veuillez le renseigner.');
    }

    public static function attributeInvalidString(string $attribute): static
    {
        return new static('La valeur de '.$attribute.' renseignée n\'est pas valide. Veuillez renseigner un(e) '.$attribute.' qui ne contient aucun espace ou une chaîne de caractère vide.');
    }

    public static function attributeInvalidUrl(string $attribute): static
    {
        return new static('L\'url '.$attribute.' renseigné n\'est pas valide. Veuillez renseigner un lien valide.');
    }

    public static function langValueInvalid(): static
    {
        return new static('La valeur de la langue par défaut n\'est pas valide. Valeurs possibles : ar, fr, en');
    }

    public static function sessionimeoutValueInvalid(): static
    {
        return new static('La valeur de délai d\'expiration de la session (sessionTimeout) n\'est pas valide? Veuillez renseigner un nombre valide. La valeur minimale autorisée est 30 secondes et la valeur maximale est 2700 secondes.');
    }
}
