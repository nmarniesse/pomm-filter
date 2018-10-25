<?php
/*
 * This file is part of nmarniesse/pomm-filter package.
 *
 * (c) 2018 Nicolas Marniesse <nicolas.marniesse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NMarniesse\PommFilter\FilterType;

use PommProject\Foundation\Where;
use NMarniesse\PommFilter\FilterInterface;

/**
 * Class BasicFilter
 *
 * @package NMarniesse\PommFilter\FilterType
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class BasicFilter implements FilterInterface
{
    /**
     * @var string
     */
    protected $field_name;

    /**
     * @var string
     */
    protected $table_name;

    /**
     * @var string
     */
    protected $operator;

    /**
     * BasicFilter constructor.
     *
     * @param string $field_name
     * @param string $table_name
     * @param string $operator
     */
    public function __construct($field_name, $table_name = '', $operator = '=')
    {
        $this->field_name = $field_name;
        $this->table_name = $table_name;
        $this->operator   = $operator;
    }

    /**
     * getFieldName
     *
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->field_name;
    }

    /**
     * getWhere
     *
     * @param mixed $value
     * @return Where
     */
    public function getWhere($value): Where
    {
        return is_array($value) ? $this->getWhereWithArray($value) : $this->getWhereWithNonArray($value);
    }

    /**
     * getWhereWithArray
     *
     * @param array $values
     * @return Where
     */
    protected function getWhereWithArray($values): Where
    {
        $where = new Where();
        foreach ($values as $value) {
            $where->orWhere($this->getWhereWithNonArray($value));
        }

        return $where;
    }

    /**
     * getWhereWithNonArray
     *
     * @param $value
     * @return Where
     */
    protected function getWhereWithNonArray($value): Where
    {
        if ($value === FilterInterface::NOT_NULL_VALUE) {
            return Where::create(
                sprintf(
                    '%s%s IS NOT NULL',
                    $this->table_name === '' ? '' : $this->table_name . '.',
                    $this->field_name
                )
            );
        } elseif ($value === FilterInterface::NULL_VALUE) {
            return Where::create(
                sprintf(
                    '%s%s IS NULL',
                    $this->table_name === '' ? '' : $this->table_name . '.',
                    $this->field_name
                )
            );
        }

        return $this->getWhereWithSimpleValue($value);
    }

    /**
     * getWhereWithSimpleValue
     *
     * @param mixed $value
     * @return Where
     */
    protected function getWhereWithSimpleValue($value): Where
    {
        return Where::create(
            sprintf(
                '%s%s %s $*',
                $this->table_name === '' ? '' : $this->table_name . '.',
                $this->field_name,
                $this->operator
            ),
            [$value]
        );
    }
}
