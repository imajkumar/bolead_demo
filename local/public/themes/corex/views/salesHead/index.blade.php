<?php
$user = auth()->user();
							$userRoles = $user->getRoleNames();
							$user_role = $userRoles[0];


use Carbon\Carbon;




?>
	<div class="m-content">


			@if (session('status'))
			<div class="alert alert-danger">
				{{ session('status') }}
			</div>
		   @endif







			<div class="row" >

					<div class="col-xl-12" >
                     <!-- ajcode for lead stage -->
											 <!--begin::Section-->
											 <h4>Lead Stages</h4>
										<div class="m-section">
											<div class="m-section__content">
												<table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
													<thead>
															<tr>
															<th>Fresh</th>
															<th>Assigned</th>
															<th>Qualified</th>
															<th>Sampling</th>
															<th>Client</th>
															<th>Repeat Client</th>
															<th>Lost</th>
															<th>Total Lead</th>
															<th>Unqualified</th>
															<th>Irrelevant</th>
														</tr>
													</thead>
													<tbody>
													<?php
														$lead_data = DB::table('lead_map_data')->first();

														?>
										 <tr>
														<td>
														<b>{{optional($lead_data)->fresh_lead}}<b>
														</td>
														<td>
														<strong>{{optional($lead_data)->assign_lead}}</strong>
														</td>
														<td>
														<strong>{{optional($lead_data)->qualified_lead}}</<strong>
														</td>
														<td>
														<strong>{{optional($lead_data)->sample_lead}}</<strong>
														</td>
														<td>
														<strong>{{optional($lead_data)->client_lead}}</strong>
														</td>
														<td>
														<strong>{{optional($lead_data)->repeat_lead}}</strong>
														</td>
														<td>
														<strong>{{optional($lead_data)->lost_lead}}</strong>
														</td>
														<td>
														<span>
										<span class="m-badge m-badge--warning m-badge--wide m-badge--rounded">
										<strong>{{optional($lead_data)->total_lead}}</strong>
										</span>
								</span>


														</td>
														<td>
														<strong>{{optional($lead_data)->unqualified_lead}}</strong>
														</td>
														<td>
														<strong>{{optional($lead_data)->irrelevant}}</strong>
														<span style="margin-bottom: -35px;" class="m-badge m-badge--warning m-badge--wide">{{ date("d-M-Y h:i:s A", strtotime(optional($lead_data)->update_at) )   }}</span>
														</td>

														</tr>

													</tbody>
												</table>

<br>


												<!--begin: Datatable -->
															<table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_LEADList_AllView">
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

											</div>
										</div>

										<!--end::Section-->

											 <!-- ajcode for lead stage -->

								<hr>






		         </div>
        	</div>
    </div>
