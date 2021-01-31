<?php

declare(strict_types=1);

namespace App\Form\UserGroup\Dto;

use App\Entity\Permissions\UserGroupPermissions;
use App\Entity\User\UserInterface;
use App\Form\AbstractFormDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueUserGroupName(errorPath="name")
 */
class UserGroupFormDto extends AbstractFormDto
{
    /** @var null|UuidInterface */
    protected $id;

    /**
     * @var null|string
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    protected $name;

    /**
     * @var null|string
     *
     * @Assert\Length(min=1, max=255)
     */
    protected $description;

    /**
     * @var null|UserGroupPermissions
     */
    protected $permissions;

    /**
     * @var Collection|UserInterface[]
     */
    protected $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function setId(?UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPermissions(): ?UserGroupPermissions
    {
        return $this->permissions;
    }

    public function setPermissions(?UserGroupPermissions $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function addUser(UserInterface $user): void
    {
        if ($this->users->contains($user)) {
            return;
        }

        $this->users->add($user);
    }

    public function removeUser(UserInterface $user): void
    {
        if (!$this->users->contains($user)) {
            return;
        }

        $this->users->removeElement($user);
    }

    /**
     * @return UserInterface[]
     */
    public function getUsers(): array
    {
        return $this->users->toArray();
    }

    /**
     * @param UserInterface[] $users
     */
    public function setUsers(array $users): void
    {
        $this->users->clear();
        foreach ($users as $user) {
            $this->addUser($user);
        }
    }
}
