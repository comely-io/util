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
 * Class IntegerValidator
 * @package Comely\Utils\Validator
 */
class IntegerValidator extends AbstractValidator
{
    /** @var array|null */
    private ?array $enum = null;
    /** @var null|int */
    private ?int $min = null;
    /** @var null|int */
    private ?int $max = null;
    /** @var null|bool */
    private ?bool $unSigned = true;

    /**
     * @param int ...$nums
     * @return $this
     */
    public function enum(int ...$nums): static
    {
        $this->enum = $nums;
        return $this;
    }

    /**
     * @param int|null $min
     * @param int|null $max
     * @return $this
     */
    public function range(?int $min = null, ?int $max = null): self
    {
        $this->min = $min;
        $this->max = $max;
        return $this;
    }

    /**
     * @return $this
     */
    public function signed(): self
    {
        $this->unSigned = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function unSigned(): self
    {
        $this->unSigned = true;
        return $this;
    }

    /**
     * @param mixed $value
     * @param bool $zeroIsNull
     * @return int|null
     * @throws ValidatorException
     */
    public function getNullable(mixed $value, bool $zeroIsNull = false): ?int
    {
        if (is_null($value) || ($zeroIsNull && !$value)) {
            return null;
        }

        return $this->getValidated($value);
    }

    /**
     * @param mixed $value
     * @return int
     * @throws ValidatorException
     */
    public function getValidated(mixed $value): int
    {
        if (is_string($value)) {
            if (!preg_match('/^-?[1-9][0-9]*$/', $value)) {
                throw new ValidatorException(code: Validator::INVALID_TYPE_ERROR);
            }

            $num = gmp_init($value, 10);
            if (gmp_sign($num) >= 0) { // Unsigned
                if (gmp_cmp($num, PHP_INT_MAX) > 0) {
                    throw new ValidatorException(code: Validator::INTEGER_OVERFLOW_ERROR);
                }
            } else {
                if (gmp_cmp($num, PHP_INT_MIN) < 0) {
                    throw new ValidatorException(code: Validator::INTEGER_UNDERFLOW_ERROR);
                }
            }

            $value = gmp_intval($value);
        }

        if (!is_int($value)) {
            throw new ValidatorException(code: Validator::INVALID_TYPE_ERROR);
        }

        if (is_bool($this->unSigned) && $value < 0) {
            throw new ValidatorException(code: Validator::SIGNED_INTEGER_ERROR);
        }

        if ($this->min && $value < $this->min) {
            throw new ValidatorException(code: Validator::RANGE_UNDERFLOW_ERROR);
        }

        if ($this->max && $value > $this->max) {
            throw new ValidatorException(code: Validator::RANGE_OVERFLOW_ERROR);
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
            if (!is_int($value)) {
                throw new ValidatorException(code: Validator::CALLBACK_TYPE_ERROR);
            }
        }

        return $value;
    }
}
