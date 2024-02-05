<?php

namespace venndev\plugin\manager;

use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use venndev\plugin\utils\StatsItem;
use venndev\plugin\VItemsAPI;
use vennv\vapm\Promise;

final class StatsManager
{
    use StatsItem;

    public const int BASIC_HEALTH = 20;
    public const float BASIC_SPEED = 0.13;
    public const float BASIC_MANA_REGEN = 0.5;
    public const float MAX_FORTUNE = 600;
    public const float MAX_BONUS_ATTACK_SPEED = 100;

    private static array $baseStats = [
        self::DAMAGE => 0,
        self::DEFENSE => 0,
        self::HEALTH => 0,
        self::SPEED => 0,
        self::CRITICAL_CHANCE => 0,
        self::CRITICAL_DAMAGE => 0,
        self::STRENGTH => 0,
        self::INTELLIGENCE => 0,
        self::FISHING_SPEED => 0,
        self::FISHING_LUCK => 0,
        self::SEA_CREATURE_CHANCE => 0,
        self::MAGIC_FIND => 0,
        self::PET_LUCK => 0,
        self::BONUS_ATTACK_SPEED => 0,
        self::MINING_SPEED => 0,
        self::MINING_FORTUNE => 0,
        self::FARMING_FORTUNE => 0,
        self::FORAGING_FORTUNE => 0,
        self::ABILITY_DAMAGE => 0,
        self::ARROW_DAMAGE => 0,
        self::ARROW_PIERCING => 0,
        self::FEROCITY => 0,
        self::CROP_FORTUNE => 0
    ];

    private static array $statsHandler = [];

    // This is where we will store the stats handler for each player. Use for reference.
    private static array $itemStatsHandler = [];

    private static array $manaHandler = [];

    private static array $hasJoined = [];

    // This is where we will store the queue for ferocity attacks.
    // This is a queue because we want to make sure that the attacks are in order.
    private static array $queueFerocity = [];

    public function __construct(public Player $player)
    {
        // TODO: :D
    }

    public function hasJoined() : bool
    {
        return self::$hasJoined[$this->player->getName()] ?? false;
    }

    public function setHasJoined(bool $hasJoined) : void
    {
        if ($hasJoined) self::$hasJoined[$this->player->getName()] = true;
        else unset(self::$hasJoined[$this->player->getName()]);
    }

    public function inQueueFerocity() : bool
    {
        return isset(self::$queueFerocity[$this->player->getName()]);
    }

    public function setQueueFerocity(Promise $queueFerocity) : void
    {
        self::$queueFerocity[$this->player->getName()] = $queueFerocity;
    }

    public function removeQueueFerocity() : void
    {
        unset(self::$queueFerocity[$this->player->getName()]);
    }

    public function getMana() : float
    {
        return self::$manaHandler[$this->player->getName()];
    }

    public function setMana(float $amount) : void
    {
        self::$manaHandler[$this->player->getName()] = $amount;
    }

    public function getTotalStat(string $stat) : float
    {
        return self::$statsHandler[$this->player->getName()][$stat] + self::$itemStatsHandler[$this->player->getName()][$stat];
    }

    public function getDamageDealt() : float
    {
        $baseDamage = $this->getTotalStat(self::DAMAGE);
        $criticalChance = $this->getTotalStat(self::CRITICAL_CHANCE);
        $criticalDamage = $this->getTotalStat(self::CRITICAL_DAMAGE);
        $strength = $this->getTotalStat(self::STRENGTH);

        $damageDealt = (5 + $baseDamage) * (1 + ($strength / 100));

        if (mt_rand(0, 100) <= $criticalChance) $damageDealt *= (1 + ($criticalDamage / 100));
        return $damageDealt;
    }

    public function getAbilityDamage(float $abilityScaling) : float
    {
        return $this->getTotalStat(self::ABILITY_DAMAGE) * (1 + ($abilityScaling / 100) * $abilityScaling);
    }

    public function checkStatsHandler() : void
    {
        if (!isset(self::$statsHandler[$this->player->getName()])) self::$statsHandler[$this->player->getName()] = self::$baseStats;
        if (!isset(self::$itemStatsHandler[$this->player->getName()])) self::$itemStatsHandler[$this->player->getName()] = self::$baseStats;
        if (!isset(self::$manaHandler[$this->player->getName()])) self::$manaHandler[$this->player->getName()] = $this->getTotalStat(self::INTELLIGENCE);
    }

    public function addItemStatsHandler(string $stat, float $amount) : void
    {
        self::$itemStatsHandler[$this->player->getName()][$stat] += $amount;
    }

    public function clearItemStatsHandler() : void
    {
        self::$itemStatsHandler[$this->player->getName()] = self::$baseStats;
    }

    public function clearStatsHandler() : void
    {
        unset(self::$statsHandler[$this->player->getName()]);
    }

    public function updateItemStats(Item $item) : void
    {
        $namedTag = $item->getNamedTag();

        // This is a function that will check if the tag exists, and if it does, it will add it to the stats' handler.
        $checkFunction = function (CompoundTag $namedTag, string $tag) : void {
            if ($namedTag->getTag($tag) !== null) $this->addItemStatsHandler($this->removeHeadTagFromString($tag), $namedTag->getTag($tag)->getValue());
        };

        $allTags = [
            self::DAMAGE,
            self::DEFENSE,
            self::HEALTH,
            self::SPEED,
            self::CRITICAL_CHANCE,
            self::CRITICAL_DAMAGE,
            self::STRENGTH,
            self::INTELLIGENCE,
            self::FISHING_SPEED,
            self::FISHING_LUCK,
            self::SEA_CREATURE_CHANCE,
            self::MAGIC_FIND,
            self::PET_LUCK,
            self::BONUS_ATTACK_SPEED,
            self::MINING_SPEED,
            self::MINING_FORTUNE,
            self::FARMING_FORTUNE,
            self::FORAGING_FORTUNE,
            self::ABILITY_DAMAGE,
            self::ARROW_DAMAGE,
            self::ARROW_PIERCING,
            self::FEROCITY,
            self::CROP_FORTUNE
        ];

        foreach ($allTags as $tag) $checkFunction($namedTag, self::HEAD_TAG . $tag);
    }

    public function updateInventoryStats() : void
    {
        $this->checkStatsHandler(); // Check in to make sure the player has a stats handler
        $this->clearItemStatsHandler(); // Clear to prevent duplicate stats handlers

        $armors = $this->player->getArmorInventory()->getContents();
        $itemHand = clone $this->player->getInventory()->getItemInHand();

        foreach ($armors as $armor) $this->updateItemStats($armor);

        $this->updateItemStats($itemHand);
    }

    public function updateStats() : void
    {
        $speed = $this->getTotalStat(self::SPEED);
        $health = $this->getTotalStat(self::HEALTH);
        $intelligence = $this->getTotalStat(self::INTELLIGENCE);

        $this->player->setMaxHealth(self::BASIC_HEALTH + $health / 5);
        $this->player->setMovementSpeed(self::BASIC_SPEED + $speed / 200);

        if ($this->getMana() < $intelligence) $this->setMana(min($intelligence, $this->getMana() + self::BASIC_MANA_REGEN));
    }

    /**
     * Sorry im fan of Hypixel Skyblock :D
     *
     * Each point of ⫽ Ferocity grants a +1% chance for an attack to
     * strike an extra time. For example, with 50⫽ Ferocity it grants
     * the player a 50% chance for their attack to hit twice. ⫽ Ferocity
     * above multiples of 100 also allow for extra triggers, meaning ⫽ Ferocity
     * above 100 allows your attack to strike three times, and so on.
     * For example, 250⫽ Ferocity would grant a 100% chance to strike three times,
     * and a 50% chance to strike a fourth time.
     *
     * @see https://hypixel-skyblock.fandom.com/wiki/Ferocity
     */
    public function calculateFerocityAttacks(int|float $ferocity = 0) : float|int
    {
        if ($ferocity === 0) $ferocity = $this->getTotalStat(self::FEROCITY);

        $attacks = 0; // Default number of attacks
        if ($ferocity > 0) {
            // Calculate the number of additional attacks based on Ferocity
            $bonusAttacks = floor($ferocity / 100);

            // Default number of attacks + number of additional attacks
            $attacks += $bonusAttacks;

            // Calculate the remainder to determine the chance of attacking again
            $remainder = $ferocity % 100;

            // Determine the chance of attacking again
            $chance = $remainder / 100;

            if (mt_rand() / mt_getrandmax() < $chance) $attacks++;
        }

        return $attacks;
    }

    /**
     * This function will determine if the player is lucky or not :)
     * Based on the base chance, magic find and pet luck and base on chance %.
     * if you have base chance of 10% and magic find of 160 and pet luck of 180
     * the chance will be 10% * (1 + (160 / 100) + (180 / 100)) = 10% * (1 + 1.6 + 1.8) = 10% * 4.4 = 44%
     */
    public function luckyOrNot(int|float $baseChance, bool $isPet = false) : bool
    {
        $magicFind = $this->getTotalStat(self::MAGIC_FIND);
        $petLuck = $isPet ? $this->getTotalStat(self::PET_LUCK) : 0;
        $chance = mt_rand(0, 100) <= $baseChance * (1 + ($magicFind / 100) + ($petLuck / 100));

        return mt_rand(0, 100) * 10 <= $chance * 10;
    }

    /**
     * This function will calculate the fortune for the player.
     * The fortune is based on the drop chance and the fortune stat.
     * The fortune stat is based on the fortune name, the drop chance and if the player has a pet.
     * Example:
     * - If the player has a farming fortune of 100 and the drop chance is 0.5,
     *      the fortune will be 0.5 * (1 + (100 / 600)) = 0.5 * (1 + 0.166) = 0.5 * 1.166 = 0.583
     * - If the player has a pet, the fortune will be 0.583 + (pet luck / 100)
     * - If the player has a crop fortune, the fortune will be 0.583 + (crop fortune / 600)
     * - If the player has a foraging fortune, the fortune will be 0.583 + (foraging fortune / 600)
     * - If the player has a mining fortune, the fortune will be 0.583 + (mining fortune / 600)
     *
     * And so on...
     */
    public function calculateFortune(string $fortuneName, int|float $dropChance, bool $isPet = false) : int|float
    {
        try {
            $fortune = $this->getTotalStat($fortuneName);
        } catch (\Exception) {
            $fortune = 0;
            VItemsAPI::getInstance()->getLogger()->error("Error while calculating fortune for " . $fortuneName);
        }

        if ($fortuneName === self::FARMING_FORTUNE) $fortune += $this->getTotalStat(self::CROP_FORTUNE);
        if ($fortuneName === self::FORAGING_FORTUNE || $fortuneName === self::MINING_FORTUNE) $fortune *= 1 / 100;
        if ($isPet) $fortune += $this->getTotalStat(self::PET_LUCK);

        return $dropChance * (1 + ($fortune / self::MAX_FORTUNE));
    }

    /**
     * This function will calculate the EHP for the player.
     * The EHP is based on the defense and health stats.
     * The EHP is calculated by the formula:
     *      EHP = Health * ((Defense + 100) / 100)
     */
    public function calculateEHP() : float
    {
        $defense = $this->getTotalStat(self::DEFENSE);
        $health = $this->getTotalStat(self::HEALTH);
        return $health * (($defense + 100) / 100);
    }

    public function calculateAttackSpeed(int|float $baseSpeed) : float
    {
        return $baseSpeed - ($baseSpeed /  (1 + min(self::MAX_BONUS_ATTACK_SPEED, $this->getTotalStat(self::BONUS_ATTACK_SPEED))));
    }

}