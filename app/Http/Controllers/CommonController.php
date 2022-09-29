<?php

namespace App\Http\Controllers;

use App\SubCategory;
use Illuminate\Http\Request;
use Exception;

class CommonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        // $this->middleware('role:Admin,Recommender,Initiator');
    }

    /**
     * Get Subcategory List based on category ID
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function subCatList(Request $request)
    {
        $catID = $request['cat_id'];
        try{
            if(!empty($catID)){
                $sub_cat_list = SubCategory::where('cat_id', '=', $catID)->where('active_date','<=',date('Y-m-d'))
                ->Where(function ($query) {
                $query->whereNull('deactive_date');
                $query->orWhere('deactive_date', '>=', date('Y-m-d'));
                })->orderBy('name')->get()->toArray();
                return response()->json(['status' => 'SUCCESS' , 'msg' => 'Unit/Section List Obtained !', 'data' => $sub_cat_list], 200);
            }
            else{
                throw new Exception('Unit/Section id not found.', 400);
            }
        }catch(Exception $e) {
            return back()->withError($e->getCode().' : '.$e->getMessage())->withInput();
        }
    }
}
