<?php

declare(strict_types=1);

use Maestroerror\PostizClient\PostizClient;
use Maestroerror\PostizClient\PostizConnector;

beforeEach(function () {
    $this->apiKey = 'test-api-key';
    $this->baseUrl = 'https://test.postiz.com/api';
});

test('can instantiate PostizClient', function () {
    $client = new PostizClient($this->apiKey);

    expect($client)->toBeInstanceOf(PostizClient::class);
});

test('can instantiate PostizClient with custom base URL', function () {
    $client = new PostizClient($this->apiKey, $this->baseUrl);

    expect($client)->toBeInstanceOf(PostizClient::class)
        ->and($client->getConnector())->toBeInstanceOf(PostizConnector::class);
});

test('connector has correct base URL', function () {
    $client = new PostizClient($this->apiKey, $this->baseUrl);
    $connector = $client->getConnector();

    expect($connector->resolveBaseUrl())->toBe($this->baseUrl);
});

test('connector has correct headers', function () {
    $client = new PostizClient($this->apiKey);
    $connector = $client->getConnector();

    $reflection = new ReflectionClass($connector);
    $method = $reflection->getMethod('defaultHeaders');
    $method->setAccessible(true);
    $headers = $method->invoke($connector);

    expect($headers)->toHaveKey('Authorization')
        ->and($headers['Authorization'])->toBe('Bearer ' . $this->apiKey)
        ->and($headers)->toHaveKey('Content-Type')
        ->and($headers['Content-Type'])->toBe('application/json')
        ->and($headers)->toHaveKey('Accept')
        ->and($headers['Accept'])->toBe('application/json');
});
