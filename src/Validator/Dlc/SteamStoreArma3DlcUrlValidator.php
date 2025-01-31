<?php

declare(strict_types=1);

namespace App\Validator\Dlc;

use App\Form\Dlc\Dto\DlcFormDto;
use App\Service\Steam\Exception\AppNotFoundException;
use App\Service\Steam\Helper\Exception\InvalidAppUrlFormatException;
use App\Service\Steam\Helper\SteamHelper;
use App\Service\Steam\SteamApiClient;
use App\Validator\AbstractValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SteamStoreArma3DlcUrlValidator extends AbstractValidator
{
    protected const ARMA_3_GAME_ID = 107410;

    public function __construct(
        EntityManagerInterface $entityManager,
        private SteamApiClient $steamApiClient
    ) {
        parent::__construct($entityManager);
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof DlcFormDto) {
            throw new UnexpectedTypeException($constraint, DlcFormDto::class);
        }

        if (!$constraint instanceof SteamStoreArma3DlcUrl) {
            throw new UnexpectedTypeException($constraint, SteamStoreArma3DlcUrl::class);
        }

        $url = $value->getUrl();

        try {
            $appId = SteamHelper::appUrlToAppId($url);
            $appInfo = $this->steamApiClient->getAppInfo($appId);
        } catch (\Exception $ex) {
            $message = null;

            if ($ex instanceof InvalidAppUrlFormatException) {
                $message = $constraint->invalidDlcUrlMessage;
            } elseif ($ex instanceof AppNotFoundException) {
                $message = $constraint->dlcNotFoundMessage;
            }

            $this->addViolation($message, [], $constraint->errorPath);

            return;
        }

        if (self::ARMA_3_GAME_ID !== $appInfo->getGameId()) {
            $this->addViolation($constraint->notAnArma3DlcMessage, [], $constraint->errorPath);
        }
    }
}
