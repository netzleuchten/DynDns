<?php

use Dotenv\Dotenv;
use INWX\Domrobot;
use Netzleuchten\DynDns\Exceptions\UpdateRecordException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$dotenv = new Dotenv(__DIR__ . '/../', defined('DOTENV') ? DOTENV : '.env');
$dotenv->load();

$api = new \Netzleuchten\DynDns\Api();

// Validate IP
if (!$api->validateIp($request->get('ip'))) {
    JsonResponse::create(
        ['error' => 'IP is not valid.'],
        Response::HTTP_BAD_REQUEST
    )->send();
    die();
}

// Validate Secret Key
if (!$api->validateKey($request->get('key'))) {
    JsonResponse::create(
        ['error' => 'Wrong key.'],
        Response::HTTP_UNAUTHORIZED
    )->send();
    die();
}

// Login and update the record or fail
$api->injectDomrobot(new Domrobot($_ENV['INWX_API']));

if ($api->login($_ENV['INWX_USERNAME'], $_ENV['INWX_PASSWORD'])) {

    try
    {
        $api->updateNameserverRecord($_ENV['DOMAIN'], $request->get('ip'));
        $api->finish();

        JsonResponse::create(
            ['success' => ['domain' => $_ENV['DOMAIN'], 'ip' => $request->get('ip'), 'message' => 'Update was successfull.']],
            Response::HTTP_OK
        )->send();
    }
    catch (UpdateRecordException $e)
    {
        JsonResponse::create(
            ['error' => ['domain' => $e->getDomain(), 'message' => $e->getMessage()]],
            Response::HTTP_OK
        )->send();
    }


} else {

    JsonResponse::create(
        ['error' => 'INWX authentication failed.'],
        Response::HTTP_OK
    )->send();
}

