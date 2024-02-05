<?php

declare(strict_types=1);

namespace venndev\plugin\utils;

trait StatsItem
{

    public const string HEAD_TAG = "v_item";
    public const string DAMAGE = "damage";
    public const string DEFENSE = "defense";
    public const string TRUE_DEFENSE = "true_defense";
    public const string HEALTH = "health";
    public const string SPEED = "speed";
    public const string CRITICAL_CHANCE = "critical_chance";
    public const string CRITICAL_DAMAGE = "critical_damage";
    public const string STRENGTH = "strength";
    public const string INTELLIGENCE = "intelligence";
    public const string FISHING_SPEED = "fishing_speed";
    public const string FISHING_LUCK = "fishing_luck";
    public const string SEA_CREATURE_CHANCE = "sea_creature_chance";
    public const string MAGIC_FIND = "magic_find";
    public const string PET_LUCK = "pet_luck";
    public const string BONUS_ATTACK_SPEED = "bonus_attack_speed";
    public const string MINING_SPEED = "mining_speed";
    public const string MINING_FORTUNE = "mining_fortune";
    public const string FARMING_FORTUNE = "farming_fortune";
    public const string FORAGING_FORTUNE = "foraging_fortune";
    public const string ABILITY_DAMAGE = "ability_damage";
    public const string ARROW_DAMAGE = "arrow_damage";
    public const string ARROW_PIERCING = "arrow_piercing";
    public const string CROP_FORTUNE = "crop_fortune";

    // Maybe this is for physical damage?
    public const string FEROCITY = "ferocity";

    public function removeHeadTagFromString(string $tag) : string
    {
        return str_replace(self::HEAD_TAG, "", $tag);
    }

}