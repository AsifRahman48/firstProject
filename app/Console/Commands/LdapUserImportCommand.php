<?php

namespace App\Console\Commands;

use App\Jobs\LdapUserImportJob;
use Illuminate\Console\Command;

class LdapUserImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ldap:user-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importing the Ldap users.';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        LdapUserImportJob::dispatch();
    }
}
