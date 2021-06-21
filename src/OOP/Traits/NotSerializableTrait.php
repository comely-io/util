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
 * Trait NotSerializableTrait
 * @package Comely\Utils\OOP\Traits
 */
trait NotSerializableTrait
{
    final public function __serialize(): array
    {
        throw new \BadMethodCallException(sprintf('Instance of class "%s" cannot be serialized', get_called_class()));
    }

    final public function __unserialize(array $data): void
    {
        throw new \BadMethodCallException(sprintf('Instance of class "%s" cannot be un-serialized', get_called_class()));
    }

    final public function __sleep(): array
    {
        throw new \BadMethodCallException(sprintf('Instance of class "%s" cannot be serialized', get_called_class()));
    }

    final public function __wakeup(): void
    {
        throw new \BadMethodCallException(sprintf('Instance of class "%s" cannot be un-serialized', get_called_class()));
    }

    final public function serialize(): ?string
    {
        throw new \BadMethodCallException(sprintf('Instance of class "%s" cannot be serialized', get_called_class()));
    }

    /** @noinspection PhpUnusedParameterInspection */
    final public function unserialize(string $data): void
    {
        throw new \BadMethodCallException(sprintf('Instance of class "%s" cannot be serialized', get_called_class()));
    }
}
