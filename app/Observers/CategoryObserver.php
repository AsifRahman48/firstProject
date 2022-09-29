<?php

namespace App\Observers;

use App\Category;
use App\Traits\AuditLogTrait;

class CategoryObserver
{
    use AuditLogTrait;

    public function created(Category $category)
    {
        $this->logStore("created", "department", "$category->name department created.",'manage department');
    }

    public function updated(Category $category)
    {
        $this->logStore("updated", "department", "$category->name department updated.",'manage department');
    }

    public function deleted(Category $category)
    {
        $this->logStore("deleted", "department", "$category->name department deleted.",'manage department');
    }
}
