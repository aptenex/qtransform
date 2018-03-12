<?php

namespace Aptenex\QTransform;

use Aptenex\QTransform\Exception\InvalidPipeException;
use Aptenex\QTransform\Exception\InvalidTransformerException;
use Aptenex\QTransform\Exception\MissingTransformerException;
use Aptenex\QTransform\Exception\ValueInvalidTransformationException;
use Aptenex\QTransform\MappingSpec\Field;
use Aptenex\QTransform\MappingSpec\MappingParser;
use Aptenex\QTransform\MappingSpec\RootMap;
use Aptenex\QTransform\Pipeline\CallablePipe;
use Aptenex\QTransform\Pipeline\PipeStringParser;
use Aptenex\QTransform\TypeTransformer\CallableTransformer;
use Aptenex\QTransform\TypeTransformer\TransformInterface;
use Aptenex\QTransform\TypeTransformer\TransformResult;
use League\Pipeline\Pipeline;
use League\Pipeline\StageInterface;

class QTransform
{

    /**
     * @var string
     */
    const PIPE_IDENTIFIER = '|';

    /**
     * @var StageInterface[]
     */
    private $pipeRegistry = [];

    /**
     * @var TransformInterface[]
     */
    private $transformerRegistry = [];

    public function __construct()
    {
        $this->pipeRegistry = [];
        $this->transformerRegistry = [
            'int'      => new TypeTransformer\IntegerTransformer(),
            'date'     => new TypeTransformer\DateTransformer(),
            'datetime' => new TypeTransformer\DateTimeTransformer(),
            'float'    => new TypeTransformer\FloatTransformer(),
            'string'   => new TypeTransformer\StringTransformer(),
            'country'  => new TypeTransformer\CountryTransformer(),
            'default'  => new TypeTransformer\DefaultTransformer()
        ];
    }

    /**
     * @param array $rawData
     * @param array $mapping
     * @param TransformOptions $options
     *
     * @return array
     *
     * @throws Exception\MissingMappingFieldException
     * @throws InvalidPipeException
     * @throws MissingTransformerException
     * @throws ValueInvalidTransformationException
     */
    public function transformToArray($rawData, $mapping, TransformOptions $options = null)
    {
        $options = !is_null($options) ? $options : new TransformOptions();

        $t = [];

        $parsedMapping = $this->parseMapping($mapping, $options);

        foreach ($parsedMapping->getFields() as $field) {
            $this->transformField($t, $field, $rawData, $options);
        }

        return $t;
    }

    /**
     * @param mixed $object
     * @param array $rawData
     * @param array $mapping
     * @param TransformOptions $options
     *
     * @throws Exception\MissingMappingFieldException
     * @throws InvalidPipeException
     * @throws MissingTransformerException
     * @throws ValueInvalidTransformationException
     */
    public function transformToObject($object, $rawData, $mapping, TransformOptions $options = null)
    {
        $arrayData = $this->transformToArray($rawData, $mapping, $options);

        foreach ($arrayData as $key => $value) {
            $setter = sprintf('set%s', ucfirst($key));
            if (method_exists($object, $setter)) {
                $object->$setter($value);
            }
        }
    }

    /**
     * @param array            $mapping
     * @param TransformOptions $options
     *
     * @return RootMap
     *
     * @throws Exception\MissingMappingFieldException
     * @throws InvalidPipeException
     */
    public function parseMapping(array $mapping, TransformOptions $options)
    {
        return (new MappingParser(new PipeStringParser($this->pipeRegistry), $options))->parse($mapping);
    }

    /**
     * @param array $newData
     * @param Field $field
     * @param array $rawData
     * @param TransformOptions|null $options
     *
     * @throws MissingTransformerException
     * @throws ValueInvalidTransformationException
     */
    private function transformField(&$newData, Field $field, array $rawData, TransformOptions $options = null)
    {
        // If no current key is set, then we cannot utilize the defaults functionality
        if ($field->hasCurrentKey()) {
            // First check if the data exists
            if (!ArrayUtils::has($field->getCurrentKey(), $rawData)) {
                if ($field->getAbsentMode() === Field::ABSENT_VALUE_DO_NOT_ADD) {
                    return; // Do nothing
                } elseif ($field->getAbsentMode() === Field::ABSENT_VALUE_THROW_EXCEPTION) {
                    throw new ValueInvalidTransformationException(vsprintf(
                        'Could not transform data due to the field "%s" not existing',
                        [
                            $field->getCurrentKey()
                        ]
                    ));
                }

                // Set default value
                ArrayUtils::set($field->getNewKey(), $field->getDefaultValue(), $newData);

                return;
            }
        }

        // Data does exist if we've reached here
        $rawDataFieldValue = null;
        if ($field->hasCurrentKey()) {
            $rawDataFieldValue = ArrayUtils::get($field->getCurrentKey(), $rawData);
        }

        // First lets get it through the transformers
        if ($field->getType() === 'custom') {
            // Special case
            $transformer = new CallableTransformer($field->getCallback());
        } else {
            if (!isset($this->transformerRegistry[$field->getType()])) {
                throw new MissingTransformerException('Could not locate the transformer for type "' . $field->getType() . '"');
            }

            $transformer = $this->transformerRegistry[$field->getType()];
        }

        // Got the transformer
        $transformResult = $transformer->transform($rawDataFieldValue, $rawData, $options);

        if (!$transformResult instanceof TransformResult) {
            // If a 'null' is returned in this instant, we will now revert to the default options once again
            // as special transformers like 'date' might fail and need to not add the field / set defaults
            if ($field->getAbsentMode() === Field::ABSENT_VALUE_DO_NOT_ADD) {
                return; // Do nothing
            } elseif ($field->getAbsentMode() === Field::ABSENT_VALUE_THROW_EXCEPTION) {
                throw new ValueInvalidTransformationException(vsprintf(
                    'Could not transform data due to the field "%s" not existing',
                    [
                        $field->getCurrentKey()
                    ]
                ));
            }

            // Set default value
            ArrayUtils::set($field->getNewKey(), $field->getDefaultValue(), $newData);

            return;
        }

        $transformedValue = $transformResult->getData();

        // If this field has any 'pipe' functions now lets execute them
        if (count($field->getPipeFunctions()) > 0) {
            $pipeline = new Pipeline($field->getPipeFunctions());
            $transformedValue = $pipeline->process($transformedValue);
        }

        ArrayUtils::set($field->getNewKey(), $transformedValue, $newData);
    }

    /**
     * @return StageInterface[]
     */
    public function getPipeRegistry(): array
    {
        return $this->pipeRegistry;
    }

    /**
     * @return TransformInterface[]
     */
    public function getTransformerRegistry(): array
    {
        return $this->transformerRegistry;
    }

    /**
     * @param string $key
     * @param TransformInterface|callable $transformer
     *
     * @throws InvalidTransformerException
     */
    public function addTransformer($key, $transformer)
    {
        if ($key === 'custom') {
            throw new InvalidTransformerException('The "custom" type is reserved for the callback functionality');
        }

        if (is_callable($transformer)) {
            $transformer = new CallableTransformer($transformer);
        }

        if (!$transformer instanceof TransformInterface) {
            throw new InvalidTransformerException("Only callable functions and classes implementing the \Aptenex\QTransform\TypeTransformer interface can be used");
        }

        $this->transformerRegistry[$key] = $transformer;
    }

    /**
     * @param string $key
     * @param StageInterface|callable $pipe
     *
     * @throws InvalidPipeException
     */
    public function addPipe($key, $pipe)
    {
        if (is_callable($pipe)) {
            $pipe = new CallablePipe($pipe);
        }

        if (!$pipe instanceof StageInterface) {
            throw new InvalidPipeException("Only callable functions and classes implementing the \League\Pipeline\StageInterface can be used");
        }

        $this->pipeRegistry[$key] = $pipe;
    }

}