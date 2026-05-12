<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Response;

abstract class Controller
{
    protected function extractData(Response $response): mixed
    {
        $json = $response->json();
        return $json['data'] ?? $json;
    }
}
