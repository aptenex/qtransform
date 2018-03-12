<?php

namespace Aptenex\QTransform;

use Aptenex\QTransform\MappingSpec\Field;

class TransformOptions
{

    /**
     * Attempt to intelligently cast values when no extra configuration is specified
     *
     * @var bool
     */
    private $intelliCast = true;

    /**
     * Whether or not to transform date / datetime strings into objects
     *
     * @var bool
     */
    private $convertToDateObjects = false;

    /**
     * @var string
     */
    private $absentMode = Field::ABSENT_VALUE_USE_DEFAULT;

    /**
     * @return bool
     */
    public function isIntelliCast(): bool
    {
        return $this->intelliCast;
    }

    /**
     * @param bool $intelliCast
     */
    public function setIntelliCast(bool $intelliCast)
    {
        $this->intelliCast = $intelliCast;
    }

    /**
     * @return bool
     */
    public function isConvertToDateObjects(): bool
    {
        return $this->convertToDateObjects;
    }

    /**
     * @param bool $convertToDateObjects
     */
    public function setConvertToDateObjects(bool $convertToDateObjects)
    {
        $this->convertToDateObjects = $convertToDateObjects;
    }

    /**
     * @return string
     */
    public function getAbsentMode(): string
    {
        return $this->absentMode;
    }

    /**
     * @param string $absentMode
     */
    public function setAbsentMode(string $absentMode)
    {
        $this->absentMode = $absentMode;
    }

}