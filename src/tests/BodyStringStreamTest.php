<?php

use PHPUnit\Framework\TestCase;

class BodyStringStreamTest extends TestCase
{
    public function testConstructor()
    {
        $stream = new \FFPerera\Cubo\BodyStringStream('{"key": "value"}');
        $this->assertInstanceOf(\FFPerera\Cubo\BodyStringStream::class, $stream);
    }

    public function testGetContents()
    {
        $stream = new \FFPerera\Cubo\BodyStringStream('{"key": "value"}');
        $this->assertEquals('{"key": "value"}', $stream->getContents());
    }

    public function testIsSeekable()
    {
        $stream = new \FFPerera\Cubo\BodyStringStream('{"key": "value"}');
        $this->assertFalse($stream->isSeekable());
    }

    public function testIsReadable()
    {
        $stream = new \FFPerera\Cubo\BodyStringStream('{"key": "value"}');
        $this->assertTrue($stream->isReadable());
    }

    public function testIsWritable()
    {
        $stream = new \FFPerera\Cubo\BodyStringStream('{"key": "value"}');
        $this->assertTrue($stream->isWritable());
    }

    public function testWriteAndRead()
    {
        $stream = new \FFPerera\Cubo\BodyStringStream('{"key": "value"}');
        $stream->write('{"new_key": "new_value"}');
        $this->assertEquals('{"key": "value"}{"new_key": "new_value"}', $stream->getContents());

        // test read
        $stream->rewind();
        $this->assertEquals('{"key": "value"}{"new_key": "new_value"}', $stream->read(100));
        $this->assertEquals('{"key": "value"}{"new_key": "new_value"}', $stream->getContents());
    }

    public function testMetadata()
    {

        $content = '{"key": "value"}';
        $metadata = [
            'timed_out' => false,
            'blocked' => false,
            'eof' => false,
            'unread_bytes' => mb_strlen($content),
            'stream_type' => 'FFPerera\Cubo\BodyStringStream',
            'wrapper_type' => 'data',
            'wrapper_data' => 'text/plain',
            'seekable' => false,
            'uri' => 'data:text/plain',
        ];

        $stream = new \FFPerera\Cubo\BodyStringStream($content);
        $this->assertEquals($metadata, $stream->getMetadata());
        $this->assertEquals($metadata['timed_out'], $stream->getMetadata('timed_out'));
        $this->assertEquals($metadata['blocked'], $stream->getMetadata('blocked'));
        $this->assertEquals($metadata['eof'], $stream->getMetadata('eof'));
    }

    public function testCloseDetatchEof()
    {
        $stream = new \FFPerera\Cubo\BodyStringStream('{"key": "value"}');
        $this->assertFalse($stream->eof());
        $stream->read(100);
        $this->assertTrue($stream->eof());

        $stream->close();
        $stream->detach();
        $stream->seek(1);

        $stream->rewind();
        $this->assertFalse($stream->eof());
        $this->assertEquals('{"key": "value"}', $stream->getContents());
        $this->assertTrue($stream->eof());

        $this->assertEquals(1, $stream->tell());
    }
}
