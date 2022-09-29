<?php

namespace App\Jobs;

use App\Contracts\ILdapService;
use App\Contracts\ILdapUserImportLogService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LdapUserImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $importLogService;
    protected $ldapService;

    /**
     * Create a new job instance.
     *
     */
    public function __construct()
    {
        $this->importLogService = resolve(ILdapUserImportLogService::class);
        $this->ldapService = resolve(ILdapService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $counts = $this->ldapService->importUsers();
        $data = [
            'imported_by' => 'Cron Job',
            'date' => Carbon::now()->toDateString(),
            'inserted_users' => $counts['total_inserted_user'],
            'updated_users' => $counts['total_updated_user'],
            'type' => 'auto',
        ];

        $this->importLogService->store($data);
    }
}
