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

namespace Comely\Utils\Math;

use Comely\Utils\DataTypes;

/**
 * Class BigNumber
 * @package Comely\Utils\Math
 */
class BigNumber
{
    /** @var string */
    private string $value;
    /** @var int */
    private int $scale;

    /**
     * BigNumber constructor.
     * @param string|int|float|BigInteger $value
     * @param int $scale
     */
    public function __construct(string|int|float|BigInteger $value, int $scale = 18)
    {
        $this->value = $this->checkValidNum($value);
        $this->changeScale($scale);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            "value" => $this->value,
            "scale" => $this->scale
        ];
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return [
            "value" => $this->value,
            "scale" => $this->scale
        ];
    }

    /**
     * @param array $data
     */
    public function __unserialize(array $data): void
    {
        $this->value = $this->checkValidNum($data["value"]);
        $this->changeScale($data["scale"]);
    }

    /**
     * @param int $scale
     * @return $this
     */
    public function changeScale(int $scale): self
    {
        if ($scale < 0) {
            throw new \InvalidArgumentException('BcMath scale value must be a positive integer');
        }

        $this->scale = $scale;
        return $this;
    }

    /**
     * @param int $retain
     * @return $this
     */
    public function trim(int $retain = 0): self
    {
        if ($this->isInteger()) {
            return $this;
        }

        $trimmed = rtrim(rtrim($this->value, "0"), ".");
        if ($retain) {
            $trimmed = explode(".", $trimmed);
            $decimals = $trimmed[1] ?? "";
            $required = $retain - strlen($decimals);
            if ($required > 0) {
                $trimmed = $trimmed[0] . "." . $decimals . str_repeat("0", $required);
            }
        }

        $this->value = $trimmed;
        return $this;
    }

    /**
     * Gets value as string
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function int(): int
    {
        if (!$this->isInteger()) {
            throw new \DomainException('Stored BcNumber value is not integer');
        }

        if (bccomp(strval(PHP_INT_MAX), $this->value, 0) === 1) {
            throw new \DomainException('Stored BcNumber cannot be converted to signed PHP integer, exceeds PHP_INT_MAX');
        }

        return intval($this->value);
    }

    /**
     * Checks if value is integral (does not have decimals)
     * @return bool
     */
    public function isInteger(): bool
    {
        return preg_match('/^-?(0|[1-9]+[0-9]*)$/', $this->value);
    }

    /**
     * Checks if value is zero
     * @return bool
     */
    public function isZero(): bool
    {
        return bccomp($this->value, "0", $this->useScale()) === 0;
    }

    /**
     * Checks if value is greater than zero
     * @return bool
     */
    public function isPositive(): bool
    {
        return bccomp($this->value, "0", $this->useScale()) === 1;
    }

    /**
     * Checks if value is less than zero
     * @return bool
     */
    public function isNegative(): bool
    {
        return bccomp($this->value, "0", $this->useScale()) === -1;
    }

    /**
     * Compares value with a number to check if both are equal
     * @param string|float|int|BigInteger|BigNumber $comp
     * @param int|null $scale
     * @return bool
     */
    public function equals(string|float|int|BigInteger|BigNumber $comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->useScale($scale)) === 0;
    }

    /**
     * Compare number with another
     * @param string|float|int|BigInteger|BigNumber $comp
     * @param int|null $scale
     * @return int
     */
    public function cmp(string|float|int|BigInteger|BigNumber $comp, ?int $scale = null): int
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->useScale($scale));
    }

    /**
     * Compares value with a number to check if value is greater than argument
     * @param string|float|int|BigInteger|BigNumber $comp
     * @param int|null $scale
     * @return bool
     */
    public function greaterThan(string|float|int|BigInteger|BigNumber $comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->useScale($scale)) === 1;
    }

    /**
     * Compares value with a number to check if value is greater than or equals argument
     * @param string|float|int|BigInteger|BigNumber $comp
     * @param int|null $scale
     * @return bool
     */
    public function greaterThanOrEquals(string|float|int|BigInteger|BigNumber $comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->useScale($scale)) >= 0;
    }

    /**
     * Compares value with a number to check if value is less than argument
     * @param string|float|int|BigInteger|BigNumber $comp
     * @param int|null $scale
     * @return bool
     */
    public function lessThan(string|float|int|BigInteger|BigNumber $comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->useScale($scale)) === -1;
    }

    /**
     * Compares value with a number to check if value is less than or equals argument
     * @param string|float|int|BigInteger|BigNumber $comp
     * @param int|null $scale
     * @return bool
     */
    public function lessThanOrEquals(string|float|int|BigInteger|BigNumber $comp, ?int $scale = null): bool
    {
        $comp = $this->checkValidNum($comp);
        return bccomp($this->value, $comp, $this->useScale($scale)) !== 1;
    }

    /**
     * Checks if value is within (or equals) given min and max arguments
     * @param string|float|int|BigInteger|BigNumber $min
     * @param string|float|int|BigInteger|BigNumber $max
     * @param int|null $scale
     * @return bool
     */
    public function inRange(string|float|int|BigInteger|BigNumber $min, string|float|int|BigInteger|BigNumber $max, ?int $scale = null): bool
    {
        $min = $this->checkValidNum($min);
        $max = $this->checkValidNum($max);
        $scale = $this->useScale($scale);

        if (bccomp($this->value, $min, $scale) !== -1) {
            if (bccomp($this->value, $max, $scale) !== 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string|float|int|BigInteger|BigNumber $num
     * @param int|null $scale
     * @return $this
     */
    public function add(string|float|int|BigInteger|BigNumber $num, ?int $scale = null): static
    {
        $num = $this->checkValidNum($num);
        $scale = $this->useScale($scale);
        return new self(bcadd($this->value, $num, $scale), $scale);
    }

    /**
     * @param string|float|int|BigInteger|BigNumber $num
     * @param int|null $scale
     * @return $this
     */
    public function sub(string|float|int|BigInteger|BigNumber $num, ?int $scale = null): static
    {
        $num = $this->checkValidNum($num);
        $scale = $this->useScale($scale);
        return new self(bcsub($this->value, $num, $scale), $scale);
    }

    /**
     * @param string|float|int|BigInteger|BigNumber $num
     * @param int|null $scale
     * @return $this
     */
    public function mul(string|float|int|BigInteger|BigNumber $num, ?int $scale = null): static
    {
        $num = $this->checkValidNum($num);
        $scale = $this->useScale($scale);
        return new self(bcmul($this->value, $num, $scale), $scale);
    }

    /**
     * @param int $base
     * @param int $exponent
     * @param int|null $scale
     * @return $this
     */
    public function multiplyByPow(int $base, int $exponent, ?int $scale = null): static
    {
        $scale = $this->useScale($scale);
        if ($base < 1) {
            throw new \InvalidArgumentException('Value for param "base" must be a positive integer');
        } elseif ($exponent < 1) {
            throw new \InvalidArgumentException('Value for param "exponent" must be a positive integer');
        }

        return new self(bcmul(
            $this->value,
            bcpow(strval($base), strval($exponent), 0),
            $scale
        ), $scale);
    }

    /**
     * @param string|float|int|BigInteger|BigNumber $num
     * @param int|null $scale
     * @return $this
     */
    public function div(string|float|int|BigInteger|BigNumber $num, ?int $scale = null): static
    {
        $num = $this->checkValidNum($num);
        $scale = $this->useScale($scale);
        return new self(bcdiv($this->value, $num, $scale), $scale);
    }

    /**
     * @param string|float|int|BigInteger|BigNumber $num
     * @param int|null $scale
     * @return $this
     */
    public function pow(string|float|int|BigInteger|BigNumber $num, ?int $scale = null): static
    {
        $num = $this->checkValidNum($num);
        $scale = $this->useScale($scale);
        return new self(bcpow($this->value, $num, $scale), $scale);
    }

    /**
     * @param string|float|int|BigInteger|BigNumber $divisor
     * @param int|null $scale
     * @return $this
     */
    public function mod(string|float|int|BigInteger|BigNumber $divisor, ?int $scale = null): static
    {
        $num = $this->checkValidNum($divisor);
        $scale = $this->useScale($scale);
        return new self(bcmod($this->value, $num, $scale), $scale);
    }

    /**
     * @param $divisor
     * @param int|null $scale
     * @return $this
     */
    public function remainder($divisor, ?int $scale = null): static
    {
        return $this->mod($divisor, $scale);
    }

    /**
     * @return $this
     */
    public function copy(): static
    {
        return new static($this->value, $this->scale);
    }

    /**
     * @param int|null $scale
     * @return int
     */
    private function useScale(?int $scale = null): int
    {
        return is_int($scale) && $scale > 0 ? $scale : $this->scale;
    }

    /**
     * Checks and accepts Integers, Double/Float values or numeric Strings for BcMath operations
     * @param string|float|int|BigInteger|BigNumber $value
     * @return string
     */
    private function checkValidNum(string|float|int|BigInteger|BigNumber $value): string
    {
        $value = DataTypes::BigNumberValue($value);
        if (is_string($value)) {
            return $value;
        }

        throw new \InvalidArgumentException('Invalid argument is not a valid number');
    }
}
