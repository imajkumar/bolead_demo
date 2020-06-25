<!-- main  -->
<div class="m-content">

    <!-- datalist -->
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Login Activity of
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="{{route('home')}}" class="btn btn-secondary m-btn m-btn--custom m-btn--icon">
                            <span>
                                <i class="la la-arrow-left"></i>
                                <span>Home </span>
                            </span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <!-- start  -->
            <!--begin: Datatable -->
            <table class="table table-striped- table-bordered table-hover table-checkable" >
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Users</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php
                 $lead_data=AyraHelp::getLeadDistribution();
                 $i=0;
                 foreach ($lead_data as $key => $row) {
                   // print_r($row);
                   // die;
                   $i++;
                   ?>
                   <tr>
                     <th scope="row">{{$i}}</th>
                   <th scope="row">
                   <a href="#" class="m-nav__link m-dropdown__toggle">
                     <span class="m-topbar__userpic">
                       <img src="{{$row['profilePic']}}" class="m--marginless"  width="30" alt="">
                     </span>
                     <span class="m-topbar__username m--hide"> </span>
                   </a>
                   <b>{{$row['sales_name']}}</b>
                     </th>
                     <th scope="row">
                       <a href="{{route('viewLoginActivityData',$row['uid'])}}" class="btn btn-primary btn-sm m-btn 	m-btn m-btn--icon">
															<span>
																<i class="fa fa-user"></i>
																<span>Login Activity</span>
															</span>
														</a>
														<a href="#" class="btn btn-success btn-sm m-btn 	m-btn m-btn--icon">
															<span>
																<i class="fa fa-archive"></i>
																<span>Lead Activity</span>
															</span>
														</a>

                     </th>
                 </tr>
                   <?php
                 }

              ?>
            </tbody>


            </table>
            <!-- end  -->
        </div>
    </div>


</div>
<!-- main  -->

<!--begin::Modal-->
						<div class="modal fade" id="m_modal_4LoginActivity" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Login Activity  Details</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body viewLoginActivity">

									</div>

								</div>
							</div>
						</div>

						<!--end::Modal-->
