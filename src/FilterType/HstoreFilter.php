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
 * Class HstoreFilter
 *
 * @package NMarniesse\PommFilter\FilterType
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class HstoreFilter extends BasicFilter implements FilterInterface
{
    /**
     * @var string
     */
    protected $field_name;

    /**
     * BasicFilter constructor.
     *
     * @param string $name
     * @param string $table_name
     * @param string $field_name
     */
    public function __construct($name, $table_name, $field_name)
    {
        parent::__construct($name, $table_name, '=');
        $this->field_name = $field_name;
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
                    '%s%s?\'%s\'',
                    $this->table_name === '' ? '' : $this->table_name . '.',
                    $this->field_name,
                    $this->field_name
                )
            );
        } elseif ($value === FilterInterface::NULL_VALUE) {
            return Where::create(
                sprintf(
                    'NOT %s%s?\'%s\'',
                    $this->table_name === '' ? '' : $this->table_name . '.',
                    $this->field_name,
                    $this->field_name
                )
            );
        }

        return Where::create(
            sprintf(
                '%s%s->\'%s\' %s $*',
                $this->table_name === '' ? '' : $this->table_name . '.',
                $this->field_name,
                $this->field_name,
                $this->operator
            ),
            [$value]
        );
    }
}
