$(function () {    
    // Put your sctipt here...
    selectDatePicker( $('input.datepicker'), 'YYYY-MM-DD' );
    selectDateTimePicker( $('input.newsedit_date') );
});

var selectDatePicker = function(el, formatdate='', el_end=''){
    //Datetimepicker plugin
    $(el).bootstrapMaterialDatePicker({
        format: formatdate!="" ? formatdate : 'YYYY-MM-DD H:mm',
        clearButton: true,
        weekStart: 1,
        year: false,
        time: false
    }).on('change', function(e, date){
        if( el_end!="" ){
            $(el_end).bootstrapMaterialDatePicker('setMinDate', date);
        }
        $(el).parent().removeClass('error');
        $(el).parent().parent().find('label').remove();
    });
}

var selectDateTimePicker = function(el, formatdate='', el_end=''){
    //Datetimepicker plugin
    $(el).bootstrapMaterialDatePicker({
        format: formatdate!="" ? formatdate : 'YYYY-MM-DD H:mm:ss',
        clearButton: true,
        weekStart: 1,
        year: true,
        time: true
    }).on('change', function(e, date){
        if( el_end!="" ){
            $(el_end).bootstrapMaterialDatePicker('setMinDate', date);
        }
        $(el).parent().removeClass('error');
        $(el).parent().parent().find('label').remove();
    });
}

var SelectGuide = function () {
    handleSelectGuide = function(){
        $('.def option').mousedown(function(e) {
            e.preventDefault();
            $(this).prop('selected', $(this).prop('selected') ? false : true);
        });     
    };

    return {
        //main function to initiate the module
        init: function () {
            handleSelectGuide();
        }
    };
}();

var PraIncubationSetting = function () {
    handlePraIncubationSettingAction = function(){
        // Close Pra-Incubation Setting
        $("body").delegate( "a.praincubsetclose", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#praincubation_setting_list').parents('.dataTables_wrapper');
            
            bootbox.confirm("Anda yakin akan menutup pengaturan Pra-Inkubasi ini?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response    = $.parseJSON(response);
                                    
                            if( response.message == 'redirect'){
                                $(location).attr('href',response.data);
                            }else if( response.message == 'error'){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.data, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.data, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                                $('#btn_praincubation_setting_list').trigger('click');
                            }
                        }
                    });
                }
            });
        });    
    };

    return {
        //main function to initiate the module
        init: function () {
            handlePraIncubationSettingAction();
        }
    };
}();

var IncubationSetting = function () {
    handleIncubationSettingAction = function(){
        // Close Pra-Incubation Setting
        $("body").delegate( "a.incubsetclose", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#incubation_setting_list').parents('.dataTables_wrapper');
            
            bootbox.confirm("Anda yakin akan menutup pengaturan Inkubasi ini?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response    = $.parseJSON(response);
                                    
                            if( response.message == 'redirect'){
                                $(location).attr('href',response.data);
                            }else if( response.message == 'error'){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.data, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.data, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                                $('#btn_incubation_setting_list').trigger('click');
                            }
                        }
                    });
                }
            });
        });    
    };

    return {
        //main function to initiate the module
        init: function () {
            handleIncubationSettingAction();
        }
    };
}();

var ScoreSetting = function () {
    handleScoreSetting = function(){
        // Details Score Incubation Setting
        $("body").delegate( "a.scoresetdet", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var step = $(this).data('step');

            var table_container = step == 1 ? $('#jury_stepone').parents('.dataTables_wrapper') : $('#jury_steptwo').parents('.dataTables_wrapper');
            var el = step == 1 ? $('#scoredata_details') : $('#scoredata_details2');

            $.ajax({
                type:   "POST",
                url:    url,
                beforeSend: function (){
                    $("div.page-loader-wrapper").fadeIn();
                },
                success: function( response ){                    
                    $("div.page-loader-wrapper").fadeOut();
                    response    = $.parseJSON(response);
                         
                    if( response.message == 'redirect'){
                        $(location).attr('href',response.data);
                    }else if( response.message == 'error'){
                        App.alert({
                            type: 'danger', 
                            icon: 'warning', 
                            message: response.data, 
                            container: table_container, 
                            place: 'prepend'
                        });
                    }else{
                        $('.profile-name').empty().html(response.details.user_name).show();
                        $('.profile-username').empty().html('<i class="material-icons">person</i> ' + response.details.username).show();
                        $('.profile-email').empty().html('<i class="material-icons">email</i> ' + response.details.email).show();
                        $('.profile-phone').empty().html('<i class="material-icons">phone</i> ' + response.details.phone).show();
                        
                        $('#reg_event_title, #reg_event_title2').val(response.details.event_title);
                        $('#reg_desc, #reg_desc2').val(response.details.event_desc);
                        $('#reg_name, #reg_name2').val(response.details.name);
                        $('#reg_category, #reg_category2').val(response.details.category);
                        
                        if( step == 1 ){
                            $('.status-examined').hide();
                            $('.status-called').hide();
                            
                            $('button.btn-examine, button.btn-call, button.btn-reject').attr('data-uniquecode',response.details.uniquecode);
                            $('button.btn-download-file').attr('data-uniquecode', response.details.uniquecode);
    
                            if(response.details.status == 0){
                                $('.selection-status').empty().html('<span class="label label-default">BELUM DIKONFIRMASI</span>').show();
                            }else if(response.details.status == 1){
                                $('.selection-status').empty().html('<span class="label label-success">DIKONFIRMASI</span>').show();
                                $('.status-examined').show();
                                $('button.btn-download-file').attr('disabled', 'disabled');
                            }else if(response.details.status == 2){
                                $('.selection-status').empty().html('<span class="label bg-brown">DIPERIKSA</span>').show();
                                $('.status-called').show();
                            }else if(response.details.status == 3){
                                $('.selection-status').empty().html('<span class="label label-warning">DIPANGGIL</span>').show();
                            }else if(response.details.status == 4){
                                $('.selection-status').empty().html('<span class="label bg-purple">DINILAI</span>').show();
                            }else if(response.details.status == 5){
                                $('.selection-status').empty().html('<span class="label label-primary">DITERIMA</span>').show();
                            }else if(response.details.status == 6){
                                $('.selection-status').empty().html('<span class="label label-danger">DITOLAK</span>').show();
                            }
                        }else{
                            if(response.details.status == 1){
                                $('.selection-status').empty().html('<span class="label label-success">DIKONFIRMASI</span>').show();
                            }
                        }

                        App.scrollTo(table_container,240);
                        el.fadeIn();
                    }
                }
            });
        });  
        
        $("body").delegate( "button.btn-reject", "click", function( event ) {
            event.preventDefault();
            var div_container   = $('#alert-display');
            var baseurl         = $(this).data('baseurl');
            var uniquecode      = $(this).data('uniquecode');
            var url             = baseurl + '/' + uniquecode;

            bootbox.confirm("Anda yakin akan menolak seleksi user ini?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: div_container, 
                                    place: 'prepend',
                                    focus: false
                                });
                                App.scrollTo(div_container, -90);
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.message, 
                                    container: div_container, 
                                    place: 'prepend',
                                    focus: false
                                });
                                App.scrollTo($('#scoredata_details'), -90);
                                $('.selection-status').empty().html('<span class="label label-danger">DITOLAK</span>').show();
                                $('#btn_list_user').trigger('click');
                                $('.status-examined').hide();
                                $('.status-called').hide();
                            }
                            return false;
                        }
                    });
                }
            });
        });
        
        $("body").delegate( "button.btn-download-file", "click", function( event ) {
            event.preventDefault();
            var div_container   = $('#alert-display');
            var baseurl         = $(this).data('baseurl');
            var uniquecode      = $(this).data('uniquecode');
            var url             = baseurl + '/' + uniquecode;

            bootbox.confirm("Anda yakin akan mendownload file seleksi user ini?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: div_container, 
                                    place: 'prepend',
                                    focus: false
                                });
                                App.scrollTo(div_container, -90);
                            }else{
                                document.location.href =(response.message);
                                setTimeout(function () { URL.revokeObjectURL(response.message); }, 100);
                            }
                            return false;
                        }
                    });
                }
            });
        });
        
        // Close Details Incubation Setting
        $("body").delegate( "a.close-details, button.close-details", "click", function( event ) {
            event.preventDefault();
            var el = $('#scoredata_details');
            el.fadeOut();
            App.scrollTo($('body'),0);
        });
        
        $("body").delegate( "button#do_save_scoreuser", "click", function( event ) {
            event.preventDefault();
            var div_container   = $('#alert-display');
            var url             = $('#selection_score_step1').attr('action');
            
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            
            $.ajax({
                type:   "POST",
                url:    url,
                data:   $('#selection_score_step1').serialize(),
                beforeSend: function (){
                    $("div.page-loader-wrapper").fadeIn();
                },
                success: function( response ){                    
                    $("div.page-loader-wrapper").fadeOut();
                    response = $.parseJSON(response);
                    
                    if( response.message == 'redirect'){
                        $(location).attr('href',response.data);
                    }else if( response.message == 'error' ){
                        App.alert({
                            type: 'danger', 
                            icon: 'warning', 
                            message: response.data, 
                            container: div_container, 
                            place: 'prepend',
                            focus: false
                        });
                        App.scrollTo(div_container, -90);
                    }else{
                        App.alert({
                            type: 'success', 
                            icon: 'check', 
                            message: response.data, 
                            container: div_container, 
                            place: 'prepend',
                            focus: false
                        });
                        App.scrollTo(div_container, -90);
                        $('button.btn-rate-step1').hide();
                        $('button.btn-rate-step1-reset').hide();
                        $('#selection_score_step1 select.form-control').attr('disabled','disabled');
                        $('#nilai_juri_comment').attr('readonly','readonly');
                    }
                    return false;
                }
            });
                
        });
        
        $("body").delegate( "button#do_save_scoreuser_praincubation2", "click", function( event ) {
            event.preventDefault();
            var div_container   = $('#alert-display');
            var url             = $('#selection_score_step2').attr('action');
            
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            
            $.ajax({
                type:   "POST",
                url:    url,
                data:   $('#selection_score_step2').serialize(),
                beforeSend: function (){
                    $("div.page-loader-wrapper").fadeIn();
                },
                success: function( response ){                    
                    $("div.page-loader-wrapper").fadeOut();
                    response = $.parseJSON(response);
                    
                    if( response.message == 'redirect'){
                        $(location).attr('href',response.data);
                    }else if( response.message == 'error' ){
                        App.alert({
                            type: 'danger', 
                            icon: 'warning', 
                            message: response.data, 
                            container: div_container, 
                            place: 'prepend',
                            focus: false
                        });
                        App.scrollTo(div_container, -90);
                    }else{
                        App.alert({
                            type: 'success', 
                            icon: 'check', 
                            message: response.data, 
                            container: div_container, 
                            place: 'prepend',
                            focus: false
                        });
                        App.scrollTo(div_container, -90);
                        $('button.btn-rate-step2').hide();
                        $('button.btn-rate-step2-reset').hide();
                        $('#selection_score_step2 select.form-control').attr('disabled','disabled');
                        $('#nilai_juri_comment').attr('readonly','readonly');
                    }
                    return false;
                }
            });
                
        });
        
        $("body").delegate( "button#do_save_scoreuser_incubation", "click", function( event ) {
            event.preventDefault();
            var div_container   = $('#alert-display');
            var url             = $('#selectionincubation_score_step1').attr('action');
            
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            
            $.ajax({
                type:   "POST",
                url:    url,
                data:   $('#selectionincubation_score_step1').serialize(),
                beforeSend: function (){
                    $("div.page-loader-wrapper").fadeIn();
                },
                success: function( response ){                    
                    $("div.page-loader-wrapper").fadeOut();
                    response = $.parseJSON(response);
                    
                    if( response.message == 'redirect'){
                        $(location).attr('href',response.data);
                    }else if( response.message == 'error' ){
                        App.alert({
                            type: 'danger', 
                            icon: 'warning', 
                            message: response.data, 
                            container: div_container, 
                            place: 'prepend',
                            focus: false
                        });
                        App.scrollTo(div_container, -90);
                    }else{
                        App.alert({
                            type: 'success', 
                            icon: 'check', 
                            message: response.data, 
                            container: div_container, 
                            place: 'prepend',
                            focus: false
                        });
                        App.scrollTo(div_container, -90);
                        $('button.btn-rate-step1').hide();
                        $('button.btn-rate-step1-reset').hide();
                        $('#selectionincubation_score_step1 select.form-control').attr('disabled','disabled');
                        $('#nilai_juri_comment').attr('readonly','readonly');
                    }
                    return false;
                }
            });
                
        });
        
        $("body").delegate( "button#do_save_scoreuser_incubation2", "click", function( event ) {
            event.preventDefault();
            var div_container   = $('#alert-display');
            var url             = $('#selectionincubation_score_step2').attr('action');
            
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            
            $.ajax({
                type:   "POST",
                url:    url,
                data:   $('#selectionincubation_score_step2').serialize(),
                beforeSend: function (){
                    $("div.page-loader-wrapper").fadeIn();
                },
                success: function( response ){                    
                    $("div.page-loader-wrapper").fadeOut();
                    response = $.parseJSON(response);
                    
                    if( response.message == 'redirect'){
                        $(location).attr('href',response.data);
                    }else if( response.message == 'error' ){
                        App.alert({
                            type: 'danger', 
                            icon: 'warning', 
                            message: response.data, 
                            container: div_container, 
                            place: 'prepend',
                            focus: false
                        });
                        App.scrollTo(div_container, -90);
                    }else{
                        App.alert({
                            type: 'success', 
                            icon: 'check', 
                            message: response.data, 
                            container: div_container, 
                            place: 'prepend',
                            focus: false
                        });
                        App.scrollTo(div_container, -90);
                        $('button.btn-rate-step2').hide();
                        $('button.btn-rate-step2-reset').hide();
                        $('#selectionincubation_score_step2 select.form-control').attr('disabled','disabled');
                        $('#nilai_juri_comment').attr('readonly','readonly');
                    }
                    return false;
                }
            });
                
        });
        
        $("body").delegate( "button.btn-rate-step1-reset", "click", function( event ) {
            $('#selection_score_step1')[0].reset();
            $('#selection_score_step1 span.help-block').hide();
            $('#selection_score_step1 div.input-group').removeClass('error');
        });
        
        $("body").delegate( "button.btn-rate-step2-reset", "click", function( event ) {
            $('#selection_score_step2')[0].reset();
            $('#selection_score_step2 span.help-block').hide();
            $('#selection_score_step2 div.input-group').removeClass('error');
        });
        
        $("body").delegate( "button.btn-rate-step1-resetincubation", "click", function( event ) {
            $('#selectionincubation_score_step1')[0].reset();
            $('#selectionincubation_score_step1 span.help-block').hide();
            $('#selectionincubation_score_step1 div.input-group').removeClass('error');
        });
        
        $("body").delegate( "button.btn-rate-step2-resetincubation", "click", function( event ) {
            $('#selectionincubation_score_step2')[0].reset();
            $('#selectionincubation_score_step2 span.help-block').hide();
            $('#selectionincubation_score_step2 div.input-group').removeClass('error');
        });
        
        // Rate Step 1 Change
        $('.rate-step1').change(function( event ){
            event.preventDefault();

            var val         = $(this).val();
            var rate        = parseInt( $(this).data('rate') );

            var rate_total  = 0;
            var rate_el     = $('#nilai_total_tahap1');
            var rate_elval  = parseInt( rate_el.val() );
            
            if( val == 0 || val == "" ){
                var rate_plus = parseInt( $(this).data('plus') );
                if( rate_plus > 0 ){
                    rate_total  = rate_elval - rate_plus;
                }else{
                    rate_total  = rate_elval;
                }
                $(this).data('plus',0);
            }else{
                $(this).data('plus',rate);
                rate_total  = rate_elval + parseInt(val);
            }
            
            if( rate_total < 0 ){
                rate_total = 0;
            }
            
            rate_el.prop('value', rate_total);
            
            
        });
        
        /*
        $("body").delegate( "button.btn-rate-step2", "click", function( event ) {
            event.preventDefault();
            var div_container   = $('#alert-display');
            var url             = $('#selection_score_step2').attr('action');
            
            bootbox.confirm("Anda yakin akan memproses penilaian seleksi user ini?", function(result) {
                if( result == true ){
                    for (instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                    
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        data:   $('#selection_score_step2').serialize(),
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.message == 'redirect'){
                                $(location).attr('href',response.data);
                            }else if( response.message == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.data, 
                                    container: div_container, 
                                    place: 'prepend',
                                    focus: false
                                });
                                App.scrollTo(div_container, -90);
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.data, 
                                    container: div_container, 
                                    place: 'prepend',
                                    focus: false
                                });
                                App.scrollTo(div_container, -90);
                                $('button.btn-rate-step2').hide();
                                $('button.btn-rate-step2-reset').hide();
                            }
                            return false;
                        }
                    });
                }
            });
        });
        */
    };
    
    var handlePraIncubationStep1List = function(){
        // User Confirm
        $("body").delegate( "a.praincubationconfirmstep1", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#admin_stepone').parents('.dataTables_wrapper');
            var msg = '';
            
            bootbox.confirm("Anda yakin akan mengkonfirmasi semua data seleksi tahap 1?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }
                            $('#btn_admin_stepone').trigger('click');
                            $('#btn_resetadmin_stepone').trigger('click');
                            $('#btn_resetadmin_steptwo').trigger('click');
                        }
                    });
                }
            });
        });
        
        $("body").delegate( "a.praincubationconfirmstep2", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#admin_steptwo').parents('.dataTables_wrapper');
            var msg = '';
            
            bootbox.confirm("Anda yakin akan mengkonfirmasi semua data seleksi tahap 2?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }
                            $('#btn_admin_steptwo').trigger('click');
                            $('#btn_resetadmin_steptwo').trigger('click');
                        }
                    });
                }
            });
        });
        
        // User Confirm
        $("body").delegate( "a.btn_scorestep1", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#admin_stepone').parents('.dataTables_wrapper');
            var msg = '';
            
            bootbox.confirm("Anda yakin akan mengkonfirmasi semua data seleksi tahap 1?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }
                            $('#btn_admin_stepone').trigger('click');
                        }
                    });
                }
            });
        });
        
        $("body").delegate( "a.btn_scorestep2", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#admin_steptwo').parents('.dataTables_wrapper');
            var msg = '';
            
            bootbox.confirm("Anda yakin akan mengkonfirmasi semua data seleksi tahap 2?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }
                            $('#btn_admin_steptwo').trigger('click');
                        }
                    });
                }
            });
        });
    };

    var handleIncubationStepList = function(){
        // User Confirm
        $("body").delegate( "a.incubationconfirmstep1", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#admin_stepone').parents('.dataTables_wrapper');
            var msg = '';
            
            bootbox.confirm("Anda yakin akan mengkonfirmasi semua data seleksi tahap 1?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }
                            $('#btn_admin_stepone').trigger('click');
                            $('#btn_resetadmin_stepone').trigger('click');
                            $('#btn_resetadmin_steptwo').trigger('click');
                        }
                    });
                }
            });
        });
        
        $("body").delegate( "a.incubationconfirmstep2", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#admin_steptwo').parents('.dataTables_wrapper');
            var msg = '';
            
            bootbox.confirm("Anda yakin akan mengkonfirmasi semua data seleksi tahap 2?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }
                            $('#btn_admin_steptwo').trigger('click');
                            $('#btn_resetadmin_steptwo').trigger('click');
                        }
                    });
                }
            });
        });
        
        // User Confirm
        $("body").delegate( "a.btn_scorestep1", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#admin_stepone').parents('.dataTables_wrapper');
            var msg = '';
            
            bootbox.confirm("Anda yakin akan mengkonfirmasi semua data seleksi tahap 1?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }
                            $('#btn_admin_stepone').trigger('click');
                        }
                    });
                }
            });
        });
        
        $("body").delegate( "a.btn_scorestep2", "click", function( event ) {
            event.preventDefault();
            var url = $(this).attr('href');
            var table_container = $('#admin_steptwo').parents('.dataTables_wrapper');
            var msg = '';
            
            bootbox.confirm("Anda yakin akan mengkonfirmasi semua data seleksi tahap 2?", function(result) {
                if( result == true ){
                    $.ajax({
                        type:   "POST",
                        url:    url,
                        beforeSend: function (){
                            $("div.page-loader-wrapper").fadeIn();
                        },
                        success: function( response ){                    
                            $("div.page-loader-wrapper").fadeOut();
                            response = $.parseJSON(response);
                            
                            if( response.msg == 'error' ){
                                App.alert({
                                    type: 'danger', 
                                    icon: 'warning', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }else{
                                App.alert({
                                    type: 'success', 
                                    icon: 'check', 
                                    message: response.message, 
                                    container: table_container, 
                                    place: 'prepend'
                                });
                            }
                            $('#btn_admin_steptwo').trigger('click');
                        }
                    });
                }
            });
        });
    };
    
    countTotalRateStep1 = function( val ){
        if( !val ) return false;
        
        var total_rate = 0;
        var el = $('#nilai_total_tahap1');
        var elval = el.val();
        elval = parseInt(elval);
 
        total_rate = elval + val;
        if( total_rate < 0 ){
            total_rate = 0;
        }

        el.prop('value', total_rate);
    };

    return {
        // main function to initiate the module
        init: function () {
            handleScoreSetting();
            handlePraIncubationStep1List();
            handleIncubationStepList();
        }
    };
}();


var AnnouncementSetting = function () {
    handleAnnouncementDataAction = function(){
        
    };

    return {
        //main function to initiate the module
        init: function () {
            handleAnnouncementDataAction();
        }
    };
}();

var SliderIndikator = function () {
    handleSliderIndikator = function(){
        $(".slider-indikator").ionRangeSlider({
            grid: true,
            min: 0,
            max: 100,
            keyboard: true,
        });
        
        $(".slider-indikator").on("change",function(){
            var $this = $(this),
                value = $this.prop("value"),
                selector = $this.data("selector"),
                max = $("#" + selector + '_max').val(),
                el = $("#" + selector + '_rate');
                
            var sum = ( value * max ) / 100;
            el.prop('value',sum);
            
            countTotalRate();
        });
    };
    
    countTotalRate = function(){
        var el = $('#total_rate');
        var el2 = $('#total_rate2');
        var total_sum_rate_1 = 0;
        var total_sum_rate_2 = 0;
        var total_sum_rate_3 = 0;
        var total_sum_rate_4 = 0;
        var total_rate = 0;
        
        //klaster1
        var rate1_a = $("#klaster1_a_rate").prop('value');
        var rate1_b = $("#klaster1_b_rate").prop('value');
        var rate1_c = $("#klaster1_c_rate").prop('value');
        var rate1_d = $("#klaster1_d_rate").prop('value');
        var rate1_e = $("#klaster1_e_rate").prop('value');
        var sum_rate1_a = parseFloat(rate1_a);
        var sum_rate1_b = parseFloat(rate1_b);
        var sum_rate1_c = parseFloat(rate1_c);
        var sum_rate1_d = parseFloat(rate1_d);
        var sum_rate1_e = parseFloat(rate1_e);
        total_sum_rate_1    = (sum_rate1_a + sum_rate1_b + sum_rate1_c + sum_rate1_d + sum_rate1_e) * 25/100;
        total_sum_rate_1    = parseFloat(total_sum_rate_1);
        
        //klaster2
        var rate2_a = $("#klaster2_a_rate").prop('value');
        var rate2_b = $("#klaster2_b_rate").prop('value');
        var rate2_c = $("#klaster2_c_rate").prop('value');
        var rate2_d = $("#klaster2_d_rate").prop('value');
        var rate2_e = $("#klaster2_e_rate").prop('value');
        var sum_rate2_a = parseFloat(rate2_a);
        var sum_rate2_b = parseFloat(rate2_b);
        var sum_rate2_c = parseFloat(rate2_c);
        var sum_rate2_d = parseFloat(rate2_d);
        var sum_rate2_e = parseFloat(rate2_e);
        total_sum_rate_2    = (sum_rate2_a + sum_rate2_b + sum_rate2_c + sum_rate2_d + sum_rate2_e) * 40/100;
        total_sum_rate_2    = parseFloat(total_sum_rate_2);
        
        //klaster3
        var rate3_a = $("#klaster3_a_rate").prop('value');
        var rate3_b = $("#klaster3_b_rate").prop('value');
        var rate3_c = $("#klaster3_c_rate").prop('value');
        var rate3_d = $("#klaster3_d_rate").prop('value');
        var rate3_e = $("#klaster3_e_rate").prop('value');
        var sum_rate3_a = parseFloat(rate3_a);
        var sum_rate3_b = parseFloat(rate3_b);
        var sum_rate3_c = parseFloat(rate3_c);
        var sum_rate3_d = parseFloat(rate3_d);
        var sum_rate3_e = parseFloat(rate3_e);
        total_sum_rate_3    = (sum_rate3_a + sum_rate3_b + sum_rate3_c + sum_rate3_d + sum_rate3_e) * 15/100;
        total_sum_rate_3    = parseFloat(total_sum_rate_3);
        
        //klaster4
        var rate4_a = $("#klaster4_a_rate").prop('value');
        var rate4_b = $("#klaster4_b_rate").prop('value');
        var rate4_c = $("#klaster4_c_rate").prop('value');
        var rate4_d = $("#klaster4_d_rate").prop('value');
        var rate4_e = $("#klaster4_e_rate").prop('value');
        var sum_rate4_a = parseFloat(rate4_a);
        var sum_rate4_b = parseFloat(rate4_b);
        var sum_rate4_c = parseFloat(rate4_c);
        var sum_rate4_d = parseFloat(rate4_d);
        var sum_rate4_e = parseFloat(rate4_e);
        total_sum_rate_4    = (sum_rate4_a + sum_rate4_b + sum_rate4_c + sum_rate4_d + sum_rate4_e) * 20/100;
        total_sum_rate_4    = parseFloat(total_sum_rate_4);
        
        /*
        for (i=1; i<5; i++) { 
            var rate = $("#klaster" + i + "_a_rate").prop('value');
            sum_rate_a  += parseFloat(rate);
        }
        for (i=1; i<5; i++) { 
            var rate    = $("#klaster" + i + "_b_rate").prop('value');
            sum_rate_b  += parseFloat(rate);
        }
        for (i=1; i<5; i++) { 
            var rate    = $("#klaster" + i + "_c_rate").prop('value');
            sum_rate_c  += parseFloat(rate);
        }
        for (i=1; i<5; i++) { 
            var rate    = $("#klaster" + i + "_d_rate").prop('value');
            sum_rate_d  += parseFloat(rate);
        }
        
        for (i=1; i<5; i++) { 
            var rate    = $("#klaster" + i + "_e_rate").prop('value');
            sum_rate_e  += parseFloat(rate);
        }
        */
        
        total_rate = ( total_sum_rate_1 + total_sum_rate_2 + total_sum_rate_3 + total_sum_rate_4 );
        el.prop('value', total_rate.toFixed(2));
        el2.prop('value', total_rate.toFixed(2));
    };
    
    handleIRLCheck = function(){
        $("input.irl").click(function() {
            if ($(this).is(":checked")) {
                var group = "input:checkbox[name='" + $(this).attr("name") + "']";
                $(group).prop("checked", false);
                $(this).prop("checked", true);
            } else {
                $(this).prop("checked", false);
            }
        });
    }
    
    return {
        //main function to initiate the module
        init: function () {
            handleSliderIndikator();
            handleIRLCheck();
        }
    };
}();

