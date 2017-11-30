<?php

use HomeUp\Api\HomeUp;

/**
 *  Corresponding Class to test YourClass class
 *
 *  For each class in your library, there should be a corresponding Unit-Test for it
 *  Unit-Tests should be as much as possible independent from other test going on.
 *
 *  @author yourname
 */
class HomeUpTest extends PHPUnit_Framework_TestCase{

    protected $key;
    protected $secret;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $dotenv = new Dotenv\Dotenv(__DIR__ . "/..");
        $dotenv->load();

        $this->key = getenv('HOMEUP_API_KEY');
        $this->secret = getenv('HOMEUP_API_SECRET');
    }

    /**
     * Test to make sure the class can be called
     */
    public function testIsThereAnySyntaxError()
    {
        $hu = new HomeUp($this->key, $this->secret);
        $this->assertTrue(is_object($hu));

        unset($hu);
    }

    /**
     * Test to make sure a response is given from getListings()
     */
    public function testListingsResponse()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $this->assertTrue(!empty($hu->listings()));

        unset($hu);
    }

    /**
     * Test to make sure the values of the response seem correct
     */
    public function testListingValues()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->listings());

        foreach($listings as $listing)
        {
            $this->assertTrue(!empty($listing->mls_id));
        }

        unset($hu);
    }

    /**
     * Test to make sure the limit works correctly
     */
    public function testListingLimitWorksCorrectly()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->listings(['limit' => 15]));

        $count = 0;
        foreach($listings as $listing)
            $count++;

        $this->assertTrue($count == 15);

        $listings = json_decode($hu->listings());

        $count = 0;
        foreach($listings as $listing)
            $count++;

        $this->assertTrue($count == 10);


        unset($hu);
    }

    /**
     * Test a single listing
     */
    public function testSingleListing()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->listings());

        $id = 0;
        $address = "";
        foreach($listings as $listing)
        {
            $id = $listing->id;
            $address = $listing->address_display;
            break;
        }

        $this->assertTrue($id > 0);

        $listing = json_decode($hu->listing($id));

        $this->assertTrue($listing->address_display == $address);

        unset($hu);
    }

    /**
     * Test a single listing images
     */
    public function testSingleListingImages()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->listings());

        $id = 0;
        foreach($listings as $listing)
        {
            $id = $listing->id;
            break;
        }

        $this->assertTrue($id > 0);

        $images = json_decode($hu->images($id));

        $count = 0;
        foreach($images as $image)
        {
            $this->assertTrue(!empty($image->filename));
            $count++;
        }

        unset($hu);
    }

    /**
     * Test a single listing images
     */
    public function testListingCanBeQueriedWithWhere()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->query()->where('square_feet', '>', 2000)->get());

        foreach($listings as $listing)
            $this->assertTrue($listing->square_feet > 0);

        unset($hu);
    }

    /**
     * Test a single listing images
     */
    public function testListingCanBeQueriedWithClosure()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = $hu->query()->where(function($query){
            $query->where('square_feet', '>', 1000);
        })->limit(100)->get();

        $listings = json_decode($listings);

        foreach($listings as $listing)
            $this->assertTrue($listing->square_feet > 1000);

        $listings = $hu->query()->where(function($query){
            $query->where('square_feet', '>', 1000);
            $query->orWhere('price', '<', 500000);
        })->limit(100)->get();

        $listings = json_decode($listings);

        foreach($listings as $listing)
            $this->assertTrue($listing->square_feet > 1000 || $listing->price < 500000);

        unset($hu);
    }

    /**
     * Test a single listing images
     */
    public function testQueryOrderBy()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->query()->limit(10)->orderBy('price', 'DESC')->get());

        // The prices should be decreasing, so start really high, and then assert each one is lower than the previous
        $price = 1000000000000;
        foreach($listings as $listing)
        {
            $this->assertTrue($listing->price <= $price);
            $price = $listing->price;
        }

        $listings = json_decode($hu->query()->limit(20)->orderBy('price', 'ASC')->get());

        // The prices should be increasing, so start at 0, and then assert each one is greater than the previous
        $price = 0;
        foreach($listings as $listing)
        {
            $this->assertTrue($listing->price >= $price);
            $price = $listing->price;
        }

        unset($hu);
    }

    /**
     * Test to make sure removed listings can be retrieved
     */
    public function testRemovedListingsCanBeRetrieved()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->removed(24));

        $yesterday = \Carbon\Carbon::now('America/Edmonton')->subHours(24);

        foreach($listings as $listing)
        {
            $deleted_at = \Carbon\Carbon::parse($listing->deleted_at)->setTimezone('America/Edmonton');

            $this->assertTrue($deleted_at->gte($yesterday));
        }

        unset($hu);
    }

    /**
     *
     */
    public function testLookupsCanBeRetrieved()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $cities = json_decode($hu->cities());

        foreach($cities as $city)
        {
            $this->assertTrue(!empty($city->name));
        }

        $communities = json_decode($hu->communities());

        foreach($communities as $community)
        {
            $this->assertTrue(!empty($community->name));
        }

        $realtors = json_decode($hu->realtors());

        foreach($realtors as $realtor)
        {
            $this->assertTrue(!empty($realtor->name));
        }

        $firms = json_decode($hu->firms());

        foreach($firms as $firm)
        {
            $this->assertTrue(!empty($firm->name));
        }
    }

    /**
     *
     */
    public function testCountCanBeRetrieved()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->query()->count());

        var_dump($listings);
        $this->assertTrue($listings > 0);

        unset($hu);
    }

    /**
     *
     */
    public function testOffsetCanBeSet()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $query = $hu->query();
        $listing_count = json_decode($query->count());
        if($listing_count <= 100)
        {
            $this->assertTrue(true);
        }
        else
        {
            $prev_listing_address = "";
            $count = 0;
            $offset = 0;
            $limit = 100;
            while($offset < $listing_count)
            {
                $listings = json_decode($query->offset($offset)->limit($limit)->get());

                foreach($listings as $listing)
                {
                    $this->assertTrue($listing->address_display != $prev_listing_address);
                    $prev_listing_address = $listing->address_display;

                    break;
                }

                $count++;
                $offset = $count * $limit;
            }
        }

        unset($hu);
    }

    /**
     *
     */
    public function testBoardCanBeSet()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $query = $hu->query();
        $listings = json_decode($query->whereBoard('creb')->get());
        foreach($listings as $listing)
        {
            $this->assertTrue($listing->listingable_type == 'App\\CrebListing');
        }

        $hu = new HomeUp($this->key, $this->secret);

        $query = $hu->query();
        $listings = json_decode($query->whereBoard('crea')->get());
        foreach($listings as $listing)
        {
            var_dump($listing->listingable_type);
            $this->assertTrue($listing->listingable_type == 'App\\CreaListing');
        }

        unset($hu);
    }

    /**
     *
     */
    public function testCreaListingsCanBeQueriedByCity()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $query = $hu->query();
        $listings = json_decode($query->whereBoard('crea')->where('city_name', '=', 'Victoria')->get());
        foreach($listings as $listing)
        {
            $this->assertTrue($listing->listingable_type == 'App\\CreaListing');
            $this->assertTrue($listing->city_name == 'Victoria');
        }

        unset($hu);
    }

    public function testImagesCanBeRetrieved()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $query = json_decode($hu->images('C4076238'));

        $images = $query->images;

        foreach($images as $image)
        {
            $this->assertNotEmpty($image);
        }

        unset($hu);
    }

}