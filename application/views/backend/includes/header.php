            <!-- begin::Header -->
			<header id="m_header" class="m-grid__item m-header "  m-minimize="minimize" m-minimize-offset="200" m-minimize-mobile-offset="200" >
				<div class="m-header__top">
					<div class="m-container m-container--responsive m-container--xxl m-container--full-height m-page__container">
						<div class="m-stack m-stack--ver m-stack--desktop">
							<!-- begin::Brand -->
							<div class="m-stack__item m-brand">
								<div class="m-stack m-stack--ver m-stack--general m-stack--inline">
                                    <!-- BEGIN LOGO -->
									<div class="m-stack__item m-stack__item--middle m-brand__logo">
										<a href="<?php echo base_url('dashboard');; ?>" class="m-brand__logo-wrapper">
											<img alt="" src="<?php echo IMG_PATH; ?>logo/logo.png"/>
										</a>
									</div>
                                    <!-- END LOGO -->
									<div class="m-stack__item m-stack__item--middle m-brand__tools">
										<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-left m-dropdown--align-push" m-dropdown-toggle="click" aria-expanded="true">
											<!-- BEGIN SIDEBAR -->
                                            <?php $this->load->view(VIEW_BACK . 'includes/topbarmodule'); ?>
                                            <!-- END SIDEBAR -->
										</div>
                                        
										<!-- BEGIN Responsive Header Menu Toggler-->
										<a id="m_aside_header_menu_mobile_toggle" href="javascript:;" class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block">
											<span></span>
										</a>
										<!-- END Responsive Header Menu Toggler-->
                                        
                                        <!-- BEGIN Topbar Toggler-->
										<a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
											<i class="flaticon-more"></i>
										</a>
										<!-- END Topbar Toggler-->
									</div>
								</div>
							</div>
							<!-- end::Brand -->		
				            
                            <!-- BEGIN RIGHT NAVIGATION -->
                            <?php $this->load->view(VIEW_BACK . 'includes/rightnavigation'); ?>
                            <!-- END RIGHT NAVIGATION -->
						</div>
					</div>
				</div>
                
                <!-- BEGIN SIDEBAR -->
                <?php $this->load->view(VIEW_BACK . 'includes/mainmenu'); ?>
                <!-- END SIDEBAR -->
				
			</header>
			<!-- end::Header -->