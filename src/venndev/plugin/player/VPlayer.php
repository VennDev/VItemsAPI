<?php

declare(strict_types=1);

namespace venndev\plugin\player;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\sound\BlockBreakSound;
use venndev\plugin\item\Tool;
use venndev\plugin\manager\ItemManager;
use venndev\plugin\manager\UtilManager;
use venndev\plugin\utils\BlockBreakHandler;
use venndev\plugin\utils\ItemUtil;
use vennv\vapm\System;

class VPlayer
{

    private static array $blockBreakHandlers = [];

    public function __construct(private readonly Player $player)
    {}

    public function getPlayer() : Player
    {
        return $this->player;
    }

    public function getBlockBreakHandler() : BlockBreakHandler|null
    {
        return self::$blockBreakHandlers[$this->player->getName()] ?? null;
    }

    public function setBlockBreakHandler(BlockBreakHandler $handler) : void
    {
        self::$blockBreakHandlers[$this->player->getName()] = $handler;
    }

    public function unsetBlockBreakHandler() : void
    {
        unset(self::$blockBreakHandlers[$this->player->getName()]);
    }

    public function doBreakVBlock(Block $block, bool $createParticles) : bool
    {
        $item = $this->player->getInventory()->getItemInHand();
        $vitem = ItemManager::getItemIsRegistered($item);

        if (!$vitem instanceof Tool) return false;

        $tool = UtilManager::getItemToolType($vitem);

        $position = $block->getPosition();
        $vector = $position->asVector3()->floor();
        $world = $this->player->getWorld();
        $tile = $world->getTile($vector);
        $target = $world->getBlock($vector);
        $affectedBlocks = $target->getAffectedBlocks();

        $drops = [];
        if ($this->player->hasFiniteResources()) $drops = array_merge(...array_map(fn(Block $block) => $block->getDrops($tool), $affectedBlocks));

        $xpDrop = 0;
        if ($this->player->hasFiniteResources()) $xpDrop = array_sum(array_map(fn(Block $block) => $block->getXpDropForTool($tool), $affectedBlocks));

        $ev = new BlockBreakEvent($this->player, $target, $item, $this->player->isCreative(), $drops, $xpDrop);
        if ($target instanceof Air || ($this->player->isSurvival() && !$target->getBreakInfo()->isBreakable()) || $this->player->isSpectator()) $ev->cancel();

        if ($this->player->isAdventure(true) && !$ev->isCancelled()) {

            $canBreak = true;
            $itemParser = LegacyStringToItemParser::getInstance();

            foreach ($vitem->getListBlocksCanNotBreak() as $v) {
                $entry = $itemParser->parse($v);

                if ($entry->getBlock()->hasSameTypeId($target)) {
                    $canBreak = false;
                    break;
                }
            }

            if (!$canBreak) $ev->cancel();

            $ev->call();

            if ($ev->isCancelled()) return false;
        }

        $drops = $ev->getDrops();
        $xpDrop = $ev->getXpDropAmount();

        $returnedItems = [];

        foreach ($affectedBlocks as $t) {
            if ($createParticles) $world->addParticle($target->getPosition()->add(0.5, 0.5, 0.5), new BlockBreakParticle($target));

            $target->onBreak($item, $this->player, $returnedItems); // Maybe can change in the future

            $tile = $world->getTile($target->getPosition());
            $tile?->onBlockDestroyed();

            $chunkX = $vector->getFloorX() >> Chunk::COORD_BIT_SIZE;
            $chunkZ = $vector->getFloorZ() >> Chunk::COORD_BIT_SIZE;

            if (!$world->isChunkLoaded($chunkX, $chunkZ) || $world->isChunkLocked($chunkX, $chunkZ)) return false;
        }

        $item->onDestroyBlock($target, $returnedItems); // Maybe can change in the future

        if (count($drops) > 0) {
            $dropPos = $vector->add(0.5, 0.5, 0.5);

            foreach ($drops as $drop) {
                if (!$drop->isNull()) $world->dropItem($dropPos, $drop);
            }
        }

        if ($xpDrop > 0) $world->dropExperience($vector->add(0.5, 0.5, 0.5), $xpDrop);

        return true;
    }

    /**
     * @throws \Throwable
     */
    public function attackVBlock(Vector3 $pos) : bool
    {
        $block = $this->player->getWorld()->getBlock($pos);
        $world = $this->player->getWorld();

        $item = $this->player->getInventory()->getItemInHand();
        $vitem = ItemManager::getItemIsRegistered($item);

        if (!$vitem instanceof Tool) return false;

        if ($this->getBlockBreakHandler() === null) $this->setBlockBreakHandler(new BlockBreakHandler($this->player, $block));

        $handler = $this->getBlockBreakHandler();
        $handlerVct = $handler->getBlock()->getPosition()->floor();
        $blockVct = $pos->floor();

        if ($handlerVct->x !== $blockVct->x || $handlerVct->y !== $blockVct->y || $handlerVct->z !== $blockVct->z) {
            $this->setBlockBreakHandler(new BlockBreakHandler($this->player, $block));
            return false;
        } else {
            if ($handler->tick()) {
                if ($this->doBreakVBlock($block, true)) {
                    System::setTimeOut(function () use ($world, $pos, $block) {
                        $world->addSound($pos, new BlockBreakSound($block));
                        $world->addParticle($pos->add(0.5, 0.5, 0.5), new BlockBreakParticle($block));
                    }, 150);
                }
            }
        }

        return true;
    }

}