<?php
//app/Helpers/AyraHelp.php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\User;
use App\Sample;
use App\QCFORM;
use App\OrderDispatchData;
use App\QCPP;
use Auth;
use App\PurchaseItemRequest;
use App\PurchaseItemGroup;
use App\PurchaseOrders;
use App\OrderStageCount;
use App\OrderStageCountNew;
use App\ClientNote;
use App\POCatalogData;
use App\HPlanDay2;
use App\OPDays;
use App\OPData;
use App\PaymentRec;
use App\QCBOM;
use App\QC_BOM_Purchase;
use App\PurchaseOrderRecieved;

use App\OrderMaster;
use App\LeadDataProcess;
use App\OrderMasterV1;
use Illuminate\Support\Str;
use App\OPDaysBulk;
use App\OPDaysRepeat;
use App\QCBULK_ORDER;
use App\SAP_CHECKLISt;

use App\Client;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class AyraHelp
{

    public static function setClientALLAccessToUser()
    {
        $accessTo = 85;
        $users_arr = DB::table('clients')->where('is_deleted', 0)->get();

        foreach ($users_arr as $key => $rowData) {
            $created_by = $rowData->added_by;
            $clientid = $rowData->id;
            DB::table('users_access')
                ->updateOrInsert(
                    ['access_to' => $accessTo, 'client_id' => $clientid],
                    [
                        'access_by' => $created_by,
                        'access_to' => $accessTo,
                        'created_on' => date('Y-m-d H:i:s'),
                        'created_by' => $created_by,
                        'user_exp_date' => '2020-10-10',
                        'client_id' => $clientid,
                        'access_from' => 1,
                    ]
                );
        }
    }

    public static function getPhoneCallDuplicate($QTYPE, $MOB)
    {


        if ($QTYPE == 'P') {

            $mylead = DB::table('indmt_data')
                ->where('QTYPE', $QTYPE)
                ->where('MOB', $MOB)
                ->first();

            if ($mylead != null) {
                return  0; //mil gaya
            } else {
                return  1; //nahi mila
            }
        } else {
            return  1; //nahi mila
        }
    }
    public static function ClientAccessFullTRANSFER()
    {
        $user_from = 99;
        $user_to = 133;
        $data_arr_data = DB::table('clients')->where('added_by', $user_from)->get();
        //client
        foreach ($data_arr_data as $key => $rowData) {

            DB::table('clients')
                ->where('added_by', $user_from)
                ->update(['client_owner_too' => $user_from]);
        }

        foreach ($data_arr_data as $key => $rowData) {

            DB::table('clients')
                ->where('added_by', $user_from)
                ->update(['added_by' => $user_to]);
        }
        //sample

        $data_arr_data_SAMPLE = DB::table('samples')->where('created_by', $user_from)->get();

        foreach ($data_arr_data_SAMPLE as $key => $rowData) {

            DB::table('samples')
                ->where('created_by', $user_from)
                ->update(['client_owner_too' => $user_from]);
        }
        foreach ($data_arr_data_SAMPLE as $key => $rowData) {

            DB::table('samples')
                ->where('created_by', $user_from)
                ->update(['created_by' => $user_to]);
        }

        //notes


        $data_arr_data_NOTES = DB::table('client_notes')->where('user_id', $user_from)->get();

        foreach ($data_arr_data_NOTES as $key => $rowData) {

            DB::table('client_notes')
                ->where('user_id', $user_from)
                ->update(['client_owner_too' => $user_from]);
        }
        foreach ($data_arr_data_NOTES as $key => $rowData) {

            DB::table('client_notes')
                ->where('user_id', $user_from)
                ->update(['user_id' => $user_to]);
        }
        //orders

        $data_arr_data_ORDERS = DB::table('qc_forms')->where('created_by', $user_from)->get();

        foreach ($data_arr_data_ORDERS as $key => $rowData) {

            DB::table('qc_forms')
                ->where('created_by', $user_from)
                ->update(['client_owner_too' => $user_from]);
        }
        foreach ($data_arr_data_ORDERS as $key => $rowData) {

            DB::table('qc_forms')
                ->where('created_by', $user_from)
                ->update(['created_by' => $user_to]);
        }

        echo "completed";
        die;


        //UPDATE `clients` SET `added_by` = '100' WHERE `clients`.`id` = 1808;
        //UPDATE `samples` SET `created_by` = '100' WHERE `samples`.`client_id` = 1808;
        //UPDATE `client_notes` SET `user_id` = '100' WHERE `client_notes`.`clinet_id` = 1808;
        //UPDATE `qc_forms` SET `created_by` = '100' WHERE `qc_forms`.`client_id` = 1808;




    }
    public static function LeadCorrection2()
    {
        //$data_arr_data = DB::table('temp_lead_sale_statge')->get();

        $data_arr_data = DB::table('temp_lead_sale_statge')
            ->join('indmt_data', 'temp_lead_sale_statge.QUERY_ID', '=', 'indmt_data.QUERY_ID')
            ->where('temp_lead_sale_statge.assign_person', '=', 76)
            //->orderBy('lead_assign.created_at','desc')
            ->select('indmt_data.assign_to')
            ->get();
        echo "<pre>";
        print_r(count($data_arr_data));


        die;

        foreach ($data_arr_data as $key => $rowData) {

            switch ($rowData->curr_stage_name) {
                case 'Assigned':
                    $st = 1;
                    break;
                case 'Qualified':
                    $st = 2;
                    break;
                case 'Sampling':
                    $st = 3;
                    break;
                case 'Client':
                    $st = 4;
                    break;
                case 'Repeat Client':
                    $st = 5;
                    break;
                case 'Lost':
                    $st = 6;
                    break;
            }


            // $data_arr_data_arr = DB::table('st_process_action_4')->where('ticket_id',$rowData->QUERY_ID)->where('stage_id',$st)->get();
            // if(count($data_arr_data_arr)>1){
            //     echo $rowData->QUERY_ID."->";
            //     echo $rowData->assign_person."<br>";

            // }

            $data_arr_data_arr = DB::table('st_process_action_4')->where('ticket_id', $rowData->QUERY_ID)->where('stage_id', $st)->first();

            if ($data_arr_data_arr == null) {

                if ($st == 1) {
                    DB::table('st_process_action_4')->insert(
                        [
                            'process_id' => 4,
                            'stage_id' => $st,
                            'action_on' => 1,
                            'ticket_id' => $rowData->QUERY_ID,
                            'remarks' => 'Scrpted',
                            'assigned_id' => $rowData->assign_person,
                            'updated_by' => 77,
                            'completed_by' => 77,
                            'created_at' => date('Y-m-d H:i:s'),
                            'expected_date' => date('Y-m-d H:i:s'),
                            'expected_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                } else {
                    DB::table('st_process_action_4')->insert(
                        [
                            'process_id' => 4,
                            'stage_id' => 1,
                            'action_on' => 1,
                            'ticket_id' => $rowData->QUERY_ID,
                            'remarks' => 'Scrpted',
                            'assigned_id' => $rowData->assign_person,
                            'updated_by' => 77,
                            'completed_by' => 77,
                            'created_at' => date('Y-m-d H:i:s'),
                            'expected_date' => date('Y-m-d H:i:s'),
                            'expected_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                    DB::table('st_process_action_4')->insert(
                        [
                            'process_id' => 4,
                            'stage_id' => $st,
                            'action_on' => 1,
                            'ticket_id' => $rowData->QUERY_ID,
                            'remarks' => 'Scrpted',
                            'assigned_id' => $rowData->assign_person,
                            'updated_by' => 77,
                            'completed_by' => 77,
                            'created_at' => date('Y-m-d H:i:s'),
                            'expected_date' => date('Y-m-d H:i:s'),
                            'expected_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
        }
    }
    public static function LeadCorrection1()
    {
        $data_arr_data = DB::table('temp_lead_sale_statge')->get();
        foreach ($data_arr_data as $key => $rowData) {

            $data_arr_data = DB::table('lead_assign')->where('QUERY_ID', $rowData->QUERY_ID)->first();
            if ($data_arr_data == null) {
            } else {

                DB::table('lead_assign')
                    ->where('QUERY_ID', $rowData->QUERY_ID)
                    ->where('assign_user_id', $rowData->assign_person)
                    ->update(['temp_del' => 7]);
            }
        }
    }
    public static function LeadCorrection()
    {

        $user_arr = [1, 8, 9, 40, 96, 86, 76, 100, 119, 4, 3, 102, 90, 85, 129, 99];
        foreach ($user_arr as $key => $usrs) {
            // echo $usrs."<br>";


            //$user_id=76;
            $data_arr_data = DB::table('indmt_data')
                ->join('lead_assign', 'indmt_data.QUERY_ID', '=', 'lead_assign.QUERY_ID')
                ->where('lead_assign.assign_user_id', '=', $usrs)
                ->orderBy('lead_assign.created_at', 'desc')
                ->select('indmt_data.*', 'lead_assign.assign_by', 'lead_assign.msg')
                ->get();

            foreach ($data_arr_data as $key => $value) {
                $AssignName = AyraHelp::getLeadAssignUser($value->QUERY_ID);
                //echo $value->QUERY_ID;
                //----------------------------
                if ($value->lead_status == 0 ||  $value->lead_status == 1 || $value->lead_status == 4) {
                    switch ($value->lead_status) {
                        case 0:
                            $st_name = 'Fresh Lead';
                            break;
                        case 1:
                            $st_name = 'Irrelevant';
                            break;
                        case 4:
                            $st_name = 'Unqualified';
                            break;
                    }
                } else {
                    $data_leadAssign = AyraHelp::isAssignLea($value->QUERY_ID);
                    if ($data_leadAssign == 1) {
                        $curr_lead_stage = AyraHelp::getCurrentStageLEAD($value->QUERY_ID);
                        $st_name = $curr_lead_stage->stage_name;
                    } else {
                    }
                }

                //----------------------------

                switch ($st_name) {
                    case 'Assigned':
                        $st = 1;

                        break;
                    case 'Qualified':
                        $st = 2;
                        break;
                    case 'Sampling':
                        $st = 3;
                        break;
                    case 'Client':
                        $st = 4;
                        break;
                    case 'Repeat Client':
                        $st = 5;
                        break;
                    case 'Lost':
                        $st = 6;
                        break;
                }

                if ($st_name == 'Assigned' || $st_name == 'Qualified' || $st_name == 'Sampling' || $st_name == 'Client' || $st_name == 'Repeat Client' ||  $st_name == 'Lost'  ||  $st_name == 'Unqualified') {


                    $data_arr_data_arr = DB::table('temp_lead_sale_statge')->where('QUERY_ID', $value->QUERY_ID)->first();
                    if ($data_arr_data_arr == null) {

                        DB::table('temp_lead_sale_statge')->insert(
                            [
                                'QUERY_ID' => $value->QUERY_ID,
                                'assign_person' => $usrs,
                                'curr_statge_id' => 5,
                                'curr_stage_name' => $st_name,
                                'assigned_name' => $AssignName,
                            ]
                        );
                    }
                }


                // echo "<br>";
            }
        }

        //print_r(count($data_arr_data));




    }
    public static function getOrderForDispatch()
    {

        $qc_arr = QCFORM::where('is_deleted', '!=', 1)->where('dispatch_status', 1)->get();


        return $qc_arr;
    }
    //get duplicateLead
    public static function getDuplicateLead()
    {
        // $qcdata_arr = DB::table('indmt_data')->where('lead_status',0)->get();



        //return $qcdata_arr;
        echo "<pre>";
        $duplicates = DB::table('indmt_data')
            ->select('QUERY_ID', 'MOB')
            ->where('lead_status', 0)
            //->whereNotNull('ENQ_MESSAGE')
            ->whereIn('MOB', function ($q) {
                $q->select('MOB')
                    ->from('indmt_data')
                    ->groupBy('MOB')
                    ->havingRaw('COUNT(*) > 1');
            })->get();
        // echo count($duplicates);


        foreach ($duplicates as $key => $rowData) {
            //print_r($rowData->QUERY_ID);
            //-----------------------
            DB::table('indmt_data')
                ->where('QUERY_ID', $rowData->QUERY_ID)
                ->update(['duplicate_lead_status' => 1]);
            //-----------------------

        }
    }

    //get duplicateLead

    public static function getTodayLeadData()
    {
        $qcdata_arr = DB::table('indmt_data')->whereDate('created_at', '=', date('Y-m-d'))->get();

        foreach ($qcdata_arr as $key => $rowData) {
            echo $rowData->QUERY_ID . "=" . $rowData->SENDERNAME . "=" . $rowData->SENDEREMAIL . "=" . $rowData->PHONE . "<br>";
        }
        //return $qcdata_arr;

    }

    public static function AssignToRUN()
    {

        $FPData = DB::table('indmt_data')
            ->rightjoin('lead_notes', 'indmt_data.QUERY_ID', '=', 'lead_notes.QUERY_ID')
            ->select('indmt_data.*')
            ->get();

        foreach ($FPData as $key => $rowData) {

            $FPDataList = DB::table('lead_assign')->where('QUERY_ID', $rowData->QUERY_ID)->first();
            if ($FPDataList == null) {

                // DB::table('indmt_data')
                // ->where('QUERY_ID',$rowData->QUERY_ID)
                // ->update(['lead_status' =>55]);

            }
        }
    }

    public static function getMyAllClient()
    {
        $client_arr = DB::table('clients')->where('is_deleted', 0)->get();

        foreach ($client_arr as $key => $clientRow) {

            $order_arr = DB::table('qc_forms')->where('client_id', $clientRow->id)->where('is_deleted', 0)->get();
            if (count($order_arr) > 0) {
                $orderHave[] = array(
                    'cid' => $clientRow->id,
                    'firstname' => $clientRow->firstname,
                    'company' => $clientRow->company,
                    'brand' => $clientRow->brand,
                    'sid' => $clientRow->added_by,
                    'sid_name' => AyraHelp::getUser($clientRow->added_by)->name,
                    'order_count' => count($order_arr)

                );
            } else {
                DB::table('clients')
                    ->where('id', $clientRow->id)
                    ->update(['temp_deleted' => 1]);

                //      DB::table('samples')
                // ->where('client_id', $clientRow->id)
                // ->update(['have_order' => 1]);


            }
        }

        //print_r($orderHave);



    }
    public static function getMyAllClient2()
    {
        $client_arr = DB::table('clients')->where('is_deleted', 0)->where('temp_deleted', 1)->get();
        $i = 0;

        foreach ($client_arr as $key => $row) {
            $i++;

            $DATE_TIME_RE = date("d-M-Y H:i:s A", strtotime($row->created_at));
            $QUERY_ID = AyraHelp::getSALE_QUERYID();

            DB::table('client_sales_lead')->insert(
                [

                    'SENDERNAME' => $row->firstname,
                    'SENDEREMAIL' => $row->email,
                    'SUBJECT' => 'Archived Client Lead',
                    'DATE_TIME_RE' => $DATE_TIME_RE,
                    'GLUSR_USR_COMPANYNAME' => $row->company,
                    'GLUSR_USR_BRANDNAME' => $row->brand,
                    'MOB' => $row->phone,
                    'COUNTRY_FLAG' => '',
                    'ENQ_MESSAGE' => 'Archived Client Lead',
                    'ENQ_ADDRESS' => $row->address,
                    'ENQ_CITY' => $row->city,
                    'ENQ_STATE' => '',
                    'PRODUCT_NAME' => '',
                    'COUNTRY_ISO' => $row->country,
                    'EMAIL_ALT' => '',
                    'MOBILE_ALT' => '',
                    'PHONE' => '',
                    'PHONE_ALT' => '',
                    'IM_MEMBER_SINCE' => '',
                    'QUERY_ID' => $QUERY_ID,
                    'QTYPE' => 'OC',
                    'ENQ_CALL_DURATION' => '',
                    'ENQ_RECEIVER_MOB' => '',
                    'data_source' => 'OC_LEAD',
                    'data_source_ID' => 5,
                    'created_at' => $row->created_at,
                    'DATE_TIME_RE_SYS' => $row->created_at,
                    'assign_to' => $row->added_by,
                    'json_api_data' => json_encode($row),

                ]
            );
            ///save to st_process_action_5_mylead
            DB::table('st_process_action_5_mylead')->insert(
                [
                    'process_id' => 5,
                    'stage_id' => 1,
                    'action_on' => 1,
                    'ticket_id' => $QUERY_ID,
                    'remarks' => 'Auto Added ',
                    'assigned_id' => $row->added_by,
                    'updated_by' => $row->added_by,
                    'completed_by' => $row->added_by,
                    'created_at' => $row->created_at,
                    'expected_date' => $row->created_at,
                    'expected_date' => $row->created_at,
                ]
            );
            ///save to st_process_action_5_mylead




        }
        echo $i;

        //print_r($orderHave);



    }

    public static function getSaleInvoiceRequestCount()
    {
        $FPData = DB::table('sales_invoice_request')->where('view_status', 0)->get();


        return count($FPData);
    }

    public static function getMyOrder()
    {
        $qc_arr = QCFORM::where('is_deleted', '!=', 1)->where('dispatch_status', '=', 1)->where('created_by', Auth::user()->id)->get();
        return $qc_arr;
    }
    public static function IsRepeatClientCheck($phone, $email)
    {
        $process_data = DB::table('clients')->where()->get();
    }
    public static function getLeadMissedRun()
    {
        $m = date('m');
        $daycountMonth = \Carbon\Carbon::now()->daysInMonth;

        $api_1 = array();
        $api_2 = array();
        $api_3 = array();
        $api_ori = array();
        for ($i = 1; $i <= $daycountMonth; $i++) {

            if ($i < date('d')) {
                //start 1

                //--------
                $products = DB::table('leadcron_run_log')
                    ->whereMonth('lrun_day_date', $m)
                    ->whereDay('lrun_day_date', $i)
                    ->where('api_details', 'INDMART-8929503295@API_2')
                    ->first();
                if ($products == null) {

                    $input = $i . "-" . $m . "-" . date('Y') . " 19:00:00";
                    $date = strtotime($input);
                    $st = date('d-M-Y H:i:s', $date);
                    $start_date = date('d-M-Y H:i:s', strtotime($st . '-1 day'));
                    $stop_date = date('d-M-Y H:i:s', strtotime($st . '+4 hour'));

                    $dateTimestamp1 = strtotime($stop_date);
                    $dateTimestamp2 = strtotime(date('d-M-Y H:i:s'));

                    if ($dateTimestamp1 >= $dateTimestamp2) {
                    } else {
                        $api_1[] = array(
                            'start_date' => $start_date,
                            'stop_date' => $stop_date,
                            'api' => 'INDMART-8929503295@API_2',
                        );
                    }
                }
                //--------
                //stop 1
                //start 2
                //--------
                $products = DB::table('leadcron_run_log')
                    ->whereMonth('lrun_day_date', $m)
                    ->whereDay('lrun_day_date', $i)
                    ->where('api_details', 'INDMART-9999955922@API_1')
                    ->first();
                if ($products == null) {

                    $input = $i . "-" . $m . "-" . date('Y') . " 19:00:00";
                    $date = strtotime($input);
                    $st = date('d-M-Y H:i:s', $date);
                    $start_date = date('d-M-Y H:i:s', strtotime($st . '-1 day'));
                    $stop_date = date('d-M-Y H:i:s', strtotime($st . '+4 hour'));

                    $dateTimestamp1 = strtotime($stop_date);
                    $dateTimestamp2 = strtotime(date('d-M-Y H:i:s'));

                    if ($dateTimestamp1 >= $dateTimestamp2) {
                    } else {
                        $api_1[] = array(
                            'start_date' => $start_date,
                            'stop_date' => $stop_date,
                            'api' => 'INDMART-9999955922@API_1',
                        );
                    }
                }
                //--------
                //stop 2
                //start 3
                //--------
                $products = DB::table('leadcron_run_log')
                    ->whereMonth('lrun_day_date', $m)
                    ->whereDay('lrun_day_date', $i)
                    ->where('api_details', 'TRADEINDIA-8850185@API_3')
                    ->first();
                if ($products == null) {

                    $input = $i . "-" . $m . "-" . date('Y') . " 19:00:00";
                    $date = strtotime($input);
                    $st = date('d-M-Y H:i:s', $date);
                    $start_date = date('d-M-Y H:i:s', strtotime($st . '-1 day'));
                    $stop_date = date('d-M-Y H:i:s', strtotime($st . '+4 hour'));

                    $dateTimestamp1 = strtotime($stop_date);
                    $dateTimestamp2 = strtotime(date('d-M-Y H:i:s'));

                    if ($dateTimestamp1 >= $dateTimestamp2) {
                    } else {
                        $api_1[] = array(
                            'start_date' => $start_date,
                            'stop_date' => $stop_date,
                            'api' => 'TRADEINDIA-8850185@API_3',
                        );
                    }
                }
                //--------
                //stop 3



            }
        }

        $api_ori = array_merge($api_1, $api_2, $api_3);


        return $api_ori;
    }
    public static function getLeadMissedRunD()
    {

        $m = 2;
        // $process_data = DB::table('leadcron_run_log')->where()->get();
        $api_1 = array();
        $api_2 = array();
        $api_3 = array();
        $api_ori = array();


        for ($i = 1; $i <= 31; $i++) {

            //-------------------------------------
            $products = DB::table('leadcron_run_log')
                ->whereMonth('created_at', $m)
                ->whereDay('created_at', $i)
                ->where('run_status', 0)
                ->where('api_details', 'INDMART-8929503295@API_2')
                ->first();
            if ($products == null) {
                if (isset($products->last_update)) {
                    echo '44';
                } else {
                    $input = $i . "-" . $m . "-" . date('Y') . " 19:00:00";
                    $date = strtotime($input);
                    $st = date('d-M-Y H:i:s', $date);
                    $start_date = date('d-M-Y H:i:s', strtotime($st . '-1 day'));
                    $stop_date = date('d-M-Y H:i:s', strtotime($st . '+4 hour'));

                    $dateTimestamp1 = strtotime($stop_date);
                    $dateTimestamp2 = strtotime(date('d-M-Y H:i:s'));

                    if ($dateTimestamp1 >= $dateTimestamp2) {
                    } else {
                        $api_1[] = array(
                            'start_date' => $start_date,
                            'stop_date' => $stop_date,
                            'api' => 'INDMART-8929503295@API_2',
                        );
                    }
                }
            }

            //-------------------------------------


            //-------------------------------------
            $products = DB::table('leadcron_run_log')
                ->whereMonth('created_at', $m)
                ->whereDay('created_at', $i)
                ->where('run_status', 0)
                ->where('api_details', 'INDMART-9999955922@API_1')
                ->first();
            if ($products == null) {

                if (isset($products->last_update)) {
                    echo '44';
                } else {
                    $input = $i . "-" . $m . "-" . date('Y') . " 19:00:00";
                    $date = strtotime($input);
                    $st = date('d-M-Y H:i:s', $date);
                    $start_date = date('d-M-Y H:i:s', strtotime($st . '-1 day'));
                    $stop_date = date('d-M-Y H:i:s', strtotime($st . '+4 hour'));

                    $dateTimestamp1 = strtotime($stop_date);
                    $dateTimestamp2 = strtotime(date('d-M-Y H:i:s'));

                    if ($dateTimestamp1 >= $dateTimestamp2) {
                    } else {
                        $api_2[] = array(
                            'start_date' => $start_date,
                            'stop_date' => $stop_date,
                            'api' => 'INDMART-9999955922@API_1'
                        );
                    }
                }
            }

            //-------------------------------------

            //-------------------------------------
            $products = DB::table('leadcron_run_log')
                ->whereMonth('created_at', $m)
                ->whereDay('created_at', $i)
                ->where('run_status', 0)
                ->where('api_details', 'TRADEINDIA-8850185@API_3')
                ->first();
            if ($products == null) {
                if (isset($products->last_update)) {
                    echo '44';
                } else {
                    $input = $i . "-" . $m . "-" . date('Y') . " 19:00:00";
                    $date = strtotime($input);
                    $st = date('d-M-Y H:i:s', $date);
                    $start_date = date('d-M-Y H:i:s', strtotime($st . '-1 day'));
                    $stop_date = date('d-M-Y H:i:s', strtotime($st . '+4 hour'));

                    $dateTimestamp1 = strtotime($stop_date);
                    $dateTimestamp2 = strtotime(date('d-M-Y H:i:s'));

                    if ($dateTimestamp1 >= $dateTimestamp2) {
                    } else {
                        $api_3[] = array(
                            'start_date' => $start_date,
                            'stop_date' => $stop_date,
                            'api' => 'TRADEINDIA-8850185@API_3'
                        );
                    }
                }
            }

            //-------------------------------------


        }
        $api_ori = array_merge($api_1, $api_2, $api_3);


        return $api_ori;
    }



    public static function getAllIrrevantLeadDonebyUser($userid)
    {
        $data_arr_data = DB::table('indmt_data')

            //->where('lead_assign.assign_user_id', '=',$userid)
            ->where('indmt_data.lead_status', '=', 1)


            ->get();
        return $data_arr_data;
    }


    public static function getAllUnQlifiedLeadDonebyUser($userid)
    {
        $data_arr_data = DB::table('indmt_data')
            ->join('lead_assign', 'indmt_data.QUERY_ID', '=', 'lead_assign.QUERY_ID')
            ->where('lead_assign.assign_user_id', '=', $userid)
            ->where('indmt_data.lead_status', '=', 4)
            ->orderBy('indmt_data.DATE_TIME_RE_SYS', 'desc')
            ->select('indmt_data.*', 'lead_assign.assign_by', 'lead_assign.msg')
            ->get();
        return $data_arr_data;
    }
    public static function getLeadAssigntCountByUser($userid)
    {
        // $userid=76;
        if ($userid == 76 || $userid == 129 || $userid == 9 || $userid == 8 || $userid == 100 || $userid == 99 || $userid == 3 || $userid == 102 || $userid == 90 || $userid == 2  || $userid == 86 || $userid == 40 || $userid == 86 || $userid == 125 || $userid == 4 || $userid == 119 || $userid == 120 || $userid == 96) {

            //------------------------------

            $data_arr_data = DB::table('indmt_data')
                ->join('lead_assign', 'indmt_data.QUERY_ID', '=', 'lead_assign.QUERY_ID')
                ->where('lead_assign.assign_user_id', '=', $userid)
                // ->where('indmt_data.assign_to', '=',$userid)
                ->orderBy('indmt_data.DATE_TIME_RE_SYS', 'desc')
                ->select('indmt_data.*', 'lead_assign.assign_by', 'lead_assign.msg')
                ->get();




            $st_1 = 0;
            $st_2 = 0;
            $st_3 = 0;
            $st_4 = 0;
            $st_5 = 0;
            $st_6 = 0;


            foreach ($data_arr_data as $key => $rowData) {
                $QUERY_ID = $rowData->QUERY_ID;
                $curr_lead_stage = AyraHelp::getCurrentStageLEAD($QUERY_ID);

                $st_name = $curr_lead_stage->stage_name;
                switch ($curr_lead_stage->stage_id) {
                    case 1:
                        $st_1++;
                        break;
                    case 2:
                        $st_2++;
                        break;
                    case 3:
                        $st_3++;
                        break;
                    case 4:
                        $st_4++;
                        break;
                    case 5:
                        $st_5++;
                        break;
                    case 6:
                        $st_6++;
                        break;
                }
            }

            $mydata = array(
                'st_1' => $st_1,
                'st_2' => $st_2,
                'st_3' => $st_3,
                'st_4' => $st_4,
                'st_5' => $st_5,
                'st_6' => $st_6,
                'st_tot' => ($st_1 + $st_2 + $st_3 + $st_4 + $st_5 + $st_6)

            );
            //------------------------------



        } else {


            $mydata = array(
                'st_1' => '',
                'st_2' => '',
                'st_3' => '',
                'st_4' => '',
                'st_5' => '',
                'st_6' => '',
                'st_tot' => '',

            );
        }


        return $mydata;
    }

    public static function getLeadDistribution()
    {

        $clients_arr = User::where('is_deleted', 0)->whereHas("roles", function ($q) {
            $q->where("name", "SalesUser")->orwhere("name", "Admin")->orwhere("name", "SalesHead")->orwhere("name", "Staff");
        })->get();
        $data = array();


        foreach ($clients_arr as $key => $rowData) {



            if (
                $rowData->id == 88
                || $rowData->id == 108
                || $rowData->id == 77
                || $rowData->id == 78
                || $rowData->id == 83
                || $rowData->id == 84
                || $rowData->id == 85
                || $rowData->id == 87
                || $rowData->id == 89
                || $rowData->id == 91
                || $rowData->id == 93
                || $rowData->id == 95
                || $rowData->id == 98
                || $rowData->id == 101
                || $rowData->id == 130
                || $rowData->id == 131

                || $rowData->id == 132
            ) {
            } else {



                $emp_arr = AyraHelp::getProfilePIC($rowData->id);
                if (!isset($emp_arr->photo)) {
                    $img_photo = asset('local/public/img/avatar.jpg');
                } else {
                    $img_photo = asset('local/public/uploads/photos') . "/" . optional($emp_arr)->photo;
                }


                $data_count_arr = AyraHelp::getLeadAssigntCountByUser($rowData->id);
                $UnQlifieddata_count_arr = AyraHelp::getAllUnQlifiedLeadDonebyUser($rowData->id);
                $Irrevant_count_arr = AyraHelp::getAllIrrevantLeadDonebyUser($rowData->id);


                $data[] = array(
                    'sales_name' => $rowData->name,
                    'uid' => $rowData->id,

                    'profilePic' => $img_photo,
                    'stage_1' => $data_count_arr['st_1'],
                    'stage_2' => $data_count_arr['st_2'],
                    'stage_3' => $data_count_arr['st_3'],
                    'stage_4' => $data_count_arr['st_4'],
                    'stage_5' => $data_count_arr['st_5'],
                    'stage_6' => $data_count_arr['st_6'],
                    'unqli' => count($UnQlifieddata_count_arr),
                    'irvant' => count($Irrevant_count_arr),

                    'stage_totoal' => $data_count_arr['st_tot'],

                );
            }
        }

        return $data;
    }
    public static function isAssignLea($QUERY_ID)
    {
        //$process_data = DB::table('clients')->where()->get();
        $process_data = DB::table('st_process_action_4')->where('action_on', 1)->where('stage_id', 1)->where('ticket_id', $QUERY_ID)->first();



        if ($process_data == null) {
            return 0;
        } else {
            return 1;
        }
    }


    public static function getLeadCountWithNoteID($QUERY_ID)
    {
        $lead_notes_data = DB::table('lead_notes')->where('QUERY_ID', $QUERY_ID)->get();

        $lnote = array();

        if (count($lead_notes_data) > 0) {
            $lnote = array(
                'lcout' => count($lead_notes_data),
                'leadAV' => 1
            );
        } else {
            $lnote = array(
                'lcout' => 0,
                'leadAV' => 0
            );
        }
        return $lnote;
    }

    //bolead_clients_irrelevant
    //bolead_clients_fresh

    public static function getFreshLead()
    {
        $process_data = DB::table('indmt_data')->get();
        // echo count($process_data);
        // die;
        $i = 0;

        foreach ($process_data as $key => $rowData) {
            if ($rowData->lead_status == 0) {
                $i++;
                DB::table('bolead_clients_fresh')->insert(
                    [

                        'QUERY_ID' => $rowData->QUERY_ID,
                        'created_at' => $rowData->created_at,
                        'added_by' => 77,
                        'firstname' => $rowData->SENDERNAME,
                        'email' => $rowData->SENDEREMAIL,
                        'company' => $rowData->GLUSR_USR_COMPANYNAME,
                        'brand' => $rowData->GLUSR_USR_COMPANYNAME,
                        'address' => $rowData->ENQ_ADDRESS,
                        'gstno' => 'NA',
                        'phone' => trim($rowData->MOB),
                        'remarks' => $rowData->remarks,
                        'location' => $rowData->ENQ_CITY,
                        //'country' =>$rowData->COUNTRY_ISO,
                        'source' => $rowData->data_source,
                        'website' => '',
                        'lead_json' => json_encode($rowData),

                    ]
                );
            }
            if ($rowData->lead_status == 1) {
                // DB::table('bolead_clients_irrelevant')->insert(
                //     [

                //         'QUERY_ID' => $rowData->QUERY_ID,
                //         'created_at' => $rowData->created_at,
                //         'added_by' =>77,
                //         'firstname' => $rowData->SENDERNAME,
                //         'email' => $rowData->SENDEREMAIL,
                //         'company' => $rowData->GLUSR_USR_COMPANYNAME,
                //         'brand' => $rowData->GLUSR_USR_COMPANYNAME,
                //         'address' => $rowData->ENQ_ADDRESS,
                //         'gstno' =>'NA',
                //         'phone' =>trim($rowData->MOB),
                //         'remarks' =>$rowData->remarks,
                //         'location' =>$rowData->ENQ_CITY,
                //        // 'country' =>$rowData->COUNTRY_ISO,
                //         'source' =>$rowData->data_source,
                //         'website' =>'',
                //         'lead_json' =>json_encode($rowData),

                //     ]
                // );
            }
        }
        echo $i;
    }
    public static function getActualClientAsNow()
    {
        $process_data = DB::table('clients')->get();

        $i = 0;
        foreach ($process_data as $key => $rowData) {
            $client_data = DB::table('qc_forms')->where('client_id', $rowData->id)->get();
            if (count($client_data) > 0) {

                $i++;
                DB::table('bolead_clients')->insert(
                    [
                        'id' => $rowData->id,
                        'created_at' => $rowData->created_at,
                        'added_by' => $rowData->added_by,
                        'firstname' => $rowData->firstname,
                        'email' => $rowData->email,
                        'company' => $rowData->company,
                        'brand' => $rowData->brand,
                        'address' => $rowData->address,
                        'gstno' => $rowData->gstno,
                        'phone' => trim($rowData->phone),
                        'remarks' => $rowData->remarks,
                        'location' => $rowData->location,
                        'country' => $rowData->country,
                        'source' => $rowData->source,
                        'website' => $rowData->website,

                    ]
                );
            } else {
                DB::table('bolead_clients_sample_only')->insert(
                    [
                        'id' => $rowData->id,
                        'created_at' => $rowData->created_at,
                        'added_by' => $rowData->added_by,
                        'firstname' => $rowData->firstname,
                        'email' => $rowData->email,
                        'company' => $rowData->company,
                        'brand' => $rowData->brand,
                        'address' => $rowData->address,
                        'gstno' => $rowData->gstno,
                        'phone' => trim($rowData->phone),
                        'remarks' => $rowData->remarks,
                        'location' => $rowData->location,
                        'country' => $rowData->country,
                        'source' => $rowData->source,
                        'website' => $rowData->website,

                    ]
                );
            }
        }
        echo $i;
    }

    public static function getClientHaveOrder($client_id)
    {
        $process_data = DB::table('qc_forms')->where('client_id', $client_id)->get();
        if (count($process_data) > 0) {
            $orders = count($process_data);
        } else {
            $orders = 'NA';
        }
        return $orders;
    }
    public static function getTodayBirday()
    {

        $orderData = DB::table('users')
            ->join('hrm_emp', 'users.id', '=', 'hrm_emp.user_id')
            // ->where('users.is_deleted',0)
            ->select('users.*', 'hrm_emp.dob', 'hrm_emp.photo', 'hrm_emp.phone')
            ->get();
        $birday_arr = array();
        foreach ($orderData as $key => $row) {
            $birthdate = $row->dob;
            $time = strtotime($birthdate);
            if (date('m-d') == date('m-d', $time)) {
                $img_photo = asset('local/public/uploads/photos') . "/" . optional($row)->photo;
                $birday_arr[] = array(
                    'user_id' => $row->id,
                    'profile_pic' => $img_photo,
                    'name' => $row->name,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'dob' => $row->dob,
                );
            }
        }

        return $birday_arr;
    }


    public static function getProfilePIC($userid)
    {
        $orderData = DB::table('hrm_emp')->where('user_id', $userid)->first();
        return $orderData;
    }
    public static function getBirthdayList($dayAgo)
    {

        $orderData = DB::table('users')
            ->join('hrm_emp', 'users.id', '=', 'hrm_emp.user_id')
            // ->where('users.is_deleted',0)
            ->select('users.*', 'hrm_emp.dob', 'hrm_emp.photo', 'hrm_emp.phone')
            ->get();
        $birday_arr = array();
        foreach ($orderData as $key => $row) {
            $birthdate = $row->dob;

            $inform_days = date('m-d', strtotime('-10 days', strtotime($birthdate)));

            $today = date('m-d');




            if ($today > $inform_days) {
                $img_photo = asset('local/public/uploads/photos') . "/" . optional($row)->photo;
                $birday_arr[] = array(
                    'user_id' => $row->id,
                    'profile_pic' => $img_photo,
                    'name' => $row->name,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'dob' => $row->dob,
                );
            }
        }

        return $birday_arr;
    }


    public static function getCurrentStageLEAD($QUERY_ID)
    {
        $process_data = DB::table('st_process_action_4')->where('action_on', 1)->where('ticket_id', $QUERY_ID)->latest()->first();

        if ($process_data == null) {
            echo "ZNOW23-" . $QUERY_ID;
            die;
            //$newsid='444';
        } else {
            $newsid = $process_data->stage_id;
            // INSERT INTO `st_process_action_4` (`id`, `process_id`, `stage_id`, `action_on`, `ticket_id`, `dependent_ticket_id`, `ticket_name`, `created_at`, `expected_date`, `remarks`, `attachment_id`, `assigned_id`, `undo_status`, `updated_by`, `created_status`, `completed_by`, `statge_color`)VALUES (NULL, '4', '1', '1', '133825048', NULL, NULL, '2020-01-27 10:28:06', '2020-01-27 10:28:06', 'Assign ', '0', '1', '1', '77', '1', '77', 'completed')

        }

        // $newsid=$process_data->stage_id;


        return $process_dataS = DB::table('st_process_stages')->where('process_id', 4)->where('stage_position', $newsid)->first();
    }
    public static function getCurrentStageMYLEAD($QUERY_ID)
    {
        $process_data = DB::table('st_process_action_5_mylead')->where('action_on', 1)->where('ticket_id', $QUERY_ID)->latest()->first();

        if ($process_data == null) {
            // echo "ZNOW23-" . $QUERY_ID;
            // die;
            $newsid = '1';
        } else {
            $newsid = $process_data->stage_id;
            // INSERT INTO `st_process_action_4` (`id`, `process_id`, `stage_id`, `action_on`, `ticket_id`, `dependent_ticket_id`, `ticket_name`, `created_at`, `expected_date`, `remarks`, `attachment_id`, `assigned_id`, `undo_status`, `updated_by`, `created_status`, `completed_by`, `statge_color`)VALUES (NULL, '4', '1', '1', '133825048', NULL, NULL, '2020-01-27 10:28:06', '2020-01-27 10:28:06', 'Assign ', '0', '1', '1', '77', '1', '77', 'completed')

        }

        // $newsid=$process_data->stage_id;


        return $process_dataS = DB::table('st_process_stages')->where('process_id', 4)->where('stage_position', $newsid)->first();
    }

    public static function getTicketID()
    {
        $length = 9;
        $number = '';
        do {
            for ($i = $length; $i--; $i > 0) {
                $number .= mt_rand(1, 9);
            }
        } while (!empty(DB::table('ticket_list')->where('ticket_id', $number)->first(['ticket_id'])));
        return $number;
    }


    public static function getSponsorID()
    {
        $length = 12;
        $number = '';
        do {
            for ($i = $length; $i--; $i > 0) {
                $number .= mt_rand(0, 9);
            }
        } while (!empty(DB::table('indmt_data')->where('QUERY_ID', $number)->first(['QUERY_ID'])));
        return $number;
    }
    public static function getSALE_QUERYID()
    {
        $length = 10;
        $number = '';
        do {
            for ($i = $length; $i--; $i > 0) {
                $number .= mt_rand(0, 9);
            }
        } while (!empty(DB::table('client_sales_lead')->where('QUERY_ID', $number)->first(['QUERY_ID'])));
        return $number;
    }

    public static function getQID()
    {
        // $length = 5;
        // $number = '';
        // do {
        //     for ($i = $length; $i--; $i > 0) {
        //         $number .= mt_rand(0, 9);
        //     }
        // } while (!empty(DB::table('client_quatation')->where('QID', $number)->first(['QUERY_ID'])));
        // return $number;

        $max_id = DB::table('client_quatation')->max('id') + 1;
        $uname = 'QID';
        $num = $max_id;
        $str_length = 4;
        $sid_code = $uname . substr("00{$num}", -$str_length);
        return $sid_code;

    }



    public static function getAllRepeatOrNewValue()
    {
        $bom_arr = DB::table('qc_forms')->where('is_deleted', 0)->where('order_repeat', 1)->get();
        $no_val = 0;
        foreach ($bom_arr as $key => $row) {
            $oval = ($row->item_sp) * ($row->item_qty);
            $no_val = $no_val + $oval;
        }
        //-----------------------------------
        $bom_arr_2 = DB::table('qc_forms')->where('is_deleted', 0)->where('order_repeat', 2)->get();
        $no_val_1 = 0;
        foreach ($bom_arr_2 as $key => $row_1) {
            $oval_1 = ($row_1->item_sp) * ($row_1->item_qty);
            $no_val_1 = $no_val_1 + $oval_1;
        }



        $data = array(
            'new_order_val' => $no_val,
            'repeat_order_val' => $no_val_1
        );
        return $data;
    }
    public static function runLeadDateUpdate()
    {
        $originalDate = "13-Jan-2020 09:40:33 PM";
        //  echo $newDate = date("y-m-d H:i:s", strtotime($originalDate));
        $bom_arr = DB::table('indmt_data')->get();
        foreach ($bom_arr as $key => $row) {
            $originalDate = $row->DATE_TIME_RE;
            $newDate = date("y-m-d H:i:s", strtotime($originalDate));

            DB::table('indmt_data')
                ->where('QUERY_ID', $row->QUERY_ID)
                ->update(['DATE_TIME_RE_SYS' => $newDate]);
        }
    }
    public static function getLeadAssignUser($leadID)
    {
        $bom_arr = DB::table('lead_assign')->where('QUERY_ID', $leadID)->get();
        $myname = '';
        if (count($bom_arr) > 0) {
            foreach ($bom_arr as $key => $rowData) {

                $myname .= AyraHelp::getUser($rowData->assign_user_id)->name . "<br>";
            }
            return $myname;
        } else {
            return '';
        }
    }

    public static function getINDMArtData()
    {
        $bom_arr = DB::table('indmt_data')->orderBy('id', 'desc')->get();
        return $bom_arr;
    }

    public static function checkArtWorkStated()
    {

        $bom_arr = DB::table('qc_bo_purchaselist')->get();
        foreach ($bom_arr as $key => $row) {

            $arr_data = DB::table('st_process_action')
                ->where('ticket_id', $row->form_id)->where('stage_id', 1)
                ->where('action_status', 1)->first();
            if ($arr_data == null) {
                $bom_arr = DB::table('qc_bo_purchaselist')->where('form_id', $row->form_id)->delete();
            } else {
            }
        }
    }
    public static function NewPurchaseScript3()
    {
        $bom_arr = DB::table('qc_bo_purchaselist_temp')->get();
        $i = 0;
        foreach ($bom_arr as $key => $rowData) {


            $temp_purchase__arr = DB::table('temp_purchase_curr_statge')->where('form_id', $rowData->form_id)->where('m_name', $rowData->material_name)->first();
            if ($temp_purchase__arr == null) {
            } else {

                switch ($temp_purchase__arr->stage_id) {
                    case 1:
                        $stid = 1;
                        break;

                    case 2:
                        $stid = 3;
                        break;

                    case 3:
                        $stid = 3;
                        break;

                    case 4:
                        $stid = 1;
                        break;

                    case 5:
                        $stid = 5;
                        break;

                    case 6:
                        $stid = 5;
                        break;

                    case 7:
                        $stid = 6;
                        break;

                    case 8:
                        $stid = 8;
                        break;
                }

                DB::table('qc_bo_purchaselist_temp')
                    ->where('form_id', $rowData->form_id)->where('material_name', $rowData->material_name)
                    ->update(['status' => $stid]);
                DB::table('temp_purchase_curr_statge')
                    ->where('form_id', $rowData->form_id)->where('m_name', $rowData->material_name)
                    ->update(['status' => 1]);
            }
        }
        echo $i;
    }
    public static function NewPurchaseScript2()
    {
        $order_arr = DB::table('qc_forms')->where('is_deleted', 0)->where('dispatch_status', 1)->get();
        $i = 0;
        foreach ($order_arr as $key => $rowData) {

            $bom_arr = DB::table('qc_forms_bom')
                ->where('form_id', $rowData->form_id)
                ->whereNotNull('m_name')
                ->where('bom_from', '!=', 'From Client')
                ->where('bom_from', '!=', 'N/A')
                ->get();
            foreach ($bom_arr as $key => $rowBOM) {

                $bom_arr = DB::table('qc_bo_purchaselist_temp')
                    ->where('form_id', $rowData->form_id)
                    ->where('material_name', $rowBOM->m_name)
                    ->where('qty', $rowBOM->qty)
                    ->first();
                if ($bom_arr == null) {
                    DB::table('qc_bo_purchaselist_temp')->insert(
                        [
                            'form_id' => $rowData->form_id,
                            'order_id' => $rowData->order_id,
                            'sub_order_index' => $rowData->subOrder,
                            'order_name' => $rowData->brand_name,
                            'order_cat' => $rowBOM->bom_cat,
                            'material_name' => $rowBOM->m_name,
                            'qty' => $rowBOM->qty,
                            'status' => 1,
                            'created_by' => $rowData->created_by,

                        ]
                    );

                    $i++;
                }
            }
        }
        echo $i;
    }

    public static function NewPurchaseScript1()
    {
        $order_arr = DB::table('qc_forms')->where('is_deleted', 0)->where('dispatch_status', 1)->get();
        $i = 0;
        foreach ($order_arr as $key => $rowData) {

            $bom_arr = DB::table('qc_forms_bom')
                ->where('form_id', $rowData->form_id)
                ->whereNotNull('m_name')
                ->where('bom_from', '!=', 'From Client')
                ->where('bom_from', '!=', 'N/A')
                ->get();
            foreach ($bom_arr as $key => $rowBOM) {

                $data = AyraHelp::getPurchaseScriptStageFind($rowBOM->m_name, $rowData->form_id);
                //print_r($data);

                DB::table('temp_purchase_curr_statge')->insert(
                    [
                        'order_id' => "#" . $rowData->order_id . "/" . $rowData->subOrder,
                        'form_id' => $rowData->form_id,
                        'stage_id' => optional($data)->status == NULL ? '1' : $data->status,
                        'm_name' => $rowBOM->m_name,

                    ]
                );


                $i++;
            }


            //$data=AyraHelp::getProcessCurrentStagePurchase(2,$rowData->form_id);



            // DB::table('temp_purchase_curr_statge')->insert(
            //     [
            //         'order_id' =>"#".$rowData->order_id."/".$rowData->subOrder,
            //         'form_id' => $rowData->form_id,
            //         'stage_id' => $data->stage_id,
            //         'stage_name' => $data->stage_name,
            //         //'st_array_json' =>json_encode($define_stage_arr),
            //     ]
            // );




        }
        echo $i;
    }

    public static function NewOrderScript4()
    {
        $order_arr = DB::table('temp_action')->get();

        foreach ($order_arr as $key => $rowData) {

            if ($rowData->stage_id == 1) {
                $order_action = DB::table('st_process_action_temp')->where('ticket_id', $rowData->form_id)->where('stage_id', $rowData->stage_id)->first();
                if ($order_action == null) {

                    // Start :save on stage

                    DB::table('st_process_action_temp')->insert(
                        [
                            'ticket_id' => $rowData->form_id,

                            'process_id' => 1,
                            'stage_id' => 1,
                            'action_on' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'expected_date' => date('Y-m-d H:i:s'),
                            'remarks' => 'Script:Auto s Completed :DP:1',
                            'attachment_id' => 0,
                            'assigned_id' => Auth::user()->id,
                            'undo_status' => 1,
                            'updated_by' => Auth::user()->id,
                            'created_status' => 1,
                            'completed_by' => AyraHelp::getOrderByFormID($rowData->form_id)->created_by,
                            'statge_color' => 'completed',
                            'action_mark' => 0,
                            'action_status' => 0,
                        ]
                    );
                    // Start :save on stage
                    DB::table('temp_action')
                        ->where('form_id', $rowData->form_id)->where('stage_id', 1)
                        ->update(['status' => 1]);
                }
            }

            //new stage
            if ($rowData->stage_id >= 2) {
                //echo $rowData->stage_id."<br>";
                $st_array = json_decode($rowData->st_array_json);

                foreach ($st_array as $key => $rowVal) {

                    if ($rowVal == 1 || $rowVal == 2) {
                    } else {

                        if ($rowVal <= $rowData->stage_id) {

                            if ($rowVal == $rowData->stage_id) {
                                //  echo "-".$rowVal."Current";
                                // Start :save on stage
                                $order_action = DB::table('st_process_action_temp')->where('ticket_id', $rowData->form_id)->where('stage_id', $rowVal)->first();
                                if ($order_action == null) {
                                    DB::table('st_process_action_temp')->insert(
                                        [
                                            'ticket_id' => $rowData->form_id,

                                            'process_id' => 1,
                                            'stage_id' => $rowVal,
                                            'action_on' => 1,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'expected_date' => date('Y-m-d H:i:s'),
                                            'remarks' => 'Script:Auto s Completed :DP:1',
                                            'attachment_id' => 0,
                                            'assigned_id' => Auth::user()->id,
                                            'undo_status' => 1,
                                            'updated_by' => Auth::user()->id,
                                            'created_status' => 1,
                                            'completed_by' => AyraHelp::getOrderByFormID($rowData->form_id)->created_by,
                                            'statge_color' => 'completed',
                                            'action_mark' => 0,
                                            'action_status' => 0,
                                        ]
                                    );
                                    DB::table('temp_action')
                                        ->where('form_id', $rowData->form_id)->where('stage_id', $rowVal)
                                        ->update(['status' => 1]);
                                }

                                // Start :save on stage


                            } else {
                                //  print_r($rowVal);
                                // Start :save on stage
                                // Start :save on stage
                                $order_action = DB::table('st_process_action_temp')->where('ticket_id', $rowData->form_id)->where('stage_id', 1)->first();
                                if ($order_action == null) {
                                    DB::table('st_process_action_temp')->insert(
                                        [
                                            'ticket_id' => $rowData->form_id,

                                            'process_id' => 1,
                                            'stage_id' => 1,
                                            'action_on' => 1,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'expected_date' => date('Y-m-d H:i:s'),
                                            'remarks' => 'Script:Auto s Completed :DP:1',
                                            'attachment_id' => 0,
                                            'assigned_id' => Auth::user()->id,
                                            'undo_status' => 1,
                                            'updated_by' => Auth::user()->id,
                                            'created_status' => 1,
                                            'completed_by' => AyraHelp::getOrderByFormID($rowData->form_id)->created_by,
                                            'statge_color' => 'completed',
                                            'action_mark' => 0,
                                            'action_status' => 1,
                                        ]
                                    );
                                }

                                $order_action = DB::table('st_process_action_temp')->where('ticket_id', $rowData->form_id)->where('stage_id', $rowVal)->first();
                                if ($order_action == null) {
                                    DB::table('st_process_action_temp')->insert(
                                        [
                                            'ticket_id' => $rowData->form_id,

                                            'process_id' => 1,
                                            'stage_id' => $rowVal,
                                            'action_on' => 1,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'expected_date' => date('Y-m-d H:i:s'),
                                            'remarks' => 'Script:Auto s Completed :DP:1',
                                            'attachment_id' => 0,
                                            'assigned_id' => Auth::user()->id,
                                            'undo_status' => 1,
                                            'updated_by' => Auth::user()->id,
                                            'created_status' => 1,
                                            'completed_by' => Auth::user()->id,
                                            'statge_color' => 'completed',
                                            'action_mark' => 0,
                                            'action_status' => 1,
                                        ]
                                    );
                                    DB::table('temp_action')
                                        ->where('form_id', $rowData->form_id)->where('stage_id', $rowVal)
                                        ->update(['status' => 1]);
                                }

                                // Start :save on stage

                            }
                        }
                    }
                }
            }
            //new stage

        }
    }

    public static function NewOrderScript3()
    {
        $order_arr = DB::table('qc_forms')->where('is_deleted', 0)->where('dispatch_status', 1)->get();
        foreach ($order_arr as $key => $rowData) {
            $data = AyraHelp::getProcessCurrentStage(1, $rowData->form_id);

            $form_data = AyraHelp::getQCFormDate($rowData->form_id);

            $orderType = optional($form_data)->order_type;
            $define_stage_arr = array();
            if ($orderType == 'Private Label') {
                if ($form_data->order_repeat == 2) {
                    $define_stage_arr = [1, 2, 0, 0, 0, 0, 7, 8, 9, 10, 0, 12, 13];
                } else {
                    // echo "no repeat";
                    $define_stage_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
                }
            }

            if ($orderType == 'Bulk' || $orderType == 'BULK') {
                if ($form_data->qc_from_bulk == 1) {
                    $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                } else {
                    $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                }
            }


            DB::table('temp_action')->insert(
                [
                    'order_id' => "#" . $rowData->order_id . "/" . $rowData->subOrder,
                    'form_id' => $rowData->form_id,
                    'stage_id' => $data->stage_id,
                    'stage_name' => $data->stage_name,
                    'st_array_json' => json_encode($define_stage_arr),
                ]
            );
        }
    }
    public static function NewOrderScript3OLD()
    {
        $user_id = 4;
        $order_arr = DB::table('qc_forms')->where('is_deleted', 0)->where('dispatch_status', 1)->get();
        $i = 0;
        $j = 0;
        foreach ($order_arr as $key => $rowData) {
            $i++;
            $data = AyraHelp::getProcessCurrentStage(1, $rowData->form_id);
            //echo "<pre>";
            //print_r($data->stage_id);


            if ($data->stage_id == 1) {
                // Start :save on stage
                DB::table('st_process_action_temp')->insert(
                    [
                        'ticket_id' => $rowData->form_id,

                        'process_id' => 1,
                        'stage_id' => 1,
                        'action_on' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'expected_date' => date('Y-m-d H:i:s'),
                        'remarks' => 'Auto s Completed :DP:1',
                        'attachment_id' => 0,
                        'assigned_id' => Auth::user()->id,
                        'undo_status' => 1,
                        'updated_by' => Auth::user()->id,
                        'created_status' => 1,
                        'completed_by' => Auth::user()->id,
                        'statge_color' => 'completed',
                        'action_mark' => 0,
                        'action_status' => 0,
                    ]
                );
                // Start :save on stage



            } else {


                //  echo  $data->stage_id."#".$rowData->order_id."/".$rowData->subOrder."=>".$data->stage_id."=>".$data->stage_name."<br>";
                //ajcode


                $form_data = AyraHelp::getQCFormDate($rowData->form_id);

                $orderType = optional($form_data)->order_type;
                $define_stage_arr = array();
                if ($orderType == 'Private Label') {
                    if ($form_data->order_repeat == 2) {

                        $define_stage_arr = [1, 2, 0, 0, 0, 0, 7, 8, 9, 10, 0, 12, 13];
                        // echo "#".$rowData->order_id."/".$rowData->subOrder."=>".$data->stage_id."=>".$data->stage_name."<br>";


                    } else {
                        // echo "no repeat";
                        $define_stage_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
                        // echo "#".$rowData->order_id."/".$rowData->subOrder."=>".$data->stage_id."=>".$data->stage_name."<br>";
                    }
                }

                if ($orderType == 'Bulk' || $orderType == 'BULK') {
                    if ($form_data->qc_from_bulk == 1) {

                        $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                    } else {
                        $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                    }
                }



                for ($i = $data->stage_id; $i >= 3; $i--) {
                    if ($define_stage_arr[$i - 1] == 0) {
                    } else {
                        $sid = $define_stage_arr[$i - 2];

                        $order_action = DB::table('st_process_action_temp')->where('ticket_id', $rowData->form_id)->where('stage_id', $sid)->first();
                        if ($order_action == null) {
                            // Start :save on stage
                            DB::table('st_process_action_temp')->insert(
                                [
                                    'ticket_id' => $rowData->form_id,

                                    'process_id' => 1,
                                    'stage_id' => $sid,
                                    'action_on' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'expected_date' => date('Y-m-d H:i:s'),
                                    'remarks' => 'Auto s Completed :DP:1',
                                    'attachment_id' => 0,
                                    'assigned_id' => Auth::user()->id,
                                    'undo_status' => 1,
                                    'updated_by' => Auth::user()->id,
                                    'created_status' => 1,
                                    'completed_by' => Auth::user()->id,
                                    'statge_color' => 'completed',
                                    'action_mark' => 0,
                                    'action_status' => 1,
                                ]
                            );
                            // Start :save on stage

                        } else {
                            // DB::table('st_process_action')
                            // ->where('ticket_id',$rowData->form_id)->where('stage_id',$sid)
                            // ->update(['action_status' => 1]);

                        }







                        //break;
                    }
                }



                //insert and update
                //insert and update

                //ajcode



            }
        }
        // echo $i;
        die;
    }
    public static function NewOrderScript2()
    {
        $order_arr = DB::table('qc_forms')->where('is_deleted', 0)->where('dispatch_status', 1)->get();
        $i = 0;
        foreach ($order_arr as $key => $rowData) {
            $order_action = DB::table('st_process_action')->where('ticket_id', $rowData->form_id)->where('stage_id', 1)->first();
            if ($order_action == null) {
                $i++;
                // Start :save on stage
                DB::table('st_process_action')->insert(
                    [
                        'ticket_id' => $rowData->form_id,

                        'process_id' => 1,
                        'stage_id' => 1,
                        'action_on' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'expected_date' => date('Y-m-d H:i:s'),
                        'remarks' => 'Auto s Completed :DP:1',
                        'attachment_id' => 0,
                        'assigned_id' => Auth::user()->id,
                        'undo_status' => 1,
                        'updated_by' => Auth::user()->id,
                        'created_status' => 1,
                        'completed_by' => Auth::user()->id,
                        'statge_color' => 'completed',
                        'action_mark' => 0,
                        'action_status' => 0,
                    ]
                );
                // Start :save on stage

            } else {
            }
        }
        echo $i;
    }
    public static function NewOrderScript()
    {


        $i = 0;
        $j = 0;
        $order_action = DB::table('st_process_action')->get();
        foreach ($order_action as $key => $rowAction) {

            $order_arr = DB::table('qc_forms')->where('form_id', $rowAction->ticket_id)->where('is_deleted', 0)->where('dispatch_status', 1)->first();
            if ($order_arr == null) {
                $i++;
                DB::table('st_process_action')->where('ticket_id', $rowAction->ticket_id)->delete();
            } else {
                $j++;
            }
        }
        echo $i;
        echo "<br>";
        echo $j;
    }


    public static function getBOMQTY($form_id, $m_name)
    {
        $users = DB::table('qc_forms_bom')->where('form_id', $form_id)->where('m_name', $m_name)->first();
        return $users;
    }


    public static function getPurchaseScriptStageFind($m_name, $form_id)
    {
        $users = DB::table('qc_bo_purchaselist')->where('form_id', $form_id)->where('material_name', $m_name)->first();
        return $users;
    }

    public static function GetBomDetail($form_id, $m_name)
    {
        $users = DB::table('qc_forms_bom')->where('form_id', $form_id)->where('m_name', $m_name)->first();
        return $users;
    }

    public static function getFinishPCatDetail($id)
    {
        $users = DB::table('rnd_finish_product_cat')->where('id', $id)->first();
        return $users;
    }

    public static function getFinishProductCatData()
    {
        $users = DB::table('rnd_finish_product_cat')->get();
        return $users;
    }
    public static function getFinishProductSubCatData()
    {
        $users = DB::table('rnd_finish_product_subcat')->get();
        return $users;
    }
    public static function getFinishPSubCatDetail($id)
    {
        $users = DB::table('rnd_finish_product_subcat')->where('id', $id)->first();
        return $users;
    }


    public static function getRNDIngredentList()
    {
        $users = DB::table('rnd_add_ingredient')->get();
        return $users;
    }
    public static function getStayFromOrder($fid)
    {

        $data = AyraHelp::getProcessCurrentStage(1, $fid);

        if ($data->stage_id == 1) {
            //$data=AyraHelp::getQCFormDate($fid);
            $data = AyraHelp::getQCFormDate($fid);
            $date = Carbon::parse($data->created_at);
            $now = Carbon::now();
            $diff = $date->diffInDays($now);
            return "Since " . $diff . " days";
        } else {

            $users = DB::table('st_process_action')

                ->where('process_id', '=', 1)
                ->where('ticket_id', '=', $fid)
                // ->where('stage_id', '=', $data->stage_id)
                ->first();
            if ($users != null) {


                $date = Carbon::parse($users->created_at);
                $now = Carbon::now();
                $diff = $date->diffInDays($now);
                return "Since " . $diff . " days";
            } else {
                return '';
            }




            //print_r($users);


        }
    }


    public static function getMACAddress()
    {
        $ipconfig =   shell_exec("ifconfig -a | grep -Po 'HWaddr \K.*$'");
        // display those informations
        echo $ipconfig;
    }
    //curl --header "DY-X-Authorization: 4ef7f4d3f784d7c9e1029713414cab2a28e7e728" https://ifsc.datayuge.com/api/v1/RATN0000114

    public static function getIngredientCategory()
    {
        $users = DB::table('rnd_ingredient_category')->get();
        return $users;
    }
    public static function getFNDIngredientCategory()
    {
        $users = DB::table('rnd_finish_product_cat')->get();
        return $users;
    }
    public static function getFNDIngredientSubCategory()
    {
        $users = DB::table('rnd_finish_product_subcat')->get();
        return $users;
    }




    public static function getIngredientBrand()
    {
        $users = DB::table('rnd_supplier_brands')->get();
        return $users;
    }
    public static function getIngredientSupplier()
    {
        $users = DB::table('rnd_ingredient_supplier')->get();
        return $users;
    }
    public static function getRNDSupplerDetailsData()
    {
        $users = DB::table('rnd_ingredient_supplier')->distinct('company_name')->get();
        return $users;
    }
    public static function getRNDSupplerDetails($id)
    {
        $users = DB::table('rnd_ingredient_supplier')->where('id', $id)->first();
        return $users;
    }
    public static function getRNDIngredientCatID($id)
    {
        $users = DB::table('rnd_ingredient_category')->where('id', $id)->first();
        return $users;
    }

    public static function getRNDIngredientBrandID($id)
    {
        $users = DB::table('rnd_supplier_brands')->where('id', $id)->first();
        return $users;
    }




    public static function getPurchaseListDataWith($id)
    {
        $users = DB::table('qc_bo_purchaselist')->where('id', $id)->first();
        return $users;
    }
    public static function OldtoNewPurchaseScript()
    {
        echo "<pre>";
        //$datas=QC_BOM_Purchase::where('id', '<=', 1033)->limit(1033)->get();
        $datas = QC_BOM_Purchase::where('id', '>', 3408)->limit(413)->get();
        foreach ($datas as $key => $valRow) {
            $dticketID = $valRow->form_id;
            $stage_id = $valRow->status;
            $ticketID = $valRow->id;
            switch ($stage_id) {
                case 2:
                    $dticketID = $valRow->form_id;
                    AyraHelp::SavePurchaseProcess(1, $ticketID, $dticketID);

                    break;
                case 3:
                    AyraHelp::SavePurchaseProcess(2, $ticketID, $dticketID);
                    break;
                case 4:
                    AyraHelp::SavePurchaseProcess(3, $ticketID, $dticketID);
                    break;
                case 5:
                    AyraHelp::SavePurchaseProcess(4, $ticketID, $dticketID);
                    break;
                case 6:
                    AyraHelp::SavePurchaseProcess(5, $ticketID, $dticketID);
                    break;
                case 7:
                    AyraHelp::SavePurchaseProcess(6, $ticketID, $dticketID);
                    break;
            }
        }
    }
    public static function getBOMImage($pm_code)
    {
        $orderData = DB::table('packaging_options_catalog')->where('poc_code', $pm_code)->first();
        return asset('/local/public/uploads/photos') . "/" . optional($orderData)->img_1;
    }

    public static function SaveOrderProcess($stage_id, $ticket_id)
    {
        DB::table('st_process_action')->insert(
            [
                'process_id' => 1,
                'stage_id' => $stage_id,
                'created_at' => date('Y-m-d'),
                'expected_date' => date('Y-m-d'),
                'assigned_id' => 1,
                'action_on' => 1,
                'completed_by' => 1,
                'ticket_id' => $ticket_id,
                'statge_color' => 'completed'
            ]
        );
    }
    public static function SavePurchaseProcess($stage_id, $ticket_id, $dticketid)
    {

        DB::table('st_process_action_2')->insert(
            [
                'process_id' => 2,
                'stage_id' => $stage_id,
                'created_at' => date('Y-m-d'),
                'expected_date' => date('Y-m-d'),
                'assigned_id' => 1,
                'action_on' => 1,
                'completed_by' => 1,
                'ticket_id' => $ticket_id,
                'dependent_ticket_id' => $dticketid,
                'statge_color' => 'completed'
            ]
        );
    }


    public static function OldtoNewOrderScript()
    {
        $data_arr = QCFORM::where('is_deleted', 0)->where('dispatch_status', 1)->where('artwork_status', 1)->get();
        foreach ($data_arr as $key => $qcROW) {
            echo $qcROW->order_id . "/" . $qcROW->subOrder;
            $orderData = DB::table('order_master')->where('form_id', $qcROW->form_id)->where('action_status', 1)->get();
            foreach ($orderData as $key => $rowData) {
                echo ":" . $rowData->order_statge_id;
                echo "-";
                switch ($rowData->order_statge_id) {
                    case 'ART_WORK_RECIEVED':
                        AyraHelp::SaveOrderProcess(1, $qcROW->form_id);
                        break;
                    case 'ART_WORK_REVIEW':
                        AyraHelp::SaveOrderProcess(3, $qcROW->form_id);
                        break;
                    case 'CLIENT_ART_CONFIRM':
                        AyraHelp::SaveOrderProcess(4, $qcROW->form_id);
                        break;
                    case 'PRINT_SAMPLE':
                        AyraHelp::SaveOrderProcess(5, $qcROW->form_id);
                        break;
                    case 'SAMPLE_ARRROVAL':
                        AyraHelp::SaveOrderProcess(6, $qcROW->form_id);
                        break;
                    case 'PURCHASE_LABEL_BOX':
                        AyraHelp::SaveOrderProcess(7, $qcROW->form_id);
                        break;
                    case 'PRODUCTION':
                        AyraHelp::SaveOrderProcess(8, $qcROW->form_id);
                        AyraHelp::SaveOrderProcess(9, $qcROW->form_id);
                        break;
                    case 'QC_CHECK':
                        AyraHelp::SaveOrderProcess(10, $qcROW->form_id);
                        break;
                    case 'SAMPLE_MADE_APPROVAL':
                        AyraHelp::SaveOrderProcess(11, $qcROW->form_id);
                        break;
                    case 'PACKING_ORDER':
                        AyraHelp::SaveOrderProcess(12, $qcROW->form_id);
                        break;
                    case 'DISPATCH_ORDER':
                        AyraHelp::SaveOrderProcess(13, $qcROW->form_id);
                        break;
                }
            }
            $orderData = DB::table('order_master')->where('form_id', $qcROW->form_id)->where('action_status', 0)->get();

            echo "-<br>";
        }
    }
    public static function OldtoNewOrderScript__latestbkp()
    {
        $data_arr = QCFORM::where('is_deleted', 0)->where('dispatch_status', 1)->where('artwork_status', 1)->get();

        foreach ($data_arr as $key => $row) {
            $qc_data_arr = AyraHelp::getCurrentStageByForMID($row->form_id);


            echo $row->order_id . "/" . $row->subOrder;
            echo '-';
            print_r($qc_data_arr->order_statge_id);
            echo '-';
            print_r($qc_data_arr->form_id);

            switch ($qc_data_arr->order_statge_id) {

                case 'ART_WORK_RECIEVED':
                    $step_code = 1;
                    break;
                case 'PURCHASE_PM':
                    $step_code = 0;
                    break;
                case 'ART_WORK_REVIEW':
                    $step_code = 2;
                    break;
                case 'CLIENT_ART_CONFIRM':
                    $step_code = 3;
                    break;
                case 'PRINT_SAMPLE':
                    $step_code = 5;
                    break;
                case 'SAMPLE_ARRROVAL':
                    $step_code = 6;
                    break;
                case 'PURCHASE_LABEL_BOX':
                    $step_code = 7;
                    break;
                case 'PRODUCTION':
                    $step_code = 8;
                    break;
                case 'QC_CHECK':
                    $step_code = 8;
                    break;
                case 'SAMPLE_MADE_APPROVAL':
                    $step_code = 9;
                    break;
                case 'PACKING_ORDER':
                    $step_code = 10;
                    break;
                case 'DISPATCH_ORDER':
                    $step_code = 11;
                    break;
            }


            echo '-';
            echo $step_code;
            echo "<br>";
            if ($step_code == 0) {
            }
            DB::table('st_process_action')->insert(
                [
                    'process_id' => 1,
                    'stage_id' => $step_code,
                    'created_at' => date('Y-m-d'),
                    'expected_date' => date('Y-m-d'),
                    'assigned_id' => 1,
                    'action_on' => 1,
                    'completed_by' => 1,
                    'ticket_id' => $row->form_id,
                    'statge_color' => 'completed'
                ]
            );
        }
    }
    public static function BKPOldtoNewOrderScript()
    {
        echo "<pre>";
        $data_arr = QCFORM::where('is_deleted', 0)->where('dispatch_status', 1)->where('artwork_status', 1)->get();
        echo count($data_arr);
        die;
        foreach ($data_arr as $key => $rowData) {
            $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
            // print_r($rowData->order_id);
            // echo '-';
            // print_r($rowData->subOrder);
            // print_r($qc_data_arr->order_statge_id);
            // print_r($qc_data_arr->form_id);
            // echo "<pre>";
            switch ($qc_data_arr->order_statge_id) {
                case 'ART_WORK_RECIEVED':
                    $step_code = 1;
                    break;
                case 'PURCHASE_PM':
                    $step_code = 2;
                    break;
                case 'ART_WORK_REVIEW':
                    $step_code = 3;
                    break;
                case 'CLIENT_ART_CONFIRM':
                    $step_code = 4;
                    break;
                case 'PRINT_SAMPLE':
                    $step_code = 5;
                    break;
                case 'SAMPLE_ARRROVAL':
                    $step_code = 6;
                    break;
                case 'PURCHASE_LABEL_BOX':
                    $step_code = 7;
                    break;
                case 'PRODUCTION':
                    $step_code = 8;
                    break;
                case 'QC_CHECK':
                    $step_code = 9;
                    break;
                case 'SAMPLE_MADE_APPROVAL':
                    $step_code = 10;
                    break;
                case 'PACKING_ORDER':
                    $step_code = 11;
                    break;
                case 'DISPATCH_ORDER':
                    $step_code = 12;
                    break;
            }


            switch ($step_code) {
                case '1':
                    $stage_id = 1;
                    break;
                case '2':
                    $stage_id = 2;
                    break;
                case '3':
                    $stage_id = 3;
                    break;
                case '4':
                    $stage_id = 4;
                    break;
                case '5':
                    $stage_id = 5;
                    break;
                case '6':
                    $stage_id = 6;
                    break;
                case '7':
                    $stage_id = 7;
                    break;
                case '8':
                    $stage_id = 9;
                    break;
                case '9':
                    $stage_id = 10;
                    break;
                case '10':
                    $stage_id = 11;
                    break;
                case '11':
                    $stage_id = 12;
                    break;
                case '12':
                    $stage_id = 13;
                    break;
            }



            // print_r($rowData->order_id);
            // echo '-';
            // print_r($rowData->subOrder);
            // echo '-';
            // echo $stage_id;
            // echo "<br>";
            for ($i = $stage_id; $i > 0; $i--) {

                //save data to new stage
                DB::table('st_process_action_1')->insert(
                    [
                        'process_id' => 1,
                        'stage_id' => $i,
                        'created_at' => date('Y-m-d'),
                        'expected_date' => date('Y-m-d'),
                        'assigned_id' => 1,
                        'action_on' => 1,
                        'completed_by' => 1,
                        'ticket_id' => $rowData->form_id,
                        'statge_color' => 'completed'
                    ]
                );
                //save data to new stage

            }
        }
    }

    public static function SetOrderDataToSAPCHECKLIST()
    {
        $datas = QCFORM::where('is_deleted', 0)->get();
        foreach ($datas as $key => $rowData) {

            $ch_data = SAP_CHECKLISt::where('form_id', $rowData->form_id)->first();
            if ($ch_data == null) {
                //--------------------------
                DB::table('sap_checklist')->insert(
                    [

                        'created_by' => Auth::user()->id,
                        'form_id' => $rowData->form_id,
                        'updated_by' => Auth::user()->id,
                        'update_on' => date('Y-m-d H:i:s')
                    ]
                );
                //--------------------------

            }
        }
    }
    //attendance
    public static function getAttenCalulation($rowID)
    {

        $atten_arr = DB::table('emp_attendance_data')->select('atten_data')->where('id', $rowID)->first();
        $atten_data = json_decode($atten_arr->atten_data);

        $day_hour = array();
        $i = 0;
        $avrHour = 0;
        $avrmin = 0;
        $lf = 0;
        foreach ($atten_data as $key => $dayRow) {
            //  print_r($dayRow);
            $contains = Str::contains($dayRow[0], ':');
            if ($contains == 1) {
                $i++;
                //get hour of day
                $today_arr = explode(" ", $dayRow[0]);
                // print_r($today_arr);
                if (empty($today_arr[1])) {
                    $entime = $today_arr[0];
                } else {
                    $entime = $today_arr[1];
                }
                $t1 = '2019-08-01 ' . $today_arr[0] . ":00";
                $t2 = '2019-08-01 ' . $entime . ":00";

                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $t1);
                $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $t2);

                $days = $startDate->diffInDays($endDate);
                $hours = $startDate->copy()->addDays($days)->diffInHours($endDate);
                $minutes = $startDate->copy()->addDays($days)->addHours($hours)->diffInMinutes($endDate);
                $day_hour[] = $hours . ":" . $minutes;
                $avrHour += $hours;
                $avrmin += $minutes;
                if ($hours < 9) {
                    $lf++;
                }
            }
        }

        $data_atten = array(
            'present_day' => $i,
            'day_hour' => $day_hour,
            'avr_hour' => $avrHour,
            'avr_min' => $avrmin,
            'hour_less_count' => $lf,


        );
        return $data_atten;
    }
    public static function getAttenDemo()
    {
        $atten_arr = DB::table('demo_attn')->get();
        $rowCount = count($atten_arr);
        $i = 0;
        foreach ($atten_arr as $key => $row) {
            //  print_r($row->id);
            $i++;
            $nextID = $row->id + 1;
            //print_r($row->attn_value);
            if ($i != $rowCount) {
                $myatten = AyraHelp::getAttenPunch(intval($nextID));
                if ($myatten == 1) {

                    $myatten_data = AyraHelp::getAttenPunchEntryTime($row->id);
                    if ($myatten_data == 1) {
                        $atten_arr = DB::table('demo_attn')->where('id', $row->id)->delete();
                    }
                }
            }
        }
    }
    public static function getAttenPunch($id)
    {

        $atten_arr = DB::table('demo_attn')->where('id', $id)->first();
        if ($atten_arr != null) {
            $data_arr = json_decode($atten_arr->attn_value);
            $contains = Str::contains($data_arr[0], 'ID');
            if ($contains == 1) {
                return 1;
            } else {
                return 0;
            }
        }
    }
    public static function getAttenPunchEntryTime($id)
    {

        $atten_arr = DB::table('demo_attn')->where('id', $id)->first();
        $data_arr = json_decode($atten_arr->attn_value);
        $contains = Str::contains($data_arr[0], 'ID');
        if ($contains == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    public static function setAttenRowBind()
    {

        $atten_arr = DB::table('demo_attn')->get();
        foreach ($atten_arr as $key => $rowVal) {
            $data_arr = json_decode($rowVal->attn_value);
            $contains = Str::contains($data_arr[0], 'ID');
            if ($contains == 1) {
                $emp_data = explode(' ', $data_arr[0]);
                $data = explode(':', $emp_data[0]);
                $dataName = explode(':', $emp_data[2]);

                $attrn_data = AyraHelp::getAllAtttenPuch($rowVal->id);
                $detail[] = array(
                    'emp_id' => intVal($data[1]),
                    'name' => $dataName[1],
                    'atten_data' => $attrn_data,
                    'atten_month' => $rowVal->atten_month,
                    'atten_year' => $rowVal->atten_yr,


                );
            }
        }



        foreach ($detail as $key => $rowData) {
            $users = DB::table('emp_attendance_data')
                ->where('emp_id', $rowData['emp_id'])
                ->where('attn_month', $rowData['atten_month'])
                ->where('atten_yr', $rowData['atten_year'])
                ->first();
            if ($users == null) {

                DB::table('emp_attendance_data')->insert(
                    [
                        'emp_id' => $rowData['emp_id'],
                        'name' => $rowData['name'],
                        'atten_data' => $rowData['atten_data'],
                        'attn_month' => $rowData['atten_month'],
                        'atten_yr' => $rowData['atten_year'],

                    ]
                );
            }
        }
    }
    public static function  getAllAtttenPuch($emp_id)
    {

        $atten_arr = DB::table('demo_attn')->where('id', $emp_id)->get();
        $myattendata = array();
        foreach ($atten_arr as $key => $rowVal) {

            $nextData = DB::table('demo_attn')->where('id', '>', $rowVal->id)->orderBy('id')->get();

            foreach ($nextData as $key => $RowData) {
                $data_arr = json_decode($RowData->attn_value);
                $contains = Str::contains($data_arr[0], 'ID');
                if ($contains != 1) {
                    $myattendata[] = $data_arr;
                } else {
                    // print_r($data_arr);
                    break;
                }
            }
            //return $myattendata;
            $data = array();
            foreach ($myattendata as $key => $finalRow) {

                for ($i = 0; $i < 31; $i++) {
                    //print_r($finalRow[$i]);
                    $data[$i][] = $finalRow[$i];
                }
            }
            return json_encode($data);
            //print_r($data);



            // exit();


        }
    }
    //attendace



    public static function getEMPCODE()
    {
        $max_id = DB::table('hrm_emp')->max('id') + 1;
        $uname = 'EMP';
        $num = $max_id;
        $str_length = 4;
        $sid_code = $uname . substr("00{$num}", -$str_length);
        return $sid_code;
    }
    public static function getEMPDetail($user_id)
    {

        $max_id = DB::table('hrm_emp')->where('user_id', $user_id)->first();
        if ($max_id == null) {
            return false;
        } else {
            return $max_id;
        }
    }


    public static function kpi_matrix_data()
    {
        $process_data = DB::table('kpi_matrix_data')->get();
        return $process_data;
    }


    public static function getStageDataBYpostionID($position_id, $process_id)
    {

        $process_data = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_position', $position_id)->first();
        return $process_data;
    }
    public static function getDepartment()
    {

        $process_data = DB::table('hrms_department')->get();
        return $process_data;
    }
    public static function getKPIBYRole($role)
    {

        $process_data = DB::table('kpi_data')->where('kpi_role', $role)->first();

        return json_decode(optional($process_data)->kpi_detail);
    }
    public static function getKPIBYUser($user_id)
    {

        $process_data = DB::table('kpi_data')->where('user_id', $user_id)->first();
        if ($process_data == null) {
            return array();
        } else {
            return json_decode(optional($process_data)->kpi_detail);
        }
    }


    public static function getDesignation()
    {
        $process_data = DB::table('hrms_designation')->get();
        return $process_data;
    }
    public static function getJobRole()
    {
        $process_data = DB::table('hrms_roles')->get();
        return $process_data;
    }
    public static function getJobRoleByid($id)
    {
        $process_data = DB::table('hrms_roles')->where('id', $id)->first();
        return $process_data;
    }
    public static function getAddressByPincode($pincode)
    {
        $json = file_get_contents('http://postalpincode.in/api/pincode/' . $pincode);
        $obj = json_decode($json);
        return $obj->PostOffice[0];
    }




    public static function getStageProcessCommentHistory($process_id, $ticket_id)
    {

        $process_data = DB::table('st_process_action')->where('process_id', $process_id)->where('ticket_id', $ticket_id)->where('action_on', 0)->where('action_status', 0)->get();

        //return $process_data;
        if (count($process_data) > 0) {
            $i = 0;
            foreach ($process_data as $key => $rowData) {

                $stage_arrData = AyraHelp::getStageDataBYpostionID($rowData->stage_id, $process_id);
                $i++;
                $stage_data[] = array(
                    'id' => $i,
                    'stage_name' => $stage_arrData->stage_name,
                    'remarks' => $rowData->remarks,
                    'completed_on' => date('j-M-y h:i A', strtotime($rowData->created_at)),
                    'completed_by' => AyraHelp::getUser($rowData->completed_by)->name
                );
            }
            return $stage_data;
        } else {
            return array();
        }
    }

    // getLeadStageProcessCompletedHistory_MYLEAD
    public static function getLeadStageProcessCompletedHistory_MYLEAD($process_id, $ticket_id)
    {
        $process_data = DB::table('st_process_action_5_mylead')->where('process_id', $process_id)->where('ticket_id', $ticket_id)->where('action_on', 1)->get();

        if (count($process_data) > 0) {
            $i = 0;
            foreach ($process_data as $key => $rowData) {
                $i++;

                $stage_arrData = AyraHelp::getStageDataBYpostionID($rowData->stage_id, $process_id);

                $stage_data2 = optional($stage_arrData)->stage_name;

                $stage_data[] = array(
                    'id' => $i,
                    'stage_name' => $stage_data2,
                    'msg' => $rowData->remarks,
                    'completed_on' => date('j-M-y h:i A', strtotime($rowData->created_at)),
                    'completed_by' => AyraHelp::getUser($rowData->completed_by)->name
                );
            }
            return $stage_data;
        } else {
            return array();
        }
    }

    // getLeadStageProcessCompletedHistory_MYLEAD

    public static function getLeadStageProcessCompletedHistory($process_id, $ticket_id)
    {
        $process_data = DB::table('st_process_action_4')->where('process_id', $process_id)->where('ticket_id', $ticket_id)->where('action_on', 1)->get();

        if (count($process_data) > 0) {
            $i = 0;
            foreach ($process_data as $key => $rowData) {
                $i++;

                $stage_arrData = AyraHelp::getStageDataBYpostionID($rowData->stage_id, $process_id);

                $stage_data2 = optional($stage_arrData)->stage_name;

                $stage_data[] = array(
                    'id' => $i,
                    'stage_name' => $stage_data2,
                    'msg' => $rowData->remarks,
                    'completed_on' => date('j-M-y h:i A', strtotime($rowData->created_at)),
                    'completed_by' => AyraHelp::getUser($rowData->completed_by)->name
                );
            }
            return $stage_data;
        } else {
            return array();
        }
    }

    public static function getStageProcessCompletedHistory($process_id, $ticket_id)
    {

        if ($process_id == 2) {
            $process_data = DB::table('st_process_action_2')->where('process_id', $process_id)->where('ticket_id', $ticket_id)->where('action_on', 1)->get();
            //return $process_data;
            if (count($process_data) > 0) {
                $i = 0;
                foreach ($process_data as $key => $rowData) {

                    $stage_arrData = AyraHelp::getStageDataBYpostionID($rowData->stage_id, $process_id);

                    $stage_data2 = $stage_arrData->stage_name;
                    if ($stage_arrData->stage_name == 'Ordered') {
                        $data_pur = PurchaseOrderRecieved::where('purchase_id', $ticket_id)->first();
                        $stage_data2 = "Ordered" . " PO No.:" . optional($data_pur)->po_no . '<br>';
                        $stage_data2 .= "ETA :" . date("j-M-y", strtotime(optional($data_pur)->eta)) . '<br>';
                        $stage_data2 .= "Note:" . optional($data_pur)->order_remark . '<br>';
                    }
                    if ($stage_arrData->stage_name == 'Received in Stock ') {
                        $data_pur = PurchaseOrderRecieved::where('purchase_id', $ticket_id)->first();
                        //$stage_data2="Received in Stock".": QTY:".optional($data_pur)->qty_recieved;
                        $stage_data2 = "Received in Stock" . " GRPO No.:" . optional($data_pur)->grpo . '<br>';
                        $stage_data2 .= "QTY :" . optional($data_pur)->qty_recieved . '<br>';
                        $stage_data2 .= "Note:" . optional($data_pur)->rec_remark . '<br>';
                    }
                    //ajay@codemunch.in




                    $i++;
                    $stage_data[] = array(
                        'id' => $i,
                        'stage_name' => $stage_data2,
                        'completed_on' => date('j-M-y h:i A', strtotime($rowData->created_at)),
                        'completed_by' => AyraHelp::getUser($rowData->completed_by)->name
                    );
                }
                return $stage_data;
            } else {
                return array();
            }
        } else {


            $process_data = DB::table('st_process_action')->where('process_id', $process_id)->where('ticket_id', $ticket_id)->where('action_on', 1)->where('action_status', 1)->get();

            //return $process_data;
            if (count($process_data) > 0) {
                $i = 0;
                foreach ($process_data as $key => $rowData) {

                    $stage_arrData = AyraHelp::getStageDataBYpostionID($rowData->stage_id, $process_id);
                    $i++;
                    $stage_data[] = array(
                        'id' => $i,
                        'stage_name' => optional($stage_arrData)->stage_name,
                        'completed_on' => date('j-M-y h:i A', strtotime($rowData->completed_on)),
                        'completed_by' => AyraHelp::getUser($rowData->completed_by)->name
                    );
                }
                return $stage_data;
            } else {
                return array();
            }
        }
    }



    public static function getProcessCurrentStageOrder($process_id, $ticket_id)
    {


        //================================
        $process_data = DB::table('st_process_action')->where('process_id', $process_id)->where('ticket_id', $ticket_id)->latest()->first();

        if ($process_data == null) {
            return $process_data = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_position', 1)->first();
        } else {



            $process_data = DB::table('st_process_stages')->where('stage_position', $process_data->stage_id)->first();

            $newsid = $process_data->stage_id + 1;


            return $process_data = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_position', $newsid)->first();
        }
        //===========================


    }

    public static function getProcessCurrentStagePurchase($process_id, $ticket_id)
    {


        //================================
        // ajcode

        $process_data = DB::table('qc_bo_purchaselist')->where('id', $ticket_id)->first();


        //$process_data = DB::table('st_process_action_2')->where('process_id',$process_id)->where('ticket_id',$ticket_id)->latest()->first();

        if ($process_data == null) {
            return $process_data = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_position', 1)->first();
        } else {





            return $process_data = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_position', $process_data->status)->first();
        }
        //===========================


    }



    public static function getPurchaseStageLIST()
    {
        return DB::table('st_process_stages')->where('process_id', 2)->get();
    }

    public static function getProcessCurrentStage($process_id, $ticket_id)
    {

        $process_data = DB::table('st_process_action')->where('process_id', $process_id)->where('ticket_id', $ticket_id)->where('action_status', 1)->orderBy('stage_id', 'desc')->first();



        if ($process_data == null) {
            return $process_data = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_position', 1)->first();
        } else {
            //ajcode
            //get ordet type
            $form_data = AyraHelp::getQCFormDate($ticket_id);

            $orderType = optional($form_data)->order_type;
            $define_stage_arr = array();
            if ($orderType == 'Private Label') {
                if ($form_data->order_repeat == 2) {

                    $define_stage_arr = [1, 0, 0, 0, 0, 0, 7, 8, 9, 10, 0, 12, 13];
                } else {
                    // echo "no repeat";
                    if ($process_data->action_status == 1) {
                        $define_stage_arr = [1, 0, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
                    } else {
                        $define_stage_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
                    }
                }
            }
            if ($orderType == 'Bulk' || $orderType == 'BULK') {
                if ($form_data->qc_from_bulk == 1) {

                    $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                } else {
                    $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                }
            }





            $process_data = DB::table('st_process_stages')->where('stage_position', $process_data->stage_id)->first();
            //echo $newsid=$process_data->stage_id+1;
            $newsid = optional($process_data)->stage_id;

            if ($newsid == count($define_stage_arr)) {
                $datamy = array('stage_name' => 'completed');
                return (object) $datamy;
            } else {
                foreach ($define_stage_arr as $key => $rowVal) {

                    if ($rowVal > $newsid) {
                        if ($rowVal == 0) {
                        } else {
                            return $process_data = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_position', $rowVal)->first();
                        }
                    }
                }
            }



            // die;
            //get ordet type

            //ajcode
            // $process_data = DB::table('st_process_stages')->where('stage_position',$process_data->stage_id)->first();
            // $newsid=$process_data->stage_id+1;




        }
    }
    public static function getProcessCurrentStage_($process_id, $ticket_id)
    {


        $process_data = DB::table('st_process_action')->where('process_id', $process_id)->where('ticket_id', $ticket_id)->latest()->first();


        if ($process_data == null) {
            return $process_data = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_position', 1)->first();
        } else {
            //ajcode
            //get ordet type
            $form_data = AyraHelp::getQCFormDate($ticket_id);

            $orderType = optional($form_data)->order_type;
            $define_stage_arr = array();
            if ($orderType == 'Private Label') {
                if ($form_data->order_repeat == 2) {

                    $define_stage_arr = [1, 2, 0, 0, 0, 0, 7, 8, 9, 10, 0, 12, 13];
                } else {
                    // echo "no repeat";
                    $define_stage_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
                }
            }
            if ($orderType == 'Bulk' || $orderType == 'BULK') {
                if ($form_data->qc_from_bulk == 1) {

                    $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                } else {
                    $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                }
            }





            $process_data = DB::table('st_process_stages')->where('stage_position', $process_data->stage_id)->first();
            //echo $newsid=$process_data->stage_id+1;
            $newsid = $process_data->stage_id;

            if ($newsid == count($define_stage_arr)) {
                $datamy = array('stage_name' => 'completed');
                return (object) $datamy;
            } else {
                foreach ($define_stage_arr as $key => $rowVal) {

                    if ($rowVal > $newsid) {
                        if ($rowVal == 0) {
                        } else {
                            return $process_data = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_position', $rowVal)->first();
                        }
                    }
                }
            }



            // die;
            //get ordet type

            //ajcode
            // $process_data = DB::table('st_process_stages')->where('stage_position',$process_data->stage_id)->first();
            // $newsid=$process_data->stage_id+1;




        }
    }
    public static function getProcessRowCount($process_id, $ticket_id)
    {

        switch ($process_id) {
            case 1:
                $data = QCFORM::where('form_id', $ticket_id)->first();
                return 1;
                break;
            case 2:
                $data = QC_BOM_Purchase::where('id', $ticket_id)->first();
                $data_arr = QC_BOM_Purchase::where('form_id', $data->form_id)->get();
                return count($data_arr);
                break;
        }
    }
    public static function getStageDependentFlagParent($process_id, $stageid, $ticket_id, $txtRowCount, $dependent_ticket_id)
    {
        $process_arr = DB::table('st_process_stages')->where('process_id', $process_id)->where('process_dependent', 1)->get();
        $mYstageCount = count($process_arr);
        $chdata = $mYstageCount - 1;



        if ($mYstageCount > 0) {


            //yes dependent with parent and check all statage neet to complete of child i.e my
            //then complete parenet stage
            $data_childCount = DB::table('st_process_action')->where('ticket_id', $ticket_id)->where('process_id', $process_id)->where('action_on', 1)->get();

            if (count($data_childCount) == $chdata) {
                // echo count($data_childCount);
                // echo $chdata;
                // die;


                //need to check is row dependent i no then ok if yes
                //find count of row is to be completed then belcode code execute
                $process_row_arr = DB::table('st_process_stages')->where('process_id', $process_id)->where('row_depenent', 1)->get();
                $mYstageRowCount = count($process_arr);




                if ($mYstageRowCount > 0) {
                    $multirowcount = $txtRowCount * $mYstageCount; //21
                    $mYstageRowCount; //7




                    $rowdata = DB::table('st_process_action')->where('dependent_ticket_id', $dependent_ticket_id)->where('process_id', $process_id)->where('action_on', 1)->get();


                    if ($multirowcount == count($rowdata) + 1) {
                        $data_pid_arr = explode("_", $process_arr[0]->process_dependent_stage_code);
                        $pid = $data_pid_arr[2];
                        $stid = $data_pid_arr[3];
                        $data = DB::table('st_process_action')->where('ticket_id', $ticket_id)->where('process_id', $pid)->where('stage_id', $stid)->where('action_on', 1)->first();
                        if ($data == null) {
                            DB::table('st_process_action')->insert(
                                [
                                    'ticket_id' => $dependent_ticket_id,

                                    'process_id' => $pid,
                                    'stage_id' => $stid,
                                    'action_on' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'expected_date' => date('Y-m-d H:i:s'),
                                    'remarks' => 'Auto s Completed :DP:' . $pid,
                                    'attachment_id' => 0,
                                    'assigned_id' => 1,
                                    'undo_status' => 1,
                                    'updated_by' => Auth::user()->id,
                                    'created_status' => 1,
                                    'completed_by' => Auth::user()->id,
                                    'statge_color' => 'completed',
                                ]
                            );
                        }
                        return 1;
                    } else {
                        return 1;
                    }
                } else {
                    $data_pid_arr = explode("_", $process_arr[0]->process_dependent_stage_code);
                    $pid = $data_pid_arr[2];
                    $stid = $data_pid_arr[3];
                    $data = DB::table('st_process_action')->where('ticket_id', $ticket_id)->where('process_id', $pid)->where('stage_id', $stid)->where('action_on', 1)->first();
                    if ($data == null) {
                        DB::table('st_process_action')->insert(
                            [
                                'ticket_id' => $dependent_ticket_id,
                                'process_id' => $pid,
                                'stage_id' => $stid,
                                'action_on' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                                'expected_date' => date('Y-m-d H:i:s'),
                                'remarks' => 'Auto . Completed :DP:' . $pid,
                                'attachment_id' => 0,
                                'assigned_id' => 1,
                                'undo_status' => 1,
                                'updated_by' => Auth::user()->id,
                                'created_status' => 1,
                                'completed_by' => Auth::user()->id,
                                'statge_color' => 'completed',
                            ]
                        );
                    }
                    return 1;
                }
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }

    public static function getStageDependentFlagChild($process_id, $stageid, $ticket_id)
    {
        $process_arr = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_id', $stageid)->where('dependent', 1)->first();

        if ($process_arr == null) {
            return 1;
        } else {

            $dp_process_id = Str::after($process_arr->dependent_with_process_id, 'DP_PID_');
            $all_process_arr = self::getAllStageBYProcessID($dp_process_id);
            foreach ($all_process_arr as $key => $myRow) {
                $data = DB::table('st_process_action')->where('ticket_id', $ticket_id)->where('process_id', $process_id)->where('stage_id', $myRow->stage_id)->where('action_on', 1)->first();
                if ($data == null) {

                    //save data all belongs to that stages
                    if ($process_id == 2) {
                        DB::table('st_process_action')->insert(
                            [
                                'ticket_id' => $ticket_id,
                                'process_id' => $dp_process_id,
                                'stage_id' => $myRow->stage_id,
                                'action_on' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                                'expected_date' => date('Y-m-d H:i:s'),
                                'remarks' => 'Auto Completed :DP:' . $process_id,
                                'attachment_id' => 0,
                                'assigned_id' => 1,
                                'undo_status' => 1,
                                'updated_by' => Auth::user()->id,
                                'created_status' => 1,
                                'completed_by' => Auth::user()->id,
                                'statge_color' => 'completed',
                            ]
                        );
                    }
                    if ($process_id == 1) {
                        $data_arr = QC_BOM_Purchase::where('form_id', $ticket_id)->get();
                        $i = 1;
                        foreach ($data_arr as $key => $RowData) {

                            DB::table('st_process_action')->insert(
                                [
                                    'ticket_id' => $RowData->id,
                                    'process_id' => $dp_process_id,
                                    'stage_id' => $i,
                                    'action_on' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'expected_date' => date('Y-m-d H:i:s'),
                                    'remarks' => 'Auto Completed :DP:' . $process_id,
                                    'attachment_id' => 0,
                                    'assigned_id' => 1,
                                    'undo_status' => 1,
                                    'updated_by' => Auth::user()->id,
                                    'created_status' => 1,
                                    'completed_by' => Auth::user()->id,
                                    'statge_color' => 'completed',
                                ]
                            );
                            $i++;
                        }
                    }
                }
            }
            return 1;
        }
    }
    public static  function getAllStageBYProcessID($process_id)
    {
        $process_arr = DB::table('st_process_stages')->where('process_id', $process_id)->get();
        return $process_arr;
    }

    // getMasterStageResponseMY_LEAD
    public static function getMasterStageResponseMY_LEAD($process_id, $ticket_id, $data, $rowCount, $dependent_ticket)
    {
        $stage_data = AyraHelp::getStagesListMY_LEAD($process_id, $ticket_id, $rowCount, $dependent_ticket);
        $myqc_data = DB::table('client_sales_lead')->where('QUERY_ID', $ticket_id)->first();
        $data_action_done_arr = AyraHelp::getLeadStageProcessCompletedHistory_MYLEAD($process_id, $ticket_id);


        $data_arr = array(
            'stages_info' => $stage_data,
            'itm_qty' => 11,
            'process_data' => $data,
            'created_by' => 44,
            'qc_data' => $myqc_data,
            'BOM_HTML' => '',
            'artwork_start_date' => date('Y-m-d'),
            'stage_action_data' => $data_action_done_arr, //0 not 1 accesabe
            'stage_action_dataComment' => '', //0 not 1 accesabe

        );
        return response()->json($data_arr);
    }

    // getMasterStageResponseMY_LEAD




    public static function getMasterStageResponseLEAD($process_id, $ticket_id, $data, $rowCount, $dependent_ticket)
    {
        $stage_data = AyraHelp::getStagesListLEAD($process_id, $ticket_id, $rowCount, $dependent_ticket);
        $myqc_data = DB::table('indmt_data')->where('QUERY_ID', $ticket_id)->first();
        $data_action_done_arr = AyraHelp::getLeadStageProcessCompletedHistory($process_id, $ticket_id);


        $data_arr = array(
            'stages_info' => $stage_data,
            'itm_qty' => 11,
            'process_data' => $data,
            'created_by' => 44,
            'qc_data' => $myqc_data,
            'BOM_HTML' => '',
            'artwork_start_date' => date('Y-m-d'),
            'stage_action_data' => $data_action_done_arr, //0 not 1 accesabe
            'stage_action_dataComment' => '', //0 not 1 accesabe

        );
        return response()->json($data_arr);
    }


    public static function getMasterStageResponseRND($process_id, $ticket_id, $data, $rowCount, $dependent_ticket)
    {
        $stage_data = AyraHelp::getStagesListRND($process_id, $ticket_id, $rowCount, $dependent_ticket);
        $myqc_data = DB::table('rnd_new_product_development')->where('id', $ticket_id)->first();

        $data_arr = array(
            'stages_info' => $stage_data,
            'itm_qty' => 11,
            'process_data' => $data,
            'created_by' => 44,
            'qc_data' => $myqc_data,
            'BOM_HTML' => '',
            'artwork_start_date' => date('Y-m-d'),
            'stage_action_data' => '', //0 not 1 accesabe
            'stage_action_dataComment' => '', //0 not 1 accesabe

        );
        return response()->json($data_arr);
    }

    public static function getMasterStageResponse($process_id, $ticket_id, $data, $rowCount, $dependent_ticket)
    {
        $stage_data = AyraHelp::getStagesList($process_id, $ticket_id, $rowCount, $dependent_ticket);
        $myqc_data = AyraHelp::getQCFormDate($data->form_id);

        $data_action_done_arr = AyraHelp::getStageProcessCompletedHistory($process_id, $ticket_id);
        $data_actionComment_done_arr = AyraHelp::getStageProcessCommentHistory($process_id, $ticket_id);



        //get purchase details with status
        $bom_arrs = QC_BOM_Purchase::where('form_id', $ticket_id)->get();

        $BO_HTML = '';
        $BO_HTML .= '<div class="m-section">

  <div class="m-section__content">
    <table class="table table-sm m-table m-table--head-bg-brand">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Item Name</th>
          <th>Quantity</th>
          <th>Stage Name</th>
          <th>Last Updates</th>
          <th>Updated by</th>
        </tr>
      </thead>
      <tbody>';
        $i = 0;
        foreach ($bom_arrs as $key => $myRow) {
            $item_name = $myRow->material_name;
            $qty = $myRow->qty;
            $i++;
            if (isset($myRow->update_statge_on)) {
                $update_statge_on = date("l d,M Y h:iA", strtotime($myRow->update_statge_on));
            } else {
                $update_statge_on = 'NA';
            }

            $up_arr = AyraHelp::getUser($myRow->update_statge_by);

            $stage = '';

            switch ($myRow->status) {
                case 1:
                    $stage = 'Not started';
                    break;
                case 2:
                    $stage = 'Design Awaited';
                    break;
                case 3:
                    $stage = 'Sample Awaited';
                    break;
                case 4:
                    $stage = 'Waiting for Quotation';
                    break;
                case 5:
                    $stage = 'Ordered';
                    break;
                case 6:
                    $stage = 'Received in Stock ';
                    break;
                case 7:
                    $stage = 'Received From Client';
                    break;
                case 8:
                    $stage = 'Removed';
                    break;
            }
            $BO_HTML .= '
            <tr>
              <th scope="row">' . $i . '</th>
              <td>' . $item_name . '</td>
              <td>' . $qty . '</td>
              <td>' . $stage . '</td>
              <td>' . $update_statge_on . '</td>
              <td>' . optional($up_arr)->name . '</td>
            </tr>';
        }
        $BO_HTML .= '
          </tbody>
          </table>
        </div>
      </div>';
        //get purchase details with status



        $data_arr = array(
            'stages_info' => $stage_data,
            'itm_qty' => $data->item_qty,
            'process_data' => $data,
            'created_by' => AyraHelp::getUser($data->created_by)->name,
            'qc_data' => $myqc_data,
            'BOM_HTML' => $BO_HTML,
            'artwork_start_date' => date('j-M-y', strtotime($myqc_data->artwork_start_date)),
            'stage_action_data' => $data_action_done_arr, //0 not 1 accesabe
            'stage_action_dataComment' => $data_actionComment_done_arr, //0 not 1 accesabe

        );
        return response()->json($data_arr);
    }

    public static function getStageAction($ticket_id, $process_id, $statge_id)
    {
        $stage_action_data = DB::table('st_process_action')->where('ticket_id', $ticket_id)->where('action_on', 1)->where('action_status', 1)->where('process_id', $process_id)->where('stage_id', $statge_id)->first();
        return $stage_action_data;
    }
    public static function getStageActionRND($ticket_id, $process_id, $statge_id)
    {


        $stage_action_data = DB::table('st_process_action_3')->where('ticket_id', $ticket_id)->where('action_on', 1)->where('process_id', $process_id)->where('stage_id', $statge_id)->first();

        return $stage_action_data;
    }



    public static function getStageActionMY_LEAD($ticket_id, $process_id, $statge_id)
    {


        $stage_action_data = DB::table('st_process_action_5_mylead')->where('ticket_id', $ticket_id)->where('action_on', 1)->where('process_id', $process_id)->where('stage_id', $statge_id)->first();

        return $stage_action_data;
    }


    public static function getStageActionLEAD($ticket_id, $process_id, $statge_id)
    {


        $stage_action_data = DB::table('st_process_action_4')->where('ticket_id', $ticket_id)->where('action_on', 1)->where('process_id', $process_id)->where('stage_id', $statge_id)->first();

        return $stage_action_data;
    }



    public static function getStageActionCHKAccessCode($access_code, $role_name)
    {

        $stage_action_data_arr = DB::table('st_user_access_action')->where('access_code', $access_code)->where('role_name', $role_name)->first();
        if ($stage_action_data_arr == null) {
            $stage_action_data = DB::table('st_user_access_action')->where('access_code', $access_code)->where('user_id', Auth::user()->id)->first();
        } else {
            $stage_action_data = DB::table('st_user_access_action')->where('access_code', $access_code)->where('role_name', $role_name)->first();
        }


        if ($stage_action_data == null) {
            return 0;
        } else {
            return 1;
        }
    }


    // getStagesListMY_LEAD

    public static function getStagesListMY_LEAD($process_id, $ticket_id, $rowCount, $dependent_ticket)
    {


        $data_arr = array();

        if ($process_id == 5) {

            $user = auth()->user();
            $userRoles = $user->getRoleNames();
            $user_role = $userRoles[0];
            $results = DB::table('st_process_stages')->where('process_id', $process_id)->orderBy('stage_position', 'asc')->get();



            $define_stage_arr = [1, 2, 3, 4, 5, 6, 7];


            //get ordet type
            foreach ($results as $key => $rowData) {

                if (count($define_stage_arr) > 0) {
                    if ($define_stage_arr[$key] == $rowData->stage_id) {
                        //================================

                        $get_stage_data = AyraHelp::getStageActionMY_LEAD($ticket_id, $process_id, $rowData->stage_id);
                        if ($get_stage_data == null) {
                            $stage_started = 0;
                        } else {
                            $stage_started = 1;
                        }
                        $access_stage = AyraHelp::getStageActionCHKAccessCode($rowData->access_code, $user_role);

                        $data_arr[] = array(
                            'stage_name' => $rowData->stage_name,
                            'stage_id' => $rowData->stage_id,
                            'process_id' => $process_id,
                            'stage_access_status' => $access_stage, //0 not 1 accesabe
                            'started' => $stage_started, //0 not 1 accesabe
                            'form_id' => $rowData->frm_id, //0 not 1 accesabe
                            'rowCount' => $rowCount, //0 not 1 accesabe
                            'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                            'exp_date' => $rowCount, //0 not 1 accesabe
                            'artwork_start_date' => '2019-09-09', //0 not 1 accesabe
                            'process_days' => $rowData->days_to_done, //0 not 1 accesabe

                        );
                        //===================================
                    }
                } else {
                    //================================
                    $get_stage_data = AyraHelp::getStageActionLEAD($ticket_id, $process_id, $rowData->stage_id);
                    if ($get_stage_data == null) {
                        $stage_started = 0;
                    } else {
                        $stage_started = 1;
                    }
                    $access_stage = AyraHelp::getStageActionCHKAccessCode($rowData->access_code, $user_role);

                    $data_arr[] = array(
                        'stage_name' => $rowData->stage_name,
                        'stage_id' => $rowData->stage_id,
                        'process_id' => $process_id,
                        'stage_access_status' => $access_stage, //0 not 1 accesabe
                        'started' => $stage_started, //0 not 1 accesabe
                        'form_id' => $rowData->frm_id, //0 not 1 accesabe
                        'rowCount' => $rowCount, //0 not 1 accesabe
                        'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                        'exp_date' => $rowCount, //0 not 1 accesabe
                        'artwork_start_date' => '2019-09-09', //0 not 1 accesabe
                        'process_days' => $rowData->days_to_done, //0 not 1 accesabe

                    );
                    //===================================
                }
            }

            return $data_arr;
        }
    }

    // getStagesListMY_LEAD

    public static function getStagesListLEAD($process_id, $ticket_id, $rowCount, $dependent_ticket)
    {



        if ($process_id == 4) {
            $user = auth()->user();
            $userRoles = $user->getRoleNames();
            $user_role = $userRoles[0];
            $results = DB::table('st_process_stages')->where('process_id', $process_id)->orderBy('stage_position', 'asc')->get();



            $define_stage_arr = [1, 2, 3, 4, 5, 6, 7];


            //get ordet type
            foreach ($results as $key => $rowData) {

                if (count($define_stage_arr) > 0) {
                    if ($define_stage_arr[$key] == $rowData->stage_id) {
                        //================================

                        $get_stage_data = AyraHelp::getStageActionLEAD($ticket_id, $process_id, $rowData->stage_id);
                        if ($get_stage_data == null) {
                            $stage_started = 0;
                        } else {
                            $stage_started = 1;
                        }
                        $access_stage = AyraHelp::getStageActionCHKAccessCode($rowData->access_code, $user_role);

                        $data_arr[] = array(
                            'stage_name' => $rowData->stage_name,
                            'stage_id' => $rowData->stage_id,
                            'process_id' => $process_id,
                            'stage_access_status' => $access_stage, //0 not 1 accesabe
                            'started' => $stage_started, //0 not 1 accesabe
                            'form_id' => $rowData->frm_id, //0 not 1 accesabe
                            'rowCount' => $rowCount, //0 not 1 accesabe
                            'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                            'exp_date' => $rowCount, //0 not 1 accesabe
                            'artwork_start_date' => '2019-09-09', //0 not 1 accesabe
                            'process_days' => $rowData->days_to_done, //0 not 1 accesabe

                        );
                        //===================================
                    }
                } else {
                    //================================
                    $get_stage_data = AyraHelp::getStageActionLEAD($ticket_id, $process_id, $rowData->stage_id);
                    if ($get_stage_data == null) {
                        $stage_started = 0;
                    } else {
                        $stage_started = 1;
                    }
                    $access_stage = AyraHelp::getStageActionCHKAccessCode($rowData->access_code, $user_role);

                    $data_arr[] = array(
                        'stage_name' => $rowData->stage_name,
                        'stage_id' => $rowData->stage_id,
                        'process_id' => $process_id,
                        'stage_access_status' => $access_stage, //0 not 1 accesabe
                        'started' => $stage_started, //0 not 1 accesabe
                        'form_id' => $rowData->frm_id, //0 not 1 accesabe
                        'rowCount' => $rowCount, //0 not 1 accesabe
                        'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                        'exp_date' => $rowCount, //0 not 1 accesabe
                        'artwork_start_date' => '2019-09-09', //0 not 1 accesabe
                        'process_days' => $rowData->days_to_done, //0 not 1 accesabe

                    );
                    //===================================
                }
            }

            return $data_arr;
        }
    }


    public static function getStagesListRND($process_id, $ticket_id, $rowCount, $dependent_ticket)
    {



        if ($process_id == 3) {
            $user = auth()->user();
            $userRoles = $user->getRoleNames();
            $user_role = $userRoles[0];
            $results = DB::table('st_process_stages')->where('process_id', $process_id)->orderBy('stage_position', 'asc')->get();



            $define_stage_arr = [1, 2, 3, 4, 5, 6];


            //get ordet type
            foreach ($results as $key => $rowData) {

                if (count($define_stage_arr) > 0) {
                    if ($define_stage_arr[$key] == $rowData->stage_id) {
                        //================================
                        //  echo "dd";
                        //  echo $ticket_id;
                        //  echo $process_id;
                        //  echo $rowData->stage_id;
                        //  die;
                        $get_stage_data = AyraHelp::getStageActionRND($ticket_id, $process_id, $rowData->stage_id);
                        if ($get_stage_data == null) {
                            $stage_started = 0;
                        } else {
                            $stage_started = 1;
                        }
                        $access_stage = AyraHelp::getStageActionCHKAccessCode($rowData->access_code, $user_role);

                        $data_arr[] = array(
                            'stage_name' => $rowData->stage_name,
                            'stage_id' => $rowData->stage_id,
                            'process_id' => $process_id,
                            'stage_access_status' => $access_stage, //0 not 1 accesabe
                            'started' => $stage_started, //0 not 1 accesabe
                            'form_id' => $rowData->frm_id, //0 not 1 accesabe
                            'rowCount' => $rowCount, //0 not 1 accesabe
                            'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                            'exp_date' => $rowCount, //0 not 1 accesabe
                            'artwork_start_date' => '2019-09-09', //0 not 1 accesabe
                            'process_days' => $rowData->days_to_done, //0 not 1 accesabe

                        );
                        //===================================
                    }
                } else {
                    //================================
                    $get_stage_data = AyraHelp::getStageActionRND($ticket_id, $process_id, $rowData->stage_id);
                    if ($get_stage_data == null) {
                        $stage_started = 0;
                    } else {
                        $stage_started = 1;
                    }
                    $access_stage = AyraHelp::getStageActionCHKAccessCode($rowData->access_code, $user_role);

                    $data_arr[] = array(
                        'stage_name' => $rowData->stage_name,
                        'stage_id' => $rowData->stage_id,
                        'process_id' => $process_id,
                        'stage_access_status' => $access_stage, //0 not 1 accesabe
                        'started' => $stage_started, //0 not 1 accesabe
                        'form_id' => $rowData->frm_id, //0 not 1 accesabe
                        'rowCount' => $rowCount, //0 not 1 accesabe
                        'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                        'exp_date' => $rowCount, //0 not 1 accesabe
                        'artwork_start_date' => '2019-09-09', //0 not 1 accesabe
                        'process_days' => $rowData->days_to_done, //0 not 1 accesabe

                    );
                    //===================================
                }
            }

            return $data_arr;
        }
    }
    public static function getStagesList($process_id, $ticket_id, $rowCount, $dependent_ticket)
    {





        if ($process_id == 2) {
            //purchase process

            $user = auth()->user();
            $userRoles = $user->getRoleNames();
            $user_role = $userRoles[0];
            // ----------ajcode


            // ----------ajcode

            //$process_arr_2= DB::table('st_process_action_2')->where('process_id',$process_id)->where('ticket_id',$ticket_id)->first();
            $process_arr_2 = DB::table('qc_bo_purchaselist')->where('id', $ticket_id)->first();




            if ($process_arr_2 == null) {

                for ($i = 1; $i <= 8; $i++) {

                    $stage_arr = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_id', $i)->first();

                    $access_stage = AyraHelp::getStageActionCHKAccessCode($stage_arr->access_code, $user_role);

                    $data_arr[] = array(
                        'stage_name' => $stage_arr->stage_name,
                        'stage_id' => $i,
                        'process_id' => $process_id,
                        'stage_access_status' => $access_stage, //0 not 1 accesabe
                        'started' => 0, //0 not 1 accesabe
                        'form_id' => $stage_arr->frm_id, //0 not 1 accesabe
                        'rowCount' => $rowCount, //0 not 1 accesabe
                        'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                        'exp_date' => $rowCount, //0 not 1 accesabe
                        'artwork_start_date' => '2019-09-09', //0 not 1 accesabe
                        'process_days' => $stage_arr->days_to_done, //0 not 1 accesabe

                    );
                }
            } else {


                //$results= DB::table('st_process_stages')->where('process_id',$process_id)->where('stage_id',$process_arr_2->stage_id)->first();
                $results = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_id', $process_arr_2->status)->first();



                // print_r($process_arr_2->stage_id);
                $stageid = ($process_arr_2->status);
                for ($i = 1; $i <= 8; $i++) {

                    $stage_arr = DB::table('st_process_stages')->where('process_id', $process_id)->where('stage_id', $i)->first();

                    $access_stage = AyraHelp::getStageActionCHKAccessCode($stage_arr->access_code, $user_role);
                    if ($i > $stageid) {
                        $started = 0;
                    } else {
                        $started = 1;
                    }
                    $data_arr[] = array(
                        'stage_name' => $stage_arr->stage_name,
                        'stage_id' => $i,
                        'process_id' => $process_id,
                        'stage_access_status' => $access_stage, //0 not 1 accesabe
                        'started' => $started, //0 not 1 accesabe
                        'form_id' => $stage_arr->frm_id, //0 not 1 accesabe
                        'rowCount' => $rowCount, //0 not 1 accesabe
                        'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                        'exp_date' => $rowCount, //0 not 1 accesabe
                        'artwork_start_date' => '2019-09-09', //0 not 1 accesabe
                        'process_days' => $stage_arr->days_to_done, //0 not 1 accesabe

                    );
                }
            }



            //purchase process
        } else {
            $user = auth()->user();
            $userRoles = $user->getRoleNames();
            $user_role = $userRoles[0];
            $results = DB::table('st_process_stages')->where('process_id', $process_id)->orderBy('stage_position', 'asc')->get();
            //get ordet type
            $form_data = AyraHelp::getQCFormDate($ticket_id);

            $orderType = optional($form_data)->order_type;
            $define_stage_arr = array();
            if ($orderType == 'Private Label') {
                if ($form_data->order_repeat == 2) {

                    $define_stage_arr = [1, 2, 0, 0, 0, 0, 7, 8, 9, 10, 0, 12, 13];
                } else {
                    // echo "no repeat";
                    $define_stage_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
                }
            }
            if ($orderType == 'Bulk' || $orderType == 'BULK') {
                if ($form_data->qc_from_bulk == 1) {

                    $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                } else {
                    $define_stage_arr = [1, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 12, 13];
                }
            }


            //get ordet type
            foreach ($results as $key => $rowData) {

                if (count($define_stage_arr) > 0) {
                    if ($define_stage_arr[$key] == $rowData->stage_id) {
                        //================================
                        $get_stage_data = AyraHelp::getStageAction($ticket_id, $process_id, $rowData->stage_id);
                        if ($get_stage_data == null) {
                            $stage_started = 0;
                            $stat_Date = '';
                            $dayAlet = '';
                        } else {


                            $stage_started = 1;
                            $dayAlet = '';
                            if ($rowData->stage_id == 1) { // if stage is 1 then check now long he started if same day then waring else delay days show with red ..

                                $created_date = date('Y-m-d', strtotime($form_data->created_at));
                                $stated_date = date('Y-m-d', strtotime($get_stage_data->created_at));
                                if ($created_date == $stated_date) {

                                    $dayAlet = '<span class="m-badge m-badge--warning m-badge--wide m-badge--rounded">1</span>';
                                } else {
                                    $created_date1 = \Carbon\Carbon::createFromFormat('Y-m-d', $created_date);
                                    $stated_date1 = \Carbon\Carbon::createFromFormat('Y-m-d', $stated_date);

                                    $diff_in_days = $stated_date1->diffForHumans($created_date1);
                                    $day_arr = explode(' ', $diff_in_days);
                                    $dayAlet = '<span class="m-badge m-badge--danger m-badge--wide m-badge--rounded">' . $day_arr[0] . '</span>';


                                    //echo "find diff of days to start";
                                }
                            } else {
                                $stat_Date = date('Y-m-d', strtotime($get_stage_data->created_at));
                                $dt = Carbon::parse($stat_Date);
                                $expected_date = $dt->addDays($rowData->days_to_done);
                                $completed_date = date('Y-m-d', strtotime($get_stage_data->completed_on));


                                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $completed_date);
                                $diff_in_days = $expected_date->diffForHumans($from);
                                $day_arr = explode(' ', $diff_in_days);
                                if ($day_arr[0] > $rowData->days_to_done) {
                                    $dayAlet = '<span class="m-badge m-badge--danger m-badge--wide m-badge--rounded">' . $day_arr[0] . '</span>';
                                } else {
                                    $dayAlet = '<span class="m-badge m-badge--warning m-badge--wide m-badge--rounded">' . $day_arr[0] . '</span>';
                                }
                            }
                        }
                        $access_stage = AyraHelp::getStageActionCHKAccessCode($rowData->access_code, $user_role);

                        $data_arr[] = array(
                            'stage_name' => $rowData->stage_name,
                            'stage_id' => $rowData->stage_id,
                            'process_id' => $process_id,
                            'stage_access_status' => $access_stage, //0 not 1 accesabe
                            'started' => $stage_started, //0 not 1 accesabe
                            'form_id' => $rowData->frm_id, //0 not 1 accesabe
                            'rowCount' => $rowCount, //0 not 1 accesabe
                            'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                            'exp_date' => $rowCount, //0 not 1 accesabe
                            'artwork_start_date' => $dayAlet, //0 not 1 accesabe
                            'process_days' => $rowData->days_to_done, //0 not 1 accesabe

                        );
                        //===================================
                    }
                } else {
                    //================================
                    $get_stage_data = AyraHelp::getStageAction($ticket_id, $process_id, $rowData->stage_id);
                    if ($get_stage_data == null) {
                        $stage_started = 0;
                        $stat_Date = '';
                    } else {
                        $stage_started = 1;
                        $stat_Date = $get_stage_data->created_at;
                        $stat_Date = $get_stage_data->created_at->diffForHumans();
                    }
                    $access_stage = AyraHelp::getStageActionCHKAccessCode($rowData->access_code, $user_role);

                    $data_arr[] = array(
                        'stage_name' => $rowData->stage_name,
                        'stage_id' => $rowData->stage_id,
                        'process_id' => $process_id,
                        'stage_access_status' => $access_stage, //0 not 1 accesabe
                        'started' => $stage_started, //0 not 1 accesabe
                        'form_id' => $rowData->frm_id, //0 not 1 accesabe
                        'rowCount' => $rowCount, //0 not 1 accesabe
                        'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                        'exp_date' => $rowCount, //0 not 1 accesabe
                        'artwork_start_date' => $stat_Date, //0 not 1 accesabe
                        'process_days' => $rowData->days_to_done, //0 not 1 accesabe

                    );
                    //===================================
                }
            }
        }




        return $data_arr;
    }

    public static function getStagesList_temp($process_id, $ticket_id, $rowCount, $dependent_ticket)
    {
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        $results = DB::table('st_process_stages')->where('process_id', $process_id)->orderBy('stage_position', 'asc')->get();

        foreach ($results as $key => $rowData) {
            $get_stage_data = AyraHelp::getStageAction($ticket_id, $process_id, $rowData->stage_id);
            if ($get_stage_data == null) {
                $stage_started = 0;
            } else {
                $stage_started = 1;
            }
            $access_stage = AyraHelp::getStageActionCHKAccessCode($rowData->access_code, $user_role);

            $data_arr[] = array(
                'stage_name' => $rowData->stage_name,
                'stage_id' => $rowData->stage_id,
                'process_id' => $process_id,
                'stage_access_status' => $access_stage, //0 not 1 accesabe
                'started' => $stage_started, //0 not 1 accesabe
                'form_id' => $rowData->frm_id, //0 not 1 accesabe
                'rowCount' => $rowCount, //0 not 1 accesabe
                'dependent_ticket' => $dependent_ticket, //0 not 1 accesabe
                'exp_date' => $rowCount, //0 not 1 accesabe
                'artwork_start_date' => '2019-09-09', //0 not 1 accesabe
                'process_days' => $rowData->days_to_done, //0 not 1 accesabe

            );
        }
        return $data_arr;
    }





    public static function UpdateSAPCHKLIST()
    {

        $form_arr = QCFORM::where('dispatch_status', 1)->where('is_deleted', 0)->get();
        foreach ($form_arr as $key => $rowData) {

            $flight = SAP_CHECKLISt::updateOrCreate(
                ['form_id' => $rowData->form_id],
                [

                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'update_on' => date('Y-m-d H:i:s')
                ]
            );
        }
    }


    public static function getClientOrderValMonthWise($m_digit, $client_id)
    {





        // $datasAll=QCFORM::where('is_deleted',0)->get();
        // $sumTotalAll=0;
        // foreach($datasAll as $key=> $rowDataAll){
        //     $sumAll=$rowDataAll->item_qty*$rowDataAll->item_sp;
        //     $sumTotalAll=$sumAll+$sumTotalAll;

        // }
        // $sumTotalAll;

        $year_digit = "2020";

        $datas = QCFORM::where('is_deleted', 0)->where('client_id', $client_id)->whereMonth('created_at', $m_digit)->whereYear('created_at', $year_digit)->get();
        // print_r(count($datas));
        // die;

        $sumTotal = 0;
        foreach ($datas as $key => $rowData) {
            if ($rowData->qc_from_bulk == 1) {
                $sum = $rowData->bulk_order_value;
                $sumTotal = $sum + $sumTotal;
            } else {
                $sum = $rowData->item_qty * $rowData->item_sp;
                $sumTotal = $sum + $sumTotal;
            }
        }


        return $sumTotal;
    }


    public static function getClientOrderValue($client_id)
    {




        $datasAll = QCFORM::where('is_deleted', 0)->get();
        $sumTotalAll = 0;
        foreach ($datasAll as $key => $rowDataAll) {
            $sumAll = $rowDataAll->item_qty * $rowDataAll->item_sp;
            $sumTotalAll = $sumAll + $sumTotalAll;
        }
        $sumTotalAll;


        $datas = QCFORM::where('is_deleted', 0)->where('client_id', $client_id)->get();
        $sumTotal = 0;
        foreach ($datas as $key => $rowData) {
            if ($rowData->qc_from_bulk == 1) {
                $sum = $rowData->bulk_order_value;
                $sumTotal = $sum + $sumTotal;
            } else {
                $sum = $rowData->item_qty * $rowData->item_sp;
                $sumTotal = $sum + $sumTotal;
            }
        }
        $sumTotal;
        $orderP = ($sumTotal * 100) / $sumTotalAll;

        $data = array(
            'order_val' => $sumTotal,
            'order_percentage' => substr($orderP, 0, 4)

        );
        return $data;
    }

    public static function getClientOrderValueFilter($client_id, $month_date, $sales_user)
    {



        $month = \Carbon\Carbon::createFromFormat('Y-m-d', $month_date)->month;




        if ($sales_user == 'ALL') {
            $datasAll = QCFORM::where('is_deleted', 0)->whereMonth('created_at', $month)->get();
        } else {
            $datasAll = QCFORM::where('is_deleted', 0)->whereMonth('created_at', $month)->where('created_by', $sales_user)->get();
        }

        $sumTotalAll = 0;
        foreach ($datasAll as $key => $rowDataAll) {
            $sumAll = $rowDataAll->item_qty * $rowDataAll->item_sp;
            $sumTotalAll = $sumAll + $sumTotalAll;
        }
        $sumTotalAll;

        if ($sales_user == 'ALL') {
            $datas = QCFORM::where('is_deleted', 0)->where('client_id', $client_id)->whereMonth('created_at', $month)->get();
        } else {
            $datas = QCFORM::where('is_deleted', 0)->where('client_id', $client_id)->whereMonth('created_at', $month)->where('created_by', $sales_user)->get();
        }

        $sumTotal = 0;
        foreach ($datas as $key => $rowData) {
            if ($rowData->qc_from_bulk == 1) {
                $sum = $rowData->bulk_order_value;
                $sumTotal = $sum + $sumTotal;
            } else {
                $sum = $rowData->item_qty * $rowData->item_sp;
                $sumTotal = $sum + $sumTotal;
            }
        }
        $sumTotal;
        $orderP = ($sumTotal * 100) / $sumTotalAll;

        $data = array(
            'order_val' => $sumTotal,
            'order_percentage' => substr($orderP, 0, 4)

        );
        return $data;
    }


    public static function ARPData($plan_id)
    {
        $datas = OPHAchieved::where('plan_id', $plan_id)->get();

        return $datas;
    }
    public static function getAllPOCData()
    {
        $datas = POCatalogData::get();
        return $datas;
    }

    public static function getMonthlyDispatchUnitsPrice($m_digit)
    {
        $ydigit = "2020";
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        $qc_data = array();
        if ($user_role == 'Admin') {
            $qc_data = OrderDispatchData::whereMonth('dispatch_on', $m_digit)->whereYear('dispatch_on', $ydigit)->get();
        }
        $me = array();
        $i = 0;
        foreach ($qc_data as $key => $row) {
            $qc_data = AyraHelp::getQCFormDate($row->form_id);
            $b = intval(optional($qc_data)->item_sp) * intval($row->total_unit);
            $i = $b;
            $me[] = $i;
        }
        return array_sum($me);
    }

    public static function purchaseArtWork()
    {
        $orderData = QC_BOM_Purchase::where('dispatch_status', 1)->where('status', 2)->where('is_deleted', 0)->orderBy('order_id', 'DESC')->get();
        $i = 0;
        foreach ($orderData as $key_opd => $orderval) {
            //current  order statge
            $qc_data_arr = AyraHelp::getCurrentStageByForMID($orderval->form_id);
            if (isset($qc_data_arr->order_statge_id)) {
                $statge_arr = AyraHelp::getStageNameByCode($qc_data_arr->order_statge_id);
                $Spname = optional($statge_arr)->step_code;
            } else {
                $Spname = '';
            }
            if ($Spname == 'ART_WORK_RECIEVED') {
                $i++;
            }
            //current  order statge

        }
        $data = array(
            'artwork_count' => $i,
            'allothers' => count($orderData) - $i,
        );
        return $data;
    }
    public static function getTopClient($digit)
    {
        //   $chartDatas = QCFORM::select([
        //      DB::raw('item_sp*item_qty AS orderVal,form_id'),

        //   ])

        //   ->orderBy('form_id', 'DESC')
        //   ->take($digit)
        //   ->get();

        $chartDatas = DB::table('qc_forms')
            ->leftJoin('clients', 'qc_forms.client_id', '=', 'clients.id')
            ->sum(DB::raw('qc_forms.item_sp * qc_forms.item_qty'));
        return $chartDatas;
    }

    public static function getMonthlyDispatchUnits($m_digit)
    {
        $ydigit = "2020";
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        $qc_data = array();

        $qc_data = OrderDispatchData::whereMonth('dispatch_on', $m_digit)->whereYear('dispatch_on', $ydigit)->get();


        $me = array();
        $i = 0;
        foreach ($qc_data as $key => $row) {
            $i = intval($row->total_unit);
            $me[] = $i;
        }



        return array_sum($me);
    }
    public static function AyraCrypt($data)
    {
        return $encrypted = Crypt::encryptString($data);
    }
    public static function AyraEnCrypt($data)
    {
        return $decrypted = Crypt::decryptString($data);
    }

    public static function getOrderListByPlan($form_id)
    {
        $data_arr = HPlanDay2::where('form_id', $form_id)->get();
    }

    public static function getRNDStageNOW()
    {
        $data_planDay4Only_arr = DB::table('st_process_stages')->where('process_id', 3)->get();
        return $data_planDay4Only_arr;
    }

    public static function getPlanDay4Day($PlanId)
    {
        $data_planDay4Only_arr = DB::table('h_plan_day_4')->where('plan_id', $PlanId)->first();
        return $data_planDay4Only_arr;
    }

    public static function getPlanDay4DayFormID($form_id)
    {
        $data_planDay4Only_arr = DB::table('h_plan_day_4')->where('form_id', $form_id)->first();
        return $data_planDay4Only_arr;
    }
    public static function getAllPlanDataByPlanID($PlanId)
    {
        $data_plan_arr = DB::table('h_plan_day')->where('id', $PlanId)->first();
        $data_planDay4_arr = DB::table('h_plan_day_4')->where('plan_id', $PlanId)->get();


        $data = array(
            'plan_data' => $data_plan_arr,
            'planDay4_data' => $data_planDay4_arr

        );
        return $data;
    }


    public static function getPendingOrderCountwithValue($id)
    {
        switch ($id) {
            case 1:
                $orderData = QCFORM::where('dispatch_status', 1)->where('is_deleted', 0)->get();
                return count($orderData);
                break;
            case 2:
                $qc_data = array();

                $qc_data = QCFORM::where('is_deleted', 0)->where('dispatch_status', 1)->get();







                $me = array();
                foreach ($qc_data as $key => $row) {
                    if (isset($row->qc_from_bulk)) {
                        if ($row->qc_from_bulk == 1) {
                            $me[] = $row->bulk_order_value;
                        } else {
                            $me[] = ($row->item_sp) * ($row->item_qty);
                        }
                    } else {
                        $me[] = ($row->item_sp) * ($row->item_qty);
                    }
                }

                $amount = array_sum($me);
                setlocale(LC_MONETARY, 'en_IN');
                $amount = money_format('%!i', $amount);
                $pieces = explode(".", $amount);
                return $pieces[0];


                break;
        }
    }
    public static function getPendingProcessCount($id)
    {
        switch ($id) {
            case 1:
                # code...
                $orderData = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();

                // $orderData =DB::table('order_master')
                //     ->join('qc_forms', 'qc_forms.form_id', '=', 'order_master.form_id')
                //     ->where('qc_forms.dispatch_status',1)
                //     ->where('qc_forms.order_type','!=','Bulk')
                //     ->where('qc_forms.artwork_status',1)
                //     ->where('qc_forms.is_deleted',0)
                //     ->where('order_master.order_statge_id','!=','DISPATCH_ORDER')
                //     ->select('qc_forms.*')
                //     ->get();


                $i = 0;
                foreach ($orderData as $key_opd => $orderval) {
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($orderval->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $orderval->form_id;


                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = $batchSize = (ceil((($orderval->item_qty) * ($orderval->item_size)) / 1000));
                        $i = $i + $mydata;
                    }
                }
                return $i . " KG";
                break;
            case 2:
                # code...
                $data_arr = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();
                $i = 0;
                $j = 0;
                foreach ($data_arr as $dataKey => $rowData) {
                    // print_r($rowData->form_id);
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = QCPP::where('qc_from_id', $rowData->form_id)->where('qc_label', 'FILLING')->where('qc_yes', 'YES')->first();
                        if ($mydata != null) {
                            $i++;
                            $j = $j + $rowData->item_qty;
                        }
                    }
                }

                return $j . ' PCS';
                # code...
                break;
            case 3:
                # code...
                $data_arr = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();
                $i = 0;
                $j = 0;
                foreach ($data_arr as $dataKey => $rowData) {
                    //-----------
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = QCPP::where('qc_from_id', $rowData->form_id)->where('qc_label', 'SEAL')->where('qc_yes', 'YES')->first();
                        if ($mydata != null) {
                            $i++;
                            $j = $j + $rowData->item_qty;
                        }
                    }

                    //------------


                }
                return $j . ' PCS';
                # code...
                break;
            case 4:
                # code...
                $data_arr = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();
                $i = 0;
                $j = 0;
                foreach ($data_arr as $dataKey => $rowData) {

                    //-----------
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = QCPP::where('qc_from_id', $rowData->form_id)->where('qc_label', 'CAPPING')->where('qc_yes', 'YES')->first();
                        if ($mydata != null) {
                            $i++;
                            $j = $j + $rowData->item_qty;
                        }
                    }

                    //------------


                }
                return $j . ' PCS';
                # code...
                break;

            case 5:
                # code...
                $data_arr = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();

                $i = 0;
                $j = 0;
                foreach ($data_arr as $dataKey => $rowData) {
                    // print_r($rowData->form_id);
                    //-----------
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = QCPP::where('qc_from_id', $rowData->form_id)->where('qc_label', 'LABEL')->where('qc_yes', 'YES')->first();
                        if ($mydata != null) {
                            $i++;
                            $j = $j + $rowData->item_qty;
                        }
                    }

                    //------------


                }
                return $j . ' PCS';
                # code...
                break;
            case 6:
                # code...
                $data_arr = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();
                $i = 0;
                $j = 0;
                foreach ($data_arr as $dataKey => $rowData) {
                    // print_r($rowData->form_id);
                    //-----------
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = QCPP::where('qc_from_id', $rowData->form_id)->where('qc_label', 'CODING ON LABEL')->where('qc_yes', 'YES')->first();
                        if ($mydata != null) {
                            $i++;
                            $j = $j + $rowData->item_qty;
                        }
                    }

                    //------------


                }
                return $j . ' PCS';
                # code...
                break;
            case 7:
                # code...
                $data_arr = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();

                $i = 0;
                $j = 0;
                foreach ($data_arr as $dataKey => $rowData) {
                    // print_r($rowData->form_id);
                    //-----------
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = QCPP::where('qc_from_id', $rowData->form_id)->where('qc_label', 'BOXING')->where('qc_yes', 'YES')->first();
                        if ($mydata != null) {
                            $i++;
                            $j = $j + $rowData->item_qty;
                        }
                    }

                    //------------


                }
                return $j . ' PCS';
                # code...
                break;
            case 8:
                # code...
                $data_arr = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();

                $i = 0;
                $j = 0;
                foreach ($data_arr as $dataKey => $rowData) {
                    //-----------
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = QCPP::where('qc_from_id', $rowData->form_id)->where('qc_label', 'CODING ON BOX')->where('qc_yes', 'YES')->first();
                        if ($mydata != null) {
                            $i++;
                            $j = $j + $rowData->item_qty;
                        }
                    }

                    //------------



                }
                return $j . ' PCS';
                # code...
                break;
            case 9:
                # code...
                $data_arr = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();

                $i = 0;
                $j = 0;
                foreach ($data_arr as $dataKey => $rowData) {
                    //-----------
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = QCPP::where('qc_from_id', $rowData->form_id)->where('qc_label', 'SHRINK WRAP')->where('qc_yes', 'YES')->first();
                        if ($mydata != null) {
                            $i++;
                            $j = $j + $rowData->item_qty;
                        }
                    }

                    //------------


                }
                return $j . ' PCS';
                # code...
                break;
            case 10:
                # code...
                $data_arr = QCFORM::where('dispatch_status', 1)->where('order_type', '!=', 'Bulk')->where('artwork_status', 1)->where('is_deleted', 0)->get();

                $i = 0;
                $j = 0;
                foreach ($data_arr as $dataKey => $rowData) {
                    //-----------
                    $qc_data_arr = AyraHelp::getCurrentStageByForMID($rowData->form_id);
                    if (isset($qc_data_arr->order_statge_id)) {
                        $step_code = $qc_data_arr->order_statge_id;
                    } else {
                        $step_code = optional($qc_data_arr)->order_statge_id;
                    }
                    if ($step_code != 'DISPATCH_ORDER') {
                        $mydata = QCPP::where('qc_from_id', $rowData->form_id)->where('qc_label', 'CARTONIZE')->where('qc_yes', 'YES')->first();
                        if ($mydata != null) {
                            $i++;
                            $j = $j + $rowData->item_qty;
                        }
                    }

                    //------------


                }
                return $j . ' PCS';
                # code...
                break;
        }
        // return $i;

    }

    public static function getSAPLISTPending($sapid)
    {

        switch ($sapid) {
            case '1':
                //$datas = DB::table('sap_checklist')->where('sap_so',0)->get();
                $datas = DB::table('sap_checklist')
                    ->join('qc_forms', 'qc_forms.form_id', '=', 'sap_checklist.form_id')
                    ->where('sap_checklist.sap_so', 0)
                    ->where('qc_forms.dispatch_status', 1)
                    ->where('qc_forms.is_deleted', 0)
                    ->get();


                break;
            case '2':
                // $datas = DB::table('sap_checklist')->where('sap_fg',0)->get();
                $datas = DB::table('sap_checklist')
                    ->join('qc_forms', 'qc_forms.form_id', '=', 'sap_checklist.form_id')
                    ->where('sap_checklist.sap_fg', 0)
                    ->where('qc_forms.dispatch_status', 1)
                    ->where('qc_forms.is_deleted', 0)
                    ->get();

                break;
            case '3':
                // $datas = DB::table('sap_checklist')->where('sap_sfg',0)->get();
                $datas = DB::table('sap_checklist')
                    ->join('qc_forms', 'qc_forms.form_id', '=', 'sap_checklist.form_id')
                    ->where('sap_checklist.sap_sfg', 0)
                    ->where('qc_forms.dispatch_status', 1)
                    ->where('qc_forms.is_deleted', 0)
                    ->get();

                break;
            case '4':
                // $datas = DB::table('sap_checklist')->where('sap_production',0)->get();
                $datas = DB::table('sap_checklist')
                    ->join('qc_forms', 'qc_forms.form_id', '=', 'sap_checklist.form_id')
                    ->where('sap_checklist.sap_production', 0)
                    ->where('qc_forms.dispatch_status', 1)
                    ->where('qc_forms.is_deleted', 0)
                    ->get();


                break;
            case '5':
                // $datas = DB::table('sap_checklist')->where('sap_invoice',0)->get();
                $datas = DB::table('sap_checklist')
                    ->join('qc_forms', 'qc_forms.form_id', '=', 'sap_checklist.form_id')
                    ->where('sap_checklist.sap_invoice', 0)
                    ->where('qc_forms.dispatch_status', 1)
                    ->where('qc_forms.is_deleted', 0)
                    ->get();

                break;
            case '6':
                //$datas = DB::table('sap_checklist')->where('sap_dispatch',0)->get();
                $datas = DB::table('sap_checklist')
                    ->rightjoin('qc_forms', 'qc_forms.form_id', '=', 'sap_checklist.form_id')
                    ->where('sap_checklist.sap_dispatch', 0)
                    ->where('qc_forms.dispatch_status', 1)
                    ->where('qc_forms.is_deleted', 0)
                    ->get();

                break;
        }



        return count($datas);
    }

    public static function getPurcahseStockRecivedOrder($form_id)
    {
        $purdatas = QC_BOM_Purchase::where('form_id', $form_id)->get();
        $pcount = count($purdatas);
        $tq = 7 * $pcount;


        $i = 0;
        foreach ($purdatas as $key => $Row) {
            $s = $Row->status;
            $i += $s;
        }

        if ($i == $tq) {
            return 2;
        } else {
            return 1;
        }
    }
    public static function getOperationalHealth()
    {
        $data = DB::table('h_operation')->get();
        return $data;
    }
    public static function getPlanOpertionCat()
    {
        $data = DB::table('plan_type_category')->get();
        return $data;
    }
    public static function getPlanOpertionCatID($id)
    {
        $data = DB::table('plan_type_category')->where('id', $id)->first();
        return $data;
    }

    public static function getOperationalHealthBYid($id)
    {
        $data = DB::table('h_operation')->where('id', $id)->first();
        return $data;
    }
    public static function getBulkITEMName($form_id)
    {
        $mydatas = DB::table('qc_bulk_order_form')->where('form_id', $form_id)->get();
        $name = "";
        foreach ($mydatas as $key => $Row) {
            if (isset($Row->item_name)) {
                $name .= $Row->item_name . ",";
            }
        }
        return $name;
    }
    public static function getSAP_CHECKLISTData($form_id)
    {
        $data = SAP_CHECKLISt::where('form_id', $form_id)->first();
        return $data;
    }
    public static function getSAP_CHECKLISTDataINNER($form_id)
    {
        $data = SAP_CHECKLISt::where('form_id', $form_id)->first();
        return $data;
    }
    public static function OrderStageCompletedByUserList()
    {
        $data_arr = OrderMaster::distinct('assigned_by')->get(['assigned_by']);
        $userData = array();
        foreach ($data_arr as $key => $value_data) {

            $userData[] = array(
                'user_id' => $value_data->assigned_by,
                'user_name' => AyraHelp::getUser($value_data->assigned_by)->name
            );
        }
        return $userData;
    }

    public static function getPlanID()
    {

        $qcdata_arrs = DB::table('h_plan_day')->max('id') + 1;


        return $qcdata_arrs;
    }
    public static function getAllUser()
    {

        $qcdata_arrs = DB::table('users')->where('is_deleted', 0)->get();


        return $qcdata_arrs;
    }



    public static function getBULKCount($form_id)
    {
        $data = QCBULK_ORDER::where('form_id', $form_id)->whereNotNull('item_name')->get();
        return count($data);
    }
    public static function getBULKData($form_id)
    {
        $data = QCBULK_ORDER::where('form_id', $form_id)->whereNotNull('item_name')->get();
        return $data;
    }



    public static function getOrderStageCompletedCountDataV1($days, $step_code)
    {

        $chartDatas = OrderMasterV1::select([
            DB::raw('DATE(completed_on) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('completed_on', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('stage_id', $step_code)
            ->where('process_id', 1)
            ->where('action_status', 1)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }




    public static function getLeadStageCompletedCountData($days, $step_code)
    {

        $chartDatas = LeadDataProcess::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('stage_id', $step_code)
            ->where('action_on', 1)

            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }


    public static function getOrderStageCompletedCountData($days, $step_code)
    {

        $chartDatas = OrderMaster::select([
            DB::raw('DATE(completed_on) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('completed_on', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('order_statge_id', $step_code)
            ->where('action_status', 1)

            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }
    public static function getINGBrands()
    {


        $brands = DB::table('rnd_brands')->get();
        return $brands;
    }
    public static function getINGBrandByID($id)
    {


        $brands = DB::table('rnd_brands')->where('id', $id)->first();
        return $brands;
    }

    public static function getOrderStageCompletedCount($active_date, $step_code)
    {


        $qc_data = array();
        $qc_data = OrderMaster::where('order_statge_id', $step_code)->where('action_status', 1)->whereDate('completed_on', $active_date)->get();
        return count($qc_data);
    }
    public static function getOrderStageCompletedCountV1($active_date, $step_code)
    {


        $qc_data = array();
        $qc_data = OrderMasterV1::where('stage_id', $step_code)->where('action_status', 1)->whereDate('completed_on', $active_date)->get();
        return count($qc_data);
    }




    public static function getLeadStageCompletedCountAjax($active_date, $stage_id, $user_id)
    {
        //$userid=NULL;

        $process_data_1 = array();


        if ($user_id == 'ALL') {
            $process_data_1 = DB::table('st_process_action_4')->where('action_on', 1)->where('stage_id', $stage_id)->whereDate('created_at', $active_date)->get();
        } else {
            $process_data_1 = DB::table('st_process_action_4')->where('action_on', 1)->where('stage_id', $stage_id)->whereDate('created_at', $active_date)->where('completed_by', $user_id)->get();
        }





        //$qc_data=OrderMaster::where('order_statge_id',$step_code)->where('assigned_by',$user_id)->where('action_status',1)->whereDate('completed_on', $active_date)->get();
        return count($process_data_1);
    }



    public static function getOrderStageCompletedCountAjax($active_date, $step_code, $user_id)
    {


        $qc_data = array();
        $qc_data = OrderMaster::where('order_statge_id', $step_code)->where('assigned_by', $user_id)->where('action_status', 1)->whereDate('completed_on', $active_date)->get();
        return count($qc_data);
    }


    public static function getUserCompletedStage($step_id, $filter_with)
    {

        $yesterday = date("Y-m-d", strtotime('-1 days'));
        $week = date("Y-m-d", strtotime('-1 week'));
        $months = date("Y-m-d", strtotime('-1 months'));
        if ($filter_with == 1) {
            $filterWithDays = $yesterday;
        }
        if ($filter_with == 2) {
            $filterWithDays = $week;
        }
        if ($filter_with == 3) {
            $filterWithDays = $months;
        }
        $mydata = array();
        $countData = OPData::where('step_id', $step_id)->where('status', 1)->whereDate('created_at', $filterWithDays)->distinct()->get(['created_by']);
        foreach ($countData as $key => $row) {

            $countData = OPData::where('created_by', $row->created_by)->where('step_id', $step_id)->where('status', 1)->whereDate('created_at', $filterWithDays)->get();
            $user = AyraHelp::getUser($row->created_by);
            $mydata[] = array(
                'name' => $user->name,
                'count' => count($countData)

            );
        }
        return $mydata;
    }

    public static function getUserCompletedStage_OK($step_id, $filter_with)
    {




        $yesterday = date("Y-m-d", strtotime('-1 days'));
        $week = date("Y-m-d", strtotime('-1 week'));
        $months = date("Y-m-d", strtotime('-1 months'));
        if ($filter_with == 1) {
            $filterWithDays = $yesterday;
        }
        if ($filter_with == 2) {
            $filterWithDays = $week;
        }
        if ($filter_with == 3) {
            $filterWithDays = $months;
        }




        //$allUsers=User::get();
        $allUsers = AyraHelp::getSalesAgentAdmin();

        $mydata = array();
        foreach ($allUsers as $key => $user) {

            $countData = OPData::where('created_by', $user->id)->where('step_id', $step_id)->where('status', 1)->whereDate('created_at', $filterWithDays)->get();
            $mydata[] = array(
                'name' => $user->name,
                'count' => count($countData)

            );
        }
        return $mydata;
    }

    public static function getOrderByFormID($formid)
    {
        $qc_data = QCFORM::where('form_id', $formid)->first();
        return $qc_data;
    }
    public static function getOrderValueFilterByUser($m_digit, $userid)
    {

        $ydigit = "2020";
        $qc_data = array();

        $qc_data = QCFORM::where('is_deleted', 0)->where('created_by', $userid)->whereMonth('created_at', $m_digit)->whereYear('created_at', $ydigit)->get();







        $me = array();
        foreach ($qc_data as $key => $row) {
            if (isset($row->qc_from_bulk)) {
                if ($row->qc_from_bulk == 1) {
                    $me[] = $row->bulk_order_value;
                } else {
                    $me[] = ($row->item_sp) * ($row->item_qty);
                }
            } else {
                $me[] = ($row->item_sp) * ($row->item_qty);
            }
        }


        $amount =  array_sum($me);
        // setlocale(LC_MONETARY, 'en_IN');
        // $amount = money_format('%!i', $amount);
        return $amount;
    }


    public static function getOrderValueFilter($m_digit)
    {
        $year_digit = "2020";

        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        $qc_data = array();
        if ($user_role == 'Admin') {
            $qc_data = QCFORM::where('is_deleted', 0)->whereMonth('created_at', $m_digit)->whereYear('created_at', $year_digit)->get();
        }
        if ($user_role == 'SalesUser') {
            $qc_data = QCFORM::where('is_deleted', 0)->where('created_by', Auth::user()->id)->whereMonth('created_at', $m_digit)->whereYear('created_at', $year_digit)->get();
        }




        $me = array();
        foreach ($qc_data as $key => $row) {
            if (isset($row->qc_from_bulk)) {
                if ($row->qc_from_bulk == 1) {
                    $me[] = $row->bulk_order_value;
                } else {
                    $me[] = ($row->item_sp) * ($row->item_qty);
                }
            } else {
                $me[] = ($row->item_sp) * ($row->item_qty);
            }
        }

        return array_sum($me);
    }


    public static function getFeedbackDataByUser($user_id, $m, $y)
    {
        if ($m == 'ALL') {

            $users_1 = DB::table('samples')->where('created_by', $user_id)->where('yr', $y)->where('sample_feedback', '=', 1)->get();
            $users_2 = DB::table('samples')->where('created_by', $user_id)->where('yr', $y)->where('sample_feedback', '=', 2)->get();
            $users_3 = DB::table('samples')->where('created_by', $user_id)->where('yr', $y)->where('sample_feedback', '=', 3)->get();
            $users_4 = DB::table('samples')->where('created_by', $user_id)->where('yr', $y)->where('sample_feedback', '=', 4)->get();


            $datafeed_ar = array(
                'feed_1' => count($users_1),
                'feed_2' => count($users_2),
                'feed_3' => count($users_3),
                'feed_4' => count($users_4),
            );
            return $datafeed_ar;
        } else {
            $time = strtotime($m);
            $m = date("m", $time);
            $users_1 = DB::table('samples')->where('created_by', $user_id)->where('mo', $m)->where('yr', $y)->where('sample_feedback', '=', 1)->get();
            $users_2 = DB::table('samples')->where('created_by', $user_id)->where('mo', $m)->where('yr', $y)->where('sample_feedback', '=', 2)->get();
            $users_3 = DB::table('samples')->where('created_by', $user_id)->where('mo', $m)->where('yr', $y)->where('sample_feedback', '=', 3)->get();
            $users_4 = DB::table('samples')->where('created_by', $user_id)->where('mo', $m)->where('yr', $y)->where('sample_feedback', '=', 4)->get();


            $datafeed_ar = array(
                'feed_1' => count($users_1),
                'feed_2' => count($users_2),
                'feed_3' => count($users_3),
                'feed_4' => count($users_4),
            );
            return $datafeed_ar;
        }
    }


    public static function getFeedbackData()
    {

        $users_1 = DB::table('samples')->where('sample_feedback', '=', 1)->get();
        $users_2 = DB::table('samples')->where('sample_feedback', '=', 2)->get();
        $users_3 = DB::table('samples')->where('sample_feedback', '=', 3)->get();
        $users_4 = DB::table('samples')->where('sample_feedback', '=', 4)->get();


        $datafeed_ar = array(
            'feed_1' => count($users_1),
            'feed_2' => count($users_2),
            'feed_3' => count($users_3),
            'feed_4' => count($users_4),
        );
        return $datafeed_ar;
    }

    public static function LastUpdateAtStageNew()
    {
        $data = OrderStageCountNew::first();
        $today = date('Y-m-d H:i:s');
        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->update_at);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $today);
        $diff_in_days = $to->diffForHumans($from);
        return $diff_in_days . " at:" . date("h:i:s", strtotime($data->update_at));
    }


    public static function LastUpdateAtStage()
    {
        $data = OrderStageCount::first();
        $today = date('Y-m-d H:i:s');
        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->update_at);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $today);
        $diff_in_days = $to->diffForHumans($from);
        return $diff_in_days . " at:" . date("h:i:s", strtotime($data->update_at));
    }

    public static function setOrderStagesForApiYestarday()
    {
    }

    public static function getOrderStagesDelayAPIV1()
    {
        //this api is used to make green and red database with order_stages_countNew

        $arr_data = DB::table('st_process_action')
            ->where('st_process_action.action_status', 0)
            ->select('st_process_action.*')
            ->get();

        foreach ($arr_data as $key => $rowdata) {
            $rowid = $rowdata->id;
            $Date = $rowdata->created_at;
            $stage_id = $created_at = $rowdata->stage_id;
            $step_data_arr = DB::table('st_process_stages')->select('days_to_done')->where('process_id', 1)->where('stage_id', $stage_id)->first();
            $days_to_done = $step_data_arr->days_to_done;
            $expire_date = strtotime(date('Y-m-d H:i:s', strtotime($Date . ' + ' . $days_to_done . ' days')));

            $today = strtotime(Date('Y-m-d H:i:s'));
            if ($today > $expire_date) {
                $mark = 1; //delay
            } else {
                $mark = 0; //ok
            }



            DB::table('st_process_action')
                ->updateOrInsert(
                    ['id' => $rowid],
                    ['action_mark' => $mark]
                );
        }
    }
    public static function setOrderStagesForApiV1()
    {
        //-------------------------------------------
        $step_data_arr = DB::table('st_process_stages')->where('process_id', 1)->get();
        foreach ($step_data_arr as $key => $row) {
            $stage_id = $row->stage_id;
            $stepcode_insert = $row->access_code;
            $stage_name_insert = $row->stage_name;
            $days_to_done = $row->days_to_done;
            //adding data to database
            $qc_data = DB::table('qc_forms')
                ->where('qc_forms.is_deleted', 0)
                ->where('qc_forms.dispatch_status', 1)
                ->select('qc_forms.*')
                ->get();

            $j = 0;
            foreach ($qc_data as $key => $row) {

                $qc_dataRed = DB::table('st_process_action')
                    ->where('st_process_action.ticket_id', $row->form_id)
                    ->where('st_process_action.stage_id', $stage_id)
                    ->where('st_process_action.action_status', 0)
                    ->where('st_process_action.action_on', 1)
                    ->where('st_process_action.action_mark', 0)
                    ->select('st_process_action.*')
                    ->get();
                $j = $j + count($qc_dataRed);
            }
            $arr_data_green = $j;


            $k = 0;
            foreach ($qc_data as $key => $row) {

                $qc_dataRed = DB::table('st_process_action')
                    ->where('st_process_action.ticket_id', $row->form_id)
                    ->where('st_process_action.stage_id', $stage_id)
                    ->where('st_process_action.action_status', 0)
                    ->where('st_process_action.action_on', 1)
                    ->where('st_process_action.action_mark', 1)
                    ->select('st_process_action.*')
                    ->get();
                $k = $k + count($qc_dataRed);
            }
            $arr_data_red = $k;


            $flight = OrderStageCountNew::updateOrCreate(
                ['stage_id' => $stepcode_insert],
                [
                    'stage_id' => $stepcode_insert,
                    'stage_name' => $stage_name_insert,
                    //'stage_name' => $stage_name_insert,
                    'red_count' => $arr_data_red,
                    'green_count' => $arr_data_green,
                    'update_at' => date('Y-m-d H:i:s')
                ]
            );

            //adding data to database
        }

        //-------------------------------------------
    }
    public static function setOrderStagesForApi()
    {
        $step_data_arr = OPDays::get();
        foreach ($step_data_arr as $key => $row) {
            $stage_id = $row->order_step;
            $stepcode_insert = $row->step_code;
            $stage_name_insert = $row->process_name;

            $data_out = OPDays::where('order_step', $stage_id)->first();
            $daystoDone = $data_out->process_days;
            $today = date('Y-m-d H:i:s');
            //=======code for update color in order master============
            $arr_datas = DB::table('order_master')
                ->join('qc_forms', 'order_master.form_id', '=', 'qc_forms.form_id')
                ->where('order_master.order_statge_id', $data_out->step_code)
                ->where('order_master.action_status', 0)
                ->where('qc_forms.is_deleted', 0)
                ->select('order_master.*')
                ->get();



            foreach ($arr_datas as $key => $arr_data) {
                $to = Carbon::createFromFormat('Y-m-d H:i:s', $arr_data->expected_date);
                $from = Carbon::createFromFormat('Y-m-d H:i:s', $today);
                $diff_in_days = $from->diffInDays($to);
                $date = new Carbon;
                if ($date > $arr_data->expected_date) {
                    OrderMaster::where('form_id', $arr_data->form_id)
                        ->update(['action_mark' => 0]); //red
                } else {
                    OrderMaster::where('form_id', $arr_data->form_id)
                        ->update(['action_mark' => 1]); //green

                }
            }

            //=======code for update color in order master===stop=========
            //=======code for update order stages===start=========
            $qc_data = DB::table('qc_forms')
                ->where('qc_forms.is_deleted', 0)
                ->where('qc_forms.dispatch_status', 1)
                ->select('qc_forms.*')
                ->get();

            $j = 0;
            foreach ($qc_data as $key => $row) {

                $qc_dataRed = DB::table('order_master')
                    ->where('order_master.form_id', $row->form_id)
                    ->where('order_master.order_statge_id', $data_out->step_code)
                    ->where('order_master.action_status', 0)
                    ->where('order_master.action_mark', 1)
                    ->select('order_master.*')
                    ->get();
                $j = $j + count($qc_dataRed);
            }

            $arr_data_green = $j;

            $qc_data = DB::table('qc_forms')
                ->where('qc_forms.is_deleted', 0)
                ->where('qc_forms.dispatch_status', 1)
                ->select('qc_forms.*')
                ->get();
            $i = 0;
            foreach ($qc_data as $key => $row) {

                $qc_dataRed = DB::table('order_master')
                    ->where('order_master.form_id', $row->form_id)
                    ->where('order_master.order_statge_id', $data_out->step_code)
                    ->where('order_master.action_status', 0)
                    ->where('order_master.action_mark', 0)
                    ->select('order_master.*')
                    ->get();
                $i = $i + count($qc_dataRed);
            }
            $arr_data_red = $i;


            //=======code for update order stages===stop=========

            //update or insert
            //  DB::table('order_stages_count')
            //  ->updateOrInsert(
            //      [
            //          'stage_id' => $stepcode_insert,
            //          'red_count' => $arr_data_red,
            //          'green_count' => $arr_data_green,
            //          'update_at' => date('Y-m-d H:i:s')
            //      ],
            //      ['stage_id' => $stepcode_insert]
            //  );

            $flight = OrderStageCount::updateOrCreate(
                ['stage_id' => $stepcode_insert],
                [
                    'stage_id' => $stepcode_insert,
                    'stage_name' => $stage_name_insert,
                    'red_count' => $arr_data_red,
                    'green_count' => $arr_data_green,
                    'update_at' => date('Y-m-d H:i:s')
                ]
            );
            //update or insert



        }

        //update for purchase
        $qcdata_arrs = DB::table('qc_bo_purchaselist')->where('is_deleted', 0)->where('dispatch_status', 1)->distinct()->get(['form_id']);
        $iaj = 0;
        foreach ($qcdata_arrs as $key => $qcdata_arr) {
            $data = AyraHelp::checkPurchaeStageIsDone($qcdata_arr->form_id);
            if (!$data) {
                $iaj++;
            }
        }

        OrderStageCount::where('stage_id', 'PURCHASE_PM')
            ->update(['red_count' => $iaj]);
        //update for purchase
    }

    public static function UpdatedByUpdatedOnOrderMaster($form_id)
    {
        OrderMaster::where('form_id', $form_id)
            ->update(['update_by' => Auth::user()->id, 'update_on' => date('Y-m-d H:i:s')]); //green

    }


    public static function getOrderStuckStatusByStageV1($stage_id)
    {
        $mydata_arr = OrderStageCountNew::where('id', $stage_id)->first();
        return $mydata_arr;
    }


    public static function getOrderStuckStatusByStage($stage_id)
    {
        $mydata_arr = OrderStageCount::where('id', $stage_id)->first();
        return $mydata_arr;
    }
    public static function getOrderStuckStatusByStageYestarday($stage_id)
    {
        $data_outData = OPDays::where('order_step', $stage_id)->first();
        $daystoDone = $data_outData->process_days;
        $stepCode = $data_outData->step_code;

        $yesterday = date("Y-m-d", strtotime('-1 days'));
        $data_out = OPData::where('status', 1)->whereDate('created_at', $yesterday)->get();
        $i = 0;
        foreach ($data_out as $key => $RowData) {
            $form_id = $RowData->order_id_form_id;
            $data_arr = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $stepCode)->where('action_status', 1)->first();
            if ($data_arr !== null) {
                $i++;
            }
        }


        $data = array(
            'stage_name' => $data_outData->process_name,
            'green_count' => $i,
            'red_count' => 0

        );
        return (object) $data;
    }


    public static function getOrderStuckStatusByStageOLD($stage_id)
    {

        $data_out = OPDays::where('order_step', $stage_id)->first();
        $daystoDone = $data_out->process_days;

        //code for red and green status
        $today = date('Y-m-d H:i:s');
        //$today='2019-10-2 10:10:10';
        // $arr_datas=OrderMaster::where('order_statge_id',$data_out->step_code)->where('action_status',0)->get();

        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        if ($user_role == 'Admin' || $user_role == 'Staff') {
            $arr_datas = DB::table('order_master')
                ->join('qc_forms', 'order_master.form_id', '=', 'qc_forms.form_id')
                ->where('order_master.order_statge_id', $data_out->step_code)
                ->where('order_master.action_status', 0)
                ->where('qc_forms.is_deleted', 0)

                ->select('order_master.*')
                ->get();
        } else {
            $arr_datas = DB::table('order_master')
                ->join('qc_forms', 'order_master.form_id', '=', 'qc_forms.form_id')
                ->where('order_master.order_statge_id', $data_out->step_code)
                ->where('order_master.action_status', 0)
                ->where('qc_forms.is_deleted', 0)

                ->where('qc_forms.created_by', Auth::user()->id)
                ->select('order_master.*')
                ->get();
        }

        foreach ($arr_datas as $key => $arr_data) {

            $to = Carbon::createFromFormat('Y-m-d H:i:s', $arr_data->expected_date);
            $from = Carbon::createFromFormat('Y-m-d H:i:s', $today);
            $diff_in_days = $from->diffInDays($to);

            $date = new Carbon;
            if ($date > $arr_data->expected_date) {
                OrderMaster::where('form_id', $arr_data->form_id)
                    ->update(['action_mark' => 0]); //red
            } else {
                OrderMaster::where('form_id', $arr_data->form_id)
                    ->update(['action_mark' => 1]); //green

            }
        }

        //code for red and green status

        if ($user_role == 'Admin' || $user_role == 'Staff') {

            // $arr_data_green = DB::table('order_master')
            // ->join('qc_forms', 'order_master.form_id', '=', 'qc_forms.form_id')
            // ->where('order_master.order_statge_id',$data_out->step_code)
            // ->where('order_master.action_status',0)
            // ->where('order_master.action_mark',1)
            // ->where('qc_forms.is_deleted',0)
            // ->select('order_master.*')
            // ->get();

            $qc_data = DB::table('qc_forms')
                ->where('qc_forms.is_deleted', 0)
                ->where('qc_forms.dispatch_status', 1)
                ->select('qc_forms.*')
                ->get();

            //$posts = Post::all();
            //Redis::set('posts.all', $posts);

            $j = 0;
            foreach ($qc_data as $key => $row) {


                $qc_dataRed = DB::table('order_master')
                    ->where('order_master.form_id', $row->form_id)
                    ->where('order_master.order_statge_id', $data_out->step_code)
                    ->where('order_master.action_status', 0)
                    ->where('order_master.action_mark', 1)
                    ->select('order_master.*')
                    ->get();
                $j = $j + count($qc_dataRed);
            }

            $arr_data_green = $j;


            // $arr_data_red = DB::table('order_master')
            // ->join('qc_forms', 'order_master.form_id', '=', 'qc_forms.form_id')
            // ->where('order_master.order_statge_id',$data_out->step_code)
            // ->where('order_master.action_status',0)
            // ->where('order_master.action_mark',0)
            // ->where('qc_forms.is_deleted',0)

            // ->select('order_master.*')
            // ->get();

            $qc_data = DB::table('qc_forms')
                ->where('qc_forms.is_deleted', 0)
                ->where('qc_forms.dispatch_status', 1)
                ->select('qc_forms.*')
                ->get();
            $i = 0;
            foreach ($qc_data as $key => $row) {


                $qc_dataRed = DB::table('order_master')
                    ->where('order_master.form_id', $row->form_id)
                    ->where('order_master.order_statge_id', $data_out->step_code)
                    ->where('order_master.action_status', 0)
                    ->where('order_master.action_mark', 0)
                    ->select('order_master.*')
                    ->get();
                $i = $i + count($qc_dataRed);
            }
            $arr_data_red = $i;
        } else {



            /*
        $arr_data_red = DB::table('order_master')
        ->join('qc_forms', 'order_master.form_id', '=', 'qc_forms.form_id')
        ->where('order_master.order_statge_id',$data_out->step_code)
        ->where('order_master.action_status',0)
        ->where('order_master.action_mark',0)
        ->where('qc_forms.is_deleted',0)

        ->where('qc_forms.created_by',Auth::user()->id)
        ->select('order_master.*')
        ->get();

             $arr_data_green = DB::table('order_master')
            ->join('qc_forms', 'order_master.form_id', '=', 'qc_forms.form_id')
            ->where('order_master.order_statge_id',$data_out->step_code)
            ->where('order_master.action_status',0)
            ->where('order_master.action_mark',1)
            ->where('qc_forms.is_deleted',0)

            ->where('qc_forms.created_by',Auth::user()->id)
            ->select('order_master.*')
            ->get();
            */
            $qc_data = DB::table('qc_forms')
                ->where('qc_forms.is_deleted', 0)
                ->where('qc_forms.dispatch_status', 1)
                ->where('qc_forms.created_by', Auth::user()->id)
                ->select('qc_forms.*')
                ->get();
            $j = 0;
            foreach ($qc_data as $key => $row) {


                $qc_dataRed = DB::table('order_master')
                    ->where('order_master.form_id', $row->form_id)
                    ->where('order_master.order_statge_id', $data_out->step_code)
                    ->where('order_master.action_status', 0)
                    ->where('order_master.action_mark', 1)
                    ->select('order_master.*')
                    ->get();
                $j = $j + count($qc_dataRed);
            }

            $arr_data_green = $j;

            $qc_data = DB::table('qc_forms')
                ->where('qc_forms.is_deleted', 0)
                ->where('qc_forms.dispatch_status', 1)
                ->where('qc_forms.created_by', Auth::user()->id)
                ->select('qc_forms.*')
                ->get();
            $i = 0;
            foreach ($qc_data as $key => $row) {


                $qc_dataRed = DB::table('order_master')
                    ->where('order_master.form_id', $row->form_id)
                    ->where('order_master.order_statge_id', $data_out->step_code)
                    ->where('order_master.action_status', 0)
                    ->where('order_master.action_mark', 0)
                    ->select('order_master.*')
                    ->get();
                $i = $i + count($qc_dataRed);
            }
            $arr_data_red = $i;
        }


        // $arr_data_green=OrderMaster::where('order_statge_id',$data_out->step_code)->where('action_status',0)->where('action_mark',1)->get();
        //$arr_data_red=OrderMaster::where('order_statge_id',$data_out->step_code)->where('action_status',0)->where('action_mark',0)->get();


        $mydatra = array(
            'statge_name' => optional($data_out)->process_name,
            'step_code' => optional($data_out)->step_code,
            'green_count' => $arr_data_green,
            'red_count' => $arr_data_red

        );


        return $mydatra;
    }
    public static function checkOrderMasterDataDuplicte($form_id, $stepCode)
    {
        $orderdata = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $stepCode)->where('action_status', 1)->first();

        if ($orderdata == null) {
            return 0; //ok can insert
        } else {
            return 1; //no can not insert alreay have
        }
    }
    public static function PurchaseStageCount($step_id, $puriD)
    {

        if ($step_id == 2) {
        } else {

            $data_arr = QC_BOM_Purchase::where('status', $step_id)->where('is_deleted', 0)->where('dispatch_status', 1)->get();
            return count($data_arr);
        }
    }
    public static function ScriptForPurchaseListReady()
    {
        //QC_BOM_Purchase
        $qc_data = QCFORM::where('is_deleted', 0)->where('dispatch_status', 1)->where('artwork_status', 1)->get();
        foreach ($qc_data as $key => $row) {

            $bom_data = QCBOM::where('form_id', $row->form_id)->get();

            foreach ($bom_data as $bomkey => $bomRow) {

                if ($bomRow->bom_from == 'N/A' || $bomRow->bom_from == 'FromClient' || $bomRow->bom_from == 'From Client') {
                } else {
                    //print_r($bomRow->m_name);
                    if (!empty($bomRow->m_name)) {
                        $pu_data = QC_BOM_Purchase::where('form_id', $row->form_id)->where('material_name', $bomRow->m_name)->first();
                        if ($pu_data == null) {
                            $bompObj = new QC_BOM_Purchase;
                            $bompObj->order_no = 3;
                            $bompObj->order_id = $row->order_id;
                            $bompObj->sub_order_index = $row->subOrder;
                            $bompObj->order_name = optional($row)->brand_name;
                            $bompObj->order_cat = optional($bomRow)->bom_cat;
                            $bompObj->material_name = optional($bomRow)->m_name;
                            $bompObj->qty = optional($bomRow)->qty;
                            $bompObj->created_by = optional($row)->created_by;
                            $bompObj->form_id = optional($row)->form_id;
                            $bompObj->dispatch_status = 1;
                            $bompObj->created_at = $row->created_at;
                            $bompObj->is_deleted = 0;
                            $bompObj->save();
                        }
                    }
                }
            }
        }
        //update status with backup
        $pur_dataBKP = DB::table('qc_bo_purchaselist_BKP')->get();
        foreach ($pur_dataBKP as $keybp => $rowbkp) {
            $pu_databkp = QC_BOM_Purchase::where('form_id', $rowbkp->form_id)->where('material_name', $rowbkp->material_name)->first();
            if ($pu_databkp != null) {

                // QC_BOM_Purchase::where('form_id', $rowbkp->form_id)
                // ->where('material_name', $rowbkp->material_name)
                // ->update(['status' => $rowbkp->status]);

            }
        }
    }

    public static function ScriptForStartDefaultNEW()
    {
        $qc_datas = QCFORM::where('is_deleted', 0)->where('dispatch_status', 1)->get();
        $i = 0;
        $c_code = 'completed';
        $diff_data = '1 second after';
        foreach ($qc_datas as $key => $qc_data) {
            if ($qc_data->order_type == 'Bulk') {

                $opdObj = new OPData;
                $opdObj->order_id_form_id = $qc_data->form_id; //formid and order id
                $opdObj->step_id = 1;
                $opdObj->expected_date = date('Y-m-d');
                $opdObj->remaks = 'auto start by script';
                $opdObj->created_by = $qc_data->created_by;
                $opdObj->assign_userid = 0;
                $opdObj->status = 1;
                $opdObj->step_status = 0;
                $opdObj->color_code = $c_code;
                $opdObj->diff_data = $diff_data;
                $opdObj->save();

                //ordermaster
                $mstOrderObj = new OrderMaster;
                $mstOrderObj->form_id = $qc_data->form_id;
                $mstOrderObj->assign_userid = 0;
                $mstOrderObj->order_statge_id = 'ART_WORK_RECIEVED';
                $mstOrderObj->assigned_by = $qc_data->created_by;
                $mstOrderObj->assigned_on = date('Y-m-d');
                $mstOrderObj->expected_date = date('Y-m-d');
                $mstOrderObj->action_status = 1;
                $mstOrderObj->completed_on = date('Y-m-d');
                $mstOrderObj->action_mark = 1;
                $mstOrderObj->assigned_team = 2; //sales user
                $mstOrderObj->save();

                $mstOrderObj = new OrderMaster;
                $mstOrderObj->form_id = $qc_data->form_id;
                $mstOrderObj->assign_userid = 0;
                $mstOrderObj->order_statge_id = 'PRODUCTION';
                $mstOrderObj->assigned_by = $qc_data->created_by;
                $mstOrderObj->assigned_on = date('Y-m-d');
                $mstOrderObj->expected_date = date('Y-m-d');
                $mstOrderObj->action_status = 0;
                $mstOrderObj->completed_on = date('Y-m-d');
                $mstOrderObj->action_mark = 1;
                $mstOrderObj->assigned_team = 2; //sales user
                $mstOrderObj->save();
                //ordermaster

            } else {
                if ($qc_data->order_repeat == 1) {
                    //ordermaster
                    $mstOrderObj = new OrderMaster;
                    $mstOrderObj->form_id = $qc_data->form_id;
                    $mstOrderObj->assign_userid = 0;
                    $mstOrderObj->order_statge_id = 'ART_WORK_RECIEVED';
                    $mstOrderObj->assigned_by = $qc_data->created_by;
                    $mstOrderObj->assigned_on = date('Y-m-d');
                    $mstOrderObj->expected_date = date('Y-m-d');
                    $mstOrderObj->action_status = 0;
                    $mstOrderObj->completed_on = date('Y-m-d');
                    $mstOrderObj->action_mark = 1;
                    $mstOrderObj->assigned_team = 2; //sales user
                    $mstOrderObj->save();

                    //ordermaster

                }
                if ($qc_data->order_repeat == 2) {

                    $opdObj = new OPData;
                    $opdObj->order_id_form_id = $qc_data->form_id; //formid and order id
                    $opdObj->step_id = 1;
                    $opdObj->expected_date = date('Y-m-d');
                    $opdObj->remaks = 'auto start by script';
                    $opdObj->created_by = $qc_data->created_by;
                    $opdObj->assign_userid = 0;
                    $opdObj->status = 1;
                    $opdObj->step_status = 0;
                    $opdObj->color_code = $c_code;
                    $opdObj->diff_data = $diff_data;
                    $opdObj->save();

                    //ordermaster
                    $mstOrderObj = new OrderMaster;
                    $mstOrderObj->form_id = $qc_data->form_id;
                    $mstOrderObj->assign_userid = 0;
                    $mstOrderObj->order_statge_id = 'ART_WORK_RECIEVED';
                    $mstOrderObj->assigned_by = $qc_data->created_by;
                    $mstOrderObj->assigned_on = date('Y-m-d');
                    $mstOrderObj->expected_date = date('Y-m-d');
                    $mstOrderObj->action_status = 1;
                    $mstOrderObj->completed_on = date('Y-m-d');
                    $mstOrderObj->action_mark = 1;
                    $mstOrderObj->assigned_team = 2; //sales user
                    $mstOrderObj->save();

                    $mstOrderObj = new OrderMaster;
                    $mstOrderObj->form_id = $qc_data->form_id;
                    $mstOrderObj->assign_userid = 0;
                    $mstOrderObj->order_statge_id = 'PURCHASE_LABEL_BOX';
                    $mstOrderObj->assigned_by = $qc_data->created_by;
                    $mstOrderObj->assigned_on = date('Y-m-d');
                    $mstOrderObj->expected_date = date('Y-m-d');
                    $mstOrderObj->action_status = 0;
                    $mstOrderObj->completed_on = date('Y-m-d');
                    $mstOrderObj->action_mark = 1;
                    $mstOrderObj->assigned_team = 2; //sales user
                    $mstOrderObj->save();
                    //ordermaster


                }
            }
        }
        //end of foreach




    }
    public static function ScriptForStartDefault()
    {
        $qc_datas = QCFORM::get();
        foreach ($qc_datas as $key => $qc_data) {

            if ($qc_data->order_type == 'Bulk') {

                OrderMaster::where('form_id', $qc_data->form_id)->whereNotIn('order_statge_id', ['ART_WORK_RECIEVED', 'PRODUCTION'])->delete();
                OrderMaster::where('form_id', $qc_data->form_id)
                    ->where('order_statge_id', 'PRODUCTION')
                    ->update([
                        'action_status' => 0,
                    ]);
                OPData::where('order_id_form_id', $qc_data->form_id)->whereNotIn('step_id', [1])->delete();
            } else {
                if ($qc_data->order_repeat == 1) {
                    OrderMaster::where('form_id', $qc_data->form_id)->whereNotIn('order_statge_id', ['ART_WORK_RECIEVED'])->delete();
                    //OrderMaster::where('form_id',$qc_data->form_id)->delete();

                    OrderMaster::where('form_id', $qc_data->form_id)
                        ->where('order_statge_id', 'ART_WORK_RECIEVED')
                        ->update([
                            'action_status' => 0,
                        ]);
                    OPData::where('order_id_form_id', $qc_data->form_id)->delete();
                }
                if ($qc_data->order_repeat == 2) {
                    OrderMaster::where('form_id', $qc_data->form_id)->whereNotIn('order_statge_id', ['ART_WORK_RECIEVED', 'PURCHASE_LABEL_BOX'])->delete();
                    OrderMaster::where('form_id', $qc_data->form_id)
                        ->where('order_statge_id', 'PURCHASE_LABEL_BOX')
                        ->update([
                            'action_status' => 0,
                        ]);
                    OPData::where('order_id_form_id', $qc_data->form_id)->whereNotIn('step_id', [1])->delete();
                }
            }
        }
    }


    public static function completePreviousStageDone($form_id, $stepid)
    {

        $qc_data = AyraHelp::getQCFormDate($form_id);

        //  print_r($qc_data);
        if ($qc_data->order_type == 'Bulk') {
            // print_r($qc_data);
            $opd_arrs = OPDaysBulk::get();

            //===========================
            foreach ($opd_arrs as $key => $opd_arr) {

                if ($opd_arr->order_step <= $stepid) {

                    if ($opd_arr->order_step != 1) {
                        //insert in OPData
                        $checkOrderProcess = AyraHelp::checkOrderProcesDuplicte($form_id, $opd_arr->order_step);
                        if (!$checkOrderProcess) {

                            $mydata_arr = OPData::where('order_id_form_id', $form_id)->where('step_id', $opd_arr->order_step)->first();
                            if ($mydata_arr == null) {
                                $opdObj = new OPData;
                                $opdObj->order_id_form_id = $form_id; //formid and order id
                                $opdObj->step_id = $opd_arr->order_step;
                                $opdObj->expected_date = date('Y-m-d');
                                $opdObj->remaks = 'Auto Completed by with previous statges';
                                $opdObj->created_by = Auth::user()->id;
                                $opdObj->assign_userid = 0;
                                $opdObj->status = 1;
                                $opdObj->step_status = 0;
                                $opdObj->color_code = 'completed';
                                $opdObj->diff_data = '1 second after';
                                $opdObj->save();
                            }
                        }
                        //insert in OPData
                        //insert in OrderMaster
                        $checkOrderProcess = AyraHelp::checkOrderMasterDataDuplicte($form_id, $opd_arr->step_code);
                        if (!$checkOrderProcess) {
                            $myorderMasterData = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $opd_arr->step_code)->first();
                            if ($myorderMasterData != null) {
                                OrderMaster::where('form_id', $form_id)
                                    ->where('order_statge_id', $opd_arr->step_code)
                                    ->update([
                                        'action_status' => 1,
                                    ]);
                            } else {

                                $mymaster_data = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $opd_arr->step_code)->first();
                                if ($mymaster_data == null) {

                                    $mstOrderObj = new OrderMaster;
                                    $mstOrderObj->form_id = $form_id;
                                    $mstOrderObj->assign_userid = 0;
                                    $mstOrderObj->order_statge_id = $opd_arr->step_code;
                                    $mstOrderObj->assigned_by = Auth::user()->id;
                                    $mstOrderObj->assigned_on = date('Y-m-d');
                                    $mstOrderObj->expected_date = date('Y-m-d');
                                    $mstOrderObj->action_status = 1;
                                    $mstOrderObj->completed_on = date('Y-m-d');
                                    $mstOrderObj->action_mark = 1;
                                    $mstOrderObj->assigned_team = 4; //sales user
                                    $mstOrderObj->save();
                                }



                                $checkOrderProcess = AyraHelp::checkOrderMasterDataDuplicte($form_id, $opd_arr->step_code);
                                if (!$checkOrderProcess) {
                                    $nextStageid = $opd_arr->order_step + 1;
                                    $mystage_arr = AyraHelp::getOrderStageCodeByID($nextStageid, $form_id);

                                    $mymaster_data = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $mystage_arr->step_code)->first();
                                    if ($mymaster_data == null) {
                                        $mstOrderObj = new OrderMaster;
                                        $mstOrderObj->form_id = $form_id;
                                        $mstOrderObj->assign_userid = 0;
                                        $mstOrderObj->order_statge_id = $mystage_arr->step_code;
                                        $mstOrderObj->assigned_by = Auth::user()->id;
                                        $mstOrderObj->assigned_on = date('Y-m-d');
                                        $mstOrderObj->expected_date = date('Y-m-d');
                                        $mstOrderObj->action_status = 0;
                                        $mstOrderObj->completed_on = date('Y-m-d');
                                        $mstOrderObj->action_mark = 1;
                                        $mstOrderObj->assigned_team = 4; //sales user
                                        $mstOrderObj->save();
                                    }
                                }
                            }
                        }
                        //insert in OrderMaster
                    }
                }
            }
            //===================


        } else {
            if ($qc_data->order_repeat == 1) {
                //print_r($qc_data);
                $opd_arrs = OPDays::get(); //private order Repaer
                //===========================
                foreach ($opd_arrs as $key => $opd_arr) {

                    if ($opd_arr->order_step <= $stepid) {

                        if ($opd_arr->order_step != 2) {
                            //insert in OPData
                            $checkOrderProcess = AyraHelp::checkOrderProcesDuplicte($form_id, $opd_arr->order_step);
                            if (!$checkOrderProcess) {
                                $mydata_arr = OPData::where('order_id_form_id', $form_id)->where('step_id', $opd_arr->order_step)->first();
                                if ($mydata_arr == null) {
                                    $opdObj = new OPData;
                                    $opdObj->order_id_form_id = $form_id; //formid and order id
                                    $opdObj->step_id = $opd_arr->order_step;
                                    $opdObj->expected_date = date('Y-m-d');
                                    $opdObj->remaks = 'Auto Completed by with previous statges';
                                    $opdObj->created_by = Auth::user()->id;
                                    $opdObj->assign_userid = 0;
                                    $opdObj->status = 1;
                                    $opdObj->step_status = 0;
                                    $opdObj->color_code = 'completed';
                                    $opdObj->diff_data = '1 second after';
                                    $opdObj->save();
                                }
                            }
                            //insert in OPData
                            //insert in OrderMaster
                            $checkOrderProcess = AyraHelp::checkOrderMasterDataDuplicte($form_id, $opd_arr->step_code);
                            if (!$checkOrderProcess) {
                                $myorderMasterData = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $opd_arr->step_code)->first();
                                if ($myorderMasterData != null) {
                                    OrderMaster::where('form_id', $form_id)
                                        ->where('order_statge_id', $opd_arr->step_code)
                                        ->update([
                                            'action_status' => 1,
                                        ]);
                                } else {

                                    $mymaster_data = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $opd_arr->step_code)->first();
                                    if ($mymaster_data == null) {
                                        $mstOrderObj = new OrderMaster;
                                        $mstOrderObj->form_id = $form_id;
                                        $mstOrderObj->assign_userid = 0;
                                        $mstOrderObj->order_statge_id = $opd_arr->step_code;
                                        $mstOrderObj->assigned_by = Auth::user()->id;
                                        $mstOrderObj->assigned_on = date('Y-m-d');
                                        $mstOrderObj->expected_date = date('Y-m-d');
                                        $mstOrderObj->action_status = 1;
                                        $mstOrderObj->completed_on = date('Y-m-d');
                                        $mstOrderObj->action_mark = 1;
                                        $mstOrderObj->assigned_team = 4; //sales user
                                        $mstOrderObj->save();
                                    }


                                    $checkOrderProcess = AyraHelp::checkOrderMasterDataDuplicte($form_id, $opd_arr->step_code);
                                    if (!$checkOrderProcess) {
                                        $nextStageid = $opd_arr->order_step + 1;
                                        $mystage_arr = AyraHelp::getOrderStageCodeByID($nextStageid, $form_id);

                                        $mymaster_data = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $mystage_arr->step_code)->first();
                                        if ($mymaster_data == null) {
                                            $mstOrderObj = new OrderMaster;
                                            $mstOrderObj->form_id = $form_id;
                                            $mstOrderObj->assign_userid = 0;
                                            $mstOrderObj->order_statge_id = $mystage_arr->step_code;
                                            $mstOrderObj->assigned_by = Auth::user()->id;
                                            $mstOrderObj->assigned_on = date('Y-m-d');
                                            $mstOrderObj->expected_date = date('Y-m-d');
                                            $mstOrderObj->action_status = 0;
                                            $mstOrderObj->completed_on = date('Y-m-d');
                                            $mstOrderObj->action_mark = 1;
                                            $mstOrderObj->assigned_team = 4; //sales user
                                            $mstOrderObj->save();
                                        }
                                    }
                                }
                            }
                            //insert in OrderMaster
                        }
                    }
                }
                //===================
            }
            if ($qc_data->order_repeat == 2) {

                $opd_arrs = OPDaysRepeat::get(); //private order Repaer
                //===========================
                foreach ($opd_arrs as $key => $opd_arr) {
                    if ($opd_arr->order_step <= $stepid) {

                        if ($opd_arr->order_step != 2) {
                            //insert in OPData
                            $checkOrderProcess = AyraHelp::checkOrderProcesDuplicte($form_id, $opd_arr->order_step);
                            if (!$checkOrderProcess) {

                                $mydata_arr = OPData::where('order_id_form_id', $form_id)->where('step_id', $opd_arr->order_step)->first();
                                if ($mydata_arr == null) {
                                    $opdObj = new OPData;
                                    $opdObj->order_id_form_id = $form_id; //formid and order id
                                    $opdObj->step_id = $opd_arr->order_step;
                                    $opdObj->expected_date = date('Y-m-d');
                                    $opdObj->remaks = 'Auto Completed by with previous statges';
                                    $opdObj->created_by = Auth::user()->id;
                                    $opdObj->assign_userid = 0;
                                    $opdObj->status = 1;
                                    $opdObj->step_status = 0;
                                    $opdObj->color_code = 'completed';
                                    $opdObj->diff_data = '1 second after';
                                    $opdObj->save();
                                }
                            }
                            //insert in OPData
                            //insert in OrderMaster
                            $checkOrderProcess = AyraHelp::checkOrderMasterDataDuplicte($form_id, $opd_arr->step_code);
                            if (!$checkOrderProcess) {
                                $myorderMasterData = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $opd_arr->step_code)->first();
                                if ($myorderMasterData != null) {
                                    OrderMaster::where('form_id', $form_id)
                                        ->where('order_statge_id', $opd_arr->step_code)
                                        ->update([
                                            'action_status' => 1,
                                        ]);
                                } else {
                                    $nextStageid = $opd_arr->order_step + 1;
                                    $mystage_arr = AyraHelp::getOrderStageCodeByID($nextStageid, $form_id);

                                    $mymaster_data = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $mystage_arr->step_code)->first();
                                    if ($mymaster_data == null) {
                                        $mstOrderObj = new OrderMaster;
                                        $mstOrderObj->form_id = $form_id;
                                        $mstOrderObj->assign_userid = 0;
                                        $mstOrderObj->order_statge_id = $opd_arr->step_code;
                                        $mstOrderObj->assigned_by = Auth::user()->id;
                                        $mstOrderObj->assigned_on = date('Y-m-d');
                                        $mstOrderObj->expected_date = date('Y-m-d');
                                        $mstOrderObj->action_status = 1;
                                        $mstOrderObj->completed_on = date('Y-m-d');
                                        $mstOrderObj->action_mark = 1;
                                        $mstOrderObj->assigned_team = 4; //sales user
                                        $mstOrderObj->save();
                                    }



                                    $checkOrderProcess = AyraHelp::checkOrderMasterDataDuplicte($form_id, $opd_arr->step_code);
                                    if (!$checkOrderProcess) {
                                        $nextStageid = $opd_arr->order_step + 1;
                                        $mystage_arr = AyraHelp::getOrderStageCodeByID($nextStageid, $form_id);

                                        $mymaster_data = OrderMaster::where('form_id', $form_id)->where('order_statge_id', $mystage_arr->step_code)->first();
                                        if ($mymaster_data == null) {
                                            $mstOrderObj = new OrderMaster;
                                            $mstOrderObj->form_id = $form_id;
                                            $mstOrderObj->assign_userid = 0;
                                            $mstOrderObj->order_statge_id = $mystage_arr->step_code;
                                            $mstOrderObj->assigned_by = Auth::user()->id;
                                            $mstOrderObj->assigned_on = date('Y-m-d');
                                            $mstOrderObj->expected_date = date('Y-m-d');
                                            $mstOrderObj->action_status = 0;
                                            $mstOrderObj->completed_on = date('Y-m-d');
                                            $mstOrderObj->action_mark = 1;
                                            $mstOrderObj->assigned_team = 4; //sales user
                                            $mstOrderObj->save();
                                        }
                                    }
                                }
                            }
                            //insert in OrderMaster
                        }
                    }
                }
                //===================



            }
        }
    }


    public static function getAllStagesData()
    {
        $clients_arr = OPDays::get();
        return $clients_arr;
    }
    public static function getAllStagesDataV1()
    {
        $qcdata_arrs = DB::table('st_process_stages')->where('process_id', 1)->get();

        return $qcdata_arrs;
    }

    public static function getAllStagesLead()
    {
        $qcdata_arrs = DB::table('st_process_stages')->where('process_id', 4)->get();
        return $qcdata_arrs;
    }


    public static function checkOrderProcesDuplicte($form_id, $stepid)
    {
        $orderdata = OPData::where('order_id_form_id', $form_id)->where('step_id', $stepid)->where('status', 1)->first();

        if ($orderdata == null) {
            return 0; //ok can insert
        } else {
            return 1; //no can not insert alreay have
        }
    }
    public static function checkPurchaeStageIsDone($form_id)
    {
        $qcdata_arrs = DB::table('qc_bo_purchaselist')->where('form_id', $form_id)->where('dispatch_status', 1)->get();
        $my_data = array();
        foreach ($qcdata_arrs as $key => $qcdata_arr) {

            $my_data[] = $qcdata_arr->status;
        }
        if (in_array(6, $my_data, true)) {
            return 1; //yes done
        } else {
            return 0; //no done
        }
    }
    public static function getPendingPurchaseStages()
    {
        $qcdata_arrs = DB::table('qc_bo_purchaselist')->where('dispatch_status', 1)->distinct()->get(['form_id']);

        $i = 0;
        foreach ($qcdata_arrs as $key => $qcdata_arr) {
            //print_r($qcdata_arr->form_id);
            $data = AyraHelp::checkPurchaeStageIsDone($qcdata_arr->form_id);
            if (!$data) {
                $i++;
            }
        }
        return $i;
    }


    public static function getOrderMaster($form_id)
    {
        $myorder_arr = OrderMaster::where('form_id', $form_id)->where('action_status', 1)->get();
        return $myorder_arr;
    }
    public static function isBoxLabelFromClient($form_id)
    {
        $data_arrs = QCBOM::where('form_id', $form_id)->get();
        $i = 0;
        foreach ($data_arrs as $key => $data_arr) {
            if ($data_arr->m_name == 'Printed Box' && $data_arr->bom_from == 'From Client') {
                $i++;
            }
            if ($data_arr->m_name == 'Printed Label' && $data_arr->bom_from == 'From Client') {
                $i++;
            }
        }

        if ($i == 2) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function OTPEnableStatus()
    {
        $qcdata_arr = DB::table('bo_settings')->where('id', 1)->first();
        return $qcdata_arr->otp_enable;
    }

    public static function getIPKEY()
    {
        $qcdata_arr = DB::table('otp_key')->where('status', 1)->first();
        return $qcdata_arr->ip_key;
    }
    public static function getCurrentStageByForMID($form_id)
    {
        $qcdata_arr = DB::table('order_master')->where('form_id', $form_id)->where('action_status', 0)->orderby('id', 'desc')->first();
        return $qcdata_arr;
    }

    public static function getQCFormDate($form_id)
    {
        $qcdata_arr = DB::table('qc_forms')->where('form_id', $form_id)->first();
        return $qcdata_arr;
    }

    public static function getDispatchedDataView($form_id)
    {
        $qcdata_arr = DB::table('order_dispatch_data')->where('form_id', $form_id)->get();
        return $qcdata_arr;
    }
    public static function getDispatchedData($form_id)
    {
        //$qcdata_arr = DB::table('order_dispatch_data')->where('form_id',$form_id)->first();

        $mydata = array();
        $qcdata_arr = DB::table('order_dispatch_data')->where('form_id', $form_id)->get();
        foreach ($qcdata_arr as $key => $value) {

            if (isset($value->dispatch_on)) {
                $dispatchedon = $value->dispatch_on;
            } else {
                $dispatchedon = $value->created_at;
            }

            $mydata[] = array(

                'dispatch_on' => $dispatchedon,


            );
        }
        return $mydata;
    }
    public static function getStageNameByCode($step_code)
    {
        $qcdata_arr = DB::table('order_process_days')->where('step_code', $step_code)->first();
        return $qcdata_arr;
    }
    /*
public static function ScriptForOrderUpdateOrder(){
    //need to write code for update order data
    $qcdata_arr = DB::table('qc_forms')->where('is_deleted',0)->where('dispatch_status',1)->get();

    foreach ($qcdata_arr as $key => $qcdata) {
        if($qcdata->order_type=='Bulk'){

            $opdObj=new OPData;
            $opdObj->order_id_form_id=$qcdata->form_id; //formid and order id
            $opdObj->step_id=1;
            $opdObj->expected_date=$qcdata->created_at;
            $opdObj->remaks='auto start';
            $opdObj->created_by=$qcdata->created_by;
            $opdObj->assign_userid=$qcdata->created_by;
            $opdObj->status=1;
            $opdObj->step_status=4;
            $opdObj->color_code='completed';
            $opdObj->diff_data='1 second before';
            $opdObj->save();

        }else{
            if($qcdata->order_type=='Private Label' && $qcdata->order_repeat=='1'){

            }else{
            $opdObj=new OPData;
            $opdObj->order_id_form_id=$qcdata->form_id; //formid and order id
            $opdObj->step_id=1;
            $opdObj->expected_date=$qcdata->created_at;
            $opdObj->remaks='auto start';
            $opdObj->created_by=$qcdata->created_by;
            $opdObj->assign_userid=$qcdata->created_by;
            $opdObj->status=1;
            $opdObj->step_status=4;
            $opdObj->color_code='completed';
            $opdObj->diff_data='1 second before';
            $opdObj->save();
            }
        }
    }
}

public static function ScriptForOrderUpdate(){
    //this code is for  update states data
    $qcdata_arr = DB::table('qc_forms')->where('is_deleted',0)->where('dispatch_status',1)->get();


    foreach ($qcdata_arr as $key => $qcdata) {

          if($qcdata->order_type=='Bulk'){
                $action_start=1;
                $completed_on=$qcdata->created_at;
                $data_out=OPDaysBulk::where('step_code','PRODUCTION')->first();
                $expencted_date= Carbon::parse($qcdata->artwork_start_date)->addDays($data_out->process_days);

                $action_mark=1;
                $mstOrderObj=new OrderMaster;
                $mstOrderObj->form_id =$qcdata->form_id;
                $mstOrderObj->assign_userid =0;
                $mstOrderObj->order_statge_id ='ART_WORK_RECIEVED';
                $mstOrderObj->assigned_by =$qcdata->created_by;
                $mstOrderObj->action_status =$action_start;
                $mstOrderObj->assigned_on =$completed_on;
                $mstOrderObj->completed_on =$completed_on;
                $mstOrderObj->action_mark =$action_mark;
                $mstOrderObj->assigned_team =1;//sales user
                $mstOrderObj->save();

                $mstOrderObj=new OrderMaster;
                $mstOrderObj->form_id =$qcdata->form_id;
                $mstOrderObj->assign_userid =0;
                $mstOrderObj->order_statge_id ='PRODUCTION';
                $mstOrderObj->assigned_by =$qcdata->created_by;
                $mstOrderObj->assigned_on =$completed_on;
                $mstOrderObj->action_status =0;
                $mstOrderObj->completed_on =$completed_on;
                $mstOrderObj->action_mark =$action_mark;
                $mstOrderObj->expected_date =$expencted_date;
                $mstOrderObj->assigned_team =4;//perchase team assign
                $mstOrderObj->save();


          }else{
            if($qcdata->order_type=='Private Label' && $qcdata->order_repeat=='1'){
                $action_start=0;
                $action_mark=1;
                $completed_on=$qcdata->created_at;
                $mstOrderObj=new OrderMaster;
                $mstOrderObj->form_id =$qcdata->form_id;
                $mstOrderObj->assign_userid =0;
                $mstOrderObj->order_statge_id ='ART_WORK_RECIEVED';
                $mstOrderObj->assigned_by =$qcdata->created_by;
                $mstOrderObj->action_status =$action_start;
                $mstOrderObj->assigned_on =$completed_on;
                $mstOrderObj->completed_on =$completed_on;
                $mstOrderObj->action_mark =$action_mark;
                $mstOrderObj->assigned_team =1;//sales user
                $mstOrderObj->save();

            }else{
                $action_start=1;
                $completed_on=$qcdata->created_at;
                $action_mark=1;

                $mstOrderObj=new OrderMaster;
                $mstOrderObj->form_id =$qcdata->form_id;
                $mstOrderObj->assign_userid =0;
                $mstOrderObj->order_statge_id ='ART_WORK_RECIEVED';
                $mstOrderObj->assigned_by =$qcdata->created_by;
                $mstOrderObj->action_status =$action_start;
                $mstOrderObj->assigned_on =$completed_on;
                $mstOrderObj->completed_on =$completed_on;
                $mstOrderObj->action_mark =$action_mark;
                $mstOrderObj->assigned_team =1;//sales user
                $mstOrderObj->save();

                $mstOrderObj=new OrderMaster;
                $mstOrderObj->form_id =$qcdata->form_id;
                $mstOrderObj->assign_userid =0;
                $mstOrderObj->order_statge_id ='ART_WORK_REVIEW';
                $mstOrderObj->assigned_by =$qcdata->created_by;
                $mstOrderObj->assigned_on =$completed_on;
                $mstOrderObj->action_status =0;
                $mstOrderObj->completed_on =$completed_on;
                $mstOrderObj->action_mark =$action_mark;
                $mstOrderObj->assigned_team =1;//sales user
                $mstOrderObj->save();
            }
          }



    }
}
*/

    public static function getOrderStageCodeByID($stage_id, $form_id)
    {
        $qc_data = AyraHelp::getQCFORMData($form_id);

        $order_type = $qc_data->order_type;
        $order_repeat = $qc_data->order_repeat;

        if ($order_type == 'Private Label' && $order_repeat == '1') {
            $data_out = OPDays::where('order_step', $stage_id)->first();
        }
        if ($order_type == 'Private Label' && $order_repeat == '2') {
            $data_out = OPDaysRepeat::where('order_step', $stage_id)->first();
        }
        if ($order_type == 'Bulk') {
            $data_out = OPDaysBulk::where('order_step', $stage_id)->first();
        }


        return $data_out;
    }


    public static function getOrderStageInfoBulk($stage_id)
    {

        $qcdata_arr_get = DB::table('order_process_data')
            ->join('qc_forms', 'order_process_data.order_id_form_id', '=', 'qc_forms.form_id')
            ->where('order_process_data.status', 1)
            ->where('order_process_data.step_id', $stage_id)
            ->where('qc_forms.order_type', 'Bulk')
            ->where('qc_forms.dispatch_status', 1)
            ->select('order_process_data.*', 'qc_forms.*')
            ->get();
        $data_out = OPDaysBulk::where('order_step', $stage_id)->first();
        $data_pendingdata = QCFORM::where('dispatch_status', 1)->where('is_deleted', 0)->where('order_type', 'Bulk')->get();






        $mydatra = array(
            'statge_name' => optional($data_out)->process_name,
            'days_to_done' => optional($data_out)->process_days,
            'countme' => count($qcdata_arr_get),
            'pending_count' => count($data_pendingdata)
        );



        return $mydatra;
    }
    public static function getOrderStageInfoNewPrivateLabel($stage_id)
    {
        $qcdata_arr_get = DB::table('order_process_data')
            ->join('qc_forms', 'order_process_data.order_id_form_id', '=', 'qc_forms.form_id')
            ->where('order_process_data.status', 1)
            ->where('order_process_data.step_id', $stage_id)
            ->where('qc_forms.order_type', 'Private Label')
            ->where('qc_forms.order_repeat', 1)
            ->where('qc_forms.dispatch_status', 1)
            ->select('order_process_data.*', 'qc_forms.*')

            ->get();
        $data_out = OPDays::where('order_step', $stage_id)->first();
        $data_pendingdata = QCFORM::where('dispatch_status', 1)->where('is_deleted', 0)->where('order_repeat', 1)->where('order_type', 'Private Label')->get();

        $mydatra = array(
            'statge_name' => optional($data_out)->process_name,
            'countme' => count($qcdata_arr_get),
            'pending_count' => count($data_pendingdata)
        );


        return $mydatra;
    }
    public static function getOrderStageInfoRepeat($stage_id)
    {
        $qcdata_arr_get = DB::table('order_process_data')
            ->join('qc_forms', 'order_process_data.order_id_form_id', '=', 'qc_forms.form_id')
            ->where('order_process_data.status', 1)
            ->where('order_process_data.step_id', $stage_id)
            ->where('qc_forms.order_type', 'Private Label')
            ->where('qc_forms.order_repeat', 2)
            ->where('qc_forms.dispatch_status', 1)
            ->select('order_process_data.*', 'qc_forms.*')
            ->get();
        $data_out = OPDaysRepeat::where('order_step', $stage_id)->first();
        $data_pendingdata = QCFORM::where('dispatch_status', 1)->where('order_type', 'Private Label')->where('is_deleted', 0)->where('order_repeat', 2)->get();





        $mydatra = array(
            'statge_name' => optional($data_out)->process_name,
            'countme' => count($qcdata_arr_get),
            'pending_count' => count($data_pendingdata)
        );


        return $mydatra;
    }
    public static function getOrderStageInfo($stage_id, $orderType)
    {

        $qcdata_arr_get = DB::table('order_process_data')
            ->join('qc_forms', 'order_process_data.order_id_form_id', '=', 'qc_forms.form_id')
            ->where('order_process_data.status', 1)
            ->where('order_process_data.step_id', $stage_id)
            ->where('qc_forms.order_type', $orderType)
            ->select('order_process_data.*', 'qc_forms.*')
            ->get();

        switch ($orderType) {
            case 'Bulk':
                $data_out = OPDaysBulk::where('order_step', $stage_id)->first();
                break;
            case 'Private Label':
                $data_out = OPDays::where('order_step', $stage_id)->first();
                break;
            case 'RepeatOrder':
                $data_out = OPDaysRepeat::where('order_step', $stage_id)->first();
                break;
        }


        $mydatra = array(
            'statge_name' => optional($data_out)->process_name,
            'countme' => count($qcdata_arr_get)
        );
        return $mydatra;
    }

    public static function getBOMScript()
    {

        $qcdata_arr = DB::table('temp_bom_1')->where('from_what', 'Order')->get();
        foreach ($qcdata_arr as $key => $value) {
            //print_r($value);
            $form_id = $value->id;
            $m_name = $value->bom_name;
            $qty = $value->qty;
            $bom_from = $value->from_what;
            $bom_cat = $value->cat;
            $order_id = $value->order_id;
            $part_id = $value->part_id;
            $qc_data = AyraHelp::getQCFORMData($form_id);



            //==============================
            /*
       $qcdata_arr_bom = DB::table('qc_forms_bom')->where('form_id',$form_id)->where('m_name',$m_name)->first();
       if($qcdata_arr_bom==null){

        DB::table('qc_forms_bom')->insert(
            [
                'form_id' => $form_id,
                'm_name' => $m_name,
                'qty' => $qty,
                'bom_from' => $bom_from,
                'bom_cat' => $bom_cat,
                ]
        );

       }
       */
            //============================================
            //==============================
            $qcdata_arr_bom = DB::table('qc_bo_purchaselist')->where('form_id', $form_id)->where('material_name', $m_name)->first();
            if ($qcdata_arr_bom == null) {


                DB::table('qc_bo_purchaselist')->insert(
                    [
                        'form_id' => $form_id,
                        'material_name' => $m_name,
                        'qty' => $qty,
                        'order_cat' => $bom_cat,
                        'order_id' => $order_id,
                        'sub_order_index' => $part_id,
                        'order_name' => $qc_data->brand_name,
                        'created_by' => $qc_data->created_by,

                    ]
                );
            }
            //============================================


        }
    }

    public static function getPurchaseOrderListHelper($form_id)
    {
        $qcdata_arr = DB::table('qc_forms_bom')->where('form_id', $form_id)->get();
        foreach ($qcdata_arr as $key => $value) {

            // print_r($value);
        }
    }
    public static function getQCFORMData($form_id)
    {
        $client_arr = DB::table('qc_forms')->where('form_id', $form_id)->first();
        return $client_arr;
    }
    public static function getClientByBrandName($brand_name)
    {
        $client_arr = DB::table('clients')->where('brand', $brand_name)->orwhere('company', $brand_name)->first();
        return $client_arr;
    }

    public static function getBOMItemCategory()
    {
        $client_arr = DB::table('bom_item_category')->get();
        return $client_arr;
    }
    public static function getBOMItemCategoryID($id)
    {
        $client_arr = DB::table('bom_item_category')->where('id', $id)->first();
        return $client_arr;
    }

    public static function getBOMItemMaterial()
    {
        $client_arr = DB::table('bom_item_material')->get();
        return $client_arr;
    }
    public static function getBOMItemMaterialID($id)
    {
        $client_arr = DB::table('bom_item_material')->where('id', $id)->first();
        return $client_arr;
    }
    public static function getBOMItemSize()
    {
        $client_arr = DB::table('bom_item_size')->get();
        return $client_arr;
    }
    public static function getBOMItemSizeID($id)
    {
        $client_arr = DB::table('bom_item_size')->where('id', $id)->first();
        return $client_arr;
    }

    public static function getBOMItemColor()
    {
        $client_arr = DB::table('bom_item_color')->get();
        return $client_arr;
    }
    public static function getBOMItemColorID($id)
    {
        $client_arr = DB::table('bom_item_color')->where('id', $id)->first();
        return $client_arr;
    }
    public static function getBOMItemSape()
    {
        $client_arr = DB::table('bom_item_sape')->get();
        return $client_arr;
    }
    public static function getBOMItemSapeID($id)
    {
        $client_arr = DB::table('bom_item_sape')->where('id', $id)->first();
        return $client_arr;
    }

    public static function getfeedbackAlert($user_id)
    {
        $date_60 = \Carbon\Carbon::today()->subDays(90);
        $date_30 = \Carbon\Carbon::today()->subDays(30);

        $from = $date_60;
        $to = $date_30;



        $getStepDays = DB::table('samples')->where('status', 2)->where('created_by', $user_id)->whereNull('sample_feedback')->whereBetween('sent_on', [$from, $to])->get();

        $data = array(
            'data' => $getStepDays,
            'count' => count($getStepDays),

        );

        return $data;
    }
    public static function samplePendingDispatchData($days)
    {
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        if ($user_role == 'Admin' || $user_role == 'Staff' || $user_role == 'CourierTrk' || $user_role == 'SalesHead') {
            $getStepDays = DB::table('samples')
                //->whereDate('created_at', '>', Carbon::now()->subDays($days))
                ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
                ->where('status', 1)->get();
        } else {
            $getStepDays = DB::table('samples')
                ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
                ->where('created_by', Auth::user()->id)
                ->where('status', 1)->get();
        }

        return $getStepDays;
    }
    public static function samplePendingDispatch($days)
    {
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        if ($user_role == 'Admin' || $user_role == 'Staff' || $user_role == 'SalesHead' || $user_role == 'CourierTrk') {
            $getStepDays = DB::table('samples')
                //->whereDate('created_at', '>', Carbon::now()->subDays($days))
                ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
                ->where('status', 1)->get();
        } else {
            $getStepDays = DB::table('samples')
                ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
                ->where('created_by', Auth::user()->id)
                ->where('status', 1)->get();
        }

        return count($getStepDays);
    }
    public static function purchasePendingTostart($days)
    {
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        if ($user_role == 'Admin' || $user_role == 'Staff' || $user_role == 'CourierTrk') {

            $getStepDays = DB::table('qc_bo_purchaselist')
                //->whereDate('created_at', '>', Carbon::now()->subDays($days))
                ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
                ->where('status', 1)->get();
        } else {
            $getStepDays = DB::table('qc_bo_purchaselist')
                ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
                ->where('created_by', Auth::user()->id)
                ->where('status', 1)->get();
        }

        return count($getStepDays);
    }

    public static function purchasePendingOrderTostartOrderOnly($days)
    {
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        if ($user_role == 'Admin' || $user_role == 'Staff' || $user_role == 'CourierTrk') {

            //$getStepDays = DB::table('qc_bo_purchaselist')

            //->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
            //->where('status','!=',7)->get();


            $qcdata_arrs = DB::table('qc_bo_purchaselist')->where('dispatch_status', 1)->distinct()
                ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())->where('status', '!=', 6)->get(['form_id', 'order_id']);
            //   print_r($qcdata_arrs);
            //   die;
            $i = 0;
            foreach ($qcdata_arrs as $key => $qcdata_arr) {

                $data = AyraHelp::checkPurchaeStageIsDone($qcdata_arr->form_id);
                if (!$data) {
                    $i++;
                }
            }
            $i;
        } else {
            // $getStepDays = DB::table('qc_bo_purchaselist')
            // ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
            // ->where('created_by',Auth::user()->id)
            // ->where('status','!=',7)->get();

            $qcdata_arrs = DB::table('qc_bo_purchaselist')->where('dispatch_status', 1)->distinct()
                ->where('created_by', Auth::user()->id)->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())->where('status', '!=', 7)->get(['form_id']);

            $i = 0;
            foreach ($qcdata_arrs as $key => $qcdata_arr) {

                $data = AyraHelp::checkPurchaeStageIsDone($qcdata_arr->form_id);
                if (!$data) {
                    $i++;
                }
            }
            $i;
        }

        return $i;
    }
    public static function purchasePendingOrderTostart($days)
    {
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        if ($user_role == 'Admin' || $user_role == 'Staff' || $user_role == 'CourierTrk') {

            $getStepDays = DB::table('qc_bo_purchaselist')
                //->whereDate('created_at', '>', Carbon::now()->subDays($days))
                ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
                ->where('status', '!=', 7)->get();
        } else {
            $getStepDays = DB::table('qc_bo_purchaselist')
                ->where('created_at', '<=', Carbon::now()->subDays($days)->toDateTimeString())
                ->where('created_by', Auth::user()->id)
                ->where('status', '!=', 7)->get();
        }

        return count($getStepDays);
    }




    public static function getStepDays($step)
    {
        $getStepDays = DB::table('order_process_days')->where('order_step', $step)->first();
        return $getStepDays;
    }

    public static function getOrderCODE()
    {
        $max_id = QCFORM::where('yr', date('Y'))->where('mo', date('m'))->max('order_index') + 1;
        $uname = 'O';
        $num = $max_id;
        $str_length = 4;
        $prifix = "20-";
        $sid_code = $uname . "#" . $prifix . substr("0000{$num}", -$str_length);
        return $sid_code;
    }
    public static function getOrderCODEIndex()
    {
        $max_id =  QCFORM::where('yr', date('Y'))->where('mo', date('m'))->max('order_index') + 1;
        return $max_id;
    }

    public static function getSampleFeedbackCount($userid, $days)
    {
        $chartDatas = Sample::select([
            DB::raw('DATE(feedback_addedon) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('feedback_addedon', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('created_by', $userid)
            ->where('sample_feedback', 1)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum_type_1 = 0;
        foreach ($chartDatas as $key => $value) {
            $sum_type_1 += $value->count;
        }
        $sum_type_1;

        $chartDatas = Sample::select([
            DB::raw('DATE(feedback_addedon) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('feedback_addedon', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('created_by', $userid)
            ->where('sample_feedback', 2)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum_type_2 = 0;
        foreach ($chartDatas as $key => $value) {
            $sum_type_2 += $value->count;
        }
        $sum_type_2;

        $chartDatas = Sample::select([
            DB::raw('DATE(feedback_addedon) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('feedback_addedon', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('created_by', $userid)
            ->where('sample_feedback', 3)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum_type_3 = 0;
        foreach ($chartDatas as $key => $value) {
            $sum_type_3 += $value->count;
        }
        $sum_type_3;

        $chartDatas = Sample::select([
            DB::raw('DATE(feedback_addedon) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('feedback_addedon', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('created_by', $userid)
            ->where('sample_feedback', 4)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum_type_4 = 0;
        foreach ($chartDatas as $key => $value) {
            $sum_type_4 += $value->count;
        }
        $sum_type_4;
        $data = array(
            'option_1' => $sum_type_1,
            'option_2' => $sum_type_2,
            'option_3' => $sum_type_3,
            'option_4' => $sum_type_4,
        );
        return $data;
    }


    public static function getCountPaymentRecClientupAddedby($userid, $days)
    {

        //   $chartDatas=PaymentRec::sum('rec_amount')
        //   //->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()])
        //   ->where('created_by',$userid)
        //   ->groupBy('date')
        //   ->orderBy('date', 'DESC')
        //   ->get();

        $chartDatas = DB::table("payment_recieved_from_client")
            ->where('created_by', $userid)
            ->where('payment_status', 1)
            ->get()->sum("rec_amount");



        return $chartDatas;
    }


    public static function getCountClientupAddedby($userid, $days)
    {
        $chartDatas = Client::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('added_by', $userid)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }
    public static function getCountSampleAddedby($userid, $days)
    {
        $chartDatas = Sample::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('created_by', $userid)
            ->where('status', 2)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }
    public static function getCountSampleFeedbackAddedby($userid, $days)
    {
        $chartDatas = Sample::select([
            DB::raw('DATE(feedback_addedon) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('feedback_addedon', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('created_by', $userid)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }
    //---------------------------------
    public static function getAssignedLead($userid, $days)
    {
        $chartDatas = ClientNote::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('user_id', $userid)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }

    public static function getIrrelevantLead($userid, $days)
    {
        $chartDatas = ClientNote::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('user_id', $userid)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }

    public static function getFreshArrived($userid, $days)
    {
        $chartDatas = ClientNote::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('user_id', $userid)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }

    //---------------------------------
    public static function getCountNotedAddedby($userid, $days)
    {
        $chartDatas = ClientNote::select([
            DB::raw('DATE(created_at) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('user_id', $userid)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }
    public static function getCountFollowupAddedby($userid, $days)
    {
        $chartDatas = Client::select([
            DB::raw('DATE(follow_date) AS date'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->whereBetween('follow_date', [Carbon::now()->subDays($days), Carbon::now()])
            ->where('added_by', $userid)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get();

        $sum = 0;
        foreach ($chartDatas as $key => $value) {
            $sum += $value->count;
        }
        return $sum;
    }

    public static function getOTP()
    {

        $env = strtoupper(trim(config('app.env')));
        if (in_array($env, ['DEV', 'DEVELOPMENT'])) {
            $otp = '11111';
        } else {
            $otp = rand(10000, 99999);
        }

        return $otp;
    }
    public static function StockAvailabilitywithItemIDQTY($item_id, $qty)
    {
        $item_stock = DB::table('item_stock')->where('item_id', $item_id)->first();
        $curr_in_stock_qty = $item_stock->qty;
        if ($curr_in_stock_qty > $qty) {
            $stock_flag = 1; //available
        } else {
            $stock_flag = 2; //not availble
        }
        return $stock_flag;
    }
    public static function getMissedFollowup($user_id)
    {
        $date = \Carbon\Carbon::today()->subDays(1);
        $clients = Client::where('added_by', $user_id)->where('follow_date', '<', date($date))->get();
        $data = array(
            'client_count' => count($clients),
            'client_data' => $clients,
        );
        return $data;
    }

    public static function getAlarm($user_id = NULL)
    {
        if (empty($user_id)) {
            $alert_alert = DB::table('user_activity')->take(10)->get();
        } else {
            $alert_alert = DB::table('user_activity')->where('user_id', $user_id)->take(10)->get();
        }

        return $alert_alert;
    }

    public static function ClinentInfoByOrderID($id)
    {
        $items_orders = DB::table('orders')->where('order_index', $id)->first();
        $client_id = $items_orders->client_id;
        $client_arr = AyraHelp::getClientbyid($client_id);
        return $client_arr;
    }

    public static function getVendors($id)
    {
        $items_master = DB::table('vendors')->where('id', $id)->first();

        return $items_master;
    }
    public static function getAllVendors()
    {
        $items_master = DB::table('vendors')->get();

        return $items_master;
    }
    public static function getUserAcessListByUserId($userid)
    {
        $items_master = DB::table('users_access')->where('access_by', $userid)->get();
        return $items_master;
    }
    public static function getRESPUR($id)
    {
        $items_master = DB::table('orders_items_material')->where('id', $id)->first();
        return $items_master;
    }

    public static function getBOMconfirmStatus($id)
    {
        $items_master = DB::table('orders_items_material')->where('order_item_id', $id)->first();
        return $items_master;
    }
    public static function getReqOrders($id)
    {

        $items_master = DB::table('orders_req_items')->where('id', $id)->first();
        return $items_master;
    }


    public static function getPIDCode()
    {
        $max_id = PurchaseOrders::max('purchase_index') + 1;

        $num = $max_id;
        $str_length = 4;
        $sid_code = "PR-" . substr("0000{$num}", -$str_length);
        return $sid_code;
    }


    //get last 30 days sample list
    //get item stok by item_id
    public static function getStockQTYbyItemID($item_id)
    {
        $items_master = DB::table('item_stock')->where('item_id', $item_id)->first();
        return $items_master;
    }

    public static function getSample30Days()
    {
        $users = DB::table("users")
            ->select('*')
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->get();
        return $users;
    }
    //this is used to get item category
    public static function getItemCategory($id_SKU = NULL)
    {
        if ($id_SKU) {
            $items_category = DB::table('item_category')->where('cat_id', $id_SKU)->get();
        } else {
            $items_category = DB::table('item_category')->get();
        }


        return $items_category;
    }
    public static function getSampleFeedback()
    {
        $items_master = DB::table('sample_feedbacktype')->get();
        return $items_master;
    }

    //this is used to get att_values
    public static function getMasterItemsType()
    {
        $items_master = DB::table('items_master_type')->get();
        return $items_master;
    }
    public static function getStockBYItemID($item_id)
    {
        $items_master = DB::table('item_stock')->where('item_id', $item_id)->first();
        return $items_master;
    }

    public static function getStockReservedByID($item_id)
    {
        $items_master = DB::table('item_stock_entry')->where('item_id', $item_id)->where('purchase_reserve_flag', 1)->where('purchase_reserved_status', 2)->sum('qty');

        $data = DB::table("item_stock_entry")

            ->select(DB::raw("SUM(qty) as count"))
            ->where('item_id', $item_id)
            ->where('purchase_reserve_flag', 1)
            ->where('purchase_reserved_status', 2)
            ->get();

        return $data[0]->count;
    }
    public static function getItemsbyItemID($code_id)
    {

        $items_master = DB::table('items')->where('item_id', $code_id)->first();
        return $items_master;
    }
    public static function getProductItemByid($item_id)
    {

        $items_master_data = DB::table('orders_req_items')->where('id', $item_id)->first();

        return $items_master_data;
    }
    public static function getItemCatbyItemID($code_id)
    {
        $items_master = DB::table('item_category')->where('cat_id', $code_id)->first();
        return $items_master;
    }
    //this is used to get att_values
    public static function getUserName($user_id)
    {
        $user_arr = DB::table('users')->where('id', $user_id)->get();
        return $user_arr[0]->name;
    }
    public static function getUser($user_id)
    {
        $user_arr = DB::table('users')->where('id', $user_id)->first();
        return $user_arr;
    }
    public static function getClientSource()
    {
        $user_arr = DB::table('clients_source')->get();
        return $user_arr;
    }
    public static function getUserPrefix($user_id)
    {
        $user_arr = DB::table('users')->where('id', $user_id)->get();
        return $user_arr[0]->user_prefix;
    }

    public static function getVendorsByadded($user_id)
    {
        $user_arr = DB::table('vendors')->where('created_by', '=', $user_id)->get();
        return $user_arr;
    }

    public static function IsClientHaveOrderList($user_id)
    {

        $user_arr = DB::table('qc_forms')->where('is_deleted', 0)->where('client_id', '=', $user_id)->get();
        return count($user_arr);
    }
    public static function getClientByadded($user_id)
    {
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        if ($user_role == 'Admin' || $user_role == 'CourierTrk' || $user_role == 'Sampler') {
            $user_arr = DB::table('clients')->where('is_deleted', '!=', 1)->get();
        } else {
            //$user_arr = DB::table('clients')->where('is_deleted','!=',1)->where('added_by', $user_id)->get();
            //newcode
            $user_arr = DB::table('clients')
                ->leftJoin('users_access', 'clients.id', '=', 'users_access.client_id')
                ->select('clients.*')
                ->orderBy('clients.id', 'DESC')
                ->where('clients.added_by', $user_id)
                ->where('clients.is_deleted', '!=', 1)
                ->orwhere('users_access.access_to', $user_id)
                ->get();
            //newcode


        }

        return $user_arr;
    }
    public static function getClientbyid($user_id)
    {
        $user_arr = DB::table('clients')->where('is_deleted', '!=', 1)->where('id', $user_id)->first();
        return $user_arr;
    }
    public static function getClientCountbyid($user_id = NULL)
    {
        if ($user_id == null) {

            $user_arr = DB::table('clients')->where('is_deleted', '!=', 1)->get();

            $user_arr_lead = DB::table('clients')->where('group_status', 2)->where('is_deleted', '!=', 1)->get();
            $user_arr_sampling = DB::table('clients')->where('group_status', 4)->where('is_deleted', '!=', 1)->get();
            $user_arr_customer = DB::table('clients')->where('group_status', 5)->where('is_deleted', '!=', 1)->get();
        } else {

            $user_arr = DB::table('clients')->where('is_deleted', '!=', 1)->where('added_by', $user_id)->get();

            $user_arr_lead = DB::table('clients')->where('group_status', 2)->where('is_deleted', '!=', 1)->where('added_by', $user_id)->get();
            $user_arr_sampling = DB::table('clients')->where('group_status', 4)->where('is_deleted', '!=', 1)->where('added_by', $user_id)->get();
            $user_arr_customer = DB::table('clients')->where('group_status', 5)->where('is_deleted', '!=', 1)->where('added_by', $user_id)->get();
        }

        $user_data = array(
            'total' => count($user_arr),
            'lead' => count($user_arr_lead),
            'sampling' => count($user_arr_sampling),
            'customer' => count($user_arr_customer),
        );
        return $user_data;
    }
    public static function getSampleCountbyid($user_id = NULL)
    {
        if ($user_id == null) {

            $user_arr = DB::table('samples')->get();

            $user_arr_new = DB::table('samples')->where('status', 1)->get();
            $user_arr_sent = DB::table('samples')->where('status', 2)->get();
            $feedback_addedon = DB::table('samples')->where('status', 2)->whereNotNull('feedback_addedon')->get();
        } else {

            $user_arr = DB::table('samples')->where('created_by', $user_id)->get();

            $user_arr_new = DB::table('samples')->where('status', 1)->where('created_by', $user_id)->get();
            $user_arr_sent = DB::table('samples')->where('status', 2)->where('created_by', $user_id)->get();
            $feedback_addedon = DB::table('samples')->where('status', 2)->where('created_by', $user_id)->whereNotNull('feedback_addedon')->get();
        }

        $user_data = array(
            'total' => count($user_arr),
            'new' => count($user_arr_new),
            'sent' => count($user_arr_sent),
            'feedback_addedon' => count($feedback_addedon),

        );
        return $user_data;
    }

    public static function getAttr()
    {
        $getAttr = DB::table('bo_attr')->get();
        return $getAttr;
    }
    public static function getCity()
    {
        $getAttr = DB::table('country_cities')->select('id', 'name')->get();
        return $getAttr;
    }
    public static function getCityByID($id)
    {
        $getAttr = DB::table('country_cities')->select('id', 'name')->where('id', $id)->first();
        return $getAttr;
    }
    public static function getCountryByID($id)
    {
        $getAttr = DB::table('countries')->select('id', 'iso_code_3')->where('id', $id)->first();
        return $getAttr;
    }


    //this is used to get name of user
    public static function getEmail($user_id)
    {
        $user = DB::table('users')->where('id', $user_id)->first();

        return (isset($user->email) ? $user->email : '');
    }
    public static function getCompany($user_id)
    {
        $companys = DB::table('client_company')->where('user_id', $user_id)->first();

        return $companys;
    }
    public static function getCourier()
    {
        $getCourier = DB::table('courier')->get();

        return $getCourier;
    }
    public static function getCouriers($id)
    {
        $getCourier = DB::table('courier')->where('id', $id)->first();

        return $getCourier;
    }
    public static function getCouriersBySamnpleid($id)
    {
        $samples = DB::table('samples')->where('id', $id)->first();

        $getCourier = DB::table('courier')->where('id', $samples->courier_details)->first();

        return $getCourier;
    }

    public static function getClientUsers()
    {
        $user = DB::table('users')->where('created_by', Auth::user()->id)->get();

        return $user;
    }


    public static function getSampleCount($userid)
    {
        $user = DB::table('samples')->where('status', '0')->where('created_by', $userid)->get()->toArray();

        return count($user);
    }
    public static function getUserRole($user_id)
    {
        $clients_arr = User::with('roles')->where('is_deleted', 0)->where('id', $user_id)->get();
        return $clients_arr;
    }
    public static function getSalesAgent()
    {
        $clients_arr = User::where('is_deleted', 0)->whereHas("roles", function ($q) {
            $q->where("name", "SalesUser")->orwhere("name", "Staff")->orwhere("name", "Admin");
        })->get();
        return $clients_arr;
    }
    public static function getSalesAgentAdmin()
    {
        $clients_arr = User::where('is_deleted', 0)->whereHas("roles", function ($q) {
            $q->where("name", "SalesUser")->orwhere("name", "Admin")->orwhere("name", "SalesHead");
        })->get();
        return $clients_arr;
    }

    public static function getSalesAgentOnly()
    {
        $clients_arr = User::where('is_deleted', 0)->whereHas("roles", function ($q) {
            $q->where("name", "SalesUser");
        })->get();
        return $clients_arr;
    }

    public static function getSalesAgentOnlyWITHSTAFF()
    {
        $clients_arrS = User::where('is_deleted', 0)->whereHas("roles", function ($q) {
            $q->where("name", "SalesUser")->orwhere("name", "Staff");
        })->get();
        return $clients_arrS;
    }

    public static function getAllClients()
    {
        $clients_arr = User::where('is_deleted', '!=', 1)->whereHas("roles", function ($q) {
            $q->where("name", "Client");
        })->get();
        return $clients_arr;
    }
    public static function getClientByAuth()
    {
        $clients_arr = User::where('created_by', Auth::user()->id)->where('is_deleted', '!=', 1)->whereHas("roles", function ($q) {
            $q->where("name", "Client");
        })->get();
        return $clients_arr;
    }
    public static function getSampleIDCode()
    {

        $max_id = Sample::where('yr', date('Y'))->where('mo', date('m'))->max('sample_index') + 1;

        $uname = strtoupper(AyraHelp::getUserPrefix(Auth::user()->id));
        $uname = substr($uname, 0, 3);
        $num = $max_id;
        $str_length = 4;
        $sid_code = $uname . "-" . substr("0000{$num}", -$str_length);
        return $sid_code;
    }
    public static function getSamples($id)
    {
        $sample = DB::table('samples')->where('id', $id)->get();
        return $sample;
    }
    public static function getTotalRequestFor()
    {
        $users_arr = User::where('is_deleted', '!=', 0)->whereHas("roles", function ($q) {
            $q->where("name", "Client");
        })->get();
        return count($users_arr);
    }



    //this function is used to get baseurl and route path
    public static function getBaseURL()
    {
        return url('/');
    }
    public static function getRouteName()
    {
        $route_arr = explode(url('/') . "/", url()->current());
        if (array_key_exists(1, $route_arr)) {
            return $route_arr[1];
        }
    }
}
