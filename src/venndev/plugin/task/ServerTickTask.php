<?php

declare(strict_types=1);

namespace venndev\plugin\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use venndev\plugin\manager\StatsManager;

final class ServerTickTask extends Task
{

    public function __construct(public Server $server)
    {
        //TODO: Add a check to make sure the server is running VItemsAPI
    }

    /**
     * @return void
     */
    public function onRun(): void
    {
        foreach ($this->server->getOnlinePlayers() as $player) {

            $statsManager = new StatsManager($player);
            if ($statsManager->hasJoined()) {
                $statsManager->updateInventoryStats();
                $statsManager->updateStats();
            }
        }
    }

}