<?php

namespace Amenessisse\PackageHeoAPI\QueryBuilder;

class ProductQueryBuilder 
{
    /** @var array */
    private $conditions = [];

    /** @var string */
    private $operator = 'and';

    /**
     * Ajoute une condition au constructeur de requête
     * 
     * @param string $field Le champ sur lequel filtrer
     * @param string $operator L'opérateur de comparaison
     * @param mixed $value La valeur de filtre
     * 
     * @return $this
     */
    public function where(string $field, string $operator, $value): self
    {
        $this->conditions[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
            'logic' => $this->operator,
        ];

        return $this;
    }

    /**
     * Ajoute une condition "OR" au constructeur de requête
     * 
     * @param string $field Le champ sur lequel filtrer
     * @param string $operator L'opérateur de comparaison
     * @param mixed $value La valeur de filtre
     * 
     * @return $this
     */
    public function orWhere(string $field, string $operator, $value): self
    {
        $prevOperator   = $this->operator;
        $this->operator = 'or';
        $this->where($field, $operator, $value);
        $this->operator = $prevOperator;

        return $this;
    }

    /** Shorthand pour un filtrage sur le champ ageRating */
    public function whereAgeRating(string $operator, $value): self
    {
        return $this->where(ProductQueryFields::FIELD_AGE_RATING, $operator, $value);
    }

    /** Shorthand pour un filtrage sur le champ manufacturer */
    public function whereManufacturer(string $operator, $value): self
    {
        return $this->where(ProductQueryFields::FIELD_MANUFACTURER, $operator, $value);
    }

    /**
     * Construit la chaîne de requête finale
     * 
     * @return string La chaîne de requête formatée
     */
    public function build(): string
    {
        if (empty($this->conditions)) {
            return '';
        }

        $parts          = [];
        $firstCondition = true;
        
        foreach ($this->conditions as $condition) {
            $formattedValue = $this->formatValue($condition['value']);
            
            if ($firstCondition) {
                $parts[] = "{$condition['field']}{$condition['operator']}{$formattedValue}";
                $firstCondition = false;
            } else {
                $parts[] = "{$condition['logic']} {$condition['field']}{$condition['operator']}{$formattedValue}";
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Formate la valeur en fonction de son type
     * 
     * @param mixed $value La valeur à formater
     *
     * @return string La valeur formatée
     */
    private function formatValue($value): string
    {
        if (is_array($value)) {
            $formattedValues = [];
            foreach ($value as $item) {
                $formattedValues[] = $this->formatValue($item);
            }
            return '(' . implode(',', $formattedValues) . ')';
        }

        if (is_string($value) && (strpos($value, ' ') !== false || strpos($value, ',') !== false)) {
            return '"' . $value . '"';
        }
        
        return (string) $value;
    }
}