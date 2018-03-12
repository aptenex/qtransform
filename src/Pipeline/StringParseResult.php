<?php

namespace Aptenex\QTransform\Pipeline;

use League\Pipeline\StageInterface;

class StringParseResult
{

    /**
     * @var string
     */
    private $currentKey;

    /**
     * @var StageInterface[]
     */
    private $pipeFunctions = [];

    /**
     * @param string   $oldKey
     * @param StageInterface[] $pipeFunctions
     */
    public function __construct($oldKey, array $pipeFunctions)
    {
        $this->currentKey = $oldKey;
        $this->pipeFunctions = $pipeFunctions;
    }

    /**
     * @return string
     */
    public function getCurrentKey(): string
    {
        return $this->currentKey;
    }

    /**
     * @return StageInterface[]
     */
    public function getPipeFunctions(): array
    {
        return $this->pipeFunctions;
    }

}