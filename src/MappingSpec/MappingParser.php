<?php

namespace Aptenex\QTransform\MappingSpec;

use Aptenex\QTransform\ArrayUtils;
use Aptenex\QTransform\Exception\MissingMappingFieldException;
use Aptenex\QTransform\Pipeline\PipeStringParser;
use Aptenex\QTransform\TransformOptions;

class MappingParser
{

    /**
     * @var PipeStringParser
     */
    private $pipeStringParser;

    /**
     * @var TransformOptions
     */
    private $options;

    /**
     * @param PipeStringParser $pipeStringParser
     * @param TransformOptions $options
     */
    public function __construct(PipeStringParser $pipeStringParser, TransformOptions $options)
    {
        $this->pipeStringParser = $pipeStringParser;
        $this->options = $options;
    }

    /**
     * @param $map
     *
     * @return RootMap
     *
     * @throws MissingMappingFieldException
     *
     * @throws \Aptenex\QTransform\Exception\InvalidPipeException
     */
    public function parse($map)
    {
        $rm = new RootMap();

        foreach($map as $newKey => $config) {
            $f = new Field();

            $f->setNewKey($newKey);

            if (is_string($config)) {
                $result = $this->pipeStringParser->parse($config);

                $f->setType('default'); // Set to default
                $f->setCurrentKey($result->getCurrentKey());
                $f->setPipeFunctions($result->getPipeFunctions());
            } elseif (is_array($config)) {
                if (!isset($config['type'])) {
                    throw new MissingMappingFieldException('Missing the mapping option "type" at key index ' . $newKey);
                }

                if (!isset($config['field'])) {
                    throw new MissingMappingFieldException('Missing the mapping option "field" at key index ' . $newKey);
                }


                $field = ArrayUtils::get('field', $config, null);
                $result = $this->pipeStringParser->parse($field);

                $f->setCurrentKey($result->getCurrentKey());
                $f->setPipeFunctions($result->getPipeFunctions());
                $f->setNewKey(ArrayUtils::get('newKey', $config, $newKey)); // Probably never used
                $f->setType(ArrayUtils::get('type', $config, null));
                $f->setDefaultValue(ArrayUtils::get('default', $config, null));
                $f->setAbsentMode(ArrayUtils::get('absentMode', $config, $this->options->getAbsentMode()));

                if ($f->getType() === 'custom' && (!isset($config['callback']) || !is_callable($config['callback']))) {
                    throw new MissingMappingFieldException('Missing the mapping option "callback" at key index ' . $newKey . ' as the "type" is "custom"');
                }

                if (isset($config['callback']) && is_callable($config['callback'])) {
                    $f->setCallback($config['callback']);
                }
            } elseif (is_callable($config)) {
                // This is a shortcut for the custom callback
                $f->setType('custom');
                $f->setCallback($config);
            }

            $rm->addField($f);
        }

        return $rm;
    }

}