<?php
    // Check Module Member
    $session        = $_SESSION['member_logged_in'];
    $moduledata = get_module($session['id_adm_module']);
    $allModule  = get_module();
?>

<a class="dropdown-toggle m-dropdown__toggle btn btn-outline-metal m-btn  m-btn--icon m-btn--pill">
	<span>
		<?php echo $moduledata->name; ?>
	</span>
</a>
<div class="m-dropdown__wrapper">
	<span class="m-dropdown__arrow m-dropdown__arrow--left m-dropdown__arrow--adjust"></span>
	<div class="m-dropdown__inner">
		<div class="m-dropdown__body">
			<div class="m-dropdown__content">
				<ul class="m-nav">
					<li class="m-nav__section m-nav__section--first">
						<span class="m-nav__section-text">
							Module
						</span>
					</li>
                    <?php foreach($allModule AS $row){ ?>
                    <?php if( $is_admin && $row->id_adm_module == 1 ) : ?>
                    <li class="m-nav__item">
						<a href="<?php echo base_url("changeModule/".$row->id_adm_module); ?>" class="m-nav__link">
							<i class="m-nav__link-icon <?php echo $row->icon_class; ?>"></i>
							<span class="m-nav__link-text">
								<?php echo $row->name; ?>
							</span>
						</a>
					</li>
                    <li class="m-nav__separator m-nav__separator--fit"></li>
                    <?php continue; endif; ?>
					<li class="m-nav__item">
						<a href="<?php echo base_url("changeModule/".$row->id_adm_module); ?>" class="m-nav__link">
							<i class="m-nav__link-icon <?php echo $row->icon_class; ?>"></i>
							<span class="m-nav__link-text">
								<?php echo $row->name; ?>
							</span>
						</a>
					</li>
                    <?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>