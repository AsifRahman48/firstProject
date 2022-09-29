<?php
namespace App\Repositories\V2;

use App\User;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function storeMultipleUsers($users)
    {
        return $this->user->insert($users);
    }

    public function getByEmailAndUserName($email, $userName)
    {
        return $this->user
            ->where('email', '=', trim($email))
            ->Orwhere('user_name', '=', trim($userName))
            ->first();
    }

    public function updateIfIsChanged($user, $data)
    {
        $user->name = $data['name'];
        $user->title = $data['title'];
        $user->department = $data['department'];
        $user->telephonenumber = $data['telephonenumber'];
        $user->company_name = $data['company_name'];

        if ($user->isDirty()) {
            $user->save();
            return true;
        }
    }
}
