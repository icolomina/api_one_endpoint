<?php

namespace Ict\ApiOneEndpoint\Exception;

use Symfony\Component\Validator\ConstraintViolationList;

class OperationValidationException extends \RuntimeException
{
    private array $errors = [];

    public function __construct(ConstraintViolationList $validationErrors, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = 'Validation input error';

        foreach ($validationErrors as $error){
            $this->errors[$error->getPropertyPath()] = $error->getMessage();
        }

        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

}
