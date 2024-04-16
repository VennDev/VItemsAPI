<?php

declare(strict_types=1);

namespace venndev\plugin\item;

use Override;
use pocketmine\block\BlockToolType;
use pocketmine\block\Block;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Entity;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\math\Facing;
use pocketmine\player\Player;
use venndev\plugin\manager\UtilManager;
use venndev\plugin\player\VPlayer;
use venndev\plugin\utils\ClickMode;

class Tool extends BaseItem
{

    public const MAX_REACH_DISTANCE_SURVIVAL = 7;
    public const MAX_REACH_DISTANCE_CREATIVE = 7;

    public function getType() : int
    {
        return BlockToolType::NONE;
    }

    /**
     * @return int
     *
     * >= 1 : Wooden
     * >= 5 : Stone
     * >= 7 : Gold
     * >= 9 : Iron
     * >= 10 : Diamond
     *
     * This is the harvest level of the tool.
     */
    public function getPower() : int
    {
        return 1;
    }

    public function getListBlocksCanNotBreak() : array
    {
        return [];
    }

    #[Override] public function getName() : string
    {
        return "Tool";
    }

    #[Override] public function getRecipeName() : string
    {
        return "Tool";
    }

    #[Override] public function getNameVanilla() : string
    {
        return "Tool";
    }

    #[Override] public function getLore() : array
    {
        return [];
    }

    #[Override] public function onBreakBlock(Block $block, Entity $owner) : void
    {
        if (!$owner instanceof Player) return;

        $owner->removeCurrentWindow();

        if ($owner->canInteract(($pos = $block->getPosition())->add(0.5, 0.5, 0.5), $owner->isCreative() ? self::MAX_REACH_DISTANCE_CREATIVE : self::MAX_REACH_DISTANCE_SURVIVAL)) {
            $owner->broadcastAnimation(new ArmSwingAnimation($owner), $owner->getViewers());
            $owner->stopBreakBlock($pos);

            $item = $owner->getInventory()->getItemInHand();
            $oldItem = clone $item;
            $returnedItems = [];

            if ((new VPlayer($owner))->doBreakVBlock($block, true)) $owner->getHungerManager()->exhaust(0.005, PlayerExhaustEvent::CAUSE_MINING);
        }
    }

    #[Override] public function onClickBlock(Entity $owner, Block $block, ClickMode $mode) : void
    {
        if (!$owner instanceof Player) return;

        (new VPlayer($owner))->attackVBlock($block->getPosition());
    }

    public function getHarvestLevel() : int
    {
        return 0;
    }

    public function getTierTool() : string
    {
        if ($this->getPower() >= 10) return "diamond";
        if ($this->getPower() >= 9) return "iron";
        if ($this->getPower() >= 7) return "gold";
        if ($this->getPower() >= 5) return "stone";

        return "wooden";
    }

}
