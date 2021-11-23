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
 * Prevent var_dump of implementing classes
 */
trait NoDumpTrait
{
    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [get_called_class()];
    }
}
