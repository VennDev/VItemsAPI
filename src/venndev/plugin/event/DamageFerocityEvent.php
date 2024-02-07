<?php

declare(strict_types=1);

namespace venndev\plugin\event;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class DamageFerocityEvent extends EntityDamageByEntityEvent
{

    public function __construct(Entity $damager, Entity $entity, int $cause, float $damage, array $modifiers = [])
    {
        parent::__construct($damager, $entity, $cause, $damage, $modifiers);
    }

}