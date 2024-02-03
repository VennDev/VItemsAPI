<?php

/**
 * All Rights Reserved
 *
 * Copyright (c) 2024 VennDev (https://github.com/VennDev)
 *
 * THE CONTENTS OF THIS PROJECT ARE PROPRIETARY AND CONFIDENTIAL.
 * UNAUTHORIZED COPYING, TRANSFERRING OR REPRODUCTION OF THE CONTENTS OF THIS PROJECT, VIA ANY MEDIUM IS STRICTLY PROHIBITED.
 *
 * The receipt or possession of the source code and/or any parts thereof does not convey or imply any right to use them
 * for any purpose other than the purpose for which they were provided to you.
 *
 * The software is provided "AS IS", without warranty of any kind, express or implied, including but not limited to
 * the warranties of merchantability, fitness for a particular purpose and non infringement.
 * In no event shall the authors or copyright holders be liable for any claim, damages or other liability,
 * whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software
 * or the use or other dealings in the software.
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 */

declare(strict_types=1);

namespace venndev\plugin\item;

use pocketmine\entity\Entity;
use pocketmine\utils\TextFormat;
use venndev\plugin\utils\ClickMode;

abstract class BaseItem
{

    abstract public function getName() : string;

    abstract public function getRecipeName() : string;

    abstract public function getNameVanilla() : string;

    abstract public function getLore() : array;

    public function getDamage() : float
    {
        return 0;
    }

    public function getDefense() : float
    {
        return 0;
    }

    public function getStrength() : float
    {
        return 0;
    }

    public function getIntelligence() : float
    {
        return 0;
    }

    public function getCritDamage() : float
    {
        return 0;
    }

    public function getCritChance() : float
    {
        return 0;
    }

    public function getHealth() : float
    {
        return 0;
    }

    public function getSpeed() : float
    {
        return 0;
    }

    public function getBonusAttackSpeed() : float
    {
        return 0;
    }

    public function getSeaCreatureChance() : float
    {
        return 0;
    }

    public function getMagicFind() : float
    {
        return 0;
    }

    public function getPetLuck() : float
    {
        return 0;
    }

    public function getFerocity() : float
    {
        return 0;
    }

    public function getAbilityDamage() : float
    {
        return 0;
    }

    public function getMiningSpeed() : float
    {
        return 0;
    }

    public function getMiningFortune() : float
    {
        return 0;
    }

    public function getFarmingFortune() : float
    {
        return 0;
    }

    public function getForagingFortune() : float
    {
        return 0;
    }

    public function getFishingLuck() : float
    {
        return 0;
    }

    public function getTrueDefense() : float
    {
        return 0;
    }

    public function getFishingSpeed() : float
    {
        return 0;
    }

    public function getArrowDamage() : float
    {
        return 0;
    }

    public function getArrowPiercing() : float
    {
        return 0;
    }

    public function unbreakable() : bool
    {
        return false;
    }

    public function onUse(Entity $owner) : void
    {
        //TODO: Implement
    }

    public function onHit(Entity $owner) : void
    {
        //TODO: Implement
    }

    public function onHitBy(Entity $owner, Entity $entity) : void
    {
        //TODO: Implement
    }

    public function onClickBlock(Entity $owner, ClickMode $mode) : void
    {
        //TODO: Implement
    }

    public function onBreakBlock(Entity $owner) : void
    {
        //TODO: Implement
    }

    public function onHeld(Entity $owner) : void
    {
        //TODO: Implement
    }

    public function onTick(Entity $owner) : void
    {
        //TODO: Implement
    }

    public function getLoreStats() : array
    {
        $loreStats = [];

        if ($this->getDamage() > 0) $loreStats[] = TextFormat::GRAY . "Damage: " . TextFormat::RED . $this->getDamage();
        if ($this->getStrength() > 0) $loreStats[] = TextFormat::GRAY . "Strength: " . TextFormat::RED . $this->getStrength();
        if ($this->getCritDamage() > 0) $loreStats[] = TextFormat::GRAY . "Crit Damage: " . TextFormat::BLUE . $this->getCritDamage();
        if ($this->getCritChance() > 0) $loreStats[] = TextFormat::GRAY . "Crit Chance: " . TextFormat::BLUE . $this->getCritChance();
        if ($this->getHealth() > 0) $loreStats[] = TextFormat::GRAY . "Health: " . TextFormat::RED . $this->getHealth();
        if ($this->getDefense() > 0) $loreStats[] = TextFormat::GRAY . "Defense: " . TextFormat::GREEN . $this->getDefense();
        if ($this->getTrueDefense() > 0) $loreStats[] = TextFormat::GRAY . "True Defense: " . TextFormat::WHITE . $this->getTrueDefense();
        if ($this->getSpeed() > 0) $loreStats[] = TextFormat::GRAY . "Speed: " . TextFormat::WHITE . $this->getSpeed();
        if ($this->getAbilityDamage() > 0) $loreStats[] = TextFormat::GRAY . "Ability Damage: " . TextFormat::RED . $this->getAbilityDamage() . "%";
        if ($this->getArrowDamage() > 0) $loreStats[] = TextFormat::GRAY . "Arrow Damage: " . TextFormat::RED . $this->getArrowDamage();
        if ($this->getArrowPiercing() > 0) $loreStats[] = TextFormat::GRAY . "Arrow Piercing: " . TextFormat::RED . $this->getArrowPiercing();
        if ($this->getFerocity() > 0) $loreStats[] = TextFormat::GRAY . "Ferocity: " . TextFormat::RED . $this->getFerocity();
        if ($this->getBonusAttackSpeed() > 0) $loreStats[] = TextFormat::GRAY . "Bonus Attack Speed: " . TextFormat::YELLOW . $this->getBonusAttackSpeed() . "%";
        if ($this->getMiningSpeed() > 0) $loreStats[] = TextFormat::GRAY . "Mining Speed: " . TextFormat::YELLOW . $this->getMiningSpeed();
        if ($this->getMiningFortune() > 0) $loreStats[] = TextFormat::GRAY . "Mining Fortune: " . TextFormat::GOLD . $this->getMiningFortune();
        if ($this->getFarmingFortune() > 0) $loreStats[] = TextFormat::GRAY . "Farming Fortune: " . TextFormat::GOLD . $this->getFarmingFortune();
        if ($this->getForagingFortune() > 0) $loreStats[] = TextFormat::GRAY . "Foraging Fortune: " . TextFormat::GOLD . $this->getForagingFortune();
        if ($this->getFishingLuck() > 0) $loreStats[] = TextFormat::GRAY . "Fishing Luck: " . TextFormat::AQUA . $this->getFishingLuck();
        if ($this->getFishingSpeed() > 0) $loreStats[] = TextFormat::GRAY . "Fishing Speed: " . TextFormat::YELLOW . $this->getFishingSpeed() . "%";
        if ($this->getSeaCreatureChance() > 0) $loreStats[] = TextFormat::GRAY . "Sea Creature Chance: " . TextFormat::AQUA . $this->getSeaCreatureChance() . "%";
        if ($this->getMagicFind() > 0) $loreStats[] = TextFormat::GRAY . "Magic Find: " . TextFormat::LIGHT_PURPLE . $this->getMagicFind();
        if ($this->getPetLuck() > 0) $loreStats[] = TextFormat::GRAY . "Pet Luck: " . TextFormat::LIGHT_PURPLE . $this->getPetLuck();

        return $loreStats;
    }

}