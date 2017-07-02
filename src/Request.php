<?php
/**
 * Created by PhpStorm.
 * User: mymediamagnet
 * Date: 01/07/17
 * Time: 6:34 PM
 */

namespace HomeUp\Api;


use GuzzleHttp\Client;

class Request
{
    /**
     * @param $url
     * @param $query
     * @param $homeup
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function send($url, $query, HomeUp $homeup)
    {
        $client = new Client();

        $query = ['query' => $query];
        $json = json_encode($query);
        $headers = ['Content-Type' => 'application/json', 'key' => $homeup->getKey(), 'secret' => $homeup->getSecret()];

        $response = $client->get($url, ['headers' => $headers, 'body' => $json]);

        return $response->getBody();
    }

}