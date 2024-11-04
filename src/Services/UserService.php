<?php

namespace Nightfury\TaskManagementSystem\Services;

use Nightfury\TaskManagementSystem\Models\User;

class UserService
{
    public function getAllUsers(): array
    {
        return User::all();
    }

    public function getUserById(int $id): ?User
    {
        return User::findById($id);
    }

    public function getUserByUsername(string $username): ?User
    {
        return User::findByUsername($username);
    }

    public function createUser(string $username, string $email, string $password): ?User
    {
        return (new User(
            id: 0,
            username: $username,
            email: $email,
            password: password_hash($password, PASSWORD_DEFAULT)
        ))->save();
    }

    public function login(string $username, string $password): ?User
    {
        $user = User::findByUsername($username);

        if ($user && password_verify($password, $user->getPassword())) {
            return $user;
        }

        return null;
    }

    public function updateUser(User $user, array $data): User
    {
        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPassword($data['password']);
        }

        return $user->save();
    }

    public function deleteUser(int $id): bool
    {
        return User::delete($id);
    }
}
