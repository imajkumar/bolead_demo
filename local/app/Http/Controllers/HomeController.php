<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Theme;
use PDF;
use App\ClientNote;
use Illuminate\Database\Eloquent\Model;
use App\Client;

use Khill\Lavacharts\Lavacharts;
use Carbon\Carbon;
use DB;
use Mail;
use App\Helpers\AyraHelp;
use App\QCFORM;
use App\QCBOM;
use App\QCPP;
use App\QC_BOM_Purchase;
use App\OrderMaster;
use App\OPData;
use AWS;

class HomeController extends Controller
{

  public function getClientInfo()
  {
    $mydata = array();
    $order_arr = QCFORM::where('is_deleted', '!=', 1)->get();
    foreach ($order_arr as $key => $value) {
      $client_arr = AyraHelp::getClientByBrandName($value->brand_name);
      $added_arr = AyraHelp::getUser($value->created_by);
      $mydata[] = array(
        'form_id' => $value->form_id,
        'order_id' => $value->order_id,
        'brand_name' => $value->brand_name,
        'email' => optional($client_arr)->email,
        'client_name' => optional($client_arr)->firstname,
        'added_by' => optional($added_arr)->name,
      );
    }
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['data' => $mydata];
    return $theme->scope('sample.print_temp_client', $data)->render();
  }

  public function setSinceFromApiV1()
  {

    $order_arr = DB::table('qc_forms')->where('is_deleted', 1)->where('dispatch_status', 1)->get();

    foreach ($order_arr as $key => $rowData) {
      $fid = $rowData->form_id;
      $data = AyraHelp::getProcessCurrentStage(1, $fid);
      if ($data->stage_id == 1) {
        $data = AyraHelp::getQCFormDate($fid);

        $date = Carbon::parse($data->created_at);
        $now = Carbon::now();
        $diff = $date->diffInDays($now);
      } else {
        $users = DB::table('st_process_action')
          ->where('process_id', '=', 1)
          ->where('ticket_id', '=', $fid)
          ->where('stage_id', '=', $data->stage_id)
          ->first();
        if ($users != null) {
          $date = Carbon::parse($users->created_at);
          $now = Carbon::now();
          $diff = $date->diffInDays($now);
        } else {
          $diff = 0;
        }
      }
      // update 
      echo $diff;
      echo "<br>";
      DB::table('qc_forms')
        ->where('form_id', $fid)
        ->update(['since_from' => $diff]);

      // update 



    }
  } ///////////////////

  //v2
  public function index()
  {

    //echo "<pre>";
  
  //  die;
    //echo AyraHelp::getPhoneCallDuplicate('P','+919582507838');
    //die;

    //AyraHelp::LeadCorrection();
    //AyraHelp::LeadCorrection1();
    //AyraHelp::LeadCorrection2(); //as per current lead need to update statge

    //====================
    //AyraHelp::getMyAllClient();
    //AyraHelp::getMyAllClient2();
    //die;

    //    $data_arr_data = DB::table('indmt_data')
    //    ->join('lead_assign', 'indmt_data.QUERY_ID', '=', 'lead_assign.QUERY_ID')           
    //    ->where('lead_assign.assign_user_id', '=',76) 
    //    ->orderBy('indmt_data.DATE_TIME_RE_SYS','desc')
    //    ->select('indmt_data.*', 'lead_assign.assign_by', 'lead_assign.msg')
    //    ->get();
    // print_r((count($data_arr_data)));
    //    die;

    //$data=AyraHelp::getDuplicateLead();
    //die;
    //$data=AyraHelp::getTodayLeadData();
    //die;

    //AyraHelp::getLeadMissedRun();
    // echo "<pre>";
    // AyraHelp::AssignToRUN();

    // die;

    //nidhibhardwaj791
    //echo "<pre>";
    //echo AyraHelp::getLeadTable();

    //die;


    //  echo  AyraHelp::IsRepeatClientCheck($phone, $email);
    //  die;

    //AyraHelp::getMACAddress();
    //AyraHelp::getActualClientAsNow();
    //AyraHelp::getFreshLead();
    //die;


    //AyraHelp::runLeadDateUpdate();
    //die;

    //AyraHelp::NewOrderScript();
    //AyraHelp::NewOrderScript2(); 

    //AyraHelp::NewOrderScript3(); //insert current stage in temp_action table 
    //AyraHelp::NewOrderScript4(); //this will save data in st_process_action_temp
    //echo "<pre>";
    //AyraHelp::NewPurchaseScript1(); //this will save data in temp_purchase_curr_statge
    //AyraHelp::NewPurchaseScript2(); //this will save data in temp_purchase_curr_statge
    //AyraHelp::NewPurchaseScript3(); //this will save data in temp_purchase_curr_statge
    //AyraHelp::checkArtWorkStated(); //check and delete from purchaselist
    //die;






    // AyraHelp::getStayFromOrder(1429);

    // die;

    // echo "<pre>";

    //  $data=AyraHelp::OldtoNewPurchaseScript();
    //  print_r($data);
    //  die;

    //  $data=AyraHelp::OldtoNewOrderScript();
    //  print_r($data);
    //  die;

    // $data=AyraHelp::getEMPCODE();
    // print_r($data);
    // die;

    // AyraHelp::UpdateSAPCHKLIST();
    // 
    //echo   AyraHelp::getAttenPunch(5);
    //echo   AyraHelp::getAttenPunchEntryTime(5);      





    // die;

    //echo "<pre>";
    //   $data=AyraHelp::getTopClient(10);
    //   print_r($data);
    //  die;
    //$currentMonth = date('m');
    // $currentMonth=8;
    // $datas=QCFORM::where('is_deleted',0)->whereRaw('MONTH(created_at) = ?',[$currentMonth])->distinct('client_id')->pluck('client_id');
    // foreach ($datas as $key => $dataRow) {
    //   print_r($dataRow);

    // }


    // $data=AyraHelp::getProcessCurrentStage(1,1052);
    // print_r($data);
    // die;





    //AyraHelp::getUserCompletedStage(1,1);
    // echo  AyraHelp::getPurcahseStockRecivedOrder(6);
    //die;


    //$data_output=AyraHelp::ScriptForStartDefaultNEW();  
    //QCFORM::query()->truncate();
    //QCBOM::query()->truncate();
    //QC_BOM_Purchase::query()->truncate();
    //QCPP::query()->truncate();
    //OrderMaster::query()->truncate();        
    //OPData::query()->truncate();

    // echo "<pre>";
    // $data=AyraHelp::getfeedbackAlert(40);
    // print_r($data);
    // die;


    //$data_output=AyraHelp::ScriptForPurchaseListReady();  

    // AyraHelp::getAttenCalulation(1);



    //ajacode
    $bo_setting = DB::table('bo_settings')->where('atten_upload_flag', 1)->first();

    if ($bo_setting != null) {

      AyraHelp::getAttenDemo(); //this function is user to filter to table: demo_attn
      AyraHelp::setAttenRowBind(); //this function save data to emp_attendance_data
      DB::table('bo_settings')
        ->where('id', 1)
        ->update(['atten_upload_flag' => 0]);
    }



    //ajcode


    $userRoles = [];
    if (Auth::user()) {   // Check is user logged in
      $user = auth()->user();
      $userRoles = $user->getRoleNames();
      $user_role = $userRoles[0];
    } else {
      $user_role = 'GUEST';
    }
    switch ($user_role) {
      case 'Admin':
        return $this->CoreDashboard();
        break;
      case 'Client':
        return $this->ClinetDashboard();
        break;
      case 'Staff':
        return $this->CoreDashboard();
        break;
      case 'SalesUser':
        return $this->CoreDashboard();
        break;
      case 'CourierTrk':
        return $this->CoreDashboard();
        break;
      case 'Sampler':
        return $this->CoreDashboard();
        break;
      case 'User':
        return $this->UserDashboard();
        break;
      case 'SalesHead':
        return $this->SalesHeadDashboard();
        break;
      default:
        return $this->Front();
        break;
    }
  }
  public function UserDashboard()
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = [
      "name" => "Ajay",

    ];
    return $theme->scope('dash.index', $data)->render();
  }
  public function SalesHeadDashboard()
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = [
      "name" => "Ajay",

    ];
    return $theme->scope('salesHead.index', $data)->render();
  }


  public function CoreDashboard()
  {
    $theme = Theme::uses('corex')->layout('layout');
    $lava = new Lavacharts; // See note below for Laravel


    //code for order values
    $finances_orderValue = $lava->DataTable();

    $finances_orderValue->addDateColumn('Year')
      ->addNumberColumn('Order Value')
      ->setDateTimeFormat('Y-m-d');
    for ($x = 4; $x <= 12; $x++) {
      $d = cal_days_in_month(CAL_GREGORIAN, $x, date('Y'));


      if ($x >= 4) {
        $active_date = "2020-" . $x . "-1";
      } else {
        $active_date = date('Y') . "-" . $x . "-1";
      }
      $data_output = AyraHelp::getOrderValueFilter($x);
      $finances_orderValue->addRow([$active_date, $data_output]);
    }




    $donutchart = \Lava::ColumnChart('FinancesOrderValue', $finances_orderValue, [
      'title' => 'Order Value ',
      'titleTextStyle' => [
        'color'    => '#16426B',
        'fontSize' => 14
      ],

    ]);



    //code for order values


    //=================================================
    $finances = $lava->DataTable();
    $finances->addStringColumn('Year')
      ->addNumberColumn('NOTES')
      ->addNumberColumn('FOLLOWUP')
      ->addNumberColumn('Add Client');
    $sales_arr = AyraHelp::getSalesAgentOnly();
    foreach ($sales_arr as $key => $value) {
      $s_userid = $value->id;
      if ($s_userid == '88') {
      } else {
        $sname = explode(" ", $value->name);
        $notes = AyraHelp::getCountNotedAddedby($s_userid, 30);
        $followups = AyraHelp::getCountFollowupAddedby($s_userid, 30);
        $client_added = AyraHelp::getCountClientupAddedby($s_userid, 30);

        $finances->addRow([strtoupper($sname[0]), $notes, $followups, $client_added]);
      }
    }
    $donutchart = \Lava::ColumnChart('Finances', $finances, [
      'title' => 'Last 30 Days Notes ,Follow Up & Added Client ',
      'titleTextStyle' => [
        'color'    => '#16426B',
        'fontSize' => 14
      ],
      'legend' => [
        'position'    => 'top',
        'maxLines' => 3

      ],

    ]);

    //===================================================
    //=================================================
    $samples = $lava->DataTable();
    $samples->addStringColumn('Year')
      ->addNumberColumn('SAMPLES SENT')
      ->addNumberColumn('FEEDBACK ');
    $sales_arr = AyraHelp::getSalesAgentOnly();
    foreach ($sales_arr as $key => $value) {
      $s_userid = $value->id;
      if ($s_userid == '88') {
      } else {
        $sname = explode(" ", $value->name);
        $notes = AyraHelp::getCountSampleAddedby($s_userid, 30);
        $followups = AyraHelp::getCountSampleFeedbackAddedby($s_userid, 30);
        $samples->addRow([strtoupper($sname[0]), $notes, $followups]);
      }
    }
    $donutchart = \Lava::ColumnChart('SampleFeeback', $samples, [
      'title' => 'Last 30 Days Sample Sent and Feedback ',
      'titleTextStyle' => [
        'color'    => '#16426B',
        'fontSize' => 14
      ],
      'colors' => ['#164252', '#008080'],
      'legend' => [
        'position'    => 'top',
        'maxLines' => 3

      ],
    ]);

    //=================================================== 

    //----------------------------
    $reasons = $lava->DataTable();
    $feed_arr = AyraHelp::getSampleFeedbackCount(8, 30);
    $reasons->addStringColumn('Reasons')
      ->addNumberColumn('Percent')
      ->addRow(['Changes suggest resend samples', $feed_arr['option_1']])
      ->addRow(['Did not like', $feed_arr['option_2']])
      ->addRow(['Stopped Responding', $feed_arr['option_3']])
      ->addRow(['Sample Selected', $feed_arr['option_4']]);

    $donutpiechart = \Lava::PieChart('IMDB', $reasons, [
      'title' => 'Last 30 Days Feeback Piechart : Deepak',
      'is3D'   => true,
      'titleTextStyle' => [
        'color'    => '#16426B',
        'fontSize' => 14
      ],
      'legend' => [
        'position'    => 'top',
        'maxLines' => 3

      ],
      'slices' => [
        ['offset' => 0.2],
        ['offset' => 0.25],
        ['offset' => 0.3]
      ]
    ]);
    //-----------------------------


    $today_node = ClientNote::whereDate('created_at', Carbon::today())->get();


    $data = [
      "name" => "Ajay",
      "today_node" => $today_node,
    ];
    return $theme->scope('dash.index', $data)->render();
  }


  public function ClinetDashboard()
  {
    echo "under process";
  }
  //v2
  public function ImportExport(Request $request)
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['info' => 'This is user information'];
    return $theme->scope('dash.import_export', $data)->render();
  }

  public function UploadDropzone(Request $request)
  {
    print_r($request->all());
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function mypdf()
  {

    AyraHelp::UpdateSAPCHKLIST();

    //  $theme = Theme::uses('admin')->layout('layout');
    //  $data=["name"=>"Ajay"];
    //  PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
    //  $pdf = PDF::loadView('pdf.sample', $data);
    //  return $pdf->download('invoice.pdf');
  }

  public function AdminDashboard()
  {
    $theme = Theme::uses('admin')->layout('layout');
    $data = ['info' => 'Hello World'];
    return $theme->scope('home.index', $data)->render();
  }
  public function StaffDashboard()
  {

    $theme = Theme::uses('staff')->layout('layout');
    $data = ['info' => 'This is user information'];
    return $theme->scope('home.index', $data)->render();
  }
  public function SalesUserDashboard()
  {

    $theme = Theme::uses('salesagent')->layout('layout');
    $data = ['info' => 'This is user information'];
    return $theme->scope('home.index', $data)->render();
  }
  public function Front()
  {
    $theme = Theme::uses('default')->layout('layout');
    $data = ['info' => 'Hello World'];
    return $theme->scope('index', $data)->render();
  }
  //  anoops@bointernational.net
  //sahilg@bointernational.net


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
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
    //
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
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
