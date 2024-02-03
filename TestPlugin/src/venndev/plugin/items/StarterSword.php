<?php

declare(strict_types=1);

namespace venndev\plugin\items;

use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use venndev\plugin\item\Weapon;

class StarterSword extends Weapon
{
    private int $tick = 0;

    public function getName() : string
    {
        return TextFormat::GREEN . "Starter Sword";
    }

    public function getRecipeName() : string
    {
        return "starter_sword";
    }

    public function getNameVanilla() : string
    {
        return "wooden_sword";
    }

    public function getLoreStats(): array
    {
        return [
            TextFormat::GRAY . "Damage: " . TextFormat::WHITE . $this->getStrength(),
            TextFormat::GRAY . "Speed: " . TextFormat::WHITE . $this->getSpeed(),
            TextFormat::GRAY . "Ferocity: " . TextFormat::WHITE . $this->getFerocity()
        ];
    }

    public function getLore() : array
    {
        return [
            TextFormat::GRAY . "A sword for beginners."
        ];
    }

    public function getStrength() : float
    {
        return 5.0;
    }

    public function getSpeed() : float
    {
        return 50.0;
    }

    public function getFerocity(): float
    {
        return 150.0;
    }

    public function onUse(Entity $owner): void
    {
        if ($owner instanceof Player) $owner->sendMessage(TextFormat::GREEN . "You used the starter sword!");
    }

    public function onTick(Entity $owner): void
    {
        if ($owner instanceof Player && $this->tick > 100) {
            $owner->sendMessage(TextFormat::GREEN . "You ticked the starter sword!");
            $this->tick = 0;
        }

        $this->tick++;
    }

}