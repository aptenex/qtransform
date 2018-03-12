<?php

namespace Aptenex\QTransform\TypeTransformer;

use Aptenex\QTransform\TransformOptions;

class StringTransformer implements TransformInterface
{

    public function transform($value, $allData, TransformOptions $options)
    {
        return new TransformResult($value);
    }

}