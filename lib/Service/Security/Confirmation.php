<?php
namespace ANZ\Appointment\Service\Security;

use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\ConfirmationType;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Application;
use Bitrix\Main\AccessDeniedException;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Exception;

class Confirmation
{
    private const SESSION_KEY = 'anz_appointment_confirm';
    private const CODE_TTL = 300;
    private const RESEND_TTL = 60;
    private const MAX_ATTEMPTS = 5;
    private const RATE_LIMIT_SEND_LIMIT = 5;
    private const RATE_LIMIT_SEND_TTL = 900;
    private const RATE_LIMIT_VERIFY_LIMIT = 10;
    private const RATE_LIMIT_VERIFY_TTL = 900;
    private const CODE_MIN = 1000;
    private const CODE_MAX = 9999;
    private const ERROR_CODE_NOT_EXPIRED = 425;
    private const ERROR_CODE_EXPIRED = 406;
    private const ERROR_CODE_INCORRECT = 406;
    private const ERROR_CODE_CONFIRM_TYPE = 400;

    public function sendConfirmCode(string $phone, string $email, string $purpose = 'appointment'): Result
    {
        try
        {
            $mailer = Container::getInstance()->getMailerService();
            $smsService = Container::getInstance()->getSmsService();

            $code = (string)random_int(self::CODE_MIN, self::CODE_MAX);
            $confirmWith = Configuration::getInstance()->getExchangeConfirmMode();
            $result = new Result();
            $target = $this->resolveTarget($confirmWith, $phone, $email);

            Container::getInstance()->getRateLimiter()->assertAllowed(
                'confirm_send',
                self::RATE_LIMIT_SEND_LIMIT,
                self::RATE_LIMIT_SEND_TTL,
                $purpose . '|' . $target
            );

            $confirm = $this->getConfirmData($purpose);
            if (!empty($confirm))
            {
                $timeExpires = (int)($confirm['resendAfter'] ?? 0);
                if ($timeExpires > time())
                {
                    $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_CODE_NOT_EXPIRED"), self::ERROR_CODE_NOT_EXPIRED));
                    return $result;
                }
            }

            switch ($confirmWith)
            {
                case ConfirmationType::PHONE->value:
                    $result = $smsService->sendConfirmCode($phone, $code);
                    break;
                case ConfirmationType::EMAIL->value:
                    $result = $mailer->sendConfirmCode($email, $code);
                    break;
                case ConfirmationType::NONE->value:
                    break;
                default:
                    $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_TYPE_ERROR"), self::ERROR_CODE_CONFIRM_TYPE));
                    break;
            }

            if ($result->isSuccess())
            {
                $timeExpires = time() + self::RESEND_TTL;
                $result->setData(['timeExpires' => $timeExpires]);
                $this->setConfirmData($purpose, [
                    'target' => $target,
                    'hash' => password_hash($code, PASSWORD_DEFAULT),
                    'expires' => time() + self::CODE_TTL,
                    'resendAfter' => $timeExpires,
                    'attempts' => 0,
                    'verified' => $confirmWith === ConfirmationType::NONE->value,
                ]);
            }

            return $result;
        }
        catch (Exception $e)
        {
            return (new Result)->addError(new Error($e->getMessage()));
        }
    }

    public function verifyConfirmCode(string $code, string $purpose = 'appointment'): Result
    {
        $result = new Result();
        try
        {
            Container::getInstance()->getRateLimiter()->assertAllowed(
                'confirm_verify',
                self::RATE_LIMIT_VERIFY_LIMIT,
                self::RATE_LIMIT_VERIFY_TTL,
                $purpose
            );
            $confirm = $this->getConfirmData($purpose);
            if (empty($confirm))
            {
                $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_CODE_EXPIRED"), self::ERROR_CODE_EXPIRED));
                return $result;
            }

            if ((int)($confirm['expires'] ?? 0) < time())
            {
                $this->clear($purpose);
                $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_CODE_EXPIRED"), self::ERROR_CODE_EXPIRED));
                return $result;
            }

            $confirm['attempts'] = (int)($confirm['attempts'] ?? 0) + 1;
            if ($confirm['attempts'] > self::MAX_ATTEMPTS)
            {
                $this->clear($purpose);
                $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_CODE_INCORRECT"), self::ERROR_CODE_INCORRECT));
                return $result;
            }

            if (password_verify($code, (string)($confirm['hash'] ?? '')))
            {
                $confirm['verified'] = true;
                $this->setConfirmData($purpose, $confirm);
                $result->setData(['success' => true]);
                return $result;
            }

            $this->setConfirmData($purpose, $confirm);
            $result->addError(new Error(Loc::getMessage("ANZ_APPOINTMENT_CONFIRM_CODE_INCORRECT"), self::ERROR_CODE_INCORRECT));
        }
        catch (Exception $e)
        {
            $result->addError(new Error($e->getMessage()));
        }
        return $result;
    }

    /**
     * @throws AccessDeniedException
     */
    public function assertVerified(string $phone, string $email, string $purpose = 'appointment'): void
    {
        $confirmWith = Configuration::getInstance()->getExchangeConfirmMode();
        if ($confirmWith === ConfirmationType::NONE->value)
        {
            return;
        }

        $target = $this->resolveTarget($confirmWith, $phone, $email);
        $confirm = $this->getConfirmData($purpose);
        if (
            empty($confirm)
            || empty($confirm['verified'])
            || (string)($confirm['target'] ?? '') !== $target
            || (int)($confirm['expires'] ?? 0) < time()
        )
        {
            throw new AccessDeniedException('Confirmation is required');
        }
    }

    public function clear(string $purpose = 'appointment'): void
    {
        $all = $this->getAllConfirmData();
        unset($all[$purpose]);
        Application::getInstance()->getSession()->set(self::SESSION_KEY, $all);
    }

    private function resolveTarget(string $confirmWith, string $phone, string $email): string
    {
        return match ($confirmWith) {
            ConfirmationType::PHONE->value => preg_replace('/\D+/', '', $phone) ?: '',
            ConfirmationType::EMAIL->value => mb_strtolower(trim($email)),
            default => '',
        };
    }

    private function getConfirmData(string $purpose): array
    {
        $all = $this->getAllConfirmData();
        return is_array($all[$purpose] ?? null) ? $all[$purpose] : [];
    }

    private function setConfirmData(string $purpose, array $data): void
    {
        $all = $this->getAllConfirmData();
        $all[$purpose] = $data;
        Application::getInstance()->getSession()->set(self::SESSION_KEY, $all);
    }

    private function getAllConfirmData(): array
    {
        $data = Application::getInstance()->getSession()->get(self::SESSION_KEY);
        return is_array($data) ? $data : [];
    }
}
