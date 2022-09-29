<?php

namespace App\Observers;

use App\Company;
use App\Traits\AuditLogTrait;

class CompanyObserver
{
    use AuditLogTrait;

    public function created(Company $company)
    {
        $this->logStore("created","company","$company->name( $company->short_name ) company created.",'manage company');
    }

    public function updated(Company $company)
    {
        $this->logStore("updated","company","$company->name( $company->short_name ) company updated.",'manage company');
    }

    public function deleted(Company $company)
    {
        $this->logStore("deleted","company","$company->name( $company->short_name ) company deleted.",'manage company');
    }
}
