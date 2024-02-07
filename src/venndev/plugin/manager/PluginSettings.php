<?php

declare(strict_types=1);

namespace venndev\plugin\manager;

final class PluginSettings
{

    public const float TIME_BETWEEN_FEROCITY_SPAWNS = 200.0;
    public const float TIME_TO_FEROCITY_RESET = 30.0;
    public const float TIME_TO_FEROCITY_RESET_IF_LAG = 50.0;
    public const float TIME_TO_REMOVE_DAMAGE_DISPLAY = 1000.0;

    /**
     * This it can be used to reduce lag by disabling some features.
     *
     * Details:
     * - The plugin will not send tick updates to the client every tick.
     * - The plugin will not send particle updates to the client every tick.
     * - The plugin will not send item updates to the client every tick.
     */
    private static bool $reduceLag = false;

    public static function isReduceLag() : bool
    {
        return self::$reduceLag;
    }

    public static function setReduceLag(bool $reduceLag) : void
    {
        self::$reduceLag = $reduceLag;
    }

}