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
 * Class BigInteger
 * @package Comely\Utils\Math
 */
class BigInteger
{
    /** @var \GMP */
    private \GMP $value;

    /**
     * @param string $value
     * @return BigInteger
     */
    public static function fromBase16(string $value): static
    {
        if (!DataTypes::isBase16($value, true)) {
            throw new \InvalidArgumentException('Argument is not a valid Hexadecimal string');
        }

        if (str_starts_with($value, "0x")) {
            $value = substr($value, 2);
        }

        return new self(gmp_init($value, 16));
    }

    /**
     * BigInteger constructor.
     * @param string|int|\GMP $value
     */
    public function __construct(string|int|\GMP $value)
    {
        $this->value = $this->createGMPValue($value);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            10 => gmp_strval($this->value),
            16 => gmp_strval($this->value, 16)
        ];
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return ["value" => gmp_strval($this->value, 16)];
    }

    /**
     * @param array $data
     */
    public function __unserialize(array $data): void
    {
        if (isset($data["value"])) {
            $this->value = gmp_init($data["value"], 16);
        }
    }

    /**
     * @return string
     */
    public function toBase16(): string
    {
        $hex = gmp_strval($this->value, 16);
        if (strlen($hex) % 2 !== 0) {
            $hex = "0" . $hex;
        }

        return $hex;
    }

    /**
     * @return BigNumber
     */
    public function toBigNumber(): BigNumber
    {
        return new BigNumber($this->value());
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return gmp_strval($this->value);
    }

    /**
     * @return \GMP
     */
    public function getGMP(): \GMP
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isSigned(): bool
    {
        return gmp_sign($this->value) === -1;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return gmp_sign($this->value) >= 0;
    }

    /**
     * @param string|int|\GMP|BigInteger $value
     * @return int
     */
    public function cmp(string|int|\GMP|BigInteger $value): int
    {
        $cmp = gmp_cmp($this->value, $value);
        if ($cmp === 0) {
            return 0;
        }

        return $cmp > 0 ? 1 : -1;
    }

    /**
     * @param string|int|\GMP|BigInteger $value
     * @return bool
     */
    public function equals(string|int|\GMP|BigInteger $value): bool
    {
        return $this->cmp($value) === 0;
    }

    /**
     * @param string|int|\GMP|BigInteger $value
     * @param bool $orEquals
     * @return bool
     */
    public function greaterThan(string|int|\GMP|BigInteger $value, bool $orEquals = false): bool
    {
        $exp = $orEquals ? 0 : 1;
        return $this->cmp($value) >= $exp;
    }

    /**
     * @param string|int|\GMP|BigInteger $value
     * @param bool $orEquals
     * @return bool
     */
    public function lessThan(string|int|\GMP|BigInteger $value, bool $orEquals = false): bool
    {
        $exp = $orEquals ? 0 : -1;
        return $this->cmp($value) <= $exp;
    }

    /**
     * @param string|int|\GMP|BigInteger $min
     * @param string|int|\GMP|BigInteger $max
     * @return bool
     */
    public function inRange(string|int|\GMP|BigInteger $min, string|int|\GMP|BigInteger $max): bool
    {
        return $this->cmp($min) >= 0 && $this->cmp($max) <= 0;
    }

    /**
     * @param string|int|\GMP|BigInteger $value
     * @return $this
     */
    public function add(string|int|\GMP|BigInteger $value): static
    {
        return new self(gmp_add($this->value, $value));
    }

    /**
     * @param string|int|\GMP|BigInteger $value
     * @return $this
     */
    public function sub(string|int|\GMP|BigInteger $value): static
    {
        return new self(gmp_sub($this->value, $value));
    }

    /**
     * @param string|int|\GMP|BigInteger $value
     * @return $this
     */
    public function mul(string|int|\GMP|BigInteger $value): static
    {
        return new self(gmp_mul($this->value, $value));
    }

    /**
     * @return int
     */
    public function sizeInBytes(): int
    {
        $bytes = strlen($this->toBase16()) / 2;

        $bracket = 1;
        while (true) {
            if ($bytes > $bracket) {
                $bracket *= 2;
                continue;
            }

            break;
        }

        return $bracket;
    }

    /**
     * @param string|int|\GMP|BigInteger $value
     * @return \GMP
     */
    private function createGMPValue(string|int|\GMP|BigInteger $value): \GMP
    {
        if ($value instanceof self) {
            return $value->getGMP();
        }

        if ($value instanceof \GMP) {
            return $value;
        }

        $value = DataTypes::BigIntegerValue($value);
        if (is_string($value)) {
            return gmp_init($value);
        }

        throw new \InvalidArgumentException('Given argument is not a valid big-integer value');
    }
}
