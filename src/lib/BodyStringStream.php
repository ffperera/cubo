<?php

declare(strict_types=1);

namespace FFPerera\Cubo;


// This class is a very simple implementation of a stream interface
// based on the PSR-7 StreamInterface.
// It is not a full implementation of the PSR-7 StreamInterface


class BodyStringStream implements \Psr\Http\Message\StreamInterface
{

    protected bool $eof = false;

    protected string $bodyContentString = '';

    public function __construct(?string $content = null)
    {
        $this->bodyContentString = $content;
    }

    public function __toString(): string
    {
        return $this->bodyContentString;
    }

    public function close(): void
    {
        return;
    }

    public function detach()
    {
        return null;
    }

    public function getSize(): int
    {
        return mb_strlen($this->bodyContentString, '8bit');
    }

    public function tell(): int
    {
        return 1;
    }


    public function eof(): bool
    {
        return $this->eof;
    }

    public function isSeekable(): bool
    {
        return false;
    }


    public function seek($offset, $whence = \SEEK_SET): void
    {

        // do nothing as this is not a seekable stream
        switch ($whence) {
        }

        $offset = (int) $offset;
    }


    public function rewind(): void
    {
        // do nothing as this is not a seekable stream
        $this->eof = false;
    }


    public function isWritable(): bool
    {
        return true;
    }

    public function write(string $content): int
    {
        $this->bodyContentString .= $content;
        return mb_strlen($this->bodyContentString, '8bit');
    }

    public function isReadable(): bool
    {
        return true;
    }


    public function read($length): string
    {
        $this->eof = true;
        return $this->bodyContentString;
    }

    public function getContents(): string
    {
        $this->eof = true;
        return $this->bodyContentString;
    }


    public function getMetadata($key = null)
    {
        $metadata = [
            'timed_out' => false,
            'blocked' => false,
            'eof' => $this->eof(),
            'unread_bytes' => $this->getSize(),
            'stream_type' => static::class,
            'wrapper_type' => 'data',
            'wrapper_data' => 'text/plain',
            'seekable' => false,
            'uri' => 'data:text/plain',
        ];

        if ($key && isset($metadata[$key])) {
            return $metadata[$key];
        } else {
            return $metadata;
        }
    }
}
