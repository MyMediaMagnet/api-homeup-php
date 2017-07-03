HomeUP API
=========================

API keys are not publicly available.  Please contact us if you would like access.

Features
--------

* Easily query MLS listings from multiple boards in Canada
* Clean, logical listing formats
* Intuitive relationships
* Google Place data for all listings
* Uses Amazon S3 for storage, other users using Amazon can utilize the "quick copy" feature for images

How To Use
--------

First, initialize your class with your API keys.  It's suggested you store these values in an environment file

```php
$homeup = new HomeUp\Api\HomeUp('your_key', 'your_token');
```

Then you can simply use any of the following commands to get data from the centralized feed.

To get a list of listings.  Please note, requests with a limit of more than 100 will not include listing images

```php
$homeup->listings(['board', 'insert_board_id', 'limit' => 20]);
```

To get an individual listing use the following along with the listing ID provided from the API

```php
$homeup->listing($listing_id, []);
```

To get an individual listings images use the following along with the listing ID provided from the API

```php
$homeup->images($listing_id, []);
```

Enter the hours in the "removed" method in order to retrieve listings that have been taken off the MLS in that timeframe

```php
$homeup->removed(24);
```

### Queries
You can also do a query based on nearly any field in the database

```php
$homeup->query()->where('square_feet', '>', 2000)->where('price', '<', 1000000)->get();
```

Ordering and limits work with queries the following way

```php
$homeup->query()->where('square_feet', '>', 2000)
                ->where('price', '<', 1000000)
                ->orderBy('price', 'DESC')
                ->limit(10
                ->get();
```

You can also chain where statements.  By default it is an AND, but you can perform an OR statement like so

```php
$homeup->query()->where(function($q){
    $q->where('square_feet', '>', 1000);
    $q->orWhere('price', '<', 500000);
 )->get();
 ```

### Lookups

There are some lookups available to help you pre-populate your database.  These will return a master list according to their name

```php
$cities = $homeup->cities();
$communities = $homeup->communities();
$realtors = $homeup->realtors();
$firms = $homeup->firms();
 ```





