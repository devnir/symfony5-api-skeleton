<?php

namespace Api\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationException extends BadRequestHttpException
{
    private const VALIDATION_ERROR = 'Invalid request parameters';

    /** @var array */
    private $errors;

    public function __construct(array $errors, string $message = self::VALIDATION_ERROR)
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
