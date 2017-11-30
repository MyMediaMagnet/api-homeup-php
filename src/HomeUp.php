<?php namespace HomeUp\Api;

use Carbon\Carbon;

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
    public function __construct($key = null, $secret = null)
    {
        $this->base_url = "http://138.197.152.15";

        if(!empty(getenv('HOMEUP_BASE_URL')))
            $this->base_url = getenv('HOMEUP_BASE_URL');

        $this->key = getenv('HOMEUP_API_KEY');
        $this->secret = getenv('HOMEUP_API_SECRET');
        if(!empty($key))
            $this->key = $key;
        if(!empty($secret))
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
     * @param $mls_id
     * @param array $query
     * @return \Psr\Http\Message\StreamInterface
     */
    public function images($mls_id, $query = [])
    {
        return Request::send($this->base_url . '/api/v1/listings/' . $mls_id . '/images', $query, $this);
    }

    /**
     * @param int $hours
     * @return \Psr\Http\Message\StreamInterface
     */
    public function removed($hours)
    {
        $since = Carbon::now('America/Edmonton')->subHours($hours);

        $query = ['since' => $since->timestamp];

        return Request::send($this->base_url . '/api/v1/listings/removed', $query, $this);
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function cities()
    {
        return Request::send($this->base_url . '/api/v1/lookup/cities', [], $this);
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function communities()
    {
        return Request::send($this->base_url . '/api/v1/lookup/communities', [], $this);
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function realtors()
    {
        return Request::send($this->base_url . '/api/v1/lookup/realtors', [], $this);
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function firms()
    {
        return Request::send($this->base_url . '/api/v1/lookup/firms', [], $this);
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