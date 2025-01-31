<?php

declare(strict_types=1);

namespace App\Validator\Dlc;

use App\Entity\Dlc\Dlc;
use App\Form\Dlc\Dto\DlcFormDto;
use App\Service\Steam\Helper\Exception\InvalidAppUrlFormatException;
use App\Service\Steam\Helper\SteamHelper;
use App\Validator\AbstractValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueSteamStoreDlcValidator extends AbstractValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof DlcFormDto) {
            throw new UnexpectedTypeException($constraint, DlcFormDto::class);
        }

        if (!$constraint instanceof UniqueSteamStoreDlc) {
            throw new UnexpectedTypeException($constraint, UniqueSteamStoreDlc::class);
        }

        $url = $value->getUrl();
        if (!$url) {
            return;
        }

        try {
            $appId = SteamHelper::appUrlToAppId($url);
        } catch (InvalidAppUrlFormatException $ex) {
            return;
        }

        $id = $value->getId();
        if ($this->isColumnValueUnique(Dlc::class, ['appId' => (string) $appId], $id)) {
            return;
        }

        $this->addViolation(
            $constraint->message,
            [
                '{{ dlcUrl }}' => $url,
            ],
            $constraint->errorPath
        );
    }
}
