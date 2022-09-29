<?php
namespace App\Services\V2;

use App\Contracts\IUserService;
use App\Repositories\V2\UserRepository;

class UserService implements IUserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function insertUsers($users)
    {
        return $this->userRepository->storeMultipleUsers($users);
    }

    public function getByEmailAndUserName($email, $userName)
    {
        return $this->userRepository->getByEmailAndUserName($email, $userName);
    }

    public function updateIfIsChanged($user, $data)
    {
        return $this->userRepository->updateIfIsChanged($user, $data);
    }
}
