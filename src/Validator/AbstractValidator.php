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
 * Class AbstractValidator
 * @package Comely\Utils\Validator
 */
abstract class AbstractValidator
{
    /** @var \Closure|null */
    protected ?\Closure $customValidator = null;

    /**
     * @param \Closure $customValidatorFn
     * @return $this
     */
    public function setCustomFn(\Closure $customValidatorFn): static
    {
        $this->customValidator = $customValidatorFn;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearCustomFn(): static
    {
        $this->customValidator = null;
        return $this;
    }
}
