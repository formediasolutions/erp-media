var Login = function () {
    var loginFailed = 0;
	var useCaptcha = false;
	var widgetCaptcha;

	var handleLogin = function() {
        var showError = function(errorMsg) {
        	var errorValidate = $('div.error-validate');
        	errorValidate.find('span').html(errorMsg);
        	errorValidate.fadeIn().delay(3000).fadeOut();
       	};
       
		$('#login-form').validate({
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true,
                    minlength: 4,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
            },
            messages: {
                username: {
                    required: "Username harus di isi",
                    minlength: "Minimal harus 4 karakter"
                },
                password: {
                    required: "Password harus di isi",
                    minlength: "Minimal harus 6 karakter"
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit   
                $('.alert-danger', $(this)).fadeIn().delay(3000).fadeOut();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.form-line').addClass('error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).parents('.form-line').removeClass('error');
            },
            success: function (label) {
                label.closest('.form-line').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                $(element).parents('.input-group').append(error);
            },
            submitHandler: function (form) {
            	return validateLogin( form );
            }
        });
        
        var validateLogin = function( form ) {
        	var url = $( form ).attr( 'action' );
        	var data = $( form ).serialize(); // convert form to array
        	
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
                    if ( response.success )
                    	return $( location ).attr( 'href', response.msg );

                    if(response.msg == 'failed') { 
                    	loginFailed++;
                    	showError(response.message);
                    	if ( loginFailed >= 5 ) showCaptcha();
                    }else if(response.msg == 'error'){
                        showError(response.message);
                    }
    			}
    		});
        };
        
        $("body").delegate( ".close", "click", function( event ) {
            event.preventDefault();
            $('.alert').hide();
        });
	};
    
    var handleForgotPassword = function () {
		$('#forgot-password-form').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: {
                    required: "Email harus di isi."
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit   
                $('.alert-danger', $(this)).fadeIn().delay(3000).fadeOut();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.form-line').addClass('error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).parents('.form-line').removeClass('error');
            },
            success: function (label) {
                label.closest('.form-line').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                $(element).parents('.input-group').append(error);
            },
            submitHandler: function (form) {
                form.submit();
            }
        });

        jQuery('#forgot-btn').click(function () {
            jQuery('#login-form').hide();
            jQuery('#forgot-password-form').fadeIn('slow');
            $('.form-line').removeClass('has-error');
            $('label.error').hide().empty();
            $('#forgot-password-form')[0].reset();
            $('.alert-danger', $('#forgot-password-form')).hide();
        });

        jQuery('#login-btn').click(function () {
            jQuery('#login-form').fadeIn('slow');
            jQuery('#forgot-password-form').hide();
            $('.form-line').removeClass('has-error');
            $('label.error').hide().empty();
            $('#login-form')[0].reset();
            $('.alert-danger', $('#login-form')).hide();
        });
	};
    
    var showCaptcha = function() {
		useCaptcha = true;
		
		if ( $( '.smit-captcha-box' ).is( ':visible' ) )
			return grecaptcha.reset();
		
		$( '.smit-captcha-box' ).show( 'fast', function(){
			// here we render the captcha
			var container = $( '.smit-captcha' )[0];
			var parameters = {
				"sitekey": $( container ).data( 'smit-site-key' )
			};
			
			widgetCaptcha = grecaptcha.render(
				container,
				parameters
			);
		});
	};
    
    var getCaptchaResponse = function() {
		if ( ! useCaptcha ) return false;
		
		return grecaptcha.getResponse( widgetCaptcha );
	};
    
    return {
        //main function to initiate the module
        init: function () {
            handleLogin();
            handleForgotPassword();
        },
        
        loadCaptcha: function() {
			if ( $( '.smit-captcha-box' ).data( 'captcha' ) ) showCaptcha();
		}
    };
}();