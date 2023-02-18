<?php

namespace Ict\ApiOneEndpoint\tests\Operation;

use Ict\ApiOneEndpoint\Exception\OperationValidationException;
use Ict\ApiOneEndpoint\Model\Api\ApiInput;
use Ict\ApiOneEndpoint\Operation\OperationInputValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Validator\Constraints\NotBlank;

class OperationInputValidatorTest extends TestCase
{
    public static OperationInputValidator $operationInputValidator;


    public static function setUpBeforeClass(): void
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $validator  = (new ValidatorBuilder())->enableAnnotationMapping()->getValidator();
        self::$operationInputValidator = new OperationInputValidator($validator, $serializer);
    }

    public function testValidInput()
    {
        $apiInput = new ApiInput();
        $apiInput->setOperation('Operation');
        $apiInput->setData(['name' => 'Jorge', 'age' => 23]);
        $inputData = self::$operationInputValidator->validateInput($apiInput, InputData::class);
        $this->assertInstanceOf(InputData::class, $inputData);
    }

    public function testInvalidInput()
    {
        $this->expectException(OperationValidationException::class);

        $apiInput = new ApiInput();
        $apiInput->setOperation('Operation');
        $apiInput->setData(['name' => 'Jorge', 'age' => 14]);
        self::$operationInputValidator->validateInput($apiInput, InputData::class);

    }
}

class InputData
{

    #[NotBlank(message: 'Name cannot be blank')]
    private string $name;

    #[GreaterThanOrEqual(18, message: 'User must be and adult')]
    private int $age;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

}

