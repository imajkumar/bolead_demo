<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\OTP;
use App\Helpers\AyraHelp;
use Carbon\Carbon;
use Session;
use \Cache;
use Illuminate\Http\Request;
use DB;
class LogoutMiddleware {
    /**
     * Instance of Session Store
     * @var session
     */
    protected $session;
    /**
     * Time for user to remain active, set to 900secs( 15minutes )
     * @var timeout
     */
    protected $timeout = 900;
    public function __construct(Store $session){
        $this->session        = $session;
        $this->redirectUrl    = 'auth/login';
        $this->sessionLabel   = 'warning';
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

      




        $OTPEnableCheck=AyraHelp::OTPEnableStatus();

        if($OTPEnableCheck){
            //if OTP is enable
            $whitelistData = array(
                '127.0.0.1',
                '::1'
            );
            if(!in_array($_SERVER['REMOTE_ADDR'], $whitelistData)){
                // $user_ip = trim($_SERVER['REMOTE_ADDR']);
                // $access_url = "http://api.ipstack.com/";
                // $iptkey=AyraHelp::getIPKEY();
                // $access_key = "?access_key=".$iptkey;
                // $ip_data = json_decode(file_get_contents($access_url . $user_ip . $access_key), true);
                //  $userip=$ip_data['ip'];
                 $ip_val = Cache::get('IP_Val');
                 $userip=$ip_val;

            }else{
                $userip=trim($_SERVER['REMOTE_ADDR']);
            }
            $otp_arr_data=OTP::where('user_ip',$userip)->where('ip_verify',1)->whereDate('expiry', Carbon::today())->first();
            if($otp_arr_data==null){
                Auth::logout();

            }

        }
        else{
            //if OTP is not eable

        }



        return $next($request);
    }






}
