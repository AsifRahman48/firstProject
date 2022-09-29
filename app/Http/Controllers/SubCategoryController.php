<?php

namespace App\Http\Controllers;

use App\Category;
use App\SubCategory;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSubCategory;
use Exception;
use Carbon\Carbon;
use App\Http\Helpers\Helper;

class SubCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');
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
            'pageTitle' => 'Unit/Section List',
            'ctrlName'  => 'sub_category',
            'listData'  => SubCategory::join('categorys', 'categorys.id', 'sub_categorys.cat_id')->select('sub_categorys.*')->orderBy('categorys.name', 'asc')->get()
        ];

        // dd($data);
        $pageName='sub_category.index';
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
            'pageTitle' => 'Unit/Section Add',
            'catList'   => Category::orderBy('id', 'DESC')->where('active_date','<=',date('Y-m-d'))
                ->Where(function ($query) {
                $query->whereNull('deactive_date');
                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->pluck('name', 'id'),
            'ctrlName'  => 'sub_category'
        ];
        $pageName='sub_category.create';
        return Helper::checkAdmin($pageName,$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSubCategory  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Insert Data
        $insertData = [
            'cat_id' => $request['cat_id'],
            'name'   => $request['name']
        ];

        try{
            $isert= new SubCategory();
            $isert->cat_id= $request['cat_id'];
            $isert->name= $request['name'];
            $isert->active_date=Carbon::parse($request['active_date'])->format('Y-m-d');
             if(!empty($request['deactive_date'])){
                 $deative_date=date('Y-m-d', strtotime($request['deactive_date']));
            $isert->deactive_date=$deative_date;
        }
            $isert->save();
            // SubCategory::create($insertData);
        }catch(Exception $e) {
            return back()->withError('Value already exists.')->withInput();
        }
        return redirect('sub-category')->with('status', 'Successfully New Unit/Section Created!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $subCatID int Sub-category ID
     * @return \Illuminate\Http\Response
     */
    public function edit($subCatID)
    {
        $data = [
            'pageTitle' => 'Sub-category Update',
            'catList'   => Category::orderBy('id', 'DESC')->pluck('name', 'id'),
            'ctrlName'  => 'sub_category',
            'editData'  => SubCategory::where('id', '=', $subCatID)->firstOrFail()
        ];
        $pageName='sub_category.edit';
        return Helper::checkAdmin($pageName,$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreSubCategory  $request
     * @param  $subCatID int Sub-category ID
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $subCatID)
    {

        $subCategory = SubCategory::where('id', '=', $subCatID)->firstOrFail();
        $subCategory->cat_id = $request['cat_id'];
        $subCategory->name   = $request['name'];
           $subCategory->active_date=Carbon::parse($request['active_date'])->format('Y-m-d');
             if(!empty($request['deactive_date'])){
                 $deative_date=date('Y-m-d', strtotime($request['deactive_date']));
            $subCategory->deactive_date=$deative_date;
        }else{
                      $subCategory->deactive_date=null;
        }
        try{
            $subCategory->save();
        }catch(Exception $e) {
            return back()->withError('Value already exists.')->withInput();
        }
        return redirect('sub-category')->with('status', 'Successfully Unit/Section Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $subCatID int Sub-category ID
     * @return \Illuminate\Http\Response
     */
    public function destroy($subCatID)
    {
        try{
            $subCat = SubCategory::find($subCatID);
            $subCat->delete();
        }catch(Exception $e) {
            return back()->withError("Can't be deleted, have dependency.")->withInput();
        }
        return redirect('sub-category')->with('status', 'Successfully Unit/Section Deleted!');
    }
}
