<?php

namespace App\Services\V2;

use Adldap\Laravel\Facades\Adldap;
use App\Contracts\ILdapService;
use App\Contracts\IUserService;

class LdapService implements ILdapService
{
    protected $userService;

    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }


    public function importUsers()
    {
        $usersNmae = array();
        $userInsetArray = array();
        $paginator = Adldap::search()
            ->users()
            ->select(['cn', 'mail', 'title', 'department', 'telephonenumber', 'company', 'samaccountname'])
            ->paginate(1000, 0);


        $totaluserInsert = 0;
        $totaluserUpdated = 0;
        for ($i = 0; $i < $paginator->getPages(); $i++) {
            $SearchResult = Adldap::search()
                ->users()
                ->select(['cn', 'mail', 'title', 'department', 'telephonenumber', 'company', 'samaccountname'])
                ->paginate(1000, $i);

            foreach ($SearchResult as $key => $resultInfo) {

                $n = $resultInfo['cn'][0];
                if (!empty($n)) {
                    $name = $n;
                } else {
                    $name = null;
                }

                $e = $resultInfo['mail'][0];
                if (!empty($e)) {
                    $email = $resultInfo['mail'][0];
                } else {
                    $email = null;
                }

                $t = $resultInfo['title'][0];
                if (!empty($t)) {
                    $title = $resultInfo['title'][0];
                } else {
                    $title = null;
                }

                $d = $resultInfo['department'][0];
                if (!empty($d)) {
                    $department = $d;
                } else {
                    $department = null;
                }

                $tele = $resultInfo['telephonenumber'][0];
                if (!empty($tele)) {
                    $telephonenumber = $tele;
                } else {
                    $telephonenumber = null;
                }

                $comp = $resultInfo['company'][0];
                if (!empty($comp)) {
                    $company = $comp;
                } else {
                    $company = null;
                }

                $userN = $resultInfo['samaccountname'][0];
                if (!empty($userN)) {
                    $userNameInf = $userN;
                } else {
                    $userNameInf = null;
                }

                if (!empty($userNameInf) && !in_array($userNameInf, $usersNmae) && isset($email)) {
                    $userNames = $userNameInf;
                    $usersNmae[] = $userNameInf;
                    $checkUser = $this->userService->getByEmailAndUserName($email, $userNames);


                    if (empty($checkUser)) {
                        $userInsetArray[] = [
                            'name' => $name,
                            'user_name' => trim($userNames),
                            'email' => trim($email),
                            'title' => $title,
                            'department' => $department,
                            'telephonenumber' => $telephonenumber,
                            'company_name' => $company
                        ];
                        $totaluserInsert++;
                    } else {
                        $userUpdateArray = [
                            'name' => $name,
                            'title' => $title,
                            'department' => $department,
                            'telephonenumber' => $telephonenumber,
                            'company_name' => $company
                        ];

                        $totaluserUpdated += $this->userService->updateIfIsChanged($checkUser, $userUpdateArray);
                    }
                }
            }

            $insert = $this->userService->insertUsers($userInsetArray);
            if ($insert) {
                $usersNmae = array();
                $userInsetArray = array();
            }
        }

        return ['total_inserted_user' => $totaluserInsert, 'total_updated_user' => $totaluserUpdated];
    }
}
