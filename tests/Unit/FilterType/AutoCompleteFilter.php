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
use NMarniesse\PommFilter\FilterType\AutoCompleteFilter as TestedClass;

/**
 * Class AutoCompleteFilter
 *
 * @package NMarniesse
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class AutoCompleteFilter extends test
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
            ->when($where = $instance->getWhere(['value1']))
                ->string((string) $where)->isEqualTo('field ~* $*')
                ->array($where->getValues())->isEqualTo(['value1'])

            ->assert('Get condition with multiple value.')
            ->given($instance = new TestedClass('field'))
            ->when($where = $instance->getWhere(['value1', 'value2']))
                ->string((string) $where)->isEqualTo('(field ~* $* OR field ~* $*)')
                ->array($where->getValues())->isEqualTo(['value1', 'value2'])

            ->assert('Get condition with specific table name.')
            ->given($instance = new TestedClass('field', 't'))
            ->when($where = $instance->getWhere(['value1', 'value2']))
                ->string((string) $where)->isEqualTo('(t.field ~* $* OR t.field ~* $*)')
                ->array($where->getValues())->isEqualTo(['value1', 'value2'])
        ;
    }
}
