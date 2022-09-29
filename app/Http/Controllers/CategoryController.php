<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategory;
use Exception;
use Carbon\Carbon;
use App\Http\Helpers\Helper;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->middleware(['auth']);
        // $this->middleware('role:Admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = [
            'pageTitle' => 'Department List',
            'ctrlName'  => 'category',
            'listData'  => Category::orderBy('name')->paginate(10)
        ];
        $pageName='category.index';
        return Helper::checkAdmin($pageName,$data);
        // return view('', compact(''));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Department Add',
            'ctrlName'  => 'category'
        ];
        $pageName='category.create';
        return Helper::checkAdmin($pageName,$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategory  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Insert Data
        if(!empty($request['deactive_date'])){
            $deative_date=date('Y-m-d', strtotime($request['deactive_date']));
        }else{
            $deative_date='0000-00-00';
        }


        try{
            $isert= new Category();
            $isert->name= $request['name'];
            $isert->active_date=Carbon::parse($request['active_date'])->format('Y-m-d');
             if(!empty($request['deactive_date'])){
            $isert->deactive_date=$deative_date;
        }
            $isert->save();
        }catch(Exception $e) {
            return back()->withError('Value already exists.')->withInput();
        }
        return redirect('category')->with('status', 'Successfully New Department Created!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $catID int Category ID
     * @return \Illuminate\Http\Response
     */
    public function edit($catID)
    {
        $data = [
            'pageTitle' => 'Category Update',
            'ctrlName'  => 'category',
            'editData'  => Category::where('id', '=', $catID)->firstOrFail()
        ];
        $pageName='category.edit';
        return Helper::checkAdmin($pageName,$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategory  $request
     * @param  $catID int Category ID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $catID)
    {
        $category = Category::where('id', '=', $catID)->firstOrFail();
        $category->name = $request['name'];
         $category->active_date=Carbon::parse($request['active_date'])->format('Y-m-d');
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
        return redirect('category')->with('status', 'Successfully Department Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $catID int Category ID
     * @return \Illuminate\Http\Response
     */
    public function destroy($catID)
    {
        try{
            $category = Category::find($catID);
            $category->delete();
        }catch(Exception $e) {
            return back()->withError("Can't be deleted, have dependency.")->withInput();
        }
        return redirect('category')->with('status', 'Successfully Department Deleted!');
    }
}
