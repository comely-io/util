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

namespace Comely\Utils\Time\TimeUnits;

/**
 * Class UnitsLabels
 * @package Comely\Utils\Time\TimeUnits
 */
class UnitsLabels
{
    /** @var string */
    private string $second;
    /** @var string */
    private string $seconds;
    /** @var string */
    private string $minute;
    /** @var string */
    private string $minutes;
    /** @var string */
    private string $hour;
    /** @var string */
    private string $hours;
    /** @var string */
    private string $day;
    /** @var string */
    private string $days;

    /**
     * UnitsLabels constructor.
     */
    public function __construct()
    {
        $this->seconds("s");
        $this->minutes("m");
        $this->hours("h");
        $this->days("d");
    }

    /**
     * @param string $singular
     * @param string|null $plural
     * @return UnitsLabels
     */
    public function seconds(string $singular, ?string $plural = null): self
    {
        $this->second = $singular;
        $this->seconds = $plural ?? $singular;
        return $this;
    }

    /**
     * @param string $singular
     * @param string|null $plural
     * @return UnitsLabels
     */
    public function minutes(string $singular, ?string $plural = null): self
    {
        $this->minute = $singular;
        $this->minutes = $plural ?? $singular;
        return $this;
    }

    /**
     * @param string $singular
     * @param string|null $plural
     * @return UnitsLabels
     */
    public function hours(string $singular, ?string $plural = null): self
    {
        $this->hour = $singular;
        $this->hours = $plural ?? $singular;
        return $this;
    }

    /**
     * @param string $singular
     * @param string|null $plural
     * @return UnitsLabels
     */
    public function days(string $singular, ?string $plural = null): self
    {
        $this->day = $singular;
        $this->days = $plural ?? $singular;
        return $this;
    }

    /**
     * @param string $which
     * @param int $num
     * @return string
     */
    public function get(string $which, int $num): string
    {
        return match ($which) {
            "day", "days" => $num > 1 ? $this->days : $this->day,
            "hour", "hours" => $num > 1 ? $this->hours : $this->hour,
            "minute", "minutes" => $num > 1 ? $this->minutes : $this->minute,
            "second", "seconds" => $num > 1 ? $this->seconds : $this->second,
            default => throw new \OutOfBoundsException('Invalid label identifier'),
        };
    }

    /**
     * @param string $label
     * @return string|null
     */
    public function find(string $label): ?string
    {
        if ($this->matchLabel($label, "days") || $this->matchLabel($label, "day")) {
            return "days";
        }

        if ($this->matchLabel($label, "hours") || $this->matchLabel($label, "hour")) {
            return "hours";
        }

        if ($this->matchLabel($label, "minutes") || $this->matchLabel($label, "minute")) {
            return "minutes";
        }

        if ($this->matchLabel($label, "seconds") || $this->matchLabel($label, "second")) {
            return "seconds";
        }

        return null;
    }

    /**
     * @param string $label
     * @param string $prop
     * @return bool
     */
    private function matchLabel(string $label, string $prop): bool
    {
        if (mb_strtolower($label) == mb_strtolower($this->$prop)) {
            return true;
        }

        return false;
    }
}
