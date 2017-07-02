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

`$homeup = new HomeUp\Api\HomeUp('your_key', 'your_token');`

Then you can simply use any of the following commands to get data from the centralized feed.

To get a list of listings.  Please note, requests with a limit of more than 100 will not include listing images

`$homeup->listings(['board', 'insert_board_id', 'limit' => 20]);`

To get an individual listing use the following along with the listing ID provided from the API

`$homeup->listing($listing_id, []);`

To get an individual listings images use the following along with the listing ID provided from the API

`$homeup->images($listing_id, []);`

You can also do a query based on nearly any field in the database

`$homeup->query()->where('square_feet', '>', 2000)->where('price', '<', 1000000)->get();`





