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