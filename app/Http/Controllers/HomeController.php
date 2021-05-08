<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

          $sellers = DB::table('tbl_seller_accounts')
                    ->select('pk_seller_account_id','seller_account_name','seller_account_primary_email')
                ->get()
                ->toArray();

        return view('home')->with('sellers',$sellers);
    }

    public function deleteSeller($id){
            
            $query = DB::table('tbl_seller_accounts')->where('pk_seller_account_id', $id)->delete();

             $sellers = DB::table('tbl_seller_accounts')
                    ->select('pk_seller_account_id','seller_account_name','seller_account_primary_email')
                ->get()
                ->toArray();

            return view('home')->with('sellers',$sellers);


    }

}
