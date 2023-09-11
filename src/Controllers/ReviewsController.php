<?php

namespace Slavytuch\LearnSlim\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slavytuch\LearnSlim\Redis\Connection;

class ReviewsController
{
    public function __construct(public readonly Connection $connection)
    {
    }

    public function list(RequestInterface $request, ResponseInterface $response)
    {
        $response->getBody()->write('test!');

        return $response;
    }

    public function add(array $reviewFields)
    {
    }

    public function edit(string $reviewHash)
    {
    }

    public function update(string $reviewHash)
    {
    }

    public function delete(string $reviewHash)
    {
    }

    public function form()
    {
    }
}