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
        
<!--begin::Web font -->
		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
		<script>
			WebFont.load({
            google: {"families":["Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
          });
        </script>

		<!--end::Web font -->

    </head>
    <body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
      <!-- begin:: Page -->
  		<div class="m-grid m-grid--hor m-grid--root m-page">
        @partial('header')
        @partial('leftside')        
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
    <!-- <script src = "{{ asset('local/public/themes/corex/assets/charts_loader.js') }}"></script> -->
    <script src = "https://www.gstatic.com/charts/loader.js"></script>

    <script type = "text/javascript">
              google.charts.load('current', {packages: ['corechart']});     
    </script>
    <script src="{{ asset('local/public/themes/corex/assets/js/form_validation.js') }}" type="text/javascript"></script>
    
    <script src="{{ asset('local/public/themes/corex/assets/js/ayra.js') }}" type="text/javascript"></script>
    <script src="{{ asset('local/public/themes/corex/assets/app/js/dashboard.js') }} " type="text/javascript"></script>

    <script src="{{ asset('local/public/themes/corex/assets/demo/default/custom/crud/forms/widgets/summernote.js')}}" type="text/javascript"></script>
    
   
    

    <script type="text/javascript">
      BASE_URL=$('meta[name="BASE_URL"]').attr('content');
      UID=$('meta[name="UUID"]').attr('content');
      _TOKEN=$('meta[name="csrf-token"]').attr('content');
      _UNIB_RIGHT=$('meta[name="UNIB"]').attr('content');     
      
    </script>
     <script src="{{ asset('local/public/themes/corex/assets/demo/default/custom/crud/forms/widgets/typeahead.js')}}" type="text/javascript"></script>

        <script type="text/javascript">
       
          function chkInternetStatus() {
              if(navigator.onLine) {
                  //alert("Hurray! You're online!!!");
              } else {
                  alert("Oops! You're offline. Please check your network connection...");
              }
          }

         
          setInterval(function(){
           
            if(UID==84 || UID==27 || UID==95){
                chkInternetStatus();
            }

          }, 5000);

          </script>
<script src="{{ asset('local/public/themes/corex/assets/owl/dist/owl.carousel.min.js')}}"></script>        
         
         

<script>





function submit6Dispatach(e){
    alert(555);
    $data=$("myFormFinalDispatchV1").serialize();
    console.log($data);
    e.preventDefault();
    return false;


    // $.ajax({
    // url: BASE_URL+'/setSaveProcessAction',
    // type: 'POST',
    // data: formData,
    //     success: function(res) {

    //     }
    // });

}
//btnGenPurchaseOrderDone
function btnGenPurchaseRecivedDone(){
    var BOMIDRV=$('#BOMIDRV').val(); 
    var txtRECQTY=$('#txtRECQTY').val(); 
    var txtGRPONumber=$('#txtGRPONumber').val(); 
    var txtRemarks=$('#txtRemarks_REC').val();
     //ajax 
  var formData = {
    'BOMIDRV':BOMIDRV, 
    'txtRECQTY':txtRECQTY, 
    'txtGRPONumber':txtGRPONumber, 
    'txtRemarks':txtRemarks,
    '_token':$('meta[name="csrf-token"]').attr('content')
  };
  $.ajax({
    url: BASE_URL+'/setSaveVendorOrderRecieved',
    type: 'POST',
    data: formData,
    success: function(res) {

        if(res.status==0){
            toasterOptions();
          toastr.error(res.msg, 'Purcase Stage Process');
          return false; 
        }
        if(res.status==1){
            toasterOptions();
          toastr.success(res.msg, 'Purcase Stage Process');
          //location.reload();
          return false; 
        } 
       

    }
});
  //ajax 



}
function btnGenPurchaseOrderDone(){
  var BOMID=$('#BOMID').val();
  var txtPO_NO=$('#txtPO_NO').val();
  var txtETA=$('#m_datepicker_1ETA').val();
  var txtRemarks=$('#txtRemarks').val();

  var venderID= $( "#venderID option:selected" ).val();
  //ajax 
  var formData = {
    'BOMID':BOMID,    
    'txtPO_NO': $('#txtPO_NO').val(),    
    'venderID': $('#venderID').html(),   
    'txtETA': txtETA,   
    'txtRemarks': txtRemarks,   
    '_token':$('meta[name="csrf-token"]').attr('content')
  };
  $.ajax({
    url: BASE_URL+'/setSaveVendorOrder',
    type: 'POST',
    data: formData,
    success: function(res) {
        if(res.status==0){
            toasterOptions();
          toastr.error(res.msg, 'Purcase Stage Process');
          return false; 
        }
        if(res.status==1){
            toasterOptions();
          toastr.success(res.msg, 'Purcase Stage Process');
          //location.reload();
          return false; 
        }
  

      

    },
    dataType : 'json'
    
});
  //ajax 




  

}
//btnGenPurchaseOrderDone



function btnGenCommentDone(){
    var formData = {
    'txtStage_ID': $('#txtStage_ID').val(),    
    'txtTicketID': $('#txtTicketID').val(),    
    'txtProcessID': $('#txtProcessID').val(),    
    'txtDependentTicketID': $('#txtDependentTicketID').val(),   
    'txtRemarks': $('#message-text').val(),    
    'action_on': 0,    
    '_token':$('meta[name="csrf-token"]').attr('content')
  };
  $.ajax({
    url: BASE_URL+'/setSaveProcessAction',
    type: 'POST',
    data: formData,
    success: function(res) {
        
        if(res.status==0){
          toasterOptions();
          toastr.error(res.msg, 'Stage Process');
          return false; 

        }else{
          toasterOptions();
          toastr.success(res.msg, 'Stage Process');
          //location.reload();
          $('#model_BO_task_12').modal('hide');
          
        }
    },
    dataType : 'json'
  });

  //ajax call
}




function btnGenProcessDone(){
                      //ajax call
                      var txtStage_ID=$('#txtStage_ID').val();
                      var pid=$('#txtProcessID').val();
                      var msg=$('#message-text').val();
                      var tikID=$('#txtTicketID').val();

                      if(pid==4 && txtStage_ID==6 && msg=="" ){
                        toasterOptions();
                        toastr.error('Enter Message for lost', 'Stage Process');
                        return false; 

                      }

            //           if(pid==4 && txtStage_ID==3){
            //     _redirect_sample =BASE_URL+'/add_stage_sample/'+tikID
            //     window.location.assign(_redirect_sample);  

            // }   
            //return false;



       var formData = {
      'txtStage_ID': $('#txtStage_ID').val(),    
      'txtTicketID': $('#txtTicketID').val(),    
      'txtProcessID': $('#txtProcessID').val(),    
      'txtDependentTicketID': $('#txtDependentTicketID').val(),    
      'txtRowCount': $('#txtRowCount').val(),    
      'txtRemarks': $('#message-text').val(),    
      'action_on': 1,    
      '_token':$('meta[name="csrf-token"]').attr('content')
    };
    $.ajax({
      url: BASE_URL+'/setSaveProcessAction',
      type: 'POST',
      data: formData,
      success: function(res) {
          
          if(res.status==0){
            toasterOptions();
            toastr.error(res.msg, 'Stage Process');
            $('#model_BO_task_1').modal('toggle');
            return false; 

          }else{
            toasterOptions();
            toastr.success(res.msg, 'Stage Process');
            //location.reload();
            $('#model_BO_task_1').modal('toggle');
            if(pid==4 && txtStage_ID==3){
                _redirect_sample =BASE_URL+'/add_stage_sample/'+tikID
                window.location.assign(_redirect_sample);  

            }   
            if(pid==5 && txtStage_ID==3){
                _redirect_sample =BASE_URL+'/add-mylead-sample/'+tikID
                window.location.assign(_redirect_sample);  

            }   
            //$('#model_BO_task_1').modal('hide');
            
          }
      },
      dataType : 'json'
    });

    //ajax call

}

</script>


         <!--begin::Modal-->
<div class="modal fade" id="model_BO_task_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Stage Action</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">      
        <input type="hidden" id="txtStage_ID">
        <input type="hidden" id="txtTicketID">
        <input type="hidden" id="txtDependentTicketID">
        <input type="hidden" id="txtProcessID">
        <input type="hidden" id="txtRowCount">
            <form>               
                <div class="form-group">
                    <label for="message-text" class="form-control-label">Remarks:</label>
                    <textarea class="form-control" id="message-text"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">         
            
        <a href="javascript:void(0)" onclick="btnGenCommentDone()" class="btn btn-warning btn-sm m-btn  m-btn m-btn--icon">
        <span>
        <i class="la la-commenting"></i>
            <span>Comment</span>
        </span>
		</a>
        <a href="javascript:void(0)"  onclick="btnGenProcessDone()" class="btn btn-success btn-sm m-btn  m-btn m-btn--icon">
        <span>
            <i class="la la-check"></i>
            <span>Completed</span>
        </span>
		</a>


        </div>
    </div>
</div>
</div>

<!--end::Modal-->





<div class="modal fade" id="model_BO_task_3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Order Vender</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">             
        <input type="hidden" id="BOMID">        
        <form> 
                  
            <div class="form-group">
                    <label for="message-text" class="form-control-label">Select Vender</label>
                    <select name="venderID" id="venderID" class="form-control">
                    <?php 
                    $datas=AyraHelp::getAllVendors();
                    foreach ($datas as $key => $rowData) {
                    ?>
                    <option value="{{$rowData->id}}">{{$rowData->name}}-{{$rowData->vendor_name}} </option>
                    <?php
                    }
                  ?>

                    </select>
                </div>
                <div class="form-group">
                    <label for="message-text" class="form-control-label">PO No.:</label>
                    <input type="text" class="form-control" id="txtPO_NO"></textarea>
                </div>
                <div class="form-group">
                    <label for="message-text" class="form-control-label">ETA(Estimated Time of Arrival):</label>
                    <input type="text" class="form-control" id="m_datepicker_1ETA"></textarea>
                </div>

                <div class="form-group">
                    <label for="message-text" class="form-control-label">Remarks:</label>
                    <textarea class="form-control" id="txtRemarks"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">         
            
        <!-- <a href="javascript:void(0)" onclick="btnGenCommentDone()" class="btn btn-warning btn-sm m-btn  m-btn m-btn--icon">
        <span>
        <i class="la la-commenting"></i>
            <span>Comment</span>
        </span>
		</a> -->
        <a href="javascript:void(0)"  onclick="btnGenPurchaseOrderDone()" class="btn btn-success btn-sm m-btn  m-btn m-btn--icon">
        <span>
            <i class="la la-check"></i>
            <span>Order Now</span>
        </span>
		</a>


        </div>
    </div>
</div>
</div>

<!--end::Modal-->


<div class="modal fade" id="model_BO_task_4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Order Recieved Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">      
        <input type="hidden" id="BOMIDRV">       

            <form>     

                <div class="form-group">
                    <label for="message-text" class="form-control-label">GRPO No.:</label>
                    <input type="text" class="form-control" id="txtGRPONumber"></textarea>
                </div>
          
                <div class="form-group">
                    <label for="message-text" class="form-control-label">Received QTY.:</label>
                    <input type="text" class="form-control" id="txtRECQTY"></textarea>
                </div>

                <div class="form-group">
                    <label for="message-text" class="form-control-label">Remarks:</label>
                    <textarea class="form-control" id="txtRemarks_REC"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">         
            
        <!-- <a href="javascript:void(0)" onclick="btnGenCommentDone()" class="btn btn-warning btn-sm m-btn  m-btn m-btn--icon">
        <span>
        <i class="la la-commenting"></i>
            <span>Comment</span>
        </span>
		</a> -->
        <a href="javascript:void(0)"  onclick="btnGenPurchaseRecivedDone()" class="btn btn-success btn-sm m-btn  m-btn m-btn--icon">
        <span>
            <i class="la la-check"></i>
            <span>Recived Now</span>
        </span>
		</a>


        </div>
    </div>
</div>
</div>

<!--end::Modal-->


<script>



          $(document).ready(function(){
           

            $("#owl-demo").owlCarousel({
 
              navigation : true, // Show next and prev buttons
              slideSpeed : 300,
              paginationSpeed : 400,
              singleItem:true

              // "singleItem:true" is a shortcut for:
              // items : 1, 
              // itemsDesktop : false,
              // itemsDesktopSmall : false,
              // itemsTablet: false,
              // itemsMobile : false

              });



             
//general process
$('#btnGenProcessDone').click(function(){
  
                   //ajax call
    var formData = {
      'txtStage_ID': $('#txtStage_ID').val(),    
      'txtTicketID': $('#txtTicketID').val(),    
      'txtProcessID': $('#txtProcessID').val(),    
      'txtProcessID': $('#txtProcessID').val(),    
      'txtRowCount': $('#txtRowCount').val(),    
      'txtRemarks': $('#message-text').val(),    
      'action_on': 1,    
      '_token':$('meta[name="csrf-token"]').attr('content')
    };
    $.ajax({
      url: BASE_URL+'/setSaveProcessAction',
      type: 'POST',
      data: formData,
      success: function(res) {
          
          if(res.status==0){
            toasterOptions();
            toastr.error(res.msg, 'Stage Process');
            return false; 

          }else{
            toasterOptions();
            toastr.success(res.msg, 'Stage Process');
            //location.reload();
            $('#model_BO_task_1').modal('hide');
            
          }
      },
      dataType : 'json'
    });

    //ajax call
             });

             //general process
             select name, count(name) from contacts group by name;
             //general commnet 
             //btnStageProcessCompletedNow
$('#btnGenCommentDone').click(function(){
 
  //ajax call
  var formData = {
    'txtStage_ID': $('#txtStage_ID').val(),    
    'txtTicketID': $('#txtTicketID').val(),    
    'txtProcessID': $('#txtProcessID').val(),    
    'txtProcessID': $('#txtProcessID').val(),    
    'txtRemarks': $('#message-text').val(),    
    'action_on': 0,    
    '_token':$('meta[name="csrf-token"]').attr('content')
  };
  $.ajax({
    url: BASE_URL+'/setSaveProcessAction',
    type: 'POST',
    data: formData,
    success: function(res) {
        
        if(res.status==0){
          toasterOptions();
          toastr.error(res.msg, 'Stage Process');
          return false; 

        }else{
          toasterOptions();
          toastr.success(res.msg, 'Stage Process');
          //location.reload();
          $('#model_BO_task_12').modal('hide');
          
        }
    },
    dataType : 'json'
  });

  //ajax call
});
             //general commnet 



            
          });
          </script>



<!--begin::Modal-->
<div class="modal fade" id="model_BO_task_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderString"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!--begin::Form-->
            <form id="myFormFinalDispatchV1"  class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed" action="{{ route('UpdateOrderDispatch_v1')}}" method="post">
                                        @csrf
                                            <input type="hidden" id="txtorderStepID_v1" name="txtorderStepID1">
                                            <input type="hidden" id="txtOrderID_FORMI_v1" name="txtOrderID_FORMID1">
                                            <input type="hidden" id="txtProcess_days_v1" name="txtProcess_days1">
                                            <input type="hidden" id="txtProcess_Name_v1" name="txtProcess_Name1"> 
                                            <input type="hidden" id="txtStepCode_v1" name="txtStepCode1"> 
                                            <input type="hidden" id="expectedDate_v1" value="{{date('Y-m-d')}}" name="expectedDate1"> 
                                            <div class="m-portlet__body">
                                                <div class="form-group m-form__group row">
                                                    <div class="col-lg-4">
                                                        <label>Resp .Person:</label>
                                                        <select name="order_crated_by" id="order_crated_by" class="form-control">
                                                                <?php
                                                                $user = auth()->user();
                                                                $userRoles = $user->getRoleNames();
                                                                $user_role = $userRoles[0];
                                                                ?>
                                                                @if ($user_role =="Admin" || $user_role =="Staff")
                                                                @foreach (AyraHelp::getSalesAgentAdmin() as $user)
                                                                <option  value="{{$user->id}}">{{$user->name}}</option>
                                                                @endforeach
                                                                @else
                                                                <option  value="{{Auth::user()->id}}">{{Auth::user()->name}}</option>
                                                                @endif
                                                        </select>
                                                        <span class="m-form__help"></span>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <label for="message-text" class="form-control-label">Comment:</label>
                                                        <textarea class="form-control" id="orderComment" name="orderComment">done</textarea>
                                                        <span class="m-form__help"></span>
                                                    </div>
                                                    
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-lg-3">
                                                        <label class="">Client Email:</label>
                                                        <input type="text" id="txtClientEmail" name="txtClientEmail"  class="form-control m-input" placeholder="Client Email">
                                                        <span class="m-form__help"></span>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="">Client Notify:</label>
                                                        <div class="m-checkbox-list">
                                                                <label class="m-checkbox">
                                                                    <input type="checkbox" id="client_notify" name="client_notify" value="1"> Email Sent
                                                                    <span></span>
                                                                </label>
                                                        </div>                                                        
                                                    </div>
                                                    <div class="col-lg-3">
                                                            <label class="">Total Order Units:</label>
                                                            <input type="text" id="GtxtTotalOrderUnit" name="txtTotalOrderUnit"  class="form-control m-input" placeholder="5000">
                                                            <span class="m-form__help"></span>

                                                    </div>
                                                    <div class="col-lg-3">
                                                       
                                                        <div class="m-form__group form-group" style="display:none">
																<label for="">Dispatch Type</label>
																<div class="m-radio-inline">
																	<label class="m-radio">
																		<input type="radio" name="dispatch_type" checked value="1"> Complete 
																		<span></span>
																	</label>
																	<label class="m-radio">
																		<input type="radio" name="dispatch_type" value="2"> Partial 
																		<span></span>
																	</label>
																	
																</div>
																<span class="m-form__help"></span>
														</div>
                                                    </div>
                                                </div>
                                                {{-- aja --}}
                                                <div id="m_repeater_3">
                                                        <div class="row" id="m_repeater_3">                                                            
                                                            <div data-repeater-list="orderFromData" class="col-lg-12" style="background-color:#ccc;border:1px red">
                                                                <div data-repeater-item class="form-group m-form__group row">
                                                                    
                                                                <div class="col-lg-3">
                                                                        <label class="">LR NO:</label>
                                                                        <input type="text" id="txtLRNo"  name="txtLRNo" class="form-control m-input" placeholder="LR NO">
                                                                        <span class="m-form__help"></span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                        <label class="">Transpoter:</label>
                                                                        <input type="text" id="txtTransport"  name="txtTransport" class="form-control m-input" placeholder="Transpoter">
                                                                        <span class="m-form__help"></span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                        <label class="">Cartons:</label>
                                                                        <input type="text" id="txtCartons" name="txtCartons"  class="form-control m-input" placeholder="Cartons">
                                                                        <span class="m-form__help"></span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                        <label class="">Cartons(Units):</label>
                                                                        <input type="text" id="txtCartonsEachUnit" name="txtCartonsEachUnit" class="form-control m-input" placeholder="Units in Cartons">
                                                                        <span class="m-form__help"></span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                        <label class="">Total Units:</label>
                                                                        <input type="text" id="txtTotalUnit"  name="txtTotalUnit" class="form-control m-input" placeholder="Total Units">
                                                                        <span class="m-form__help"></span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                        <label class="">Booking For:</label>
                                                                        <input type="text" id="txtBookingFor"  name="txtBookingFor" class="form-control m-input" placeholder="Booking For">
                                                                        <span class="m-form__help"></span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                        <label class="">PO NO.:</label>
                                                                        <input type="text" id="txtPONumber" name="txtPONumber" class="form-control m-input" placeholder="">                                               
                                                                        <span class="m-form__help"></span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                        <label class="">Invoice No:</label>
                                                                        <input type="text" id="txtInvoice" name="txtInvoice" class="form-control m-input" placeholder="Invoice No">                                               
                                                                        <span class="m-form__help"></span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <label class="">Disptach Date:</label>
                                                                    <input type="text" id="m_datepicker_1" name="txtDispatchDate" class="form-control m-input" placeholder="Dispatch Date">                                               
                                                                    <span class="m-form__help"></span>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                        <label class="">Client Email:</label>
                                                                        <input type="text" id="txtClientEmailSend" name="txtClientEmailSend"  class="form-control m-input" placeholder="Client Email">
                                                                        <span class="m-form__help"></span>
                                                                </div>


                                                                <div class="col-md-3">
                                                                    <div data-repeater-delete="" style="margin-top:31px" class="btn-sm btn btn-danger m-btn m-btn--icon m-btn--pill">
                                                                        <span>
                                                                            <i class="la la-trash-o"></i>
                                                                            <span>Remove</span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                        <br>
                                                        <div class="m-form__group form-group row">
                                                            <label class="col-lg-2 col-form-label"></label>
                                                            <div class="col-lg-4">
                                                                <div data-repeater-create="" class="btn btn btn-sm btn-brand m-btn m-btn--icon m-btn--pill m-btn--wide">
                                                                    <span>
                                                                        <i class="la la-plus"></i>
                                                                        <span>Add</span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                              
                                           
                                                {{-- aja --}}
                                            </div>
                                            
                                      
    
                                        <!--end::Form-->

                
            </div>
            <div class="modal-footer">
                <button type="submit"  class="btn btn-primary btn-sm m-btn 	m-btn m-btn--icon">
                        <span>                              
                            <span>Process Complete</span>
                        </span>
                </button>

            
            </div>
        </form>
        </div>
    </div>
</div>

<!--end::Modal-->
<script>
$( document ).ready(function() {
    
    $('.ajproview').hover(function(){
     userId=$(this).attr('id');
     photo =$(this).data("photo");
     name =$(this).data("name");     
     phone =$(this).data("phone");     
     $('#txtEMPName').html(name);
     $('.viewProfilePIC').html(`<!--begin:: Widgets/Blog-->
								<div class="m-portlet m-portlet--bordered-semi m-portlet--full-height  m-portlet--rounded-force">
									<div class="m-portlet__head m-portlet__head--fit">
										<div class="m-portlet__head-caption">
											<div class="m-portlet__head-action">
												<button type="button" class="btn btn-sm m-btn--pill  btn-brand"></button>
											</div>
										</div>
									</div>
									<div class="m-portlet__body">
										<div class="m-widget19">
											<div class="m-widget19__pic m-portlet-fit--top m-portlet-fit--sides" style="min-height-: 286px">
												<img src="${photo}" alt="">
												
												<div class="m-widget19__shadow"></div>
											</div>
											<div class="m-widget19__content">
												<div class="m-widget19__header">
													
													<div class="m-widget19__info">
														<span class="m-widget19__username">
                                                        Phone:
														</span><br>
														
													</div>
													<div class="m-widget19__stats">
														<span class="m-widget19__number m--font-brand">
														${phone}
														</span>
														
													</div>
												</div>
												
											</div>
											
										</div>
									</div>
								</div>

								<!--end:: Widgets/Blog-->`);

    

     

        $('#viewEMPPic').modal('show');

    });

    $("#myFormFinalDispatchV1").submit(function(e) {

e.preventDefault(); // avoid to execute the actual submit of the form.

var form = $(this);
var url = form.attr('action');

$.ajax({
       type: "POST",
       url: url,
       data: form.serialize(), // serializes the form's elements.
       success: function(res)
       {
         
         if(res.status==0){
            toasterOptions();
          toastr.error(res.Message, 'Order Process');
          return false; 
         }else{
            toasterOptions();
          toastr.success(res.Message, 'Order Process');
          return false; 
         }
          
          
       },
       dataType : 'json'
     });


});



});

</script>


<!--begin::Modal-->
<div class="modal fade" id="viewEMPPic" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-sm" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="txtEMPName"></h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body viewProfilePIC">
										

									</div>
									
								</div>
							</div>
						</div>

						<!--end::Modal-->

                        <script type="text/javascript">

                        $('#btnShowFiletPIEChart').click(function(){
                            
var salesPerson=$('#salesPerson').val();
var txtMonth=$('#txtMonth').val();
var txtyear=$('#txtyear').val();



                             // Load the Visualization API and the piechart package.
    google.charts.load('current', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);
        
      function drawChart() {
  
      var formData = {
      
      '_token':$('meta[name="csrf-token"]').attr('content'),
      'salesPerson':salesPerson,      
      'txtMonth':txtMonth,      
      'txtyear':txtyear,      

      };
  
  
        var jsonData = $.ajax({
            url: BASE_URL+'/getSampleFeedbackPIE',
            dataType: "json",
            type: "POST",
            data: formData,
            async: false
            }).responseText;
            
        // Create our data table out of JSON data loaded from server.
        var data = new google.visualization.DataTable(jsonData);
            
            console.log(jsonData);

            
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('b_sale'));
        chart.draw(data, {width: 400, height: 240});
      }

                        });
    

   

    </script>


<script>
$(document).ready(function(){

 $(document).on('click', '.pagination a', function(event){
  event.preventDefault(); 
  var page = $(this).attr('href').split('page=')[1];
  fetch_data(page);
 });

 function fetch_data(page)
 {
  $.ajax({
   url:"/pagination/fetch_data?page="+page,
   success:function(data)
   {
    $('#table_data').html(data);
   }
  });
 }
 
});
</script>



                        

<!--begin::Modal-->
<div class="modal fade" id="m_modal_4_sendQuation_view" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">New message</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form>
											<div class="form-group">
												<label for="recipient-name" class="form-control-label">Recipient:</label>
												<input type="text" class="form-control" id="recipient-name">
											</div>
											<div class="form-group">
												<label for="message-text" class="form-control-label">Message:</label>
												<textarea class="form-control" id="message-text"></textarea>
											</div>
										</form>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										<button type="button" class="btn btn-primary">Send message</button>
										<button type="button" class="btn btn-primary">Send message</button>
									</div>
								</div>
							</div>
						</div>

						<!--end::Modal-->


    </body>

</html>

