<?php

namespace App\Http\Controllers\Setting;

use App\SiteSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'Settings',
            'setting' => SiteSetting::first() ?? [],
        ];

        return view('setting.index', compact('data'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        @ini_set( 'upload_max_size' , '64M' );
        $this->validate($request, [
           'site_title' => 'required',
           'copyright_text' => 'required',
            'logo' => 'image|mimes:jpeg,png,jpg|max:2048',
            'favicon' => 'image|mimes:jpeg,jpg,png,jpg|max:300'
        ]);
        if ($request['logo']) {
            $data['logo'] = $request['logo']->storeAs('setting',
                     'logo.' . $request['logo']->getClientOriginalExtension());
        }

        if ($request['favicon']) {
            $data['icon'] = $request['favicon']->storeAs('setting',
                'favicon.' . $request['favicon']->getClientOriginalExtension());
        }

        if ($request['admin_manual']) {
            $file = $request->file('admin_manual');
            $file->move(storage_path('app/public/setting'), 'user_manual(admin).pdf');
            $data['user_manual_admin'] = '/storage/setting/user_manual(admin).pdf';
        }


        if ($request['user_manual']) {
            $file = $request->file('user_manual');
            $file->move(storage_path('app/public/setting'), 'user_manual(user).pdf');
            $data['user_manual_user'] = '/storage/setting/user_manual(user).pdf';
        }

        $data['site_title'] = $request['site_title'];
        $data['footer_copyright'] = $request['copyright_text'];

        $siteSetting = new SiteSetting();
        $siteSetting->first() ? $siteSetting->where('id',
            $siteSetting->first()->id)->update($data) : $siteSetting->create($data);

        Config::set('app.name', $request['site_title']);
        Artisan::call('config:clear');
        Session::flash('message', 'Site setting updated successfully');
        return redirect()->back();
    }

    private function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $oldValue = explode($str, "{$envKey}=");

        $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}\n", $str);

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }



}
