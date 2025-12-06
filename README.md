# Postiz PHP SDK

A PHP SDK for interacting with the [Postiz API](https://postiz.com) - an open-source social media scheduling platform.

Built with [Saloon](https://docs.saloon.dev/) for a modern and elegant API client experience.

## Features

- ✅ Full Postiz API coverage
- ✅ Type-safe requests and responses
- ✅ PSR-4 autoloading
- ✅ Comprehensive test coverage with PEST
- ✅ Modern PHP 8.1+ support
- ✅ Built on Saloon for flexibility and extensibility

## Requirements

- PHP 8.1 or higher
- Composer

## Installation

Install via Composer:

```bash
composer require maestroerror/postiz-php-sdk
```

## Quick Start

```php
<?php

use Maestroerror\PostizClient\PostizClient;

// Initialize the client
$client = new PostizClient('your-api-key');

// Get integrations
$integrations = $client->getIntegrations();

// Create a post
$post = $client->createPost([
    'type' => 'now',
    'date' => '2024-12-14T10:00:00.000Z',
    'posts' => [
        [
            'integration' => ['id' => 'your-integration-id'],
            'value' => [
                ['content' => 'Hello from Postiz PHP SDK!', 'image' => []],
            ],
        ],
    ],
]);

// Schedule a post
$scheduledPost = $client->createPost([
    'type' => 'schedule',
    'date' => '2024-12-15T14:30:00.000Z',
    'posts' => [
        [
            'integration' => ['id' => 'your-integration-id'],
            'value' => [
                ['content' => 'This post will be published later', 'image' => []],
            ],
        ],
    ],
]);
```

## API Reference

### Initialize Client

```php
$client = new PostizClient($apiKey, $baseUrl = 'https://postiz.com/api');
```

### Available Methods

#### Get Integrations

Retrieves all integrations for the authenticated user.

```php
$response = $client->getIntegrations();
```

#### Create Post

Creates a new post to be published immediately or scheduled.

```php
$response = $client->createPost([
    'type' => 'now', // or 'schedule'
    'date' => '2024-12-14T10:00:00.000Z',
    'shortLink' => false,
    'posts' => [
        [
            'integration' => ['id' => 'integration-id'],
            'value' => [
                ['content' => 'Your content here', 'image' => []],
            ],
        ],
    ],
]);
```

**Using Carbon for dates:**

```php
use Carbon\Carbon;

$response = $client->createPost([
    'type' => 'schedule',
    'date' => Carbon::now()->addHours(2)->toIso8601String(), // Schedule 2 hours from now
    'posts' => [
        [
            'integration' => ['id' => 'integration-id'],
            'value' => [
                ['content' => 'Scheduled post', 'image' => []],
            ],
        ],
    ],
]);

// Or for a specific date/time
$response = $client->createPost([
    'type' => 'schedule',
    'date' => Carbon::parse('2024-12-25 14:30:00', 'UTC')->toIso8601String(),
    'posts' => [
        [
            'integration' => ['id' => 'integration-id'],
            'value' => [
                ['content' => 'Christmas post', 'image' => []],
            ],
        ],
    ],
]);
```

#### Update Post

Updates an existing post.

```php
$response = $client->updatePost('post-id', [
    'type' => 'schedule',
    'date' => '2024-12-15T10:00:00.000Z',
    'posts' => [
        [
            'integration' => ['id' => 'integration-id'],
            'value' => [
                ['content' => 'Updated content', 'image' => []],
            ],
        ],
    ],
]);
```

#### Delete Post

Deletes an existing post.

```php
$response = $client->deletePost('post-id');
```

#### Upload File

Uploads a media file from local path.

```php
$response = $client->uploadFile('/path/to/file.jpg');
```

#### Upload from URL

Uploads a file from an existing URL.

```php
$response = $client->uploadFromUrl('https://example.com/image.jpg');
```

#### Generate Video

Creates AI-generated videos for posts.

```php
$response = $client->generateVideo([
    'type' => 'image-text-slides',
    'content' => 'Video content',
]);
```

#### Video Function

Executes video-related functions.

```php
$response = $client->videoFunction([
    'functionName' => 'loadVoices',
    'identifier' => 'image-text-slides',
]);
```

## Advanced Usage

### Custom Base URL

If you're self-hosting Postiz or using a different API endpoint:

```php
$client = new PostizClient('your-api-key', 'https://your-postiz-instance.com/api');
```

### Accessing Raw Responses

All methods return a Saloon `Response` object with helpful methods:

```php
$response = $client->getIntegrations();

// Get status code
$statusCode = $response->status();

// Get JSON response
$data = $response->json();

// Get specific key from JSON
$integrations = $response->json('integrations');

// Check if successful
if ($response->successful()) {
    // Handle success
}

// Get headers
$headers = $response->headers();
```

### Error Handling

```php
try {
    $response = $client->createPost($postData);
    
    if ($response->failed()) {
        // Handle API errors
        $error = $response->json('error');
        $message = $response->json('message');
    }
} catch (\Exception $e) {
    // Handle exceptions
    echo $e->getMessage();
}
```

## Testing

The SDK comes with comprehensive PEST tests:

```bash
# Run tests
./vendor/bin/pest

# Run tests with coverage
./vendor/bin/pest --coverage
```

## Code Style

This project follows PSR-12 coding standards. To check and fix code style:

```bash
# Check code style
./vendor/bin/php-cs-fixer fix --dry-run --diff

# Fix code style
./vendor/bin/php-cs-fixer fix
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This SDK is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- Built with [Saloon](https://docs.saloon.dev/)
- For the [Postiz](https://postiz.com) API

## Support

For issues and questions:
- [GitHub Issues](https://github.com/MaestroError/postiz-php-sdk/issues)
- [Postiz Documentation](https://docs.postiz.com)
