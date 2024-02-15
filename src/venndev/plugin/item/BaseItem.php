<?php

declare(strict_types=1);

namespace venndev\plugin\item;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\utils\TextFormat;
use venndev\plugin\utils\ClickMode;
use venndev\plugin\utils\StatsItem;

abstract class BaseItem
{
    use StatsItem;

    abstract public function getName() : string;

    abstract public function getRecipeName() : string;

    abstract public function getNameVanilla() : string;

    abstract public function getLore() : array;

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

    public function getDamage() : float
    {
        return 0;
    }

    public function unbreakable() : bool
    {
        return false;
    }

    /**
     * This is method is called when the item is used
     */
    public function onUse(Entity $owner) : void
    {
        //TODO: Implement
    }

    /**
     * This is method is called when the item is used on another entity
     */
    public function onHit(Entity $owner) : void
    {
        //TODO: Implement
    }

    /**
     * This is method is called when owner is hit by another entity
     */
    public function onHitBy(Entity $owner, Entity $entity) : void
    {
        //TODO: Implement
    }

    /**
     * This is method is called when the item is used on a block
     */
    public function onClickBlock(Entity $owner, Block $block, ClickMode $mode) : void
    {
        //TODO: Implement
    }

    /**
     * This is method is called when the item is used to break a block
     */
    public function onBreakBlock(Block $block, Entity $owner) : void
    {
        //TODO: Implement
    }

    /**
     * This is method is called when the item is held
     */
    public function onHeld(Entity $owner) : void
    {
        //TODO: Implement
    }

    /**
     * This is method is called when the item is equipped in inventory anywhere (armor, hot bar, etc.)
     */
    public function onTick(Entity $owner) : void
    {
        //TODO: Implement
    }

    /**
     * This is method is called when the item is equipped in armor slot or hot bar
     */
    public function onConsume(Entity $owner) : void
    {
        //TODO: Implement
    }

    public function canAttack() : bool
    {
        return false;
    }

    public function getLoreStats() : array
    {
        $loreStats = [];

        if ($this->getDamage() != 0) $loreStats[] = TextFormat::GRAY . "Damage: " . TextFormat::RED . $this->getDamage();
        if ($this->getStrength() != 0) $loreStats[] = TextFormat::GRAY . "Strength: " . TextFormat::RED . $this->getStrength();
        if ($this->getCritDamage() != 0) $loreStats[] = TextFormat::GRAY . "Crit Damage: " . TextFormat::BLUE . $this->getCritDamage();
        if ($this->getCritChance() != 0) $loreStats[] = TextFormat::GRAY . "Crit Chance: " . TextFormat::BLUE . $this->getCritChance();
        if ($this->getHealth() != 0) $loreStats[] = TextFormat::GRAY . "Health: " . TextFormat::RED . $this->getHealth();
        if ($this->getDefense() != 0) $loreStats[] = TextFormat::GRAY . "Defense: " . TextFormat::GREEN . $this->getDefense();
        if ($this->getTrueDefense() != 0) $loreStats[] = TextFormat::GRAY . "True Defense: " . TextFormat::WHITE . $this->getTrueDefense();
        if ($this->getSpeed() != 0) $loreStats[] = TextFormat::GRAY . "Speed: " . TextFormat::WHITE . $this->getSpeed();
        if ($this->getAbilityDamage() != 0) $loreStats[] = TextFormat::GRAY . "Ability Damage: " . TextFormat::RED . $this->getAbilityDamage() . "%";
        if ($this->getArrowDamage() != 0) $loreStats[] = TextFormat::GRAY . "Arrow Damage: " . TextFormat::RED . $this->getArrowDamage();
        if ($this->getArrowPiercing() != 0) $loreStats[] = TextFormat::GRAY . "Arrow Piercing: " . TextFormat::RED . $this->getArrowPiercing();
        if ($this->getFerocity() != 0) $loreStats[] = TextFormat::GRAY . "Ferocity: " . TextFormat::RED . $this->getFerocity();
        if ($this->getBonusAttackSpeed() != 0) $loreStats[] = TextFormat::GRAY . "Bonus Attack Speed: " . TextFormat::YELLOW . $this->getBonusAttackSpeed() . "%";
        if ($this->getMiningSpeed() != 0) $loreStats[] = TextFormat::GRAY . "Mining Speed: " . TextFormat::YELLOW . $this->getMiningSpeed();
        if ($this->getMiningFortune() != 0) $loreStats[] = TextFormat::GRAY . "Mining Fortune: " . TextFormat::GOLD . $this->getMiningFortune();
        if ($this->getFarmingFortune() != 0) $loreStats[] = TextFormat::GRAY . "Farming Fortune: " . TextFormat::GOLD . $this->getFarmingFortune();
        if ($this->getForagingFortune() != 0) $loreStats[] = TextFormat::GRAY . "Foraging Fortune: " . TextFormat::GOLD . $this->getForagingFortune();
        if ($this->getFishingLuck() != 0) $loreStats[] = TextFormat::GRAY . "Fishing Luck: " . TextFormat::AQUA . $this->getFishingLuck();
        if ($this->getFishingSpeed() != 0) $loreStats[] = TextFormat::GRAY . "Fishing Speed: " . TextFormat::YELLOW . $this->getFishingSpeed() . "%";
        if ($this->getSeaCreatureChance() != 0) $loreStats[] = TextFormat::GRAY . "Sea Creature Chance: " . TextFormat::AQUA . $this->getSeaCreatureChance() . "%";
        if ($this->getMagicFind() != 0) $loreStats[] = TextFormat::GRAY . "Magic Find: " . TextFormat::LIGHT_PURPLE . $this->getMagicFind();
        if ($this->getPetLuck() != 0) $loreStats[] = TextFormat::GRAY . "Pet Luck: " . TextFormat::LIGHT_PURPLE . $this->getPetLuck();

        return $loreStats;
    }

    public function getTagList() : array
    {
        return [
            self::DAMAGE => $this->getDamage(),
            self::STRENGTH => $this->getStrength(),
            self::DEFENSE => $this->getDefense(),
            self::HEALTH => $this->getHealth(),
            self::SPEED => $this->getSpeed(),
            self::CRITICAL_CHANCE => $this->getCritChance(),
            self::CRITICAL_DAMAGE => $this->getCritDamage(),
            self::INTELLIGENCE => $this->getIntelligence(),
            self::SEA_CREATURE_CHANCE => $this->getSeaCreatureChance(),
            self::MAGIC_FIND => $this->getMagicFind(),
            self::PET_LUCK => $this->getPetLuck(),
            self::ABILITY_DAMAGE => $this->getAbilityDamage(),
            self::MINING_FORTUNE => $this->getMiningFortune(),
            self::MINING_SPEED => $this->getMiningSpeed(),
            self::FISHING_SPEED => $this->getFishingSpeed(),
            self::FISHING_LUCK => $this->getFishingLuck(),
            self::FORAGING_FORTUNE => $this->getForagingFortune(),
            self::FARMING_FORTUNE => $this->getFarmingFortune(),
            self::ARROW_DAMAGE => $this->getArrowDamage(),
            self::ARROW_PIERCING => $this->getArrowPiercing(),
            self::FEROCITY => $this->getFerocity(),
            self::TRUE_DEFENSE => $this->getTrueDefense(),
            self::BONUS_ATTACK_SPEED => $this->getBonusAttackSpeed()
        ];
    }

}