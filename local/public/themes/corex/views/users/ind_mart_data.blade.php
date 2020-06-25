
<style>

.show-read-more .more-text{

	display: none;

}

</style>
<!-- main  -->
<div class="m-content">
   
    <div class="m-portlet m-portlet--mobile">
       <div class="m-portlet__head">
          <div class="m-portlet__head-caption">
             <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                   Freah Leads  
				   
				   <?php 
													 $user = auth()->user();
													 $userRoles = $user->getRoleNames();
													 $user_role = $userRoles[0];
													 if($user_role=='Admin' || $user_role=='SalesHead' || Auth::user()->id==77 || Auth::user()->id==3 || Auth::user()->id==40 ){
														 ?>

					<span class="m-badge m-badge--warning m-badge--wide" style="margin-left:5px">
				   <strong > 
				   <?php 
				  $arr_data= DB::table('indmt_data')->latest('created_at')->first();				  
				  echo "Updated".$newDate = date(" j F Y h:iA", strtotime($arr_data->created_at));

				   ?>
				   </strong>

				   </span>

				   <span class="m-badge m-badge--info m-badge--wide" style="margin-left:5px">
				   <strong > 
				   <?php 
				  $arr_data= DB::table('indmt_data')->where('lead_status',0)->get();
				  
				  echo "Pending:".count($arr_data);


				   ?>
				   </strong>

				   </span>
				   <a href="{{route('viewMissedCronJob')}}">
				   <span class="m-badge m-badge--default m-badge--wide" style="margin-left:5px">
				   
				   Missed:
				   <?php 
				   $data_lm=AyraHelp::getLeadMissedRun();
				   echo count($data_lm);

				   ?>
				   </span>
				   </a>

														 <?php
													 }
													 ?>

				  

				  
                </h3>
             </div>
          </div>
          <div class="m-portlet__head-tools">
             <ul class="m-portlet__nav">
			 <?php
			 if($user_role=='Admin' || $user_role=='SalesHead' || Auth::user()->id==77 ||  Auth::user()->id==130 ||  Auth::user()->id==131){
				 ?>
					<li class="m-portlet__nav-item">
                        <a href="{{route('add_lead_data')}}" class="btn btn-primary m-btn m-btn--custom m-btn--icon">
                        <span>
                        <i class="la la-plus"></i>
                        <span>ADD NEW LEAD </span>
                        </span>
                        </a>
                     </li>
				 <?php

			 }
			  ?>
                  
                <li class="m-portlet__nav-item">
                   <a href="/" class="btn btn-secondary m-btn m-btn--custom m-btn--icon">
                   <span>
                   <i class="la la-arrow-left"></i>
                   <span>BACK </span>
                   </span>
                   </a>
                </li>
                
             </ul>
          </div>
       </div>
       <div class="m-portlet__body">
	   <!--begin::Section-->
	 

											
													<?php 
													 $user = auth()->user();
													 $userRoles = $user->getRoleNames();
													 $user_role = $userRoles[0];
													 if($user_role=='Admin' || $user_role=='SalesHead' || Auth::user()->id==77 || Auth::user()->id==3 || Auth::user()->id==4 || Auth::user()->id==40){
														?>
														<div class="m-demo__preview m-demo__preview--btn">
														<a href="javascript:void(0)" onclick="viewAllAssign()" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-users"></i>
																<span>Assigned</span>
															</span>
														</a>
														<a href="javascript:void(0)" onclick="viewAllIreevant()" class="btn btn-secondary btn-sm m-btn 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-list-3"></i>
																<span>Irrelevant</span>
															</span>
														</a>
														<a href="javascript:void(0)" onclick="viewFreshLead()" class="btn btn-secondary btn-sm m-btn 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-list"></i>
																<span>Fresh Lead</span>
															</span>
														</a>

														<a href="javascript:void(0)" onclick="viewUnQualifiedLead()" class="btn btn-info btn-sm m-btn 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-list"></i>
																<span>UnQualified Lead</span>
															</span>
														</a>
														<a href="javascript:void(0)" onclick="viewHOLDLead()" class="btn btn-warning btn-sm m-btn 	m-btn m-btn--icon">
															<span>
															<i class="fa fa-hand-point-right"></i>
																<span>HOLD Lead</span>
															</span>
														</a>
														<a href="javascript:void(0)" onclick="viewDUPLICATELead()" class="btn btn-default btn-sm m-btn 	m-btn m-btn--icon">
															<span>
															<i class="fa fa-hand-point-right"></i>
																<b><span>DUPLICATE</span></b>
															</span>
														</a>

														<div style="margin-bottom:10px "></div>

														<!--begin: Search Form -->
								<form class="m-form m-form--fit m--margin-bottom-20">
									<div class="row m--margin-bottom-20">
										
										
										<div class="col-lg-3 m--margin-bottom-10-tablet-and-mobile">
											<label><b>Stages</b></label>
											<select class="form-control m-input"   data-col-index="5">
                                                <option  value="">-SELECT- </option>                                               
                                                @foreach (AyraHelp::getAllStagesLead() as $stage)
                                                <option  value="{{  str_replace('/', '-', $stage->stage_name) }}">{{$stage->stage_name}}</option>
                                                @endforeach
                                           </select>
										</div>


										<div class="col-lg-3 m--margin-bottom-10-tablet-and-mobile">
											<label><b>Assigned Users</b></label>
											<select class="form-control m-input"   data-col-index="6">
                                                <option  value="">-SELECT-</option>
                                                <?php
                                                $user = auth()->user();
                                                $userRoles = $user->getRoleNames();
                                                $user_role = $userRoles[0];
                                                ?>
                                                @if ($user_role =="Admin" || Auth::user()->id==77 || Auth::user()->id==90)
                                                @foreach (AyraHelp::getSalesAgentAdmin() as $user)
												@if ($user->id==130 || $user->id==131
												|| $user->id==78
												|| $user->id==83
												|| $user->id==85
												|| $user->id==84
												|| $user->id==87
												|| $user->id==88
												|| $user->id==89
												|| $user->id==91
												|| $user->id==93
												|| $user->id==95
												|| $user->id==98
												|| $user->id==108
												
												

												)

												@else
												<option  value="{{$user->name}}">{{$user->name}}</option>
												@endif
                                               
                                                @endforeach
                                                @else
                                                <option  value="{{Auth::user()->id}}">{{Auth::user()->name}}</option>
                                                @endif
												<option  value="DEEPIKA JOSHI">DEEPIKA JOSHI</option>
                                        
                                        </select>
										</div>

										

										<div class="col-lg-3 m--margin-bottom-10-tablet-and-mobile">
											<label><b>LEAD FROM</b></label>
											<select class="form-control m-input"   data-col-index="7">
                                                <option  value="">-SELECT-</option>
												<option  value="INDIA">INDIA</option>
												<option  value="FOREIGN">FOREIGN</option>
                                        </select>
										</div>

										<div class="col-lg-3 m--margin-bottom-10-tablet-and-mobile">
										     <button class="btn btn-brand m-btn m-btn--icon" id="m_search" style="margin-top:25px">
												<span>
													<i class="la la-search"></i>
													<span>Search</span>
												</span>
											</button>

											<button class="btn btn-secondary m-btn m-btn--icon" id="m_reset" style="margin-top:25px">
												<span>
													<i class="la la-close"></i>
													<span>Reset</span>
												</span>
											</button>


										</div>

										



										
									</div>
									
									
									
								</form>


														<!-- <a href="#" class="btn btn-success btn-sm m-btn 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-time-3"></i>
																<span>Success</span>
															</span>
														</a>
														<a href="#" class="btn btn-warning m-btn btn-sm 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-file-1"></i>
																<span>Warning</span>
															</span>
														</a>
														<a href="#" class="btn btn-danger m-btn btn-sm 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-profile-1"></i>
																<span>Danger</span>
															</span>
														</a>
														<a href="#" class="btn btn-brand m-btn btn-sm 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-time-2"></i>
																<span>Brand</span>
															</span>
														</a> -->
														
								
														
													</div>

														<?php
													 }else{
														 ?>

														 <a href="javascript:void(0)" title="Click here to refresh Lead List"  onclick="viewFreshLead()" class="btn btn-warning btn-sm m-btn 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-list"></i>
																<span>Refresh Lead</span>
															</span>
														</a>
														<a href="javascript:void(0)" onclick="viewUnQualifiedLead()" class="btn btn-info btn-sm m-btn 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-list"></i>
																<span>UnQualified Lead</span>
															</span>
														</a>
														<?php 

															$data_arr_data_data = DB::table('indmt_data')
															->join('lead_assign', 'indmt_data.QUERY_ID', '=', 'lead_assign.QUERY_ID')           
															->where('lead_assign.assign_user_id', '=', Auth::user()->id) 
															//->where('indmt_data.lead_status', '=', 0) 
															->whereDate('lead_assign.created_at',date('Y-m-d'))
															->orderBy('lead_assign.created_at','desc')
															->select('indmt_data.*', 'lead_assign.assign_by', 'lead_assign.msg')
															->get();

														?>
														<a href="javascript:void(0)" onclick="viewTodayLeadLead()" class="btn btn-secondary btn-sm m-btn 	m-btn m-btn--icon">
														
															<span>
																<i class="fa flaticon-list"></i>
																<span><b>Today Lead</b>
																<span class="m-badge m-badge--warning"> {{count($data_arr_data_data)}}</span>
																</span>
															</span>
														</a>

														<div class="m-separator m-separator--dashed m--margin-top-1"></div>													

														 <?php
													 }
													?>
													
											
											
										

										<!--end::Section-->

          <!-- form  -->
		  <!--begin::Section-->
		  <div class="m-section">
											<span class="m-section__sub">Fresh Lead filter by:</span>
											<div class="m-section__content">
												<div class="m-demo" data-code-preview="true" data-code-html="true" data-code-js="false">
													
														
														<a href="javascript:void(0)" onclick="viewDIRECTLead()" class="btn btn-outline-primary btn-sm 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-list-2"></i>
																<span>DIRECT</span>
															</span>
														</a>
														<a href="javascript:void(0)" onclick="viewBUYLead()" class="btn btn-outline-accent btn-sm 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-multimedia-2"></i>
																<span>BUY</span>
															</span>
														</a>
														<a href="javascript:void(0)" onclick="viewPHONELead()" class="btn btn-outline-success btn-sm 	m-btn m-btn--icon">
															<span>
															<i class="flaticon-support"></i>
																<span>PHONE</span>
															</span>
														</a>
														<a href="javascript:void(0)" onclick="viewINHOUSELead()" class="btn btn-outline-warning 	btn-sm 	m-btn m-btn--icon">
															<span>
																<i class="fa flaticon-file"></i>
																<span>IN HOUSE</span>
															</span>
														</a>
														
														
												</div>
											</div>
										</div>

										<!--end::Section-->


<!--begin: Datatable -->
			<table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_LEADList">
				<thead>
					<tr>
						
						<th>LID</th>
						<th>Company</th>
						<th>Location</th>
						<th>Product</th>
						<th>Message</th>
						<th>Status</th>

						 <th>Date</th>					
						 <th>Source</th>					
						 
						<th>Actions</th>
					</tr>
				</thead>

			</table>

          	<!--begin: Datatable -->
								
							</div>
						</div>

						<!-- END EXAMPLE TABLE PORTLET-->

          <!-- form  -->
       </div>
    </div>
    
 </div>
 
 
 

 <!--begin::Modal-->
 <div class="modal fade" id="m_modal_ViewINDMartData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg" role="document">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
								<div class="modal-content">	
									<div class="modal-body ">
										
										<!--begin::Portlet-->
								<div class="m-portlet">
									
									<div class="m-portlet__body">
										<ul class="nav nav-pills nav-fill" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" href="#m_tabs_5_1">LEAD INFO</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" href="#m_tabs_5_2">HISTORY </a>
											</li>
											<li class="nav-item">
												<a class="nav-link disabled" data-toggle="tab" href="#m_tabs_5_3"></a>
											</li>
											<li class="nav-item">
												<a class="nav-link disabled" data-toggle="tab" href="#m_tabs_5_4"></a>
											</li>
										</ul>
										<div class="tab-content">
											<div class="showINDMartData tab-pane active " id="m_tabs_5_1" role="tabpanel">
												
											</div>
											<div class="showINDMartData_HIST tab-pane" id="m_tabs_5_2" role="tabpanel">
												It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more
												recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
											</div>
											<div class="tab-pane" id="m_tabs_5_3" role="tabpanel">
												Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type
												specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged
											</div>
											<div class="tab-pane" id="m_tabs_5_4" role="tabpanel">
												Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type
												specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
												industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and
											</div>
										</div>
									</div>
								</div>

								<!--end::Portlet-->
								
									</div>
									
								</div>
							</div>
						</div>

						<!--end::Modal-->


						<!--begin::Modal-->
						<div class="modal fade" id="m_modal_LeadNotesAddedList" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Notes</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body listaddednoteslist">
										
									</div>
									
								</div>
							</div>
						</div>

						<!--end::Modal-->
						
						

						<!--begin::Modal-->
<div class="modal fade" id="m_modal_LeadAssignModel_ToOther" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-sm" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Lead Assignment</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form>
										   <input type="hidden" id="QUERY_ID_ToOther" >
											<div class="form-group">
												<label for="recipient-name" class="form-control-label">Sales Person:</label>
												<select class="form-control m-input"  id="assign_user_id_toOther">
                                                <option  value="">-SELECT-</option>
                                                <?php
                                                $user = auth()->user();
                                                $userRoles = $user->getRoleNames();
                                                $user_role = $userRoles[0];
                                                ?>                                                
                                                @foreach (AyraHelp::getSalesAgentAdmin() as $user)
												@if($user->id==Auth::user()->id)
												@else
                                                <option  value="{{$user->id}}">{{$user->name}}</option>
												@endif
                                                @endforeach
												<option  value="102">DEEPIKA JOSHI</option>
                                                
                                        
                                        </select>

											</div>
											<div class="form-group">
												<label for="message-text" class="form-control-label">Message:</label>
												<textarea class="form-control" id="assign_msg_ToOther"></textarea>
											</div>
										</form>
									</div>
									<div class="modal-footer">										
										<button type="button" class="btn btn-primary" id="btnAssign_ToOther">Assign Now</button>
									</div>
								</div>
							</div>
						</div>

						<!--end::Modal-->


<!--begin::Modal-->
<div class="modal fade" id="m_modal_LeadAssignModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-sm" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Lead Assignment</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form>
										   <input type="hidden" id="QUERY_ID" >
											<div class="form-group">
												<label for="recipient-name" class="form-control-label">Sales Person:</label>
												<select class="form-control m-input"  id="assign_user_id">
                                                <option  value="">-SELECT-</option>
												
                                                <?php
                                                $user = auth()->user();
                                                $userRoles = $user->getRoleNames();
                                                $user_role = $userRoles[0];
                                                ?>
                                                @if ($user_role =="Admin" || Auth::user()->id==3   || Auth::user()->id==77 || Auth::user()->id==90 || Auth::user()->id==130 || Auth::user()->id==131)
                                                @foreach (AyraHelp::getSalesAgentAdmin() as $user)
												@if ($user->id==134 || $user->id==135 ||  $user->id==136
												|| $user->id==78
												|| $user->id==83
												|| $user->id==85
												|| $user->id==84
												|| $user->id==87
												|| $user->id==88
												|| $user->id==89
												|| $user->id==91
												|| $user->id==93
												|| $user->id==95
												|| $user->id==98
												|| $user->id==108
												
												

												)

												@else
												<option  value="{{$user->id}}">{{$user->name}}</option>
												@endif
                                               
                                                @endforeach
                                                @else
                                                <option  value="{{Auth::user()->id}}">{{Auth::user()->name}}</option>
                                                @endif
												<option  value="102">DEEPIKA JOSHI</option>
                                        
                                        </select>

											</div>
											<div class="form-group">
												<label for="message-text" class="form-control-label">Message:</label>
												<textarea class="form-control" id="assign_msg"></textarea>
											</div>
										</form>
									</div>
									<div class="modal-footer">										
										<button type="button" class="btn btn-primary" id="btnAssign">Assign Now</button>
									</div>
								</div>
							</div>
						</div>

						<!--end::Modal UnQualified -->
						

						 <!--begin::Modal-->
						 <div class="modal fade" id="m_modal_LeadUnQliFiedModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-sm" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Lead UnQualifed</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form>
										<input type="hidden" id="QUERY_IDA_UNQLI" >
										<div class="form-group">
												<label for="message-text" class="form-control-label">Unqualified Type:</label>
												<select name="unqlified_type" id="unqlified_type" class="form-control">
												<option value="">--SELECT--</option>
												<?php 
												
												$arr_data=DB::table('iIrrelevant_type')->get();
												foreach ($arr_data as $key => $rowData) {
													?>
													<option value="{{$rowData->id}}">{{$rowData->Irrelevant_name}}</option>
													<?php
												}

												?>
												</select>
												
											</div>
											<div class="form-group">
												<label for="message-text" class="form-control-label">Message:</label>
												<textarea class="form-control" id="txtMessageUnQLiFiedReponse"></textarea>
											</div>
										</form>
									</div>
									<div class="modal-footer">										
										<button type="button" class="btn btn-primary" id="btnSubmitUnQlifiedResponse">Submit Now</button>
									</div>
								</div>
							</div>
						</div>

						<!--end::Modal-->







                  <!--begin::Modal-->
                  <div class="modal fade" id="m_modal_LeadIrrelevantModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-sm" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Lead Irrelevant</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form>
										<input type="hidden" id="QUERY_IDA" >
										<div class="form-group">
												<label for="message-text" class="form-control-label">Irrelevant Type:</label>
												<select name="iIrrelevant_type" id="iIrrelevant_type" class="form-control">
												<option value="">--SELECT--</option>

												<?php 
												
												$arr_data=DB::table('iIrrelevant_type')->get();
												foreach ($arr_data as $key => $rowData) {
													?>
													<option value="{{$rowData->id}}">{{$rowData->Irrelevant_name}}</option>
													<?php
												}

												?>
												</select>
												
											</div>
											<div class="form-group">
												<label for="message-text" class="form-control-label">Message:</label>
												<textarea class="form-control" id="txtMessageIreeReponse"></textarea>
											</div>
										</form>
									</div>
									<div class="modal-footer">										
										<button type="button" class="btn btn-primary" id="btnSubmitLeadResponse">Submit Now</button>
									</div>
								</div>
							</div>
						</div>

						<!--end::Modal-->

						 <!-- Modal -->
			  <div class="modal fade" id="m_modal_LeadAddNotesModel_sales" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLongTitle">Lead Notes</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">						
								<input type="hidden" id="QUERY_IDB_sales" >
								<div class="form-group">
										<label for="message-text" class="form-control-label">*Message:</label>
										<textarea class="form-control" id="txtNotesLead"  name="txtNotesLead"></textarea>
								</div>

								<div class="form-group m-form__group">
									<label>Next Follow Up</label>
									<div class="input-group">
										<input type="text" readonly id="shdate_input" class="form-control" aria-label="Text input with dropdown button">
										<div class="input-group-append">
											<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="la la-calendar glyphicon-th"></i>
											</button>
											<div class="dropdown-menu">
												<a class="dropdown-item" href="javascript:void(0)" id="aj_today">Today</a>
												<a class="dropdown-item" href="javascript:void(0)" id="aj_3days" >3 Days</a>
												<a class="dropdown-item" href="javascript:void(0)" id="aj_7days" >7 Days</a>																	
												<a class="dropdown-item" href="javascript:void(0)" id="aj_15days" >15 Days</a>
												<a class="dropdown-item" href="javascript:void(0)" id="aj_next_month" >Next Month</a>
											</div>
										</div>
									</div>
								</div>
								
							</div>
							<div class="modal-footer">
								<button type="button"  class="btn btn-secondary" data-dismiss="modal">Close</button>
								<button type="button" id="btnLeadNotesSales" class="btn btn-primary">Save</button>
							</div>
						</div>
					</div>
				</div>

	  <!-- m_modal_6 -->



						<!--begin::Modal-->
						<div class="modal fade" id="m_modal_LeadAddN5otesModel_sales" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-sm" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Lead Notes :Sales</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form>
										<input type="hidden" id="QUERY_IDB_sales" >
											<div class="form-group">
												<label for="message-text" class="form-control-label">Message:</label>
												<textarea class="form-control" id="txtMessageNoteReponse_sales"></textarea>
											</div>
										</form>
									</div>
									<div class="modal-footer">										
										<button type="button" class="btn btn-primary" id="bt6nSubmitNote" >Submit Note Now</button>
									</div>
								</div>
							</div>
						</div>

						<!--end::Modal-->



                   <!--begin::Modal-->
                   <div class="modal fade" id="m_modal_LeadAddNotesModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-sm" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Lead Notes</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form>
										<input type="hidden" id="QUERY_IDB" >
										


											<div class="form-group">
												<label for="message-text" class="form-control-label">Message:</label>
												<textarea class="form-control" id="txtMessageNoteReponse"></textarea>
											</div>
										</form>
									</div>
									<div class="modal-footer">										
										<button type="button" class="btn btn-primary" id="btnSubmitNote" >Submit Note Now</button>
									</div>
								</div>
							</div>
						</div>

						<!--end::Modal-->



<!--begin::Modal-->
<div class="modal fade" id="m_modal_4_2_GeneralViewModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Stage Progress</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
                                         <!-- ajtab -->
                                         <style>
                                         .breadcrumb {
                                                 /*centering*/
    display: inline-block;
    box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.35);
    overflow: hidden;
    border-radius: 5px;
    /*Lets add the numbers for each link using CSS counters. flag is the name of the counter. to be defined using counter-reset in the parent element of the links*/
    counter-reset: flag; 
}

.breadcrumb a {
    text-decoration: none;
    outline: none;
    display: block;
    float: left;
    font-size: 12px;
    line-height: 36px;
    color: white;
    /*need more margin on the left of links to accomodate the numbers*/
    padding: 0 10px 0 60px;
    background: #16426b;
    background: linear-gradient(#16426b, #16426b);
    position: relative;
}
/*since the first link does not have a triangle before it we can reduce the left padding to make it look consistent with other links*/
.breadcrumb a:first-child {
    padding-left: 46px;
    border-radius: 5px 0 0 5px; /*to match with the parent's radius*/
}
.breadcrumb a:first-child:before {
    left: 14px;
}
.breadcrumb a:last-child {
    border-radius: 0 5px 5px 0; /*this was to prevent glitches on hover*/
    padding-right: 20px;
}

/*hover/active styles*/
.breadcrumb a.active, .breadcrumb a:hover{
    background: #008031;
    background: linear-gradient(#008031, #008031);
}
.breadcrumb a.active:after, .breadcrumb a:hover:after {
    background: #008031;
    background: linear-gradient(135deg, #008031, #008031);
}

/*adding the arrows for the breadcrumbs using rotated pseudo elements*/
.breadcrumb a:after {
    content: '';
    position: absolute;
    top: 0; 
    right: -18px; /*half of square's length*/
    /*same dimension as the line-height of .breadcrumb a */
    width: 36px; 
    height: 36px;
    /*as you see the rotated square takes a larger height. which makes it tough to position it properly. So we are going to scale it down so that the diagonals become equal to the line-height of the link. We scale it to 70.7% because if square's: 
    length = 1; diagonal = (1^2 + 1^2)^0.5 = 1.414 (pythagoras theorem)
    if diagonal required = 1; length = 1/1.414 = 0.707*/
    transform: scale(0.707) rotate(45deg);
    /*we need to prevent the arrows from getting buried under the next link*/
    z-index: 1;
    /*background same as links but the gradient will be rotated to compensate with the transform applied*/
    background: #16426b;
    background: linear-gradient(135deg, #16426b, #16426b);
    /*stylish arrow design using box shadow*/
    box-shadow: 
        2px -2px 0 2px rgba(0, 0, 0, 0.4), 
        3px -3px 0 2px rgba(255, 255, 255, 0.1);
    /*
        5px - for rounded arrows and 
        50px - to prevent hover glitches on the border created using shadows*/
    border-radius: 0 5px 0 50px;
}
/*we dont need an arrow after the last link*/
.breadcrumb a:last-child:after {
    content: none;
}
/*we will use the :before element to show numbers*/
.breadcrumb a:before {
    content: counter(flag);
    counter-increment: flag;
    /*some styles now*/
    border-radius: 100%;
    width: 20px;
    height: 20px;
    line-height: 20px;
    margin: 8px 0;
    position: absolute;
    top: 0;
    left: 30px;
    background: #444;
    background: linear-gradient(#444, #222);
    font-weight: bold;
}


.flat a, .flat a:after {
    background: white;
    color: black;
    transition: all 0.5s;
}
.flat a:before {
    background: white;
    box-shadow: 0 0 0 1px #ccc;
}
.flat a:hover, .flat a.active, 
.flat a:hover:after, .flat a.active:after{
    background: #008080;
}

.ajkumar{
    /* background: gray !important; */
   
}

li:disabled {
  background: #dddddd;
}






</style>

                                       <!--begin::Section-->
										<div class="m-section">
											
											<div class="m-section__content">
												<table class="table table-sm m-table m-table--head-bg-brand">
												<thead class="thead-inverse">
														<tr>
															<th>#</th>
															<th>Stage Name</th>
															<th>Created at</th>
															<th>Message</th>
															<th>Completed By</th>
														</tr>
													</thead>
													
													<tbody class="StageActionHistory">
														
														
													</tbody>
												</table>
											</div>
										</div>

										<!--end::Section-->
                                       
                                     </div>
                                        <!-- a simple div with some links -->
                                        <div class="breadcrumb ajcustomProgessBar" style="text-align: center;">

                                        </div>




                                         <!-- ajtab -->
										
									</div>
									
								</div>
							</div>
						</div>

						<!--end::Modal-->
<!-- v1 model -->
                  