<?php

declare(strict_types=1);

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class FakeRepository
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com'
        ]);
    }

    public function list(): array
    {
        $request = new Request('GET', '/posts');
        $response = $this->client->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }
}