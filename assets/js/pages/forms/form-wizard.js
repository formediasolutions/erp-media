var Wizard = function () {

    // Selection Incubation Wizard Validation
    var handleSelectionPraIncubationWizard = function(){

        var form = $('#selection_praincubation_wizard');
        form.steps({
            headerTag: 'h3',
            bodyTag: 'section',
            transitionEffect: 'slideLeft',
            onInit: function (event, currentIndex) {
                $.AdminBSB.input.activate();

                //Set tab width
                var $tab = $(event.currentTarget).find('ul[role="tablist"] li');
                var tabCount = $tab.length;
                $tab.css('width', (100 / tabCount) + '%');

                //set button waves effect
                setButtonWavesEffect(event);
            },
            onStepChanging: function (event, currentIndex, newIndex) {
                if (currentIndex > newIndex) { return true; }

                if (currentIndex < newIndex) {
                    form.find('.body:eq(' + newIndex + ') label.error').remove();
                    form.find('.body:eq(' + newIndex + ') .error').removeClass('error');
                }

                form.validate().settings.ignore = ':disabled,:hidden';
                return form.valid();
            },
            onStepChanged: function (event, currentIndex, priorIndex) {
                setButtonWavesEffect(event);

                if (currentIndex == 3) {
                    setSelecetionDet();
                }else{
                    clearSelecetionDet();
                }
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ':disabled';
                return form.valid();
            },
            onFinished: function (event, currentIndex) {
                $('#save_selectionpraincubationsetting').modal('show');
            }
        });
        formWizardValidate(form);

        //Datetimepicker plugin
        selectDatePicker( $('.selection_date_publication'),'',$('.selection_date_reg_start') );
        selectDatePicker( $('.selection_date_reg_start'),'',$('.selection_date_reg_end') );
        selectDatePicker( $('.selection_date_reg_end'),'',$('.selection_date_adm_start') );
        selectDatePicker( $('.selection_date_adm_start'),'',$('.selection_date_adm_end') );
        selectDatePicker( $('.selection_date_adm_end'),'',$('.selection_date_invitation_send') );
        selectDatePicker( $('.selection_date_invitation_send'),'',$('.selection_date_interview_start') );
        selectDatePicker( $('.selection_date_interview_start'),'',$('.selection_date_interview_end') );
        selectDatePicker( $('.selection_date_interview_end'),'',$('.selection_date_result') );
        selectDatePicker( $('.selection_date_result'),'',$('.selection_date_proposal_start') );
        selectDatePicker( $('.selection_date_proposal_start'),'',$('.selection_date_proposal_end') );
        selectDatePicker( $('.selection_date_proposal_end'),'',$('.selection_date_agreement') );
        selectDatePicker( $('.selection_date_agreement'),'',$('.selection_imp_date_start') );
        selectDatePicker( $('.selection_imp_date_start'),'',$('.selection_imp_date_end') );
        selectDatePicker( $('.selection_imp_date_end') );
    };

    // Selection Incubation Wizard Validation
    var handleSelectionIncubationWizard = function(){

        var form = $('#selection_incubation_wizard');
        form.steps({
            headerTag: 'h3',
            bodyTag: 'section',
            transitionEffect: 'slideLeft',
            onInit: function (event, currentIndex) {
                $.AdminBSB.input.activate();

                //Set tab width
                var $tab = $(event.currentTarget).find('ul[role="tablist"] li');
                var tabCount = $tab.length;
                $tab.css('width', (100 / tabCount) + '%');

                //set button waves effect
                setButtonWavesEffect(event);
            },
            onStepChanging: function (event, currentIndex, newIndex) {
                if (currentIndex > newIndex) { return true; }

                if (currentIndex < newIndex) {
                    form.find('.body:eq(' + newIndex + ') label.error').remove();
                    form.find('.body:eq(' + newIndex + ') .error').removeClass('error');
                }

                form.validate().settings.ignore = ':disabled,:hidden';
                return form.valid();
            },
            onStepChanged: function (event, currentIndex, priorIndex) {
                setButtonWavesEffect(event);

                if (currentIndex == 2) {
                    setSelecetionDetInc();
                }else{
                    clearSelecetionDetInc();
                }
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ':disabled';
                return form.valid();
            },
            onFinished: function (event, currentIndex) {
                $('#save_selectionincubationsetting').modal('show');
            }
        });
        formWizardValidateIncubation(form);

        //Datetimepicker plugin
        selectDatePicker( $('.selection_date_publication'),'',$('.selection_date_reg_start') );
        selectDatePicker( $('.selection_date_reg_start'),'',$('.selection_date_reg_end') );
        selectDatePicker( $('.selection_date_reg_end'),'',$('.selection_date_adm_start') );
        selectDatePicker( $('.selection_date_adm_start'),'',$('.selection_date_adm_end') );
        selectDatePicker( $('.selection_date_adm_end'),'',$('.selection_date_invitation_send') );
        selectDatePicker( $('.selection_date_invitation_send'),'',$('.selection_date_interview_start') );
        selectDatePicker( $('.selection_date_interview_start'),'',$('.selection_date_interview_end') );
        selectDatePicker( $('.selection_date_interview_end'),'',$('.selection_date_result') );
        selectDatePicker( $('.selection_date_result'),'',$('.selection_date_proposal_start') );
        selectDatePicker( $('.selection_date_proposal_start'),'',$('.selection_date_proposal_end') );
        selectDatePicker( $('.selection_date_proposal_end'),'',$('.selection_date_agreement') );
        selectDatePicker( $('.selection_date_agreement'),'',$('.selection_imp_date_start') );
        selectDatePicker( $('.selection_imp_date_start'),'',$('.selection_imp_date_end') );
        selectDatePicker( $('.selection_imp_date_end') );
    };
    // Details Selection Incubation Validation
    var handleDetailsSelectionIncubation = function(){
        var form = $('#details_selection_incubation');
        formWizardValidateIncubationDetails(form, 'details_selection_incubation');
    };

    // Details Selection Pra Incubation Validation
    var handleDetailsSelectionPraIncubation = function(){
        var form = $('#details_selection_praincubation');
        formWizardValidate(form, 'details_selection_praincubation');
    };

    // Detail Information Pra-Incubation
    var setSelecetionDet = function(){
        var selection_year_publication_val = $('#selection_year_publication').val();
        $('.selection_det_year_publication').empty().html( selection_year_publication_val ).show();

        var selection_det_date_publication_val = $('.selection_date_publication').val();
        selection_det_date_publication_val = moment(selection_det_date_publication_val).format('DD MMM YYYY');
        $('.selection_det_date_publication').empty().html( selection_det_date_publication_val ).show();

        var selection_det_reg_start_val = $('.selection_date_reg_start').val();
        var selection_det_reg_end_val = $('.selection_date_reg_end').val();
        var selection_det_reg = moment(selection_det_reg_start_val).format('DD MMM') + ' - ' + moment(selection_det_reg_end_val).format('DD MMM YYYY');
        $('.selection_det_reg').empty().html( selection_det_reg ).show();

        var selection_det_adm_start_val = $('.selection_date_adm_start').val();
        var selection_det_adm_end_val = $('.selection_date_adm_end').val();
        var selection_det_adm = moment(selection_det_adm_start_val).format('DD MMM') + ' - ' + moment(selection_det_adm_end_val).format('DD MMM YYYY');
        $('.selection_det_adm').empty().html( selection_det_adm ).show();

        var selection_det_invitation_send_val = $('.selection_date_invitation_send').val();
        selection_det_invitation_send_val = moment(selection_det_invitation_send_val).format('DD MMM YYYY');
        $('.selection_det_invitation_send').empty().html( selection_det_invitation_send_val ).show();

        var selection_det_interview_start_val = $('.selection_date_interview_start').val();
        var selection_det_interview_end_val = $('.selection_date_interview_end').val();
        var selection_det_interview = moment(selection_det_interview_start_val).format('DD MMM') + ' - ' + moment(selection_det_interview_end_val).format('DD MMM YYYY');
        $('.selection_det_interview').empty().html( selection_det_interview ).show();

        var selection_det_result_val = $('.selection_date_result').val();
        selection_det_result_val = moment(selection_det_result_val).format('DD MMM YYYY');
        $('.selection_det_result').empty().html( selection_det_result_val ).show();

        var selection_det_proposal_start_val = $('.selection_date_proposal_start').val();
        var selection_det_proposal_end_val = $('.selection_date_proposal_end').val();
        var selection_det_proposal = moment(selection_det_proposal_start_val).format('DD MMM') + ' - ' + moment(selection_det_proposal_end_val).format('DD MMM YYYY');
        $('.selection_det_proposal').empty().html( selection_det_proposal ).show();

        var selection_det_date_agreement_val = $('.selection_date_agreement').val();
        selection_det_date_agreement_val = moment(selection_det_date_agreement_val).format('DD MMM YYYY');
        $('.selection_det_date_agreement').empty().html( selection_det_date_agreement_val ).show();

        var selection_det_imp_date_start_val = $('.selection_imp_date_start').val();
        var selection_det_imp_date_end_val = $('.selection_imp_date_end').val();
        var selection_det_imp_date = moment(selection_det_imp_date_start_val).format('DD MMM') + ' - ' + moment(selection_det_imp_date_end_val).format('DD MMM YYYY');
        $('.selection_det_imp_date').empty().html( selection_det_imp_date ).show();

        $('.selection_det_desc').empty().html( $('.selection_desc').val() ).show();

        var selection_det_selection_files_val = $('#selection_files').val()
        var selection_det_selection_files_txt = '';
        $.each( selection_det_selection_files_val, function( index, value ){
            selection_det_selection_files_txt += "- " + $("#selection_files option[value='"+value+"']").text() + "<br />";
        });
        $('.selection_det_selection_files').empty().html( selection_det_selection_files_txt ).show();

        var selection_det_juri_phase1_val = $('#selection_juri_phase1').val();
        var selection_det_juri_phase1_txt = '';
        $.each( selection_det_juri_phase1_val, function( index, value ){
            selection_det_juri_phase1_txt += "- " + $("#selection_juri_phase1 option[value='"+value+"']").text() + "<br />";
        });
        $('.selection_det_juri_phase1').empty().html( selection_det_juri_phase1_txt ).show();

        var selection_det_juri_phase2_val = $('#selection_juri_phase2').val();
        var selection_det_juri_phase2_txt = '';
        $.each( selection_det_juri_phase2_val, function( index, value ){
            selection_det_juri_phase2_txt += "- " + $("#selection_juri_phase2 option[value='"+value+"']").text() + "<br />";
        });
        $('.selection_det_juri_phase2').empty().html( selection_det_juri_phase2_txt ).show();
    }

    var clearSelecetionDet = function(){
        $('.selection_det_year_publication').empty().html('-').show();
        $('.selection_det_date_publication').empty().html('-').show();
        $('.selection_det_reg').empty().html('-').show();
        $('.selection_det_adm').empty().html('-').show();
        $('.selection_det_invitation_send').empty().html('-').show();
        $('.selection_det_interview').empty().html('-').show();
        $('.selection_det_result').empty().html('-').show();
        $('.selection_det_proposal').empty().html('-').show();
        $('.selection_det_date_agreement').empty().html('-').show();
        $('.selection_det_imp_date').empty().html('-').show();
        $('.selection_det_desc').empty().html('-').show();
        $('.selection_det_selection_files').empty().html('-').show();
        $('.selection_det_juri_phase1').empty().html('-').show();
        $('.selection_det_juri_phase2').empty().html('-').show();
    }

    // Detail Information Incubation
    var setSelecetionDetInc = function(){
        var selection_year_publication_val = $('#selection_year_publication').val();
        $('.selection_det_year_publication').empty().html( selection_year_publication_val ).show();

        var selection_det_selection_files_val = $('#selection_files').val()
        var selection_det_selection_files_txt = '';
        $.each( selection_det_selection_files_val, function( index, value ){
            selection_det_selection_files_txt += "- " + $("#selection_files option[value='"+value+"']").text() + "<br />";
        });
        $('.selection_det_selection_files').empty().html( selection_det_selection_files_txt ).show();

        var selection_det_juri_phase1_val = $('#selection_juri_phase1').val();
        var selection_det_juri_phase1_txt = '';
        $.each( selection_det_juri_phase1_val, function( index, value ){
            selection_det_juri_phase1_txt += "- " + $("#selection_juri_phase1 option[value='"+value+"']").text() + "<br />";
        });
        $('.selection_det_juri_phase1').empty().html( selection_det_juri_phase1_txt ).show();

        var selection_det_juri_phase2_val = $('#selection_juri_phase2').val();
        var selection_det_juri_phase2_txt = '';
        $.each( selection_det_juri_phase2_val, function( index, value ){
            selection_det_juri_phase2_txt += "- " + $("#selection_juri_phase2 option[value='"+value+"']").text() + "<br />";
        });
        $('.selection_det_juri_phase2').empty().html( selection_det_juri_phase2_txt ).show();
    }

    var clearSelecetionDetInc = function(){
        $('.selection_det_year_publication').empty().html('-').show();
        $('.selection_det_selection_files').empty().html('-').show();
        $('.selection_det_juri_phase1').empty().html('-').show();
        $('.selection_det_juri_phase2').empty().html('-').show();
    }

    var selectDatePicker = function(el, formatdate='', el_end=''){
        //Datetimepicker plugin
        $(el).bootstrapMaterialDatePicker({
            format: formatdate!="" ? formatdate : 'YYYY-MM-DD H:mm',
            clearButton: true,
            weekStart: 1,
            year: false
        }).on('change', function(e, date){
            if( el_end!="" ){
                $(el_end).bootstrapMaterialDatePicker('setMinDate', date);
            }
            $(el).parent().removeClass('error');
            $(el).parent().parent().find('label').remove();
        });
    }

    var formWizardValidate = function(form, id=''){
        form.validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                selection_year_publication: {
                    required: true,
                },
                selection_date_publication: {
                    required: true,
                },
                selection_date_reg_start: {
                    required: true,
                },
                selection_date_reg_end: {
                    required: true,
                },
                selection_date_adm_start: {
                    required: true,
                },
                selection_date_adm_end: {
                    required: true,
                },
                selection_date_invitation_send: {
                    required: true,
                },
                selection_date_interview_start: {
                    required: true,
                },
                selection_date_interview_end: {
                    required: true,
                },
                selection_date_result: {
                    required: true,
                },
                selection_date_proposal_start: {
                    required: true,
                },
                selection_date_proposal_end: {
                    required: true,
                },
                selection_date_agreement: {
                    required: true,
                },
                selection_imp_date_start: {
                    required: true,
                },
                selection_imp_date_end: {
                    required: true,
                },
                'selection_files[]': {
                    required: true,
                },
                'selection_juri_phase1[]': {
                    required: true,
                },
                'selection_juri_phase2[]': {
                    required: true,
                }
            },
            messages: {
                selection_year_publication: {
                    required: "Tahun Publikasi harus di isi",
                },
                selection_date_publication: {
                    required: "Tanggal Publikasi harus di isi",
                },
                selection_date_reg_start: {
                    required: "Tanggal mulai pendaftaran online harus di isi",
                },
                selection_date_reg_end: {
                    required: "Tanggal selesai pendaftaran online harus di isi",
                },
                selection_date_adm_start: {
                    required: "Tanggal mulai seleksi administrasi harus di isi",
                },
                selection_date_adm_end: {
                    required: "Tanggal selesai seleksi administrasi harus di isi",
                },
                selection_date_invitation_send: {
                    required: "Tanggal undangan presentasi harus di isi",
                },
                selection_date_interview_start: {
                    required: "Tanggal mulai seleksi presentasi harus di isi",
                },
                selection_date_interview_end: {
                    required: "Tanggal selesai seleksi presentasi harus di isi",
                },
                selection_date_result: {
                    required: "Tanggal pengumuman hasil seleksi harus di isi",
                },
                selection_date_proposal_start: {
                    required: "Tanggal mulai perbaikan proposal harus di isi",
                },
                selection_date_proposal_end: {
                    required: "Tanggal selesai perbaikan proposal harus di isi",
                },
                selection_date_agreement: {
                    required: "Tanggal penetapan &amp; penandatanganan perjanjian harus di isi",
                },
                selection_imp_date_start: {
                    required: "Tanggal mulai pelaksanaan kegiatan harus di isi",
                },
                selection_imp_date_end: {
                    required: "Tanggal selesai pelaksanaan kegiatan harus di isi",
                },
                'selection_files[]': {
                    required: "Silahkan pilih berkas panduan",
                },
                'selection_juri_phase1[]': {
                    required: "Silahkan pilih juri tahap 1",
                },
                'selection_juri_phase2[]': {
                    required: "Silahkan pilih juri tahap 2",
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.form-line').addClass('error'); // set error class to the control group
                App.scrollTo( $('#details_selection_praincubation') );
            },
            unhighlight: function (element) {
                $(element).parents('.form-line').removeClass('error');
            },
            success: function (label) {
                label.closest('.form-line').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                $(element).parents('.form-group').append(error);
            },
            submitHandler: function (form) {
                if( id == 'details_selection_praincubation' ){
                    $('#save_selectionpraincubationsettingdetails').modal('show');
                }else{
                    return false;
                }
            }
        });
    };
    
    var formWizardValidateIncubationDetails = function(form, id=''){
        form.validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                selection_year_publication: {
                    required: true,
                },
                selection_date_agreement: {
                    required: true,
                },
                'selection_files[]': {
                    required: true,
                },
                'selection_juri_phase1[]': {
                    required: true,
                },
                'selection_juri_phase2[]': {
                    required: true,
                }
            },
            messages: {
                selection_year_publication: {
                    required: "Tahun Publikasi harus di isi",
                },
                selection_date_agreement: {
                    required: "Tanggal penetapan &amp; penandatanganan perjanjian harus di isi",
                },
                'selection_files[]': {
                    required: "Silahkan pilih berkas panduan",
                },
                'selection_juri_phase1[]': {
                    required: "Silahkan pilih juri tahap 1",
                },
                'selection_juri_phase2[]': {
                    required: "Silahkan pilih juri tahap 2",
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.form-line').addClass('error'); // set error class to the control group
                App.scrollTo( $('#details_selection_incubation') );
            },
            unhighlight: function (element) {
                $(element).parents('.form-line').removeClass('error');
            },
            success: function (label) {
                label.closest('.form-line').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                $(element).parents('.form-group').append(error);
            },
            submitHandler: function (form) {
                if( id == 'details_selection_incubation' ){
                    $('#save_selectionincubationsettingdetails').modal('show');
                }else{
                    return false;
                }
            }
        });
    };

    // Save Pra-Incubation Setting
    $('#do_save_selectionpraincubationsetting').click(function(e){
        e.preventDefault();
        processSaveSelectionPraIncubationSetting($('#selection_praincubation_wizard'));
    });

    // Update Pra-Incubation Setting
    $('#do_save_selectionpraincubationsettingdetails').click(function(e){
        e.preventDefault();
        processUpdateSelectionPraIncubationSetting($('#details_selection_praincubation'));
    });

    // Save Incubation Setting
    $('#do_save_selectionincubationsetting').click(function(e){
        e.preventDefault();
        processSaveSelectionIncubationSetting($('#selection_incubation_wizard'));
    });

    // Update Pra-Incubation Setting
    $('#do_save_selectionincubationsettingdetails').click(function(e){
        e.preventDefault();
        processUpdateSelectionIncubationSetting($('#details_selection_incubation'));
    });

    var processSaveSelectionIncubationSetting = function( form ) {
        var url     = form.attr( 'action' );
        var data    = new FormData(form[0]);
        var msg     = $('#alert');

        $.ajax({
			type : "POST",
			url  : url,
			data : data,
            cache : false,
            contentType : false,
            processData : false,
            beforeSend: function(){
                $("div.page-loader-wrapper").fadeIn();
            },
			success: function(response) {
                $("div.page-loader-wrapper").fadeOut();
                response = $.parseJSON( response );

                if(response.message == 'redirect'){
                    $(location).attr('href',response.data);
                }else if(response.message == 'error'){
                    msg.html(response.data);
                    msg.removeClass('alert-success').addClass('alert-danger').fadeIn('fast').delay(3000).fadeOut();
                }else{
                    msg.removeClass('alert-danger').addClass('alert-success').hide();
                    $('#selection_incubation_wizard').steps('reset');
                    $('#selection_incubation_wizard')[0].reset();
                    $('#selection_list_tab').trigger('click');
                }
			}
		});
    };

    var formWizardValidateIncubation = function(form, id=''){
        form.validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                'selection_files[]': {
                    required: true,
                },
                'selection_juri_phase1[]': {
                    required: true,
                },
                'selection_juri_phase2[]': {
                    required: true,
                }
            },
            messages: {
                'selection_files[]': {
                    required: "Silahkan pilih berkas panduan",
                },
                'selection_juri_phase1[]': {
                    required: "Silahkan pilih juri tahap 1",
                },
                'selection_juri_phase2[]': {
                    required: "Silahkan pilih juri tahap 2",
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.form-line').addClass('error'); // set error class to the control group
                App.scrollTo( $('#details_selection_incubation') );
            },
            unhighlight: function (element) {
                $(element).parents('.form-line').removeClass('error');
            },
            success: function (label) {
                label.closest('.form-line').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                $(element).parents('.form-group').append(error);
            },
            submitHandler: function (form) {
                if( id == 'details_selection_incubation' ){
                    $('#save_selectionincubationsetting').modal('show');
                }else{
                    return false;
                }
            }
        });
    };

    var processSaveSelectionPraIncubationSetting = function( form ) {
        var url     = form.attr( 'action' );
        var data    = new FormData(form[0]);
        var msg     = $('#alert');

        $.ajax({
			type : "POST",
			url  : url,
			data : data,
            cache : false,
            contentType : false,
            processData : false,
            beforeSend: function(){
                $("div.page-loader-wrapper").fadeIn();
            },
			success: function(response) {
                $("div.page-loader-wrapper").fadeOut();
                response = $.parseJSON( response );

                if(response.message == 'redirect'){
                    $(location).attr('href',response.data);
                }else if(response.message == 'error'){
                    msg.html(response.data);
                    msg.removeClass('alert-success').addClass('alert-danger').fadeIn('fast').delay(3000).fadeOut();
                }else{
                    msg.removeClass('alert-danger').addClass('alert-success').hide();
                    $('#selection_praincubation_wizard').steps('reset');
                    $('#selection_praincubation_wizard')[0].reset();
                    $('#selection_list_tab').trigger('click');
                }
			}
		});
    };

    var processUpdateSelectionPraIncubationSetting = function( form ) {
        var url     = form.attr( 'action' );
        var data    = new FormData(form[0]);
        var msg     = $('#alert');

        $.ajax({
			type : "POST",
			url  : url,
			data : data,
            cache : false,
            contentType : false,
            processData : false,
            beforeSend: function(){
                $("div.page-loader-wrapper").fadeIn();
            },
			success: function(response) {
                $("div.page-loader-wrapper").fadeOut();
                response = $.parseJSON( response );
                $(location).attr('href',response.data);
			}
		});
    };

    var processUpdateSelectionIncubationSetting = function( form ) {
        var url     = form.attr( 'action' );
        var data    = new FormData(form[0]);
        var msg     = $('#alert');

        $.ajax({
			type : "POST",
			url  : url,
			data : data,
            cache : false,
            contentType : false,
            processData : false,
            beforeSend: function(){
                $("div.page-loader-wrapper").fadeIn();
            },
			success: function(response) {
                $("div.page-loader-wrapper").fadeOut();
                response = $.parseJSON( response );
                $(location).attr('href',response.data);
			}
		});
    };

    var setButtonWavesEffect = function(event) {
        $(event.currentTarget).find('[role="menu"] li a').removeClass('waves-effect');
        $(event.currentTarget).find('[role="menu"] li:not(.disabled) a').addClass('waves-effect');
    };

    // Selection Setting Tabs
    $('#selection_list_tab').click(function(e){
        e.preventDefault();
        $('#btn_praincubation_setting_list').trigger('click');
        $('#btn_incubation_setting_list').trigger('click');

    });

    return {
        //main function to initiate the module
        init: function () {
            handleSelectionPraIncubationWizard();
            handleSelectionIncubationWizard();
            handleDetailsSelectionPraIncubation();
            handleDetailsSelectionIncubation();
        }
    };
}();
