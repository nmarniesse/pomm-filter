<?php
/*
 * This file is part of nmarniesse/pomm-filter package.
 *
 * (c) 2018 Nicolas Marniesse <nicolas.marniesse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NMarniesse\PommFilter;

use PommProject\Foundation\Where;

/**
 * Interface FilterInterface
 *
 * @package NMarniesse\PommFilter
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
interface FilterInterface
{
    const NULL_VALUE     = '_null_';
    const NOT_NULL_VALUE = '_not_null_';

    /**
     * getFieldName
     *
     * @return string
     */
    public function getFieldName(): string;

    /**
     * getWhere
     *
     * @param mixed $value
     * @return Where
     */
    public function getWhere($value): Where;
}
