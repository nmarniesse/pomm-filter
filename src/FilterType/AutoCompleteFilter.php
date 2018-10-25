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
     * BasicFilter constructor.
     *
     * @param string $name
     * @param string $table_name
     */
    public function __construct($name, $table_name)
    {
        parent::__construct($name, $table_name, 'ILIKE');
    }

    /**
     * getWhereWithSimpleValue
     *
     * @param mixed $value
     * @return Where
     */
    protected function getWhereWithSimpleValue($value): Where
    {
        if (substr($value, -1) !== '%') {
            $value = $value . '%';
        }

        return Where::create(
            sprintf(
                '%s%s ILIKE $*',
                $this->table_name === '' ? '' : $this->table_name . '.',
                $this->field_name
            ),
            [$value]
        );
    }
}
