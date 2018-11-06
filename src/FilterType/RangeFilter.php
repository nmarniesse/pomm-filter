<?php
/*
 * This file is part of pomm-filter package.
 *
 * (c) 2018 Son-Video Distribution
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NMarniesse\PommFilter\FilterType;

use NMarniesse\PommFilter\ValueType\RangeValue;
use PommProject\Foundation\Where;

/**
 * Class RangeFilter
 *
 * @package NMarniesse\PommFilter\FilterType
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class RangeFilter extends BasicFilter
{
    /**
     * RangeFilter constructor.
     *
     * @param string $field_name
     * @param string $table_name
     * @param string $operator
     */
    public function __construct($field_name, $table_name = '', $operator = '@>')
    {
        parent::__construct($field_name, $table_name, $operator);
    }

    /**
     * getWhereWithSimpleValue
     *
     * @param $value
     * @return Where
     */
    protected function getWhereWithSimpleValue($value)
    {
        if ($value instanceof RangeValue) {
            $value = $value->getFormattedForDb();
        } elseif ($value instanceof \DateTime) {
            $value = (new \DateTime($value))->format('c');
        }

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
