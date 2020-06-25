<!-- BEGIN: Left Aside -->
<button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn"><i class="la la-close"></i></button>
<div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
   <!-- BEGIN: Aside Menu -->
   <div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark " m-menu-vertical="1" m-menu-scrollable="1" m-menu-dropdown-timeout="500" style="position: relative;">

      <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
         <li class="m-menu__item  m-menu__item--active" aria-haspopup="true"><a href="#" class="m-menu__link "><i class="m-menu__link-icon flaticon-user"></i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span class="m-menu__link-text">{{Auth::user()->name}}</span>
                     <span class="m-menu__link-badge"></span> </span></span></a>
         </li>

         <li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true" m-menu-submenu-toggle="hover">
      <a href="javascript:;" class="m-menu__link m-menu__toggle"><i class="m-menu__link-icon flaticon-interface-7"></i><span class="m-menu__link-text">
      Clients  </span><i class="m-menu__ver-arrow la la-angle-right"></i></a>
      <div class="m-menu__submenu ">
         <span class="m-menu__arrow"></span>
         <ul class="m-menu__subnav">

               <li class="m-menu__item " aria-haspopup="true"><a href="{{ route('client.index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Client List</span></a>
               </li>
               <li class="m-menu__item " aria-haspopup="true"><a href="{{ route('client.notes')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Client Notes</span></a>
               </li>
               </ul>
      </div>
     </li>
     <li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true" m-menu-submenu-toggle="hover">
  <a href="javascript:;" class="m-menu__link m-menu__toggle"><i class="m-menu__link-icon flaticon-interface-7"></i><span class="m-menu__link-text">
  Samples  </span><i class="m-menu__ver-arrow la la-angle-right"></i></a>
  <div class="m-menu__submenu ">
     <span class="m-menu__arrow"></span>
     <ul class="m-menu__subnav">

           <li class="m-menu__item " aria-haspopup="true"><a href="{{ route('sample.index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Samples</span></a>
           </li>
           </ul>
  </div>
 </li>



         <?php
     $route_name=AyraHelp::getRouteName();
     $user = auth()->user();
      $userRoles = $user->getRoleNames();
      $user_role = $userRoles[0];


    if($user_role=='SalesUser' || $user_role=='SalesHead' || $user_role=='Staff'){


      if (Auth::user()->hasPermissionTo('LeadManagementSalesDashboard')) {
       $data_arr_data_data = DB::table('indmt_data')
       ->join('lead_assign', 'indmt_data.QUERY_ID', '=', 'lead_assign.QUERY_ID')
       ->where('lead_assign.assign_user_id', '=', Auth::user()->id)
       //->where('indmt_data.lead_status', '=', 0)
       ->whereDate('lead_assign.created_at',date('Y-m-d'))
       ->orderBy('lead_assign.created_at','desc')
       ->select('indmt_data.*', 'lead_assign.assign_by', 'lead_assign.msg')
       ->get();

         ?>
          <li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true" m-menu-submenu-toggle="hover">
       <a href="javascript:;" class="m-menu__link m-menu__toggle"><i class="m-menu__link-icon flaticon-interface-7"></i><span class="m-menu__link-text">
       Leads  <span class="m-badge m-badge--warning" title="Assign Lead Count Today">{{count($data_arr_data_data)}}</span> </span><i class="m-menu__ver-arrow la la-angle-right"></i></a>
       <div class="m-menu__submenu ">
          <span class="m-menu__arrow"></span>
          <ul class="m-menu__subnav">
             <?php
             if (Auth::user()->hasPermissionTo('LeadManagementSalesDashboard_Access')) {
                ?>
                <li class="m-menu__item " aria-haspopup="true"><a href="{{ route('getLeadsAcceessList')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Leads</span></a>
                </li>
                <li class="m-menu__item " aria-haspopup="true"><a href="{{ route('getQutatationList')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Quotation List</span></a>
                </li>



                <?php
             }
             ?>





          </ul>
       </div>
    </li>




         <?php

      }

    }
    ?>





         <?php
         if (Auth::user()->hasPermissionTo('LeadManagement')) {
            if (Auth::user()->id == 1 || Auth::user()->id == 77 || Auth::user()->id == 3 || Auth::user()->id == 90 || Auth::user()->id == 129 || Auth::user()->id == 134 || Auth::user()->id == 40  || Auth::user()->id == 4|| Auth::user()->id == 135 || Auth::user()->id == 136 ) {
         ?>
               <li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true" m-menu-submenu-toggle="hover">
                  <a href="javascript:;" class="m-menu__link m-menu__toggle"><i class="m-menu__link-icon flaticon-interface-7"></i><span class="m-menu__link-text">
                        Lead Management</span><i class="m-menu__ver-arrow la la-angle-right"></i></a>
                  <div class="m-menu__submenu ">
                     <span class="m-menu__arrow"></span>
                     <ul class="m-menu__subnav">
                        <?php
                        if (Auth::user()->hasPermissionTo('LeadManagement_FreshLead')) {
                        ?>
                           <li class="m-menu__item " aria-haspopup="true">
                              <a href="{{ route('getINDMartData')}}" class="m-menu__link ">
                                 <i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
                                 <span class="m-menu__link-text">Leads</span>
                              </a>
                           </li>


                           <li class="m-menu__item " aria-haspopup="true">
                              <a href="{{ route('getLeadManagerReport')}}" class="m-menu__link ">
                                 <i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
                                 <span class="m-menu__link-text">Lead Manager Report</span>
                              </a>
                           </li>
                           <li class="m-menu__item " aria-haspopup="true">
                              <a href="{{ route('getLeadReports')}}" class="m-menu__link ">
                                 <i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
                                 <span class="m-menu__link-text">Leads Reports</span>
                              </a>
                           </li>
                           <li class="m-menu__item " aria-haspopup="true">
                              <a href="{{ route('getLeadReports_Dist')}}" class="m-menu__link ">
                                 <i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
                                 <span class="m-menu__link-text">Leads Distribution </span>
                              </a>
                           </li>
                           <li class="m-menu__item " aria-haspopup="true">
                              <a href="{{ route('getLeadStagesGrapgh')}}" class="m-menu__link ">
                                 <i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
                                 <span class="m-menu__link-text">Leads Stages Grapgh </span>
                              </a>
                           </li>


                        <?php
                        }

                        ?>





                     </ul>
                  </div>
               </li>



         <?php

            }
         }
         ?>



         </li>


         <!-- ajcode for new menu -->
         <li class="m-menu__item  m-menu__item" aria-haspopup="true"><a href="{{route('loginActivity')}}" class="m-menu__link "><i class="m-menu__link-icon flaticon-plus"></i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> Login Activity
                     <span class="m-menu__link-badge"></span> </span></span></a>
         </li>


      </ul>
   </div>
   <!-- END: Aside Menu -->
</div>
<!-- END: Left Aside -->
<div class="m-grid__item m-grid__item--fluid m-wrapper">
