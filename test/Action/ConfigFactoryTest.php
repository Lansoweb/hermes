<?php
namespace AppTest\Action;

use Demeter\Action\GetAction;
use Demeter\Action\ConfigFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;
use Demeter\Action\CreateAction;
use Demeter\Action\DeleteAction;
use Demeter\Action\UpdateAction;

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
