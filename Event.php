<?php


namespace lkorponai\SimpleDispatcher;


class Event
{

    /** @var bool */
    private $propagationStopped = false;

    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    /**
     * @return boolean
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

}
