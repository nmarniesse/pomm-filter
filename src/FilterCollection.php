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
use NMarniesse\PommFilter\FilterType\BasicFilter;

/**
 * Class FilterCollection
 *
 * @package NMarniesse\PommFilter
 * @author  Nicolas Marniesse <nicolas.marniesse@phc-holding.com>
 */
class FilterCollection
{
    /**
     * @var string
     */
    private $table_name;

    /**
     * @var array
     */
    private $filters = [];

    /**
     * FilterCollection constructor.
     *
     * @param string $table_name
     */
    public function __construct($table_name = '')
    {
        $this->table_name = $table_name;
    }

    /**
     * addFilter
     *
     * @param FilterInterface $filter
     * @param string|null     $filter_name
     * @return FilterCollection
     */
    public function addFilter(FilterInterface $filter, $filter_name = null): self
    {
        $this->filters[$filter_name ?? $filter->getFieldName()] = $filter;

        return $this;
    }

    /**
     * getFilterNames
     *
     * @return array
     */
    public function getFilterNames(): array
    {
        return array_keys($this->filters);
    }

    /**
     * getWhere
     *
     * @param array $filters
     * @return Where
     */
    public function getWhere($filters): Where
    {
        $where = new Where();
        foreach ($filters as $name => $value) {
            $where->andWhere($this->getWhereForFilter($name, $value));
        }

        return $where;
    }

    /**
     * getWhereForFilter
     *
     * @param string $name
     * @param mixed  $value
     * @return Where
     */
    private function getWhereForFilter($name, $value): Where
    {
        $filter = $this->filters[$name] ?? new BasicFilter($name, $this->table_name);

        return $filter->getWhere($value);
    }
}
