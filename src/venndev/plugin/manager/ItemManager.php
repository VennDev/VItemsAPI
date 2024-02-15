<?php

namespace venndev\plugin\manager;

use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use venndev\plugin\item\BaseItem;
use venndev\plugin\utils\ItemUtil;
use venndev\plugin\utils\StatsItem;
use venndev\plugin\utils\StatsPlayer;

final class ItemManager
{
    use StatsItem;
    use StatsPlayer;

    private static array $items = [];

    /** @var array<Item, int|float> */
    private static array $itemsDropChanceList = [];
    private static array $itemsMiningList = [];
    private static array $itemsFarmingList = [];

    public static function getVItem(string $name) : ?BaseItem
    {
        return self::$items[$name] ?? null;
    }

    public static function getNameOrigin(Item $item) : string
    {
        return ($tagName = $item->getNamedTag()->getTag(self::HEAD_TAG)) === null ? "" : (string) $tagName->getValue();
    }

    public static function getItemIsRegistered(Item $item) : ?BaseItem
    {
        return self::getVItem(self::getNameOrigin($item));
    }

    public static function registerVItem(BaseItem $item) : void
    {
        self::$items[$item->getRecipeName()] = $item;
    }

    public static function registerVItemDropChance(Item $item, float $chance) : void
    {
        self::$itemsDropChanceList[serialize($item)] = $chance;
    }

    public static function registerVItemMining(Item $item, float $chance) : void
    {
        self::$itemsMiningList[serialize($item)] = $chance;
    }

    public static function registerVItemFarming(Item $item, float $chance) : void
    {
        self::$itemsFarmingList[serialize($item)] = $chance;
    }

    public static function hasVItemMining(Item $item) : bool
    {
        return isset(self::$itemsMiningList[serialize($item)]);
    }

    public static function hasVItemFarming(Item $item) : bool
    {
        return isset(self::$itemsFarmingList[serialize($item)]);
    }

    public static function getVItemDropChance(Item $item) : float
    {
        return self::$itemsDropChanceList[serialize($item)] ?? 0;
    }

    public static function giveItem(Player $player, string $name, int $count = 1) : bool
    {
        $vitem = self::getVItem($name);

        if ($vitem === null) {
            $player->sendMessage(TextFormat::RED . "Item not found in VItemsAPI!");
            return false;
        }

        $item = ItemUtil::getItem($vitem->getNameVanilla(), $count);

        if ($item === null) {
            $player->sendMessage(TextFormat::RED . "Item not found in vanilla!");
            return false;
        }

        // I will update more in the future
        $item->setCustomName($vitem->getName());
        $item->setLore(array_merge($vitem->getLoreStats(), [TextFormat::GOLD], $vitem->getLore()));

        if ($item instanceof Durable) $item->setUnbreakable($vitem->unbreakable());

        $item->getNamedTag()->setString(self::HEAD_TAG, $vitem->getRecipeName());

        $tagList = $vitem->getTagList();

        // This is for all tags stats for the item
        foreach ($tagList as $tagName => $value) $item->getNamedTag()->setFloat(self::HEAD_TAG . $tagName, $value);

        $player->getInventory()->addItem($item);
        return true;
    }

}