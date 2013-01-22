<?php

namespace cbednarski\Pulse;

class Healthcheck
{
    private $description = null;
    private $callable = null;
    private $status = null;

    public function __construct($description, \Closure $callable)
    {
        $this->description = $description;
        $this->callable = $callable;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getStatus()
    {
        if($this->status === null){;
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