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
 * Class LtreeFilter
 *
 * @package NMarniesse\PommFilter\FilterType
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class LtreeFilter extends BasicFilter implements FilterInterface
{
    /**
     * LtreeFilter constructor.
     *
     * @param string $name
     * @param string $table_name
     * @param string $operator
     */
    public function __construct($name, $table_name, $operator = '~')
    {
        parent::__construct($name, $table_name, $operator);
    }

    /**
     * getWhereWithSimpleValue
     *
     * With operator '~' request will be: column ~ <value>.*
     * With that, the filter value can be:
     *  - a.b    (all location under a.b and a.b itself)
     *  - a.b.*  (all location under a.b and a.b itself)
     *  - *.short_transit    (all location in short_transit)
     *  - *.short_transit.*  (all location in short_transit)
     *
     * @param mixed $value
     * @return Where
     */
    protected function getWhereWithSimpleValue($value): Where
    {
        if (strlen($value) < 2 || (substr($value, -2) !== '.*' && $this->operator = '~')) {
            $value .= '.*';
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
