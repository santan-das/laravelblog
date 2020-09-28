<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use mysql_xdevapi\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Exception;
use Config;
use App\Common_model;
use Excel;
use Response;
	if (!function_exists('prepareResponse')) {

		function prepareResponse($errorReason, $success_message, $data) {

			if ($errorReason == "") {
				return response()->json(array('success' => true, 'response_message' => $success_message, 'data' => $data), 200);
			} else {
		       	return response()->json(array('success' => false, 'response_message' => $errorReason, 'data' => $data), 200);//422
		       }
		   }

        }
        if (!function_exists('prepareResponse_401')) {

    function prepareResponse_401() {

      return response()->json(array('success' => true, 'response_message' => "Session Timed out", 'data' => array()), 401);
      }

        }
    if (!function_exists('getBearerToken')) {
        function getBearerToken($header){
                $splitName = explode(' ', $header, 2);
                return $auth_user_token = isset($splitName[1]) ? $splitName[1] : '';
        }
    }

     if (!function_exists('random_str')) {
        function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
        {
            $pieces = [];
            $max = mb_strlen($keyspace, '8bit') - 1;
            for ($i = 0; $i < $length; ++$i) {
                $pieces []= $keyspace[random_int(0, $max)];
            }
            return implode('', $pieces);
        }
    }

     if (!function_exists('stockTemplate')) {
            function stockTemplate(){
                    \Excel::create('file', function($excel)  {
    $getfuels   = $this->getFuelTypes(); 
    $getpaints  = $this->getCarColors();
    $endval = count($getfuels);

     $excel->sheet('colors', function($sheet2) use($getpaints) {

        $excel_cell_start_row = 2; 
        if(count($getpaints)){ 
          $loop_var = 1;
          foreach($getpaints as $getpaint){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Colors');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $getpaint->color_name);  
            $loop_var++;
          }  
        } 

    });


    $excel->sheet('fuel', function($sheet2) use($getfuels) {
        $excel_cell_start_row = 2;  
        if(count($getfuels)){ 
          $loop_var = 1;
          foreach($getfuels as $fuel_type){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Petrol');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $fuel_type->type);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('product', function($sheet) use($endval,$excel) {
       $headingarr = config('constants.EXCEL_HEADER');
       $sheet->appendRow($headingarr); // column names
                              // getting last row number (the one we already filled and setting it to bold
                              $sheet->row($sheet->getHighestRow(), function ($row) {
                                  $row->setFontWeight('bold');
                              });




    $variantsSheet = $sheet->_parent->getSheet(1); // Variants Sheet , "0" is the Index of Variants Sheet
    $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'petrol', $variantsSheet, 'B3:B10' // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 

       )
    );

     $variantsSheet1 = $sheet->_parent->getSheet(0); 
        $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'colors', $variantsSheet1, 'B3:B10' // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
       )
    );

for($row = 2; $row < 1000; $row++) {
       $objValidation = $sheet->getCell('C'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('petrol'); //note this!
       //$sheet->appendRow($objValidation);

       $objValidation = $sheet->getCell('F'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('colors'); //note this!
     }

    });
  })->download("xlsx"); 
            }
     }

    if (!function_exists('templateDownload')) {
        function templateDownload($option){
            if($option == "stock"){
                $file = public_path(). "/download/template.xlsx";
                $sheetname = 'stock';
              }else{
                $file = public_path(). "/download/pricetemplate.xlsx";
                $sheetname = 'pricing';
              }
              
              $headers = array(
                          'Content-Type: application/pdf',
                        );
              $details['file']      = $file;
              $details['sheetname'] = $sheetname;
              $details['headers']   = $headers;

              return $details;
        }
    }

     if (!function_exists('excelStockDataDownload')) {
        function excelStockDataDownload($newdata,$sheetname){
                return Excel::create($sheetname['name'], function($excel) use ($newdata,$sheetname) {
                $excelindex = config('constants.EXCEL_START_INDEX');
                $getfuels    = getFuelTypes(); 
                $getpaints   = getCarColors();
                $getbrands   = getAllBrands();
                $getmodels   = getAllModels();
                $getsites    = getAllSites($sheetname['dealer_id']);
                $getstatus   = config('constants.STOCK_STATUS_CHECK');
                $get_regs    = config('constants.STOCK_PRE_REG');
                $get_trans    = config('constants.TRANSMISSION_TYPE');


                $cmodels     = count($getmodels)+$excelindex;
                $cfuels      = count($getfuels)+$excelindex;
                $cbrands     = count($getbrands)+$excelindex;
                $cpaints     = count($getpaints)+$excelindex;
                $cstatus     = count($getstatus)+$excelindex;
                $creg        = count($get_regs)+$excelindex;
                $ctrans        = count($get_trans)+$excelindex;
                $csites        = count($getsites)+$excelindex;


/*    $excel->sheet('brands', function($sheet2) use($getbrands) {
        $excel_cell_start_row = 2; 
        if(count($getbrands)){ 
          $loop_var = 1;
          foreach($getbrands as $getbrand){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Brands');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $getbrand->brand_name);  
            $loop_var++;
          }  
        } 

    });

     $excel->sheet('colors', function($sheet2) use($getpaints) {

        $excel_cell_start_row = 2; 
        if(count($getpaints)){ 
          $loop_var = 1;
          foreach($getpaints as $getpaint){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Colors');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $getpaint->color_name);  
            $loop_var++;
          }  
        } 

    });*/

/*
    $excel->sheet('fuel', function($sheet2) use($getfuels) {
        $excel_cell_start_row = 2;  
        if(count($getfuels)){ 
          $loop_var = 1;
          foreach($getfuels as $fuel_type){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Petrol');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $fuel_type->type);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('Model', function($sheet2) use($getmodels) {
        $excel_cell_start_row = 2;  
        if(count($getmodels)){ 
          $loop_var = 1;
          foreach($getmodels as $getmodel){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Model');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $getmodel->model_name);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('Status', function($sheet2) use($getstatus) {
        $excel_cell_start_row = 2;  
        if(count($getstatus)){ 
          $loop_var = 1;
          foreach($getstatus as $stat){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Status');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $stat['type']);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('Pre Reg', function($sheet2) use($get_regs) {
        $excel_cell_start_row = 2;  
        if(count($get_regs)){ 
          $loop_var = 1;
          foreach($get_regs as $reg){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'regs');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $reg['type']);  
            $loop_var++;
          }  
        } 

    });

        $excel->sheet('Transmission', function($sheet2) use($get_trans) {
        $excel_cell_start_row = 2;  
        if(count($get_trans)){ 
          $loop_var = 1;
          foreach($get_trans as $trans){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'transmission');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $trans['type']);  
            $loop_var++;
          }  
        } 

    });

        $excel->sheet('Site', function($sheet2) use($getsites) {
        $excel_cell_start_row = 2;  
        if(count($getsites)){ 
          $loop_var = 1;
          foreach($getsites as $sites){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'sites');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $sites->site_name);  
            $loop_var++;
          }  
        } 

    });*/


     $excel->sheet($sheetname['innername'], function($sheet) use($newdata)/*use ($newdata,$sheetname,$cfuels,$excel,$cbrands,$cpaints,$cmodels,$cstatus,$creg,$ctrans,$csites)*/
                        {


   /* $variantsSheet = $sheet->_parent->getSheet(2); // Variants Sheet , "0" is the Index of Variants Sheet
    $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'petrol', $variantsSheet, 'B3:B'.$cfuels // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 

       )
    );

     $variantsSheet1 = $sheet->_parent->getSheet(1); 
        $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'colors', $variantsSheet1, 'B3:B'.$cpaints // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
       )
    );

     $variantsSheet2 = $sheet->_parent->getSheet(0); 
        $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'brands', $variantsSheet2, 'B3:B'.$cbrands // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
       )
    );

    $variantsSheet3 = $sheet->_parent->getSheet(3); 
        $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'models', $variantsSheet3, 'B3:B'.$cmodels // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
       )
    );

        $variantsSheet4 = $sheet->_parent->getSheet(4); 
        $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'status', $variantsSheet4, 'B3:B'.$cstatus // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
       )
    );

        $variantsSheet5 = $sheet->_parent->getSheet(5); 
        $sheet->_parent->addNamedRange(
           new \PHPExcel_NamedRange(
              'regs', $variantsSheet5, 'B3:B'.$creg // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
           )
        );

  $variantsSheet6 = $sheet->_parent->getSheet(6); 
        $sheet->_parent->addNamedRange(
           new \PHPExcel_NamedRange(
              'transmission', $variantsSheet6, 'B3:B'.$ctrans // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
           )
        );

         $variantsSheet7 = $sheet->_parent->getSheet(7); 
        $sheet->_parent->addNamedRange(
           new \PHPExcel_NamedRange(
              'sites', $variantsSheet7, 'B3:B'.$csites // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
           )
        );*/


                            //$sheet->fromArray($newdata);
                            // if($sheetname['option'] == "stock"){
                                $headingarr = config('constants.STOCK_EXCEL_HEADER');
                             /*}else{
                                $headingarr = config('constants.PRICE_EXCEL_HEADER');
                             }*/
                             $sheet->appendRow($headingarr); // column names
                              // getting last row number (the one we already filled and setting it to bold
                              $sheet->row($sheet->getHighestRow(), function ($row) {
                                  $row->setFontWeight('bold');
                                  $row->setBackground('#90EE90');
                              });

                              foreach ($newdata as $stock) {
                                  $sheet->appendRow($stock);
                              }

/*                              for($row = 2; $row < 1000; $row++) {
       $objValidation = $sheet->getCell('G'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('petrol'); //note this!
       //$sheet->appendRow($objValidation);

       $objValidation = $sheet->getCell('J'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('colors'); //note this!

       $objValidation = $sheet->getCell('D'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('brands'); //note this!

       $objValidation = $sheet->getCell('E'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('models'); //note this!

        $objValidation = $sheet->getCell('N'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('status'); //note this!

       $objValidation = $sheet->getCell('L'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('regs'); //note this!


        $objValidation = $sheet->getCell('H'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('transmission'); //note this!

       $objValidation = $sheet->getCell('M'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('sites'); //note this!
     }*/


                        });
                    })->download('xlsx');
        }
    }

if (!function_exists('excelPricingDataDownload')) {
    function excelPricingDataDownload($newdata,$sheetname){
          \Excel::create('pricing-download', function($excel) use ($newdata,$sheetname) {
    $excelindex = config('constants.EXCEL_START_INDEX');
    $getfuels    = getFuelTypes(); 
    $getpaints   = getCarColors();
    $getbrands   = getAllBrands();
    $getmodels   = getAllModels();
    $getmileages = getAllMileages();
    $get_funders = getAllFunders();
    $getstatus   = config('constants.PRICE_STATUS');
    $get_terms   = config('constants.PRICE_TERM');
    $get_campaigns   = config('constants.PRICE_CAMPAIGN');
    $get_finances   = config('constants.PRICE_FINANCE');

    $cmodels     = count($getmodels)+$excelindex;
    $cfuels      = count($getfuels)+$excelindex;
    $cbrands     = count($getbrands)+$excelindex;
    $cpaints     = count($getpaints)+$excelindex;
    $cstatus     = count($getstatus)+$excelindex;
    $cterm       = count($get_terms)+$excelindex;
    $cmileage    = count($getmileages)+$excelindex;
    $ccampaign   = count($get_campaigns)+$excelindex;
    $cfunder     = count($get_funders)+$excelindex;
    $cfinance     = count($get_finances)+$excelindex;

    $excel->sheet('brands', function($sheet2) use($getbrands) {
        $excel_cell_start_row = 2; 
        if(count($getbrands)){ 
          $loop_var = 1;
          foreach($getbrands as $getbrand){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Brands');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $getbrand->brand_name);  
            $loop_var++;
          }  
        } 

    });

     $excel->sheet('colors', function($sheet2) use($getpaints) {

        $excel_cell_start_row = 2; 
        if(count($getpaints)){ 
          $loop_var = 1;
          foreach($getpaints as $getpaint){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Colors');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $getpaint->color_name);  
            $loop_var++;
          }  
        } 

    });


    $excel->sheet('fuel', function($sheet2) use($getfuels) {
        $excel_cell_start_row = 2;  
        if(count($getfuels)){ 
          $loop_var = 1;
          foreach($getfuels as $fuel_type){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Petrol');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $fuel_type->type);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('Model', function($sheet2) use($getmodels) {
        $excel_cell_start_row = 2;  
        if(count($getmodels)){ 
          $loop_var = 1;
          foreach($getmodels as $getmodel){          
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Model');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $getmodel->model_name);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('Status', function($sheet2) use($getstatus) {
        $excel_cell_start_row = 2;  
        if(count($getstatus)){ 
          $loop_var = 1;
          foreach($getstatus as $stat){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'Status');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $stat['type']);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('terms', function($sheet2) use($get_terms) {
        $excel_cell_start_row = 2;  
        if(count($get_terms)){ 
          $loop_var = 1;
          foreach($get_terms as $term){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'terms');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $term['type']);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('mileages', function($sheet2) use($getmileages) {
        $excel_cell_start_row = 2;  
        if(count($getmileages)){ 
          $loop_var = 1;
          foreach($getmileages as $mileage){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'mileages');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $mileage->mileage_value);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('campaigns', function($sheet2) use($get_campaigns) {
        $excel_cell_start_row = 2;  
        if(count($get_campaigns)){ 
          $loop_var = 1;
          foreach($get_campaigns as $campaign){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'campaigns');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $campaign['type']);  
            $loop_var++;
          }  
        } 

    });

    $excel->sheet('funders', function($sheet2) use($get_funders) {
        $excel_cell_start_row = 2;  
        if(count($get_funders)){ 
          $loop_var = 1;
          foreach($get_funders as $funder){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'funders');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $funder->funder_name);  
            $loop_var++;
          }  
        } 

    });

     $excel->sheet('finances', function($sheet2) use($get_finances) {
        $excel_cell_start_row = 2;  
        if(count($get_finances)){ 
          $loop_var = 1;
          foreach($get_finances as $finance){ 
            $excel_cell_pos = $excel_cell_start_row + $loop_var; 
            $sheet2->SetCellValue('B1', 'finances');
            $sheet2->SetCellValue('A'.$excel_cell_pos, $loop_var);  
            $sheet2->SetCellValue('B'.$excel_cell_pos, $finance['type']);  
            $loop_var++;
          }  
        } 

    });


    $excel->sheet('pricing', function($sheet) use($newdata,$cfuels,$excel,$cbrands,$cpaints,$cmodels,$cstatus,$cterm,$cmileage,$ccampaign,$cfunder,$cfinance) {



    $variantsSheet = $sheet->_parent->getSheet(2); // Variants Sheet , "0" is the Index of Variants Sheet
    $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'petrol', $variantsSheet, 'B3:B'.$cfuels // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 

       )
    );

     $variantsSheet1 = $sheet->_parent->getSheet(1); 
        $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'colors', $variantsSheet1, 'B3:B'.$cpaints // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
       )
    );

     $variantsSheet2 = $sheet->_parent->getSheet(0); 
        $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'brands', $variantsSheet2, 'B3:B'.$cbrands // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
       )
    );

    $variantsSheet3 = $sheet->_parent->getSheet(3); 
        $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'models', $variantsSheet3, 'B3:B'.$cmodels // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
       )
    );

        $variantsSheet4 = $sheet->_parent->getSheet(4); 
        $sheet->_parent->addNamedRange(
       new \PHPExcel_NamedRange(
          'status', $variantsSheet4, 'B3:B'.$cstatus // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
       )
    );

        $variantsSheet5 = $sheet->_parent->getSheet(5); 
        $sheet->_parent->addNamedRange(
           new \PHPExcel_NamedRange(
              'terms', $variantsSheet5, 'B3:B'.$cterm // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
           )
        );

      $variantsSheet6 = $sheet->_parent->getSheet(6); 
        $sheet->_parent->addNamedRange(
           new \PHPExcel_NamedRange(
              'mileages', $variantsSheet6, 'B3:B'.$cmileage // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
           )
        );

        $variantsSheet7 = $sheet->_parent->getSheet(7); 
        $sheet->_parent->addNamedRange(
           new \PHPExcel_NamedRange(
              'campaigns', $variantsSheet7, 'B3:B'.$ccampaign // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
           )
        );

        $variantsSheet8 = $sheet->_parent->getSheet(8); 
        $sheet->_parent->addNamedRange(
           new \PHPExcel_NamedRange(
              'funders', $variantsSheet8, 'B3:B'.$cfunder // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
           )
        );

        $variantsSheet9 = $sheet->_parent->getSheet(9); 
        $sheet->_parent->addNamedRange(
           new \PHPExcel_NamedRange(
              'finances', $variantsSheet9, 'B3:B'.$cfinance // You can also replace 'A2:A7' with $variantsSheet ->calculateWorksheetDimension() 
           )
        );


                          $headingarr = config('constants.PRICE_EXCEL_HEADER');

                             $sheet->appendRow($headingarr); // column names
                              // getting last row number (the one we already filled and setting it to bold
                              $sheet->row($sheet->getHighestRow(), function ($row) {
                                  $row->setFontWeight('bold');
                              });

                              foreach ($newdata as $pricing) {
                                  $sheet->appendRow($pricing);
                              }



for($row = 2; $row < 1000; $row++) {

       $objValidation = $sheet->getCell('I'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('terms'); //note this!

       $objValidation = $sheet->getCell('E'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('brands'); //note this!

       $objValidation = $sheet->getCell('F'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('models'); //note this!

       $objValidation = $sheet->getCell('J'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('mileages'); //note this!

         $objValidation = $sheet->getCell('R'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('status'); //note this!

       $objValidation = $sheet->getCell('S'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('campaigns'); //note this!

       $objValidation = $sheet->getCell('U'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('funders'); //note this!

         $objValidation = $sheet->getCell('B'.$row)->getDataValidation();
       $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
       $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
       $objValidation->setAllowBlank(false);
       $objValidation->setShowInputMessage(true);
       $objValidation->setShowErrorMessage(true);
       $objValidation->setShowDropDown(true);
       $objValidation->setErrorTitle('Input error');
       $objValidation->setError('Value is not in list.');
       $objValidation->setPromptTitle('Pick from list');
       $objValidation->setPrompt('Please pick a value from the drop-down list.');
       $objValidation->setFormula1('finances'); //note this!


     }

    });
  })->download("xlsx");
    }
  }

   function getFuelTypes(){
       $fuelarr             =  array(
                                'table_name'  =>  'tbl_carfuel_type',
                                'fields_data'   =>  array('pk_id','type')
                              );
       $fuels =  Common_model::get_data($fuelarr);
       return $fuels;
   }

   function getCarColors(){
       $colorarr             =  array(
                                'table_name'  =>  'tbl_stock_color',
                                'fields_data'   =>  array('pk_id','color_name')
                              );
       $colors =  Common_model::get_data($colorarr);
       return $colors;
   }

   function getAllBrands(){
         $brandarr             =  array(
                                'table_name'  =>  'tbl_brands',
                                'fields_data'   =>  array('pk_id','brand_name')
                              );
         $brands =  Common_model::get_data($brandarr);
         return $brands;
   }

   function getAllStatuses(){

    $statusarray             =  array(
      'table_name'  =>  'tbl_stock_status',
      'fields_data'   =>  array('tbl_stock_status.pk_id','tbl_stock_status.stock_status as type')
    );
    $statuses =  Common_model::get_data($statusarray);
    return $statuses;

   }

   function getAllModels(){
        $modelarr             =  array(
                                'table_name'  =>  'tbl_car_model',
                                'fields_data'   =>  array('pk_id','model_name')
                              );
         $models =  Common_model::get_data($modelarr);
         return $models;
   }

  
   function getAllMileages(){
        $mileagearr             =  array(
                                'table_name'  =>  'tbl_mileages',
                                'fields_data'   =>  array('pk_id','mileage_value')
                              );
         $mileages =  Common_model::get_data($mileagearr);
         return $mileages;
   }

   
    function getAllFunders(){
        $funderarr             =  array(
                                'table_name'  =>  'tbl_funder_list',
                                'fields_data'   =>  array('pk_id','funder_name')
                              );
         $funders =  Common_model::get_data($funderarr);
         return $funders;
   }

   function getAllExtras(){
        $extrasarr             =  array(
                                'table_name'  =>  'tbl_extras',
                                'fields_data'   =>  array('pk_id','extra_name','extra_blp')
                              );
         $extras =  Common_model::get_data($extrasarr);
         return $extras;
   }

   function getAllSites($dealerid){
       $where                = array('dealer_id'=>$dealerid);
       $sitesarr             =  array(
                                'table_name'  =>  'tbl_dealer_sites',
                                'fields_data'   =>  array('pk_id','site_name'),
                                'where'       => $where
                              );
         $sites =  Common_model::get_data($sitesarr);
         return $sites;
   }

   function getModelBasedBrand($brand_id){

      $mids = DB::table('tbl_car_model')
                    ->select('pk_id','model_name as brand_name')
                    ->where('manufacturer_id', $brand_id)
                    ->get();
     return $mids;

   }


   function getModelYear(){
         $myrs = DB::table('tbl_car_model')
                    ->select('model_introduced as model_year')
                    ->distinct()
                    ->get();
         return $myrs;
   }


     if (!function_exists('importExcelData')) {
        function importExcelData($path,$option){
          $data    = Excel::load($path)->get();
          $results = Excel::selectSheetsByIndex(0)->load($path)->get();
          $heading = $results->getHeading();
          $heading = array_map('ucfirst', $heading); // case sensitive first letter as capital
          if($option == "stock"){
                $headarr = config('constants.EXCEL_HEADER');
          }else{
                $headarr = config('constants.PRICE_EXCEL_HEADER');
          }

          sort($heading);
          sort($headarr);
    
          return $datamatch = array("heading"=>$heading,"headarr"=>$headarr,"data"=>$data);

        }
    }

if (!function_exists('getCompanyDetails')) {
	function getCompanyDetails($search){
		$appkey = config('constants.APP_KEY');
		$appurl = config('constants.APP_URL');
   		  
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $appurl.$search);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_USERPWD,$appkey);
        $response = curl_exec($curl);
        if($errno = curl_errno($curl)) {
        $error_message = curl_strerror($errno);
        echo "cURL error ({$errno}):\n {$error_message}";
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $json = json_decode($response, true);

        if(!$response || strlen(trim($response)) == 0)
        {
           //header('Location: http://www.example.com/');
        }

    }
}



if (!function_exists('checkToken')) {
          function checkToken($matcharr){
            return true;
            $splitName = explode(' ', $matcharr['header'], 2);
            $auth_user_token = isset($splitName[1]) ? $splitName[1] : '';

           $table_name      =  config('constants.TBL_USERS');
           $where           =  array('pk_id'=>$matcharr['dealership_id'],'user_auth_token'=>$auth_user_token);
           $fields_data     =  array('user_auth_token');
           $data            =  array('table_name' => $table_name,'where' => $where,'fields_data'=>$fields_data);
           $gettoken        =  Common_model::get_data($data);

            if(isset($gettoken[0]->user_auth_token) && $gettoken[0]->user_auth_token == $auth_user_token ){
                return true;
            }else{
                return false;
            }

      }
}

/*this function is used for getting broker as well as employee name as table is same*/
if (!function_exists('getBrokerName')) {
function getBrokerName($id){
         $name = DB::table('tbl_users')
                 ->select('first_name','last_name','telephone')
                 ->where('pk_id','=',$id)
                 ->first();
        $fullname = $name->first_name.' '.$name->last_name;
        //$details = array('first_name'=>$name->first_name,'last_name' => $name->last_name,'telephone'  => $name->telephone);
       return $fullname;
    }
  }

if (!function_exists('getMileageVal')) {
  function getMileageVal($id){
       $mileage = DB::table('tbl_mileages')
                 ->select('mileage_value')
                 ->where('pk_id','=',$id)
                 ->first();
        return $mileage->mileage_value;
  }
}

if (!function_exists('getBrokerDetails')) {
function getBrokerDetails($id){
         $name = DB::table('tbl_users')
                 ->select('first_name','last_name','telephone')
                 ->where('pk_id','=',$id)
                 ->first();
        $details = array('first_name'=>$name->first_name,'last_name' => $name->last_name,'telephone'  => $name->telephone);
       return $details;
    }
  }



  if (!function_exists('getBrokerCompany')) {
function getBrokerCompany($id){
           $where             =   array('pk_id'=>$id);
           $get_details       =   array(
             'table_name'     =>  'tbl_users',
             'fields_data'    =>  array('dealership_id'),
             'where'          => $where,
           );
          $get_dealerid        =   Common_model::get_data($get_details);
         
           $where             =   array('pk_id'=>$get_dealerid[0]->dealership_id);
           $get_details       =   array(
             'table_name'     =>  'tbl_dealer_groups',
             'fields_data'    =>  array('company_name'),
             'where'          => $where,
           );
          $get_company        =   Common_model::get_data($get_details);


          return $get_company[0]->company_name;
        
    }
  }


  function checkFileType($ext){

      $filetype = array("jpg","jpeg", "png","pdf","doc","docx");
   
       if(!in_array($ext,$filetype)){
                return false;
              }else{
                return true;
              }
    }

      function checkFileBankType($ext){

      $filetype = array("pdf");
   
       if(!in_array($ext,$filetype)){
                return false;
              }else{
                return true;
              }
    }


    


/*for encryption*/
    if (!function_exists('custom_encode'))
    {
        function custom_encode($str)
        {
            $encrypt_obj    =   new Encryption();
            
            $return_str     =   $encrypt_obj->encode($str);
            
            $return_str = strtr($return_str,    array(
                                                        '/' => '~'
                                                )
                                            );
            
            return $return_str;
        }
    }
    
    if (!function_exists('custom_decode'))
    {
        function custom_decode($str)
        {
            $encrypt_obj    =   new Encryption();
            //return $encrypt_obj->decode($str);
            
            $return_str = strtr($str,   array(
                                        '~' => '/'
                                    )
                                );
                                            
            $return_str     =   $encrypt_obj->decode($return_str);                                          
                                            
            
            return $return_str;
            
        }
    }
    if (!function_exists('encode'))
    {
        function encode($string,$decode=null)
        {
            if($string == '')
            {
                return $string;
            }
            
            if($decode == 1)
            {
                $decrypt_string = custom_decode(trim($string));
                if($decrypt_string && !filter_var($string, FILTER_VALIDATE_EMAIL))
                {
                    $return = $decrypt_string;
                    if(filter_var($return, FILTER_VALIDATE_EMAIL))
                    {
                        return $return;
                    }
                    else
                    {
                        return $string;
                    }
                }
                else
                {
                    return $string;
                }
            }
            else
            {
                
                if(filter_var($string, FILTER_VALIDATE_EMAIL))
                {
                    $string = strtolower($string);
                    return custom_encode(trim($string));
                }
                else
                    return $string;
            }
        }
    }
    if (!function_exists('getValue')) 
    {
        function getValue($data, $value) 
        {
            $key = $data['key_id'] != ''?$data['key_id']:"pk_id";
            $text_key = $data['text_key'] != ''?$data['text_key']:"type";
            foreach($data['array'] as $d)
            {
                if($d[$key] == $value)
                    return $d[$text_key];
            }
            return null;
        }
    }


    function getBasePriceDetails($id){
           $where             =   array('cap_id'=>$id);
           $get_details       =   array(
             'table_name'     =>  'tbl_car_derivatives',
             'fields_data'    =>  array('*'),
             'where'          => $where,
           );
          $base_details        =   Common_model::get_data($get_details);
          return $base_details;
         
    }





    //class
    class Encryption {
    
    // var $skey   = "Sparshcommu@2017"; // you can change it
    public function __construct()
    {
        $this->skey = md5('voozo-technology1');
    }
    public  function safe_b64encode($string) {
    
        $data = base64_encode($string);
        $data = str_replace(array('+'),array('-'),$data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-'),array('+'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
    
    public  function encode($value)
    { 
        $value = trim($value);
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext)); 
    }
    
    public function decode($value)
    {
        $value = trim($value);
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

}


/* ALL functions after cap implementation  */
    if (!function_exists('getCapFueltype')) 
    {
        function getCapFueltype(){
             $capfuels = DB::table('tbl_carfuel_type')
                       ->select('type_code as pk_id')
                       ->distinct()
                       ->get();
              return $capfuels;
        }
    }




	









	