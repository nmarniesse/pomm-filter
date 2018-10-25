<?php
/*
 * This file is part of wall-e package.
 *
 * (c) 2018 Son-Video Distribution
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NMarniesse\PommFilter\Test\Unit;

use atoum\test;
use NMarniesse\PommFilter\FilterCollection as TestedClass;
use NMarniesse\PommFilter\FilterInterface;
use NMarniesse\PommFilter\FilterType\BooleanFilter;
use NMarniesse\PommFilter\FilterType\DateTimeFilter;
use NMarniesse\PommFilter\FilterType\HstoreFilter;
use NMarniesse\PommFilter\FilterType\LtreeFilter;
use PommProject\Foundation\Where;

/**
 * Class FilterCollection
 *
 * @package NMarniesse\PommFilter\Test\Unit
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class FilterCollection extends test
{
    /**
     * testGetWhere
     */
    public function testGetWhere()
    {
        $this
            ->assert('Get condition with simple filters.')
            ->given($tested_instance = new TestedClass(''))
            ->and($filters = ['a' => 1, 'b' => 2])
            ->when($where = $tested_instance->getWhere($filters))
                ->object($where)->isInstanceOf(Where::class)
                    ->string($where->__toString())->isEqualTo('(a = $* AND b = $*)')
                    ->array($where->getValues())->isEqualTo([1, 2])
            ;
    }

    /**
     * testGetWhereWithComplexFilters
     */
    public function testGetWhereWithComplexFilters()
    {
        $this
            ->assert('Get condition with complex filters.')
            ->given($tested_instance = new TestedClass(''))
            ->and($tested_instance->addFilter(new LtreeFilter('z', 'alias'), 'a'))
            ->and($tested_instance->addFilter(new BooleanFilter('c', 'alias')))
            ->and($tested_instance->addFilter(new HstoreFilter('d', 'alias', 'field')))
            ->and($tested_instance->addFilter(new DateTimeFilter('e', 'alias2')))
            ->and($filters = [
                'a' => 1,
                'b' => FilterInterface::NULL_VALUE,
                'c' => 0,
                'd' => 'test',
                'e' => '2018-08-24T12:00:00+02',
            ])
            ->when($where = $tested_instance->getWhere($filters))
                ->object($where)->isInstanceOf(Where::class)
                    ->string($where->__toString())
                    ->isEqualTo(
                        "(alias.z ~ $* AND b IS NULL AND NOT alias.c AND alias.field->'d' = $* AND alias2.e = $*)"
                    )
                    ->array($where->getValues())->isEqualTo([1, 'test', '2018-08-24T12:00:00+02:00'])
            ;
    }
}
