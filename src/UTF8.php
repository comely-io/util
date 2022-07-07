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

use Comely\Utils\UTF8\UTF8Charset;

/**
 * Class UTF8
 * @package Comely\Utils
 */
class UTF8
{
    /**
     * @param string $input
     * @param bool $spaces
     * @param bool $ascii
     * @param UTF8Charset ...$charsets
     * @return bool
     */
    public static function Check(string $input, bool $spaces = true, bool $ascii = true, UTF8Charset...$charsets): bool
    {
        $ranges = "";
        foreach ($charsets as $charset) {
            $ranges .= $charset->unicodeRange();
        }

        $spaces = $spaces ? '\s' : '';
        $ascii = $ascii ? "\x20-\x7E" : "";
        $exp = '/^[' . $spaces . $ascii . $ranges . ']+$/u';
        return (bool)preg_match($exp, $input);
    }

    /**
     * @param string $input
     * @param bool $ascii
     * @param UTF8Charset ...$charsets
     * @return string
     */
    public static function Filter(string $input, bool $ascii = true, UTF8Charset...$charsets): string
    {
        $ranges = "";
        foreach ($charsets as $charset) {
            $ranges .= $charset->unicodeRange();
        }

        $ascii = $ascii ? "\x20-\x7E" : "";
        $exp = "/[^" . $ascii . $ranges . "]+/u";
        return trim(preg_replace('/(\s+|\t)/', ' ', preg_replace($exp, "", $input)));
    }
}
