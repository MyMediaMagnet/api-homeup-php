<?php namespace HomeUp\Api;

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
     * Request constructor.
     * @param $key
     * @param $secret
     */
    public function __construct($key, $secret)
    {
        $this->base_url = "http://138.197.152.15";

        if(!empty(getenv('HOMEUP_BASE_URL')))
            $this->base_url = getenv('HOMEUP_BASE_URL');

        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @param array $query
     * @return \Psr\Http\Message\StreamInterface
     */
    public function listings($query = [])
    {
        return Request::send($this->base_url . '/api/v1/listings', $query, $this);
    }

    /**
     * @param $id
     * @param array $query
     * @return \Psr\Http\Message\StreamInterface
     */
    public function listing($id, $query = [])
    {
        return Request::send($this->base_url . '/api/v1/listings/' . $id, $query, $this);
    }

    /**
     * @param $id
     * @param array $query
     * @return \Psr\Http\Message\StreamInterface
     */
    public function images($id, $query = [])
    {
        return Request::send($this->base_url . '/api/v1/listings/' . $id . '/images', $query, $this);
    }

    /**
     * @return Query
     */
    public function query()
    {
        return new Query($this);
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }
}