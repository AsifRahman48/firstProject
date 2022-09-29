<?php
namespace App\Contracts;

interface IUserService
{
    public function insertUsers($users);

    public function getByEmailAndUserName($email, $userName);

    public function updateIfIsChanged($user, $data);
}
