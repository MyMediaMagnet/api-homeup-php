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

        $this->key = getenv('API_KEY');
        $this->secret = getenv('API_SECRET');
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
            $this->assertTrue(!empty($listing->address_display));

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
     * Test to make sure the limit works correctly
     */
    public function testListingImagesOnlyAttachedWithLimitUnder100()
    {
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->listings(['limit' => 99]));

        foreach($listings as $listing)
            $this->assertTrue(!empty($listing->images[0]));

        $listings = json_decode($hu->listings(['limit' => 101]));

        foreach($listings as $listing)
            $this->assertNotTrue(!empty($listing->images[0]));

        unset($hu);
    }

    /**
     * Test a single listing
     */
    public function testSingleListing(){
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
    public function testSingleListingImages(){
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
    public function testListingCanBeQueriedWithWhere(){
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->query()->where('square_feet', '>', 2000)->get());

        foreach($listings as $listing)
            $this->assertTrue($listing->square_feet > 0);

        unset($hu);
    }

    /**
     * Test a single listing images
     */
    public function testListingCanBeQueriedWithClosure(){
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
    public function testQueryOrderBy(){
        $hu = new HomeUp($this->key, $this->secret);

        $listings = json_decode($hu->query()->limit(10)->orderBy('price', 'DESC')->get());

        // The prices should be decreasing, so start really high, and then assert each one is lower than the previous
        $price = 1000000000000;
        foreach($listings as $listing)
        {
            $this->assertTrue($listing->price < $price);
            $price = $listing->price;
        }

        $listings = json_decode($hu->query()->limit(10)->orderBy('price', 'ASC')->get());

        // The prices should be decreasing, so start really high, and then assert each one is lower than the previous
        $price = 0;
        foreach($listings as $listing)
        {
            $this->assertTrue($listing->price > $price);
            $price = $listing->price;
        }

        unset($hu);
    }

}