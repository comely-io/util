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

/**
 * Class Validator
 * @package Comely\Utils\Validator
 */
class Validator
{
    /** @var int */
    public const INVALID_TYPE_ERROR = 0x64;
    /** @var int */
    public const ENUM_ERROR = 0x65;
    /** @var int Custom validation callback returned invalid type */
    public const CALLBACK_TYPE_ERROR = 0x66;
    /** @var int */
    public const RANGE_UNDERFLOW_ERROR = 0x67;
    /** @var int */
    public const RANGE_OVERFLOW_ERROR = 0x68;
    /** @var int PHP_INT_MIN exceeds integer value */
    public const INTEGER_UNDERFLOW_ERROR = 0x78;
    /** @var int Integer value exceeds PHP_INT_MAX */
    public const INTEGER_OVERFLOW_ERROR = 0x79;
    /** @var int */
    public const SIGNED_INTEGER_ERROR = 0x7a;
    /** @var int String length is not an exact match */
    public const LENGTH_ERROR = 0x82;
    /** @var int String length is smaller */
    public const LENGTH_UNDERFLOW_ERROR = 0x83;
    /** @var int String length exceeds */
    public const LENGTH_OVERFLOW_ERROR = 0x84;
    /** @var int String preg_match fail */
    public const REGEX_MATCH_ERROR = 0x85;
    /** @var int String contains chars outside of ASCII table */
    public const ASCII_CHARSET_ERROR = 0x8c;
    /** @var int String contains non-printable sequence from ASCII charset */
    public const ASCII_PRINTABLE_ERROR = 0x8d;
    /** @var int String contains chars outside allowed UTF8 charset */
    public const UTF8_CHARSET_ERROR = 0x8e;

    /** @var int Alias of ENUM_ERROR */
    public const NOT_IN_ARRAY_ERROR = self::ENUM_ERROR;

    /**
     * @return StringValidator
     */
    public static function String(): StringValidator
    {
        return new StringValidator();
    }

    /**
     * @return ASCII_Validator
     */
    public static function ASCII(): ASCII_Validator
    {
        return new ASCII_Validator();
    }

    /**
     * @return UTF8_Validator
     */
    public static function UTF8(): UTF8_Validator
    {
        return new UTF8_Validator();
    }

    /**
     * @return IntegerValidator
     */
    public static function Integer(): IntegerValidator
    {
        return new IntegerValidator();
    }
}
