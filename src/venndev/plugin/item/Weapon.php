<?php

declare(strict_types=1);

namespace venndev\plugin\item;

use Override;

class Weapon extends BaseItem
{

    #[Override] public function getName() : string
    {
        return "Weapon";
    }

    #[Override] public function getRecipeName() : string
    {
        return "Weapon";
    }

    #[Override] public function getNameVanilla() : string
    {
        return "Weapon";
    }

    #[Override] public function getLore() : array
    {
        return [];
    }

    #[Override] public function canAttack(): bool
    {
        return true;
    }

}