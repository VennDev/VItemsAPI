<?php

declare(strict_types=1);

namespace venndev\plugin\manager;

final class ServerHandler
{

    private static bool $hasUpdatedItems = false;

    public static function hasUpdatedItems() : bool
    {
        return self::$hasUpdatedItems;
    }

    public static function setHasUpdatedItems(bool $hasUpdatedItems) : void
    {
        self::$hasUpdatedItems = $hasUpdatedItems;
    }

}