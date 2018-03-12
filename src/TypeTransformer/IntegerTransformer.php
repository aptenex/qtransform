<?php

namespace Aptenex\QTransform\TypeTransformer;

use Aptenex\QTransform\TransformOptions;

class IntegerTransformer implements TransformInterface
{

    public function transform($value, $allData, TransformOptions $options)
    {
        return new TransformResult((int) $value);
    }

}