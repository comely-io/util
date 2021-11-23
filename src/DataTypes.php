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

namespace Comely\Utils;

use Comely\Utils\Math\BigInteger;
use Comely\Utils\Math\BigNumber;
use Comely\Utils\Validator\Exception\ValidatorException;
use Comely\Utils\Validator\Validator;

/**
 * Class DataTypes
 * @package Comely\Utils
 */
class DataTypes
{
    /**
     * Checks is input string is ASCII only
     * @param $val
     * @param bool $printableOnly
     * @return bool
     */
    public static function isASCII($val, bool $printableOnly = true): bool
    {
        if (!is_string($val)) {
            return false;
        }

        return $printableOnly ? ASCII::isPrintableOnly($val) : ASCII::Charset($val);
    }

    /**
     * Checks if string may have UTF8 characters
     * @param $val
     * @return bool
     */
    public static function hasUtf8Chars($val): bool
    {
        return is_string($val) && !ASCII::Charset($val);
    }

    /**
     * @param $val
     * @param false $allowPrefix
     * @return bool
     */
    public static function isBase16($val, bool $allowPrefix = false): bool
    {
        $exp = $allowPrefix ? '/^(0x)?[a-f0-9]+$/i' : '/^[a-f0-9]+$/i';
        return is_string($val) && preg_match($exp, $val);
    }

    /**
     * @param $int
     * @return int|null
     */
    public static function IntegerValue($int): ?int
    {
        try {
            return Validator::Integer()->getValidated($int);
        } catch (ValidatorException) {
            return null;
        }
    }

    /**
     * @param $value
     * @return string|null
     */
    public static function BigIntegerValue($value): ?string
    {
        if ($value instanceof BigInteger) {
            return $value->value();
        }

        if (is_int($value)) {
            return strval($value);
        }

        if (is_string($value) && preg_match('/^(0|-?[1-9][0-9]*)$/', $value)) {
            return $value;
        }

        return null;
    }

    /**
     * @param $value
     * @return string|null
     */
    public static function BigNumberValue($value): ?string
    {
        if ($value instanceof BigInteger) {
            return $value->value();
        }

        if ($value instanceof BigNumber) {
            return $value->value();
        }

        // Integers are obviously valid numbers
        if (is_int($value)) {
            return strval($value);
        }

        // Floats are valid numbers too but must be checked for scientific E-notations
        if (is_float($value)) {
            $floatAsString = strval($value);
            // Look if scientific E-notation
            if (preg_match('/e-/i', $floatAsString)) {
                // Auto-detect decimals
                $decimals = preg_split('/e-/i', $floatAsString);
                $decimals = strlen($decimals[0]) + intval($decimals[1]);
                return number_format($value, $decimals, ".", "");
            } elseif (preg_match('/e\+/i', $floatAsString)) {
                return number_format($value, 0, "", "");
            }

            return $floatAsString;
        }

        // Check with in String
        if (is_string($value)) {
            if (preg_match('/^-?(0|[1-9]+[0-9]*)(\.[0-9]+)?$/', $value)) {
                return $value;
            }
        }

        return null;
    }
}
