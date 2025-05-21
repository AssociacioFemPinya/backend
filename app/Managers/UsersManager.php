<?php

declare(strict_types=1);

namespace App\Managers;

use App\Colla;
use App\Factories\UserFactory;
use App\Repositories\UserRepository;
use App\User;
use Symfony\Component\HttpFoundation\ParameterBag;

class UsersManager
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createUser(Colla $colla, ParameterBag $bag): User
    {
        $user = UserFactory::make($colla->getId(), $bag);
        $this->repository->save($user);

        return $user;
    }

    public function updateUser(User $user, ParameterBag $bag): User
    {
        $event = UserFactory::update($user, $bag);
        $this->repository->save($user);

        return $user;
    }

    public function deleteUser(User $user)
    {
        $this->repository->delete($user);

    }
}
