<?php

namespace Aptenex\QTransform\Pipeline;

use Aptenex\QTransform\Exception\InvalidPipeException;
use Aptenex\QTransform\QTransform;
use League\Pipeline\StageInterface;

class PipeStringParser
{
    /**
     * @var array|StageInterface
     */
    private $pipeRegistry;

    /**
     * @param StageInterface[] $pipeRegistry
     */
    public function __construct(array $pipeRegistry)
    {
        $this->pipeRegistry = $pipeRegistry;
    }

    /**
     * @param $string
     *
     * @return StringParseResult
     *
     * @throws InvalidPipeException
     */
    public function parse($string)
    {
        if (is_null($string)) {
            return new StringParseResult(null, []);
        }

        $parts = explode(QTransform::PIPE_IDENTIFIER, $string);

        if (count($parts) === 1) {
            return new StringParseResult($parts[0], []);
        }

        $pipes = [];
        $oldKey = array_shift($parts);

        foreach($parts as $pipeFunction) {
            if (isset($this->pipeRegistry[$pipeFunction])) {
                $pipes[] = $this->pipeRegistry[$pipeFunction];
            } else if (function_exists($pipeFunction)) {
                // We can also use the callable function here too
                $pipes[] = new CallablePipe($pipeFunction);
            } else {
                throw new InvalidPipeException('Could not locate the pipe "' . $pipeFunction . '"');
            }
        }

        return new StringParseResult($oldKey, $pipes);
    }

}