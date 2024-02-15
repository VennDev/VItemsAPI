<?php

declare(strict_types=1);

namespace venndev\plugin\utils;

use pocketmine\block\Block;
use pocketmine\player\Player;
use venndev\plugin\item\Tool;
use venndev\plugin\item\tools\Pickaxe;
use venndev\plugin\manager\ItemManager;
use venndev\plugin\manager\StatsManager;

final class BlockBreakHandler
{

    private float $timeStart = 0.0;
    private float $timeGoal = 0.0;

    public function __construct(
        private readonly Player $player,
        private readonly Block  $block
    ) {
        $this->timeStart = microtime(true);
        $this->timeGoal = $block->getBreakInfo()->getHardness();
    }

    public function getPlayer() : Player
    {
        return $this->player;
    }

    public function getBlock() : Block
    {
        return $this->block;
    }

    public function getTimeGoal() : float
    {
        return $this->timeGoal;
    }

    public function tick() : bool
    {
        $playerStats = new StatsManager($this->player);
        $item = $this->player->getInventory()->getItemInHand();
        $vitem = ItemManager::getItemIsRegistered($item);

        if (!$vitem instanceof Tool) return false;

        $diff = (microtime(true) - $this->timeStart) + ($vitem instanceof Pickaxe ? $playerStats->calculateMiningSpeed() : 0.0);

        return $diff >= $this->timeGoal;
    }

}