<?php
namespace AppTest\Action;

use Hermes\Action\GetAction;
use Hermes\Action\ConfigFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;
use Hermes\Action\CreateAction;
use Hermes\Action\DeleteAction;
use Hermes\Action\UpdateAction;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->factory = new ConfigFactory();
    }

    public function testCanCreateServiceWithName()
    {
        $sm = new ServiceManager(new Config([]));
        $this->assertTrue($this->factory->canCreateServiceWithName($sm, GetAction::class, GetAction::class));
        $this->assertTrue($this->factory->canCreateServiceWithName($sm, CreateAction::class, CreateAction::class));
        $this->assertTrue($this->factory->canCreateServiceWithName($sm, DeleteAction::class, DeleteAction::class));
        $this->assertTrue($this->factory->canCreateServiceWithName($sm, UpdateAction::class, UpdateAction::class));
    }

    public function testCreateServiceWithName()
    {
        $sm = new ServiceManager(new Config([]));
        $this->assertInstanceOf(
            GetAction::class,
            $this->factory->createServiceWithName($sm, GetAction::class, GetAction::class)
        );
    }
}
