<?php

namespace AppTest\Action;

use Demeter\Action\CreateAction;
use Demeter\Storage\StorageInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;
use Demeter\Action\UpdateAction;

class UpdateActionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->storage = $this->prophesize(StorageInterface::class);
    }

    public function testUpdateKeyWithEmptyValue()
    {
        $this->storage->has('found')->willReturn(true);
        $action = new UpdateAction($this->storage->reveal());

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

    public function testKeyNotFound()
    {
        $this->storage->has('notfound')->willReturn(false);
        $action = new UpdateAction($this->storage->reveal());

        $request = new ServerRequest([], [], '/key/notfound', 'POST');
        $request = $request->withAttribute('key', 'notfound')->withParsedBody(['value'=>123]);
        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
        $json = json_decode((string) $response->getBody());

        $this->assertTrue($response instanceof Response\JsonResponse);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertTrue(isset($json->message));
    }

    public function testUpdate()
    {
        $value = ['value'=>123];
        $this->storage->has('found')->willReturn(true);
        $this->storage->set('found', $value)->willReturn(null);
        $action = new UpdateAction($this->storage->reveal());

        $request = new ServerRequest([], [], '/key/found', 'PUT');
        $request = $request->withAttribute('key', 'found')->withParsedBody($value);
        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
        $json = json_decode((string) $response->getBody());

        $this->assertTrue($response instanceof Response\JsonResponse);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertEmpty($json);
    }

    public function testUpdateWithJson()
    {
        $value = ['value'=>123,'_embedded'=>['key'=>'value']];
        $body = json_encode($value);
        $stream = new Stream('php://memory', 'wb+');
        $stream->write($body);
        $stream->rewind();

        $this->storage->has('found')->willReturn(true);
        $this->storage->set('found', $value)->willReturn(null);
        $action = new UpdateAction($this->storage->reveal());

        $request = (new ServerRequest())
        ->withMethod('PUT')
        ->withUri(new Uri('http://example.com/key/found'))
        ->withAddedHeader('Accept', 'application/json')
        ->withAddedHeader('Content-Type', 'application/json')
        ->withAttribute('key', 'found')
        ->withBody($stream);

        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
            $json = json_decode((string) $response->getBody());

            $this->assertTrue($response instanceof Response\JsonResponse);
            $this->assertSame(200, $response->getStatusCode());
            $this->assertEmpty($json);
    }

    public function testUpdateWithPatchJson()
    {
        $value = ['value'=>123,'_embedded'=>['key'=>'value']];
        $body = json_encode($value);
        $stream = new Stream('php://memory', 'wb+');
        $stream->write($body);
        $stream->rewind();

        $this->storage->has('found')->willReturn(true);
        $this->storage->get('found')->willReturn($value);
        $this->storage->set('found', $value)->willReturn(null);
        $action = new UpdateAction($this->storage->reveal());

        $request = (new ServerRequest())
        ->withMethod('PATCH')
        ->withUri(new Uri('http://example.com/key/found'))
        ->withAddedHeader('Accept', 'application/json')
        ->withAddedHeader('Content-Type', 'application/json')
        ->withAttribute('key', 'found')
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
