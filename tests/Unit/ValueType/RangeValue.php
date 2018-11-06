<?php
/*
 * This file is part of pomm-filter package.
 *
 * (c) 2018 Son-Video Distribution
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NMarniesse\PommFilter\Test\Unit\ValueType;

use atoum\test;
use NMarniesse\PommFilter\ValueType\RangeValue as TestedClass;

/**
 * Class RangeValue
 *
 * @package NMarniesse\PommFilter\Test\Unit\ValueType
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class RangeValue extends test
{
    /**
     * testConstruct
     */
    public function testConstruct()
    {
        $this
            ->assert('Construct instance with 2 numbers.')
            ->when($instance = new TestedClass(2.5, '10'))
                ->object($instance)->isInstanceOf('NMarniesse\PommFilter\ValueType\RangeValue')

            ->assert('Construct instance with 2 DateTime.')
            ->when($instance = new TestedClass(new \DateTime(), new \DateTime()))
                ->object($instance)->isInstanceOf('NMarniesse\PommFilter\ValueType\RangeValue')

            ->assert('Error with string value.')
            ->exception(function () {
                new TestedClass('a', 10);
            })
                ->isInstanceOf('InvalidArgumentException')
                ->hasMessage('Values must be numeric or an instance of DateTime.')

            ->assert('Error with string value.')
            ->exception(function () {
                new TestedClass(1, new \DateTime());
            })
                ->isInstanceOf('InvalidArgumentException')
                ->hasMessage('Values have not the same type.')
            ;
    }

    /**
     * testGetFormattedForDb
     */
    public function testGetFormattedForDb()
    {
        $this
            ->assert('Get formatted range for database.')
            ->when($formatted = (new TestedClass(2.5, '10'))->getFormattedForDb())
                ->string($formatted)->isEqualTo('[2.5, 10]')

            ->assert('Get formatted range for database.')
            ->given($instance = new TestedClass(
                new \DateTime('2010-01-01 00:00:00+00'),
                new \DateTime('2010-12-31 23:59:59+00')
            ))
            ->when($formatted = $instance->getFormattedForDb())
                ->string($formatted)->isEqualTo('[2010-01-01 00:00:00+00:00, 2010-12-31 23:59:59+00:00]')
            ;
    }
}
