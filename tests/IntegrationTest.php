<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockResponse;

test('can get integrations', function () {
    $mockResponse = MockResponse::make([
        'integrations' => [
            [
                'id' => 'int-1',
                'name' => 'LinkedIn',
                'type' => 'linkedin',
            ],
            [
                'id' => 'int-2',
                'name' => 'X (Twitter)',
                'type' => 'x',
            ],
        ],
    ]);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->getIntegrations();

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKey('integrations')
        ->and($response->json('integrations'))->toBeArray()
        ->and($response->json('integrations'))->toHaveCount(2);
});

test('can create a post', function () {
    $postData = [
        'type' => 'now',
        'date' => '2024-12-14T10:00:00.000Z',
        'posts' => [
            [
                'integration' => ['id' => 'int-1'],
                'value' => [
                    ['content' => 'Test post', 'image' => []],
                ],
            ],
        ],
    ];

    $mockResponse = MockResponse::make([
        'id' => 'post-123',
        'status' => 'created',
    ], 201);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->createPost($postData);

    expect($response->status())->toBe(201)
        ->and($response->json())->toHaveKey('id')
        ->and($response->json('id'))->toBe('post-123');
});

test('can schedule a post', function () {
    $postData = [
        'type' => 'schedule',
        'date' => '2024-12-14T10:00:00.000Z',
        'posts' => [
            [
                'integration' => ['id' => 'int-1'],
                'value' => [
                    ['content' => 'Scheduled post', 'image' => []],
                ],
            ],
        ],
    ];

    $mockResponse = MockResponse::make([
        'id' => 'post-456',
        'status' => 'scheduled',
    ], 201);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->createPost($postData);

    expect($response->status())->toBe(201)
        ->and($response->json('status'))->toBe('scheduled');
});

test('can update a post', function () {
    $updateData = [
        'type' => 'schedule',
        'date' => '2024-12-15T10:00:00.000Z',
        'posts' => [
            [
                'integration' => ['id' => 'int-1'],
                'value' => [
                    ['content' => 'Updated post', 'image' => []],
                ],
            ],
        ],
    ];

    $mockResponse = MockResponse::make([
        'id' => 'post-123',
        'status' => 'updated',
    ]);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->updatePost('post-123', $updateData);

    expect($response->status())->toBe(200)
        ->and($response->json('status'))->toBe('updated');
});

test('can delete a post', function () {
    $mockResponse = MockResponse::make([
        'success' => true,
        'message' => 'Post deleted successfully',
    ]);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->deletePost('post-123');

    expect($response->status())->toBe(200)
        ->and($response->json('success'))->toBeTrue();
});

test('can upload file from URL', function () {
    $mockResponse = MockResponse::make([
        'id' => 'upload-789',
        'path' => 'https://uploads.postiz.com/image.jpg',
    ]);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->uploadFromUrl('https://example.com/image.jpg');

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKey('id')
        ->and($response->json())->toHaveKey('path');
});

test('can generate video', function () {
    $videoData = [
        'type' => 'image-text-slides',
        'content' => 'Test video content',
    ];

    $mockResponse = MockResponse::make([
        'id' => 'video-123',
        'status' => 'processing',
    ]);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->generateVideo($videoData);

    expect($response->status())->toBe(200)
        ->and($response->json('status'))->toBe('processing');
});

test('can call video function', function () {
    $functionData = [
        'functionName' => 'loadVoices',
        'identifier' => 'image-text-slides',
    ];

    $mockResponse = MockResponse::make([
        'voices' => [
            ['id' => 'voice-1', 'name' => 'Voice 1'],
            ['id' => 'voice-2', 'name' => 'Voice 2'],
        ],
    ]);

    $mock = mockClient([
        '*' => $mockResponse,
    ]);

    $client = createTestClient('test-key', $mock);
    $response = $client->videoFunction($functionData);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKey('voices')
        ->and($response->json('voices'))->toBeArray();
});
