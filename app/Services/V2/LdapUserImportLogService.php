<?php
namespace App\Services\V2;

use App\Contracts\ILdapUserImportLogService;
use App\Repositories\V2\LdapUserImportLogRepository;

class LdapUserImportLogService implements ILdapUserImportLogService
{
    protected $logRepository;

    public function __construct(LdapUserImportLogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function indexManual()
    {
        return $this->logRepository->getManual();
    }

    public function indexAuto()
    {
        return $this->logRepository->getAuto();
    }

    public function store(array $data)
    {
        return $this->logRepository->store($data);
    }
}
