# Upgrading notes

## From 1.07 to 1.08

#### Change 1
Add *getContext()* method to all operations since *OperationInterface* defines such method from v1.0.8

#### Change 2
In your controllers pass the context as the last argument to *OperationControllerTrait::executeOperation* since it requires it
since v1.0.8. Leave Context constructor empty if you do not want to use a context.

- Not using context:
```php
return $this->executeOperation($request, $serializer, $operationHandler, new Context());
```

- Using context:
```php
return $this->executeOperation($request, $serializer, $operationHandler, new Context('client'));
```
