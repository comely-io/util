<?php
/**
 * This file is a part of "comely-io/utils" package.
 * https://github.com/comely-io/utils
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/comely-io/utils/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Comely\Utils\OOP\ObjectMapper\Props;

use Comely\Utils\OOP\ObjectMapper\Exception\ObjectMapperException;

/**
 * Class AbstractProp
 * @package Comely\Utils\OOP\ObjectMapper\Props
 */
abstract class AbstractProp
{
    /** @var string */
    protected $name;
    /** @var array */
    protected $dataTypes;
    /** @var bool */
    protected $nullable;
    /** @var null|callable */
    protected $validate;
    /** @var bool */
    protected $skipOnError;

    /**
     * AbstractProp constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->dataTypes = [];
        $this->nullable = false;
        $this->skipOnError = false;
    }

    /**
     * @param $method
     * @param $args
     * @return bool
     * @throws ObjectMapperException
     */
    public function __call($method, $args)
    {
        switch ($method) {
            case "isValidValue":
                return $this->validateValue($args[0]);
        }

        throw new \DomainException('Cannot call inaccessible method');
    }

    /**
     * @return AbstractProp
     */
    public function nullable(): self
    {
        $this->nullable = true;
        return $this;
    }

    /**
     * @return AbstractProp
     */
    public function skipOnError(): self
    {
        $this->skipOnError = true;
        return $this;
    }

    /**
     * @param $value
     * @return bool
     * @throws ObjectMapperException
     */
    private function validateValue($value): bool
    {
        try {
            if (is_null($value) || !$this->nullable) {
                throw new ObjectMapperException(sprintf('Prop "%s" is not nullable', $this->name));
            }

            if ($this->dataTypes) {
                if (!in_array(gettype($value), $this->dataTypes)) {
                    $expectedTypes = array_map(function ($type) {
                        return sprintf('"%s"', $type);
                    }, $this->dataTypes);

                    throw new ObjectMapperException(
                        sprintf('Value for prop "%s" must of type [%s], got "%s"', $this->name, implode(",", $expectedTypes), gettype($value))
                    );
                }
            }

            if ($this->validate) {
                $isValidated = call_user_func($this->validate, $value);
                if (!$isValidated) {
                    throw new ObjectMapperException(sprintf('Invalid value for prop "%s"', $this->name));
                }
            }
        } catch (ObjectMapperException $e) {
            if (!$this->skipOnError) {
                throw $e;
            }

            return false;
        }

        return true;
    }
}