<?php
namespace App\Repositories\V2;

use App\Models\V2\LdapUserImportLog;

class LdapUserImportLogRepository
{
    protected $ldapUserImportLog;

    public function __construct(LdapUserImportLog $ldapUserImportLog)
    {
        $this->ldapUserImportLog = $ldapUserImportLog;
    }

    public function getManual()
    {
        return $this->ldapUserImportLog->where('type', 'manual')->paginate(15);
    }

    public function getAuto()
    {
        return $this->ldapUserImportLog->where('type', 'auto')->paginate(15);
    }

    public function store(array $inputs)
    {
        return $this->ldapUserImportLog->create($inputs);
    }
}
