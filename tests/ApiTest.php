<?php

use Netzleuchten\DynDns\Api;

class ApiTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Api
     */
    private $api;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|INWX\Domrobot
     */
    private $domrobot;

    public function setUp()
    {
        $this->api = new Api();
        $this->domrobot = $this
            ->getMockBuilder('INWX\Domrobot')
            ->setConstructorArgs(['foo', 'bar'])
            ->setMethods(['setLanguage', 'login'])
            ->getMock();
    }

    public function testInjectDomrobot()
    {
        $this->domrobot->expects($this->once())
            ->method('setLanguage')
            ->with($this->equalTo('en'));

        $this->api->injectDomrobot($this->domrobot);
    }

    public function testLogin()
    {
        $this->domrobot->expects($this->once())
            ->method('login')
            ->with('user', 'pass');

        $this->api->injectDomrobot($this->domrobot);
        $this->api->login('user', 'pass');
    }
}
