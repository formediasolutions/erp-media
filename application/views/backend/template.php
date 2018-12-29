<!DOCTYPE html>
<html lang="en" >
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>
			<?php echo $title; ?>
		</title>
		<meta name="description" content="Latest updates and statistic charts">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!--begin::Web font -->
		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
        
		<!-- <script src="<?php echo JS_PATH; ?>webfont/webfont.js"></script> -->
		<script>
          WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
          });
		</script>
		<!--end::Web font -->
        
        <script type='text/javascript'>
		    var titleData = '<?php echo $title; ?>';
			msg = " " + titleData;
			pos = 0;
			
			function scrollMSG() {
				document.title = msg.substring(pos, msg.length) + msg.substring(0, pos); pos++;
				if (pos > msg.length) pos = 0
				window.setTimeout("scrollMSG()",150);
			}
			scrollMSG();
		</script>
        
        <!-- Shortcut Icon ================================================ -->
        <link rel="shortcut icon" href="<?php echo IMG_PATH; ?>logo/favicon.ico" />
        
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

			
		
        <!--begin::Base Styles -->  
        <!--begin::Page Vendors -->
		<link href="<?php echo VENDORS_PATH; ?>custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Page Vendors -->
		<link href="<?php echo VENDORS_PATH; ?>base/vendors.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo ASSETS_PATH; ?>demo/demo5/base/style.bundle.css" rel="stylesheet" type="text/css" />
        
        <!-- Additional/Plugins CSS -->
        <link href="<?php echo PLUGIN_PATH; ?>sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        
        <?php echo $headstyles; ?>
		
		<!-- 
		<script src="../../lib/js/jquery.min.js" type="text/javascript"></script>
		<script src="../../lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
		<script src="../../lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
		<script src="../../lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
		
		
			<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
		-->
        <!-- Custom CSS -->
		<link href="<?php echo ASSETS_PATH; ?>css/custom.css" rel="stylesheet" type="text/css" />
	</head>
	<!-- end::Head -->
    
    <!-- end::Body -->
	<body  class="m-page--wide m-header--fixed m-header--fixed-mobile m-footer--push m-aside--offcanvas-default"  >
		<!-- begin:: Page -->
		<div class="m-grid m-grid--hor m-grid--root m-page">
			<!-- begin::HEADER -->
			<?php $this->load->view(VIEW_BACK . 'includes/header'); ?>
			<!-- end::HEADER -->
            		
            <!-- begin::CONTENT -->
			<?php $this->load->view(VIEW_BACK . $main_content); ?>
			<!-- end::CONTENT -->
			
            
            <!-- begin::FOOTER -->
			<?php $this->load->view(VIEW_BACK . 'includes/footer'); ?>
			<!-- end::FOOTER -->
		</div>
		<!-- end:: Page -->
        
		<!-- begin::QUICKSIDEBAR -->
		<?php $this->load->view(VIEW_BACK . 'includes/quicksidebar'); ?>
		<!-- end::QUICKSIDEBAR -->     
	    
		<!-- begin::Scroll Top -->
		<div id="m_scroll_top" class="m-scroll-top">
			<i class="la la-arrow-up"></i>
		</div>
		<!-- end::Scroll Top -->	
        <?php echo $scripts; ?>
        <!-- Custom Js -->
        <script src="<?php echo JS_PATH . 'custom.js'; ?>"></script>
        
        <!-- Init Js -->
        <?php 
            echo $scripts_init; 
            echo $scripts_add;
        ?>
	</body>
	<!-- end::Body -->
</html>
