<?php

declare(strict_types=1);

namespace App\Api\DataTransformer\ModList;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Api\Output\ModList\ModListOutput;
use App\Entity\ModList\ModList;

class ModListOutputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = []): ModListOutput
    {
        /** @var ModList $object */
        $output = new ModListOutput();

        $output->setId($object->getId()->toString());
        $output->setName($object->getName());
        $output->setActive($object->isActive());
        $output->setApproved($object->isApproved());
        $output->setCreatedAt($object->getCreatedAt());
        $output->setLastUpdatedAt($object->getLastUpdatedAt());

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ModListOutput::class === $to && $data instanceof ModList;
    }
}
