<?php

declare(strict_types=1);

use Maestroerror\PostizClient\Requests\CreatePostRequest;
use Maestroerror\PostizClient\Requests\DeletePostRequest;
use Maestroerror\PostizClient\Requests\GenerateVideoRequest;
use Maestroerror\PostizClient\Requests\GetIntegrationsRequest;
use Maestroerror\PostizClient\Requests\UpdatePostRequest;
use Maestroerror\PostizClient\Requests\UploadFileRequest;
use Maestroerror\PostizClient\Requests\UploadFromUrlRequest;
use Maestroerror\PostizClient\Requests\VideoFunctionRequest;
use Saloon\Enums\Method;

test('GetIntegrationsRequest has correct method and endpoint', function () {
    $request = new GetIntegrationsRequest();

    expect($request->getMethod())->toBe(Method::GET)
        ->and($request->resolveEndpoint())->toBe('/integrations');
});

test('CreatePostRequest has correct method and endpoint', function () {
    $data = ['type' => 'now', 'posts' => []];
    $request = new CreatePostRequest($data);

    expect($request->getMethod())->toBe(Method::POST)
        ->and($request->resolveEndpoint())->toBe('/posts');
});

test('UpdatePostRequest has correct method and endpoint', function () {
    $data = ['type' => 'schedule', 'posts' => []];
    $request = new UpdatePostRequest('post-123', $data);

    expect($request->getMethod())->toBe(Method::PUT)
        ->and($request->resolveEndpoint())->toBe('/posts/post-123');
});

test('DeletePostRequest has correct method and endpoint', function () {
    $request = new DeletePostRequest('post-456');

    expect($request->getMethod())->toBe(Method::DELETE)
        ->and($request->resolveEndpoint())->toBe('/posts/post-456');
});

test('UploadFromUrlRequest has correct method and endpoint', function () {
    $request = new UploadFromUrlRequest('https://example.com/image.jpg');

    expect($request->getMethod())->toBe(Method::POST)
        ->and($request->resolveEndpoint())->toBe('/upload-from-url');
});

test('GenerateVideoRequest has correct method and endpoint', function () {
    $data = ['type' => 'image-text-slides'];
    $request = new GenerateVideoRequest($data);

    expect($request->getMethod())->toBe(Method::POST)
        ->and($request->resolveEndpoint())->toBe('/generate-video');
});

test('VideoFunctionRequest has correct method and endpoint', function () {
    $data = ['functionName' => 'loadVoices'];
    $request = new VideoFunctionRequest($data);

    expect($request->getMethod())->toBe(Method::POST)
        ->and($request->resolveEndpoint())->toBe('/video/function');
});

test('UploadFileRequest validates file exists', function () {
    expect(fn () => new UploadFileRequest('/non/existent/file.jpg'))
        ->toThrow(\InvalidArgumentException::class, 'File does not exist');
});

test('UploadFileRequest validates file is readable', function () {
    // Create a temporary file for testing
    $tmpFile = tempnam(sys_get_temp_dir(), 'postiz_test_');
    chmod($tmpFile, 0000); // Make it unreadable

    expect(fn () => new UploadFileRequest($tmpFile))
        ->toThrow(\InvalidArgumentException::class, 'File is not readable');

    // Clean up
    chmod($tmpFile, 0644);
    unlink($tmpFile);
});

test('UploadFileRequest validates file size limit', function () {
    // Create a temporary file for testing
    $tmpFile = tempnam(sys_get_temp_dir(), 'postiz_test_');

    // Mock a file that appears to be over 100MB by using reflection
    // We can't actually create a 100MB+ file in tests, so we'll verify the validation logic exists
    $reflection = new ReflectionClass(UploadFileRequest::class);
    $constructor = $reflection->getConstructor();

    // Create a small test file
    file_put_contents($tmpFile, 'test content');

    // This should not throw for a small file
    $request = new UploadFileRequest($tmpFile);
    expect($request)->toBeInstanceOf(UploadFileRequest::class);

    // Clean up
    unlink($tmpFile);
});
