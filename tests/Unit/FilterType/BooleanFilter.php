<?php
/*
 * This file is part of pomm-filter package.
 *
 * (c) 2018 Son-Video Distribution
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NMarniesse\PommFilter\Test\Unit\FilterType;

use atoum\test;
use NMarniesse\PommFilter\FilterInterface;
use NMarniesse\PommFilter\FilterType\BooleanFilter as TestedClass;

/**
 * Class BooleanFilter
 *
 * @package NMarniesse
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class BooleanFilter extends test
{
    /**
     * testGetFieldName
     */
    public function testGetFieldName()
    {
        $this
            ->assert('Get field name.')
            ->given($instance = new TestedClass('field'))
            ->when($field_name = $instance->getFieldName())
                ->string($field_name)->isEqualTo('field')
        ;
    }

    /**
     * testGetWhere
     */
    public function testGetWhere()
    {
        $this
            ->assert('Get condition with value and table name.')
            ->given($instance = new TestedClass('field', 't'))
            ->when($where = $instance->getWhere([true]))
                ->string((string) $where)->isEqualTo('t.field')
                ->array($where->getValues())->isEqualTo([])

            ->assert('Get condition with null.')
            ->given($instance = new TestedClass('field', 't'))
            ->when($where = $instance->getWhere([FilterInterface::NULL_VALUE]))
                ->string((string) $where)->isEqualTo('t.field IS NULL')
                ->array($where->getValues())->isEqualTo([])

            ->assert('Get condition with not null.')
            ->given($instance = new TestedClass('field', 't'))
            ->when($where = $instance->getWhere([FilterInterface::NOT_NULL_VALUE]))
                ->string((string) $where)->isEqualTo('t.field IS NOT NULL')
                ->array($where->getValues())->isEqualTo([])
        ;
    }
}
