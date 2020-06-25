<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;
use DB;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      //$sessID= Session::get('variable_Name');
      if (Auth::user()) {
           $sessID = Auth::user()->user_session_id;

        $affected = DB::table('login_activity')
                ->where('session_id', $sessID)
                ->update(['logout_start' => date('Y-m-d H:i:s')]);
      }


      // $sessID = $request->session()->get('variable_Name');

        // if(Auth::user()->is_deleted==1){
        //     abort('401');
        // }
        // if (!Auth::user()->hasPermissionTo('userAccessRight')) {
        //              abort('401');
        // }
        //13.234.240.39


        $user = User::all()->count();
        // if (!($user == 1)) {
        //     if (!Auth::user()->hasPermissionTo('Administer roles & permissions')) {
        //         abort('401');
        //     }
        //  }
        /*
        |--------------------------------------------------------------------------
        | View-Client-List :View list of all client
        |--------------------------------------------------------------------------
        */


         if ($request->is('client')) {
            if (!Auth::user()->hasPermissionTo('view-client-list')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('employee')) {
            if (!Auth::user()->hasPermissionTo('view-employee')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Add-Client-Data :Add New Client
        |--------------------------------------------------------------------------
        */
        if ($request->is('client/create')) {
            if (!Auth::user()->hasPermissionTo('add-clients')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Client View Single :Client View Single
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | Edit-Client-Data :Edit-Client-Data
        |--------------------------------------------------------------------------
        */
        if ($request->is('client/*/edit')) {
            if (!Auth::user()->hasPermissionTo('edit-client')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('client/*')) {
            if (!Auth::user()->hasPermissionTo('Client-View-Single')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Edit-Client-Data :Edit-Client-Data
        |--------------------------------------------------------------------------
        */


        if ($request->is('sample/add/*')) {

            if (!Auth::user()->hasPermissionTo('add-samples')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('sample/*/edit')) {

            if (!Auth::user()->hasPermissionTo('edit-sample')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('sample')) {

            if (!Auth::user()->hasPermissionTo('view-sample')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('sample/create')) {

            if (!Auth::user()->hasPermissionTo('add-samples')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('orders/create')) {

            if (!Auth::user()->hasPermissionTo('add-orders')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('sample.print.all')) {

            if (!Auth::user()->hasPermissionTo('print-sample-all')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('saveSampleCourier')) {

            if (!Auth::user()->hasPermissionTo('add-sample-tracking-data')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('saveSampleCourier')) {

            if (!Auth::user()->hasPermissionTo('print-own-sample')) {
                abort('401');
            } else {
                return $next($request);
            }
        }


        if ($request->is('orders')) {

            if (!Auth::user()->hasPermissionTo('view-orders-list')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('orders/create')) {

            if (!Auth::user()->hasPermissionTo('add-orders')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('client.notes')) {

            if (!Auth::user()->hasPermissionTo('view-notes-list')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('add.notes')) {

            if (!Auth::user()->hasPermissionTo('add-client-notes')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('softdeleteClient')) {

            if (!Auth::user()->hasPermissionTo('soft-delete-client')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('client/*/edit')) {

            if (!Auth::user()->hasPermissionTo('edit-client')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('stocks')) {

            if (!Auth::user()->hasPermissionTo('view-stock-list')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('stocks-entry')) {

            if (!Auth::user()->hasPermissionTo('add-stock-entry')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('rawclientdata')) {

            if (!Auth::user()->hasPermissionTo('view-raw-client-data')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
        if ($request->is('purchase-request-list')) {

            if (!Auth::user()->hasPermissionTo('purchase-orders')) {
                abort('401');
            } else {
                return $next($request);
            }
        }










        return $next($request);
    }
}
