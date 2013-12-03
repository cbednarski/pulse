<?php

namespace cbednarski\Pulse;

class Healthcheck
{
    const INFO = 'info';
    const WARNING = 'warning';
    const CRITICAL = 'critical';

    private $description = null;
    private $callable = null;
    private $status = null;
    private $type = null;

    public function __construct($description, \Closure $callable, $type = self::CRITICAL)
    {
        $this->type = $type;
        $this->description = $description;
        $this->callable = $callable;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getStatus()
    {
        if ($this->status === null) {
            $this->status = $this->call();
        }

        return $this->status;
    }

    private function call()
    {
        $c = $this->callable;

        return $c();
    }
}
