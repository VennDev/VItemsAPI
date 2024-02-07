<?php

declare(strict_types=1);

namespace venndev\plugin\world\particle;

use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use pocketmine\world\particle\FloatingTextParticle;

class DamageDisplay extends FloatingTextParticle
{

    public function __construct(int $damage, bool $hasCrit)
    {
        parent::__construct($hasCrit ? "§c" : "§e" . $damage . "❤");
    }

    public function remove(World $world) : void
    {
        $pk = new RemoveActorPacket();
        $pk->actorUniqueId = $this->entityId;

        foreach ($world->getPlayers() as $player) $player->getNetworkSession()->sendDataPacket($pk);
    }

}