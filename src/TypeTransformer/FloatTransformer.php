<?php

namespace Aptenex\QTransform\TypeTransformer;

use Aptenex\QTransform\TransformOptions;

class FloatTransformer implements TransformInterface
{

    public function transform($value, $allData, TransformOptions $options)
    {
        return new TransformResult((float) $value);
    }

}