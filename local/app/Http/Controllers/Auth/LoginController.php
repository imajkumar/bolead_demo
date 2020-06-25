<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\OTP;
use App\Helpers\AyraHelp;
use AWS;
use Carbon\Carbon;
use \Cache;
use DB;
use Session;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/getIndData';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function LoginOTPVerify(Request $request){
        $otp_arr = OTP::where('otp_token', $request->otp_token)->where('otp', $request->otp)->first();


        if($otp_arr==null){
            $data=array(
                'login_token'=>'',
                'status'=>0
            );
        }else{
            OTP::where('otp_token', $request->otp_token)
          ->where('otp', $request->otp)
          ->update(['ip_verify' => 1]);
            Auth::loginUsingId($otp_arr->user_id, true);
            $data=array(
                'login_token'=>'',
                'status'=>1
            );
        }

        return response()->json($data);




    }
    public function validateNetwork(){
        if( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || strtoupper($_SERVER['REMOTE_ADDR']) == 'LOCALHOST'){
            return true;
        }

        $config_network = Cache::get('config_network');
        if( !is_array($config_network) ){
            $config_network = [];
        }

        foreach( $config_network as $key => $network){
            if( strtotime($network['EXPIRY']) <= strtotime('now')){
                unset($config_network[$key]);
            }
        }

        Cache::forget('config_network');
        Cache::forever('config_network', $config_network);

        $user_ip = trim($_SERVER['REMOTE_ADDR']);
        $access_url = "http://api.ipstack.com/";
        // $access_key = "?access_key=cf96ebd2eacbc68c6f43a91475b80c0c";
        $access_key = "?access_key=ef7ff697fcf23bceca167c80b762a0be"; //bointldev@gmail.com | Ajay#9711


        $ip_data = json_decode(file_get_contents($access_url . $user_ip . $access_key), true);

        $distance = 1000;


        foreach( $config_network as $key => $network){
            if( $network['VERIFY'] == 0){
                continue;
            }
            $network_data = json_decode(file_get_contents($access_url . $network['IP'] . $access_key), true);
            if( $network_data ){
                $lat1 = $network_data['latitude'];
                $lon1 = $network_data['longitude'];

                $lat2 = $ip_data['latitude'];
                $lon2 = $ip_data['longitude'];

                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $dist = $dist * 60 * 1.1515 * 1.609344;

                if( $dist < $distance){
                    $distance = $dist;
                }
            }
        }

        if( $distance > 1){
            throw new Exception("User-000");
        }

        return true;
    }

    public function customLogin(Request $request){

        $OTPEnableCheck=AyraHelp::OTPEnableStatus();

        if($OTPEnableCheck){
            //if OTP is enable
            $whitelistData = array(
                '127.0.0.1',
                '::1'
            );
            if(!in_array($_SERVER['REMOTE_ADDR'], $whitelistData)){
            $user_ip = trim($_SERVER['REMOTE_ADDR']);
            $userip = trim($_SERVER['REMOTE_ADDR']);

            $access_url = "http://api.ipstack.com/";
            $iptkey=AyraHelp::getIPKEY();
            $iptkey="9c61078fbc109f08a8804bb2d571d718";

            $access_key = "?access_key=".$iptkey;
            $ip_data = json_decode(file_get_contents($access_url . $user_ip . $access_key), true);
          //   print_r($ip_data);
          // //  echo $userip=$ip_data['ip'];
          //   die;
            Cache::add('IP_Val',$userip, now()->addMinutes(45));

            }else{
                $userip = trim($_SERVER['REMOTE_ADDR']);
                $ip_data['ip']='127.0.0.1';
                $ip_data['region_name']='local';
                $ip_data['city']='local';
             }
            $email=$request->email;
            $password=$request->password;
            $otp_arr_data=OTP::where('user_ip',$userip)->where('ip_verify',1)->whereDate('expiry', Carbon::today())->first();

            if($otp_arr_data==null){
                // not validate then ask otp and validate
                $model = User::where('email', $request->email)->first();
                if (Hash::check($request->password, $model->password, [])) {
                    $otp=AyraHelp::getOTP();
                    $otp_token=uniqid(base64_encode(str_random(60)));
                    $otpObj=new OTP;
                    $otpObj->user_id=$model->id;
                    $otpObj->otp=$otp;
                    $otpObj->otp_type=2;
                    $otpObj->user_ip=$userip;
                    $otpObj->location_details =json_encode($ip_data);
                    $otpObj->otp_token=$otp_token;
                    $otpObj->expiry=date('Y-m-d');
                    $otpObj->save();


                        $otp_msg=$ip_data['region_name'].' , '.$ip_data['city'];
                        //$otp_msg='OK';

                        $user_arr=AyraHelp::getUser($model->id);
                        $user_data=$user_arr->name;
                        $whitelist = array(
                            '127.0.0.1',
                            '::1'
                        );

                        if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){

                            $phone = $model->phone;
                            $msg=$otp.' OTP for BO LEAD  :Name'.$user_data.' Location '.$otp_msg;

                            $this->msg91SendSMS($phone,$msg);


                        // $sms = AWS::createClient('sns');

                        //  $data_sms= $sms->publish([
                        //         'Message' => $otp.' OTP for Bo  Access Requested from  :'.$user_data.' Location'.$otp_msg,
                        //         'PhoneNumber' => '919999955922',
                        //         'MessageAttributes' => [
                        //             'AWS.SNS.SMS.SMSType'  => [
                        //                 'DataType'    => 'String',
                        //                 'StringValue' => 'Transactional',
                        //             ]
                        //         ],
                        // ]);

                        // $data_sms= $sms->publish([
                        //     'Message' => $otp.' OTP for Bo  Access Requested from  :'.$user_data.' Location'.$otp_msg,
                        //     'PhoneNumber' => '919811098426',
                        //     'MessageAttributes' => [
                        //         'AWS.SNS.SMS.SMSType'  => [
                        //             'DataType'    => 'String',
                        //             'StringValue' => 'Transactional',
                        //         ]
                        //     ],
                        // ]);

                        // $data_sms= $sms->publish([
                        //     'Message' => $otp.' OTP for Bo  Access Requested from  :'.$user_data.' Location'.$otp_msg,
                        //     'PhoneNumber' => '917703886088',
                        //     'MessageAttributes' => [
                        //         'AWS.SNS.SMS.SMSType'  => [
                        //             'DataType'    => 'String',
                        //             'StringValue' => 'Transactional',
                        //         ]
                        //     ],
                        // ]);



                        }









                    $data=array(
                    'login_token'=>$otp_token,
                    'status'=>1
                    );

                }else{
                    $data=array(
                    'login_token'=>'',
                    'status'=>0
                    );
                }

            }else{

                //yes validate then make login
                $model = User::where('email', $request->email)->first();
                if (Hash::check($request->password, $model->password, [])) {

                    Auth::loginUsingId($model->id, true);
                    $data=array(
                        'login_token'=>'',
                        'status'=>2
                    );
                }else{
                    $data=array(
                        'login_token'=>'',
                        'status'=>0
                    );
                }

            }


        }else{


           //if OTP is desable
            //yes validate then make login
            $model = User::where('email', $request->email)->first();
            if (Hash::check($request->password, $model->password, [])) {

                Auth::loginUsingId($model->id, true);
                  //save login session start
                $sessID=$model->id."ID-".date('Ymdhis').uniqid();
               $affected = DB::table('users')
              ->where('id', $model->id)
              ->update(['user_session_id' => $sessID]);

                DB::table('login_activity')->insert(
                  [
                    'user_id' =>$model->id,
                    'user_name' =>$model->name,
                    'login_start' => date('Y-m-d H:i:s'),
                    'login_details' => 'OTP Disable login',
                    'session_id' => $sessID,
                    'created_on' => date('Ymd')
                 ]
                );

                //save login session stop


                $data=array(
                    'login_token'=>'',
                    'status'=>2
                );
            }else{
                $data=array(
                    'login_token'=>'',
                    'status'=>0
                );
            }

           //---------------------


        }




        return response()->json($data);


    }
}
