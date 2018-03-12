<?php

namespace Aptenex\QTransform\TypeTransformer;

use Aptenex\QTransform\TransformOptions;

interface TransformInterface
{

    /**
     * @param mixed            $value
     * @param array            $allData
     * @param TransformOptions $options
     *
     * @return TransformResult|null
     */
    public function transform($value, $allData, TransformOptions $options);

}