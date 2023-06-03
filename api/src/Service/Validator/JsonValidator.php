<?php

namespace App\Service\Validator;

use JsonSchema\Validator;
use Symfony\Component\HttpFoundation\RequestStack;

class JsonValidator
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function validateRequest(string $schemaName): array
    {
        $validator = new Validator();
        $errors = [];
        $request = $this->requestStack->getCurrentRequest();
        $requestData = json_decode($request->getContent());
        $schema = json_decode(file_get_contents(__DIR__ . '/' . $schemaName));

        $validator->validate($requestData, $schema);

        foreach ($validator->getErrors() as $error) {
            $errors[] = $error['message'];
        }

        return $errors;
    }
}