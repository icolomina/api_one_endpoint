# api_one_endpoint

Api one endpoint bundle allows you to create an api focusing on operations instead of resources. One endpoint is used as a resource and api looks into payload 
to extract what operation has to perform and what data needs to perform it.

### Installation

### Inputs and outputs

Inputs and outputs define operations I/O flow. Each operatios can require an input (if it needs data to perform it) and must define an output which will be returned 
back to the client. 
Input have to be sent as a POST Json request and must follow the next schema:

```json
   {
     "$schema": "http://json-schema.org/draft-04/schema#",
     "type": "object",
     "properties": {
       "operation": {
         "type": "string"
       },
       "data": {
         "type": "object"
       }
     },
     "required": [
       "operation",
       "data"
     ]  
   }
```
As an example, let's see how a SendPaymentOperation input could looks like:

```json
   {
      "operation" : "SendPaymentOperation",
      "data" : {
        "from" : "xxxx",
        "to" : "yyyy",
        "amount" : 6.35
      }
   }
```

As we can seein the above payload, we're sending an input which tells our api to perform _SendPaymentOperation_ and use as data the following keys:
- **from**: Who sends payment
- **To**: Who receives payment
- **Amount**: How much money _xxxx_ sends to _yyyy_

Outputs must be wrapped with an _Ict\ApiOneEndpoint\Model\Api\ApiOutput_ object. As a parameters, ApiOutput receives data which will be returned to client 
and http code which will be used in the response. Data can be an iterable (if we want to return a collection of data) or an object. This bundles relies 
on symfony serializer to send a json response to the client.

Let's see the following examples:

```php
   
   class Hero {
      public function __construct(
         public readonly string $name,
         public readonly string $hero
      ){ }
   }

   $data = [
      new Hero('Peter Parker', 'spiderman'),
      new Hero('Clark Kent', 'superman')
   ];
   
   return new ApiOutput($data, 200);
```

```php
   class PaymentDoneOutput {
       public function __construct(
          public readonly string paymentId,
          public readonly \DateTimeInmutable $date
       ){ }
   }
   
   $paymentOutput = new PaymentDoneOutput('899875557', new \DateTimeInmutable('2023-03-05 12:25');
   return new ApiOutput($paymentOutput, 202);
```

First example returns an array of Hero objects as an output and second example returns PaymentDoneOutput object.

### Defining input operations

Operation inputs must be defined creating simple objects with its getters and setters. You can use [symfony validation constraints](https://symfony.com/doc/current/reference/constraints.html) to define validation rules so your input must hold required and valid data. This bundle 
will validate input automatically and will throw an Ict\ApiOneEndpoint\Exception\OperationValidationException if validation fails.

As an example, let's see how a payment operation input would looks like:

```php
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SendPaymentOperationInput
{
    #[NotBlank(message: 'From cannot be empty')]
    private string $from;

    #[NotBlank(message: 'To cannot be empty')]
    private string $to;

    #[NotBlank(message: 'Amount cannot be empty')]
    #[GreaterThan(0, message: 'Amount must be greater then 0')]
    private string $amount;
    
    // getters & setters

}
```

### Defining operations

Operations must implement Ict\ApiOneEndpoint\Contract\Operation\OperationInterface

```php
use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Ict\ApiOneEndpoint\Model\Api\ApiOutput;

class SendPaymentOperation implements OperationInterface
{

    public function perform(mixed $operationData): ApiOutput
    {
        // Perform operation ....
        // Sending bizum, BTC ......
        return new ApiOutput([], 200);
    }

    public function getName(): string
    {
        return 'SendPayment';
    }

    public function getInput(): ?string
    {
        return SendPaymentOperationInput::class;
    }

    public function getGroup(): ?string
    {
        return null;
    }
}
```

Each operation must define the following methods (declared in OperationInterface):

- **perform**: Executes operation
- **getName**: Gets operation name
- **getInput**: Gets input class. When an operation is executed, payload data will be deserialized to the input class and also validated. 
- **getGroup**: Gets operation group. We will cover it when seeing operation authorization.

### Protecting operations

This bundle relies on [symfony voters](https://symfony.com/doc/current/security/voters.html) to protect operations. Take a look to the following voter:

```php
use Ict\ApiOneEndpoint\Model\Operation\OperationSubject;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SendPaymentVoter extends Voter
{

    protected function supports(string $attribute, mixed $subject): bool
    {
        if(!$subject instanceof OperationSubject){
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if(!in_array('ROLE_PAYMENT', $user->getRoles())){
            return false;
        }

        return true;
    }
}
```

> If you want to protect you api from an autentication context (JWT token, user / pass) you can use [symfony security](https://symfony.com/doc/current/security/custom_authenticator.html)

