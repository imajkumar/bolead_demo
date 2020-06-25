<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Company;
use App\RowClient;
use App\ContactClient;
use App\UserAccess;
use App\Employee;
use App\KPIData;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\KPIReport;
use App\MHP;
use Illuminate\Support\Facades\Hash;
use DB;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Session;
use Mail;
use Theme;
use Khill\Lavacharts\Lavacharts;
use App\Helpers\AyraHelp;
//use Carbon\Carbon;
class UserController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'isAdmin'])->except(['samples']);
  }







public function getLoginActivityDetails(Request $request){
$login_arr=DB::table('login_activity')->where('created_on',$request->rowID)->get();
$HTML='<table class="table table-sm m-table m-table--head-bg-brand">
                        <thead class="thead-inverse">
                          <tr>
                            <th>#</th>
                            <th>Session Start</th>
                            <th>Session Start</th>
                            <th>Session Active</th>
                          </tr>
                        </thead>
                        <tbody>';
                        $i=0;
foreach ($login_arr as $key => $data) {
   $st_login=date('j M Y H:iA', strtotime($data->login_start));
   $st_stop=date('j M Y H:iA', strtotime($data->logout_start));

   $start_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->login_start);
   $end_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->logout_start);
   $different_days = $end_date->diffForHumans($start_date);

  $i++;
  $HTML .='<tr>
      <th scope="row">'.$i.'</th>
      <td>'.$st_login.'</td>
      <td>'.$st_stop.'</td>
      <td>'.$different_days.'</td>
    </tr>';

}
$HTML .='	</tbody>
</table>';
echo $HTML;

}
public function getLoginActivityUser(Request $request){
  if(isset($request->userID)){
$login_arr=DB::table('login_activity')->where('user_id',$request->userID)->distinct()->get(['created_on']);

}else{
$login_arr=DB::table('login_activity')->where('user_id',Auth::user()->id)->distinct()->get(['created_on']);

}



$data_arr = array();
foreach ($login_arr as $key => $value) {


if(isset($request->userID)){
  $login_first_arr=DB::table('login_activity')->where('user_id',$request->userID)->where('created_on',$value->created_on)->first();
  $login_last_arr=DB::table('login_activity')->where('user_id',$request->userID)->where('created_on',$value->created_on)->orderBy('id', 'DESC')->first();

}else{
  $login_first_arr=DB::table('login_activity')->where('user_id',Auth::user()->id)->where('created_on',$value->created_on)->first();
  $login_last_arr=DB::table('login_activity')->where('user_id',Auth::user()->id)->where('created_on',$value->created_on)->orderBy('id', 'DESC')->first();

}
   $start_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $login_first_arr->login_start);
   $end_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $login_last_arr->logout_start);
   $different_days = $end_date->diffForHumans($start_date);


    //dd($different_days);





    $data_arr[] = array(
      'RecordID' => $login_first_arr->id,
      'login_date' => date('j M Y ', strtotime($login_first_arr->login_start)),
      'login_date_db' => $login_first_arr->login_start,
      'login_name' => $login_first_arr->user_name,
      'first_login' => date('j M Y H:iA', strtotime($login_first_arr->login_start)),
      'last_login' =>  date('j M Y H:iA', strtotime($login_last_arr->logout_start)),
      'session_hour' => $different_days,
      'loginDBID' => $login_last_arr->created_on


    );

}

  $JSON_Data = json_encode($data_arr);

  $columnsDefault = [
  'RecordID' => true,
  'login_date' => true,
  'login_date_db' => true,
  'login_name' => true,
  'first_login' => true,
  'last_login' => true,
  'session_hour' => true,
  'loginDBID' => true,
  'Actions'      => true,

  ];

  $this->DataGridResponse($JSON_Data, $columnsDefault);

}
public function viewLoginActivityData($id){
    $users=User::find($id);
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' =>$users];
    return $theme->scope('lead.login_activity_admin_userView', $data)->render();

}
public function loginActivity(){


  $theme = Theme::uses('corex')->layout('layout');
  $data = ['users' =>''];
  $user = auth()->user();
  $userRoles = $user->getRoleNames();
  $user_role = $userRoles[0];
  if($user_role=='Admin'){
    return $theme->scope('lead.login_activity_admin', $data)->render();
  }else{
    return $theme->scope('lead.login_activity', $data)->render();
  }

}


  public function sendQuationView($id){
    $users = DB::table('indmt_data')->where('QUERY_ID',$id)->first();

    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => $users];
    return $theme->scope('client.send_quatation_view', $data)->render();
  }
  public function AddNewLead(Request $request){
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('client.add_new_lead', $data)->render();

  }

  public function setReplayToTicket(Request $request)
  {

    DB::table('ticket_chats')->insert(
      [

        'ticket_id' => $request->ticketid,
        'user_from' => Auth::user()->id,
        'user_message' => $request->txtReplay,
        'created_at' => date('Y-m-d H:i:s')


      ]
    );
    $resp = array(
      'status' => 1,

    );
    return response()->json($resp);
  }
  public function setTicketResponseSELF(Request $request)
  {
    //print_r($request->all());

    DB::table('ticket_list')
      ->where('id', $request->TID)
      ->update([
        'ticket_closed_at' => date('Y-m-d H:i:s'),
        'ticket_closed_by' => Auth::user()->id,
        'ticket_status' => $request->txtTicketSelectResp,
      ]);
    $resp = array(
      'status' => 1,

    );
    return response()->json($resp);
  }
  public function supportTicket(Request $request)
  {

    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('users.supportTicket', $data)->render();
  }
  public function getTicketListDataInfo(Request $request)
  {
    $tType_arr = DB::table('ticket_list')->where('id', $request->rowID)->get();



    $user_data = array();
    $rep_data = array();
    foreach ($tType_arr as $key => $row) {

      $emp_arr = AyraHelp::getProfilePIC($row->created_by);

      $user_arr = AyraHelp::getUser($row->created_by);


      if (!isset($emp_arr->photo)) {
        $img_photo = asset('local/public/img/avatar.jpg');
      } else {
        $img_photo = asset('local/public/uploads/photos') . "/" . optional($emp_arr)->photo;
      }


      $date = Carbon::parse($row->created_at);
      $now = Carbon::now();


      $created_at = $date->diffForHumans();
      $created_at = str_replace([' seconds', ' second'], ' sec', $created_at);
      $created_at = str_replace([' minutes', ' minute'], ' min', $created_at);
      $created_at = str_replace([' hours', ' hour'], ' Hrs', $created_at);
      $created_at = str_replace([' months', ' month'], ' M', $created_at);

      if (preg_match('(years|year)', $created_at)) {
        $created_at = $this->created_at->toFormattedDateString();
      }
      switch ($row->ticket_status) {
        case 0:
          $t_status = 'PENDING';
          break;
        case 1:
          $t_status = 'OPEN';
          break;
        case 2:
          $t_status = 'CLOSED';
          break;
        case 3:
          $t_status = 'RE-OPEN';
          break;
      }


      $replay_data = DB::table('ticket_chats')->where('ticket_id', $request->rowID)->get();

      foreach ($replay_data as $key => $rDATA) {

        $emp_arr = AyraHelp::getProfilePIC($rDATA->user_from);


        if (!isset($emp_arr->photo)) {
          $img_photo = asset('local/public/img/avatar.jpg');
        } else {
          $img_photo = asset('local/public/uploads/photos') . "/" . optional($emp_arr)->photo;
        }
        $date2 = Carbon::parse($rDATA->created_at);
        $created_at1 = $date2->diffForHumans();
        $created_at1 = str_replace([' seconds', ' second'], ' sec', $created_at1);
        $created_at1 = str_replace([' minutes', ' minute'], ' min', $created_at1);
        $created_at1 = str_replace([' hours', ' hour'], ' Hrs', $created_at1);
        $created_at1 = str_replace([' months', ' month'], ' M', $created_at1);

        if (preg_match('(years|year)', $created_at1)) {
          $created_at1 = $this->created_at1->toFormattedDateString();
        }


        $rep_data[] = array(
          'name' => AyraHelp::getUser($rDATA->user_from)->name,
          'profile_pic' => $img_photo,
          'user_msg' => $rDATA->user_message,
          'time_ag' => $created_at1,
        );
      }


      $user_data[] = array(
        'userid' => $user_arr->id,
        'ticketid' => $request->rowID,
        'name' => $user_arr->name,
        'profile_pic' => $img_photo,
        'ticket_ago' => $created_at,
        'ticket_msg' => $row->ticket_message,
        'ticket_status' => $t_status,

      );
    }
    $resp = array(
      'status' => 1,
      'user_data' => $user_data,
      'replay_data' => $rep_data
    );
    return response()->json($resp);
  }

  public function getTicketListData(Request $request)
  {

    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $user_role = $userRoles[0];
    $data_arr = array();

    if ($user_role == 'Admin' || Auth::user()->id == 112) {
      $ticket_arr = DB::table('ticket_list')->get();
    } else {

      $ticket_arr = DB::table('ticket_list')
        ->join('ticket_assign_to', 'ticket_list.ticket_id', '=', 'ticket_assign_to.ticket_id')
        ->where('ticket_assign_to.assign_to', Auth::user()->id)
        ->orWhere('ticket_list.created_by', Auth::user()->id)
        ->select('ticket_list.*', 'ticket_assign_to.read_status', 'ticket_assign_to.assign_to')
        ->get();
    }








    foreach ($ticket_arr as $key => $value) {


      $tType_arr = DB::table('ticket_type')->where('id', $value->ticket_type)->first();
      $assin_arr = DB::table('ticket_assign_to')->where('ticket_id', $value->ticket_id)->get();


      foreach ($assin_arr as $key => $user) {


        $user_arr = AyraHelp::getUser($user->assign_to);
        $emp_arr = AyraHelp::getProfilePIC($user_arr->id);

        if (!isset($emp_arr->photo)) {
          $img_photo = asset('local/public/img/avatar.jpg');
        } else {
          $img_photo = asset('local/public/uploads/photos') . "/" . optional($emp_arr)->photo;
        }

        $assin_user[] = array(
          'user_id' => $user->id,
          'name' => $user_arr->name,
          'profile_pic' => $img_photo,
        );
      }

      $date = Carbon::parse($value->created_at);
      $now = Carbon::now();


      $created_at = $date->diffForHumans();
      $created_at = str_replace([' seconds', ' second'], ' sec', $created_at);
      $created_at = str_replace([' minutes', ' minute'], ' min', $created_at);
      $created_at = str_replace([' hours', ' hour'], ' Hrs', $created_at);
      $created_at = str_replace([' months', ' month'], ' M', $created_at);

      if (preg_match('(years|year)', $created_at)) {
        $created_at = $this->created_at->toFormattedDateString();
      }



      $data_arr[] = array(
        'RecordID' => $value->id,
        'ticket_id' => $value->ticket_id,
        'ticket_type' => $tType_arr->ticket_type,
        'ticket_subject' => $value->ticket_subject,
        'ticket_status' => $value->ticket_status,
        'created_by' => AyraHelp::getUser($value->created_by)->name,
        'priority_type' => $value->priority_type,
        'assignTo' => json_encode($assin_user),
        'created_at' => date('j M Y h:i A', strtotime($value->created_at)),
        'since_ago' => $created_at,
      );
      $assin_user = (array) null;
    }

    $JSON_Data = json_encode($data_arr);

    $columnsDefault = [
      'RecordID' => true,
      'ticket_id' => true,
      'ticket_type' => true,
      'ticket_subject' => true,
      'ticket_status' => true,
      'created_by' => true,
      'priority_type' => true,
      'created_at' => true,
      'assignTo' => true,
      'since_ago' => true,
      'Actions'      => true,

    ];

    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }


  public function view_ticket_data()
  {


    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('orders.v1.ticketRequestedData', $data)->render();
  }
  // sentTicketRequest
  public function sentTicketRequest(Request $request)
  {

    $getTicketID = AyraHelp::getTicketID();

    DB::table('ticket_list')->insert(
      [

        'ticket_id' => $getTicketID,
        'ticket_type' => $request->ticketType,
        'ticket_subject' => $request->ticket_subject,
        'priority_type' => $request->ticketPriority,
        'ticket_message' => $request->txtTicketMessage,
        'ticket_message' => $request->txtTicketMessage,
        'created_at' => date('Y-m-d H:i:s'),
        'created_by' => Auth::user()->id

      ]
    );
    //assign to
    $assign_arr = $request->ticket_user;

    foreach ($assign_arr as $key => $user) {
      DB::table('ticket_assign_to')->insert(
        [

          'ticket_id' => $getTicketID,
          'assign_to' => $user,
          'assign_by' => Auth::user()->id

        ]
      );
    }
    //assign to

    $resp = array(
      'status' => 1
    );
    return response()->json($resp);
  }
  // sentTicketRequest
  public function saveLeadData(Request $request)
  {


    $QUERY_ID = AyraHelp::getSponsorID();

    DB::table('indmt_data')->insert(
      [

        'SENDERNAME' => $request->contact_person,
        'SENDEREMAIL' => $request->email,
        'SUBJECT' => 'In Hourse Data',
        'DATE_TIME_RE' => date('j F Y h:iA'),
        'GLUSR_USR_COMPANYNAME' => $request->company,
        'MOB' => $request->phone,
        'COUNTRY_FLAG' => 'https://1.imimg.com/country-flags/small/in_flag_s.png',
        'ENQ_MESSAGE' => $request->remarks,
        'ENQ_ADDRESS' => $request->address,
        'ENQ_CITY' => $request->city,
        'ENQ_STATE' => $request->state,
        'PRODUCT_NAME' => $request->product_name,
        'COUNTRY_ISO' => 'IN',
        'EMAIL_ALT' => '',
        'MOBILE_ALT' => '',
        'PHONE' => '',
        'PHONE_ALT' => '',
        'IM_MEMBER_SINCE' => '',
        'QUERY_ID' => intval($QUERY_ID),
        'data_source' => 'INHOUSE-ENTRY',
        'data_source_ID' => 6,
        'created_at' => date('Y-m-d H:i:s'),
        'DATE_TIME_RE_SYS' => date('Y-m-d H:i:s'),
        'assign_to' => 77,

      ]
    );

    $resp = array(
      'status' => 1
    );
    return response()->json($resp);
  }
  //LEAD
  public function setLeadAssign(Request $request)
  {


    if ($request->action == 6) { //lead notes unqlified

      DB::table('indmt_data')
        ->where('QUERY_ID', $request->QUERY_ID)
        ->update([
          'lead_status' => 4,
          'iIrrelevant_type' =>  $request->unqlified_type,
          'remarks' => $request->txtMessageNoteReponse,
        ]);
      $unqlified_type_HTML = $request->unqlified_type_HTML;

      //--------------------------------
      $QUERY_ID = $request->QUERY_ID;
      $user_id = Auth::user()->id;
      $msg = $request->txtMessageNoteReponse;

      $msg_desc = 'This is UnQualified by :' . AyraHelp::getUser($user_id)->name . " on " . date('j F Y H:i:s') . "With reason by :" . $unqlified_type_HTML;

      $this->saveLeadHistory($QUERY_ID, $user_id, $msg_desc, $msg, NULL, NULL);
      //----------------------------



      $resp = array(
        'status' => 1
      );
      return response()->json($resp);
    }


    if ($request->action == 5) { //lead notes sales


      DB::table('lead_notesby_sales')->insert(
        [
          'QUERY_ID' => $request->QUERY_ID,
          'added_by' => Auth::user()->id,
          'message' => $request->txtMessageNoteReponse,
          'created_at' => date('Y-m-d H:i:s'),
          'date_schedule' => $request->shdate_input,

        ]
      );


      //--------------------------------
      $QUERY_ID = $request->QUERY_ID;
      $user_id = Auth::user()->id;
      $msg = $request->txtMessageNoteReponse;
      $sechule_date_time = $request->shdate_input;
      $msg_desc = 'Reminder is added by Sales:  :' . AyraHelp::getUser($user_id)->name . " on " . date('j F Y H:i:s');

      $this->saveLeadHistory($QUERY_ID, $user_id, $msg_desc, $msg, NULL, $sechule_date_time);
      //----------------------------



      $resp = array(
        'status' => 1
      );
      return response()->json($resp);
    }

    if ($request->action == 4) { //lead moves

      $expire_time = date("Y-m-d H:i:s", strtotime('1 day'));

      $arr_data = DB::table('lead_moves')
        ->where('QUERY_ID', $request->QUERY_ID)
        ->where('assign_to', $request->assign_user_id)->first();
      if ($arr_data == null) {
        $assign_user_name = AyraHelp::getUser($request->assign_user_id)->name;
        $stage_data = AyraHelp::getCurrentStageLEAD($request->QUERY_ID);

        DB::table('lead_moves')->insert(
          [
            'QUERY_ID' => $request->QUERY_ID,
            'assign_to' => $request->assign_user_id,
            'assign_by' => Auth::user()->id,
            'msg' => $request->assign_msg,
            'assign_remarks' => 'This lead tranfer to :' . $assign_user_name,
            'stage_name' => $stage_data->stage_name,
            'stage_id' => $stage_data->stage_id,
            'created_at' => date('Y-m-d H:i:s'),

          ]
        );


        DB::table('indmt_data')
          ->where('QUERY_ID', $request->QUERY_ID)
          ->update([
            'assign_to' => $request->assign_user_id
          ]);


        DB::table('lead_assign')
          ->where('QUERY_ID', $request->QUERY_ID)
          ->update([
            'assign_user_id' => $request->assign_user_id
          ]);


        //--------------------------------
        $QUERY_ID = $request->QUERY_ID;
        $user_id = Auth::user()->id;
        $msg = $request->assign_msg;

        $msg_desc = 'This is Lead moves to  :' . AyraHelp::getUser($request->assign_user_id)->name . " on " . date('j F Y H:i:s') . "by :" . AyraHelp::getUser($user_id)->name;

        $this->saveLeadHistory($QUERY_ID, $user_id, $msg_desc, $msg, NULL, NULL);
        //----------------------------



      }










      $resp = array(
        'status' => 1
      );
      return response()->json($resp);
    }
    if ($request->action == 3) { //add notes
      $expire_time = date("Y-m-d H:i:s", strtotime('1 day'));
      DB::table('lead_notes')->insert(
        [
          'QUERY_ID' => $request->QUERY_ID,
          'created_by' => Auth::user()->id,
          'msg' => $request->txtMessageNoteReponse,
          'expire_time' => $expire_time,
          'created_at' => date('Y-m-d H:i:s'),
          'status' => 1,

        ]
      );

      //--------------------------------
      $QUERY_ID = $request->QUERY_ID;
      $user_id = Auth::user()->id;
      $msg = $request->txtMessageNoteReponse;
      $msg_desc = 'Note is added by :' . AyraHelp::getUser(Auth::user()->id)->name . " on " . date('j F Y H:i:s');

      $this->saveLeadHistory($QUERY_ID, $user_id, $msg_desc, $msg);
      //----------------------------

      $resp = array(
        'status' => 1
      );
      return response()->json($resp);
    }

    if ($request->action == 1) { //lead assign

      $expire_time = date("Y-m-d H:i:s", strtotime('1 day'));

      $arr_data = DB::table('lead_assign')
        ->where('QUERY_ID', $request->QUERY_ID)
        ->first();
      if ($arr_data == null) {

        DB::table('lead_assign')->insert(
          [
            'QUERY_ID' => $request->QUERY_ID,
            'assign_user_id' => $request->assign_user_id,
            'assign_by' => Auth::user()->id,
            'msg' => $request->assign_msg,
            'expire_time' => $expire_time,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 1,

          ]
        );

        //---------------------
        //  $arr_data=DB::table('indmt_data')
        //  ->where('QUERY_ID', $request->QUERY_ID)
        //  ->where('assign_to', $request->assign_user_id)->first();
        //  if($arr_data==null){

        DB::table('indmt_data')
          ->where('QUERY_ID', $request->QUERY_ID)
          ->update([
            'lead_status' => 2,
            'assign_to' => $request->assign_user_id,
            'assign_on' => date('Y-m-d H:i:s'),
            'assign_by' => Auth::user()->id,
            'remarks' => $request->assign_msg,
          ]);


        //--------------------------------
        $QUERY_ID = $request->QUERY_ID;
        $user_id = Auth::user()->id;
        $msg = $request->assign_msg;
        $at_stage_id = 1;
        $msg_desc = 'This lead is asign to  :' . AyraHelp::getUser($request->assign_user_id)->name . " on " . date('j F Y H:i:s') . ' By ' . AyraHelp::getUser(Auth::user()->id)->name;

        $this->saveLeadHistory($QUERY_ID, $user_id, $msg_desc, $msg, $at_stage_id);
        //----------------------------


        DB::table('st_process_action_4')->insert(
          [
            'ticket_id' => $request->QUERY_ID,
            'process_id' => 4,
            'stage_id' => 1,
            'action_on' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'expected_date' => date('Y-m-d H:i:s'),
            'remarks' => 'Assign ',
            'attachment_id' => 0,
            'assigned_id' => 1,
            'undo_status' => 1,
            'updated_by' => Auth::user()->id,
            'created_status' => 1,
            'completed_by' => $request->assign_user_id,
            'statge_color' => 'completed',
          ]
        );

        //---------------------

      }



      $resp = array(
        'status' => 1
      );
      return response()->json($resp);
    }
    if ($request->action == 2) { //ireeleavant



      if ($request->iIrrelevant_type == 5) {
        DB::table('indmt_data')
          ->where('QUERY_ID', $request->QUERY_ID)
          ->update([
            'lead_status' => 55,
            'iIrrelevant_type' =>  $request->iIrrelevant_type,
            'remarks' => $request->txtMessageIreeReponse,

          ]);
      } else {
        DB::table('indmt_data')
          ->where('QUERY_ID', $request->QUERY_ID)
          ->update([
            'lead_status' => 1,
            'iIrrelevant_type' =>  $request->iIrrelevant_type,
            'remarks' => $request->txtMessageIreeReponse,

          ]);
      }




      DB::table('lead_Irrelevant')->insert(
        [
          'QUERY_ID' => $request->QUERY_ID,
          'iIrrelevant_type' => $request->iIrrelevant_type,
          'created_by' => Auth::user()->id,
          'msg' => $request->txtMessageIreeReponse,
          'created_at' => date('Y-m-d H:i:s'),
          'status' => 1,

        ]
      );


      //--------------------------------
      $QUERY_ID = $request->QUERY_ID;
      $user_id = Auth::user()->id;
      $msg = $request->txtMessageIreeReponse;
      $iIrrelevant_type_HTML = $request->iIrrelevant_type_HTML;
      $msg_desc = 'This lead is Irrelevant mark by :' . AyraHelp::getUser($user_id)->name . " on " . date('j F Y H:i:s') . " with reason type :" . $iIrrelevant_type_HTML;

      $this->saveLeadHistory($QUERY_ID, $user_id, $msg_desc, $msg, NULL, NULL);
      //----------------------------




      $resp = array(
        'status' => 1
      );
      return response()->json($resp);
    }
  }

  // getLeadListSalesOwn
  public function getLeadListSalesOwn(Request $request)
  {



    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $user_role = $userRoles[0];
    $i = 0;
    if ($user_role == 'Admin') {
      $data_arr_data = DB::table('client_sales_lead')

        // ->where('assign_to', '=', Auth::user()->id)
        ->where('is_deleted', '=', 0)
        //->where('indmt_data.lead_status', '=', 0)
        ->orderBy('created_at', 'desc')

        ->get();
    } else {
      $data_arr_data = DB::table('client_sales_lead')

        ->where('assign_to', '=', Auth::user()->id)
        ->where('is_deleted', '=', 0)
        //->where('indmt_data.lead_status', '=', 0)
        ->orderBy('created_at', 'desc')

        ->get();
    }






    $data_arr = array();
    foreach ($data_arr_data as $key => $value) {
      $i++;
      $AssignName = AyraHelp::getLeadAssignUser($value->QUERY_ID);

      $lsource = "";

      $LS = $value->data_source;
      if ($LS == 'INDMART-9999955922@API_1' || $LS == 'INDMART-9999955922') {
        $lsource = 'IM1';
      }
      if ($LS == 'INDMART-8929503295@API_2' || $LS == 'INDMART-8929503295') {
        $lsource = 'IM2';
      }

      if ($LS == 'TRADEINDIA-8850185@API_3' || $LS == 'TRADEINDIA-8850185@API_3') {
        $lsource = 'TD1';
      }
      if ($LS == 'INHOUSE-ENTRY') {
        $lsource = 'IN';
      }


      $QTYPE = IS_NULL($value->QTYPE) ? 'NA' : $value->QTYPE;

      switch ($QTYPE) {
        case 'NA':
          $QTYPE_ICON = '';
          break;
        case 'W':
          $QTYPE_ICON = 'D';
          break;
        case 'P':
          $QTYPE_ICON = 'P';
          break;
        case 'B':
          $QTYPE_ICON = 'B';
        case 'OC':
          $QTYPE_ICON = 'M';

          break;
      }
      //  $leadNoteCount=AyraHelp::getLeadCountWithNoteID($value->QUERY_ID);

      // $value->lead_status,
      //----------------------------
      if ($value->lead_status == 0 ||  $value->lead_status == 1 || $value->lead_status == 4) {
        switch ($value->lead_status) {
          case 0:
            $st_name = 'Fresh';
            break;
          case 1:
            $st_name = 'Irrelevant';
            break;
          case 4:
            $st_name = 'Unqualified';
            break;
        }
      } else {

        $curr_lead_stage = AyraHelp::getCurrentStageMYLEAD($value->QUERY_ID);
        $st_name = $curr_lead_stage->stage_name;
      }

      //----------------------------



      if ($value->last_note_updated != null) {
        $lastNote = date('j M Y', strtotime($value->last_note_updated));
      } else {
        $lastNote = 'N/A';
      }
      if ($value->follow_date != null) {
        $followdate = date('j M Y', strtotime($value->follow_date));
      } else {
        $followdate = 'N/A';
      }


      //$st_name='Unqualified';

      $data_arr[] = array(
        'RecordID' => $value->id,
        'QUERY_ID' => $value->QUERY_ID,
        'QTYPE' => $QTYPE_ICON,
        'SENDERNAME' => IS_NULL($value->SENDERNAME) ? '' : $value->SENDERNAME,
        'SENDEREMAIL' => $value->SENDEREMAIL,
        'SUBJECT' => $value->SUBJECT,
        'DATE_TIME_RE' => $value->DATE_TIME_RE,
        'GLUSR_USR_COMPANYNAME' => IS_NULL($value->GLUSR_USR_COMPANYNAME) ? '' : $value->GLUSR_USR_COMPANYNAME,
        'MOB' => $value->MOB,
        'created_at' => $value->DATE_TIME_RE,
        'COUNTRY_FLAG' => $value->COUNTRY_FLAG,
        'ENQ_MESSAGE' => IS_NULL($value->ENQ_MESSAGE) ? '' : $value->ENQ_MESSAGE,
        'ENQ_ADDRESS' => $value->ENQ_ADDRESS,
        'ENQ_CITY' => IS_NULL($value->ENQ_CITY) ? '' : $value->ENQ_CITY,
        'ENQ_STATE' => IS_NULL($value->ENQ_STATE) ? '' : $value->ENQ_STATE,
        'PRODUCT_NAME' => IS_NULL($value->PRODUCT_NAME) ? '' : $value->PRODUCT_NAME,
        'COUNTRY_ISO' => $value->COUNTRY_ISO,
        'EMAIL_ALT' => $value->EMAIL_ALT,
        'MOBILE_ALT' => $value->MOBILE_ALT,
        'PHONE' => $value->PHONE,
        'PHONE_ALT' => $value->PHONE_ALT,
        'IM_MEMBER_SINCE' => $value->IM_MEMBER_SINCE,
        'data_source' => $lsource,
        'data_source_ID' => $value->data_source_ID,
        'updated_by' => $value->updated_by,
        'lead_check' => $value->lead_check,
        'lead_status' => $st_name,
        'st_name' => $st_name,
        'last_note_added' => $lastNote,
        'follow_date' => $followdate,
        'AssignName' => $AssignName,
        'AssignID' => $value->assign_to,
        'LeadOwner' => AyraHelp::getUser($value->assign_to)->name,

        'remarks' => $value->remarks,
      );
    }

    $JSON_Data = json_encode($data_arr);

    $columnsDefault = [
      'RecordID' => true,
      'QUERY_ID' => true,
      'SENDERNAME' => true,
      'SENDEREMAIL' => true,
      'SUBJECT' => true,
      'DATE_TIME_RE' => true,
      'GLUSR_USR_COMPANYNAME' => true,
      'MOB' => true,
      'created_at' => true,
      'COUNTRY_FLAG' => true,
      'ENQ_MESSAGE' => true,
      'ENQ_ADDRESS' => true,
      'ENQ_CITY' => true,
      'ENQ_STATE' => true,
      'PRODUCT_NAME' => true,
      'COUNTRY_ISO' => true,
      'EMAIL_ALT' => true,
      'MOBILE_ALT' => true,
      'PHONE' => true,
      'PHONE_ALT' >= true,
      'IM_MEMBER_SINCE' => true,
      'data_source' => true,
      'data_source_ID' => true,
      'updated_by' => true,
      'lead_check' => true,
      'lead_status' => true,
      'st_name' => true,
      'last_note_added' => true,
      'follow_date' => true,
      'remarks' => true,
      'AssignName' => true,
      'AssignID' => true,
      'LeadOwner' => true,
      'Actions'      => true,
    ];

    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }

  // getLeadListSalesOwn


  //getLeadList_SALES_END
  public function getLeadList_SALES_END(Request $request)
  {



    $i = 0;
    // $data_arr_data=DB::table('indmt_data')->orderBy('QUERY_ID','desc')->get();



    if ($request->action_name == 'viewUnQualifiedLead') {
      $data_arr_data = DB::table('indmt_data')
        ->join('lead_assign', 'indmt_data.QUERY_ID', '=', 'lead_assign.QUERY_ID')
        ->where('lead_assign.assign_user_id', '=', Auth::user()->id)
        ->where('indmt_data.lead_status', '=', 4)
        ->orderBy('lead_assign.created_at', 'desc')
        ->select('indmt_data.*', 'lead_assign.assign_by', 'lead_assign.msg')
        ->get();
    } else {
      if ($request->action_name == 'viewTodayLeadLead') {
        $data_arr_data = DB::table('indmt_data')
          ->join('lead_assign', 'indmt_data.QUERY_ID', '=', 'lead_assign.QUERY_ID')
          ->where('lead_assign.assign_user_id', '=', Auth::user()->id)
          //->where('indmt_data.lead_status', '=', 0)
          ->whereDate('lead_assign.created_at', date('Y-m-d'))
          ->orderBy('lead_assign.created_at', 'desc')
          ->select('indmt_data.*', 'lead_assign.assign_by', 'lead_assign.msg')
          ->get();
      } else {

        $data_arr_data = DB::table('indmt_data')
          ->join('lead_assign', 'indmt_data.QUERY_ID', '=', 'lead_assign.QUERY_ID')
          ->where('lead_assign.assign_user_id', '=', Auth::user()->id)
          //->where('indmt_data.lead_status', '=', 0)
          ->where('indmt_data.lead_status', '!=', 4)
          ->orderBy('lead_assign.created_at', 'desc')
          ->select('indmt_data.*', 'lead_assign.assign_by', 'lead_assign.msg')
          ->get();
      }
    }


    $data_arr = array();
    foreach ($data_arr_data as $key => $value) {
      $i++;
      $AssignName = AyraHelp::getLeadAssignUser($value->QUERY_ID);

      $lsource = "";

      $LS = $value->data_source;
      if ($LS == 'INDMART-9999955922@API_1' || $LS == 'INDMART-9999955922') {
        $lsource = 'IM1';
      }
      if ($LS == 'INDMART-8929503295@API_2' || $LS == 'INDMART-8929503295') {
        $lsource = 'IM2';
      }

      if ($LS == 'TRADEINDIA-8850185@API_3' || $LS == 'TRADEINDIA-8850185@API_3') {
        $lsource = 'TD1';
      }
      if ($LS == 'INHOUSE-ENTRY') {
        $lsource = 'IN';
      }

      $QTYPE = IS_NULL($value->QTYPE) ? 'NA' : $value->QTYPE;

      switch ($QTYPE) {
        case 'NA':
          $QTYPE_ICON = '';
          break;
        case 'W':
          $QTYPE_ICON = 'D';
          break;
        case 'P':
          $QTYPE_ICON = 'P';
          break;
        case 'B':
          $QTYPE_ICON = 'B';

          break;
      }
      //  $leadNoteCount=AyraHelp::getLeadCountWithNoteID($value->QUERY_ID);

      // $value->lead_status,
      //----------------------------
      // if($value->lead_status==4){
      //   switch ($value->lead_status) {
      //       case 4:
      //       $st_name='Unqualified';
      //       break;

      //   }

      // }else{
      //   $curr_lead_stage=AyraHelp::getCurrentStageLEAD($value->QUERY_ID);
      //   $st_name=$curr_lead_stage->stage_name;
      // }

      $curr_lead_stage = AyraHelp::getCurrentStageLEAD($value->QUERY_ID);
      $st_name = $curr_lead_stage->stage_name;

      //----------------------------


      $data_arr[] = array(
        'RecordID' => $value->id,
        'QUERY_ID' => $value->QUERY_ID,
        'QTYPE' => $QTYPE_ICON,
        'SENDERNAME' => IS_NULL($value->SENDERNAME) ? '' : $value->SENDERNAME,
        'SENDEREMAIL' => $value->SENDEREMAIL,
        'SUBJECT' => $value->SUBJECT,
        'DATE_TIME_RE' => $value->DATE_TIME_RE,
        'GLUSR_USR_COMPANYNAME' => IS_NULL($value->GLUSR_USR_COMPANYNAME) ? '' : $value->GLUSR_USR_COMPANYNAME,
        'MOB' => $value->MOB,
        'created_at' => $value->DATE_TIME_RE,
        'COUNTRY_FLAG' => $value->COUNTRY_FLAG,
        'ENQ_MESSAGE' => IS_NULL($value->ENQ_MESSAGE) ? '' : $value->ENQ_MESSAGE,
        'ENQ_ADDRESS' => $value->ENQ_ADDRESS,
        'ENQ_CITY' => IS_NULL($value->ENQ_CITY) ? '' : $value->ENQ_CITY,
        'ENQ_STATE' => IS_NULL($value->ENQ_STATE) ? '' : $value->ENQ_STATE,
        'PRODUCT_NAME' => IS_NULL($value->PRODUCT_NAME) ? '' : $value->PRODUCT_NAME,
        'COUNTRY_ISO' => $value->COUNTRY_ISO,
        'EMAIL_ALT' => $value->EMAIL_ALT,
        'MOBILE_ALT' => $value->MOBILE_ALT,
        'PHONE' => $value->PHONE,
        'PHONE_ALT' => $value->PHONE_ALT,
        'IM_MEMBER_SINCE' => $value->IM_MEMBER_SINCE,
        'data_source' => $lsource,
        'data_source_ID' => $value->data_source_ID,
        'updated_by' => $value->updated_by,
        'lead_check' => $value->lead_check,
        'lead_status' => $st_name,
        'st_name' => $st_name,
        'AssignName' => $AssignName,
        'AssignID' => $value->assign_to,

        'remarks' => $value->remarks,
      );
    }


    $JSON_Data = json_encode($data_arr);

    $columnsDefault = [
      'RecordID' => true,
      'QUERY_ID' => true,
      'SENDERNAME' => true,
      'SENDEREMAIL' => true,
      'SUBJECT' => true,
      'DATE_TIME_RE' => true,
      'GLUSR_USR_COMPANYNAME' => true,
      'MOB' => true,
      'created_at' => true,
      'COUNTRY_FLAG' => true,
      'ENQ_MESSAGE' => true,
      'ENQ_ADDRESS' => true,
      'ENQ_CITY' => true,
      'ENQ_STATE' => true,
      'PRODUCT_NAME' => true,
      'COUNTRY_ISO' => true,
      'EMAIL_ALT' => true,
      'MOBILE_ALT' => true,
      'PHONE' => true,
      'PHONE_ALT' >= true,
      'IM_MEMBER_SINCE' => true,
      'data_source' => true,
      'data_source_ID' => true,
      'updated_by' => true,
      'lead_check' => true,
      'lead_status' => true,
      'st_name' => true,
      'remarks' => true,
      'AssignName' => true,
      'AssignID' => true,
      'Actions'      => true,
    ];

    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }
  //getLeadList_SALES_END



  // getLeadList_LMLayout
  public function getLeadList_LMLayout(Request $request)
  {

    // $user = auth()->user();
    // $userRoles = $user->getRoleNames();
    // $user_role = $userRoles[0];
    // if($user_role=='Admin' || Auth::user()->id==77 || Auth::user()->id==90 || Auth::user()->id==130 || Auth::user()->id==131){
    $i = 0;

    if (isset($request->action_name)) {
      if ($request->action_name == 'viewAllAssign') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 2)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'viewAllIreevant') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 1)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'viewUnQualifiedLead') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 4)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'viewHOLDLead') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 55)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'viewDUPLICATELead') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('duplicate_lead_status', 1)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }

      if ($request->action_name == 'BUY_LEAD') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'B')->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'DIRECT_LEAD') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'W')->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'PHONE_LEAD') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'P')->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'INHOUSED_LEAD') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('duplicate_lead_status', 0)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
    } else {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('duplicate_lead_status', 0)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }

    $data_arr = array();
    foreach ($data_arr_data as $key => $value) {
      $i++;
      $AssignName = AyraHelp::getLeadAssignUser($value->QUERY_ID);
      $lsource = "";

      $LS = $value->data_source;
      if ($LS == 'INDMART-9999955922@API_1' || $LS == 'INDMART-9999955922') {
        $lsource = 'IM1';
      }
      if ($LS == 'INDMART-8929503295@API_2' || $LS == 'INDMART-8929503295') {
        $lsource = 'IM2';
      }
      if ($LS == 'TRADEINDIA-8850185@API_3' || $LS == 'TRADEINDIA-8850185@API_3') {
        $lsource = 'TD1';
      }

      if ($LS == 'INHOUSE-ENTRY') {
        $lsource = 'IN';
      }
      $QTYPE = IS_NULL($value->QTYPE) ? 'NA' : $value->QTYPE;
      switch ($QTYPE) {
        case 'NA':
          $QTYPE_ICON = '';
          break;
        case 'W':
          $QTYPE_ICON = 'D';
          break;
        case 'P':
          $QTYPE_ICON = 'P';
          break;
        case 'B':
          $QTYPE_ICON = 'B';

          break;
      }

      // $leadNoteCount=AyraHelp::getLeadCountWithNoteID($value->QUERY_ID);
      //----------------------------
      if ($value->lead_status == 0 ||  $value->lead_status == 1 || $value->lead_status == 4 || $value->lead_status == 55) {
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
          case 55:
            $st_name = 'HOLD';
            break;
        }
      } else {


        $curr_lead_stage = AyraHelp::getCurrentStageLEAD($value->QUERY_ID);
        $st_name = optional($curr_lead_stage)->stage_name;
      }

      //----------------------------
      // LEAD_TYPE
      switch ($value->COUNTRY_ISO) {
        case 'IN':
          $LEAD_TYPE = 'INDIA';
          break;
        case 'India':
          $LEAD_TYPE = 'INDIA';
          break;

        default:
          $LEAD_TYPE = 'FOREIGN';
          break;
      }
      // LEAD_TYPE

      $ENQ_MESSAGE = substr(optional($value)->ENQ_MESSAGE, 0, 30) . '...';


      $data_arr[] = array(
        'RecordID' => $value->id,
        'QUERY_ID' => $value->QUERY_ID,
        'QTYPE' => $QTYPE_ICON,
        'SENDERNAME' => IS_NULL($value->SENDERNAME) ? '' : $value->SENDERNAME,
        'SENDEREMAIL' => $value->SENDEREMAIL,
        'SUBJECT' => $value->SUBJECT,
        'DATE_TIME_RE' => $value->DATE_TIME_RE,
        'GLUSR_USR_COMPANYNAME' => IS_NULL($value->GLUSR_USR_COMPANYNAME) ? '' : $value->GLUSR_USR_COMPANYNAME,
        'MOB' => $value->MOB,
        'created_at' => $value->DATE_TIME_RE,
        'COUNTRY_FLAG' => $value->COUNTRY_FLAG,
        'ENQ_MESSAGE' => $ENQ_MESSAGE,
        'ENQ_ADDRESS' => $value->ENQ_ADDRESS,
        'ENQ_CITY' => IS_NULL($value->ENQ_CITY) ? '' : $value->ENQ_CITY,
        'ENQ_STATE' => IS_NULL($value->ENQ_STATE) ? '' : $value->ENQ_STATE,
        'PRODUCT_NAME' => IS_NULL($value->PRODUCT_NAME) ? '' : $value->PRODUCT_NAME,
        'COUNTRY_ISO' => $value->COUNTRY_ISO,
        'EMAIL_ALT' => $value->EMAIL_ALT,
        'MOBILE_ALT' => $value->MOBILE_ALT,
        'PHONE' => $value->PHONE,
        'PHONE_ALT' => $value->PHONE_ALT,
        'IM_MEMBER_SINCE' => $value->IM_MEMBER_SINCE,
        'data_source' => $lsource,
        'data_source_ID' => $value->data_source_ID,
        'updated_by' => $value->updated_by,
        'lead_check' => $value->lead_check,
        'st_name' => $st_name,
        'LEAD_TYPE' => $LEAD_TYPE,

        'lead_status' => $st_name,
        'AssignName' => $AssignName,
        'AssignID' => $value->assign_to,
        'remarks' => $value->remarks,
      );
    }




    $JSON_Data = json_encode($data_arr);

    $columnsDefault = [
      'RecordID' => true,
      'QUERY_ID' => true,
      'SENDERNAME' => true,
      'SENDEREMAIL' => true,
      'SUBJECT' => true,
      'DATE_TIME_RE' => true,
      'GLUSR_USR_COMPANYNAME' => true,
      'MOB' => true,
      'created_at' => true,
      'COUNTRY_FLAG' => true,
      'ENQ_MESSAGE' => true,
      'ENQ_ADDRESS' => true,
      'ENQ_CITY' => true,
      'ENQ_STATE' => true,
      'PRODUCT_NAME' => true,
      'COUNTRY_ISO' => true,
      'EMAIL_ALT' => true,
      'MOBILE_ALT' => true,
      'PHONE' => true,
      'PHONE_ALT' >= true,
      'IM_MEMBER_SINCE' => true,
      'data_source' => true,
      'data_source_ID' => true,
      'updated_by' => true,
      'lead_check' => true,
      'lead_status' => true,
      'st_name' => true,
      'LEAD_TYPE' => true,
      'remarks' => true,
      'AssignName' => true,
      'AssignID' => true,
      'Actions'      => true,
    ];

    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }
  // getLeadList_LMLayout


// getLeadListViewAll
public function getLeadListViewAll(Request $request)
{

  // $user = auth()->user();
  // $userRoles = $user->getRoleNames();
  // $user_role = $userRoles[0];
  // if($user_role=='Admin' || Auth::user()->id==77 || Auth::user()->id==90 || Auth::user()->id==130 || Auth::user()->id==131){
  $i = 0;

  if (isset($request->action_name)) {
    if ($request->action_name == 'viewAllAssign') {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 2)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }
    if ($request->action_name == 'viewAllIreevant') {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 1)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }
    if ($request->action_name == 'viewUnQualifiedLead') {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 4)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }
    if ($request->action_name == 'viewHOLDLead') {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 55)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }
    if ($request->action_name == 'viewDUPLICATELead') {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('duplicate_lead_status', 1)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }


    if ($request->action_name == 'BUY_LEAD') {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'B')->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }
    if ($request->action_name == 'DIRECT_LEAD') {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'W')->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }
    if ($request->action_name == 'PHONE_LEAD') {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'P')->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }
    if ($request->action_name == 'INHOUSED_LEAD') {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('duplicate_lead_status', 0)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }
  } else {



    $data_arr_data = DB::table('indmt_data')->whereDate('created_at', Carbon::yesterday())->where('lead_status', 0)->where('QTYPE', 'W')->where('duplicate_lead_status', 0)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
  }


  $data_arr = array();
  foreach ($data_arr_data as $key => $value) {
    $i++;
    $AssignName = AyraHelp::getLeadAssignUser($value->QUERY_ID);
    $lsource = "";

    $LS = $value->data_source;
    if ($LS == 'INDMART-9999955922@API_1' || $LS == 'INDMART-9999955922') {
      $lsource = 'IM1';
    }
    if ($LS == 'INDMART-8929503295@API_2' || $LS == 'INDMART-8929503295') {
      $lsource = 'IM2';
    }
    if ($LS == 'TRADEINDIA-8850185@API_3' || $LS == 'TRADEINDIA-8850185@API_3') {
      $lsource = 'TD1';
    }

    if ($LS == 'INHOUSE-ENTRY') {
      $lsource = 'IN';
    }
    $QTYPE = IS_NULL($value->QTYPE) ? 'NA' : $value->QTYPE;
    switch ($QTYPE) {
      case 'NA':
        $QTYPE_ICON = '';
        break;
      case 'W':
        $QTYPE_ICON = 'D';
        break;
      case 'P':
        $QTYPE_ICON = 'P';
        break;
      case 'B':
        $QTYPE_ICON = 'B';

        break;
    }

    // $leadNoteCount=AyraHelp::getLeadCountWithNoteID($value->QUERY_ID);
    //----------------------------
    if ($value->lead_status == 0 ||  $value->lead_status == 1 || $value->lead_status == 4 || $value->lead_status == 55) {
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
        case 55:
          $st_name = 'HOLD';
          break;
      }
    } else {


      $curr_lead_stage = AyraHelp::getCurrentStageLEAD($value->QUERY_ID);
      $st_name = optional($curr_lead_stage)->stage_name;
    }

    //----------------------------
    // LEAD_TYPE
    switch ($value->COUNTRY_ISO) {
      case 'IN':
        $LEAD_TYPE = 'INDIA';
        break;
      case 'India':
        $LEAD_TYPE = 'INDIA';
        break;

      default:
        $LEAD_TYPE = 'FOREIGN';
        break;
    }
    // LEAD_TYPE

    $ENQ_MESSAGE = substr(optional($value)->ENQ_MESSAGE, 0, 30) . '...';


    $data_arr[] = array(
      'RecordID' => $value->id,
      'QUERY_ID' => $value->QUERY_ID,
      'QTYPE' => $QTYPE_ICON,
      'SENDERNAME' => IS_NULL($value->SENDERNAME) ? '' : $value->SENDERNAME,
      'SENDEREMAIL' => $value->SENDEREMAIL,
      'SUBJECT' => $value->SUBJECT,
      'DATE_TIME_RE' => $value->DATE_TIME_RE,
      'GLUSR_USR_COMPANYNAME' => IS_NULL($value->GLUSR_USR_COMPANYNAME) ? '' : $value->GLUSR_USR_COMPANYNAME,
      'MOB' => $value->MOB,
      'created_at' => $value->DATE_TIME_RE,
      'COUNTRY_FLAG' => $value->COUNTRY_FLAG,
      'ENQ_MESSAGE' => $ENQ_MESSAGE,
      'ENQ_ADDRESS' => $value->ENQ_ADDRESS,
      'ENQ_CITY' => IS_NULL($value->ENQ_CITY) ? '' : $value->ENQ_CITY,
      'ENQ_STATE' => IS_NULL($value->ENQ_STATE) ? '' : $value->ENQ_STATE,
      'PRODUCT_NAME' => IS_NULL($value->PRODUCT_NAME) ? '' : $value->PRODUCT_NAME,
      'COUNTRY_ISO' => $value->COUNTRY_ISO,
      'EMAIL_ALT' => $value->EMAIL_ALT,
      'MOBILE_ALT' => $value->MOBILE_ALT,
      'PHONE' => $value->PHONE,
      'PHONE_ALT' => $value->PHONE_ALT,
      'IM_MEMBER_SINCE' => $value->IM_MEMBER_SINCE,
      'data_source' => $lsource,
      'data_source_ID' => $value->data_source_ID,
      'updated_by' => $value->updated_by,
      'lead_check' => $value->lead_check,
      'st_name' => $st_name,
      'LEAD_TYPE' => $LEAD_TYPE,

      'lead_status' => $st_name,
      'AssignName' => $AssignName,
      'AssignID' => $value->assign_to,
      'remarks' => $value->remarks,
    );
  }




  $JSON_Data = json_encode($data_arr);

  $columnsDefault = [
    'RecordID' => true,
    'QUERY_ID' => true,
    'SENDERNAME' => true,
    'SENDEREMAIL' => true,
    'SUBJECT' => true,
    'DATE_TIME_RE' => true,
    'GLUSR_USR_COMPANYNAME' => true,
    'MOB' => true,
    'created_at' => true,
    'COUNTRY_FLAG' => true,
    'ENQ_MESSAGE' => true,
    'ENQ_ADDRESS' => true,
    'ENQ_CITY' => true,
    'ENQ_STATE' => true,
    'PRODUCT_NAME' => true,
    'COUNTRY_ISO' => true,
    'EMAIL_ALT' => true,
    'MOBILE_ALT' => true,
    'PHONE' => true,
    'PHONE_ALT' >= true,
    'IM_MEMBER_SINCE' => true,
    'data_source' => true,
    'data_source_ID' => true,
    'updated_by' => true,
    'lead_check' => true,
    'lead_status' => true,
    'st_name' => true,
    'LEAD_TYPE' => true,
    'remarks' => true,
    'AssignName' => true,
    'AssignID' => true,
    'Actions'      => true,
  ];

  $this->DataGridResponse($JSON_Data, $columnsDefault);
}


// getLeadListViewAll


  public function getLeadList(Request $request)
  {

    // $user = auth()->user();
    // $userRoles = $user->getRoleNames();
    // $user_role = $userRoles[0];
    // if($user_role=='Admin' || Auth::user()->id==77 || Auth::user()->id==90 || Auth::user()->id==130 || Auth::user()->id==131){
    $i = 0;

    if (isset($request->action_name)) {
      if ($request->action_name == 'viewAllAssign') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 2)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'viewAllIreevant') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 1)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'viewUnQualifiedLead') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 4)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'viewHOLDLead') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 55)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'viewDUPLICATELead') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('duplicate_lead_status', 1)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }


      if ($request->action_name == 'BUY_LEAD') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'B')->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'DIRECT_LEAD') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'W')->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'PHONE_LEAD') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'P')->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
      if ($request->action_name == 'INHOUSED_LEAD') {
        $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('duplicate_lead_status', 0)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
      }
    } else {
      $data_arr_data = DB::table('indmt_data')->whereDate('created_at','>=','2020-05-20')->where('lead_status', 0)->where('QTYPE', 'W')->where('duplicate_lead_status', 0)->orderBy('DATE_TIME_RE_SYS', 'desc')->get();
    }


    $data_arr = array();
    foreach ($data_arr_data as $key => $value) {
      $i++;
      $AssignName = AyraHelp::getLeadAssignUser($value->QUERY_ID);
      $lsource = "";

      $LS = $value->data_source;
      if ($LS == 'INDMART-9999955922@API_1' || $LS == 'INDMART-9999955922') {
        $lsource = 'IM1';
      }
      if ($LS == 'INDMART-8929503295@API_2' || $LS == 'INDMART-8929503295') {
        $lsource = 'IM2';
      }
      if ($LS == 'TRADEINDIA-8850185@API_3' || $LS == 'TRADEINDIA-8850185@API_3') {
        $lsource = 'TD1';
      }

      if ($LS == 'INHOUSE-ENTRY') {
        $lsource = 'IN';
      }
      $QTYPE = IS_NULL($value->QTYPE) ? 'NA' : $value->QTYPE;
      switch ($QTYPE) {
        case 'NA':
          $QTYPE_ICON = '';
          break;
        case 'W':
          $QTYPE_ICON = 'D';
          break;
        case 'P':
          $QTYPE_ICON = 'P';
          break;
        case 'B':
          $QTYPE_ICON = 'B';

          break;
      }

      // $leadNoteCount=AyraHelp::getLeadCountWithNoteID($value->QUERY_ID);
      //----------------------------
      if ($value->lead_status == 0 ||  $value->lead_status == 1 || $value->lead_status == 4 || $value->lead_status == 55) {
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
          case 55:
            $st_name = 'HOLD';
            break;
        }
      } else {


        $curr_lead_stage = AyraHelp::getCurrentStageLEAD($value->QUERY_ID);
        $st_name = optional($curr_lead_stage)->stage_name;
      }

      //----------------------------
      // LEAD_TYPE
      switch ($value->COUNTRY_ISO) {
        case 'IN':
          $LEAD_TYPE = 'INDIA';
          break;
        case 'India':
          $LEAD_TYPE = 'INDIA';
          break;

        default:
          $LEAD_TYPE = 'FOREIGN';
          break;
      }
      // LEAD_TYPE

      $ENQ_MESSAGE = substr(optional($value)->ENQ_MESSAGE, 0, 30) . '...';


      $data_arr[] = array(
        'RecordID' => $value->id,
        'QUERY_ID' => $value->QUERY_ID,
        'QTYPE' => $QTYPE_ICON,
        'SENDERNAME' => IS_NULL($value->SENDERNAME) ? '' : $value->SENDERNAME,
        'SENDEREMAIL' => $value->SENDEREMAIL,
        'SUBJECT' => $value->SUBJECT,
        'DATE_TIME_RE' => $value->DATE_TIME_RE,
        'GLUSR_USR_COMPANYNAME' => IS_NULL($value->GLUSR_USR_COMPANYNAME) ? '' : $value->GLUSR_USR_COMPANYNAME,
        'MOB' => $value->MOB,
        'created_at' => $value->DATE_TIME_RE,
        'COUNTRY_FLAG' => $value->COUNTRY_FLAG,
        'ENQ_MESSAGE' => $ENQ_MESSAGE,
        'ENQ_ADDRESS' => $value->ENQ_ADDRESS,
        'ENQ_CITY' => IS_NULL($value->ENQ_CITY) ? '' : $value->ENQ_CITY,
        'ENQ_STATE' => IS_NULL($value->ENQ_STATE) ? '' : $value->ENQ_STATE,
        'PRODUCT_NAME' => IS_NULL($value->PRODUCT_NAME) ? '' : $value->PRODUCT_NAME,
        'COUNTRY_ISO' => $value->COUNTRY_ISO,
        'EMAIL_ALT' => $value->EMAIL_ALT,
        'MOBILE_ALT' => $value->MOBILE_ALT,
        'PHONE' => $value->PHONE,
        'PHONE_ALT' => $value->PHONE_ALT,
        'IM_MEMBER_SINCE' => $value->IM_MEMBER_SINCE,
        'data_source' => $lsource,
        'data_source_ID' => $value->data_source_ID,
        'updated_by' => $value->updated_by,
        'lead_check' => $value->lead_check,
        'st_name' => $st_name,
        'LEAD_TYPE' => $LEAD_TYPE,

        'lead_status' => $st_name,
        'AssignName' => $AssignName,
        'AssignID' => $value->assign_to,
        'remarks' => $value->remarks,
      );
    }




    $JSON_Data = json_encode($data_arr);

    $columnsDefault = [
      'RecordID' => true,
      'QUERY_ID' => true,
      'SENDERNAME' => true,
      'SENDEREMAIL' => true,
      'SUBJECT' => true,
      'DATE_TIME_RE' => true,
      'GLUSR_USR_COMPANYNAME' => true,
      'MOB' => true,
      'created_at' => true,
      'COUNTRY_FLAG' => true,
      'ENQ_MESSAGE' => true,
      'ENQ_ADDRESS' => true,
      'ENQ_CITY' => true,
      'ENQ_STATE' => true,
      'PRODUCT_NAME' => true,
      'COUNTRY_ISO' => true,
      'EMAIL_ALT' => true,
      'MOBILE_ALT' => true,
      'PHONE' => true,
      'PHONE_ALT' >= true,
      'IM_MEMBER_SINCE' => true,
      'data_source' => true,
      'data_source_ID' => true,
      'updated_by' => true,
      'lead_check' => true,
      'lead_status' => true,
      'st_name' => true,
      'LEAD_TYPE' => true,
      'remarks' => true,
      'AssignName' => true,
      'AssignID' => true,
      'Actions'      => true,
    ];

    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }

  public function updateLeadData(Request $request)
  {
    // print_r($request->all());
    // die;
    $rmk = $request->remarks;

    DB::table('indmt_data')
      ->where('QUERY_ID', $request->QUERY_ID)
      ->update([
        'GLUSR_USR_COMPANYNAME' => $request->GLUSR_USR_COMPANYNAME,
        'SENDERNAME' => $request->SENDERNAME,
        'MOB' => $request->MOB,
        'SENDEREMAIL' => $request->SENDEREMAIL,
        'PRODUCT_NAME' => $request->PRODUCT_NAME,
        'MOBILE_ALT' => $request->MOBILE_ALT,
        'EMAIL_ALT' => $request->EMAIL_ALT,
        'ENQ_ADDRESS' => $request->ENQ_ADDRESS,
        'ENQ_CITY' => $request->ENQ_CITY,
        'ENQ_STATE' => $request->ENQ_STATE,
        'updated_by' => Auth::user()->id,
        'remarks' => $rmk,

      ]);

    $resp = array(
      'status' => 1
    );
    return response()->json($resp);
  }
  public function editLead($leadID)
  {

    $users_data = DB::table('indmt_data')->where('QUERY_ID', $leadID)->first();

    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => $users_data];
    return $theme->scope('lead.edit_new_lead', $data)->render();
  }



  public function getLeadReports_Dist()
  {






    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('lead.leadReports_dist', $data)->render();
  }

  public function getLeadStagesGrapgh()
  {
    $lava = new Lavacharts; // See note below for Laravel

    //get Assign lead

    //get Assign lead




    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('lead.leadStageReportsgrapgh', $data)->render();
  }

  public function getLeadReports()
  {



    //=================================================
    $lava = new Lavacharts; // See note below for Laravel

    $finances = $lava->DataTable();
    $finances->addDateColumn('Year')
      ->addNumberColumn('Fresh')
      ->addNumberColumn('Irrelevant')
      ->setDateTimeFormat('Y-m-d');
    //echo "<pre>";
    $fl_userid = 77;
    $freshlead_arr = $this->getBarGraphStackDataFresh('LeadData', 'created_at', $fl_userid, 'assign_to');

    $fl_userid = 77;
    $Irrelevant_arr = $this->getBarGraphStackDataIrrelevant('LeadData', 'created_at', $fl_userid, 'assign_to');




    $data = array();
    foreach ($freshlead_arr as $key => $value) {
      $data[] = $value;
    }

    $i = 0;

    foreach ($Irrelevant_arr as $key => $value) {

      if ($i == 30) {
      } else {
        $finances->addRow([$key, $data[$i], $value]);
        $i++;
      }
    }





    $bo_level = 'BOLEAD_G1';


    $donutchart = \Lava::ColumnChart($bo_level, $finances, [
      'title' => 'Last 30 Days Fresh Lead |  Irrelevant ',
      'legend' => [
        'position'    => 'top',
        'maxLines' => 3,


      ],

      'titleTextStyle' => [
        'color'    => '#16426B',
        'fontSize' => 14

      ]
    ]);





    //===================================================




    //***************************** */
    $finances_g1 = $lava->DataTable();
    $finances_g1->addDateColumn('Year')
      ->addNumberColumn('Fresh')
      ->setDateTimeFormat('Y-m-d');
    //echo "<pre>";
    $fl_userid = 77;
    $freshlead_arr = $this->getBarGraphStackDataFresh('LeadData', 'created_at', $fl_userid, 'assign_to');


    $i = 0;

    foreach ($freshlead_arr as $key => $value) {

      if ($i == 30) {
      } else {
        $finances_g1->addRow([$key, $value]);
        $i++;
      }
    }





    $bo_level = 'BOLEAD_G2';


    $donutchart = \Lava::ColumnChart($bo_level, $finances_g1, [
      'title' => 'Last 30 Days Fresh Lead ',
      'legend' => [
        'position'    => 'top',
        'maxLines' => 3,
        'color'    => '#16426B'

      ],
      'colors' => ['DodgerBlue'],
      'titleTextStyle' => [
        'color'    => '#16426B',
        'fontSize' => 14

      ]
    ]);


    //***************************** */


    //***************************** */
    $finances_g2 = $lava->DataTable();
    $finances_g2->addDateColumn('Year')
      ->addNumberColumn('Irrelevant')
      ->setDateTimeFormat('Y-m-d');
    //echo "<pre>";
    $fl_userid = 77;
    $ireelead_arr = $this->getBarGraphStackDataIrrelevant('LeadData', 'created_at', $fl_userid, 'assign_to');







    $i = 0;

    foreach ($ireelead_arr as $key => $value) {

      if ($i == 30) {
      } else {
        $finances_g2->addRow([$key, $value]);
        $i++;
      }
    }





    $bo_level = 'BOLEAD_G3';


    $donutchart = \Lava::ColumnChart($bo_level, $finances_g2, [
      'title' => 'Last 30 Days  Irrelevant Lead ',
      'legend' => [
        'position'    => 'top',
        'maxLines' => 3

      ],
      'colors' => ['Tomato'],
      'titleTextStyle' => [
        'color'    => '#000000',
        'fontSize' => 14

      ]
    ]);


    //***************************** */

    //***************************** */
    $finances_g3 = $lava->DataTable();
    $finances_g3->addDateColumn('Year')
      ->addNumberColumn('Assigned')
      ->setDateTimeFormat('Y-m-d');
    //echo "<pre>";
    $fl_userid = 77;
    $ireelead_arr = $this->getBarGraphStackDataAssigned('LeadDataProcess', 'created_at', $fl_userid, 'assign_to');
    $i = 0;

    foreach ($ireelead_arr as $key => $value) {

      if ($i == 30) {
      } else {
        $finances_g3->addRow([$key, $value]);
        $i++;
      }
    }





    $bo_level = 'BOLEAD_G4';


    $donutchart = \Lava::ColumnChart($bo_level, $finances_g3, [
      'title' => 'Last 30 Days Assign Lead ',
      'legend' => [
        'position'    => 'top',
        'maxLines' => 3

      ],
      'colors' => ['#16426B'],
      'titleTextStyle' => [
        'color'    => '#000000',
        'fontSize' => 14

      ]
    ]);


    //***************************** */



    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('lead.leadReports', $data)->render();
  }
  public function printLabel($sampleID, $newSaple = null)
  {
    $users_data = DB::table('samples')->where('sample_code', $sampleID)->first();
    $users_data_1 = DB::table('samples')->where('sample_code', $newSaple)->first();



    $theme = Theme::uses('corex')->layout('layout');
    $data = [
      'sample_data' => $users_data,
      'sample_data_1' => $users_data_1
    ];

    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $user_role = $userRoles[0];
    if ($user_role == 'Admin' || $user_role == 'CourierTrk') {
      return $theme->scope('sample.sampleLablePrint', $data)->render();
    } else {
      abort(401);
    }
  }






  public function add_lead_data()
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('lead.add_new_lead', $data)->render();
  }
  //LEAD

  //HRMS
  public function setClientUpdation(Request $request)
  {
    $cid = $request->cid;
    $txtClientGST = $request->txtClientGST;
    $txtClientAddress = $request->txtClientAddress;
    DB::table('clients')
      ->where('id', $cid)
      ->update([
        'gstno' => $txtClientGST,
        'address' => $txtClientAddress
      ]);
  }
  public function myAttendance()
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('hrms.myAttendance', $data)->render();
  }
  public function getIndividualAttendance(Request $request)
  {
    $recordID = $request->recordID;
    $users_data = DB::table('emp_attendance_data')->where('id', $recordID)->first();
    $data_arrs = json_decode($users_data->atten_data);
    $HTML = '<div class="m-section">
        <div class="m-section__content">
          <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
            <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Timing</th>
              </tr>
            </thead>
            <tbody>';

    $i = 0;
    foreach ($data_arrs as $key => $row) {
      $i++;
      $yr = $users_data->atten_yr;
      $mo = $users_data->attn_month;
      $dateON = $yr . "-" . $mo . "-" . $i;
      $created_on = date('l jS F Y', strtotime($dateON));
      //ajcode
      $contains = Str::contains($row[0], ':');
      if ($contains == 1) {

        //get hour of day
        $today_arr = explode(" ", $row[0]);
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

        //  if($hours<9){
        //      $lf++;
        //  }
        $badata = $hours . "Hr " . $minutes . "m";
        if (intVal($hours) >= 9) {
          $badge = '<span class="m-badge m-badge--success m-badge--wide m-badge--rounded">' . $badata . '</span>';
        } else {
          $badge = '<span class="m-badge m-badge--warning m-badge--wide m-badge--rounded">' . $badata . '</span>';
        }
        $whour = $row[0] . ":" . $badge;
      } else {
        $whour = '';
      }

      //ajcode





      $HTML .= '<tr>
              <th scope="row">' . $i . '</th>
              <td>' . $created_on . '</td>
              <td>' . $whour . '.</td>

            </tr>';
    }
    $HTML .= '
                      </tbody>
                    </table>
                  </div>
                </div>';
    echo $HTML;
  }



  public function getMyMasterAttenDance(Request $request)
  {
    $users_data = DB::table('emp_attendance_data')->where('emp_id', Auth::user()->atten_id)->get();
    $data_arr_1 = array();
    foreach ($users_data as $key => $rowData) {
      $data_cal_arr = AyraHelp::getAttenCalulation($rowData->id);


      $data_arr_1[] = array(
        'RecordID' => $rowData->id,
        'emp_id' => $rowData->emp_id,
        'emp_name' => $rowData->name,
        'month' => date("F", mktime(0, 0, 0, $rowData->attn_month, 10)),
        'present' => $data_cal_arr['present_day'],
        'half_day' => '',
        'late_fine' => $data_cal_arr['hour_less_count'],
        'total_day' => '',
      );
    }

    $JSON_Data = json_encode($data_arr_1);
    $columnsDefault = [
      'RecordID'     => true,
      'emp_id'     => true,
      'emp_name'     => true,
      'month'  => true,
      'present'  => true,
      'half_day'  => true,
      'late_fine'  => true,
      'total_day'  => true,
      'Actions'      => true,
    ];
    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }


  public function getMasterAttenDance(Request $request)
  {
    $users_data = DB::table('emp_attendance_data')->get();
    $data_arr_1 = array();
    foreach ($users_data as $key => $rowData) {
      $data_cal_arr = AyraHelp::getAttenCalulation($rowData->id);


      $data_arr_1[] = array(
        'RecordID' => $rowData->id,
        'emp_id' => $rowData->emp_id,
        'emp_name' => $rowData->name,
        'month' => date("F", mktime(0, 0, 0, $rowData->attn_month, 10)),
        'present' => $data_cal_arr['present_day'],
        'half_day' => '',
        'late_fine' => $data_cal_arr['hour_less_count'],
        'total_day' => '',
      );
    }

    $JSON_Data = json_encode($data_arr_1);
    $columnsDefault = [
      'RecordID'     => true,
      'emp_id'     => true,
      'emp_name'     => true,
      'month'  => true,
      'present'  => true,
      'half_day'  => true,
      'late_fine'  => true,
      'total_day'  => true,
      'Actions'      => true,
    ];
    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }


  public function upload_epm_attendance()
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('hrms.empAttendance', $data)->render();
  }
  public function getKIPDetailsByUserDay(Request $request)
  {
    $kpi_arrs = KPIReport::where('id', $request->rowID)->first();
    $HTML = '<div class="m-section">
        <div class="m-section__content">
          <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
            <thead>
              <tr>
                <th>#</th>
                <th>KPI</th>
                <th>Achivement QTY</th>
                <th>Hours Spend</th>
              </tr>
            </thead>
            <tbody>';





    $i = 0;
    foreach (json_decode($kpi_arrs->kpi_own_task) as $key => $row) {
      $i++;
      $HTML .= '<tr>
        <th scope="row">' . $i . '</th>
        <td>' . $row->task_v1 . '</td>
        <td>' . $row->task_qty_v1 . '</td>
        <td>' . $row->task_spend_hour_v1 . '</td>
      </tr>';
    }
    $HTML .= '
                </tbody>
              </table>
            </div>
          </div>';

    $HTML . '<hr>';


    //-------------------
    $HTML .= '<div class="m-section">
        <div class="m-section__content">
          <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
            <thead>
              <tr>
                <th>#</th>
                <th>Task Discrption</th>
                <th>Achievement</th>
                <th>Hours Spend</th>
              </tr>
            </thead>
            <tbody>';





    $i = 0;

    foreach (json_decode($kpi_arrs->kpi_own_task) as $key => $rowData) {

      $i++;
      $HTML .= '<tr>
        <th scope="row">' . $i . '</th>
        <td>' . $rowData->task_v1 . '</td>
        <td>' . $rowData->task_qty_v1 . '</td>
        <td>' . $rowData->task_spend_hour_v1 . '</td>
      </tr>';
    }
    $HTML .= '
                </tbody>
              </table>
            </div>
          </div>';

    $HTML . '<hr>';
    $HTML .= '<div class="m-section">
          <div class="m-section__content">
            <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
              <thead>
                <tr>

                  <th>Remarks</th>

                </tr>
              </thead>
              <tbody>';







    $HTML .= '<tr>
          <th scope="row">' . $kpi_arrs->kpi_remarks . '</th>

        </tr>';

    $HTML .= '
                  </tbody>
                </table>
              </div>
            </div>';

    $HTML . '<hr>';



    echo $HTML;
  }


  public function kpiDetailHistory_all(Request $request)
  {
    $kpi_arrs = KPIReport::where('user_id', $request->empID)->get();
    $data_arr_1 = array();
    foreach ($kpi_arrs as $key => $rows) {
      //print_r($kpi_arrs);
      $data_arr_1[] = array(
        'id' => $rows->id,
        'kpi_date' => date('j F Y h:i A', strtotime($rows->kpi_date)),
        'kpi_month' => $rows->kpi_month_goal,
        'kpi_today' => $rows->kpi_today_goal,
        'kpi_remark' => $rows->kpi_remarks,

      );
    }

    $JSON_Data = json_encode($data_arr_1);
    $columnsDefault = [
      'id'     => true,
      'kpi_date'     => true,
      'kpi_month'     => true,
      'kpi_today'  => true,
      'kpi_remark'  => true,
      'Actions'      => true,
    ];
    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }


  public function getKPIDataReportHistory(Request $request)
  {
    $kpi_arrs = KPIReport::where('user_id', Auth::user()->id)->get();
    $data_arr_1 = array();
    foreach ($kpi_arrs as $key => $rows) {
      //print_r($kpi_arrs);
      $data_arr_1[] = array(
        'id' => $rows->id,
        'kpi_date' => date('j F Y h:i A', strtotime($rows->kpi_date)),
        'kpi_month' => $rows->kpi_month_goal,
        'kpi_today' => $rows->kpi_today_goal,
        'kpi_remark' => $rows->kpi_remarks,

      );
    }

    $JSON_Data = json_encode($data_arr_1);
    $columnsDefault = [
      'id'     => true,
      'kpi_date'     => true,
      'kpi_month'     => true,
      'kpi_today'  => true,
      'kpi_remark'  => true,
      'Actions'      => true,
    ];
    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }
  public function kpiDetailHistoryEMP(Request $request)
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('hrms.kpiReportEMP', $data)->render();
  }

  public function kpiDetailHistory(Request $request)
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('hrms.kpiReport', $data)->render();
  }
  public function saveKPIReportSubmit(Request $request)
  {
    $kpi_date = date('Y-m-d H:i:s', strtotime($request->kpi_date));
    $kpi_month = date('F', strtotime($request->kpi_date));

    //ajcode

    $taskAks = $request->taskAks;



    //ajcode

    //  $kpi_d_arr=$request->kpi_details;


    //  $kpi_n_arr=$request->kpi_number;
    //  $kpi_sph_arr=$request->kpi_spendhour;
    //  $kpi_data_arr=array();
    //  foreach ($kpi_d_arr as $key => $row) {
    //    $kpi_data_arr[]=array(
    //      'kpi_detail'=>$row,
    //      'kpi_number'=>$kpi_n_arr[$key],
    //      'kpi_shour'=>$kpi_sph_arr[$key],
    //    );

    //  }

    $kpi_data_arr_other[] = array(
      'kpi_detail' => $request->kpi_other_discption,
      'kpi_number' => $request->kpi_other_acthmentNo,
      'kpi_shour' => $request->kpi_other_spendHour,
    );
    $kpiObj = new KPIReport;
    $kpiObj->kpi_month_goal = $request->goal_for_month;
    $kpiObj->kpi_today_goal = $request->goal_for_today;
    $kpiObj->kpi_date = $kpi_date;
    $kpiObj->kpi_month = $kpi_month;
    $kpiObj->user_id = Auth::user()->id;
    // $kpiObj->kpi_detail=json_encode($kpi_data_arr);
    $kpiObj->kpi_other_details = json_encode($kpi_data_arr_other);
    $kpiObj->kpi_own_task = json_encode($taskAks);
    $kpiObj->kpi_remarks = $request->kpi_remarks;
    $kpiObj->save();

    //send email
    $sent_to = 'bointldev@gmail.com';
    //$myorder=$row['txtPONumber'];
    $html = "<p>This is </p>";
    $empName = 'AJAY KUMAR';
    $toaj = date('F');
    $curr_date = date('d-m-Y');
    $subLine = "Daily Report [ " . $curr_date . " ] " . "[" . $toaj . "]" . $empName;
    $myreport = array('3,4');
    $data = array(
      'html_report_data' => $myreport,
      'name' => 'Ajay',
      'designation' => 'WEB IT',
      'phoneNO' => '9711309624',
      'email' => 'ajayits2020@avas.com',
      'today_report' => date('j M Y'),
      'html' => $html



    );
    Mail::send('mail_daily_report', $data, function ($message) use ($sent_to, $data, $subLine) {

      $message->to($sent_to, 'Bo | Daily Report')->subject($subLine);
      //$message->cc($use_data->email,$use_data->name = null);
      $message->setBody($data['html'], 'text/html');
      $message->bcc('bointldev@gmail.com', 'HR Department');
      $message->from('bointldev@gmail.com', 'Bo International');
    });

    //send email


    return 1;
  }
  public function kpiDetails($kpi_id)
  {
    $emp_arr = KPIData::where('id', $kpi_id)->first();


    $theme = Theme::uses('corex')->layout('layout');
    $data = ['kpi_data' => $emp_arr];
    return $theme->scope('hrms.kpi_viewDeatils', $data)->render();
  }
  public function getKPIData()
  {

    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $user_role = $userRoles[0];
    if ($user_role == 'SalesUser') {
      $where_role = 'SALES';
    }
    if ($user_role == 'Staff') {
      $where_role = 'STAFF';
    }

    $data_arr_1 = array();
    $data_arr_2 = array();
    $aj_arr = array();

    if ($user_role == 'Admin') {
      $kpi_arr = KPIData::get();


      foreach ($kpi_arr as $key => $rowData) {
        $user_arr = AyraHelp::getUser($rowData->user_id);
        if ($user_arr != null) {
          $u_name = $user_arr->name;
        } else {
          $u_name = '';
        }

        $aj_arr[] = array(
          'RecordID' => $rowData->id,
          'kpi_role' => $rowData->kpi_role,
          'user_ID' => $rowData->user_id,
          'user_name' => $u_name,
          'kpi_department' => $rowData->kpi_department,
          'status' => 1
        );
      }
    } else {

      $kpi_arr = KPIData::where('user_id', Auth::user()->id)->get();
      $data_emp = Employee::where('user_id', Auth::user()->id)->first();
      $kpi_arr1 = KPIData::where('kpi_role', optional($data_emp)->job_role)->get();

      foreach ($kpi_arr as $key => $rowData) {
        $user_arr = AyraHelp::getUser($rowData->user_id);
        if ($user_arr != null) {
          $u_name = $user_arr->name;
        } else {
          $u_name = '';
        }

        $data_arr_1[] = array(
          'RecordID' => $rowData->id,
          'kpi_role' => $rowData->kpi_role,
          'user_ID' => $rowData->user_id,
          'user_name' => $u_name,
          'kpi_department' => $rowData->kpi_department,
          'status' => 1
        );
      }
      foreach ($kpi_arr1 as $key => $rowData) {
        $user_arr = AyraHelp::getUser($rowData->user_id);
        if ($user_arr != null) {
          $u_name = $user_arr->name;
        } else {
          $u_name = '';
        }

        $data_arr_2[] = array(
          'RecordID' => $rowData->id,
          'kpi_role' => $rowData->kpi_role,
          'user_ID' => $rowData->user_id,
          'user_name' => $u_name,
          'kpi_department' => $rowData->kpi_department,
          'status' => 1
        );
      }

      $aj_arr = array_merge($data_arr_1, $data_arr_2);
    }






    $JSON_Data = json_encode($aj_arr);
    $columnsDefault = [
      'RecordID'     => true,
      'kpi_role'     => true,
      'user_ID'     => true,
      'user_name'     => true,
      'kpi_department'     => true,
      'status'  => true,
      'Actions'      => true,
    ];
    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }
  public function saveKPIData(Request $request)
  {


    // $objKPIData=new KPIData;
    // $objKPIData->user_id=$request->user_id;
    // $objKPIData->kpi_role=$request->job_role;
    // $objKPIData->kpi_department=$request->department_data;
    // $objKPIData->kpi_detail=json_encode($request->KPIData);
    // $objKPIData->save();

    //  return 1;
    //admin can add kpi for particular user as well as role and all add  more to user

    if (isset($request->user_id)) {
      $user_id = $request->user_id;
      DB::table('kpi_data')->insert(
        [
          'user_id' => $request->user_id,
          'kpi_department' => $request->department_data,
          'kpi_detail' => json_encode($request->KPIData)
        ]
      );
    }
    if (isset($request->job_role)) {

      $job_role = $request->job_role;

      DB::table('kpi_data')->insert(
        [
          'kpi_role' => $job_role,
          'kpi_department' => $request->department_data,
          'kpi_detail' => json_encode($request->KPIData)
        ]
      );
    }

    return 1;
  }

  public function jobRole(Request $request)
  {
    $theme = Theme::uses('corex')->layout('hrmsLayout');
    $data = ['users' => ''];
    return $theme->scope('hrms.job_role', $data)->render();
  }
  public function deleteEMP(Request $request)
  {
    Employee::where('id', $request->emp_id)
      ->update(['is_deleted' => 1]);
    $data_arr = array(
      'status' => 1,
      'msg' => 'Deleted successfully'
    );
    return response()->json($data_arr);
  }

  public function empView($emp_id)
  {
    $emp_arr = Employee::where('id', $emp_id)->first();
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['user_data' => $emp_arr];
    return $theme->scope('hrms.employee_view', $data)->render();
  }

  public function getEmpListData(Request $request)
  {

    $data_arr_1 = array();
    $emp_arr = Employee::where('is_deleted', 0)->get();
    //$emp_arr=Employee::get();
    $i = 0;
    foreach ($emp_arr as $key => $Row) {
      $i++;
      // http://demo.local/local/public/img/avatar.jpg
      if (isset($Row->photo)) {
        $img_photo = asset('local/public/uploads/photos') . "/" . optional($Row)->photo;
      } else {
        $img_photo = asset('local/public/img/avatar.jpg');
      }
      if (!empty($Row->job_role)) {
        $jobRoleArr = AyraHelp::getJobRoleByid($Row->job_role);
        $role_name = $jobRoleArr->name;
      } else {
        $role_name = 'N/A';
      }



      $data_arr_1[] = array(
        'RecordID' => $Row->id,
        'photo' => $img_photo,
        'empID' => $Row->emp_code,
        'name' => $Row->name,
        'user_id' => $Row->user_id,
        'email' => $Row->email,
        'office_email' => $Row->comp_email,
        'phone' => $Row->phone,
        'department' => $Row->phone,
        'job_role' => $role_name,
        'user_status' => $Row->user_status,
        'Actions' => ""
      );
    }

    $JSON_Data = json_encode($data_arr_1);
    $columnsDefault = [
      'RecordID'  => true,
      'photo'     => true,
      'empID'     => true,
      'name'      => true,
      'user_id'   => true,
      'email'      => true,
      'office_email' => true,
      'phone'      => true,
      'department' => true,
      'job_role'   => true,
      'is_deleted' => true,
      'Actions'    => true,
    ];
    $this->DataGridResponse($JSON_Data, $columnsDefault);
  }

  public function getLocation(Request $request)
  {
    $pincode_arr = AyraHelp::getAddressByPincode($request->pincode);
    if ($pincode_arr == null) {
      $res_arr = array(
        'status' => 0,
        'data' => '',
        'Message' => 'Location by pincode',
      );
    } else {
      $res_arr = array(
        'status' => 1,
        'data' => $pincode_arr,
        'Message' => 'Location by pincode',
      );
    }
    return response()->json($res_arr);
  }

  public function kpiupdateData(Request $request)
  {

    // echo "<pre>";
    // print_r($request->all());
    //DB::table('kpi_data')->where('id',$request->txtKPIID)->delete();

    $validatedData = $request->validate([
      'user_id' => 'required',

    ]);


    if (isset($request->user_id)) {
      $user_id = $request->user_id;
      DB::table('kpi_data')
        ->where('id', $request->txtKPIID)
        ->update([
          'user_id' => $request->user_id,
          'kpi_department' => $request->department_data,
          'kpi_detail' => json_encode($request->KPIData)
        ]);

      // $ida= DB::table('kpi_data')->insert(
      //  [
      //    'user_id' => $request->user_id,
      //    'kpi_department' =>$request->department_data,
      //    'kpi_detail' =>json_encode($request->KPIData)
      //  ]
      // );
      return redirect()->route('jobRole');
    }
    if (isset($request->job_role)) {

      $job_role = $request->job_role;

      $ida = DB::table('kpi_data')->insert(
        [
          'kpi_role' => $job_role,
          'kpi_department' => $request->department_data,
          'kpi_detail' => json_encode($request->KPIData)
        ]
      );
      return redirect()->route('kpiDetails', ['id' => $ida]);
    }




    //return back()->with('success', 'Saved Changes Successfully..');




  }

  public function updateEmpdata(Request $request)
  {



    $epm_id = $request->txtUserID;
    $empCODE = $request->txtEMPCODE;
    $num = $request->atten_ID;
    $user_status = $request->user_status;



    $str_length = 4;

    if (isset($request->emp_code)) {
      $sid_code = $request->emp_code;
    } else {
      $sid_code = "EMP" . substr("0000{$num}", -$str_length);
    }


    Employee::where('id', $request->txtUserID)
      ->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'dob' => date("Y-m-d", strtotime($request->birth_date)),
        'doj' => date("Y-m-d", strtotime($request->join_date)),

        'department_id' => $request->department,
        'designation_id' => $request->designation,
        'gender' => $request->gender,
        'address' => $request->address,
        'pincode' => $request->pincode,
        'city' => $request->loccity,
        'state' => $request->locstate,
        'comp_email' => $request->offcial_email,
        'pan_card' => $request->pan_no,
        'aadhar_card' => $request->aadhar_no,
        'basic_salary' => $request->basic_salary,
        'atten_ID' => $request->atten_ID,
        'user_status' => $request->user_status,
        'emp_code' => $sid_code,
        'bank_name' => $request->bank_name,
        'account_no' => $request->account_no,
        'ifsc_code' => $request->ifsc_code,
        'job_role' => $request->jobrole,
        'doe' => date("Y-m-d", strtotime($request->exit_date)),
      ]);



    if ($request->hasFile('pan_doc')) {
      $file = $request->file('pan_doc');
      $filename = $empCODE . "_pan" . date('Ymshis') . '.' . $file->getClientOriginalExtension();
      // save to local/uploads/photo/ as the new $filename
      $path = $file->storeAs('photos', $filename);
      Employee::where('id', $epm_id)
        ->update(['pan_doc_img' => $filename]);
    }

    if ($request->hasFile('emp_photo')) {

      $file = $request->file('emp_photo');
      $filename = $empCODE . "_EMPPhoto" . date('Ymshis') . '.' . $file->getClientOriginalExtension();
      // save to local/uploads/photo/ as the new $filename
      $path = $file->storeAs('photos', $filename);
      Employee::where('id', $epm_id)
        ->update(['photo' => $filename]);
    }

    if ($request->hasFile('aadhar_doc')) {
      $file = $request->file('aadhar_doc');
      $filename = $empCODE . "_adhar" . date('Ymshis') . '.' . $file->getClientOriginalExtension();
      // save to local/uploads/photo/ as the new $filename
      $path = $file->storeAs('photos', $filename);
      Employee::where('id', $epm_id)
        ->update(['aadhar_doc_img' => $filename]);
    }


    return back()->with('success', 'Saved Changes Successfully..');
  }
  public function saveEmployee(Request $request)
  {

    $empCODE = AyraHelp::getEMPCODE();
    $objEmp = new Employee;
    $objEmp->emp_code = 'N/A';
    $objEmp->name = $request->name;
    $objEmp->email = $request->email;
    $objEmp->phone = $request->phone;
    $objEmp->dob = date("Y-m-d", strtotime($request->birth_date));
    $objEmp->doj = date("Y-m-d", strtotime($request->join_date));
    $objEmp->department_id = $request->department;
    $objEmp->designation_id = $request->designation;
    $objEmp->gender = $request->gender;
    $objEmp->address = $request->address;
    $objEmp->pincode = $request->pincode;
    $objEmp->city = $request->loccity;
    $objEmp->state = $request->locstate;
    $objEmp->comp_email = $request->offcial_email;
    $objEmp->pan_card = $request->pan_no;
    $objEmp->aadhar_card = $request->aadhar_no;
    $objEmp->pan_card = $request->pan_no;
    $objEmp->aadhar_card = $request->aadhar_no;


    // $objEmp->bank_name=$request->bank_name;
    // $objEmp->account_no=$request->account_no;
    // $objEmp->ifsc_code=$request->ifsc_code;

    $objEmp->created_by = Auth::user()->id;
    $objEmp->save();
    $epm_id = $objEmp->id;

    if ($request->hasFile('pan_doc')) {
      $file = $request->file('pan_doc');
      $filename = $empCODE . "_pan" . date('Ymshis') . '.' . $file->getClientOriginalExtension();
      // save to local/uploads/photo/ as the new $filename
      $path = $file->storeAs('photos', $filename);
      Employee::where('id', $epm_id)
        ->update(['pan_doc_img' => $filename]);
    }

    if ($request->hasFile('emp_photo')) {
      $file = $request->file('emp_photo');
      $filename = $empCODE . "_EMPPhoto" . date('Ymshis') . '.' . $file->getClientOriginalExtension();
      // save to local/uploads/photo/ as the new $filename
      $path = $file->storeAs('photos', $filename);
      Employee::where('id', $epm_id)
        ->update(['photo' => $filename]);
    }

    if ($request->hasFile('aadhar_doc')) {
      $file = $request->file('aadhar_doc');
      $filename = $empCODE . "_adhar" . date('Ymshis') . '.' . $file->getClientOriginalExtension();
      // save to local/uploads/photo/ as the new $filename
      $path = $file->storeAs('photos', $filename);
      Employee::where('id', $epm_id)
        ->update(['aadhar_doc_img' => $filename]);
    }
  }
  public function HrDashbaord(Request $request)
  {
    $theme = Theme::uses('corex')->layout('hrmsLayout');
    $data = ['users' => ''];
    return $theme->scope('hrms.dashboard', $data)->render();
  }
  public function employee(Request $request)
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = ['users' => ''];
    return $theme->scope('hrms.employee', $data)->render();
  }


  //HRMS



  public function reportSalesGraph(Request $request)
  {
    $theme = Theme::uses('corex')->layout('layout');
    $lava = new Lavacharts; // See note below for Laravel




    //code for show sales values monthly
    //code for order values
    $finances_orderValue = $lava->DataTable();
    $finances_orderValue->addDateColumn('Year')
      ->addNumberColumn('Order Value')
      ->setDateTimeFormat('Y-m-d');
    for ($x = 4; $x <= 12; $x++) {
      $d = cal_days_in_month(CAL_GREGORIAN, $x, date('Y'));

      //$active_date=date('Y')."-".$x."-1";

      if ($x >= 5) {
        $active_date = "2020-" . $x . "-1";
      } else {
        $active_date = date('Y') . "-" . $x . "-1";
      }

      $data_output = AyraHelp::getOrderValueFilter($x);
      $finances_orderValue->addRow([$active_date, $data_output]);
    }




    $donutchart = \Lava::ColumnChart('FinancesOrderValueMonthly', $finances_orderValue, [
      'title' => 'Order Value ',
      'titleTextStyle' => [
        'color'    => '#16426B',
        'fontSize' => 14
      ],

    ]);



    //code for order values
    //code for show sales values monthly



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


    $sales_arr = AyraHelp::getSalesAgentOnly();

    foreach ($sales_arr as $key => $value) {
      $s_userid = $value->id;
      $sname = $value->name;

      $finances = $lava->DataTable();
      $finances->addDateColumn('Year')
        ->addNumberColumn('NOTES')
        ->addNumberColumn('FOLLOW UP')
        ->setDateTimeFormat('Y-m-d');




      $clinet_arr = $this->getBarGraphStackData('ClientNote', 'created_at', $s_userid, 'user_id');

      $follow_arr = $this->getBarGraphStackData('Client', 'follow_date', $s_userid, 'added_by');


      $data = array();
      foreach ($follow_arr as $key => $value) {
        $data[] = $value;
      }
      $i = 0;

      foreach ($clinet_arr as $key => $value) {

        if ($i == 30) {
        } else {
          $finances->addRow([$key, $value, $data[$i]]);
          $i++;
        }
      }





      $bo_level = 'BO' . $s_userid;


      $donutchart = \Lava::ColumnChart($bo_level, $finances, [
        'title' => 'Last 30 Days Notes & Follow Up :' . $sname,
        'legend' => [
          'position'    => 'top',
          'maxLines' => 3

        ],
        'titleTextStyle' => [
          'color'    => '#16426B',
          'fontSize' => 14

        ]
      ]);
    }






    return $theme->scope('reports.sales', $data)->render();
  }
  public function saveuserPermission(Request $request)
  {

    foreach ($request->permissions as $key => $perms) {

      $mhp_data = MHP::where('permission_id', $perms)->where('model_id', $request->user_id)->first();
      if ($mhp_data == null) {
        $mhpobe = new MHP;
        $mhpobe->permission_id = $perms;
        $mhpobe->model_type = 'App\User';
        $mhpobe->model_id = $request->user_id;
        $mhpobe->save();
      } else {
      }
    }
    //

  }
  public function addPermissionUsers(Request $request)
  {
    $theme = Theme::uses('corex')->layout('layout');
    $users = User::all();
    $permissions = Permission::all();

    $data = ['users' => $users, 'permissions' => $permissions];
    return $theme->scope('users.add_permission_user', $data)->render();
  }

  public function setUserPermission(Request $request)
  {
    $uid = $request->user_id;
    $perm_state = $request->perm_state;
    $perm_data = $request->perm_data;
    $user = User::find($request->user_id);
    if ($perm_state === 'true' || $perm_state === 'TRUE') {

      //  $mhpobe=new MHP;
      //  $mhpobe->permission_id=$perm_data;
      //  $mhpobe->model_type='App\User';
      //  $mhpobe->model_id=$uid;
      //  $mhpobe->save();
      $user->givePermissionTo($perm_data);
      $res_arr = array(
        'status' => 1,
        'type' => 'success',
        'Message' => $perm_data . " Permission added successfully",
      );
    }
    if ($perm_state === 'false' || $perm_state === 'FALSE') {

      // MHP::where('permission_id', $perm_data)->where('model_id', $uid)->delete();
      $user->revokePermissionTo($perm_data);
      $res_arr = array(
        'status' => 1,
        'type' => 'warning',
        'Message' => $perm_data . " Permission Removed successfully",
      );
    }

    return response()->json($res_arr);
  }

  public function userPermissions()
  {
    $theme = Theme::uses('corex')->layout('layout');
    $users = User::orderby('id', 'desc')->get();

    $data = ['users' => $users];
    return $theme->scope('users.user_permission', $data)->render();
  }
  public function UserResetPassword(Request $request)
  {

    if (!(Hash::check($request->get('current'), Auth::user()->password))) {
      // The passwords matches
      $res_arr = array(
        'status' => 2,
        'Message' => 'Your current password does not matches with the password you provided. Please try again..',
      );
      return response()->json($res_arr);
    }
    if (strcmp($request->get('current'), $request->get('password')) == 0) {
      //Current password and new password are same
      $res_arr = array(
        'status' => 3,
        'Message' => 'New Password cannot be same as your current password. Please choose a different password..',
      );
      return response()->json($res_arr);
    }

    $id = $request->user_id;
    $user = User::findOrFail($id);
    $this->validate($request, [
      'password' => 'required'
    ]);

    $input = $request->only(['password']);
    $user->fill($input)->save();
    $res_arr = array(
      'status' => 1,
      'Message' => 'Password saved successfully.',
    );
    return response()->json($res_arr);
  }

  // userAccessRemove
  public function userAccessRemove(Request $request)
  {

    $user = UserAccess::find($request->rowid);
    $user->delete();
  }
  // userAccessRemove

  //userAccess
  public function userAccess(Request $request)
  {
    $checkuser = UserAccess::where('access_by', Auth::user()->id)->where('access_to', $request->catsalesUser)->where('client_id', $request->client_id)->first();
    if ($checkuser == null) {
      //echo "inset now";
      $userAccessobj = new UserAccess;
      $userAccessobj->access_by = Auth::user()->id;
      $userAccessobj->client_id = $request->client_id;

      $userAccessobj->access_to = $request->catsalesUser;
      $userAccessobj->created_by = Auth::user()->id;
      $userAccessobj->remarks = '';
      $userAccessobj->user_exp_date = date('Y-m-d');
      $userAccessobj->save();
    } else {
      //echo "update time perios";
    }
  }

  //userProfile
  public function userProfile()
  {
    $theme = Theme::uses('corex')->layout('layout');
    $users = User::orderby('id', 'desc')->get();

    $data = ['users' => $users];
    return $theme->scope('users.profile', $data)->render();
  }
  //userProfile


  //getCountry
  public function getCountry(Request $request, $id)
  {

    $getAttr = DB::table('country_cities')->select('country_id')->where('id', $id)->first();
    $getAttrC = DB::table('countries')->where('id', $getAttr->country_id)->first();
    $data = array('id' => $getAttrC->id, 'name' => $getAttrC->name);
    return response()->json($data);
  }
  //getCountry
  public function getCity(Request $request)
  {
    $q = $request->q;


    $getAttr = DB::table('country_cities')->select('id', 'name')->where('name', 'like', "$q%")->get();
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
    $data = array(
      'total_count' => 1222,
      'incomplete_results' => 1222,
      'items' => $getAttr,
    );
    return response()->json($data);
  }
  public function setContactClient(Request $request)
  {
    $contactClient = new ContactClient;
    $contactClient->name = $request->name;
    $contactClient->email = $request->email;
    $contactClient->phone = $request->phone;
    $contactClient->parent_userid = $request->recordID;
    $contactClient->addedby = $request->added_by;
    $contactClient->created_at = date('Y-m-d H:i:s');
    $contactClient->save();
    $res_arr = array(
      'status' => 1,
      'Message' => 'Data saved successfully.',
    );
    return response()->json($res_arr);
  }

  public function saveRowClient(Request $request)
  {
    $rowClient = new RowClient;
    $rowClient->name = $request->name;
    $rowClient->email = $request->email;
    $rowClient->phone = $request->phone;
    $rowClient->company = $request->company;
    $rowClient->remarks = $request->remarks;
    $rowClient->brand_name = $request->brand_name;
    $rowClient->gst = $request->gst;
    $rowClient->address = $request->address;
    $rowClient->save();
    $res_arr = array(
      'status' => 1,
      'Message' => 'Data saved successfully.',
    );
    return response()->json($res_arr);
  }
  // save row client

  public function rowClientList()
  {
    $theme = Theme::uses('admin')->layout('layout');
    $users = User::orderby('id', 'desc')->get();
    $users_staff = User::role('Staff')->get();
    $data = ['users' => $users, 'users_staff' => $users_staff];
    return $theme->scope('users.row_client_list', $data)->render();
  }
  public function add_ajax_clients(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|max:120',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6|confirmed'
    ]);
    $user = User::create($request->only('email', 'name', 'password'));
    $role_r = 'Client';
    $user->assignRole($role_r);
    $insertedId = $user->id;
    $comp_obj = new Company;
    $comp_obj->user_id = $insertedId;
    $comp_obj->company_name = $request->compname;
    $comp_obj->user_role = 'RootClient';
    $comp_obj->brand_name = $request->brand_name;
    $comp_obj->gst_details = $request->gst_details;
    $comp_obj->address = $request->address;
    $comp_obj->sale_agent_id = $request->sale_agent;
    $comp_obj->remarks = $request->remarks;
    $comp_obj->save();
    $res_arr = array(
      'status' => 1,
      'Message' => 'Data saved successfully.',
    );
    return response()->json($res_arr);
  }

  public function clinetListforDelete()
  {
    $theme = Theme::uses('admin')->layout('layout');
    $users = User::orderby('id', 'desc')->get();
    $users_staff = User::role('Staff')->get();
    $data = ['users' => $users, 'users_staff' => $users_staff];
    return $theme->scope('users.view_clientsfordelte', $data)->render();
  }
  public function clinetList()
  {
    $theme = Theme::uses('admin')->layout('layout');
    $users = User::orderby('id', 'desc')->get();
    $users_staff = User::role('Staff')->get();
    $data = ['users' => $users, 'users_staff' => $users_staff];
    return $theme->scope('users.view_clients', $data)->render();
  }
  public function sampleList()
  {
    $theme = Theme::uses('admin')->layout('layout');
    $users = User::orderby('id', 'desc')->get();
    $data = ['users' => $users];
    return $theme->scope('users.view_samples', $data)->render();
  }


  public function index()
  {
    // $users = User::all();
    $theme = Theme::uses('corex')->layout('layout');
    $users = User::orderby('id', 'desc')->with('roles')->get();

    $data = ['users' => $users];
    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $user_role = $userRoles[0];
    if ($user_role == 'Admin') {
      return $theme->scope('users.index', $data)->render();
    } else {
      abort('401');
    }


    //return view('users.index')->with('users', $users);
  }
  // getAllLeadData_OWNLEAD

  public function getAllLeadData_OWNLEAD(Request $request)
  {
    $users = DB::table('client_sales_lead')->where('QUERY_ID', $request->rowid)->first();

    DB::table('client_sales_lead')
      ->where('QUERY_ID', $request->rowid)
      ->update([
        'view_status' => 1,
      ]);


    $assign_to = AyraHelp::getUser($users->assign_to)->name;

    if ($users->updated_by == NULL) {
      $updated_by = 'Harsit';
    } else {
      $updated_by = AyraHelp::getUser($users->updated_by)->name;
    }


    $assign_on = date("j M Y h:i:sA", strtotime($users->assign_on));
    $users_lead_assign = DB::table('lead_assign')->where('QUERY_ID', $request->rowid)->first();
    $users_lead_moves = DB::table('lead_moves')->where('QUERY_ID', $request->rowid)->get();
    $users_lead_notesby_sales = DB::table('lead_notesby_sales')->where('QUERY_ID', $request->rowid)->get();
    $users_lead_lead_notes = DB::table('lead_notes')->where('QUERY_ID', $request->rowid)->get();

    $users_lead_lead_chat_histroy = DB::table('lead_chat_histroy')->where('QUERY_ID', $request->rowid)->get();




    $created_on = date('j M Y h:i A', strtotime($users->created_at));

    if (count($users_lead_lead_chat_histroy) > 0) {

      $HTML_HIST = '   <table class="table table-sm m-table m-table--head-bg-primary">
        <thead class="thead-inverse">
          <tr>

            <th>User</th>
            <th>Stage</th>
            <th>Message</th>
            <th>Created</th>
          </tr>
        </thead>
        <tbody>';


      foreach ($users_lead_lead_chat_histroy as $key => $Leadrow) {
        $assign_by = AyraHelp::getUser($Leadrow->user_id)->name;

        $users_lead_move_created_on = date('j M Y h:iA', strtotime($Leadrow->created_at));
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];

        $HTML_HIST .= '<tr>

          <td>' . $assign_by . '</td>
          <td></td>
          <td>' . $Leadrow->msg . '</td>
          <td>' . $users_lead_move_created_on . '</td>
        </tr>';
      }


      $HTML_HIST .= '<tr>

        <td>Auto</td>
        <td>Fresh Lead</td>
        <td>5</td>
        <td>' . $created_on . '</td>
      </tr>';
      //remarks

      if (isset($users->remarks)) {
        $HTML_HIST .= '<tr>

        <td>' . $updated_by . '</td>
        <td>Remaks</td>
        <td>' . $users->remarks . '</td>
        <td></td>
      </tr>';
      }


      //remarks


      $HTML_HIST .= '</tbody> </table>';
    } else {
      $HTML_HIST = '   <table class="table table-sm m-table m-table--head-bg-primary">
                  <thead class="thead-inverse">
                    <tr>

                      <th>User</th>
                      <th>Stage</th>
                      <th>Message</th>
                      <th>Created</th>
                    </tr>
                  </thead>
                  <tbody>';

      $HTML_HIST .= '<tr>

                      <td>Auto</td>
                      <td>Fresh Lead</td>
                      <td></td>
                      <td>' . $created_on . '</td>
                    </tr>';
      //remarks

      if (isset($users->remarks)) {
        $HTML_HIST .= '<tr>

                      <td>' . $updated_by . '</td>
                      <td>Remaks</td>
                      <td>' . $users->remarks . '</td>
                      <td></td>
                    </tr>';
      }


      //remarks

      if ($users_lead_assign != null) {
        $assign_by = AyraHelp::getUser($users_lead_assign->assign_by)->name;
        $assign_user_id = AyraHelp::getUser($users_lead_assign->assign_user_id)->name;
        $users_lead_assign_created_on = date('j M Y h:iA', strtotime($users_lead_assign->created_at));

        $HTML_HIST .= '<tr>

                      <td>' . $assign_user_id . '</td>
                      <td>Assigned</td>
                      <td>' . $users_lead_assign->msg . '</td>
                      <td>' . $users_lead_assign_created_on . '</td>
                    </tr>';
      }

      if (count($users_lead_moves) > 0) {

        foreach ($users_lead_moves as $key => $Leadrow) {
          $assign_by = AyraHelp::getUser($Leadrow->assign_by)->name;
          $assign_to = AyraHelp::getUser($Leadrow->assign_to)->name;
          $users_lead_move_created_on = date('j M Y h:iA', strtotime($Leadrow->created_at));
          $user = auth()->user();
          $userRoles = $user->getRoleNames();
          $user_role = $userRoles[0];
          if ($user_role == 'Admin') {
            $mgdata = $Leadrow->msg . "(" . $Leadrow->assign_remarks . ")";
          } else {
            $mgdata = $Leadrow->msg;
          }

          $HTML_HIST .= '<tr>

                        <td>' . $assign_to . '</td>
                        <td>' . optional($Leadrow)->stage_name . '</td>
                        <td>' . $mgdata . '</td>
                        <td>' . $users_lead_move_created_on . '</td>
                      </tr>';
        }
      }

      if (count($users_lead_notesby_sales) > 0) {

        foreach ($users_lead_notesby_sales as $key => $Leadrow) {
          $assign_by = AyraHelp::getUser($Leadrow->added_by)->name;

          $users_lead_move_created_on = date('j M Y h:iA', strtotime($Leadrow->created_at));
          $user = auth()->user();
          $userRoles = $user->getRoleNames();
          $user_role = $userRoles[0];


          $HTML_HIST .= '<tr>

                        <td>' . $assign_by . '</td>
                        <td></td>
                        <td>' . $Leadrow->message . '</td>
                        <td>' . $users_lead_move_created_on . '</td>
                      </tr>';
        }
      }

      if (count($users_lead_lead_notes) > 0) {

        foreach ($users_lead_lead_notes as $key => $Leadrow) {
          $assign_by = AyraHelp::getUser($Leadrow->created_by)->name;

          $users_lead_move_created_on = date('j M Y h:iA', strtotime($Leadrow->created_at));
          $user = auth()->user();
          $userRoles = $user->getRoleNames();
          $user_role = $userRoles[0];

          $HTML_HIST .= '<tr>

                        <td>' . $assign_by . '</td>
                        <td></td>
                        <td>' . $Leadrow->msg . '</td>
                        <td>' . $users_lead_move_created_on . '</td>
                      </tr>';
        }
      }






      $HTML_HIST .= '</tbody> </table>';
    }



    $HTML = '<!--begin::Section-->
      <div class="m-section">
        <div class="m-section__content">
          <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
            <thead>
              <tr >
                <th colspan="3">Leads Full Information</th>
              </tr>
            </thead>
            <tbody>';
    $i = 0;



    // $HTML .='
    // <tr>
    //   <th scope="row">1</th>
    //   <td>Assign Message</td>
    //   <td>'.optional($users_lead_assign)->msg.'</td>

    // </tr>';

    // $HTML .='
    // <tr>

    //   <td colspan="3">'.$MyTable.'</td>

    // </tr>';



    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>QUERY_ID</td>
                <td>' . $users->QUERY_ID . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>SENDER NAME</td>
                <td>' . $users->SENDERNAME . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>SENDERE MAIL</td>
                <td>' . $users->SENDEREMAIL . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>SUBJECT</td>
                <td>' . $users->SUBJECT . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>DATE TIME</td>
                <td>' . $users->DATE_TIME_RE . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>COMPANY NAME</td>
                <td>' . $users->GLUSR_USR_COMPANYNAME . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>MOBILE No.</td>
                <td>' . $users->MOB . '</td>

              </tr>';



    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>COUNTRY FLAG

                </td>
                <td><img src="' . $users->COUNTRY_FLAG . '"></td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>MESSAGE

                </td>
                <td>' . $users->ENQ_MESSAGE . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>ADDRESS

                </td>
                <td>' . $users->ENQ_ADDRESS . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>CITY

                </td>
                <td>' . $users->ENQ_CITY . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>STATE

                </td>
                <td>' . $users->ENQ_STATE . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>STATE

                </td>
                <td>' . $users->ENQ_STATE . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>PRODUCT NAME

                </td>
                <td>' . $users->PRODUCT_NAME . '</td>

              </tr>';


    // $HTML .='
    // <tr>
    //   <th scope="row">1</th>
    //   <td> 	COUNTRY ISO

    //   </td>
    //   <td>'.$users->COUNTRY_ISO.'</td>

    // </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>EMAIL ALT

                </td>
                <td>' . $users->EMAIL_ALT . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>MOBILE  ALT

                </td>
                <td>' . $users->MOBILE_ALT . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>PHONE

                </td>
                <td>' . $users->PHONE . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>PHONE ALT

                </td>
                <td>' . $users->PHONE_ALT . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>MEMBER SINCE

                </td>
                <td>' . $users->IM_MEMBER_SINCE . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>Created At

                </td>
                <td>' . $created_on . '</td>

              </tr>';

    $lsource = "";

    $LS = $users->data_source;
    if ($LS == 'INDMART-9999955922@API_1' || $LS == 'INDMART-9999955922') {
      $lsource = 'IM1';
    }
    if ($LS == 'INDMART-8929503295@API_2' || $LS == 'INDMART-8929503295') {
      $lsource = 'IM2';
    }


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>Lead Souce

                </td>
                <td>' . $lsource . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>Remarks

                </td>
                <td><strong style="color:#16426B">' . $users->remarks . '</strong></td>

              </tr>';









    $HTML .= '
            </tbody>
          </table>
        </div>
      </div>

      <!--end::Section-->';


    $resp = array(
      'HTML_LEAD' => $HTML,
      'HTML_ASSIGN_HISTORY' => $HTML_HIST,

    );
    return response()->json($resp);
  }

  // getAllLeadData_OWNLEAD

  public function getAllLeadData(Request $request)
  {
    $users = DB::table('indmt_data')->where('QUERY_ID', $request->rowid)->first();

    DB::table('indmt_data')
      ->where('QUERY_ID', $request->rowid)
      ->update([
        'view_status' => 1,
      ]);


    $assign_to = AyraHelp::getUser($users->assign_to)->name;

    if ($users->updated_by == NULL) {
      $updated_by = 'Harsit';
    } else {
      $updated_by = AyraHelp::getUser($users->updated_by)->name;
    }


    $assign_on = date("j M Y h:i:sA", strtotime($users->assign_on));
    $users_lead_assign = DB::table('lead_assign')->where('QUERY_ID', $request->rowid)->first();
    $users_lead_moves = DB::table('lead_moves')->where('QUERY_ID', $request->rowid)->get();
    $users_lead_notesby_sales = DB::table('lead_notesby_sales')->where('QUERY_ID', $request->rowid)->get();
    $users_lead_lead_notes = DB::table('lead_notes')->where('QUERY_ID', $request->rowid)->get();

    $users_lead_lead_chat_histroy = DB::table('lead_chat_histroy')->where('QUERY_ID', $request->rowid)->get();




    $created_on = date('j M Y h:i A', strtotime($users->created_at));

    if (count($users_lead_lead_chat_histroy) > 0) {

      $HTML_HIST = '   <table class="table table-sm m-table m-table--head-bg-primary">
        <thead class="thead-inverse">
          <tr>

            <th>User</th>
            <th>Stage</th>
            <th>Message</th>
            <th>Created</th>
          </tr>
        </thead>
        <tbody>';


      foreach ($users_lead_lead_chat_histroy as $key => $Leadrow) {
        $assign_by = AyraHelp::getUser($Leadrow->user_id)->name;

        $users_lead_move_created_on = date('j M Y h:iA', strtotime($Leadrow->created_at));
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];

        $HTML_HIST .= '<tr>

          <td>' . $assign_by . '</td>
          <td></td>
          <td>' . $Leadrow->msg . '</td>
          <td>' . $users_lead_move_created_on . '</td>
        </tr>';
      }


      $HTML_HIST .= '<tr>

        <td>Auto</td>
        <td>Fresh Lead</td>
        <td>5</td>
        <td>' . $created_on . '</td>
      </tr>';
      //remarks

      if (isset($users->remarks)) {
        $HTML_HIST .= '<tr>

        <td>' . $updated_by . '</td>
        <td>Remaks</td>
        <td>' . $users->remarks . '</td>
        <td></td>
      </tr>';
      }


      //remarks


      $HTML_HIST .= '</tbody> </table>';
    } else {
      $HTML_HIST = '   <table class="table table-sm m-table m-table--head-bg-primary">
                  <thead class="thead-inverse">
                    <tr>

                      <th>User</th>
                      <th>Stage</th>
                      <th>Message</th>
                      <th>Created</th>
                    </tr>
                  </thead>
                  <tbody>';

      $HTML_HIST .= '<tr>

                      <td>Auto</td>
                      <td>Fresh Lead</td>
                      <td></td>
                      <td>' . $created_on . '</td>
                    </tr>';
      //remarks

      if (isset($users->remarks)) {
        $HTML_HIST .= '<tr>

                      <td>' . $updated_by . '</td>
                      <td>Remaks</td>
                      <td>' . $users->remarks . '</td>
                      <td></td>
                    </tr>';
      }


      //remarks

      if ($users_lead_assign != null) {
        $assign_by = AyraHelp::getUser($users_lead_assign->assign_by)->name;
        $assign_user_id = AyraHelp::getUser($users_lead_assign->assign_user_id)->name;
        $users_lead_assign_created_on = date('j M Y h:iA', strtotime($users_lead_assign->created_at));

        $HTML_HIST .= '<tr>

                      <td>' . $assign_user_id . '</td>
                      <td>Assigned</td>
                      <td>' . $users_lead_assign->msg . '</td>
                      <td>' . $users_lead_assign_created_on . '</td>
                    </tr>';
      }

      if (count($users_lead_moves) > 0) {

        foreach ($users_lead_moves as $key => $Leadrow) {
          $assign_by = AyraHelp::getUser($Leadrow->assign_by)->name;
          $assign_to = AyraHelp::getUser($Leadrow->assign_to)->name;
          $users_lead_move_created_on = date('j M Y h:iA', strtotime($Leadrow->created_at));
          $user = auth()->user();
          $userRoles = $user->getRoleNames();
          $user_role = $userRoles[0];
          if ($user_role == 'Admin') {
            $mgdata = $Leadrow->msg . "(" . $Leadrow->assign_remarks . ")";
          } else {
            $mgdata = $Leadrow->msg;
          }

          $HTML_HIST .= '<tr>

                        <td>' . $assign_to . '</td>
                        <td>' . optional($Leadrow)->stage_name . '</td>
                        <td>' . $mgdata . '</td>
                        <td>' . $users_lead_move_created_on . '</td>
                      </tr>';
        }
      }

      if (count($users_lead_notesby_sales) > 0) {

        foreach ($users_lead_notesby_sales as $key => $Leadrow) {
          $assign_by = AyraHelp::getUser($Leadrow->added_by)->name;

          $users_lead_move_created_on = date('j M Y h:iA', strtotime($Leadrow->created_at));
          $user = auth()->user();
          $userRoles = $user->getRoleNames();
          $user_role = $userRoles[0];


          $HTML_HIST .= '<tr>

                        <td>' . $assign_by . '</td>
                        <td></td>
                        <td>' . $Leadrow->message . '</td>
                        <td>' . $users_lead_move_created_on . '</td>
                      </tr>';
        }
      }

      if (count($users_lead_lead_notes) > 0) {

        foreach ($users_lead_lead_notes as $key => $Leadrow) {
          $assign_by = AyraHelp::getUser($Leadrow->created_by)->name;

          $users_lead_move_created_on = date('j M Y h:iA', strtotime($Leadrow->created_at));
          $user = auth()->user();
          $userRoles = $user->getRoleNames();
          $user_role = $userRoles[0];

          $HTML_HIST .= '<tr>

                        <td>' . $assign_by . '</td>
                        <td></td>
                        <td>' . $Leadrow->msg . '</td>
                        <td>' . $users_lead_move_created_on . '</td>
                      </tr>';
        }
      }






      $HTML_HIST .= '</tbody> </table>';
    }



    $HTML = '<!--begin::Section-->
      <div class="m-section">
        <div class="m-section__content">
          <table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
            <thead>
              <tr >
                <th colspan="3">Leads Full Information</th>
              </tr>
            </thead>
            <tbody>';
    $i = 0;



    // $HTML .='
    // <tr>
    //   <th scope="row">1</th>
    //   <td>Assign Message</td>
    //   <td>'.optional($users_lead_assign)->msg.'</td>

    // </tr>';

    // $HTML .='
    // <tr>

    //   <td colspan="3">'.$MyTable.'</td>

    // </tr>';



    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>QUERY_ID</td>
                <td>' . $users->QUERY_ID . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>SENDER NAME</td>
                <td>' . $users->SENDERNAME . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>SENDERE MAIL</td>
                <td>' . $users->SENDEREMAIL . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>SUBJECT</td>
                <td>' . $users->SUBJECT . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>DATE TIME</td>
                <td>' . $users->DATE_TIME_RE . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>COMPANY NAME</td>
                <td>' . $users->GLUSR_USR_COMPANYNAME . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>MOBILE No.</td>
                <td>' . $users->MOB . '</td>

              </tr>';



    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>COUNTRY FLAG

                </td>
                <td><img src="' . $users->COUNTRY_FLAG . '"></td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>MESSAGE

                </td>
                <td>' . $users->ENQ_MESSAGE . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>ADDRESS

                </td>
                <td>' . $users->ENQ_ADDRESS . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>CITY

                </td>
                <td>' . $users->ENQ_CITY . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>STATE

                </td>
                <td>' . $users->ENQ_STATE . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>STATE

                </td>
                <td>' . $users->ENQ_STATE . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>PRODUCT NAME

                </td>
                <td>' . $users->PRODUCT_NAME . '</td>

              </tr>';


    // $HTML .='
    // <tr>
    //   <th scope="row">1</th>
    //   <td> 	COUNTRY ISO

    //   </td>
    //   <td>'.$users->COUNTRY_ISO.'</td>

    // </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>EMAIL ALT

                </td>
                <td>' . $users->EMAIL_ALT . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>MOBILE  ALT

                </td>
                <td>' . $users->MOBILE_ALT . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>PHONE

                </td>
                <td>' . $users->PHONE . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>PHONE ALT

                </td>
                <td>' . $users->PHONE_ALT . '</td>

              </tr>';


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>MEMBER SINCE

                </td>
                <td>' . $users->IM_MEMBER_SINCE . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>Created At

                </td>
                <td>' . $created_on . '</td>

              </tr>';

    $lsource = "";

    $LS = $users->data_source;
    if ($LS == 'INDMART-9999955922@API_1' || $LS == 'INDMART-9999955922') {
      $lsource = 'IM1';
    }
    if ($LS == 'INDMART-8929503295@API_2' || $LS == 'INDMART-8929503295') {
      $lsource = 'IM2';
    }


    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>Lead Souce

                </td>
                <td>' . $lsource . '</td>

              </tr>';

    $HTML .= '
              <tr>
                <th scope="row">1</th>
                <td>Remarks

                </td>
                <td><strong style="color:#16426B">' . $users->remarks . '</strong></td>

              </tr>';









    $HTML .= '
            </tbody>
          </table>
        </div>
      </div>

      <!--end::Section-->';


    $resp = array(
      'HTML_LEAD' => $HTML,
      'HTML_ASSIGN_HISTORY' => $HTML_HIST,

    );
    return response()->json($resp);
  }

  public function getLeadManagerReport()
  {
    // $users = User::all();
    $theme = Theme::uses('corex')->layout('layout');
    $users = User::orderby('id', 'desc')->with('roles')->get();

    $data = ['users' => $users];
    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $user_role = $userRoles[0];
    if ($user_role == 'Admin' ||  $user_role == 'SalesHead') {

      return $theme->scope('lead.leadManagerReport', $data)->render();
    } else {

      abort('401');
    }


    //return view('users.index')->with('users', $users);
  }

  public function getINDMartDataNEW()
  {
    $theme = Theme::uses('corex')->layout('layout');
    $data = DB::table('indmt_data')->paginate(10);


    return $theme->scope('users.ind_mart_dataTEST', compact('data'))->render();
  }
  function fetch_data(Request $request)
  {

    if ($request->ajax()) {
      $data = DB::table('indmt_data')->paginate(10);
      return view('pagination_data', compact('data'))->render();
    }
  }

  public function getINDMartData()
  {
    // $users = User::all();
    $theme = Theme::uses('corex')->layout('layout');
    $users = User::orderby('id', 'desc')->with('roles')->get();

    $data = ['users' => $users];
    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $user_role = $userRoles[0];
    if ($user_role == 'Admin' || Auth::user()->id == 77  || Auth::user()->id == 3  || Auth::user()->id == 40  || Auth::user()->id == 4||  $user_role == 'SalesHead') {
      return $theme->scope('users.ind_mart_data', $data)->render();
    } else {
      if (Auth::user()->id == 134 || Auth::user()->id == 135 || Auth::user()->id == 136) {
        return $theme->scope('users.ind_mart_data_LMLayout', $data)->render();
      } else {
        abort('401');
      }
    }


    //return view('users.index')->with('users', $users);
  }



  public function getLeadsAcceessListOwn()
  {
    // $users = User::all();
    $theme = Theme::uses('corex')->layout('layout');
    $users = User::orderby('id', 'desc')->with('roles')->get();

    $data = ['users' => $users];
    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $user_role = $userRoles[0];

    return $theme->scope('users.salesOwnLead', $data)->render();



    //return view('users.index')->with('users', $users);
  }


  public function getLeadsAcceessList()
  {
    // $users = User::all();
    $theme = Theme::uses('corex')->layout('layout');
    $users = User::orderby('id', 'desc')->with('roles')->get();

    $data = ['users' => $users];
    $user = auth()->user();
    $userRoles = $user->getRoleNames();
    $user_role = $userRoles[0];
    if ($user_role == 'Admin' || $user_role == 'SalesUser' ||$user_role == 'SalesHead'  || Auth::user()->id == 102) {

      if ($user_role == "Admin") {
        return $theme->scope('users.ind_mart_data', $data)->render();
      } else {
        return $theme->scope('users.ind_mart_dataSales', $data)->render();
      }
    } else {
      abort('401');
    }


    //return view('users.index')->with('users', $users);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $roles = Role::get();
    return view('users.create', ['roles' => $roles]);
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
      'name' => 'required|max:120',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6|confirmed'
    ]);

    $user = User::create($request->only('email', 'name', 'password'));

    $roles = $request['roles'];

    if (isset($roles)) {

      foreach ($roles as $role) {
        $role_r = Role::where('id', '=', $role)->firstOrFail();
        $user->assignRole($role_r);
      }
    }

    return redirect()->route('users.index')
      ->with(
        'flash_message',
        'User successfully added.'
      );
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    return redirect('users');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $theme = Theme::uses('corex')->layout('layout');
    $user = User::findOrFail($id);
    $roles = Role::get();
    $data = ['user' => $user, 'roles' => $roles];
    return $theme->scope('users.user_edit', $data)->render();
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
    $user = User::findOrFail($id);
    $this->validate($request, [
      'name' => 'required|max:120',
      'email' => 'required|email|unique:users,email,' . $id,
      'password' => 'required|min:6|confirmed'
    ]);

    $input = $request->only(['name', 'email', 'password']);
    $roles = $request['roles'];
    $user->fill($input)->save();

    if (isset($roles)) {
      $user->roles()->sync($roles);
    } else {
      $user->roles()->detach();
    }
    return redirect()->route('users.index')
      ->with(
        'flash_message',
        'User successfully edited.'
      );
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('users.index')
      ->with(
        'flash_message',
        'User successfully deleted.'
      );
  }
}
