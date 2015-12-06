<?php

namespace AppTest\Action;

use Demeter\Action\CreateAction;
use Demeter\Storage\StorageInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class CreateActionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->storage = $this->prophesize(StorageInterface::class);
    }

    public function testCreateKeyWithEmptyValue()
    {
        $this->storage->has('notfound')->willReturn(false);
        $this->storage->delete('notfound')->willReturn(true);
        $action = new CreateAction($this->storage->reveal());

        $request = new ServerRequest([], [], '/key/found', 'DELETE');
        $request = $request->withAttribute('key', 'found');
        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
        $json = json_decode((string) $response->getBody());

        $this->assertTrue($response instanceof Response\JsonResponse);
        $this->assertSame(406, $response->getStatusCode());
        $this->assertTrue(isset($json->message));
    }

    public function testKeyFound()
    {
        $this->storage->has('found')->willReturn(true);
        $action = new CreateAction($this->storage->reveal());

        $request = new ServerRequest([], [], '/key/found', 'POST');//, null, ['Content-type'=>'application/json']);
        $request = $request->withAttribute('key', 'found')->withParsedBody(['value'=>123]);
        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
        $json = json_decode((string) $response->getBody());

        $this->assertTrue($response instanceof Response\JsonResponse);
        $this->assertSame(409, $response->getStatusCode());
        $this->assertTrue(isset($json->message));
    }

    public function testCreate()
    {
        $value = ['value'=>123];
        $this->storage->has('notfound')->willReturn(false);
        $this->storage->set('notfound', $value)->willReturn(null);
        $action = new CreateAction($this->storage->reveal());

        $request = new ServerRequest([], [], '/key/notfound', 'POST');//, null, ['Content-type'=>'application/json']);
        $request = $request->withAttribute('key', 'notfound')->withParsedBody($value);
        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
            $json = json_decode((string) $response->getBody());

            $this->assertTrue($response instanceof Response\JsonResponse);
            $this->assertSame(200, $response->getStatusCode());
            $this->assertEmpty($json);
    }

    public function testCreateWithJson()
    {
        $value = ['value'=>123,'_embedded'=>['key'=>'value']];
        $body = json_encode($value);
        $stream = new Stream('php://memory', 'wb+');
        $stream->write($body);
        $stream->rewind();

        $this->storage->has('notfound')->willReturn(false);
        $this->storage->set('notfound', $value)->willReturn(null);
        $action = new CreateAction($this->storage->reveal());

        $request = (new ServerRequest())
        ->withMethod('POST')
        ->withUri(new Uri('http://example.com/key/notfound'))
        ->withAddedHeader('Accept', 'application/json')
        ->withAddedHeader('Content-Type', 'application/json')
        ->withAttribute('key', 'notfound')
        ->withBody($stream);

        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
            $json = json_decode((string) $response->getBody());

            $this->assertTrue($response instanceof Response\JsonResponse);
            $this->assertSame(200, $response->getStatusCode());
            $this->assertEmpty($json);
    }
}
