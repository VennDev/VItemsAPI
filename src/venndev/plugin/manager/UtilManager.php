<?php

declare(strict_types=1);

namespace venndev\plugin\manager;

use pocketmine\item\Item;
use Throwable;
use pocketmine\entity\Entity;
use venndev\plugin\item\BaseItem;
use venndev\plugin\item\Tool;
use venndev\plugin\item\tools\Axe;
use venndev\plugin\item\tools\Pickaxe;
use venndev\plugin\item\tools\Shovel;
use venndev\plugin\utils\ItemUtil;
use venndev\plugin\world\particle\DamageDisplay;
use vennv\vapm\System;

final class UtilManager
{

    public static function getItemToolType(BaseItem $vitem) : null|Item
    {
        $toolType = "none";

        if (!$vitem instanceof Tool) return null;

        if ($vitem instanceof Pickaxe) $toolType = "pickaxe";
        if ($vitem instanceof Axe) $toolType = "axe";
        if ($vitem instanceof Shovel) $toolType = "shovel";

        return ItemUtil::getItem($vitem->getTierTool() . "_" . $toolType);
    }

    /**
     * @throws Throwable
     */
    public static function spawnDamageDisplay(Entity $entity, int $damage, bool $hasCrit) : void
    {
        $angle = mt_rand(0, 360);
        $locEntity = $entity->getLocation();
        $worldEntity = $entity->getWorld();
        $vectorFloor = $locEntity->floor()->add(0.5 * cos(deg2rad($angle)) * 2, 1, 0.5 * sin(deg2rad($angle)) * 2);

        $particle = new DamageDisplay($damage, $hasCrit);
        $worldEntity->addParticle($vectorFloor, $particle);

        System::setTimeout(function() use ($worldEntity, $particle) {
            $particle->remove($worldEntity);
        }, (int) PluginSettings::TIME_TO_REMOVE_DAMAGE_DISPLAY);
    }

}