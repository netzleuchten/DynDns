<?php

use Guzzle\Http\Client;

class FunctionalTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->client = new Client('http://' . WEB_SERVER_HOST . ':' . WEB_SERVER_PORT, array(
            'request.options' => array(
                'exceptions' => false,
            )
        ));
    }

    public function testApiCall()
    {
        $result = $this->client->get('/index.php?key=TheSecretKeyForUnitTests&ip=8.8.8.8')->send();
        $this->arrayHasKey('error', json_decode($result->getBody(true))); // the call has to return an error because you cannot log into INWX with test data
        $this->assertEquals('200', $result->getStatusCode());
    }

    public function testlWrongIp()
    {
        $result = $this->client->get('/index.php?key=TheSecretKeyForUnitTests&ip=123.123.1.256')->send();
        $this->arrayHasKey('error', json_decode($result->getBody(true)));
        $this->assertEquals('400', $result->getStatusCode());
    }

    public function testWrongKey()
    {
        $result = $this->client->get('/index.php?key=ThisIsTheWrongKey&ip=8.8.8.8')->send();
        $this->arrayHasKey('error', json_decode($result->getBody(true)));
        $this->assertEquals('401', $result->getStatusCode());
    }
}
