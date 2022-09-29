<?php

namespace App\Http\Controllers\V2;

use App\AuditLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stevebauman\Location\Facades\Location;

class AuditLogController extends Controller
{
    public function index()
    {
//        $actionTypes = AuditLog::query()->distinct()->pluck('menu_journey')->toArray();
        $actionTypes = [
            "archive live",
            "ad user manual import",
            "ad user auto import",
            "auto full backup",
            "auto db backup",
            "backup manual",
            "manage company",
            "manage department",
            "manage unit/section",
            "manage reassign",
            "manage tickets",
            "manage users",
            "request inbox",
            "request new",
            "request drafts",
            "vacation",
        ];
        $logs = AuditLog::with('causer')
            ->when(request('start_date'), function ($query) {
                $query->whereBetween('created_at', [Carbon::parse(request('start_date'))->format('Y-m-d'), request('start_end') ? Carbon::parse(request('start_end'))->endOfDay() : Carbon::parse(request('start_date'))->endOfDay()]);
            })
            ->when(request('action_type'), function ($query) {
                $query->where('menu_journey', request('action_type'));
            })
            ->when(request('user_id'), function ($query) {
                $query->where('causer_id', request('user_id'));
            })
            ->orderBy('id', 'desc')->paginate(30);

        $data = [
            'pageTitle' => 'Audit Logs',
            'auditLogs' => $logs,
            'actionTypes' => $actionTypes
        ];

        return view('report.audit_log.index', compact('data'));
    }

    public function show($id)
    {
        $log = AuditLog::findorfail($id);
        $data = [
            'pageTitle' => 'Audit Log Details',
            'auditLog' => $log,
            'ipInfo' => Location::get($log->ip)
        ];

        return view('report.audit_log.show', compact('data'));
    }
}
