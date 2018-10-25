# Simple filter implementation for [Pomm Project](http://www.pomm-project.org/)

Asking a collection of resources is often an action provided with an array of filters. But it's not always
obvious to handle those filters and build the query accordingly: multi-filters, multi-values for a filter, 
null values, dates, ...  
  
This library provides a simple implementation to build query's condition from array filters.  


## Requirements and installation

The requirements are the same as Pomm Project Foundation v2:

- php >=5.5
- Postgresql >= 9.2


```bash
composer require nmarniesse/pomm-filter
```

## Usage

The library helps you to create an instance of `PommProject\Foundation\Where` that you could use in every pomm query
(see https://github.com/pomm-project/Foundation/blob/master/documentation/foundation.rst#where-the-condition-builder
for further explanation).

You want to filter on active products with color 'blue' or 'yellow', in category 'accessory',
with price between 50 and 100, and which have one tag. The filter array representation could be:

```php
use NMarniesse\PommFilter\FilterInterface;

$filters = [
    'is_active'  => true,
    'color'      => ['blue', 'yellow'],
    'category'   => ['accessory'],
    'price_from' => 50,
    'price_to'   => 100,
    'tag'        => FilterInterface::_not_null_,
];
```

Then you can build your query like that:

```php
use NMarniesse\PommFilter\FilterCondition;
use NMarniesse\PommFilter\FilterCondition\FilterType\BasicFilter;
use NMarniesse\PommFilter\FilterCondition\FilterType\BooleanFilter;

$sql = <<<SQL
SELECT
  p.id,
  p.color,
  c.category_id,
  pr.unit_price
FROM product p
 INNER JOIN category c ON ...
 INNER JOIN price pr ON ...
 LEFT JOIN  product_tag pt ON ...
WHERE {conditions}
SQL;

# Create filters
$filter_condition = new FilterCondition('p');

$filter_condition->addField(new BooleanFilter('is_active'));
$filter_condition->addField(new BasicFilter('category_id', 'c'));
$filter_condition->addField(new BasicFilter('unit_price', 'pr', '>='));
$filter_condition->addField(new BasicFilter('unit_price', 'pr', '<='), 'price_from');
$filter_condition->addField(new BasicFilter('tag', 'pt'), 'price_to');

# Filter on color 'blue' or 'yellow', with category 'accessory', price between 50 and 100, and have one tag:
$where = $filter_condition>getWhere([
    'is_active'  => true,
    'color'      => ['blue', 'yellow'],
    'category'   => ['accessory'],
    'price_from' => 50,
    'price_to'   => 100,
    'tag'        => FilterInterface::_not_null_,
]);

$sql = str_replace('{conditions}', (string) $where, $sql);
$pomm->getQueryManager()->query($sql, $where->getValues());

```

## FilterCollection

By default the *FilterCollection* does not contain any filter.

The method `getWhere(array $filters)` convert any associative array into `Where` instance
When you do a `getWhere(['key1' => 'val1'])`, it assumes that the key1 field exists in your query and 
build a simple condition query "key1 = $*" with parameter 'val1'.

If you want to specify a table alias in the condition query, or not use the `=` operator, you have to
add the filter manually using the `addFilter` method.

Examples:

```php
use NMarniesse\PommFilter\FilterCondition;
use NMarniesse\PommFilter\FilterCondition\FilterType\BasicFilter;

# Create a filter condition.
# When you pass a filter {"key1": "value1"}, it assumes that the field *key1* exists in your query
$filter_condition = new FilterCondition();

# When you have multiple tables in your query, you may specify the table/alias name
# Then when you pass a filter {"key1": "value1"}, it will automatically construct "user.key1 = $*"
$filter_condition = new FilterCondition('user');

# To use a filter on a field which is not on main table, you have to add it manually
# For example to add a filter on the field category on table p
$filter_condition->addField(new BasicField('category', 'p'));

# If you want personnalize your filter name, use second parameter to specify it
$filter_condition->addField(new BasicField('category', 'p'), 'my_custom_category_filter_name');
```


## Filter types

This library provides several filter types to help you to create your own filter collection.


### BasicFilter

As its name indicates, it is useful to create simple filter.  
But not only because you can specify the operator you want to use (default is `=`). This way you
can create a lot of specific filter with this one.  

```php
use NMarniesse\PommFilter\FilterCondition;
use NMarniesse\PommFilter\FilterCondition\FilterType\BasicFilter;

# Create a BasicFilter
$filter1 = new BasicFilter('color');

# Create a BasicFilter and specify the table name/alias used in the query
$filter2 = new BasicFilter('category_id', 'c');

# If you want to filter on prices greater than specific value
$filter3 = new BasicFilter('unit_price', 'p', '>=');

# If you want to filter on prices greater than specific value
$filter4 = new BasicFilter('unit_price', 'p', '<=');

$filter_condition = new FilterCondition();
$filter_condition->addField($filter1);
$filter_condition->addField($filter2);
$filter_condition->addField($filter3, 'price_from');
$filter_condition->addField($filter4, 'price_to');

# Filter on color 'blue' or 'yellow', with category 'accessory', and price between 50 and 100
$filter_condition>getWhere([
    'color'      => ['blue', 'yellow'],
    'category'   => ['accessory'],
    'price_from' => 50,
    'price_to'   => 100,
]);

```

### DateTimeFilter

This filter allow you to use date values.

```php
use NMarniesse\PommFilter\FilterCondition;
use NMarniesse\PommFilter\FilterCondition\FilterType\DateTimeFilter;

# Create a BasicFilter
$filter1 = new DateTimeFilter('created_at', '', '>=');
$filter2 = new DateTimeFilter('created_at', '', '<=');

$filter_condition->addField($filter1, 'created_date_from');
$filter_condition->addField($filter2, 'created_date_to');

# Filter on color 'blue' or 'yellow', with category 'accessory', and price between 50 and 100
$filter_condition>getWhere([
    'created_date_from' => '2010-01-01T00:00:00+00',
    'created_date_to'   => '2010-12-31T23:59:59+00',
]);

```

### Others

Other filter types are available: AutoCompletefilter, BooleanFilter, HstoreFilter, LtreeFilter, ...  

You can create your own filters by implementing the `FilterInterface` interface


# Dev

## Run unit tests

```bash
php ./vendor/bin/atoum -d tests/Unit
```
