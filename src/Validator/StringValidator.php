<?php
/*
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

namespace Comely\Utils\Validator;

use Comely\Utils\Validator\Exception\ValidatorException;

/**
 * Class StringValidator
 * @package Comely\Utils\Validator
 */
class StringValidator extends AbstractValidator
{
    /** @var int */
    private const CHANGE_CASE_LC = 0x01;
    /** @var int */
    private const CHANGE_CASE_UC = 0x02;

    /** @var array|null */
    private ?array $enum = null;
    /** @var null|int */
    private ?int $exactLen = null;
    /** @var int|null */
    private ?int $minLen = null;
    /** @var int|null */
    private ?int $maxLen = null;
    /** @var string|null */
    private ?string $matchExp = null;
    /** @var int|null */
    private ?int $changeCase = null;

    /**
     * @param int|null $exact
     * @param int|null $min
     * @param int|null $max
     * @return $this
     */
    public function len(?int $exact = null, ?int $min = null, ?int $max = null): static
    {
        if ($exact > 0) {
            $this->exactLen = $exact;
            $this->minLen = null;
            $this->maxLen = null;
            return $this;
        }

        $this->exactLen = null;
        $this->minLen = $min > 0 ? $min : null;
        $this->maxLen = $max > 0 ? $max : null;
        return $this;
    }

    /**
     * @param string $regexExp
     * @return $this
     */
    public function match(string $regexExp): static
    {
        $this->matchExp = $regexExp;
        return $this;
    }

    /**
     * @param string ...$opts
     * @return $this
     */
    public function enum(string ...$opts): static
    {
        $this->enum = $opts;
        return $this;
    }

    /**
     * @return $this
     */
    public function lowerCase(): static
    {
        $this->changeCase = self::CHANGE_CASE_LC;
        return $this;
    }

    /**
     * @return $this
     */
    public function upperCase(): static
    {
        $this->changeCase = self::CHANGE_CASE_UC;
        return $this;
    }

    /**
     * @param mixed $value
     * @param false $emptyStrIsNull
     * @return string|null
     * @throws ValidatorException
     */
    public function getNullable(mixed $value, bool $emptyStrIsNull = false): ?string
    {
        if (is_null($value) || ($emptyStrIsNull && is_string($value) && !$value)) {
            return null;
        }

        return $this->getValidated($value);
    }

    /**
     * @param mixed $value
     * @return string
     * @throws ValidatorException
     */
    public function getValidated(mixed $value): string
    {
        // Type
        if (!is_string($value)) {
            throw new ValidatorException(code: Validator::INVALID_TYPE_ERROR);
        }

        // Check length
        if ($this->exactLen) {
            if (strlen($value) !== $this->exactLen) {
                throw new ValidatorException(code: Validator::LENGTH_ERROR);
            }
        } elseif ($this->minLen || $this->maxLen) {
            $len = strlen($value);
            if ($this->minLen && $len < $this->minLen) {
                throw new ValidatorException(code: Validator::LENGTH_UNDERFLOW_ERROR);
            }

            if ($this->maxLen && $len > $this->maxLen) {
                throw new ValidatorException(code: Validator::LENGTH_OVERFLOW_ERROR);
            }
        }

        // Change Case
        if ($this->changeCase) {
            $value = match ($this->changeCase) {
                self::CHANGE_CASE_UC => strtoupper($value),
                default => strtolower($value)
            };
        }

        // PREG pattern match
        if ($this->matchExp && !preg_match($this->matchExp, $value)) {
            throw new ValidatorException(code: Validator::REGEX_MATCH_ERROR);
        }

        // Check if is in defined Array
        if ($this->enum) {
            if (!in_array($value, $this->enum)) {
                throw new ValidatorException(code: Validator::ENUM_ERROR);
            }
        }

        // Custom validator
        if ($this->customValidator) {
            $value = call_user_func($this->customValidator, $value);
            if (!is_string($value)) {
                throw new ValidatorException(code: Validator::CALLBACK_TYPE_ERROR);
            }
        }

        return $value;
    }
}
