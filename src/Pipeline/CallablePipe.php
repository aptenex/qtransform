<?php

namespace Aptenex\QTransform\Pipeline;

use League\Pipeline\StageInterface;

class CallablePipe implements StageInterface
{

    /**
     * @var callable
     */
    private $callable;

    /**
     * @param $callable
     */
    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param mixed $payload
     *
     * @return mixed
     */
    public function __invoke($payload)
    {
        $callable = $this->callable;

        return $callable($payload);
    }

}