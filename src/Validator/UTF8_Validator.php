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

use Comely\Utils\UTF8;
use Comely\Utils\UTF8\UTF8Charset;
use Comely\Utils\Validator\Exception\ValidatorException;

/**
 * Class UTF8_Validator
 * @package Comely\Utils\Validator
 */
class UTF8_Validator extends StringValidator
{
    /** @var array */
    private array $charsets = [];
    /** @var bool */
    private bool $allowASCII = true;
    /** @var bool */
    private bool $allowSpaces = true;
    /** @var bool */
    private bool $filterOutIllegals = false;

    /**
     * @param bool|null $allowASCII
     * @param bool|null $allowSpaces
     * @param bool|null $filterOutIllegals
     * @return $this
     */
    public function utf8Options(?bool $allowASCII = null, ?bool $allowSpaces = null, ?bool $filterOutIllegals = null): static
    {
        if (is_bool($allowASCII)) {
            $this->allowASCII = $allowASCII;
        }

        if (is_bool($allowSpaces)) {
            $this->allowSpaces = $allowSpaces;
        }

        if (is_bool($filterOutIllegals)) {
            $this->filterOutIllegals = $filterOutIllegals;
        }

        return $this;
    }

    /**
     * @param UTF8Charset $charset
     * @return $this
     */
    public function addCharset(UTF8Charset $charset): static
    {
        $this->charsets[] = $charset;
        return $this;
    }

    /**
     * @param string $value
     * @return string
     * @throws ValidatorException
     */
    protected function typeValidation(string $value): string
    {
        if ($this->filterOutIllegals) {
            $value = UTF8::Filter($value, $this->allowASCII, ...$this->charsets);
        }

        if (!UTF8::Check($value, $this->allowSpaces, $this->allowASCII, ...$this->charsets)) {
            throw new ValidatorException(code: Validator::UTF8_CHARSET_ERROR);
        }

        return $value;
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

        // Trim values?
        if ($this->trim) {
            $value = match ($this->trim) {
                self::TRIM_RIGHT => rtrim($value, $this->trimChars),
                self::TRIM_LEFT => ltrim($value, $this->trimChars),
                default => trim($value, $this->trimChars)
            };
        }

        // Clean multi-spaces and tabs with a single space
        if ($this->cleanSpaces) {
            $value = preg_replace('/(\s+|\t)/', " ", $value);
        }

        // Change Case
        if ($this->changeCase) {
            $value = match ($this->changeCase) {
                self::CHANGE_CASE_UC => mb_strtoupper($value, "UTF-8"),
                default => mb_strtolower($value, "UTF-8")
            };
        }

        // Sub-type validations
        $value = $this->typeValidation($value);

        // Check length
        $strLen = mb_strlen($value, "UTF-8");
        if ($this->exactLen) {
            if ($strLen !== $this->exactLen) {
                throw new ValidatorException(code: Validator::LENGTH_ERROR);
            }
        } elseif ($this->minLen || $this->maxLen) {
            if ($this->minLen && $strLen < $this->minLen) {
                throw new ValidatorException(code: Validator::LENGTH_UNDERFLOW_ERROR);
            }

            if ($this->maxLen && $strLen > $this->maxLen) {
                throw new ValidatorException(code: Validator::LENGTH_OVERFLOW_ERROR);
            }
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
