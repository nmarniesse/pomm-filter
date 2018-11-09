<?php
/*
 * This file is part of pomm-filter package.
 *
 * (c) 2018 Son-Video Distribution
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NMarniesse\PommFilter\ValueType;

/**
 * Class RangeValue
 *
 * @package NMarniesse\PommFilter\ValueType
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class RangeValue
{
    /**
     * @var mixed
     */
    private $from_value;

    /**
     * @var mixed
     */
    private $to_value;

    /**
     * RangeValue constructor.
     *
     * @param mixed $from_value
     * @param mixed $to_value
     */
    public function __construct($from_value, $to_value)
    {
        if (!is_numeric($from_value) && !$from_value instanceof \DateTime) {
            throw new \InvalidArgumentException('Values must be numeric or an instance of DateTime.');
        }

        if (!is_numeric($to_value) && !$to_value instanceof \DateTime) {
            throw new \InvalidArgumentException('Values must be numeric or an instance of DateTime.');
        }

        if (is_numeric($to_value) !== is_numeric($from_value)) {
            throw new \InvalidArgumentException('Values have not the same type.');
        }

        $this->from_value = $from_value;
        $this->to_value   = $to_value;
    }

    /**
     * getFormattedForDb
     *
     * @return string
     */
    public function getFormattedForDb()
    {
        if ($this->from_value instanceof \DateTime && $this->to_value instanceof \DateTime) {
            return sprintf(
                '[%s, %s]',
                date_format($this->from_value, 'Y-m-d H:i:sP'),
                date_format($this->to_value, 'Y-m-d H:i:sP')
            );
        }

        return sprintf('[%s, %s]', $this->from_value, $this->to_value);
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFormattedForDb();
    }
}
