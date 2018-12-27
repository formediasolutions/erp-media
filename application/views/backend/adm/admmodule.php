<!-- begin::Body -->
<div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver-desktop m-grid--desktop m-container m-container--responsive m-container--xxl m-page__container m-body">
	<div class="m-grid__item m-grid__item--fluid m-wrapper">
		
		<div class="m-content">
        <!-- BEGIN: Subheader -->
		<div class="m-subheader ">
			<div class="d-flex align-items-center">
				<div class="mr-auto">
					<h3 class="m-subheader__title m-subheader__title--separator">
						Master Module
					</h3>
					<ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
						<li class="m-nav__item m-nav__item--home">
							<a href="<?php echo base_url('dashboard'); ?>" class="m-nav__link m-nav__link--icon">
								<i class="m-nav__link-icon la la-home"></i>
							</a>
						</li>
						<li class="m-nav__separator">
							-
						</li>
						<li class="m-nav__item">
							<a href="" class="m-nav__link">
								<span class="m-nav__link-text">
									Actions
								</span>
							</a>
						</li>
						<li class="m-nav__separator">
							-
						</li>
						<li class="m-nav__item">
							<a href="" class="m-nav__link">
								<span class="m-nav__link-text">
									Generate Reports
								</span>
							</a>
						</li>
					</ul>
				</div>
				<div>
					<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="hover" aria-expanded="true">
						<a href="#" class="m-portlet__nav-link btn btn-lg btn-secondary  m-btn m-btn--outline-2x m-btn--air m-btn--icon m-btn--icon-only m-btn--pill  m-dropdown__toggle">
							<i class="la la-plus m--hide"></i>
							<i class="la la-ellipsis-h"></i>
						</a>
						<div class="m-dropdown__wrapper">
							<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
							<div class="m-dropdown__inner">
								<div class="m-dropdown__body">
									<div class="m-dropdown__content">
										<ul class="m-nav">
											<li class="m-nav__section m-nav__section--first m--hide">
												<span class="m-nav__section-text">
													Quick Actions
												</span>
											</li>
											<li class="m-nav__item">
												<a href="" class="m-nav__link">
													<i class="m-nav__link-icon flaticon-share"></i>
													<span class="m-nav__link-text">
														Activity
													</span>
												</a>
											</li>
											<li class="m-nav__item">
												<a href="" class="m-nav__link">
													<i class="m-nav__link-icon flaticon-chat-1"></i>
													<span class="m-nav__link-text">
														Messages
													</span>
												</a>
											</li>
											<li class="m-nav__item">
												<a href="" class="m-nav__link">
													<i class="m-nav__link-icon flaticon-info"></i>
													<span class="m-nav__link-text">
														FAQ
													</span>
												</a>
											</li>
											<li class="m-nav__item">
												<a href="" class="m-nav__link">
													<i class="m-nav__link-icon flaticon-lifebuoy"></i>
													<span class="m-nav__link-text">
														Support
													</span>
												</a>
											</li>
											<li class="m-nav__separator m-nav__separator--fit"></li>
											<li class="m-nav__item">
												<a href="#" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm">
													Submit
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <br />
		<!-- END: Subheader -->
        <div class="m-portlet m-portlet--mobile">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text">
							Module List Data
							<small>
								data loaded from remote data source
							</small>
						</h3>
					</div>
				</div>
			</div>
			<div class="m-portlet__body">
				<div class="table-container table-responsive">
                    <div class="table-actions-wrapper">
						<select class="table-group-action-input form-control m-input form-control-sm" disabled="disabled">
							<option value="">Select...</option>
							<option value="confirm">Aktif</option>
							<option value="banned">Banned</option>
							<option value="delete">Hapus</option>
						</select>
						<button class="btn m-btn--pill m-btn--air btn-primary btn-sm table-group-action-submit" disabled="disabled">Proses</button>
      
                        <!--
                        <div class="btn-group">
                            <a class="btn btn-sm btn-warning dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                                Export
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:;" class="table-export-excel"> Export ke Excel </a></li>
                                <li><a href="javascript:;" class="table-export-pdf"> Export ke PDF </a></li>
                            </ul>
                        </div>
                        -->
					</div>
                    <table class="table table-striped table-bordered table-hover" id="module_list" data-url="<?php echo base_url('modulelistdata'); ?>">
                        <thead>
    						<tr role="row" class="heading bg-blue">
                                <th class="width5 text-center"><input name="select_all" id="select_all" value="1" type="checkbox" class="select_all filled-in chk-col-orange" /><label for="select_all"></label></th>
    							<th class="width5 text-center">No</th>
    							<th class="width20">Nama</th>
                                <th class="width15 text-center">Visible</th>
                                <th class="width15 text-center">Folder</th>
                                <th class="width10 text-center">Icon</th>
    							<th class="width15 text-center">Actions</th>
    						</tr>
                            <tr role="row" class="filter display-hide table-filter">
    							<td></td>
                                <td></td>
    							<td><input type="text" class="form-control form-control-sm m-input form-filter text-uppercase" name="search_name" /></td>
    							<td></td>
    							<td><input type="text" class="form-control form-control-sm m-input form-filter text-uppercase" name="search_folder" /></td>
                                <td></td>
    							<td style="text-align: center;">
    								<button class="btn m-btn--pill m-btn--air btn-primary btn-sm filter-submit" id="btn_list_module">Search</button>
                                    <button class="btn m-btn--pill m-btn--air btn-danger btn-sm filter-cancel">Reset</button>
    							</td>
    						</tr>
                        </thead>
                        <tbody>
                            <!-- Data Will Be Placed Here -->
                        </tbody>
                    </table>
                </div>
			</div>
		</div>
        </div>
	</div>
</div>
<!-- end::Body -->

