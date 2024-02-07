<?php

declare(strict_types=1);

namespace venndev\plugin\manager;

use Throwable;
use pocketmine\entity\Entity;
use venndev\plugin\world\particle\DamageDisplay;
use vennv\vapm\System;

final class UtilManager
{

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