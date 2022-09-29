<?php

namespace App\Http\Controllers\V2;

use App\Contracts\ILdapService;
use App\Contracts\ILdapUserImportLogService;
use App\Traits\AuditLogTrait;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LdapUserImportLogsController extends Controller
{
    use AuditLogTrait;

    protected $importLogService;
    protected $ldapService;

    public function __construct(ILdapUserImportLogService $importLogService, ILdapService $ldapService)
    {
        $this->importLogService = $importLogService;
        $this->ldapService = $ldapService;
    }

    public function manual()
    {
        $manuals = $this->importLogService->indexManual();
        $data = [
          'manuals' => $manuals,
          'pageTitle' => 'Import Ldap users manually',
        ];

        return view('v2.ldap.manual-import', compact('data'));
    }

    public function auto()
    {
        $autos = $this->importLogService->indexAuto();
        $scheduler = json_decode(setting('scheduler_ldap'));
        $data = [
            'autos' => $autos,
            'scheduler' => $scheduler,
            'pageTitle' => 'Import Ldap users automatically',
        ];

        return view('v2.ldap.auto-import', compact('data'));
    }

    public function manualImport()
    {
        $counts = $this->ldapService->importUsers();
        $data = [
            'imported_by' => auth()->user()->name,
            'date' => Carbon::now()->toDateString(),
            'inserted_users' => $counts['total_inserted_user'],
            'updated_users' => $counts['total_updated_user'],
            'type' => 'manual',
        ];

        $this->importLogService->store($data);

        if (($data['inserted_users'] ?? 0) > 0 || ($data['updated_users'] ?? 0) > 0){
            $this->logStore('created','ad user manual import',"Ad users imported manually.",'ad user manual import');
        }

        return response()->json([
            'data' => $counts,
        ], 200);
    }

    public function autoImport(Request $request)
    {
        $inputs = $request->only(['time', 'is_disable']);

        $setting = storeSetting('scheduler_ldap', json_encode($inputs));

        $this->logStore('updated','ad user auto import','Ad users auto import scheduler setting updated.','ad user auto import');

        return response()->json([
            'data' => $setting,
        ], 200);
    }


}
