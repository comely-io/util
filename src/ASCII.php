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

/**
 * Class ASCII
 * @package Comely\Utils
 */
class ASCII
{
    /**
     * @param string $in
     * @return bool
     */
    public static function isPrintableOnly(string $in): bool
    {
        return (bool)preg_match('/^[\x20-\x7E]*$/', $in);
    }

    /**
     * @param string $in
     * @return bool
     */
    public static function Charset(string $in): bool
    {
        return (bool)preg_match('/^[\x00-\x7F]*$/', $in);
    }

    /**
     * @param string $value
     * @param string|null $allowLowChars
     * @param string|null $stripChars
     * @return string
     */
    public static function Filter(string $value, ?string $allowLowChars = null, ?string $stripChars = null): string
    {
        $clean = filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH); // Remove all chars > 127
        $allowed = [];
        if ($allowLowChars) {
            $allowLen = strlen($allowLowChars);
            for ($i = 0; $i < $allowLen; $i++) {
                $allowed[] = ord($allowLowChars[$i]);
            }
        }

        $stripped = [];
        if ($stripChars) {
            $stripLen = strlen($stripChars);
            for ($i = 0; $i < $stripLen; $i++) {
                $stripped[] = ord($stripChars[$i]);
            }
        }

        $len = strlen($clean);
        if (!$len) {
            return "";
        }

        $filtered = "";
        for ($i = 0; $i < $len; $i++) {
            $ord = ord($clean[$i]);
            if ($ord < 32) {
                if (!in_array($ord, $allowed)) {
                    continue;
                }
            }

            if (in_array($ord, $stripped)) {
                continue;
            }

            $filtered .= chr($ord);
        }

        return $filtered;
    }

    /**
     * @param string $str
     * @return string
     */
    public static function toHex(string $str): string
    {
        if (!self::Charset($str)) {
            throw new \InvalidArgumentException('Cannot encode UTF-8 string into hexadecimals');
        }

        $hex = "";
        for ($i = 0; $i < strlen($str); $i++) {
            $hex .= str_pad(dechex(ord($str[$i])), 2, "0", STR_PAD_LEFT);
        }

        return $hex;
    }

    /**
     * @param string $hex
     * @return string
     */
    public static function fromHex(string $hex): string
    {
        if (!preg_match('/^[a-f0-9]+$/i', $hex)) {
            throw new \InvalidArgumentException('Cannot decoded non-hexadecimal value to ASCII');
        }

        if (strlen($hex) % 2 !== 0) {
            $hex = "0" . $hex;
        }

        $str = "";
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }

        return $str;
    }
}

