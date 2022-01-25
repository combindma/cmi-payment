<?php

namespace Combindma\Cmi\Exceptions;

use Exception;

class InvalidRequest extends Exception
{
    public static function amountNotSpecified(): static
    {
        return new static('Aucun montant n\'a été renseigné. Veuilez renseigner un montant de la transaction.');
    }

    public static function amountValueInvalid(): static
    {
        return new static('Le montant de la transaction saisie est invalide. Veuillez renseigner une valeur numérique du montant sans symbole monétaire. Utilisez « . » ou « , » pour le séparateur du décimal.');
    }

    public static function currencyNotSpecified(): static
    {
        return new static('Aucun code de devise n\'a été renseigné. Veuilez renseigner un code ISO de la devise de la transaction.');
    }

    public static function currencyValueInvalid(): static
    {
        return new static('Le code de devise renseigné est invalide. Veuillez renseigner un code numérique ISO 4217 de la devise. Code ISO du MAD : 504');
    }

    public static function attributeNotSpecified(string $attribute): static
    {
        return new static('Aucun(e) '.$attribute.' n\'a été renseigné(e). Veuillez le renseigner.');
    }

    public static function attributeInvalidString(string $attribute): static
    {
        return new static('La valeur de '.$attribute.' renseignée n\'est pas valide. Veuillez reseigner un(e) '.$attribute.' qui ne contient aucun espace ou une chaîne de caractère vide.');
    }

    public static function emailValueInvalid(): static
    {
        return new static('L\'adresse email du client renseignée n\'est pas une adresse électronique valide.');
    }
}
