<?php

namespace Amenessisse\PackageHeoAPI\QueryBuilder;

use InvalidArgumentException;

class ProductQueryBuilder
{
    /**
     * Liste des opérateurs valides
     */
    const VALID_OPERATORS = ['==', '!=', '=lt=', '=le=', '=gt=', '=ge=', '=in='];

    /**
     * Tableau des conditions à appliquer.
     * Chaque condition sera de la forme "champ opérateur valeur", par exemple "ageRating==1".
     *
     * @var array<string>
     */
    private $conditions = [];

    /**
     * Ajoute une condition.
     *
     * @param string $field     Le nom du champ (ex. 'ageRating', 'productNumber')
     * @param string $operator  L'opérateur (ex. '==', '!=', '=lt=', '=gt=', '=in=')
     * @param mixed  $value     La valeur à comparer
     *
     * @throws InvalidArgumentException Si l'opérateur n'est pas valide
     * @return self
     */
    public function where(string $field, string $operator, $value): self
    {
        $this->validateOperator($operator);
        $this->validateField($field);
        
        $this->conditions[] = $this->buildCondition($field, $operator, $value);

        return $this;
    }

    /**
     * Ajoute une condition avec "or".
     * La condition sera automatiquement combinée avec la condition précédente via un OR.
     *
     * @param string $field     Le nom du champ
     * @param string $operator  L'opérateur
     * @param mixed  $value     La valeur à comparer
     *
     * @throws InvalidArgumentException Si l'opérateur n'est pas valide
     * @return self
     */
    public function orWhere(string $field, string $operator, $value): self
    {
        $this->validateOperator($operator);
        $this->validateField($field);

        $newCondition = $this->buildCondition($field, $operator, $value);

        if (! empty($this->conditions)) {
            $lastCondition = array_pop($this->conditions);
            $this->conditions[] = sprintf("(%s) or (%s)", $lastCondition, $newCondition);
        } else {
            $this->conditions[] = $newCondition;
        }

        return $this;
    }

    /**
     * Construit la query finale.
     *
     * @return string La query à utiliser pour les appels de l'API.
     */
    public function build(): string
    {
        return implode(' and ', $this->conditions);
    }

    /**
     * Vérifie si l'opérateur est valide.
     *
     * @param string $operator L'opérateur à vérifier
     * @throws InvalidArgumentException
     */
    private function validateOperator(string $operator)
    {
        if (!in_array($operator, self::VALID_OPERATORS, true)) {
            throw new InvalidArgumentException(
                sprintf('Opérateur invalide : %s. Les opérateurs valides sont : %s',
                    $operator,
                    implode(', ', self::VALID_OPERATORS)
                )
            );
        }
    }

    /**
     * Vérifie si le champ n'est pas vide.
     *
     * @param string $field Le champ à vérifier
     * @throws InvalidArgumentException
     */
    private function validateField(string $field)
    {
        if (empty(trim($field))) {
            throw new InvalidArgumentException('Le champ ne peut pas être vide');
        }
    }

    /**
     * Construit une condition unique.
     *
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return string
     */
    private function buildCondition(string $field, string $operator, $value): string
    {
        return sprintf("%s%s%s", $field, $operator, $value);
    }
}