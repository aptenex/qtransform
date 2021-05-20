<?php

namespace Aptenex\QTransform\TypeTransformer;

use Aptenex\QTransform\ArrayUtils;
use Aptenex\QTransform\TransformOptions;

class CountryTransformer implements TransformInterface
{

    /**
     * @var array
     */
    private $countryExceptionMappings = [
        'usa' => 'US'
    ];

    public function transform($value, $allData, TransformOptions $options)
    {
        if (!is_null($value)) {
            if (strlen($value) === 2) {
                return new TransformResult(strtoupper($value));
            } else {
                $reversedMap = array_flip(ArrayUtils::getCountryMapISO2ToName());
                $countryName = ucwords($value);

                if (isset($this->countryExceptionMappings[strtolower($value)])) {
                    $value = $this->countryExceptionMappings[strtolower($value)];

                    return new TransformResult($value);
                } else if (isset($reversedMap[$countryName])) {
                    $value = $reversedMap[$countryName];

                    return new TransformResult($value);
                }
            }
        }

        return null;
    }

}