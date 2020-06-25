<!-- main  -->
<div class="m-content">

    <!-- datalist -->
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Login Activity
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
            <table class="table table-striped- table-bordered table-hover table-checkable" id="m_table_loginActivity">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Name</th>
                  <th>First Active</th>
                  <th>Last Active</th>
                  <th>Session Hours</th>
                  <th>Actions</th>
                </tr>
              </thead>

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
