<?php

namespace cbednarski\Pulse;

class Pulse
{
    private $healthchecks = array();

    /**
     * Convenience function for adding simple healthchecks.
     *
     * @param string $description A description of this check
     * @param Closure $healthcheck A callable that returns true when the check passes, false on failure
     */
    public function add($description, \Closure $healthcheck)
    {
        $this->healthchecks[] = new Healthcheck($description, $healthcheck);
    }

    /**
     * Add an instance of healthcheck, useful if you want to subclass
     * the healthcheck class and add custom behavior.
     *
     * @param Healthcheck $healthcheck
     */
    public function addHealthcheck(Healthcheck $healthcheck)
    {
        $this->healthchecks[] = $healthcheck;
    }

    /**
     * Evaluate all healthchecks and return a boolean based on the aggregate.
     *
     * @return bool true if all tests pass, false otherwise
     */
    public function getStatus()
    {
        $status = true;

        foreach ($this->healthchecks as $healthcheck) {
            // Shortcut the rest if any check fails
            $status = $status && $healthcheck->getStatus();
        }

        return $status;
    }

    /**
     * @return Array List of all healthchecks currently registered
     */
    public function getHealthchecks()
    {
        return $this->healthchecks;
    }

    /**
     * Evaluate all healthchecks and output a summary, using Formatter->autoexec()
     */
    public function check()
    {
        $results = array();

        $formatter = new Formatter($this->healthchecks);
        $formatter->autoexec();
    }
}