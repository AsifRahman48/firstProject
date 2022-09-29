<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServerDnsName extends Model
{
    protected $fillable = ['dns_name', 'ip_address', 'status'];
}
