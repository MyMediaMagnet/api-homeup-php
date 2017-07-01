<?php namespace HomeUp\Api;

use GuzzleHttp\Client;

/**
*  Wrapper class for the HomeUp Centralized Database API
*
*  @author homeup
*/

class HomeUp
{
    private $base_url;
    private $key;
    private $secret;

    /**
     * HomeUp constructor.
     * @param $key
     * @param $secret
     */
    public function __construct($key, $secret)
    {
//        $this->base_url = "http://mlslistings.dev";
        $this->base_url = "http://138.197.152.15";
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @param array $query
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getListings($query = [])
    {
        return $this->guzzle($this->base_url . '/api/v1/listings', $query);
    }

    /**
     * @param $id
     * @param array $query
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getListing($id, $query = [])
    {
        return $this->guzzle($this->base_url . '/api/v1/listings/' . $id, $query);
    }

    /**
     * @param $id
     * @param array $query
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getListingImages($id, $query = [])
    {
        return $this->guzzle($this->base_url . '/api/v1/listings/' . $id . '/images', $query);
    }

    /**
     * @param $url
     * @param $query
     * @return \Psr\Http\Message\StreamInterface
     */
    private function guzzle($url, $query)
    {
        $client = new Client();

        $query = ['query' => $query];
        $json = json_encode($query);
        $headers = ['Content-Type' => 'application/json', 'key' => $this->key, 'secret' => $this->secret];

        $response = $client->get($url, ['headers' => $headers, 'body' => $json]);

        return $response->getBody();
    }
}