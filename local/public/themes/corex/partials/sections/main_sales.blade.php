<?php
$user = auth()->user();
							$userRoles = $user->getRoleNames();
							$user_role = $userRoles[0];
if($user_role=='Admin'){
$client_arr_data=AyraHelp::getClientCountbyid();
}else{
$client_arr_data=AyraHelp::getClientCountbyid(Auth::user()->id);
}
if($user_role=='Admin'){
$sample_arr_data=AyraHelp::getSampleCountbyid();
}else{
$sample_arr_data=AyraHelp::getSampleCountbyid(Auth::user()->id);
}

use Carbon\Carbon;
							if($user_role=='Admin'){
								$today_node = App\Client::where('is_deleted','!=',1)->whereDate('follow_date', Carbon::today())->orderBy('follow_date','ASC')->get();
								$yesterday_node = App\Client::where('is_deleted','!=',1)->whereDate('follow_date', Carbon::yesterday())->orderBy('follow_date','ASC')->get();
								$without_sch = App\Client::where('is_deleted','!=',1)->whereDate('follow_date', Carbon::now()->subDays(365))->orderBy('follow_date','ASC')->get();

							}else{
								$today_node = App\Client::where('is_deleted','!=',1)->whereDate('follow_date', Carbon::today())->where('user_id',Auth::user()->id)->orderBy('follow_date','ASC')->get();
								$yesterday_node = App\Client::where('is_deleted','!=',1)->whereDate('follow_date', Carbon::yesterday())->where('user_id',Auth::user()->id)->orderBy('follow_date','ASC')->get();
								$without_sch = App\Client::where('is_deleted','!=',1)->where('user_id',Auth::user()->id)->whereDate('follow_date', Carbon::now()->subDays(365))->orderBy('follow_date','ASC')->get();
							}




?>
	<div class="m-content">	


			@if (session('status'))
			<div class="alert alert-danger">
				{{ session('status') }}
			</div>
		@endif



		<?php
		if($user_role=='Staff'){
			?>
			<div class="row">
				<div class="col-xl-12">
						<!--begin::Widget 29-->
						<div class="m-widget29">
								<div class="m-widget_content">
									<h3 class="m-widget_content-title">Order Pending By Stages</h3>
									<div class="m-widget_content-items">
									<div class="m-widget_content-items">

									<div class="m-widget_content-item">
									<?php
									$data=AyraHelp::getOrderStuckStatusByStage(1);
									?>
									<span>{{$data->stage_name}}</span>
									<span>
									<span class="m-badge m-badge m-badge-bordered--primary">
									<a href="{{ route('getOrderList', ['ART_WORK_RECIEVED','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
									<a href="{{ route('getOrderList', ['ART_WORK_RECIEVED','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
									</span>
									</span>
									</div>

									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(2);
											//$qc_data_arr=AyraHelp::getPendingPurchaseStages();
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">

											<a href="#" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="#" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(3);
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['ART_WORK_REVIEW','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['ART_WORK_REVIEW','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(4);
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['CLIENT_ART_CONFIRM','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['CLIENT_ART_CONFIRM','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(5);
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['PRINT_SAMPLE','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['PRINT_SAMPLE','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(6);
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['SAMPLE_ARRROVAL','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['SAMPLE_ARRROVAL','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(7);
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['PURCHASE_LABEL_BOX','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['PURCHASE_LABEL_BOX','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(8);
											?>
											<span>{{$data->stage_name}}</span>

											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['PRODUCTION','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['PRODUCTION','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(9);
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['QC_CHECK','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['QC_CHECK','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(10);
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['SAMPLE_MADE_APPROVAL','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['SAMPLE_MADE_APPROVAL','red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(11);
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['PACKING_ORDER','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['PACKING_ORDER','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>
									<div class="m-widget_content-item">
											<?php
											$data=AyraHelp::getOrderStuckStatusByStage(12);
											?>
											<span>{{$data->stage_name}}</span>
											<span>
											<span class="m-badge m-badge m-badge-bordered--primary">
											<a href="{{ route('getOrderList', ['DISPATCH_ORDER','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
											<a href="{{ route('getOrderList', ['DISPATCH_ORDER','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
											</span>
											</span>
									</div>

										</div>

									</div>
									<div align="right">
											<span style="margin-bottom: -41px;" class="m-badge m-badge--warning m-badge--wide">Last Updated:{{  AyraHelp::LastUpdateAtStage()}}</span>

									</div>

							</div>
						</div>
					</div>
				</div>





							<hr>


							<!--begin::Portlet-->

					<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
									<div class="m-portlet__head">
										<div class="m-portlet__head-caption">
											<div class="m-portlet__head-title">
												<h3 class="m-portlet__head-text">
													Daily Report
												</h3>
											</div>
										</div>
									</div>
									<div class="m-portlet__body">
											<table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_KPILISTUser">
											<thead>
												<tr>
													<th>ID</th>

													<th>Status</th>
													<th>Actions</th>
												</tr>
											</thead>

										</table>

									</div>
								</div>

								<!--end::Portlet-->


			<?php
		}


		if($user_role=='Admin' || $user_role=='SalesUser'){
			?>
			<div class="row">
					<div class="col-xl-12">
							<!--begin::Widget 29-->
							<div class="m-widget29">
									<div class="m-widget_content">
										<h3 class="m-widget_content-title">Order Pending By Stages</h3>
										<div class="m-widget_content-items">
										<div class="m-widget_content-items">

										<div class="m-widget_content-item">
										<?php
										$data=AyraHelp::getOrderStuckStatusByStage(1);
										?>
										<span>{{optional($data)->stage_name}}</span>
										<span>
										<span class="m-badge m-badge m-badge-bordered--primary">
										<a href="{{ route('getOrderList', ['ART_WORK_RECIEVED','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{optional($data)->green_count}}</span></a>
										<a href="{{ route('getOrderList', ['ART_WORK_RECIEVED','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{optional($data)->red_count}}</span></a>
										</span>
										</span>
										</div>

										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(2);
												//$qc_data_arr=AyraHelp::getPendingPurchaseStages();
												?>
												<span>{{optional($data)->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">

												<a href="#" title="Count of Processing"><span class="m-badge m-badge--success">{{optional($data)->green_count}}</span></a>
												<a href="#" title="Count of Delayed"><span class="m-badge m-badge--danger">{{optional($data)->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(3);
												?>
												<span>{{optional($data)->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['ART_WORK_REVIEW','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{optional($data)->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['ART_WORK_REVIEW','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{optional($data)->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(4);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['CLIENT_ART_CONFIRM','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['CLIENT_ART_CONFIRM','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(5);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['PRINT_SAMPLE','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['PRINT_SAMPLE','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(6);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['SAMPLE_ARRROVAL','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['SAMPLE_ARRROVAL','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(7);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['PURCHASE_LABEL_BOX','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['PURCHASE_LABEL_BOX','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(8);
												?>
												<span>{{$data->stage_name}}</span>

												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['PRODUCTION','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['PRODUCTION','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(9);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['QC_CHECK','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['QC_CHECK','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(10);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['SAMPLE_MADE_APPROVAL','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['SAMPLE_MADE_APPROVAL','red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(11);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['PACKING_ORDER','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['PACKING_ORDER','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStage(12);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['DISPATCH_ORDER','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<a href="{{ route('getOrderList', ['DISPATCH_ORDER','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>
												</span>
												</span>
										</div>

											</div>

										</div>
										<div align="right">
												<span style="margin-bottom: -41px;" class="m-badge m-badge--warning m-badge--wide">Last Updated:{{  AyraHelp::LastUpdateAtStage()}}</span>

										</div>
									</div>



								</div>
								<hr>


								<!--end::Widget 29-->
								<!--begin::Widget 29-->
								<?php /*
							<div class="m-widget29">
									<div class="m-widget_content">
										<h3 class="m-widget_content-title">Yesterday order statge completed</h3>
										<div class="m-widget_content-items">
										<div class="m-widget_content-items">

										<div class="m-widget_content-item">
										<?php
										$data=AyraHelp::getOrderStuckStatusByStageYestarday(1);

										?>
										<span>{{$data->stage_name}}</span>
										<span>
										<span class="m-badge m-badge m-badge-bordered--primary">
										<a href="{{ route('getOrderList', ['ART_WORK_RECIEVED','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
										<!-- <a href="{{ route('getOrderList', ['ART_WORK_RECIEVED','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
										</span>
										</span>
										</div>

										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(2);
												//$qc_data_arr=AyraHelp::getPendingPurchaseStages();
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">

												<a href="#" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="#" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(3);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['ART_WORK_REVIEW','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['ART_WORK_REVIEW','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(4);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['CLIENT_ART_CONFIRM','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['CLIENT_ART_CONFIRM','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(5);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['PRINT_SAMPLE','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['PRINT_SAMPLE','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(6);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['SAMPLE_ARRROVAL','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['SAMPLE_ARRROVAL','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(7);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['PURCHASE_LABEL_BOX','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['PURCHASE_LABEL_BOX','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(8);
												?>
												<span>{{$data->stage_name}}</span>

												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['PRODUCTION','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['PRODUCTION','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(9);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['QC_CHECK','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['QC_CHECK','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(10);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['SAMPLE_MADE_APPROVAL','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['SAMPLE_MADE_APPROVAL','red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(11);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['PACKING_ORDER','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['PACKING_ORDER','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>
										<div class="m-widget_content-item">
												<?php
												$data=AyraHelp::getOrderStuckStatusByStageYestarday(12);
												?>
												<span>{{$data->stage_name}}</span>
												<span>
												<span class="m-badge m-badge m-badge-bordered--primary">
												<a href="{{ route('getOrderList', ['DISPATCH_ORDER','my_green']) }}" title="Count of Processing"><span class="m-badge m-badge--success">{{$data->green_count}}</span></a>
												<!-- <a href="{{ route('getOrderList', ['DISPATCH_ORDER','my_red']) }}" title="Count of Delayed"><span class="m-badge m-badge--danger">{{$data->red_count}}</span></a>													 -->
												</span>
												</span>
										</div>

											</div>

										</div>
										<div align="right">
												<span style="margin-bottom: -41px;" class="m-badge m-badge--warning m-badge--wide">Last Updated:{{  AyraHelp::LastUpdateAtStage()}}</span>

										</div>
									</div>



								</div>
								<hr>
								*/
								?>



								<!--end::Widget 29-->




								{{-- order value --}}
								<div id="perf_divOrderVale"></div>

								<?= Lava::render('ColumnChart', 'FinancesOrderValue', 'perf_divOrderVale') ?>

								{{-- order value --}}



									<!--begin::Portlet-->
									<hr>

					<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
									<div class="m-portlet__head">
										<div class="m-portlet__head-caption">
											<div class="m-portlet__head-title">
												<h3 class="m-portlet__head-text">
													Daily Report
												</h3>
											</div>
										</div>
									</div>
									<div class="m-portlet__body">
											<table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_KPILISTUser">
											<thead>
												<tr>
													<th>ID</th>

													<th>Status</th>
													<th>Actions</th>
												</tr>
											</thead>

										</table>

									</div>
								</div>

								<!--end::Portlet-->


					</div>

			</div>


			<?php
			if($user_role=='SalesUser'){
			?>
			    	<!--Begin::Section-->

					<!--begin::Portlet-->
					<hr>
					<div class="m-portlet m-portlet--mobile m-portlet--body-progress-">
									<div class="m-portlet__head">
										<div class="m-portlet__head-caption">
											<div class="m-portlet__head-title">
												<h3 class="m-portlet__head-text">
													Daily Report
												</h3>
											</div>
										</div>
									</div>
									<div class="m-portlet__body">
											<table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_KPILISTUser">
											<thead>
												<tr>
													<th>ID</th>

													<th>Status</th>
													<th>Actions</th>
												</tr>
											</thead>

										</table>

									</div>
								</div>

								<!--end::Portlet-->


		<div class="m-portlet">

				<div class="m-portlet__body  m-portlet__body--no-padding">
					<div class="row m-row--no-padding m-row--col-separator-xl">

							<div class="col-xl-12">

								<div id="perf_div"></div>

								<?= Lava::render('ColumnChart', 'Finances', 'perf_div') ?>

								</div>
								<hr>






					</div>
				</div>
			</div>

			<div class="m-portlet">

				<div class="m-portlet__body  m-portlet__body--no-padding">
					<div class="row m-row--no-padding m-row--col-separator-xl">

						<div class="col-xl-12">

							<div id="perf_div_sample"></div>

							<?= Lava::render('ColumnChart', 'SampleFeeback', 'perf_div_sample') ?>

							</div>






					</div>
				</div>
			</div>


	<!--begin:: Widgets/Daily Sales-->

							<div class="m-widget14">
								<div class="m-widget14__header m--margin-bottom-30">
									<h3 class="m-widget14__title">
										Last 30 days Sample Added Graph
									</h3>
									<span class="m-widget14__desc">
										Check out each collumn for more details
									</span>
								</div>
								<div class="m-widget14__chart" style="height:120px;">
									<canvas id="m_chart_daily_sales"></canvas>
								</div>
							</div>

			<!--End::Section-->
			<div class="row" style="display:none">
				<div class="col-xl-12">
					<div id="chart-div"></div>
					<?= Lava::render('PieChart', 'IMDB', 'chart-div') ?>
				</div>

			</div>


			<?php

		}


			?>


			<div class="row">
					<div class="col-xl-4">
						<!--begin::Preview-->
						<div class="m-demo" data-code-preview="true" data-code-html="true" data-code-js="false">
								<div class="m-demo__preview">
										<ul class="m-nav">
												<li class="m-nav__section m-nav__section--first">
													<span class="m-nav__section-text">Clients</span>
												</li>
												<li class="m-nav__item">
												<a href="{{ route('client.index')}}" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-users"></i>
																<span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">Total </span>
																		<span class="m-nav__link-badge">
																			<span class="m-badge m-badge--info m-badge--wide">{{ $client_arr_data['total']}}</span>
																		</span>
																	</span>
																</span>
															</a>
												</li>
												<li class="m-nav__item">
														<a href="{{ route('client.leads')}}" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-users"></i>
																<span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">LEAD</span>
																		<span class="m-nav__link-badge">
																			<span class="m-badge m-badge--secondary m-badge--wide">{{ $client_arr_data['lead']}}</span>
																		</span>
																	</span>
																</span>
															</a>
												</li>
												<li class="m-nav__item">
														<a href="" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-box"></i>
																<span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">SAMPLING</span>
																		<span class="m-nav__link-badge">
																			<span class="m-badge m-badge--secondary m-badge--wide">{{ $client_arr_data['sampling']}}</span>
																		</span>
																	</span>
																</span>
															</a>
												</li>

												<li class="m-nav__item">
														<a href="" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-users"></i>
																<span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">CUSTOMER</span>
																		<span class="m-nav__link-badge">
																			<span class="m-badge m-badge--secondary m-badge--wide">{{ $client_arr_data['customer']}}</span>
																		</span>
																	</span>
																</span>
															</a>
												</li>



											</ul>



								</div>

							</div>

							<!--end::Preview-->
							<!--begin::Widget 29-->
							<?php
	if($user_role=='Admin'){
		?>
			<div class="m-widget29">
					<div class="m-widget_content">
						<h3 class="m-widget_content-title">Samples pending for dispatch since</h3>
						<div class="m-widget_content-items">
							<div class="m-widget_content-item">
								<span>
										<span class="m-badge m-badge--warning m-badge--wide m-badge--rounded">3 Days</span>
								</span>
								<span class="m--font-accent">
									{{  $spcount=AyraHelp::samplePendingDispatch(3)}}
								</span>
							</div>
							<div class="m-widget_content-item">
								<span>
										<span class="m-badge m-badge--warning m-badge--wide m-badge--rounded">7 Days</span>
								</span>
								<span class="m--font-brand">
										{{  $spcount=AyraHelp::samplePendingDispatch(7)}}
								</span>
							</div>
							<div class="m-widget_content-item">
								<span>
										<span class="m-badge m-badge--warning m-badge--wide m-badge--rounded">15 Days</span>
								</span>
								<span>
										{{  $spcount=AyraHelp::samplePendingDispatch(15)}}
								</span>
							</div>
						</div>
					</div>


				</div>

				<!--end::Widget 29-->
		<?php
	}

	 ?>




					</div>

					<div class="col-xl-4">
						<!--begin::Preview-->
						<div class="m-demo" data-code-preview="true" data-code-html="true" data-code-js="false">
								<div class="m-demo__preview">
										<ul class="m-nav">
												<li class="m-nav__section m-nav__section--first">
													<span class="m-nav__section-text">Samples</span>
												</li>
												<li class="m-nav__item">
												<a href="{{ route('sample.index')}}" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-users"></i>
																<span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">Total </span>
																		<span class="m-nav__link-badge">
																			<span class="m-badge m-badge--info m-badge--wide">{{$sample_arr_data['total']}}</span>
																		</span>
																	</span>
																</span>
															</a>
												</li>
												<li class="m-nav__item">
														<a href="{{ route('sample.new')}}" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-users"></i>
																<span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">NEW</span>
																		<span class="m-nav__link-badge">
																			<span class="m-badge m-badge--secondary m-badge--wide">{{$sample_arr_data['new']}}</span>
																		</span>
																	</span>
																</span>
															</a>
												</li>
												<li class="m-nav__item">
														<a href="" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-users"></i>
																<span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">SENT</span>
																		<span class="m-nav__link-badge">
																			<span class="m-badge m-badge--secondary m-badge--wide">{{$sample_arr_data['sent']}}</span>
																		</span>
																	</span>
																</span>
															</a>
												</li>

												<li class="m-nav__item">
														<a href="" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-users"></i>
																<span class="m-nav__link-title">
																	<span class="m-nav__link-wrap">
																		<span class="m-nav__link-text">FEEDBACK</span>
																		<span class="m-nav__link-badge">
																			<span class="m-badge m-badge--secondary m-badge--wide">{{$sample_arr_data['feedback_addedon']}}</span>
																		</span>
																	</span>
																</span>
															</a>
												</li>
											</ul>
								</div>
							</div>

							<!--end::Preview-->






					</div>
					<div class="col-xl-4">

								<!--begin::Preview-->
								<div class="m-demo" data-code-preview="true" data-code-html="true" data-code-js="false">
										<div class="m-demo__preview">
												<ul class="m-nav">
														<li class="m-nav__section m-nav__section--first">
															<span class="m-nav__section-text">Follow Up</span>
														</li>
														<li class="m-nav__item">
																<a href="" class="m-nav__link">
																		<i class="m-nav__link-icon flaticon-users"></i>
																		<span class="m-nav__link-title">
																			<span class="m-nav__link-wrap">
																				<span class="m-nav__link-text">Total </span>
																				<span class="m-nav__link-badge">
																					<span class="m-badge m-badge--info m-badge--wide">{{ count($today_node)+count($without_sch)+count($yesterday_node)}} </span>
																				</span>
																			</span>
																		</span>
																	</a>
														</li>
														<li class="m-nav__item">
																<a href="{{ route('today.clientFollow')}}" class="m-nav__link">
																		<i class="m-nav__link-icon flaticon-users"></i>
																		<span class="m-nav__link-title">
																			<span class="m-nav__link-wrap">
																				<span class="m-nav__link-text">Today</span>
																				<span class="m-nav__link-badge">
																					<span class="m-badge m-badge--info m-badge--wide">{{ count($today_node)}}</span>
																				</span>
																			</span>
																		</span>
																	</a>
														</li>
														<li class="m-nav__item">
																<a href="{{ route('yestarday.clientFollow')}}" class="m-nav__link">
																		<i class="m-nav__link-icon flaticon-users"></i>
																		<span class="m-nav__link-title">
																			<span class="m-nav__link-wrap">
																				<span class="m-nav__link-text">Yestarday</span>
																				<span class="m-nav__link-badge">
																					<span class="m-badge m-badge--secondary m-badge--wide">{{ count($yesterday_node)}}</span>
																				</span>
																			</span>
																		</span>
																	</a>
														</li>
														<li class="m-nav__item">
																<a href="{{ route('delayed.clientFollow')}}" class="m-nav__link">
																		<i class="m-nav__link-icon flaticon-users"></i>
																		<span class="m-nav__link-title">
																			<span class="m-nav__link-wrap">
																				<span class="m-nav__link-text">Delayed</span>
																				<span class="m-nav__link-badge">
																					<span class="m-badge m-badge--secondary m-badge--wide">{{ count($without_sch)}}</span>
																				</span>
																			</span>
																		</span>
																	</a>
														</li>



													</ul>
										</div>
									</div>

									<!--end::Preview-->


					</div>
			</div>



			<?php
		}
			?>








		<!--End::Section-->
	</div>



<!--begin::Modal-->
<div class="modal fade" id="m_modal_dailyReportSubmit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Work Report</h5>



										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
									<!-- ajcode -->
									<!--begin::Form-->
									<form class="m-form m-form--state m-form--fit m-form--label-align-right" id="m_form_KPIDataSubmitReport">
										@csrf
										<div class="m-portlet__body">
											<div class="m-form__section m-form__section--first">

												<div class="form-group m-form__group row">



													<div class="col-lg-4">
													<label class="form-control-label">Date:</label>
													<div class="input-group date">

														<input type="text" name="kpi_date" class="form-control m-input" readonly  id="m_datepicker_3" />
														<div class="input-group-append">
															<span class="input-group-text">
																<i class="la la-calendar"></i>
															</span>
														</div>
													</div>

													</div>

													<div class="col-lg-8">
														<label class="form-control-label">*Major Goal for <b> {{Date('F')}} :</label>
														<input type="text" name="goal_for_month" class="form-control m-input" placeholder="" value="">
													</div>
												</div>
												<div class="form-group m-form__group row">
													<div class="col-lg-12">
														<label class="form-control-label">* Major Goal for <b> {{Date('j F ')}}</label>
														<input type="text" name="goal_for_today" class="form-control m-input" placeholder="" value="">
													</div>
												</div>

											</div>

											<div class="form-group m-form__group row">
											<?php
											   $user = auth()->user();
											   $userRoles = $user->getRoleNames();
											   $user_role = $userRoles[0];
											   if($user_role=='SalesUser'){
												 $where_role='SALES';
											   }
											  if($user_role=='Staff'){
												$where_role='STAFF';
											  }
											  if($user_role=='Admin'){
												$where_role='STAFF';
											  }
											  if($user_role=='CourierTrk'){
												$where_role='STAFF';
											  }

											$kpi_data=AyraHelp::getKPIBYRole($where_role);
											foreach ($kpi_data as $key => $row) {

												?>
												<div class="col-lg-4">
													<label>KPI:</label>
													<div class="m-input-icon m-input-icon--right">
														<input type="text" value="{{optional($row)->kpi_detail}}"  name="kpi_details[]" class="form-control m-input" placeholder="">

													</div>
													<span class="m-form__help"></span>
												</div>
												<div class="col-lg-4">
													<label class="">Achievement(Number only):</label>
													<div class="m-input-icon m-input-icon--right">
														<input type="text" class="form-control m-input" name="kpi_number[]"  placeholder="">

													</div>
													<span class="m-form__help"></span>
												</div>
												<div class="col-lg-4">
													<label class="">Hours Spend:</label>
													<div class="m-input-icon m-input-icon--right">
														<input type="text" class="form-control m-input" name="kpi_spendhour[]" placeholder="">

													</div>
													<span class="m-form__help"></span>
												</div>

												<?php
											}
											?>


											</div>

											<div class="form-group m-form__group row">

												<div class="col-lg-4">
													<label>Task Discrption:</label>
													<div class="m-input-icon m-input-icon--right">
														<input type="text" name="kpi_other_discption"  class="form-control m-input" placeholder="">

													</div>
													<span class="m-form__help"></span>
												</div>
												<div class="col-lg-4">
												<label class="">Achievement(Number only):</label>
													<div class="m-input-icon m-input-icon--right">
														<input type="text" name="kpi_other_acthmentNo" class="form-control m-input" placeholder="">

													</div>
													<span class="m-form__help"></span>
												</div>
												<div class="col-lg-4">
													<label class="">Hours Spend:</label>
													<div class="m-input-icon m-input-icon--right">
														<input type="text" name="kpi_other_spendHour"  class="form-control m-input" placeholder="">

													</div>
													<span class="m-form__help"></span>
												</div>



											</div>

											<div class="form-group m-form__group row m--margin-top-10">
												<label class="col-form-label col-lg-3 col-sm-12">Remarks (Optional)</label>
												<div class="col-lg-9 col-md-9 col-sm-12">
												  <textarea name="kpi_remarks" id="" cols="30" rows="2" class="form-control"></textarea>
												</div>
											</div>





										</div>
										<div class="m-portlet__foot m-portlet__foot--fit">
											<div class="m-form__actions m-form__actions">
												<div class="row">
													<div class="col-lg-12">
														<button type="submit" class="btn btn-accent">Submit Report</button>

													</div>
												</div>
											</div>
										</div>
									</form>

									<!--end::Form-->


									<!-- ajcode -->




							</div>
						</div>

						<!--end::Modal-->



<!-- work history -->
