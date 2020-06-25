<!DOCTYPE html>
<html lang="en">

    <head>
        <?php 
        $user = auth()->user();
        $userRoles = $user->getRoleNames();
        $user_role = $userRoles[0];
        ?>
        {!! meta_init() !!}
        <meta name="keywords" content="@get('keywords')">
        <meta name="description" content="@get('description')">
        <meta name="author" content="@get('author')">
        <meta name="BASE_URL" content="{{ url('/') }}" />
        <meta name="UUID" content="{{Auth::user()->id}}" />
        <meta name="BASE_URL" content="{{ url('/') }}" />        
        <meta name="UNIB" content="{{ $user_role }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@get('title')</title>       
        <link href="{{ asset('local/public/themes/corex/assets/vendors/base/vendors.bundle.css') }} " rel="stylesheet" type="text/css" />      
        <link href="{{ asset('local/public/themes/corex/assets/demo/default/base/style.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('local/public/themes/corex/assets/vendors/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('local/public/themes/corex/assets/vendors/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="{{ asset('local/public/img/logo/favicon.ico') }}" />      
        <link rel="stylesheet" href="{{ asset('local/public/themes/corex/assets/owl/owl.carousel.min.css')}}">
        <link rel="stylesheet" href="{{ asset('local/public/themes/corex/assets/owl/owl.theme.default.min.css')}}">


    </head>
    <body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
      <!-- begin:: Page -->
  		<div class="m-grid m-grid--hor m-grid--root m-page">
        @partial('header')
        @partial('leftsideEMP')
        @content()
        @partial('footer')
        @partial('quicknav')
        <!--begin::Global Theme Bundle -->
		<script src="{{ asset('local/public/themes/corex/assets/vendors/base/vendors.bundle.js') }} " type="text/javascript"></script>
		<script src="{{ asset('local/public/themes/corex/assets/demo/default/base/scripts.bundle.js') }} " type="text/javascript"></script>

		<!--end::Global Theme Bundle -->

		<!--begin::Page Vendors -->
		<script src="{{ asset('local/public/themes/corex/assets/vendors/custom/fullcalendar/fullcalendar.bundle.js') }}" type="text/javascript"></script>

		<!--end::Page Vendors -->

		<!--begin::Page Scripts -->
		
		<script src="{{ asset('local/public/themes/corex/assets/app/js/datalist.js') }} " type="text/javascript"></script>

    <script src="{{ asset('local/public/themes/corex/assets/js/ajax_client_list_.js') }}" type="text/javascript"></script>
    <script src="{{ asset('local/public/themes/corex/assets/js/ajax_sample_list_.js') }}" type="text/javascript"></script>
    <script src="{{ asset('local/public/themes/corex/assets/js/ajax_orders_list_.js') }}" type="text/javascript"></script>
    <script src="{{ asset('local/public/themes/corex/assets/js/stock.js') }}" type="text/javascript"></script>
    <script src="{{ asset('local/public/themes/corex/assets/js/purchase.js') }}" type="text/javascript"></script>
    <script src="{{ asset('local/public/themes/corex/assets/js/vendors.js') }}" type="text/javascript"></script>
    <script src = "{{ asset('local/public/themes/corex/assets/charts_loader.js') }}"></script>
    <script src = "https://www.gstatic.com/charts/loader.js"></script>

    <script type = "text/javascript">
              google.charts.load('current', {packages: ['corechart']});     
    </script>
    <script src="{{ asset('local/public/themes/corex/assets/js/form_validation.js') }}" type="text/javascript"></script>
    <script src="{{ asset('local/public/themes/corex/assets/js/ayra.js') }}" type="text/javascript"></script>
    <script src="{{ asset('local/public/themes/corex/assets/app/js/dashboard.js') }} " type="text/javascript"></script>
    <script type="text/javascript">
      BASE_URL=$('meta[name="BASE_URL"]').attr('content');
      UID=$('meta[name="UUID"]').attr('content');
      _TOKEN=$('meta[name="csrf-token"]').attr('content');
      _UNIB_RIGHT=$('meta[name="UNIB"]').attr('content');     
      
    </script>
        <script type="text/javascript">
       
          function chkInternetStatus() {
              if(navigator.onLine) {
                  //alert("Hurray! You're online!!!");
              } else {
                  alert("Oops! You're offline. Please check your network connection...");
              }
          }

         
          setInterval(function(){
           
            if(UID==84 || UID==27){
                chkInternetStatus();
            }

          }, 5000);

          </script>


         
        



    </body>

</html>
