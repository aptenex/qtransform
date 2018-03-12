<?php

namespace Aptenex\QTransform\TypeTransformer;

use Aptenex\QTransform\TransformOptions;

class CallableTransformer implements TransformInterface
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
     * @param mixed            $value
     * @param array            $allData
     * @param TransformOptions $options
     *
     * @return mixed
     */
    public function transform($value, $allData, TransformOptions $options)
    {
        $callable = $this->callable;

        return new TransformResult($callable($value, $allData, $options));
    }

}