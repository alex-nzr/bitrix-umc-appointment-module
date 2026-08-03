<?php

namespace ANZ\Appointment\Integration\UmcSdk\Validator;

use ANZ\Appointment\Core\Exception\ValidatorException;
use ANZ\Appointment\Core\Trait\Validator;
use ANZ\Appointment\Dto\AppointmentDto;
use DateTime;

class RequestValidator
{
    use Validator;

    /**
     * @throws \ANZ\Appointment\Core\Exception\ValidatorException
     */
    public function validateAppointment(AppointmentDto $dto): bool
    {
        $requiredFields = [
            'uid' => 'string',
            'clinicUid' => 'string',
            'employeeUid' => 'string',
            'dateTimeBegin' => 'date',
            'phone' => 'phone',
        ];

        foreach ($requiredFields as $fieldName => $fieldType)
        {
            switch ($fieldType)
            {
                case 'date':
                    if (!($dto->{$fieldName} instanceof DateTime))
                    {
                        throw new ValidatorException("$fieldName is not a valid date");
                    }
                    break;
                case 'phone':
                    $dto->{$fieldName} = $this->validateAndFormatPhone($dto->{$fieldName});
                    break;
                case 'string':
                    if (strlen($dto->{$fieldName}) <= 0)
                    {
                        throw new ValidatorException("$fieldName can not be empty");
                    }
                    break;
                default:
                    if (empty($dto->{$fieldName}))
                    {
                        throw new ValidatorException("$fieldName can not be empty");
                    }
            }
        }

        if (is_string($dto->email) && strlen($dto->email) > 0)
        {
            $this->validateEmail($dto->email, true);
        }

        return true;
    }
}
