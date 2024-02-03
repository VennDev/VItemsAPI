<?php

declare(strict_types=1);

namespace venndev\plugin;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use venndev\plugin\manager\ItemManager;
use venndev\plugin\manager\PluginSettings;
use venndev\plugin\manager\ServerHandler;

class Loader extends PluginBase implements Listener
{
    use SingletonTrait;

    /**
     * @return void
     */
    protected function onLoad() : void
    {
        self::setInstance($this);
    }

    /**
     * @return void
     */
    protected function onEnable() : void
    {
        ItemManager::registerVItem(new items\StarterSword());
        //PluginSettings::setReduceLag(true);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event) : void
    {
        $player = $event->getPlayer();
        $player->getInventory()->clearAll();
        ItemManager::giveItem($player, "starter_sword");
    }

}