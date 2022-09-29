<?php

namespace App\Observers;

use App\SubCategory;
use App\Traits\AuditLogTrait;

class SubCategoryObserver
{
    use AuditLogTrait;

    public function created(SubCategory $subCategory)
    {
        $this->logStore("created","unit/section","$subCategory->name unit/section created.",'manage unit/section');
    }

    public function updated(SubCategory $subCategory)
    {
        $this->logStore("updated","unit/section","$subCategory->name unit/section updated.",'manage unit/section');
    }

    public function deleted(SubCategory $subCategory)
    {
        $this->logStore("deleted","unit/section","$subCategory->name unit/section deleted.",'manage unit/section');
    }
}
