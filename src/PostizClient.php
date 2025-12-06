<?php

declare(strict_types=1);

namespace Maestroerror\PostizClient;

use Maestroerror\PostizClient\Requests\CreatePostRequest;
use Maestroerror\PostizClient\Requests\DeletePostRequest;
use Maestroerror\PostizClient\Requests\GenerateVideoRequest;
use Maestroerror\PostizClient\Requests\GetIntegrationsRequest;
use Maestroerror\PostizClient\Requests\UpdatePostRequest;
use Maestroerror\PostizClient\Requests\UploadFileRequest;
use Maestroerror\PostizClient\Requests\UploadFromUrlRequest;
use Maestroerror\PostizClient\Requests\VideoFunctionRequest;
use Saloon\Http\Response;

/**
 * Postiz PHP SDK
 *
 * PHP SDK for interacting with Postiz API
 */
class PostizClient
{
    protected PostizConnector $connector;

    public function __construct(string $apiKey, string $baseUrl = 'https://postiz.com/api')
    {
        $this->connector = new PostizConnector($apiKey, $baseUrl);
    }

    /**
     * Get integrations
     *
     * Retrieves a list of all integrations for the authenticated user
     */
    public function getIntegrations(): Response
    {
        return $this->connector->send(new GetIntegrationsRequest());
    }

    /**
     * Create post
     *
     * Creates a new post to be scheduled or published immediately
     */
    public function createPost(array $data): Response
    {
        return $this->connector->send(new CreatePostRequest($data));
    }

    /**
     * Update post
     *
     * Updates an existing post
     */
    public function updatePost(string $postId, array $data): Response
    {
        return $this->connector->send(new UpdatePostRequest($postId, $data));
    }

    /**
     * Delete post
     *
     * Deletes an existing post
     */
    public function deletePost(string $postId): Response
    {
        return $this->connector->send(new DeletePostRequest($postId));
    }

    /**
     * Upload file
     *
     * Uploads a media file
     */
    public function uploadFile(string $filePath): Response
    {
        return $this->connector->send(new UploadFileRequest($filePath));
    }

    /**
     * Upload from URL
     *
     * Uploads a file from an existing URL
     */
    public function uploadFromUrl(string $url): Response
    {
        return $this->connector->send(new UploadFromUrlRequest($url));
    }

    /**
     * Generate video
     *
     * Creates AI-generated videos for posts
     */
    public function generateVideo(array $data): Response
    {
        return $this->connector->send(new GenerateVideoRequest($data));
    }

    /**
     * Video function
     *
     * Execute video-related functions like loading available voices
     */
    public function videoFunction(array $data): Response
    {
        return $this->connector->send(new VideoFunctionRequest($data));
    }

    /**
     * Get the underlying connector
     */
    public function getConnector(): PostizConnector
    {
        return $this->connector;
    }
}
