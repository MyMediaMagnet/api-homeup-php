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

    protected $key = "b2d5c969052992d990cd58593b9cf903";
    protected $secret = "f9ecddb9d24616513517cf4a49f374300edc9668018ebe01";

  /**
  * Test to make sure the class can be called
  */
  public function testIsThereAnySyntaxError(){
	$var = new HomeUp($this->key, $this->secret);
	$this->assertTrue(is_object($var));

	unset($var);
  }

  /**
  * Test to make sure a response is given from getListings()
  */
  public function testListingsResponse(){
    $var = new HomeUp($this->key, $this->secret);

	$this->assertTrue(!empty($var->getListings()));

	unset($var);
  }

  /**
  * Test to make sure the values of the response seem correct
  */
  public function testListingValues(){
      $var = new HomeUp($this->key, $this->secret);

	$listings = json_decode($var->getListings());
	foreach($listings as $listing)
        $this->assertTrue(!empty($listing->address_display));

	unset($var);
  }

  /**
  * Test to make sure the limit works correctly
  */
  public function testListingLimitWorksCorrectly(){
	$var = new HomeUp($this->key, $this->secret);

	$listings = json_decode($var->getListings(['limit' => 15]));

	$count = 0;
	foreach($listings as $listing)
        $count++;

    $this->assertTrue($count == 15);

	$listings = json_decode($var->getListings());

	$count = 0;
	foreach($listings as $listing)
        $count++;

    $this->assertTrue($count == 10);


	unset($var);
  }

  /**
  * Test to make sure the limit works correctly
  */
  public function testListingImagesOnlyAttachedWithLimitUnder100(){
	$var = new HomeUp($this->key, $this->secret);

	$listings = json_decode($var->getListings(['limit' => 99]));

	foreach($listings as $listing)
        $this->assertTrue(!empty($listing->images[0]));

	$listings = json_decode($var->getListings(['limit' => 101]));

	foreach($listings as $listing)
        $this->assertNotTrue(!empty($listing->images[0]));

	unset($var);
  }

  /**
  * Test a single listing
  */
  public function testSingleListing(){
	$var = new HomeUp($this->key, $this->secret);

	$listings = json_decode($var->getListings());

	$id = 0;
	$address = "";
	foreach($listings as $listing)
    {
        $id = $listing->id;
        $address = $listing->address_display;
        break;
    }

    $this->assertTrue($id > 0);

    $listing = json_decode($var->getListing($id));

    $this->assertTrue($listing->address_display == $address);

	unset($var);
  }

  /**
  * Test a single listing images
  */
  public function testSingleListingImages(){
	$var = new HomeUp($this->key, $this->secret);

	$listings = json_decode($var->getListings());

	$id = 0;
	foreach($listings as $listing)
    {
        $id = $listing->id;
        break;
    }

    $this->assertTrue($id > 0);

    $images = json_decode($var->getListingImages($id));

    $count = 0;
    foreach($images as $image)
    {
        $this->assertTrue(!empty($image->filename));
        $count++;
    }

    $this->assertTrue($count > 0);

	unset($var);
  }
  
}