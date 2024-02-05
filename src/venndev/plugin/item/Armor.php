<?php

declare(strict_types=1);

namespace venndev\plugin\item;

use Override;

class Armor extends BaseItem
{

    #[Override] public function getName(): string
    {
        return "Armor";
    }

    #[Override] public function getRecipeName(): string
    {
        return "Armor";
    }

    #[Override] public function getNameVanilla(): string
    {
        return "Armor";
    }

    #[Override] public function getLore(): array
    {
        return [];
    }

}