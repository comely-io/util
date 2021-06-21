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

namespace Comely\Utils\OOP\Traits;

/**
 * Trait NotCloneableTrait
 * @package Comely\Utils\OOP\Traits
 */
trait NotCloneableTrait
{
    final public function __clone(): void
    {
        throw new \BadMethodCallException(sprintf('Instance of class "%s" cannot be clone', get_called_class()));
    }
}
