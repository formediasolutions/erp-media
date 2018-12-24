<?php
    $topbar         = array();
    
    $session        = $_SESSION['member_logged_in'];
    // Check Module Member
    $moduledata     = get_module($session['id_adm_module']);
    // Check Group Menu
    $groupMenu      = get_groupmenu($session['id_adm_group']);
    
    $arrMenu = "";
    $arrSubMenu = array();
    foreach($groupMenu AS $rowGroupMenu){
        // Check Menu
        $menu      = get_mainmenu($rowGroupMenu->id_adm_menu, $session['id_adm_module']);
        if($menu->menu_level == 0){
            $arrMenu[$menu->id_adm_menu] = $menu;    
        }elseif($menu->menu_level == 1){
            $arrSubMenu[$menu->parent_id_adm_menu][$menu->id_adm_menu] = $menu;
        } 
    }
    
    /*
    $topbar[] = array (
        'title' => 'Beranda',
        'nav'   => 'dashboard',
        'menu_level'   => 0,
        'sequence' => 1,
        'link'  => 'dashboard',
        'icon'  => 'fa-home',
        'sub'   => false,
    );
    */
    
    $arrSub = array();
    foreach($arrMenu AS $row){
        if( !empty($arrSubMenu[$row->id_adm_menu]) ){
            foreach($arrSubMenu[$row->id_adm_menu] AS $rowSub){
                $arrSub[] = array (
                    'title' => $rowSub->name,
                    'nav'   => $row->name,
                    'menu_level'   => $rowSub->menu_level,
                    'sequence' => $rowSub->sequence_no,
                    'link'  => $rowSub->route,
                    'icon'  => $rowSub->icon_class,
                    'sub'   => false,
        	    );
            }
        }else{
            $arrSub = false;
        }
        
        $topbar[] = array (
            'title' => $row->name,
            'nav'   => $row->name,
            'menu_level'   => $row->menu_level,
            'sequence' => $row->sequence_no,
            'link'  => $row->route,
            'icon'  => $row->icon_class,
            'sub'   => $arrSub
	    );
        unset($arrSub);
    }
    
    $active_page    = ( $this->uri->segment(1, 0) ? $this->uri->segment(1, 0) : '');
    $active_sub     = ( $this->uri->segment(2, 0) ? $this->uri->segment(2, 0) : '');
?>

<div class="m-header__bottom">
	<div class="m-container m-container--responsive m-container--xxl m-container--full-height m-page__container">
		<div class="m-stack m-stack--ver m-stack--desktop">
			<!-- begin::Horizontal Menu -->
			<div class="m-stack__item m-stack__item--middle m-stack__item--fluid">
				<button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-light " id="m_aside_header_menu_mobile_close_btn">
					<i class="la la-close"></i>
				</button>
				<div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-dark m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-light m-aside-header-menu-mobile--submenu-skin-light "  >
					<ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
                        <?php if($active_page == 'dashboard') {
                            $active = 'active';
                        }?>
						<li class="m-menu__item  m-menu__item--<?php echo $active; ?>"  aria-haspopup="true">
							<a href="<?php echo base_url('dashboard'); ?>" class="m-menu__link ">
								<span class="m-menu__item-here"></span>
								<span class="m-menu__link-text">
									Dashboard
								</span>
							</a>
						</li>
                        <?php if($topbar) : ?>
                        <?php foreach($topbar as $nav): ?>
                        <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel <?php echo ($active_page == $nav['nav'] ? 'active' : ''); ?>"  m-menu-submenu-toggle="click" aria-haspopup="true">
							<a href="javascript:;" class="m-menu__link m-menu__toggle">
								<span class="m-menu__item-here"></span>
								<span class="m-menu__link-text">
									<?php echo $nav['title']; ?>
								</span>
                                <?php if( !empty($nav['sub']) ): ?>
								<i class="m-menu__hor-arrow la la-angle-down"></i>
                                <?php endif; ?>
								<i class="m-menu__ver-arrow la la-angle-right"></i>
							</a>
                            <?php if( !empty($nav['sub']) ): ?>
							<div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
								<span class="m-menu__arrow m-menu__arrow--adjust"></span>
								<ul class="m-menu__subnav">
                                    <?php foreach($nav['sub'] as $sub): ?>
									<li class="m-menu__item  m-menu__item--submenu <?php echo ($active_page == $sub['nav'] ? 'active' : ''); ?>"  m-menu-submenu-toggle="hover" m-menu-link-redirect="1" aria-haspopup="true">
										<a href="<?php echo base_url($sub['link']); ?>" class="m-menu__link">
											<i class="m-menu__link-icon <?php echo $sub['icon']; ?>"></i>
											<span class="m-menu__link-text">
												<?php echo $sub['title']; ?>
											</span>
											<?php if( !empty($sub['sub']) ): ?>
            								<i class="m-menu__hor-arrow la la-angle-down"></i>
                                            <?php endif; ?>
            								<i class="m-menu__ver-arrow la la-angle-right"></i>
										</a>
                                        <!--
                                        <?php if( !empty($sub['sub']) ): ?>
										<div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--right"><!--
											<span class="m-menu__arrow "></span>
											<ul class="m-menu__subnav">
                                                
                                                <?php foreach($sub['sub'] as $subTree): ?>
												<li class="m-menu__item "  m-menu-link-redirect="1" aria-haspopup="true">
													<a  href="<?php echo base_url($subTree['link']); ?>" class="m-menu__link ">
														<span class="m-menu__link-text">
															<?php echo $subTree['title']; ?>
														</span>
													</a>
												</li>
                                                <?php endforeach; ?>
											</ul>
										</div>
                                        <?php endif; ?>
                                        -->
									</li>
                                    <?php endforeach; ?>
								</ul>
							</div>
                            <?php endif; ?>
						</li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        
					</ul>
				</div>
			</div>
			<!-- end::Horizontal Menu -->
		</div>
	</div>
</div>