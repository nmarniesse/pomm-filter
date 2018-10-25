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
 * Class BooleanFilter
 *
 * @package NMarniesse\PommFilter\FilterType
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class BooleanFilter extends BasicFilter implements FilterInterface
{
    /**
     * BooleanFilter constructor.
     *
     * @param string $field_name
     * @param string $table_name
     */
    public function __construct($field_name, $table_name = '')
    {
        parent::__construct($field_name, $table_name);
    }

    /**
     * getWhereWithSimpleValue
     *
     * @param $value
     * @return Where
     */
    protected function getWhereWithSimpleValue($value)
    {
        return Where::create(sprintf(
            '%s%s%s',
            in_array($value, [false, 'inactive', 'false', '0', 0], true) ? 'NOT ' : '',
            $this->table_name === '' ? '' : $this->table_name . '.',
            $this->field_name
        ));
    }
}
