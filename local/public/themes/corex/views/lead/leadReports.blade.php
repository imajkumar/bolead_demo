

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

														<h4>Fresh Lead Graph</h4>	

														<!-- ajcode -->
														<div id="perf_div_1"></div>
															<?= Lava::render('ColumnChart', 'BOLEAD_G2', 'perf_div_1') ?>
														<!-- ajcode -->
														<hr>
														<h4> Irrevant Lead Graph</h4>

														<!-- ajcode -->
														<div id="perf_div_2"></div>
															<?= Lava::render('ColumnChart', 'BOLEAD_G3', 'perf_div_2') ?>
														<!-- ajcode -->

														<hr>
														<h4> Assigned Lead Graph</h4>

														<!-- ajcode -->
														<div id="perf_div_3"></div>
															<?= Lava::render('ColumnChart', 'BOLEAD_G4', 'perf_div_3') ?>
														<!-- ajcode -->

														<hr>

														<h4>Fresh &  Irrevant Lead Graph</h4>


														<!-- ajcode -->
														<div id="perf_div"></div>
															<?= Lava::render('ColumnChart', 'BOLEAD_G1', 'perf_div') ?>
														<!-- ajcode -->



                    
													</div>
													</div>


					</div>
          <!-- main  -->
