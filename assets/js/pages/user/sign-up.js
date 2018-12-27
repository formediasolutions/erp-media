var SignUp = function () {
    var handleSignUp = function() {
        $('#sign-up-form').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                user_type: {
                    required: true,
                },
                username: {
                    required: true,
                    minlength: 6,
                    usernamecheck: true,
                },
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    minlength: 6,
                    required: true,
                },
                password_confirm: {
                    minlength: 6,
                    required: true,
                    equalTo : function(element) {
                        if( $('#password').length ){
                            return "#password";
                        }
                    }
                },
                name: {
                    required: true,
                },
                gender: {
                    required: true,
                },
                workunit_type: {
                    required: true,
                },
            },
            messages: {
                user_type: {
                    required: 'Tipe pengguna harus di pilih',
                },
                username: {
                    required: 'Username harus di isi',
                    minlength: 'Minimal 6 karakter',
                    usernamecheck: 'Username tidak memenuhi kriteria',
                },
                email: {
                    required: 'Email harus di isi',
                    email: 'Masukkan alamat email Anda yang benar',
                },
                password: {
                    required: 'Password harus di isi',
                    minlength: 'Minimal 6 karakter',
                },
                password_confirm: {
                    required: 'Konfirmasi password harus di isi',
                    minlength: 'Minimal 6 karakter',
                    equalTo : "Konfirmasi password tidak sesuai dengan password yang diinputkan"
                },
                name: {
                    required: 'Nama harus di isi',
                },
                address: {
                    required: 'Alamat harus di isi',
                },
                gender: {
                    required: 'Jenis Kelamin harus di pilih',
                },
                workunit_type: {
                    required: 'Satuan Kerja harus di pilih',
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit   
                $('.alert-danger', $(this)).fadeIn().delay(3000).fadeOut();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.form-line').addClass('error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).closest('.form-line').removeClass('error');
            },
            success: function (label) {
                label.closest('.form-line').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                $(element).parents('.input-group').append(error);
            },
            submitHandler: function (form) {
                return processSignUp( form );
            }
        });
        
        var processSignUp = function( form ) {
        	var url = $( form ).attr( 'action' );
        	var data = $( form ).serialize(); // convert form to array
            var msg = $('.alert');
        	
            bootbox.confirm("Anda yakin data user sudah benar?", function(result) {
                if( result == true ){
                    $.ajax({
            			type : "POST",
            			url  : url,
            			data : data,
                        beforeSend: function(){
                            $("div.page-loader-wrapper").fadeIn();
                        },
            			success: function(response) {
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON( response );
                            
                            if(response.message == 'error'){
                                msg.html(response.data.msg);
                                msg.removeClass('alert-success').addClass('alert-danger').fadeIn('fast').delay(3000).fadeOut();
                            }else{
                                msg.html(response.data.msgsuccess);
                                msg.removeClass('alert-danger').addClass('alert-success').fadeIn('fast').delay(3000).fadeOut();
                                
                                $('#sign-up-form')[0].reset();
                                $('html, body').animate( { scrollTop: $('body').offset().top }, 500 );
                                $(".selectpicker, .show-tick").val('').selectpicker('render');
                                
                                grecaptcha.reset();
                            }
            			}
            		});
                }
            });
        };
        
        $('[name="user_type"]').change(function(){
            val = $(this).val();
            if( val != "" ){
                $(this).parent().parent().find('label').removeClass('error').hide();
            }
        });
        
        $.validator.addMethod("usernamecheck", function(value) {
           return /^[a-z][a-z0-9_.-]{4,19}$/i.test(value); // consists of only these
        });
        
        jQuery('#sign-up-btn').click(function () {
            jQuery('#login-form').hide();
            jQuery('#sign-up-form').fadeIn('slow');
            $('.form-line').removeClass('has-error');
            $('label.error').hide().empty();
            $('#sign-up-form')[0].reset();
            $('.alert-danger', $('#sign-up-form')).hide();
        });

        jQuery('#login-reg-btn').click(function () {
            jQuery('#login-form').fadeIn('slow');
            jQuery('#sign-up-form').hide();
            $('.form-line').removeClass('has-error');
            $('label.error').hide().empty();
            $('#login-form')[0].reset();
            $('.alert-danger', $('#login-form')).hide();
        });
        
        //Mobile Phone Number
        $('.mobile-phone-number').inputmask('+6299999999999', { placeholder: '+_____________' });
        
        //Datetimepicker plugin
        $('.birthdate').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            clearButton: true,
            weekStart: 1,
            time: false
        });
        
        //Selectpicker
        $('.show-tick').selectpicker();
    };
    
    // --------------------------------
    // Handle Profile Setting
    // --------------------------------
    var handleSaveAccount = function() {
        // Save Account
        $('#do_save_account').click(function(e){
            e.preventDefault();
            processSaveAccount($('#accountsetting'));
        });
        
        var processSaveAccount = function( form ) {
            var url     = form.attr( 'action' );
            var data    = new FormData(form[0]);
            var msg     = $('.alert');
        	
            $.ajax({
    			type : "POST",
    			url  : url,
    			data : data,
                
                cache : false,
                contentType : false,
                processData : false,
                beforeSend: function(){
                    $("div.page-loader-wrapper").fadeIn();
                    $('#save_account').modal('hide');
                },
    			success: function(response) {
                    $("div.page-loader-wrapper").fadeOut();
                    response = $.parseJSON( response );
                    
                    if(response.message == 'error'){
                        msg.html(response.data.msg);
                        msg.removeClass('alert-success').addClass('alert-danger').fadeIn('fast').delay(3000).fadeOut();
                    }else{
                        msg.html(response.data.msgsuccess);
                        msg.removeClass('alert-danger').addClass('alert-success').fadeIn('fast').delay(3000).fadeOut();
                        
                        $('#accountsetting')[0].reset();
                        $('html, body').animate( { scrollTop: $('body').offset().top + 550 }, 500 );
                        $('#ava_selection_files').fileinput('refresh', {
                            showUpload : false,
                            showUploadedThumbs : false,
                            'theme': 'explorer',
                            'uploadUrl': '#',
                            fileType: "any",
                            overwriteInitial: false,
                            initialPreviewAsData: true,
                            allowedFileExtensions: ['jpeg', 'jpg', 'png'],
                            fileActionSettings : {
                                showUpload: false,
                                showZoom: false,
                            },
                            maxFileSize: 1024,
                        });
                        window.location.reload();
                    }
    			}
    		});
        };
    };
    
    var handleSaveProfile = function() {
        // Save Profile
        $('#do_save_profile').click(function(e){
            e.preventDefault();
            processSaveProfile($('#personal'));
        });
        
        var processSaveProfile = function( form ) {
            var url     = $( form ).attr( 'action' );
            var data    = $( form ).serialize(); // convert form to array
            var wrapper = form;
        	
            $.ajax({
    			type : "POST",
    			url  : url,
    			data : data,
                beforeSend: function(){
                    $("div.page-loader-wrapper").fadeIn();
                    $('#save_profile').modal('hide');
                },
    			success: function(response) {
                    $("div.page-loader-wrapper").fadeOut();
                    response = $.parseJSON( response );
                    
                    if(response.message == 'error'){
                        App.alert({
                            type: 'danger',
                            icon: 'warning',
                            message: response.data,
                            container: wrapper,
                            place: 'prepend'
                        });
                    }else{
                        App.alert({
                            type: 'success',
                            icon: 'check',
                            message: response.data,
                            container: wrapper,
                            place: 'prepend'
                        });
                        
                        if( response.name != '' ){
                            if( $('#name_member').length ){
                                $('#name_member').empty().html(response.name).fadeIn(); 
                            }
                        }
                    }
                    App.scrollTo($('body'), 0);
    			}
    		});    
        };
        
        //Mobile Phone Number
        $('#up_phone').inputmask('+6299999999999', { placeholder: '+_____________' });
    };
    
    var handleSaveJob = function() {
        // Save Job
        $('#do_save_job').click(function(e){
            e.preventDefault();
            processSaveJob($('#jobupdate'));
        });
        
        var processSaveJob = function( form ) {
            var url         = $( form ).attr( 'action' );
            var data        = $( form ).serialize(); // convert form to array
            var wrapper     = form;
        	
            $.ajax({
    			type : "POST",
    			url  : url,
    			data : data,
                beforeSend: function(){
                    $("div.page-loader-wrapper").fadeIn();
                    $('#save_job').modal('hide');
                },
    			success: function(response) {
                    $("div.page-loader-wrapper").fadeOut();
                    response = $.parseJSON( response );
                    
                    if(response.message == 'error'){
                        App.alert({
                            type: 'danger',
                            icon: 'warning',
                            message: response.data,
                            container: wrapper,
                            place: 'prepend'
                        });
                    }else{
                        App.alert({
                            type: 'success',
                            icon: 'check',
                            message: response.data,
                            container: wrapper,
                            place: 'prepend'
                        });
                    }
                    App.scrollTo($('body'), 0);
    			}
    		});
        };
        
        //NIP
        $('#up_nip').inputmask('999999999999999999', { placeholder: '__________________' });
    };
    
    var handleSaveChangePassword = function() {
        // Save Job
        $('#do_save_changepassword').click(function(e){
            e.preventDefault();
            processSaveChangePassword($('#changepassword'));
        });
        
        var processSaveChangePassword = function( form ) {
            var url         = $( form ).attr( 'action' );
            var data        = $( form ).serialize(); // convert form to array
            var wrapper     = form;
        	
            $.ajax({
    			type : "POST",
    			url  : url,
    			data : data,
                beforeSend: function(){
                    $("div.page-loader-wrapper").fadeIn();
                    $('#save_changepassword').modal('hide');
                },
    			success: function(response) {
                    $("div.page-loader-wrapper").fadeOut();
                    response = $.parseJSON( response );
                    
                    if(response.message == 'error'){
                        if( response.login == 'login' ){
                            $(location).attr('href',response.data);
                        }else{
                            App.alert({
                                type: 'danger',
                                icon: 'warning',
                                message: response.data,
                                container: wrapper,
                                place: 'prepend'
                            });
                        }
                    }else{
                        $(location).attr('href',response.data);
                    }
    			}
    		});
        };
    };
    
    var handleSaveResetPassword = function() {
        // Save Job
        $('#do_reset_pass').click(function(e){
            e.preventDefault();
            processSaveResetPassword($('#changepassword'));
        });
        
        var processSaveResetPassword = function( form ) {
            var url         = $( form ).attr( 'action' );
            var data        = $( form ).serialize(); // convert form to array
            var wrapper     = form;
        	
            $.ajax({
    			type : "POST",
    			url  : url,
    			data : data,
                beforeSend: function(){
                    $("div.page-loader-wrapper").fadeIn();
                    $('#reset_pass').modal('hide');
                },
    			success: function(response) {
                    $("div.page-loader-wrapper").fadeOut();
                    response = $.parseJSON( response );
                    
                    if(response.message == 'error'){
                        if( response.login == 'login' ){
                            $(location).attr('href',response.data);
                        }else{
                            App.alert({
                                type: 'danger',
                                icon: 'warning',
                                message: response.data,
                                container: wrapper,
                                place: 'prepend',
                                closeInSeconds: 3
                            });
                        }
                    }else{
                        App.alert({
                            type: 'success',
                            icon: 'check',
                            message: response.data,
                            container: wrapper,
                            place: 'prepend',
                            closeInSeconds: 3
                        });
                    }
    			}
    		});
        };
    };
    
    // --------------------------------
    // Handle Province Change
    // --------------------------------
	var handleProvinceChange = function() {
        // Province Change
        $('#province-select').change(function(e){
            var val     = $(this).val();
            var url     = $(this).data('url');
            var el      = $('#regional-select');
            
            $.ajax({
                type:   "POST",
                data:   {
                    'province' : val,
                    'csrf_smit' : $('input[name="csrf_smit"]').val()
                },
                url:    url,
                beforeSend: function (){},
                success: function( response ){
                    response = $.parseJSON(response);
                    el.empty().hide();
                    
                    el.parent().removeClass('has-error');
                    el.parent().find('.help-block').empty().hide();
                    
                    if(response.message == 'success'){
                        el.attr('disabled', false);
                        el.html(response.data).selectpicker('refresh'); 
                    }else{
                        el.attr('disabled', true);
                        el.html(response.data).selectpicker('refresh'); 
                    }
                }
            });
            return false;
        });
	};
    
    // --------------------------------
    // Show Captcha
    // --------------------------------
    var showCaptcha = function() {
		if ( $( '.smit-captcha-box-signup' ).is( ':visible' ) )
			return grecaptcha.reset();
		
		$( '.smit-captcha-box-signup' ).show( 'fast', function(){
			// here we render the captcha
			var container = $( '.smit-captcha-signup' )[0];
			var parameters = {
				"sitekey": $( container ).data( 'smit-site-key' )
			};
			
			widgetCaptcha = grecaptcha.render(
				container,
				parameters
			);
		});
	};
    
    var showCaptchaAdmin = function() {
		// here we render the captcha
		var container = $( '.smit-captcha-signup-admin' )[0];
		var parameters = {
			"sitekey": $( container ).data( 'smit-site-key' )
		};
		
		widgetCaptcha = grecaptcha.render(
			container,
			parameters
		);
	};
    
    var showCaptchaUser = function() {
        // here we render the captcha
		var container = $( '.smit-captcha-signup-user' )[0];
		var parameters = {
			"sitekey": $( container ).data( 'smit-site-key' )
		};
		
		widgetCaptcha = grecaptcha.render(
			container,
			parameters
		);
	};
    
    var getCaptchaResponse = function() {
		return grecaptcha.getResponse( widgetCaptcha );
	};
    
    $('a.tab-profile').click(function(){
        var wrapper = $(this).attr('href');
        wrapper     = $(wrapper);
        
        $('label.error', wrapper).remove();
        $('div.form-line', wrapper).removeClass('error');
    });
    
    return {
        //main function to initiate the module
        init: function () {
            handleSignUp();
            handleProvinceChange();
            handleSaveJob();
            handleSaveProfile();
            handleSaveAccount();
            handleSaveChangePassword();
            handleSaveResetPassword();
        },
        loadCaptcha: function() {
			showCaptcha();
		},
        loadCaptchaAdmin: function() {
			showCaptchaAdmin();
		},
        loadCaptchaUser: function() {
			showCaptchaUser();
		}
    };
}();