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
 * Class DateTimeFilter
 *
 * @package NMarniesse\PommFilter\FilterType
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class DateTimeFilter extends BasicFilter implements FilterInterface
{
    /**
     * getWhereWithSimpleValue
     *
     * @param $value
     * @return Where
     */
    protected function getWhereWithSimpleValue($value)
    {
        return Where::create(
            sprintf(
                '%s%s %s $*',
                $this->table_name === '' ? '' : $this->table_name . '.',
                $this->field_name,
                $this->operator
            ),
            [(new \DateTime($value))->format('c')]
        );
    }
}
