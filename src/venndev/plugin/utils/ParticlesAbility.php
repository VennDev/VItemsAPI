<?php

declare(strict_types=1);

namespace venndev\plugin\utils;

use pocketmine\color\Color;
use pocketmine\world\particle\Particle;
use pocketmine\world\particle\DustParticle;

trait ParticlesAbility
{

    public static function getParticleFerocity() : Particle
    {
        return new DustParticle(new Color(255, 0, 0)); // This is color red
    }

}