<?php

declare(strict_types=1);

namespace venndev\plugin\utils;

trait StatsItem
{

    public const HEAD_TAG = "v_item";
    public const DAMAGE = "damage";
    public const DEFENSE = "defense";
    public const TRUE_DEFENSE = "true_defense";
    public const HEALTH = "health";
    public const SPEED = "speed";
    public const CRITICAL_CHANCE = "critical_chance";
    public const CRITICAL_DAMAGE = "critical_damage";
    public const STRENGTH = "strength";
    public const INTELLIGENCE = "intelligence";
    public const FISHING_SPEED = "fishing_speed";
    public const FISHING_LUCK = "fishing_luck";
    public const SEA_CREATURE_CHANCE = "sea_creature_chance";
    public const MAGIC_FIND = "magic_find";
    public const PET_LUCK = "pet_luck";
    public const BONUS_ATTACK_SPEED = "bonus_attack_speed";
    public const MINING_SPEED = "mining_speed";
    public const MINING_FORTUNE = "mining_fortune";
    public const FARMING_FORTUNE = "farming_fortune";
    public const FORAGING_FORTUNE = "foraging_fortune";
    public const ABILITY_DAMAGE = "ability_damage";
    public const ARROW_DAMAGE = "arrow_damage";
    public const ARROW_PIERCING = "arrow_piercing";

    // Maybe this is for physical damage?
    public const FEROCITY = "ferocity";

    public function removeHeadTagFromString(string $tag) : string
    {
        return str_replace(self::HEAD_TAG, "", $tag);
    }

}
