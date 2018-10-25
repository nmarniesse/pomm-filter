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
    protected $base_field_name;

    /**
     * BasicFilter constructor.
     *
     * @param string $hstore_key_name
     * @param string $base_field_name
     * @param string $table_name
     */
    public function __construct($hstore_key_name, $base_field_name, $table_name = '')
    {
        parent::__construct($hstore_key_name, $table_name, '=');
        $this->base_field_name = $base_field_name;
    }

    /**
     * getWhereWithNonArray
     *
     * @param $value
     * @return Where
     */
    protected function getWhereWithNonArray($value)
    {
        if ($value === FilterInterface::NOT_NULL_VALUE) {
            return Where::create(
                sprintf(
                    '%s%s?\'%s\'',
                    $this->table_name === '' ? '' : $this->table_name . '.',
                    $this->base_field_name,
                    $this->field_name
                )
            );
        } elseif ($value === FilterInterface::NULL_VALUE) {
            return Where::create(
                sprintf(
                    'NOT %s%s?\'%s\'',
                    $this->table_name === '' ? '' : $this->table_name . '.',
                    $this->base_field_name,
                    $this->field_name
                )
            );
        }

        return Where::create(
            sprintf(
                '%s%s->\'%s\' %s $*',
                $this->table_name === '' ? '' : $this->table_name . '.',
                $this->base_field_name,
                $this->field_name,
                $this->operator
            ),
            [$value]
        );
    }
}
