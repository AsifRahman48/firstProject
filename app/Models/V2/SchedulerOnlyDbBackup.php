<?php

namespace App\Models\V2;

use Illuminate\Database\Eloquent\Model;


class SchedulerOnlyDbBackup extends Model
{
    protected $fillable = ['name', 'size', 'path'];
}
