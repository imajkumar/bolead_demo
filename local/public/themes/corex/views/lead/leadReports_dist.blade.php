

          <!-- main  -->
          <div class="m-content">

						<!-- datalist -->
						<div class="m-portlet m-portlet--mobile">
													<div class="m-portlet__head">
														<div class="m-portlet__head-caption">
															<div class="m-portlet__head-title">
																<h3 class="m-portlet__head-text">
																	Leads Reports
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
                      <ul class="nav nav-pills" role="tablist">
											<li class="nav-item ">
												<a class="nav-link active" data-toggle="tab" href="#m_tabs_3_1">
                          <i class="la la-gear"></i>
                          Assign  Leads  </a>
											</li>

											<li class="nav-item">
												<a class="nav-link " data-toggle="tab" href="#m_tabs_3_3">
												<i class="flaticon-users-1"></i>
												Irrelevant Irrelevant
												</a>
											</li>
											<li class="nav-item">
												<a class="nav-link " data-toggle="tab" href="#m_tabs_3_4">
												<i class="flaticon-users-1"></i>
												Lead Action
												</a>
											</li>

										</ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="m_tabs_3_1" role="tabpanel">
                            <?php 
                            //echo "<pre>";
                             $lead_data=AyraHelp::getLeadDistribution();
                           //  print_r($lead_data);

                           
                            



                            ?>
                            <!--begin::Section-->
                            <!--begin::Portlet-->
								<div class="m-portlet">
									<div class="m-portlet__head">
										<div class="m-portlet__head-caption">
											<div class="m-portlet__head-title">
												<h3 class="m-portlet__head-text">
													Lead Distribution Table
												</h3>
											</div>
										</div>
									</div>

										<div class="m-section">
											<div class="m-section__content">
												<table class="table table-bordered m-table m-table--border-brand m-table--head-bg-brand">
													<thead>
                         
														<tr>
															<th>Name</th>
															<th>Assigned</th>
															<th>Qualified</th>
															<th>Sampling</th>
															<th>Client</th>
															<th>Repeat Client</th>
															<th>Lost</th>															
															<th>Total Lead</th>
															<th>Unqualified</th>
														</tr>
													</thead>
													<tbody>
						  <?php 
						  $aj1=0;
						  $aj2=0;
						  $aj3=0;
						  $aj4=0;
						  $aj5=0;
						  $aj6=0;
						  $aj7=0;
						  $unqli=0;

                          foreach ($lead_data as $key => $row) {
							  $aj1=intval($aj1)+intval($row['stage_1']);
							  $aj2=intval($aj2)+intval($row['stage_2']);
							  $aj3=intval($aj3)+intval($row['stage_3']);
							  $aj4=intval($aj4)+intval($row['stage_4']);
							  $aj5=intval($aj5)+intval($row['stage_5']);
							  $aj6=intval($aj6)+intval($row['stage_6']);
							  $unqli=intval($unqli)+intval($row['unqli']);
							  $aj7=intval($aj7)+intval($row['stage_totoal']);

                            ?>
                            	<tr>
															<th scope="row">
                              <a href="#" class="m-nav__link m-dropdown__toggle">
                                <span class="m-topbar__userpic">
                                  <img src="{{$row['profilePic']}}" class="m--marginless"  width="30" alt="">
                                </span>
                                <span class="m-topbar__username m--hide"> </span>
                              </a>
                              <b>{{$row['sales_name']}}</b>
                              </th>
															<td>{{$row['stage_1']}}</td>
															<td>{{$row['stage_2']}}</td>
															<td>{{$row['stage_3']}}</td>
                              <td>{{$row['stage_4']}}</td>
															<td>{{$row['stage_5']}}</td>
															<td>{{$row['stage_6']}}</td>
															
                              <td>
							  <span class="m-badge m-badge--warning m-badge--wide m-badge--rounded">
							  {{$row['stage_totoal']}}
							  
							  </span>

							  
							  </td>
							  <td>{{$row['unqli']}}</td>

														</tr>
														
                            <?php
                          }
                          ?>

													
                          
														<tr>
														<td>
														<b>TOTAL<b>
														</td>
														<td>
														<strong>{{$aj1}}</strong>
														</td>
														<td>
														<strong>{{$aj2}}</<strong>
														</td>
														<td>
														<strong>{{$aj3}}</<strong>
														</td>
														<td>
														<strong>{{$aj4}}</strong>
														</td>
														<td>
														<strong>{{$aj5}}</strong>
														</td>
														<td>
														<strong>{{$aj6}}</strong>
														</td>
														
														<td>
														<strong>{{$aj7}}</strong>
														</td>

														<td>
														<strong>{{$unqli}}</strong>
														</td>

														
														</tr>
														
													</tbody>
												</table>
												
											</div>
										</div>


										<!--end::Section-->


										
                </div>


                        </div>

                        <div class="tab-pane" id="m_tabs_3_3" role="tabpanel">
                                under construction f


								
                        </div>

						<div class="tab-pane " id="m_tabs_3_4" role="tabpanel">
						  		<!-- ajcode -->
								 					

								

									<!-- ajcode -->

						</div>

					</div>
                    <!-- end tab -->
                  </div>
                </div>


					</div>
          <!-- main  -->
