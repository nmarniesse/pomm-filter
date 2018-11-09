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
 * Class AutoCompleteFilter
 *
 * @package NMarniesse\PommFilter\FilterType
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class AutoCompleteFilter extends BasicFilter implements FilterInterface
{
    /**
     * AutoCompleteFilter constructor.
     *
     * @param string $field_name
     * @param string $table_name
     * @param string $operator
     */
    public function __construct($field_name, $table_name = '', $operator = 'ILIKE')
    {
        parent::__construct($field_name, $table_name, $operator);
    }

    /**
     * getWhereWithSimpleValue
     *
     * @param mixed $value
     * @return Where
     */
    protected function getWhereWithSimpleValue($value)
    {
        if (in_array(strtolower($this->operator), ['like', 'ilike', '~~', '~~*'])) {
            $value .= '%';
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
