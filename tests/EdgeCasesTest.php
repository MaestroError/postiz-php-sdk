<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockResponse;

test('handles API errors gracefully', function () {
    $mockResponse = MockResponse::make([
        'error' => 'Invalid API key',
        'message' => 'The provided API key is not valid',
    ], 401);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('invalid-key', $mock);
    $response = $client->getIntegrations();

    expect($response->status())->toBe(401)
        ->and($response->json())->toHaveKey('error');
});

test('handles network errors', function () {
    $mockResponse = MockResponse::make([], 500);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->getIntegrations();

    expect($response->status())->toBe(500);
});

test('handles validation errors for post creation', function () {
    $mockResponse = MockResponse::make([
        'error' => 'Validation failed',
        'errors' => [
            'posts' => ['The posts field is required'],
        ],
    ], 422);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->createPost(['type' => 'now']);

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKey('errors');
});

test('handles post not found error', function () {
    $mockResponse = MockResponse::make([
        'error' => 'Not found',
        'message' => 'Post not found',
    ], 404);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->deletePost('non-existent-id');

    expect($response->status())->toBe(404);
});

test('can create post with multiple platforms', function () {
    $postData = [
        'type' => 'now',
        'date' => '2024-12-14T10:00:00.000Z',
        'posts' => [
            [
                'integration' => ['id' => 'linkedin-int'],
                'value' => [
                    ['content' => 'LinkedIn post', 'image' => []],
                ],
            ],
            [
                'integration' => ['id' => 'x-int'],
                'value' => [
                    ['content' => 'X post', 'image' => []],
                ],
            ],
        ],
    ];

    $mockResponse = MockResponse::make([
        'id' => 'multi-post-123',
        'status' => 'created',
        'platforms' => ['linkedin', 'x'],
    ], 201);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->createPost($postData);

    expect($response->status())->toBe(201)
        ->and($response->json('platforms'))->toBeArray()
        ->and($response->json('platforms'))->toHaveCount(2);
});

test('can create post with media', function () {
    $postData = [
        'type' => 'now',
        'date' => '2024-12-14T10:00:00.000Z',
        'posts' => [
            [
                'integration' => ['id' => 'int-1'],
                'value' => [
                    [
                        'content' => 'Post with image',
                        'image' => [
                            [
                                'id' => 'img-123',
                                'path' => 'https://uploads.postiz.com/image.jpg',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $mockResponse = MockResponse::make([
        'id' => 'post-with-media',
        'status' => 'created',
        'media_count' => 1,
    ], 201);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->createPost($postData);

    expect($response->status())->toBe(201)
        ->and($response->json('media_count'))->toBe(1);
});

test('can create post with short link', function () {
    $postData = [
        'type' => 'now',
        'date' => '2024-12-14T10:00:00.000Z',
        'shortLink' => true,
        'posts' => [
            [
                'integration' => ['id' => 'int-1'],
                'value' => [
                    ['content' => 'Check out this link: https://example.com/very-long-url', 'image' => []],
                ],
            ],
        ],
    ];

    $mockResponse = MockResponse::make([
        'id' => 'post-shortlink',
        'status' => 'created',
        'shortened_links' => 1,
    ], 201);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->createPost($postData);

    expect($response->status())->toBe(201)
        ->and($response->json('shortened_links'))->toBe(1);
});

test('can create post with tags', function () {
    $postData = [
        'type' => 'schedule',
        'date' => '2024-12-14T10:00:00.000Z',
        'tags' => ['marketing', 'announcement'],
        'posts' => [
            [
                'integration' => ['id' => 'int-1'],
                'value' => [
                    ['content' => 'Tagged post', 'image' => []],
                ],
            ],
        ],
    ];

    $mockResponse = MockResponse::make([
        'id' => 'post-tags',
        'status' => 'scheduled',
        'tags' => ['marketing', 'announcement'],
    ], 201);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->createPost($postData);

    expect($response->status())->toBe(201)
        ->and($response->json('tags'))->toBeArray()
        ->and($response->json('tags'))->toContain('marketing');
});
