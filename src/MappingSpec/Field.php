<?php

namespace Aptenex\QTransform\MappingSpec;

use League\Pipeline\StageInterface;

class Field
{

    const ABSENT_VALUE_DO_NOT_ADD = 'DO_NOT_ADD';
    const ABSENT_VALUE_USE_DEFAULT = 'USE_DEFAULT';
    const ABSENT_VALUE_THROW_EXCEPTION = 'THROW_EXCEPTION';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $newKey;

    /**
     * @var string
     */
    private $currentKey;

    /**
     * @var callable|null
     */
    private $callback;

    /**
     * @var mixed
     */
    private $defaultValue = null;

    /**
     * @var string
     */
    private $absentMode = self::ABSENT_VALUE_USE_DEFAULT;

    /**
     * @var StageInterface[]
     */
    private $pipeFunctions = [];

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getNewKey(): string
    {
        return $this->newKey;
    }

    /**
     * @param string $newKey
     */
    public function setNewKey(string $newKey)
    {
        $this->newKey = $newKey;
    }

    /**
     * @return bool
     */
    public function hasCurrentKey()
    {
        return !empty($this->currentKey);
    }

    /**
     * @return string
     */
    public function getCurrentKey(): string
    {
        return $this->currentKey;
    }

    /**
     * @param string $currentKey
     */
    public function setCurrentKey(string $currentKey)
    {
        $this->currentKey = $currentKey;
    }

    /**
     * @return null|callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param null|callable $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return StageInterface[]
     */
    public function getPipeFunctions(): array
    {
        return $this->pipeFunctions;
    }

    /**
     * @param StageInterface[] $pipeFunctions
     */
    public function setPipeFunctions(array $pipeFunctions)
    {
        $this->pipeFunctions = $pipeFunctions;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
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