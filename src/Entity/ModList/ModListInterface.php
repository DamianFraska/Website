<?php

declare(strict_types=1);

namespace App\Entity\ModList;

use App\Entity\DescribedEntityInterface;
use App\Entity\Mod\ModInterface;
use App\Entity\User\UserInterface;

interface ModListInterface extends DescribedEntityInterface
{
    public function addMod(ModInterface $mod): void;

    public function removeMod(ModInterface $mod): void;

    /**
     * @return ModInterface[]
     */
    public function getMods(): array;

    /**
     * @param ModInterface[] $mods
     */
    public function setMods(array $mods): void;

    public function getOwner(): ?UserInterface;

    public function setOwner(?UserInterface $owner): void;

    public function isActive(): bool;

    public function setActive(bool $active): void;
}
