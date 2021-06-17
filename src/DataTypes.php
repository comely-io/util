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
    public static function getIntegerValue($int): ?int
    {
        try {
            return Validator::Integer()->getValidated($int);
        } catch (ValidatorException) {
            return null;
        }
    }
}
