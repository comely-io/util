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
 * Class ASCII_Validator
 * @package Comely\Utils\Validator
 */
class ASCII_Validator extends StringValidator
{
    /** @var bool */
    private bool $printableOnly = true;

    /**
     * @param bool|null $printableOnly
     * @return $this
     */
    public function opts(?bool $printableOnly = null): static
    {
        if (is_bool($printableOnly)) {
            $this->printableOnly = $printableOnly;
        }

        return $this;
    }

    /**
     * @param mixed $value
     * @param bool $emptyStrIsNull
     * @return string|null
     * @throws Exception\ValidatorException
     */
    public function getNullable(mixed $value, bool $emptyStrIsNull = false): ?string
    {
        return parent::getNullable($value, $emptyStrIsNull);
    }

    /**
     * @param string $value
     * @return string
     * @throws ValidatorException
     */
    protected function typeValidation(string $value): string
    {
        if ($this->printableOnly) {
            if (!preg_match('/^[\x20-\x7E]*$/', $value)) {
                throw new ValidatorException(code: Validator::ASCII_PRINTABLE_ERROR);
            }
        } else {
            // Because after dec(127), PHP behaves differently and required utf8_encode/decode methods
            if (!preg_match('/^[\x00-\x7F]*$/', $value)) {
                throw new ValidatorException(code: Validator::ASCII_CHARSET_ERROR);
            }
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return string
     * @throws Exception\ValidatorException
     */
    public function getValidated(mixed $value): string
    {
        return parent::getValidated($value);
    }
}
