<?php

namespace Ict\ApiOneEndpoint\Contract\Operation;

use Symfony\Component\Security\Core\User\UserInterface;

interface RequiredUserInputInterface
{
    public function getUser(): UserInterface;
    public function setUser(UserInterface $user): void;
}