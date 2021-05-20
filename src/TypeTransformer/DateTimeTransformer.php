<?php

namespace Aptenex\QTransform\TypeTransformer;

use Aptenex\QTransform\DateUtils;
use Aptenex\QTransform\TransformOptions;

class DateTimeTransformer implements TransformInterface
{

    public function transform($value, $allData, TransformOptions $options)
    {
        if (!DateUtils::isValidDate($value)) {
            return null; // Not a valid date
        }

        $obj = (new \DateTime($value));

        if ($options->isConvertToDateObjects()) {
            return new TransformResult($obj);
        }

        return new TransformResult($obj->format("Y-m-d H:i:s"));
    }

}