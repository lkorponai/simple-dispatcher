<?php


namespace lkorponai\SimpleDispatcher\Tests;


use lkorponai\SimpleDispatcher\Dispatcher;
use lkorponai\SimpleDispatcher\Event;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{

    public function testAdd()
    {
        $dispatcher = new Dispatcher();

        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listen', 1);
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation', 2);
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listen');
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation');

        $this->assertEquals(
            array(
                2 => array('lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation'),
                1 => array('lkorponai\SimpleDispatcher\Tests\DummyListener::listen'),
                0 => array(
                    'lkorponai\SimpleDispatcher\Tests\DummyListener::listen',
                    'lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation',
                ),
            ),
            $dispatcher->getEventListeners('testEvent')
        );
    }

    public function testRemove()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listen');
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation');

        $dispatcher->removeEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listen');

        $this->assertEquals(
            array(
                0 => array(
                    'lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation',
                ),
            ),
            $dispatcher->getEventListeners('testEvent')
        );

    }

    public function testGet()
    {
        $dispatcher = new Dispatcher();

        $this->assertEquals(array(), $dispatcher->getEventListeners());
        $this->assertEquals(array(), $dispatcher->getEventListeners('nonRegisteredEvent'));

        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listen', 5);

        $this->assertEquals(
            array(
                'testEvent' => array(
                    5 => array(
                        'lkorponai\SimpleDispatcher\Tests\DummyListener::listen',
                    ),
                ),
            ),
            $dispatcher->getEventListeners()
        );

    }

    public function testHas()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listen');
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation');

        $this->assertTrue($dispatcher->hasEventListener('testEvent'));

        $dispatcher->removeEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listen');
        $dispatcher->removeEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation');

        $this->assertFalse($dispatcher->hasEventListener());
    }


    public function testDispatch()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listen');
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation');
        $dispatcher->addEventListener('testEvent', 'lkorponai\SimpleDispatcher\Tests\DummyListener::listen');

        $event = new DummyEvent();

        $dispatcher->dispatch('testEvent', $event);

        $dispatcher->dispatch('nonRegisteredEvent');

        $this->assertEquals(
            array(
                'lkorponai\SimpleDispatcher\Tests\DummyListener::listen',
                'lkorponai\SimpleDispatcher\Tests\DummyListener::listenAndStopPropagation',
            ),
            $event->listenersCalled
        );
    }

}

class DummyEvent extends Event
{

    public $listenersCalled = array();

}

class DummyListener
{

    public static function listen(DummyEvent $event)
    {
        $event->listenersCalled[] = __METHOD__;
    }

    public static function listenAndStopPropagation(DummyEvent $event)
    {
        $event->stopPropagation();
        $event->listenersCalled[] = __METHOD__;
    }

}
