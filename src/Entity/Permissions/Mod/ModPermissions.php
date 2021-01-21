<?php

declare(strict_types=1);

namespace App\Entity\Permissions\Mod;

use App\Entity\Permissions\AbstractCrudPermissions;

class ModPermissions extends AbstractCrudPermissions
{
    /** @var bool */
    protected $changeStatus = false;

    public function canChangeStatus(): bool
    {
        return $this->changeStatus;
    }

    public function setChangeStatus(bool $changeStatus): void
    {
        $this->changeStatus = $changeStatus;
    }
}
