<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Log;

class Common_model extends Model
{


    public static function get_data($data)
    {

        $query  =   DB::table($data['table_name']);

        if(isset($data['fields_data'])){
            $query->select($data['fields_data']?$data['fields_data']:'*');
        }


        if(isset($data['left_join']))
        {
            foreach($data['left_join'] as $other_table){
                $query->leftJoin($other_table['table_name1'],   $other_table['table_name2'].".".$other_table['field_name2']  , '=' , $other_table['table_name1'].".".$other_table['field_name1']);
            }

        }



        if(isset($data['inner_join']))
        {
            foreach($data['inner_join'] as $other_table){
                $query->join($other_table['table_name1'],   $other_table['table_name2'].".".$other_table['field_name2']  , '=' , $other_table['table_name1'].".".$other_table['field_name1']);
            }

        }


        if(isset($data['filter_col']) && !empty($data['filter_col']))
        {
            $query->where($data['filter_col']);
        }


        if(isset($data['where']))
        {
            $query->where($data['where']);
        }



        if(isset($data['or_where']))
        {
            $query->orWhere($data['or_where']);
        }



        if(isset($data['where_between']))
        {
            $query->whereBetween($data['where_between'][0],$data['where_between'][1]);
        }



        if(isset($data['where_in']))
        {
            $query->whereIn($data['where_in']['col_name'],$data['where_in']['col_val']);

        }

        if(isset($data['wherein_arr']))
        {
            $query->whereIn($data['wherein_arr']['col_name'],$data['wherein_arr']['build_arr_vals']);
        }



        if(isset($data['where_not_in']))
        {
            $query->whereNotIn($data['where_not_in']['where_key1'],array($data['where_not_in']['where_val1']));
        }

        if(isset($data['where_not_in2']))
        {
            $query->whereNotIn($data['where_not_in2']['where_key1'],array($data['where_not_in2']['where_val1']));
        }



        if(isset($data['group_by']))
        {
            $query->groupBy($data['group_by']);
        }

        if(isset($data['order_by']))
        {
            foreach($data['order_by'] as $col => $dir){  //*****  $dir-> asc or desc
                $query->orderBy($col, $dir);
            }
        }

        if(isset($data['limit']))
        {
            $query->offset($data['limit']['from_offset']);
            $query->limit($data['limit']['to_limit']);
        }

        if(isset($data['search'])){
            $search = $data['search'];
            $query->where($data['like'], 'LIKE','%'.$search.'%');
                foreach ($data['like_more'] as $key => $value) 
                {
                    $query->orWhere($value, 'LIKE', '%'.$search.'%');
                }
        }

         if(isset($data['search_all'])){
            $search = reset($data['search_all']);
            //$query->where($data['like'], 'LIKE','%'.$search.'%');
                /*if(isset($data['search_all']['cap_id'])){
                    $cap_id        = $data['search_all']['cap_id'];
                    $query->orWhere('cap_id', 'LIKE', '%'.$cap_id.'%');
                }*/
                if($data['search_all']['manufacturer'] !=0){
                    $manufacturer        = $data['search_all']['manufacturer'];
                    $query->Where('tbl_car_derivatives.manufacturer_id',$manufacturer);
                }
                
                if($data['search_all']['fuelType'] !=0){
                    $fueltype        = $data['search_all']['fuelType'];
                    $query->Where('fuel_type',$fueltype);
                }
                if($data['search_all']['transmissionType'] !=0){
                    $transmission        = $data['search_all']['transmissionType'];
                    $query->Where('tbl_car_derivatives.transmission',$transmission);
                }
                if($data['search_all']['transmission'] !=0){
                    $transmission        = $data['search_all']['transmission'];
                    $query->Where('transmission_type',$transmission);
                }
                if($data['search_all']['modelYear'] !=0){
                    $modelyear        = $data['search_all']['modelYear'];
                    $query->Where('tbl_car_model.model_introduced',$modelyear);
                }
                if($data['search_all']['model'] !=0){
                    $carmodel        = $data['search_all']['model'];
                    $query->Where('model_id',$carmodel);
                }
                 if($data['search_all']['model_id'] !=0){
                    $carmodel        = $data['search_all']['model_id'];
                    $query->Where('car_model',$carmodel);
                }

                if($data['search_all']['brand'] !=0){
                    $brand        = $data['search_all']['brand'];
                    $query->Where('tbl_dealer_stock.brand_id',$brand);
                }

                if($data['search_all']['site'] !=0){
                    $site        = $data['search_all']['site'];
                    $query->Where('site_id',$site);
                }
                if($data['search_all']['status'] !=0){
                    $status        = $data['search_all']['status'];
                    $query->Where('status',$status);
                }
                if($data['search_all']['orderId'] !=''){
                    $orderId        = $data['search_all']['orderId'];
                    $query->Where('order_id', 'LIKE', '%'.$orderId.'%');
                }
                 if($data['search_all']['capId'] !=''){
                    $capId        = $data['search_all']['capId'];
                    $query->Where('cap_id', 'LIKE', '%'.$capId.'%');
                    $query->orWhere('cap_code', 'LIKE', '%'.$capId.'%');

                    //$words = explode(' ', $capId);
                    //$query->Where('cap_id', 'LIKE', '%'.$capId.'%');
                    //$query->Where('cap_id',$capId);
                    //foreach ($words as $key => $word) {
                        //$query->orWhere('cap_id', 'LIKE', '%'.$capId.'%');
                       // $query->Where('cap_code', 'LIKE', '%'.$word.'%');
                       //$query->orWhere('cap_code', 'LIKE', '%'.$word.'%');
                    //}
                   
                }


       
        }

        if(isset($data['search_deliveries'])){
            if($data['search_deliveries']['toDate'] !=0 && $data['search_deliveries']['fromDate'] !=0){
                    $toDate        = $data['search_deliveries']['toDate'];
                    $fromDate        = $data['search_deliveries']['fromDate'];
                    $query->Where('order_date','>=',$fromDate);
                    $query->Where('order_date','<=',$toDate);
            }
            if($data['search_deliveries']['orderid'] !=0 || !empty($data['search_deliveries']['orderid'])){
                    $orderid        = $data['search_deliveries']['orderid'];
                    $query->Where('order_id', 'LIKE', '%'.$orderid.'%');
            }
            if($data['search_deliveries']['status'] !=0){
                    $status = explode(',', $data['search_deliveries']['status']);
                    $query->WhereIn('order_status',$status);
            }
            if($data['search_deliveries']['brokerId'] !=0){
                    $broker_id        = explode(',',$data['search_deliveries']['brokerId']);
                    $query->WhereIn('tbl_orders.broker_id',$broker_id);
            }
            if($data['search_deliveries']['empId'] !=0){
                    $emp_id        = explode(',',$data['search_deliveries']['empId']);
                    $query->WhereIn('employee_id',$emp_id);
            }

        }



        if(isset($data['search_orders'])){
            if(!empty($data['search_orders']['date'])){
                    $toDate        = date('Y-m-d');
                    $incdate = $data['search_orders']['date'];

                    switch ($incdate) {
                        case "TW":
                            $fromDate = config('constants.THIS_WEEK');
                            break;
                        case "LTW":
                            $fromDate = config('constants.LAST_WEEK');
                            break;
                        case "TM":
                            $fromDate = config('constants.MONTH_START');
                            break;
                        case "LTM":
                            $fromDate = config('constants.LAST_MONTH');
                            break;
                        case "LQ":
                            //$fromDate = config('constants.LAST_QUARTER');
                          $current_month = date('m');
                          $current_year = date('Y');

                          if($current_month>=1 && $current_month<=3)
                          {
                            //$start_date = strtotime('1-October-'.($current_year-1));  // timestamp or 1-October Last Year 12:00:00 AM
                            //$end_date = strtotime('1-January-'.$current_year);  // // timestamp or 1-January  12:00:00 AM means end of 31 December Last year
                            $fromDate = $current_year.'-10-01';
                            $toDate   = $current_year.'-12-31';
                          } 
                          else if($current_month>=4 && $current_month<=6)
                          {
                            //$start_date = strtotime('1-January-'.$current_year);  // timestamp or 1-Januray 12:00:00 AM
                            //$end_date = strtotime('1-April-'.$current_year);  // timestamp or 1-April 12:00:00 AM means end of 31 March
                            $fromDate = $current_year.'-01-01';
                            $toDate   = $current_year.'-03-31';
                          }
                          else  if($current_month>=7 && $current_month<=9)
                          {
                            //$start_date = strtotime('1-April-'.$current_year);  // timestamp or 1-April 12:00:00 AM
                            //$end_date = strtotime('1-July-'.$current_year);  // timestamp or 1-July 12:00:00 AM means end of 30 June
                            $fromDate = $current_year.'-04-01';
                            $toDate   = $current_year.'-06-30';
                          }
                          else  if($current_month>=10 && $current_month<=12)
                          {
                            //$start_date = strtotime('1-July-'.$current_year);  // timestamp or 1-July 12:00:00 AM
                            //$end_date = strtotime('1-October-'.$current_year);  // timestamp or 1-October 12:00:00 AM means end of 30 September
                             $fromDate = $current_year.'-07-01';
                             $toDate   = $current_year.'-09-30';
                          }

                            break;
                            default:
                            $fromDate = $toDate;
                    }

                    $query->Where('order_date','>=',$fromDate);
                    $query->Where('order_date','<=',$toDate);
            }
            if($data['search_orders']['orderid'] !=0 || !empty($data['search_orders']['orderid'])){
                    $orderid        = str_ireplace('ORD-', '', $data['search_orders']['orderid']);
                    $query->Where('tbl_orders.quote_id', 'LIKE', '%'.$orderid.'%');
            }
            if($data['search_orders']['status'] !=0){
                    $status = $data['search_orders']['status'];
                    $query->Where('order_status',$status);
            }
           /* if($data['search_orders']['brokerId'] !=0){
                    $broker_id        = $data['search_orders']['brokerId'];
                    $query->Where('tbl_orders.broker_id',$broker_id);
            }*/
            if($data['search_orders']['empId'] !=0){
                    //$emp_id        = explode(',',$data['search_orders']['empId']);
                    $emp_id          = $data['search_orders']['empId'];
                    $query->Where('employee_id',$emp_id);
            }

        }


        if(isset($data['search_proposal'])){

            if($data['search_proposal']['quoteId'] !=''){
                $quoteId        = $data['search_proposal']['quoteId'];
                $query->Where('pk_id', 'LIKE', '%'.$quoteId.'%');
            }
            // if($data['search_proposal']['pricingId'] !=''){
            //     $pricingId        = $data['search_proposal']['pricingId'];
            //     $query->Where('quote_ref', 'LIKE', '%'.$pricingId.'%');
            // }
            if(sizeof($data['search_proposal']['broker_users']) > 0){

                foreach($data['search_proposal']['broker_users'] as $brokerId){
                    $broker[]       = $brokerId->userId;  
                }
                $query->WhereIn('broker_id',$broker);    
              
            }
            if(sizeof($data['search_proposal']['customer_users']) > 0){

                foreach($data['search_proposal']['customer_users'] as $customerId){
                    $customer[]       = $customerId->customerId;  
                }
                $query->WhereIn('customer_id',$customer);

            }
        }

        // if(isset($data['search_proposal_dashboard'])){
        //     if($data['search_proposal_dashboard']['proposalStatus'] !=''){
        //         foreach($data['search_proposal_dashboard']['proposalStatus'] as $status){
        //             $stat[]       = $status["pk_id"];  
        //         }
        //         $query->WhereIn('proposal_status',$stat);
        //     }
        //     if(count($data['search_proposal_dashboard']['employee']) > 0){
        //         foreach($data['search_proposal_dashboard']['employee'] as $employee){
        //             $employ[]       = $employee["item_id"];  

        //         }
        //         $query->WhereIn('employee_id',$employ);
        //     }
           
        //     if($data['search_proposal_dashboard']['proposalType'] !=0){
        //         $proposalStatus        = $data['search_proposal_dashboard']['proposalType'];
        //         $query->Where('proposal_type', 'LIKE', '%'.$proposalType.'%');
        //     }
        // }


        if(isset($data['dates'])){
            $toDate        = $data['dates']['toDate'];
            $fromDate        = $data['dates']['fromDate'];
            $query->Where('order_date','>=',$fromDate);
            $query->Where('order_date','<=',$toDate);
        }



        return $query->get();

    }

    public static function get_extras_name($data){
        
        $extras = DB::table($data['table_name'])
                    ->whereIn('pk_id', $data['where_in']['col_val'])
                    ->get('extra_name');
                    foreach ($extras as $key => $value) {
                        $ids[] = $value->extra_name;
                    }
                   $ext_ids = implode (",", $ids);
                    return $ext_ids;

    }

    public static function get_extras_id($data){
        
        $extras = DB::table($data['table_name'])
                    ->whereIn('extra_name', $data['where_in']['col_val'])
                    ->get('pk_id');
                    foreach ($extras as $key => $value) {
                        $ids[] = $value->pk_id;
                    }
                   $ext_ids = implode (",", $ids);
                    return $ext_ids;

    }

    public static function search_all($data){
            if(isset($data['search']['manufacturer'])){
                $manufacturer = $data['search']['manufacturer'];
            }
            
            $query  =   DB::table($data['table_name']);
            $search = $data['search'];
            $query->where('manufacturer', 'LIKE','%'.$manufacturer.'%');
            
            //$query->where($data['like'], 'LIKE','%'.'6789'.'%');
                /*foreach ($data['like_more'] as $key => $value) 
                {*/

            if(isset($data['search']['fueltype'])){
                $fueltype        = $data['search']['fueltype'];
                $query->orWhere('fuel_type', 'LIKE', '%'.$fueltype.'%');
            }
            if(isset($data['search']['modelyear'])){
                $modelyear        = $data['search']['modelyear'];
                $query->orWhere('model_year', 'LIKE', '%'.$modelyear.'%');
            }
            if(isset($data['search']['transmission'])){
                $transmission        = $data['search']['transmission'];
                $query->orWhere('transmission', 'LIKE', '%'.$transmission.'%');
            }
            if(isset($data['search']['cap_id'])){
                $cap_id        = $data['search']['cap_id'];
                $query->orWhere('cap_id', 'LIKE', '%'.$cap_id.'%');
            }
            
                //}
                     return $query->get();
    }


    public static function insert_data($data){
        return  DB::table($data['table_name'])->insertGetId($data['fields_data']);
    }

    public static function bulkInsert_data($data){
        return  DB::table($data['table_name'])->insert($data['fields_data']);
    }

    public static function update_data($data){

        return  DB::table($data['table_name'])
                ->where($data['where'])
                ->update($data['fields_data']);
    }

    public static function bulkUpdate_data($data){
            $query = DB::table($data['table_name']);
                if(isset($data['where']))
                {
                    $query->where($data['where']);
                }

                if(isset($data['where_in']))
                {
                    $query->whereIn($data['where_in']['col_name'],$data['where_in']['col_val']);
                }

                $result =   $query->update($data['fields_data']);
                return $result;

    }


    public static function delete_data($data){

        $query = DB::table($data['table_name']);
        if(isset($data['where']))
        {
            $query->where($data['where']);
        }

        if(isset($data['where_in']))
        {
            //$query->whereIn($data['where_in']);
            $query->whereIn($data['where_in']['col_name'],$data['where_in']['col_val']);
        }

        $result =   $query->delete();

        return $result;
    }
    public static function DealerBasedBrand($dealer_id){
         $inner_join[]       =   array(
               'table_name1' => 'tbl_dealer_brands' ,  //join table
               'table_name2' => 'tbl_brands' , //start table
               'field_name1' => 'brand_id', //join on
               'field_name2' => 'brand_cap_code' //start with
           );
           $where            =   array('dealer_id'=>$dealer_id);
           $get_details      =   array(
               'table_name'  =>  'tbl_brands',
               'fields_data' =>  array('brand_name','brand_status','tbl_brands.brand_cap_code as brand_id'),
               'where'       => $where,
               'inner_join'  => $inner_join,
           );

           $dealer_brands        =   Common_model::get_data($get_details);


           $brand_list           = array();
           foreach ($dealer_brands as $key => $value) {
                      $dealer_brand = array(
                                        'car_brand' => $value->brand_name,
                                        'car_bid'   => $value->brand_id,
                                        'current_status' => $value->brand_status
                                      );

                array_push($brand_list,$dealer_brand);
           }

           return $brand_list;
  }
  public static function getModels($param)
  {
    $model_details      = array(
                                'table_name'  =>  'tbl_car_model',
                                'fields_data'   =>  array('model_code AS pk_id','model_name')
                              );
       $models =  Common_model::get_data($model_details);
       $modelarr = array();
       foreach ($models as $key => $model) {
              $model_car = array(
                              'pk_id' => $model->pk_id,
                              'brand_name' => $model->model_name
                            );
              array_push($modelarr,$model_car);
       }
       return $modelarr;
  }
    public static function verifyPrices($params)
    {
        $query = "SELECT t1.pk_id, t2.initial_rental, t2.excess_mileage, t2.biz_2years, t2.biz_3years, t2.biz_4years, t2.pl_2years, t2.pl_3years, t2.pl_4years,
            t3.mileage_value 
            FROM  tbl_rate_books t1 LEFT JOIN tbl_stock_pricing t2 ON t1.pk_id=t2.ratebook_id AND t2.is_active=1 
            LEFT JOIN tbl_mileages t3 ON t2.mileage_id = t3.pk_id 
            WHERE t2.ratebook_id='".$params['ratebook_id']."' AND t1.dealer_id='".$params['dealer_id']."'";
        return DB::select(DB::raw($query));
    }
    public static function getStockRatebook($params)
    {
        $query = "SELECT * FROM ((SELECT t1.ratebook_name AS ratebook_name, t1.ratebook_is_camp AS ratebook_is_camp, t1.ratebook_commission AS ratebook_commission, DATE_FORMAT(t1.ratebook_expiry, '%d-%m-%Y') AS ratebook_expiry,t2.cap_id AS cap_id, t2.cap_code AS cap_code, t2.derivative AS derivative, t2.model_year AS model_year, t3.brand_name AS brand_name, t4.model_name AS model_name FROM  tbl_rate_books t1 
            JOIN tbl_dealer_stock t2 ON t1.stock_id=t2.pk_id AND t1.is_factory='2' 
            LEFT JOIN tbl_brands t3 ON t2.brand_id=t3.brand_cap_code 
            LEFT JOIN tbl_car_model t4 ON t2.car_model=t4.model_code 
            WHERE t1.pk_id='".$params['ratebook_id']."' AND t1.dealer_id='".$params['dealer_id']."')
            UNION ALL 
            (SELECT t1.ratebook_name AS ratebook_name, t1.ratebook_is_camp AS ratebook_is_camp, t1.ratebook_commission AS ratebook_commission, DATE_FORMAT(t1.ratebook_expiry, '%d-%m-%Y') AS ratebook_expiry,t2.cap_id AS cap_id, t2.cap_code AS cap_code, t2.derivative_name AS derivative, t2.model_id AS model_year, t3.brand_name AS brand_name, t4.model_name AS model_name FROM  tbl_rate_books t1 
            JOIN tbl_car_derivatives t2 ON t1.stock_id=t2.pk_id AND t1.is_factory='1' 
            LEFT JOIN tbl_brands t3 ON t2.manufacturer_id=t3.brand_cap_code 
            LEFT JOIN tbl_car_model t4 ON t2.model_id=t4.model_code 
            WHERE t1.pk_id='".$params['ratebook_id']."' AND t1.dealer_id='".$params['dealer_id']."')) AS a";
        return DB::select(DB::raw($query));
    }
    public static function getRatebooks($params)
    {
        $search_array = $params['search_data'];
        if($search_array['cap_id_code'] == '')
        {
            $string = "";
            if($search_array['brand'] != 0)
            {
                $string .= "t5.brand_id='".$search_array['brand']."'";
                $string2 .= "t5.manufacturer_id='".$search_array['brand']."'";
            }
            if($search_array['fuel_type'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t5.fuel_type='".$search_array['fuel_type']."'";
                $string2 .= $and."t5.fuel_type='".$search_array['fuel_type']."'";
            }
            if($search_array['model'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t5.car_model='".$search_array['model']."'";
                $string2 .= $and."t5.model_id='".$search_array['model']."'";
            }
            if($search_array['transmission_type'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t5.transmission_type='".$search_array['transmission_type']."'";
                $string2 .= $and."t5.transmission='".$search_array['transmission_type']."'";
            }
            if($search_array['model_year'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t5.model_year='".$search_array['model_year']."'";
                $string2 .= $and."t6.model_introduced='".$search_array['model_year']."'";
            }
            if($search_array['active'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t1.ratebook_is_active='".$search_array['active']."'";
                $string2 .= $and."t1.ratebook_is_active='".$search_array['active']."'";
            }
            if($string != '')
                $search = " AND (".$string.")";
            if($string2 != '')
                $search2 = " AND (".$string2.")";
        }
        else
        {
            $string = "";
            if($search_array['cap_id_code'] != '')
            {
                $search = " AND (t5.cap_id LIKE '".$search_array['cap_id_code']."%' OR t5.cap_code LIKE '".$search_array['cap_id_code']."%')";
                $search2 = " AND (t5.cap_id LIKE '".$search_array['cap_id_code']."%' OR t5.cap_code LIKE '".$search_array['cap_id_code']."%')";
            }
        }
        if($params['count'] == 1)
        {
            $limit_string = '';
        }
        else
        {
            $limit = $params['limit']?$params['limit']:config('constants.PAGE_SIZE');
            $offset = $params['offset']?$params['offset']:0;
            $limit_string = " LIMIT ".$offset.",".$limit;
        }
            $query = "SELECT * FROM ((SELECT t1.pk_id AS pk_id, t1.ratebook_name AS ratebook_name, t1.ratebook_created_date AS ratebook_created_date, t1.publish_date AS publish_date, t1.ratebook_expiry AS ratebook_expiry,t1.ratebook_is_active AS ratebook_is_active,t2.funder_name AS funder_name,t3.first_name AS first_name,count(t4.pk_id) as entries FROM tbl_rate_books t1 
                LEFT JOIN tbl_funder_list t2 ON t2.pk_id=t1.ratebook_funder_id 
            LEFT JOIN tbl_users t3 ON t3.pk_id=t1.created_by 
            LEFT JOIN tbl_stock_pricing t4 ON t4.ratebook_id=t1.pk_id 
            JOIN tbl_dealer_stock t5 ON t1.stock_id=t5.pk_id AND t5.is_trash!='1' AND t1.is_factory='2' 
            WHERE t1.dealer_id='".$params['dealer_id']."' ".$search." GROUP BY t4.ratebook_id)
            UNION ALL 
            (SELECT t1.pk_id AS pk_id, t1.ratebook_name AS ratebook_name, t1.ratebook_created_date AS ratebook_created_date, t1.publish_date AS publish_date, t1.ratebook_expiry AS ratebook_expiry,t1.ratebook_is_active AS ratebook_is_active,t2.funder_name AS funder_name,t3.first_name AS first_name,count(t4.pk_id) as entries FROM tbl_rate_books t1 

            LEFT JOIN tbl_funder_list t2 ON t2.pk_id=t1.ratebook_funder_id 
            LEFT JOIN tbl_users t3 ON t3.pk_id=t1.created_by 
            LEFT JOIN tbl_stock_pricing t4 ON t4.ratebook_id=t1.pk_id 
            JOIN tbl_car_derivatives t5 ON t1.stock_id=t5.pk_id AND t1.is_factory='1' 
            LEFT JOIN tbl_car_model t6 ON  t6.model_introduced=t5.model_id 
            WHERE t1.dealer_id='".$params['dealer_id']."' ".$search2." GROUP BY t4.ratebook_id)) AS b ORDER BY pk_id DESC ".$limit_string;
            return DB::select(DB::raw($query));
    }
    public static function get_prices($params)
    {
        $query = "SELECT t1.pk_id as id, t1.mileage_id, t2.mileage_value as mileage, t1.initial_rental as initial_rental, t1.excess_mileage as excess_mileage, t1.biz_2years as two_years, t1.biz_3years as three_years, t1.biz_4years as four_years, t1.pl_2years as two_years_vat, t1.pl_3years as three_years_vat, t1.pl_4years as four_years_vat, t1.is_active as status FROM tbl_stock_pricing t1 
            LEFT JOIN tbl_mileages t2 ON t2.pk_id=t1.mileage_id WHERE t1.ratebook_id='".$params['ratebook_id']."'";
            return DB::select(DB::raw($query));
    }
    public static function getVehcle($params)
    {
        $search_array = $params['search_data'];
        if($search_array['search_by_cap_id'] == '')
        {
            $string = "";
            if($search_array['manufacturer'] != 0)
            {
                $string .= "t1.brand_id='".$search_array['manufacturer']."'";
            }
            if($search_array['fuel_type'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t1.fuel_type='".$search_array['fuel_type']."'";
            }
            if($search_array['model_type'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t1.car_model='".$search_array['model_type']."'";
            }
            if($search_array['transmission_type'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t1.transmission_type='".$search_array['transmission_type']."'";
            }
            if($search_array['m_year'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t1.model_year='".$search_array['m_year']."'";
            }
            if($string != '')
                $search = " AND (".$string.")";
        }
        else
        {
            $string = "";
            if($search_array['search_by_cap_id'] != '')
            {
                $search = " AND (t1.cap_id='".$search_array['search_by_cap_id']."' OR t1.cap_code='".$search_array['search_by_cap_id']."')";
            }
        }
        if($search != '')
        {
            $search .= " GROUP BY t1.color_id";
            $query = "SELECT t1.pk_id, t1.derivative, t1.transmission_type, t1.model_year, t1.color_id,
                t2.type FROM tbl_dealer_stock t1 
                LEFT JOIN tbl_carfuel_type t2 ON t1.fuel_type=t2.pk_id 
                WHERE t1.is_trash='0' AND t1.dealer_id='".$params['dealer_id']."' ".$search;
            return DB::select(DB::raw($query));
        }
        return false;
    }
    public static function getVehcleFactory($params)
    {
        $search_array = $params['search_data'];
        if($search_array['search_by_cap_id'] == '')
        {
            $string = "";
            /*if($search_array['finance'] != 0)
            {
                $string .= "t1.finance_type='".$search_array['finance']."'";
            }*/
            if($search_array['manufacturer'] != 0)
            {
                $string .= "t1.manufacturer_id='".$search_array['manufacturer']."'";
            }
            if($search_array['fuel_type'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t1.fuel_type='".$search_array['fuel_type']."'";
            }
            if($search_array['model_type'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t1.model_id='".$search_array['model_type']."'";
            }
            if($search_array['transmission_type'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t1.transmission='".$search_array['transmission_type']."'";
            }
            if($search_array['m_year'] != 0)
            {
                $and = $string==''?'':" AND ";
                $string .= $and."t3.model_introduced='".$search_array['m_year']."'";
            }
            if($string != '')
                $search = " AND (".$string.")";
        }
        else
        {
            $string = "";
            if($search_array['search_by_cap_id'] != '')
            {
                $search = " AND (t1.cap_id='".$search_array['search_by_cap_id']."' OR t1.cap_code='".$search_array['search_by_cap_id']."')";
            }
        }
        if($search != '')
        {
            $query = "SELECT t1.pk_id, t1.derivative_name, t1.transmission as transmission_type, t3.model_introduced AS model_year, /*t1.color_id,*/
            t2.type FROM tbl_car_derivatives t1 
            LEFT JOIN tbl_carfuel_type t2 ON t1.fuel_type=t2.pk_id 
            LEFT JOIN tbl_car_model t3 ON t1.model_id=t3.model_code
            WHERE 1=1 ".$search." LIMIT 0, 1";
            return DB::select(DB::raw($query));
        }
        return false;
    }

    public static function getTotalUsers($param){
        $search = '';
        //$param=$params['searchtxt'];
   /*     if($param['brokerName'] != '')
        {
            $search .= " AND t8.company_name LIKE '%".$param['brokerName']."%' ";
        }
        if($param['customerName'] != '')
        {
            $search .= " AND (t5.first_name LIKE '%".$param['customerName']."%'|| t5.middle_name LIKE '%".$param['customerName']."%' || t5.last_name LIKE '%".$param['customerName']."%')";
        }
        if($param['quoteId'] != ''){
            $quote_id = str_replace('prop-', '', strtolower($param['quoteId']));
            $search .= " AND t6.pk_id='".$quote_id."'";
        }*/
        if($param['count'] == 1)
        {
            $limit_string = '';
        }
        else
        {
            $limit = $param['limit']?$param['limit']:5;
            $offset = $param['offset']?$param['offset']:0;
            $limit_string = " LIMIT ".$offset.",".$limit;
        }
        $query = "SELECT t1.id,t1.email,t1.title,t1.name,t1.user_image,t1.status FROM tbl_users t1 
        WHERE t1.status=0 ".$search." ORDER BY t1.id DESC ".$limit_string;
        $result = DB::select(DB::raw($query));
        return  $result;


    }


    public static function filterSearchProposals($params){
        $search = '';
        $param=$params['searchtxt'];
        if($param['brokerName'] != '')
        {
            $search .= " AND t8.company_name LIKE '%".$param['brokerName']."%' ";
        }
        if($param['customerName'] != '')
        {
            $search .= " AND (t5.first_name LIKE '%".$param['customerName']."%'|| t5.middle_name LIKE '%".$param['customerName']."%' || t5.last_name LIKE '%".$param['customerName']."%')";
        }
        if($param['quoteId'] != ''){
           // $search .= " AND t6.pk_id='".$param['quote_id']."'";
            $quote_id = str_replace('prop-', '', strtolower($param['quoteId']));
            $search .= " AND t6.pk_id='".$quote_id."'";
        }
        if($params['count'] == 1)
        {
            $limit_string = '';
        }
        else
        {
            $limit = $param['limit']?$param['limit']:config('constants.PAGE_SIZE');
            $offset = $param['offset']?$param['offset']:0;
            $limit_string = " LIMIT ".$offset.",".$limit;
        }
        $query = "SELECT t1.pk_id AS proposalId,t1.quote_id AS proposalQuote,t1.is_active AS isActive,t3.pk_id AS userId,t3.first_name AS userFirstName,t3.last_name AS userLastName,t4.brand_cap_code AS brandId,t4.brand_name AS brandName,t6.derivative AS Derrivative,t6.funder_id AS funderId,t6.funder_ref AS fundReferenceNo,t7.funder_name AS funderName,t2.model_code AS modelId,t2.model_name AS modelName,t5.first_name AS customerFirstName,t5.middle_name AS customerMiddleName,t5.last_name AS customerLastName,t8.company_name AS brokercompaney FROM tbl_proposals t1 
        LEFT JOIN tbl_quote_enquiries t6 ON t6.pk_id=t1.quote_id
        LEFT JOIN tbl_car_model t2 ON t2.model_code=t6.model_id
        LEFT JOIN tbl_users t3 ON t3.pk_id=t6.broker_id 
        LEFT JOIN tbl_brands t4 ON t4.brand_cap_code=t6.make_id
        LEFT JOIN tbl_customers t5 ON t5.pk_id=t6.customer_id
        LEFT JOIN tbl_funder_list t7 ON t7.pk_id=t6.funder_id
        LEFT JOIN tbl_dealer_groups t8 ON t8.pk_id=t3.dealership_id
        WHERE t1.dealership_id='".$params['dealership_id']."' 
         ".$search." GROUP BY t1.pk_id ORDER BY t1.pk_id DESC ".$limit_string;
        $result = DB::select(DB::raw($query));
        return  $result;
    }
    /*public static function getFromStock($params){

        $query  =   DB::table($params['table_name']);

        if(isset($params['fields_data'])){
            $query->select($params['fields_data']?$params['fields_data']:'*');
        }


        if(isset($params['left_join']))
        {
            foreach($params['left_join'] as $other_table){
                $query->leftJoin($other_table['table_name1'],   $other_table['table_name2'].".".$other_table['field_name2']  , '=' , $other_table['table_name1'].".".$other_table['field_name1']);
            }

        }

          if(isset($params['where']))
        {
            $query->where($params['where']);
        }

        if($params['manufacturer'] !=''){
                    $manufacturer        = $params['manufacturer'];
                    $query->Where('tbl_dealer_stock.brand_id', $manufacturer);
                }

        if($params['derivative'] !=''){
                    $derivative        = $params['derivative'];
                    $query->Where('derivative',$derivative);
                }
         if($params['fuel_type'] !=''){
                    $fuel_type        = $params['fuel_type'];
                    $query->Where('fuel_type',$fuel_type);
                }
         if($params['model'] !=''){
                    $model        = $params['model'];
                    $query->Where('car_model',$model);
                }
        if($params['model_year'] !=''){
                    $model_year        = $params['model_year'];
                    $query->Where('model_year',$model_year);
                }
        if($params['paint'] !=''){
                    $paint        = $params['paint'];
                    $query->Where('color_id',$paint);
                }
         if($params['transmission_id'] !=''){
                    $transmission_id        = $params['transmission_id'];
                    $query->Where('transmission_type',$transmission_id);
                }

        return $query->get();
    }*/
}
