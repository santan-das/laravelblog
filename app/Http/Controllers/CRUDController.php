<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Crud;
use DB;
use App\Common_model;


class CRUDController extends Controller
{

     public $error_reason;
    public $success_message;
    public $data;

    public function __construct(){
        $this->error_reason         = "";
        $this->success_message      = "";
        $this->data                 = (object)array();
        error_reporting(0);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       //$cruds = Crud::all()->toArray();
        $cruds = DB::table('cruds')
                ->get()
                ->toArray();

        return view('crud.index', compact('cruds'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('crud.create');
    }


    public function testingcode(Request $request){

        $getpincodes = DB::table('tbl_visibility_of_data_based_on_pincodes')
                        ->select('set_of_pincodes_access_over')
                        ->get();
        
        $getallpins  = $getpincodes[0]->set_of_pincodes_access_over;

        echo '<pre>';
        $pincodearr = explode(',', $getallpins);
        foreach($pincodearr as $key => $value)
        {
           // print_r($value);
            $arrData = array( 
               'pincodes'    => $value                     
            );
            DB::table('tbl_pincodes')->insert($arrData);
        }
            die;

    }

    public function pincodeData(Request $request){

         $where = "";
        $query = "SELECT t1.seller_account_name  FROM tbl_seller_accounts- t1 
            LEFT JOIN tbl_pincodes t2 ON t2.pincodes=t1.seller_account_pincode
            WHERE t2.status='1' " . $where . " ";

        $result = DB::select(DB::raw($query));

        return $result;



    }

    public function getAllUsers(){

        $getusers = DB::table('tbl_users')
                        ->get();

        $allusers = array();
        $url = url('');
        foreach ($getusers as $key => $value) {

                if(!empty($value->user_image)){
                  $path = $url.'/uploads/profile_pic/'.$value->user_image;  
              }else{
                  $path = "";
              }

                
                $userarr = array(
                                'id'=> $value->id,
                                'name'=> $value->name,
                                'email'=> $value->email,
                                'title'=> $value->title,
                                'user_image'=> $path,
                                );

                array_push($allusers, $userarr);
        }

        return response()->json(['success' => 'true','data'=>$allusers]);
    }

    public function getViewdata($id,$custid){


          $returnHTML = view('delivery_document')->with(['final_orders' => $final_orders[0]])->render();
          
        return $returnHTML;
        //return response()->json(['success' => 'true','data'=>$returnHTML]);

    }

    public function getTotalUsers(Request $request){

        $params['offset'] = $request->offset;
        $params['limit']  = $request->limit;
        $params['count'] = 1;
        $getusers = Common_model::getTotalUsers($params);

          $finalarr['users']=$getusers;
          $params['count'] = 1;
         $finalarr['users_count'] = count(Common_model::getTotalUsers($params));

        if(count($finalarr)>0){
            return response()->json(['success' => 'true','data'=>$finalarr]);
        }else{
              return response()->json(['success' => 'false','data'=>'']);
        }

    }


    public function singleUser(Request $request){

        $id = $request->id;
        $getusers = DB::table('tbl_users')
                        ->where('id','=',$id)
                        ->get();

        return response()->json(['success' => 'true','data'=>$getusers]);
    }

    public function submitData(Request $request){
   /*     echo '<pre>';
        print_r($request->all());
        die;*/

         if ( $image = $request->file('file')) {
            //$image = $user_image;
            $filename = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/profile_pic');
            $image->move($destinationPath, $filename);
            //$file_filename = $filename;
            /* $cover = $request->file('image_profile');
              $extension = time().'.'.$image->getClientOriginalExtension();
               $filename = $request->image_profile->storeAs('public/uploads/profile_pic',$extension);
             $where            =   array('tbl_users.pk_id' => $member_id);
             $get_details      =   array(
               'table_name'  =>  'tbl_users',
               'fields_data' =>  array(
                                      'first_name' => $user_fname,'last_name'=> $user_lname, 'telephone' => $user_mobile,'user_image'=> $extension,'dept_id'=> $user_dept,'fk_role_id' => $user_role,'username'=>$encmail
                                    ),
               'where'       =>  $where
           );*/
        }


       $data  = array(
                         'email' => isset($request->email)?$request->email:'',
                         'title' => isset($request->deptxt)?$request->deptxt:'',
                         'name'  => isset($request->name)?$request->name:'',
                         'user_image'  => isset($filename)?$filename:'',
                        'created_at'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s'));
       $lastid =  \DB::table('tbl_users')->insertGetId($data);
       if($lastid){

        return response()->json(['success' => 'true','data'=>$lastid]);
          }

    }

    public function updateRecord(Request $request){
        $userid = $request->id;
        $upatearr = array(
                        'email' => $request->email,
                        'name'  => $request->name,
                        'title' => 'mr',
                        );

        $updatedata = \DB::table('tbl_users')
            ->where('id', $userid)
            ->update($upatearr);

         if($updatedata){

                return response()->json(['success' => 'true','data'=>$updatedata]);
          }



    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
        'input_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title' => 'required',
        'post'   => 'required'
    ]);


    if ($request->hasFile('input_img')) {
        $image = $request->file('input_img');
        $name = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/images');
        $dbpath = 'images/'.$name;
        $image->move($destinationPath, $name);
        //$this->save();

        //return back()->with('success','Image Upload successfully');
    }
        /*this will check what we are getting in request method*/
        //dd($request->all());
        /*$crud = new Crud([
          'title' => $request->get('title'),
          'post' => $request->get('post')
        ]);

        $crud->save();*/

       $data  = array('title' => $request->get('title'), 'post' => $request->get('post'),'image'=>$dbpath);
       $lastid =  \DB::table('cruds')->insertGetId($data);
       if($lastid){

        return redirect('/crud');
          }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $crud = Crud::find($id);

        return view('crud.edit', compact('crud','id'));

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

        if ($request->hasFile('input_img')) {
        $image = $request->file('input_img');
        $name = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/images');
        $dbpath = 'images/'.$name;
        //$destinationPath = '../images/'.$name;
        $image->move($destinationPath, $name);
        //$this->save();

        //return back()->with('success','Image Upload successfully');
    
        $crud = Crud::find($id);
        $crud->title = $request->get('title');
        $crud->post = $request->get('post');

        $crud->image = isset($dbpath) ? $dbpath : '';
        $crud->save();
}
        return redirect('/crud')->with('success','Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $crud = Crud::find($id);
      $crud->delete();

      return redirect('/crud');
    }
}
