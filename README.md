# Simple filter implementation for [Pomm Project](http://www.pomm-project.org/)

Asking a collection of resources is often given with filter needs. But it's not always
obvious to handle filters and build the query accordingly: multiple filters on different tables, multiple 
values for a filter, null values, dates, ...  

This library provides a simple implementation to build query's condition from an array of filters.  


## Requirements and installation

- php >=5.4


```bash
composer require nmarniesse/pomm-filter
```

## Usage

The library helps to create an instance of `PommProject\Foundation\Where` that you could use in every pomm query
(see [here](https://github.com/pomm-project/Foundation/blob/master/documentation/foundation.rst#where-the-condition-builder)
for further explanation).

To explain what we can do with this library, we can take a practical case: we want to filter
on active products with color 'blue' or 'yellow', in category 'accessory',
with price between 50 and 100, and which have one tag.  

The filter array representation is:

```php
use NMarniesse\PommFilter\FilterInterface;

$array_filters = [
    'is_active'  => '1',
    'color'      => ['blue', 'yellow'],
    'category'   => ['accessory'],
    'price_from' => 50,
    'price_to'   => 100,
    'tag'        => FilterInterface::_not_null_,
];
```

With an HTTP query similar to `?filter[is_active]=1&filter[color][]=blue&filter[color][]=yellow&filter[category]=accessory&filter[price_from]=50&filter[price_from]=50&filter[price_to]=100&filter[tag]=_not_null_`
you can have he same array in php with `$array_filters = $_GET['filter'];`.

You have your array filters, now let build the query:

```php
use NMarniesse\PommFilter\FilterCondition;
use NMarniesse\PommFilter\FilterCondition\FilterType\BasicFilter;
use NMarniesse\PommFilter\FilterCondition\FilterType\BooleanFilter;

# The sql query with a placeholder for the where condition
$sql = <<<SQL
SELECT
  p.id,
  p.color,
  c.category_id,
  pr.unit_price
FROM product p
 INNER JOIN category c     ON ...
 INNER JOIN price pr       ON ...
 LEFT JOIN  product_tag pt ON ...
WHERE {conditions}
SQL;

# Define the available filters and create the Where instance
$filter_condition = new FilterCondition('p');

$filter_condition->addFilter(new BasicFilter('color', 'p')); // optional
$filter_condition->addFilter(new BooleanFilter('is_active'));
$filter_condition->addFilter(new BasicFilter('category_id', 'c'));
$filter_condition->addFilter(new BasicFilter('unit_price', 'pr', '>='));
$filter_condition->addFilter(new BasicFilter('unit_price', 'pr', '<='), 'price_from');
$filter_condition->addFilter(new BasicFilter('tag', 'pt'), 'price_to');
// ...

$where = $filter_condition>getWhere($array_filters);

# Execute the query with Pomm with our instance of Where
$sql = str_replace('{conditions}', (string) $where, $sql);
$pomm_session->getQueryManager()->query($sql, $where->getValues());

```


**Important note**

Even if the generated *Where* condition protects the query against SQL injection, please note you
must clean and validate the data coming from users, according to your business rules.


## Documentation

### FilterCollection

By default the *FilterCollection* does not contain any filter.

The method `getWhere($filters)` convert any associative array into `Where` instance
When you do a `getWhere(['key1' => 'val1'])`, it assumes that the key1 field exists in your query and 
build a simple condition query `key1 = $*` with parameter `'val1'`.

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
$filter_condition->addFilter(new BasicFilter('category', 'p'));

# If you want personnalize your filter name, use second parameter to specify it
$filter_condition->addFilter(new BasicFilter('category', 'p'), 'my_custom_category_filter_name');
```


### Filter types

This library provides several filter types to help you to create your own filter collection.


#### BasicFilter

As its name indicates, this class is useful to create simple filter.  
However you can specify the operator you want to use (default is `=`) in order to
customize the behavior of your filter.  

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
$filter_condition->addFilter($filter1);
$filter_condition->addFilter($filter2);
$filter_condition->addFilter($filter3, 'price_from');
$filter_condition->addFilter($filter4, 'price_to');

# Filter on color 'blue' or 'yellow', with category 'accessory', and price between 50 and 100
$filter_condition>getWhere([
    'color'      => ['blue', 'yellow'],
    'category'   => ['accessory'],
    'price_from' => 50,
    'price_to'   => 100,
]);

```


#### DateTimeFilter

This filter allow you to use date values.

```php
use NMarniesse\PommFilter\FilterCondition;
use NMarniesse\PommFilter\FilterCondition\FilterType\DateTimeFilter;

# Create a BasicFilter
$filter1 = new DateTimeFilter('created_at', '', '>=');
$filter2 = new DateTimeFilter('created_at', '', '<=');

$filter_condition->addFilter($filter1, 'created_date_from');
$filter_condition->addFilter($filter2, 'created_date_to');

# Filter on color 'blue' or 'yellow', with category 'accessory', and price between 50 and 100
$filter_condition>getWhere([
    'created_date_from' => '2010-01-01T00:00:00+00',
    'created_date_to'   => '2010-12-31T23:59:59+00',
]);
```


#### BooleanFilter

This filter is used to handle boolean fields.

```php
use NMarniesse\PommFilter\FilterCondition;
use NMarniesse\PommFilter\FilterCondition\FilterType\DateTimeFilter;

# Create a BasicFilter
$filter1 = new BooleanFilter('is_new');
$filter_condition->addFilter($filter1);

# Filter on true value
$filter_condition>getWhere([
    'is_new' => true, // Any value different from false, 'inactive', 'false', '0', 0
]);

# Filter on true value
$filter_condition>getWhere([
    'is_new' => false, // Any value among the values false, 'inactive', 'false', '0', 0
]);
```


#### HstoreFilter

This filter is used to handle hstore fields.

Given we have a hstore field full_address which contains keys like street, city, postal code, country, etc...

```php
use NMarniesse\PommFilter\FilterCondition;
use NMarniesse\PommFilter\FilterCondition\FilterType\DateTimeFilter;

# Create a BasicFilter
$filter1 = new HstoreFilter('city', 'full_address');
$filter2 = new HstoreFilter('country_code', 'full_address');
$filter_condition->addFilter($filter1);
$filter_condition->addFilter($filter2);

# Filter on city value
$filter_condition>getWhere([
    'city' => 'Paris',
]);

# Filter on country_code value
$filter_condition>getWhere([
    'country_code' => 'FR',
]);
```


#### LtreeFilter

This filter is used to handle ltree fields. As ltree is commonly used to handle tree views, you can filter on
a value and all its descendants using this filter. If you don't want to filter on the descendants the BasicFilter
is enough.


#### Others

Other filter types are available: AutoCompletefilter, BooleanFilter, HstoreFilter, LtreeFilter, ...  

You can create your own filters by implementing the `FilterInterface` interface


## Dev

### Run unit tests

```bash
make unit-tests
```
