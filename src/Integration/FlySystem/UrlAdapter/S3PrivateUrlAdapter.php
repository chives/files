<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\Files\Integration\FlySystem\UrlAdapter;

use Aws\S3\S3ClientInterface;
use FSi\Component\Files;
use FSi\Component\Files\Integration\FlySystem;
use FSi\Component\Files\UrlAdapter;
use Psr\Http\Message\UriInterface;

final class S3PrivateUrlAdapter implements UrlAdapter
{
    /**
     * @var S3ClientInterface
     */
    private $amazonClient;

    /**
     * @var string
     */
    private $fileSystemName;

    /**
     * @var string
     */
    private $amazonBucket;

    public function __construct(S3ClientInterface $s3Client, string $fileSystemName, string $s3Bucket)
    {
        $this->amazonClient = $s3Client;
        $this->fileSystemName = $fileSystemName;
        $this->amazonBucket = $s3Bucket;
    }

    public function supports(Files\WebFile $file): bool
    {
        return true === $file instanceof FlySystem\WebFile && $this->fileSystemName === $file->getFileSystemName();
    }

    /**
     * @param FlySystem\WebFile $file
     * @return UriInterface
     */
    public function url(Files\WebFile $file): UriInterface
    {
        $cmd = $this->amazonClient->getCommand('GetObject', [
            'Bucket' => $this->amazonBucket,
            'Key' => $file->getPath()
        ]);

        return $this->amazonClient->createPresignedRequest($cmd, '+1 hour')->getUri();
    }
}
