<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use App\Category;
use App\Company;
use Auth;
use App\Http\Helpers\Helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CompanyName extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    $data = [
            'pageTitle' => 'Company List',
            'ctrlName'  => 'companyName',
            'listData'  => Company::orderBy('name')->paginate(15)
        ];
        // orderBy('id', 'DESC')->
        $pageName='company.index';
        return Helper::checkAdmin($pageName,$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $data = [
            'pageTitle' => 'Company Name Add',
            'ctrlName'  => 'companyName'
        ];
        $pageName='company.create';
        return Helper::checkAdmin($pageName,$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//         $messages = [
//     'logo.dimensions' => 'Please Select Logo size W:120px & H:60px',
// ];
//  $request->validate($messages);
  $validatedData = $request->validate([
        'name' => 'required|unique:company_name|max:255',
        'active_date' => 'required',
        'shortName' => 'required',
        'logo' => 'mimes:jpeg,jpg,png,gif|required|dimensions:width=160,height=80',
    ]);
   // $this->validate($request, [
   //           'avatar' => ''
   //      ]);

        if(!empty($request['deactive_date'])){
            $deative_date=date('Y-m-d', strtotime($request['deactive_date']));
        }else{
            $deative_date='0000-00-00';
        }


        try{
        $file=$request->file('logo');
        $fileName  = $request['name'].'-'.time().'-'.$file->getClientOriginalName();
        // $fileType=$file->getClientOriginalExtension();
        $destinationPath = public_path('/upload/company');
        $file->move($destinationPath, $fileName);

            $isert= new Company();
            $isert->name= $request['name'];
            $isert->short_name= $request['shortName'];
            $isert->logo= $fileName;
            $isert->active_date=Carbon::parse($request['active_date'])->format('Y-m-d');
             if(!empty($request['deactive_date'])){
            $isert->deactive_date=$deative_date;
        }
            $isert->save();
        }catch(Exception $e) {
            return back()->withError('Value already exists.')->withInput();
        }
        return redirect('company/list')->with('status', 'Successfully New Company Created!');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [
            'pageTitle' => 'Company Name Update',
            'ctrlName'  => 'companyName',
            'editData'  => Company::where('id', '=', $id)->firstOrFail()
        ];
        $pageName='company.edit';
        return Helper::checkAdmin($pageName,$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

  $validatedData = $request->validate([
        'name' => 'sometimes|required|unique:company_name,id,'.$id,
        'active_date' => 'required',
         'shortName' => 'required',
        // 'logo' => 'mimes:jpeg,jpg,png,gif|required|max:10000',
    ]);


       $category = Company::where('id', '=', $id)->firstOrFail();
        $category->name = $request['name'];
          $category->short_name= $request['shortName'];
         $category->active_date=Carbon::parse($request['active_date'])->format('Y-m-d');
if(!empty($request->file('logo'))){
    $validatedData = $request->validate([
        'logo' => 'mimes:jpeg,jpg,png,gif|required|dimensions:width=160,height=80'
    ]);

       $file=$request->file('logo');
        $fileName  = $request['name'].'-'.time().'-'.$file->getClientOriginalName();
        // $fileType=$file->getClientOriginalExtension();
        $destinationPath = public_path('/upload/company');
        $file->move($destinationPath, $fileName);
        $category->logo= $fileName;

}


             if(!empty($request['deactive_date'])){
                  $deative_date=date('Y-m-d', strtotime($request['deactive_date']));
            $category->deactive_date=$deative_date;
        }else{
          $category->deactive_date=null;
        }

        try{
            $category->save();
        }catch(Exception $e) {
            return back()->withError('Value already exists.')->withInput();
        }
        return redirect('company/list')->with('status', 'Successfully Comapany Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     //
    // }
}
