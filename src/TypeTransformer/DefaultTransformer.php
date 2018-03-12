<?php

namespace Aptenex\QTransform\TypeTransformer;

use Aptenex\QTransform\TransformOptions;

class DefaultTransformer implements TransformInterface
{

    public function transform($value, $allData, TransformOptions $options)
    {
        if ($options->isIntelliCast()) {
            if (is_integer($value)) {
                $value = (int) $value;
            } else if (is_float($value)) {
                $value = (float) $value;
            }
        }

        return new TransformResult($value);
    }

}