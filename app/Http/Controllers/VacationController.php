<?php

namespace App\Http\Controllers;

use App\Services\VacationService;
use App\Traits\AuditLogTrait;
use App\User;
use App\LeaveType;
use Carbon\Carbon;
use App\UserVacation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\VacantionRequest;

class VacationController extends Controller
{
    use AuditLogTrait;

    private $vacationService;

    public function __construct(VacationService $vacationService)
    {
        $this->vacationService = $vacationService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        $vacantions = UserVacation::where('user_id', Auth::user()->id)->paginate(10);

        $data = [
            'pageTitle' => 'Vacation',
            'vacations' => $vacantions
        ];

        return view('vacation.index', compact('data'));
    }

    public function create()
    {
        if ($this->vacationService->checkUserHasSetVacationOrNot(Auth::user()->id)) {
            return redirect()->back()->with('error', 'You have already one active vacantion!');
        }

        $leaves = LeaveType::pluck('name', 'id');
        $data = [
            'pageTitle' => 'Set Vacation',
            'leaves' => $leaves
        ];

        return view('vacation.create', compact('data', 'leaves'));
    }

    /**
     * @param VacantionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(VacantionRequest $request)
    {
        if ($this->vacationService->checkUserHasSetVacationOrNot(Auth::user()->id)) {
            return redirect()->back()->with('error', 'You have already one active vacantion!');
        }

        $data = $request->all();

        $data = $this->prepareVacationData($data);

        UserVacation::create($data);

        $status = $request->status == 'draft' ? "draft" : "created";
        $this->logStore($status,'vacation',"Vacation $status.","vacation");

        return redirect()->route('vacations.index')->with('success', 'Vacation Set Successfully!');
    }

    public function edit($id)
    {
        $vacation = UserVacation::where('id', $id)->first();
        $leaves = LeaveType::pluck('name', 'id');
        $users = User::where('id', '!=', Auth::user()->id)->get()->pluck('full_name', 'id');

        $data = [
            'pageTitle' => 'Edit Vacation',
            'vacation' => $vacation,
            'leaves' => $leaves,
            'users' => $users
        ];


        return view('vacation.edit', compact('data'));
    }

    /**
     * @param VacantionRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(VacantionRequest $request, $id)
    {
        $data = $request->all();

        $data = $this->prepareVacationData($data);

        UserVacation::find($id)->update($data);

        $status = $request->status == 'draft' ? "draft" : "updated";
        $this->logStore($status,'vacation',"Vacation $status.","vacation");

        return redirect()->route('vacations.index')->with('success', 'Vacation Updated Successfully!');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        UserVacation::where('id', $id)->delete();

        $this->logStore('deleted','vacation',"Vacation deleted.","vacation");

        return redirect()->route('vacations.index')->with('success', 'Vacation Deleted Successfully ! ');
    }

    /**
     * @param $data
     * @return mixed
     */
    public function prepareVacationData($data)
    {
        $data['user_id'] = Auth::user()->id;
        $data['from_date'] = Carbon::parse($data['from_date'])->format('Y-m-d h:i:s');
        $data['to_date'] = Carbon::parse($data['to_date'])->format('Y-m-d h:i:s');

        return $data;
    }
}
