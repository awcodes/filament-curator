<?php

declare(strict_types=1);

namespace Awcodes\Curator\Glide;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Glide\Responses\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SymfonyResponseFactory implements ResponseFactoryInterface
{
    /**
     * Create SymfonyResponseFactory instance.
     *
     * @param  Request|null  $request  Request object to check "is not modified".
     */
    public function __construct(protected ?Request $request = null) {}

    /**
     * Create the response.
     *
     * @param  FilesystemOperator  $cache  The cache file system.
     * @param  string  $path  The cached file path.
     * @return StreamedResponse The response object.
     *
     * @throws FilesystemException
     */
    public function create(FilesystemOperator $cache, string $path): StreamedResponse
    {
        $stream = $cache->readStream($path);

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $cache->mimeType($path));
        $response->headers->set('Content-Length', (string) $cache->fileSize($path));
        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setExpires(date_create_immutable()->modify('+1 years'));

        if ($this->request instanceof Request) {
            $response->setLastModified(date_create_immutable()->setTimestamp($cache->lastModified($path)));
            $response->isNotModified($this->request);
        }

        $response->setCallback(function () use ($stream): void {
            if (ftell($stream) !== 0) {
                rewind($stream);
            }
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}
