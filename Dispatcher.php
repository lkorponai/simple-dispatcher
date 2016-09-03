<?php

namespace lkorponai\SimpleDispatcher;


class Dispatcher
{

    /** @var array */
    protected $eventListeners = array();

    public function addEventListener($eventName, callable $callable, $priority = 0)
    {
        $this->eventListeners[$eventName][$priority][] = $callable;
        ksort($this->eventListeners[$eventName]);
    }

    public function removeEventListener($eventName, $listener)
    {
        foreach ($this->getEventListeners($eventName) as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                unset($this->eventListeners[$eventName][$priority][$key]);
                $this->eventListeners[$eventName][$priority] = array_values($this->eventListeners[$eventName][$priority]);

                if (!count($this->eventListeners[$eventName][$priority])) {
                    unset($this->eventListeners[$eventName][$priority]);
                }

                if (!count($this->eventListeners[$eventName])) {
                    unset($this->eventListeners[$eventName]);
                }
            }
        }
    }

    public function getEventListeners($eventName = null)
    {
        if (null !== $eventName && !array_key_exists($eventName, $this->eventListeners)) {
            return array();
        }

        return null === $eventName ? $this->eventListeners : $this->eventListeners[$eventName];
    }

    public function hasEventListener($eventName = null)
    {
        return (bool)count($this->getEventListeners($eventName));
    }

    public function dispatch($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        foreach ($this->getEventListeners($eventName) as $priority => $listeners) {
            foreach ($listeners as $callable) {
                if ($event->isPropagationStopped()) {
                    break;
                }
                call_user_func($callable, $event, $eventName, $this);
            }
        }
    }

}
