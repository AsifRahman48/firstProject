<?php

namespace App\Http\Controllers;

use App\ServerDnsName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServerDnsNameController extends Controller
{
    public function index()
    {
        $server_data = ServerDnsName::latest()->first();

        $data = [
            'server_data' => $server_data,
            'ctrlName' =>  'dns',
            'pageTitle' => 'Server DNS Name'
        ];

        return view('server-dns.index', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'dns_name' => 'required|max:255'
            ]
        );

        if($validate->fails()) {
            return redirect()->route('server-dns.index')->with('error', $validate->errors()->first());
        }

        ServerDnsName::find($id)->update($request->all());

        return redirect()->route('server-dns.index')->with('success', 'Server Data Updated Successfully!');
    }
}
