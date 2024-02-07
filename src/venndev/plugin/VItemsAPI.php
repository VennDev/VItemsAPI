<?php

declare(strict_types=1);

namespace venndev\plugin;

use pocketmine\block\tile\TileFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use vennv\vapm\VapmPMMP;

class VItemsAPI extends PluginBase
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
        // Initialize the VAPM API
        VapmPMMP::init($this);

        // Register the event listener
        $this->getServer()->getPluginManager()->registerEvents(new \venndev\plugin\listener\EventListener(), $this);

        // Register the server tick task
        $this->getScheduler()->scheduleRepeatingTask(new \venndev\plugin\task\ServerTickTask($this->getServer()), 20);
    }

}