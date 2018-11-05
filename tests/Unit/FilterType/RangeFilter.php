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
use NMarniesse\PommFilter\FilterType\RangeFilter as TestedClass;
use NMarniesse\PommFilter\RangeValue;

/**
 * Class RangeFilter
 *
 * @package NMarniesse
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class RangeFilter extends test
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
            ->assert('Get condition with simple value.')
            ->given($instance = new TestedClass('field'))
            ->when($where = $instance->getWhere(5))
                ->string((string) $where)->isEqualTo('field @> $*')
                ->array($where->getValues())->isEqualTo([5])

            ->assert('Get condition with a range.')
            ->given($instance = new TestedClass('field'))
            ->when($where = $instance->getWhere(new RangeValue(1, 3)))
                ->string((string) $where)->isEqualTo('field @> $*')
                ->array($where->getValues())->isEqualTo(['[1, 3]'])

            ->assert('Get condition with a range of dates.')
            ->given($instance = new TestedClass('field'))
            ->when($where = $instance->getWhere(new RangeValue(
                new \DateTime('2010-01-01 00:00:00+00'),
                new \DateTime('2010-12-31 23:59:59+00')
            )))
                ->string((string) $where)->isEqualTo('field @> $*')
                ->array($where->getValues())->isEqualTo(['[2010-01-01 00:00:00+00:00, 2010-12-31 23:59:59+00:00]'])

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
