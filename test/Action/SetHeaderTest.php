<?php

namespace AppTest\Action;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Demeter\Action\GetAction;
use Demeter\Storage\StorageInterface;
use Demeter\Action\SetHeader;

class SetHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->storage = $this->prophesize(StorageInterface::class);
    }

    public function testSetHeader()
    {
        $this->storage->getIndex()->willReturn('2');

        $action = new SetHeader($this->storage->reveal());
        $response = $action(new ServerRequest(['/key/notfound']), new Response(), function ($request, $response) {
            return $response;
        });

        $this->assertSame('2', $response->getHeaderLine('X-Demeter-Index'));
    }
}
