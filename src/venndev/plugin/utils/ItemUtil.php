<?php

declare(strict_types=1);

namespace venndev\plugin\utils;

use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\CompoundTag;

class ItemUtil
{

    public static function getItem(string $name, int $count = 1) : null|Item
    {
        try {
            $item = StringToItemParser::getInstance()->parse($name) ?? LegacyStringToItemParser::getInstance()->parse($name);
        } catch(LegacyStringToItemParserException $e) {
            return null;
        }

        return $item;
    }

}