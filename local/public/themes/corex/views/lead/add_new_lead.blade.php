

          <!-- main  -->
          <div class="m-content">

						<!-- datalist -->
						<div class="m-portlet m-portlet--mobile">
													<div class="m-portlet__head">
														<div class="m-portlet__head-caption">
															<div class="m-portlet__head-title">
																<h3 class="m-portlet__head-text">
																	Add New Lead
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
                          General</a>
											</li>

											<!-- <li class="nav-item">
												<a class="nav-link " data-toggle="tab" href="#m_tabs_3_3">
                          <i class="flaticon-users-1"></i>
                          Contacts
                        </a>
											</li> -->

										</ul>

                    <div class="tab-content">
			<div class="tab-pane active" id="m_tabs_3_1" role="tabpanel">

                        <!--begin::Portlet-->
              <div class="m-portlet">
                <div class="m-portlet__head">
                  <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                      <span class="m-portlet__head-icon">
                        <i class="flaticon-map-location"></i>
                      </span>
                      <h3 class="m-portlet__head-text">
                      Lead Information
                      </h3>
                    </div>
                  </div>
                  <div class="m-portlet__head-tools">

                  </div>
                </div>
                <div class="m-portlet__body">
                  <!-- form  -->
                  <!--begin::Form-->
                                    <form class="m-form m-form--state m-form--fit m-form--label-align-right" id="m_form_add_clientLead" data-redirect="{{route('getINDMartData')}}"  method="post" action="{{route('saveLeadData')}}">
                                      @csrf
                                      <div class="m-portlet__body">
                                        <div class="m-form__section m-form__section--first">
                                          <div class="form-group m-form__group row">
                                            <div class="col-lg-4 m-form__group-sub">
                                               <label class="form-control-label">* Source:</label>
                                               <select class="form-control m-input" id="exampleSelect1" name="source">
                                                @foreach (AyraHelp::getClientSource() as $source)
                                              	<option value="{{$source->id}}">{{$source->source_name}}</option>
                                                @endforeach
                      						   </select>

                                             </div>
                                            <div class="col-lg-4 m-form__group-sub">
                                              <label class="form-control-label">Company:</label>
                                              <input type="text" class="form-control m-input" name="company" placeholder="Enter Company ">
                                           </div>
                                           <div class="col-lg-4 m-form__group-sub">
                                               <label class="form-control-label"> Contact Person:</label>
                                               <input type="text" class="form-control m-input" name="contact_person" placeholder="Enter Contact Person" >
                                             </div>
                                           </div>
                                         </div>
                                         <!-- name email phone -->
                                         <div class="m-form__section m-form__section--first">
                                           <div class="form-group m-form__group row">
                                             
                                             <div class="col-lg-4 m-form__group-sub">
                                               <label class="form-control-label">*Mobile:</label>
                                               <input type="text" class="form-control m-input" name="phone" placeholder="Enter Mobile" >
                                             </div>
                                             <div class="col-lg-4 m-form__group-sub">
                                               <label class="form-control-label"> Email:</label>
                                               <input type="text" class="form-control m-input" name="email" placeholder="Enter Email">
                                             </div>
                                             <div class="col-lg-4 m-form__group-sub">
                                               <label class="form-control-label">* Product:</label>
                                                 <input type="text" class="form-control m-input" name="product_name" placeholder="Enter Product Name" >
                                             </div>
                                           </div>
                                         </div>
                                         <!-- name email phone -->
                                         <!-- <address location source>

                                         </address> email phone -->
                                         <div class="m-form__section m-form__section--first">
                                           <div class="form-group m-form__group row">
                                             <div class="col-lg-4 m-form__group-sub">
                                               <label class="form-control-label"> Address:</label>
                                                 <input type="text" class="form-control m-input" name="address" placeholder="Enter Address" >
                                             </div>
                                             <div class="col-lg-4 m-form__group-sub">
                                               <label class="form-control-label">City:</label>
                                               <input type="text" class="form-control m-input" name="city" placeholder="Enter Location" >
                                             </div>
                                             <div class="col-lg-4 m-form__group-sub">
                                               <label class="form-control-label">State:</label>
                                               <input type="text" class="form-control m-input" name="state" placeholder="Enter Location" >
                                             </div>                                            
                                           </div>
                                         </div>
                                      <!-- <address location source-->
                                      <!-- website and remarks -->
                                      <div class="m-form__section m-form__section--first">
                                        <div class="form-group m-form__group row">
                                          <div class="col-lg-4 m-form__group-sub">
                                            <label class="form-control-label"> Website:</label>
                                              <input type="text" class="form-control m-input" name="website" placeholder="Enter Website" >
                                          </div>
                                          <div class="col-lg-8 m-form__group-sub">
                                            <label class="form-control-label">Remraks:</label>                                            
                                            <textarea name="remarks"   id="remarks" placeholder="Remarks" class="form-control" data-provide="markdown" rows="5"></textarea>
                                          </div>
                                         

                                        </div>
                                      </div>

                                      <!-- website and remarks -->



                                       </div>
                                       <div class="m-portlet__foot m-portlet__foot--fit">
                                         <div class="m-form__actions m-form__actions">
                                           <div class="row">
                                             <div class="col-lg-12">
                                               <button type="submit" data-wizard-action="submitLeadData" class="btn btn-primary">Save</button>
                                               <button type="reset" class="btn btn-secondary">Reset</button>
                                             </div>
                                           </div>
                                         </div>
                                       </div>
                                     </form>

                                     <!--end::Form-->

                   <!-- form  -->

                </div>
              </div>

              <!--end::Portlet-->










											             <!-- general -->

											</div>

											<div class="tab-pane" id="m_tabs_3_3" role="tabpanel">
										              under construction
											</div>

										</div>
                    <!-- end tab -->
                  </div>
                </div>


					</div>
          <!-- main  -->
