<?php

namespace Amenessisse\PackageHeoAPI\QueryBuilder;

class ProductQueryFields
{
    // Champs supportés
    const FIELD_PRODUCT_NUMBER = 'productNumber';
    const FIELD_AGE_RATING = 'ageRating';
    const FIELD_BARCODE = 'barcode';
    const FIELD_CATEGORY = 'category';
    const FIELD_THEME = 'theme';
    const FIELD_MANUFACTURER = 'manufacturer';
    const FIELD_TYPE = 'type';
    const FIELD_SPECIAL = 'special';

    // Liste des champs valides
    const VALID_FIELDS = [
        self::FIELD_PRODUCT_NUMBER,
        self::FIELD_AGE_RATING,
        self::FIELD_BARCODE,
        self::FIELD_CATEGORY,
        self::FIELD_THEME,
        self::FIELD_MANUFACTURER,
        self::FIELD_TYPE,
        self::FIELD_SPECIAL
    ];

    // Opérateurs de jointure
    const JOIN_AND = 'and';
    const JOIN_OR = 'or';

    // Opérateurs de comparaison
    const OPERATOR_EQUALS = '==';
    const OPERATOR_NOT_EQUALS = '!=';
    const OPERATOR_LESS_THAN = '=lt=';
    const OPERATOR_LESS_EQUALS = '=le=';
    const OPERATOR_GREATER_THAN = '=gt=';
    const OPERATOR_GREATER_EQUALS = '=ge=';
    const OPERATOR_IN = '=in=';

    // Liste des opérateurs valides
    const VALID_OPERATORS = [
        self::OPERATOR_EQUALS,
        self::OPERATOR_NOT_EQUALS,
        self::OPERATOR_LESS_THAN,
        self::OPERATOR_LESS_EQUALS,
        self::OPERATOR_GREATER_THAN,
        self::OPERATOR_GREATER_EQUALS,
        self::OPERATOR_IN
    ];
}