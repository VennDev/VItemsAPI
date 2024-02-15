<?php

declare(strict_types=1);

namespace venndev\plugin\item\tools;

use Override;
use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\entity\Entity;
use venndev\plugin\item\Tool;

class Shovel extends Tool
{

    public function getType(): int
    {
        return BlockToolType::SHOVEL;
    }

}