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

The bundle pass an Ict\ApiOneEndpoint\Model\Operation\OperationSubject as an attribute to the voter. This gives you access to operation name and 
operation group (if it's been defined in getGroup method). Groups could be useful when you want to grant access to a group of operations to certain role or roles.
As an example, let's imagine you have the following operations:

- CreateAccount
- UpdateAccount
- RemoveAccount

If you would want to restrict access to admin role, you would have to return the same group in getGroup method and then check the group in your voter.

```php
use Ict\ApiOneEndpoint\Model\Operation\OperationSubject;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountManagementVoter extends Voter
{

    protected function supports(string $attribute, mixed $subject): bool
    {
        if(!$subject instanceof OperationSubject){
            return false;
        }

        return $subject->getGroup() === 'ACCOUNT';
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if(!in_array('ROLE_ADMIN', $user->getRoles())){
            return false;
        }

        return true;
    }
}
```
> This bundle always check operation authorization by it's possible there are no voters defined or no voters supporting conditions. To avoid getting denied access when no voters executed, set the following access decision strategy in your security.yaml file:

```yaml
security:
   ......
   access_decision_manager:
      strategy: unanimous
      allow_if_all_abstain: true
```

You can find more information about access decision strategy in [symfony docs](https://symfony.com/doc/current/security/voters.html#changing-the-access-decision-strategy)

### Sending operations to the background

In order to allow developers to configure some operations to be executed in the background, this bundle relies on:

- [Symfony messenger](https://symfony.com/doc/current/messenger.html)
- **Ict\ApiOneEndpoint\Model\Attribute\IsBackground** attribute

Let's go back to SendPayment operation

```php
use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Ict\ApiOneEndpoint\Model\Api\ApiOutput;
use Ict\ApiOneEndpoint\Model\Attribute\IsBackground;

#[IsBackground]
class SendPaymentOperation implements OperationInterface
{
   .......
}
```
When an operation is annotated with **IsBackground** attribute, it's execution will be performed in the background and the client will no have to wait until it finishes. It can be useful for operations which can take more time to finish. Sending an email or a payment would be examples of this kind of operations.

After an operation is sent to the background, a 202 (Accepted) http request is returned to the client with the following content:

```json
  {
     "status" : "QUEUED"
  }
```
If you want to delay the execution some time, you can add the delay property to the attribute:

```php
use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Ict\ApiOneEndpoint\Model\Api\ApiOutput;
use Ict\ApiOneEndpoint\Model\Attribute\IsBackground;

#[IsBackground(delay: 300)]
class SendPaymentOperation implements OperationInterface
{
   .......
}
```

It will delay the execution 300 seconds (5 minutes).

### Sending notifications to the user

When an operation execution has been sent to the background, it can be useful to send an notification to the client to notify about the status of the operation. This bundle uses [symfony mercure bundle](https://symfony.com/doc/current/mercure.html) to send a notification to the client when an operation execution finishes.

Again, let's go back to SendPayment operation

```php
use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Ict\ApiOneEndpoint\Contract\Operation\OperationNotificationInterface;
use Ict\ApiOneEndpoint\Model\Api\ApiOutput;
use Ict\ApiOneEndpoint\Model\Attribute\IsBackground;

#[IsBackground]
class SendPaymentOperation implements OperationInterface, OperationNotificationInterface
{

    private array $notificationData = [];
    
    public function perform(mixed $operationData): ApiOutput
    {
        // Perform operation ....
        // Sending bizum, BTC ......
        
        $this->notificationData = []; // Push here all data we want to push back to the client
        return new ApiOutput([], 200);
    }

    public function getNotificationData(): string
    {
        return json_encode($this->notificationData);
    }

    public function getTopic(?string $userIdentifier = null): string
    {
        return 'https://mydomain.com/' . $userIdentifier . '/payments';
    }
```

As we can see above, we have to implement _Ict\ApiOneEndpoint\Contract\Operation\OperationNotificationInterface_ in our operation to be able to send notification data to the client. This interface defines two methods:

- **getNotificationData()**: Returns raw data which will be sent as notification payload (You have to use a format you can easily read on the client. Json format use to be the preferred one ).
- **getTopic()**: Topic client will have to subscribe to in order to read notifications (See [mercure docs](https://mercure.rocks/spec#topic-selectors) to learn more about topics).

With this, a notification would be sent to the client after a payment sending operation finishes.

> If you want to use another way to sending notifications, you can create your own services and use it into your operations.

### The controller

Setting up your controller is a really easy task. Let's take a look

```php
use Ict\ApiOneEndpoint\Controller\ApiOeController;
use Ict\ApiOneEndpoint\Operation\OperationHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/backend/v1')]
class BackendController extends ApiOeController
{
    #[Route('', name: 'api_backend_v1_process_operation', methods: ['POST'])]
    public function processOperation(Request $request, SerializerInterface $serializer, OperationHandler $operationHandler): JsonResponse
    {
        return $this->executeOperation($request, $serializer, $operationHandler);
    }
}
```

You simply have to create your controller and extends it from _Ict\ApiOneEndpoint\Controller\ApiOeController_. The use the method _executeOperation_ passing to it $request, $serializer and $operationHandler as an arguments and your operation will be executed.
