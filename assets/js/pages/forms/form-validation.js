$(function () {
    //Custom Validations ===============================================================================
    //Date
    $.validator.addMethod('customdate', function (value, element) {
        return value.match(/^\d\d\d\d?-\d\d?-\d\d$/);
    },
        'Please enter a date in the format YYYY-MM-DD.'
    );

    //Credit card
    $.validator.addMethod('creditcard', function (value, element) {
        return value.match(/^\d\d\d\d?-\d\d\d\d?-\d\d\d\d?-\d\d\d\d$/);
    },
        'Please enter a credit card in the format XXXX-XXXX-XXXX-XXXX.'
    );
    //==================================================================================================
});

// Additional Function
// =====================================================================================================
var GuidesValidation = function () {
    var handleGuideValidation = function(){
        $('#guide_files').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                guide_title: {
                    required: true,
                },
                guide_description: {
                    required: true,
                },
                guide_selection_files: {
                    required: true,
                },
            },
            messages: {
                guide_title: {
                    required: "Judul panduan harus di isi."
                },
                guide_description: {
                    required: "Deskripsi panduan harus di isi."
                },
                guide_selection_files: {
                    required: "Berkas harus diisi."
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
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
                $(element).parents('.form-group').append(error);
            },
            submitHandler: function (form) {
                //form.submit();
                $('#save_guides').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleGuideValidation();
        }
    };
}();

var ServicesValidation = function () {
    var handleCommunicationValidation = function(){
        $('#cmm_form').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                cmm_title: {
                    required: true,
                },
                cmm_description: {
                    required: true,
                },
                cmm_user_id: {
                    required: true,
                },
            },
            messages: {
                cmm_title: {
                    required: "Judul komunikasi dan bantuan harus di isi."
                },
                cmm_description: {
                    required: "Deskripsi komunikasi dan bantuan harus di isi."
                },
                cmm_user_id: {
                    required: "ID Pengguna harus di isi."
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
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
                $(element).parents('.form-group').append(error);
            },
            submitHandler: function (form) {
                $('#save_cmm').modal('show');
            }
        });
    };

    var handleCommunicationReplyValidation = function(){
        $('#cmmreply_form').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                cmm_description: {
                    required: true,
                },
            },
            messages: {
                cmm_description: {
                    required: "Deskripsi komunikasi dan bantuan harus di isi."
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
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
                $(element).parents('.form-group').append(error);
            },
            submitHandler: function (form) {
                $('#save_cmmreply').modal('show');
            }
        });
    };

    var handleIKMValidation = function(){
        $('#ikmadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_title: {
                    required: true,
                },
                reg_question: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: "Judul Pertanyaan harus di isi."
                },
                reg_question: {
                    required: "Pertanyaan harus di isi."
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
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
                $(element).parents('.form-group').append(error);
            },
            submitHandler: function (form) {
                $('#save_ikmadd').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleCommunicationValidation();
            handleIKMValidation();
            handleCommunicationReplyValidation();
        }
    };
}();

var ProfileValidation = function () {
    var handleProfileRole = function(){
        //Multi-select
        $('#user_role_select').multiSelect();
    };

    var handleProfileValidation = function(){
        $('#personal').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                up_name: {
                    required: true,
                },
                up_email: {
                    required: true,
                    email: true,
                },
                up_phone: {
                    required: true,
                },
                up_address: {
                    required: true,
                },
                up_province: {
                    required: true,
                },
                up_regional: {
                    required: true,
                },
                up_district: {
                    required: true,
                },
                up_gender: {
                    required: true,
                },
                up_birthplace: {
                    required: true,
                },
                up_birthdate: {
                    required: true,
                },
                up_religion: {
                    required: true,
                },
                up_marital_status: {
                    required: true,
                },
            },
            messages: {
                up_name: {
                    required: 'Nama harus di isi',
                },
                up_email: {
                    required: 'Email harus di isi',
                    email: 'Masukkan alamat email Anda yang benar',
                },
                up_phone: {
                    required: 'Nomor Telp/HP harus di isi',
                },
                up_address: {
                    required: 'Alamat harus di isi',
                },
                up_province: {
                    required: 'Provinsi harus di isi',
                },
                up_regional: {
                    required: 'Kota/Kabupaten harus di isi',
                },
                up_district: {
                    required: 'Kecamatan/Kelurahan harus di isi',
                },
                up_address: {
                    required: 'Alamat harus di isi',
                },
                up_gender: {
                    required: 'Jenis Kelamin harus di pilih',
                },
                up_birthplace: {
                    required: 'Tempat lahir harus di pilih',
                },
                up_birthdate: {
                    required: 'Tanggal lahir harus di pilih',
                },
                up_regional: {
                    required: 'Agama harus di pilih',
                },
                up_marital_status: {
                    required: 'Status pernikahan harus di pilih',
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
                $('#save_profile').modal('show');
            }
        });
    };

    var handleAccountValidation = function(){
        $('#accountsetting').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                ava_selection_files: {
                    required: true,
                },
            },
            messages: {
                ava_selection_files: {
                    required: "Berkas harus diisi."
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
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
                $(element).parents('.form-group').append(error);
            },
            submitHandler: function (form) {
                $('#save_account').modal('show');
            }
        });
    };

    var handleJobValidation = function(){
        $('#jobupdate').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                up_nip: {
                    required: true,
                },
                up_position: {
                    required: true,
                },
                workunit_type: {
                    required: true,
                },
            },
            messages: {
                up_nip: {
                    required: 'Nomor Induk Pegawai harus di isi',
                },
                up_position: {
                    required: 'Posisi anda harus di isi',
                },
                workunit_type: {
                    required: 'Satuan kerja anda harus di isi',
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
                $('#save_job').modal('show');
            }
        });
    };

    var handleChangePasswordValidation = function(){
        $('#changepassword').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                cur_pass: {
                    minlength: 6,
                    required: true,
                },
                new_pass: {
                    minlength: 6,
                    required: true,
                },
                cnew_pass: {
                    minlength: 6,
                    required: true,
                    equalTo : "#new_pass"
                },
            },
            messages: {
                cur_pass: {
                    minlength: "Minimal 6 karakter",
                    required: "Kata sandi lama anda harus diisi."
                },
                new_pass: {
                    minlength: "Minimal 6 karakter",
                    required: "Kata sandi baru harus diisi."
                },
                cnew_pass: {
                    minlength: "Minimal 6 karakter",
                    required: "Ulangi kata sandi baru harus diisi.",
                    equalTo: "Konfirmasi kata sandi harus sesuai dengan kata sandi"
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
                $('#save_changepassword').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleProfileValidation();
            handleAccountValidation();
            handleJobValidation();
            handleChangePasswordValidation();
            handleProfileRole();
        }
    };
}();

var AnnouncementValidation = function () {
    var handleAnnouncementValidation = function(){
        $('#announcementadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_title: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                reg_agree: {
                    required: true,
                },
            },
            messages: {
                reg_desc: {
                    required: 'Deskripsi Kegiatan harus di isi',
                },
                reg_title: {
                    required: 'Judul Kegiatan harus di isi',
                },
                reg_agree: {
                    required: 'Anda harus setuju atas pengisian formulir ini',
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
                $('#save_announcement').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleAnnouncementValidation();
        }
    };
}();

var NewsValidation = function () {
    var handleNewsValidation = function(){
        $('#newsadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_title: {
                    required: true,
                },
                reg_source: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                reg_date: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: 'Judul Berita harus di isi',
                },
                reg_source: {
                    required: 'Sumber Berita harus di isi',
                },
                reg_desc: {
                    required: 'Isi Berita harus di isi',
                },
                reg_date: {
                    required: 'Tanggal Publikasi harus di isi',
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
                $('#save_news').modal('show');
            }
        });
    };

    var handleNewsEditValidation = function(){
        $('#newsedit').validate({
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                newsedit_title: {
                    required: true,
                },
                newsedit_source: {
                    required: true,
                },
                newsedit_desc: {
                    required: true,
                },
                newsedit_date: {
                    required: true,
                },
            },
            messages: {
                newsedit_title: {
                    required: 'Judul Berita harus di isi',
                },
                newsedit_source: {
                    required: 'Sumber Berita harus di isi',
                },
                newsedit_desc: {
                    required: 'Isi Berita harus di isi',
                },
                newsedit_date: {
                    required: 'Tanggal Publikasi harus di isi',
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
                $('#save_newsedit').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleNewsValidation();
            handleNewsEditValidation();
        }
    };
}();

var PaymentValidation = function () {
    var handlePaymentValidation = function(){
        $('#paymentadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_event: {
                    required: true,
                },
                reg_title: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                news_selection_files: {
                    required: true,
                },
            },
            messages: {
                reg_event: {
                    required: 'Nama Tenant harus di isi',
                },
                reg_title: {
                    required: 'Judul Pembayaran harus di isi',
                },
                reg_desc: {
                    required: 'Keterangan harus di isi',
                },
                news_selection_files: {
                    required: 'Bukti Pemabayaran harus di isi',
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
                $('#save_payment').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handlePaymentValidation();
        }
    };
}();

var IncubationValidation = function () {
    var handlePraincubationValidation = function(){
        $('#praincubationadd').validate({
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_year: {
                    required: true,
                },
                reg_user_id: {
                    required: true,
                },
                reg_name: {
                    required: true,
                },
                reg_title: {
                    required: true,
                },
                reg_category: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                /*
                reg_selection_files: {
                    required: true,
                },
                reg_selection_rab: {
                    required: true,
                },
                */
            },
            messages: {
                reg_year: {
                    required: 'Tahun Kegiatan harus di isi',
                },
                reg_user_id: {
                    required: 'Nama Pengguna harus di isi',
                },
                reg_name: {
                    required: 'Nama Peneliti Utama harus di isi',
                },
                reg_title: {
                    required: 'Judul Kegiatan harus di isi',
                },
                reg_category: {
                    required: 'Kategori Kegiatan harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Kegiatan harus di isi',
                },
                /*
                reg_selection_files: {
                    required: 'Berkas Proposal Kegiatan harus di isi',
                },
                reg_selection_rab: {
                    required: 'Berkas RAB Kegiatan harus di isi',
                },
                */
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('#alert', $(this)).addClass('alert-danger').html('Ada beberapa kesalahan, silahkan cek formulir di bawah ini!').fadeIn().delay(3000).fadeOut();
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
                if (element.parents(".form-group").size() > 0) {
                    element.parents(".form-group").append(error);
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            submitHandler: function (form) {
                $('#save_praincubationadd').modal('show');
            }
        });
    };

    var handleIncubationValidation = function(){
        $('#incubationadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_year: {
                    required: true,
                },
                reg_user_id: {
                    required: true,
                },
                reg_name: {
                    required: true,
                },
                reg_title: {
                    required: true,
                },
                reg_category: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                /*
                reg_selection_files: {
                    required: true,
                },
                reg_selection_rab: {
                    required: true,
                },
                */
            },
            messages: {
                reg_year: {
                    required: 'Tahun Kegiatan harus di isi',
                },
                reg_user_id: {
                    required: 'Nama Pengguna harus di isi',
                },
                reg_name: {
                    required: 'Nama Peneliti Utama harus di isi',
                },
                reg_title: {
                    required: 'Judul Kegiatan harus di isi',
                },
                reg_category: {
                    required: 'Kategori Kegiatan harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Kegiatan harus di isi',
                },
                /*
                reg_selection_files: {
                    required: 'Berkas Proposal Kegiatan harus di isi',
                },
                reg_selection_rab: {
                    required: 'Berkas RAB Kegiatan harus di isi',
                },
                */
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
                $('#save_incubationadd').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleIncubationValidation();
            handlePraincubationValidation();
        }
    };
}();

var ProductValidation = function () {
    var handleProductValidation = function(){
        $('#productadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_title: {
                    required: true,
                },
                reg_event: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                reg_thumbnail: {
                    required: true,
                },
                reg_details: {
                    required: true,
                },
                reg_category: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: 'Judul Produk harus di isi',
                },
                reg_event: {
                    required: 'Usulan Kegiatan harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Produk harus di isi',
                },
                reg_thumbnail: {
                    required: 'Thumbnail harus di isi',
                },
                reg_details: {
                    required: 'Details Gambar harus di isi',
                },
                reg_category: {
                    required: 'Kategori Produk harus di isi',
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
                $('#save_productadd').modal('show');
            }
        });
    };

    var handleProductEditValidation = function(){
        $('#productedit').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_title: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: 'Judul Produk harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Produk harus di isi',
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
                $('#save_productedit').modal('show');
            }
        });
    };

    var handleProductTenantEditValidation = function(){
        $('#producttenantedit').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_title: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: 'Judul Produk harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Produk harus di isi',
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
                $('#save_producttenantedit').modal('show');
            }
        });
    };

    var handleTenantProductValidation = function(){
        $('#producttenantadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_title: {
                    required: true,
                },
                reg_event: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                reg_thumbnail: {
                    required: true,
                },
                reg_details: {
                    required: true,
                },
                reg_category: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: 'Judul Produk harus di isi',
                },
                reg_event: {
                    required: 'Usulan Kegiatan harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Produk harus di isi',
                },
                reg_thumbnail: {
                    required: 'Thumbnail harus di isi',
                },
                reg_details: {
                    required: 'Details Gambar harus di isi',
                },
                reg_category: {
                    required: 'Kategori Produk harus di isi',
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
                $('#save_producttenantadd').modal('show');
            }
        });
    };

    var handleBlogTenantEditValidation = function(){
        $('#blogtenantedit').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_title: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: 'Judul Produk harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Produk harus di isi',
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
                $('#save_blogtenantedit').modal('show');
            }
        });
    };

    var handlePaymentEditValidation = function(){
        $('#paymentdataedit').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_title: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: 'Judul Produk harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Produk harus di isi',
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
                $('#save_paymentdataedit').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleProductValidation();
            handleTenantProductValidation();
            handleProductEditValidation();
            handleProductTenantEditValidation();
            handleBlogTenantEditValidation();
            handlePaymentEditValidation();
        }
    };
}();


var SliderValidation = function () {
    var handleSliderValidation = function(){
        $('#slideradd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                slider_image: {
                    required: true,
                },
            },
            messages: {
                slider_image: {
                    required: 'Gambar Slider di isi',
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
                if (element.is('input[type=file]')) {
                    $(element).parent().parent().parent().parent().parent().append(error);
                } else {
                    $(element).parents('.input-group').append(error);
                }
            },
            submitHandler: function (form) {
                $('#save_slider').modal('show');
            }
        });
    };

    var handleSliderEditValidation = function(){
        $('#sliderdataedit').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {},
            messages: {},
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
                if (element.is('input[type=file]')) {
                    $(element).parent().parent().parent().parent().parent().append(error);
                } else {
                    $(element).parents('.input-group').append(error);
                }
            },
            submitHandler: function (form) {
                $('#save_sliderdataedit').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleSliderValidation();
            handleSliderEditValidation();
        }
    };
}();

var SettingValidation = function () {
    var handleSettingWorkunitValidation = function(){
        $('#workunitadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_workunit: {
                    required: true,
                },
                reg_status: {
                    required: true,
                },
            },
            messages: {
                reg_workunit: {
                    required: 'Nama Satuan Kerja harus di isi',
                },
                reg_status: {
                    required: 'Status Satuan Kerja harus di isi',
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
                $('#save_workunit').modal('show');
            }
        });
        
        $('#workunitedit').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_workunit_edit: {
                    required: true,
                },
                reg_status_edit: {
                    required: true,
                },
            },
            messages: {
                reg_workunit_edit: {
                    required: 'Nama Satuan Kerja harus di isi',
                },
                reg_status_edit: {
                    required: 'Status Satuan Kerja harus di isi',
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
                $('#do_edit_workunit').trigger('click');
            }
        });
    };

    var handleSettingCategoryValidation = function(){
        $('#categoryadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_category: {
                    required: true,
                },
            },
            messages: {
                reg_category: {
                    required: 'Nama Kategori harus di isi',
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
                $('#save_category').modal('show');
            }
        });
    };

    var handleSettingCategoryProductValidation = function(){
        $('#categoryproductadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_category: {
                    required: true,
                },
            },
            messages: {
                reg_category: {
                    required: 'Nama Kategori Produk harus di isi',
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
                $('#save_categoryproduct').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleSettingWorkunitValidation();
            handleSettingCategoryValidation();
            handleSettingCategoryProductValidation();
        }
    };
}();

var AccountValidation = function () {
    var handleAccountValidation = function(){
        $('#accountsetting').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                ava_selection_files: {
                    required: true,
                },
            },
            messages: {
                ava_selection_files: {
                    required: "Berkas harus diisi."
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
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
                $(element).parents('.form-group').append(error);
            },
            submitHandler: function (form) {
                $('#save_account').modal('show');
            }
        });
    };

    var handleJobValidation = function(){
        $('#jobupdate').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                up_nip: {
                    required: true,
                },
                up_position: {
                    required: true,
                },
                workunit_type: {
                    required: true,
                },
            },
            messages: {
                up_nip: {
                    required: 'Nomor Induk Pegawai harus di isi',
                },
                up_position: {
                    required: 'Posisi anda harus di isi',
                },
                workunit_type: {
                    required: 'Satuan kerja anda harus di isi',
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
                $('#save_job').modal('show');
            }
        });
    };

    var handleChangePasswordValidation = function(){
        $('#changepassword').validate({
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                cur_pass: {
                    minlength: 6,
                    required: true,
                },
                new_pass: {
                    minlength: 6,
                    required: true,
                },
                cnew_pass: {
                    minlength: 6,
                    required: true,
                    equalTo : "#new_pass"
                },
            },
            messages: {
                cur_pass: {
                    minlength: "Minimal 6 karakter",
                    required: "Kata sandi lama anda harus diisi."
                },
                new_pass: {
                    minlength: "Minimal 6 karakter",
                    required: "Kata sandi baru harus diisi."
                },
                cnew_pass: {
                    minlength: "Minimal 6 karakter",
                    required: "Ulangi kata sandi baru harus diisi.",
                    equalTo: "Konfirmasi kata sandi harus sesuai dengan kata sandi"
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn().delay(3000).fadeOut();
            },
            highlight: function (element) { // hightlight error inputs
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
                $('#save_changepassword').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleProfileValidation();
            handleAccountValidation();
            handleJobValidation();
            handleChangePasswordValidation();
        }
    };
}();

var TenantValidation = function () {
    var handleAddTenantValidation = function(){
        $('#addtenant').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                tenant_username: {
                    required: true,
                },
                tenant_name: {
                    required: true,
                },
                tenant_email: {
                    required: true,
                    email: true,
                },
                tenant_year: {
                    required: true,
                },
                tenant_address: {
                    required: true,
                },
                tenant_district: {
                    required: true,
                },
                tenant_province: {
                    required: true,
                },
                tenant_regional: {
                    required: true,
                },
                tenant_phone_contact: {
                    required: true,
                },
                tenant_legal: {
                    required: true,
                },
                tenant_bussiness: {
                    required: true,
                },
                tenant_mitra: {
                    required: true,
                },
                /*
                tenant_event_id: {
                    required: true,
                },
                */
                tenant_desc: {
                    required: true,
                },
            },
            messages: {
                tenant_username: {
                    required: 'Username tenant harus di isi',
                },
                /*
                tenant_event_id: {
                    required: 'Usulan Kegiatan harus di isi',
                },
                */
                tenant_name: {
                    required: 'Nama Tenant harus di isi',
                },
                tenant_email: {
                    required: 'Email harus di isi',
                    email: 'Masukkan alamat email Anda yang benar',
                },
                tenant_year: {
                    required: 'Pilih tahun berdirinya tenant anda',
                },
                tenant_address: {
                    required: 'Alamat harus di isi',
                },
                tenant_district: {
                    required: 'Kecamatan/Kelurahan harus di isi',
                },
                tenant_province: {
                    required: 'Provinsi harus di isi',
                },
                tenant_regional: {
                    required: 'Kota/Kabupaten harus di isi',
                },
                tenant_phone_contact: {
                    required: 'Kontak harus di isi',
                },
                tenant_legal: {
                    required: 'Legal harus di pilih',
                },
                tenant_bussiness: {
                    required: 'NPWP harus di pilih',
                },
                tenant_mitra: {
                    required: 'Kemitraan harus di pilih',
                },
                tenant_desc: {
                    required: 'Deskripsi Tenant harus di pilih',
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
                if (element.parents(".form-group").size() > 0) {
                    element.parents(".form-group").append(error);
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) {
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            submitHandler: function (form) {
                $('#save_addtenant').modal('show');
            }
        });

        //Mobile Phone Number
        $('#tenant_phone_contact').inputmask('+62 99999999999', { placeholder: '+__ ___________' });
    };

    var handleAddTenantUserValidation = function(){
        $('#addtenantuser').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                tenant_name: {
                    required: true,
                },
                tenant_email: {
                    required: true,
                    email: true,
                },
                tenant_year: {
                    required: true,
                },
                tenant_address: {
                    required: true,
                },
                tenant_district: {
                    required: true,
                },
                province: {
                    required: true,
                },
                regional: {
                    required: true,
                },
                tenant_phone_contact: {
                    required: true,
                },
                tenant_legal: {
                    required: true,
                },
                tenant_bussiness: {
                    required: true,
                },
                tenant_mitra: {
                    required: true,
                },
                tenant_event_id: {
                    required: true,
                },
            },
            messages: {
                tenant_event_id: {
                    required: 'Usulan Kegiatan harus di isi',
                },
                tenant_name: {
                    required: 'Nama Tenant harus di isi',
                },
                tenant_email: {
                    required: 'Email harus di isi',
                    email: 'Masukkan alamat email Anda yang benar',
                },
                tenant_year: {
                    required: 'Pilih tahun berdirinya tenant anda',
                },
                tenant_address: {
                    required: 'Alamat harus di isi',
                },
                tenant_district: {
                    required: 'Kecamatan/Kelurahan harus di isi',
                },
                province: {
                    required: 'Provinsi harus di isi',
                },
                regional: {
                    required: 'Kota/Kabupaten harus di isi',
                },
                tenant_phone_contact: {
                    required: 'Kontak harus di isi',
                },
                tenant_legal: {
                    required: 'Legal harus di pilih',
                },
                tenant_bussiness: {
                    required: 'NPWP harus di pilih',
                },
                tenant_mitra: {
                    required: 'Kemitraan harus di pilih',
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
                $('#save_addtenantuser').modal('show');
            }
        });

        //Mobile Phone Number
        $('#tenant_phone_contact').inputmask('+62 99999999999', { placeholder: '+__ ___________' });
    };

    var handleLogoTenantValidation = function(){
        $('#tenantlogo').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                tenant_logo_files: {
                    required: true,
                },
            },
            messages: {
                tenant_logo_files: {
                    required: "Berkas logo tenant harus diisi."
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
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
                if (element.is('input[type=file]')) {
                    $(element).parent().parent().parent().parent().parent().append(error);
                } else {
                    $(element).parents('.input-group').append(error);
                }
            },
            submitHandler: function (form) {
                bootbox.confirm("Anda yakin akan mengganti logo tenant?", function(result) {
                    if( result == true ){
                        processSaveLogoTenant($('#tenantlogo'));
                    }
                });
            }
        });

        var processSaveLogoTenant = function( form ) {
            var url     = form.attr( 'action' );
            var data    = new FormData(form[0]);
            var wrapper = $('#tenantlogo');
            var elimg   = $('.tenant-logo-wrapper');

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

                    if( response.status == 'error' ){
                        App.alert({
                            type: 'danger',
                            icon: 'warning',
                            message: response.message,
                            container: wrapper,
                            place: 'prepend',
                            closeInSeconds: 2,
                        });
                    }else{
                        App.alert({
                            type: 'success',
                            icon: 'check',
                            message: response.message,
                            container: wrapper,
                            place: 'prepend',
                            closeInSeconds: 2,
                        });

                        $('#tenantlogo')[0].reset();
                        $('#tenant_logo_files').fileinput('refresh');
                        elimg.empty().html(response.file).fadeIn();
                    }
    			}
    		});
        };

        $('#tenant_logo_files').on('fileselect', function(event, numFiles, label) {
            $('#tenantlogo label.error').remove();
        });

        $('button.btn-clear-logo-tenant').on('click', function(event) {
            event.preventDefault();
            if( $('button.fileinput-remove-button').is(':visible') ){
                $('button.fileinput-remove-button').trigger('click');
                return false;
            }
            $('#tenantlogo label.error').remove();
        });
    };

    var handleAvatarTenantTeamValidation = function(){
        $('#tenantteamava').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                tenant_team_ava_files: {
                    required: true,
                },
            },
            messages: {
                tenant_team_ava_files: {
                    required: "Berkas avatar tim tenant harus diisi."
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
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
                if (element.is('input[type=file]')) {
                    $(element).parent().parent().parent().parent().parent().append(error);
                } else {
                    $(element).parents('.input-group').append(error);
                }
            },
            submitHandler: function (form) {
                processUpdateAvatarTenantTeam($('#tenantteamava'));
                return false;
            }
        });

        var processUpdateAvatarTenantTeam = function( form ) {
            var url     = form.attr( 'action' );
            var data    = new FormData(form[0]);
            var wrapper = $('#tenantteamava');
            var elimg   = $('.tenant-team-ava-wrapper');
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

                    if( response.status == 'error' ){
                        msg.html(response.message);
                        msg.removeClass('alert-success').addClass('alert-danger').fadeIn('fast').delay(3000).fadeOut();
                    }else{
                        msg.html(response.message);
                        msg.removeClass('alert-danger').addClass('alert-success').fadeIn('fast').delay(3000).fadeOut();

                        $('#tenantteamava')[0].reset();
                        $('#tenant_team_ava_files').fileinput('refresh');
                        elimg.empty().html(response.file).fadeIn();
                        $('button#btn_list_tenant_team_reset').trigger('click');
                    }
    			}
    		});
            return false;
        };

        $('#tenant_team_ava_files').on('fileselect', function(event, numFiles, label) {
            $('#tenantteamava label.error').remove();
        });

        $('button.btn-clear-tenant-team-ava').on('click', function(event) {
            event.preventDefault();
            if( $('button.fileinput-remove-button').is(':visible') ){
                $('button.fileinput-remove-button').trigger('click');
                return false;
            }
            $('#tenantteamava label.error').remove();
        });
    };

    var handleEditTenantDetailValidation = function(){
        $('#tenantdetails').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                tenant_name: {
                    required: true,
                },
                tenant_email: {
                    required: true,
                    email: true,
                },
                tenant_year: {
                    required: true,
                },
                tenant_address: {
                    required: true,
                },
                tenant_province: {
                    required: true,
                },
                tenant_regional: {
                    required: true,
                },
                tenant_district: {
                    required: true,
                },
                tenant_phone_contact: {
                    required: true,
                },
                tenant_legal: {
                    required: true,
                },
                tenant_bussiness: {
                    required: true,
                },
                tenant_mitra: {
                    required: true,
                },
                tenant_desc: {
                    required: true,
                },
            },
            messages: {
                tenant_name: {
                    required: 'Nama Tenant harus di isi',
                },
                tenant_email: {
                    required: 'Email harus di isi',
                    email: 'Masukkan alamat email Anda yang benar',
                },
                tenant_year: {
                    required: 'Pilih tahun berdirinya tenant anda',
                },
                tenant_address: {
                    required: 'Alamat harus di isi',
                },
                tenant_district: {
                    required: 'Kecamatan/Kelurahan harus di isi',
                },
                tenant_province: {
                    required: 'Provinsi harus di isi',
                },
                tenant_regional: {
                    required: 'Kota/Kabupaten harus di isi',
                },
                tenant_phone_contact: {
                    required: 'Kontak harus di isi',
                },
                tenant_legal: {
                    required: 'Legal harus di pilih',
                },
                tenant_bussiness: {
                    required: 'NPWP harus di pilih',
                },
                tenant_mitra: {
                    required: 'Kemitraan harus di pilih',
                },
                tenant_mitra: {
                    required: 'Deskripsi Tenant harus di pilih',
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn();
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
                if (element.parents(".form-group").size() > 0) {
                    element.parents(".form-group").append(error);
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            submitHandler: function (form) {
                bootbox.confirm("Anda yakin akan memperbaharui data tenant?", function(result) {
                    if( result == true ){
                        processEditTenantDetail($('#tenantdetails'));
                    }
                });
            }
        });

        var processEditTenantDetail = function( form ) {
            var url     = $(form).attr( 'action' );
            var data    = $(form).serialize()
            var wrapper = form;

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

                    if( response.status == 'error' ){
                        App.alert({
                            type: 'danger',
                            icon: 'warning',
                            message: response.message,
                            container: wrapper,
                            place: 'prepend',
                            closeInSeconds: 2,
                        });
                    }else{
                        App.alert({
                            type: 'success',
                            icon: 'check',
                            message: response.message,
                            container: wrapper,
                            place: 'prepend',
                            closeInSeconds: 2,
                        });
                    }
    			}
    		});
        };

        //Mobile Phone Number
        $('#tenant_phone_contact').inputmask('+62 99999999999', { placeholder: '+__ ___________' });
    };

    var handleEditTenantTeamValidation = function(){
        $("body").delegate( "button.btn-edit-tenant-team", "click", function( event ) {
            event.preventDefault();

            $('#tenantteamedit').validate({
                focusInvalid: true, // do not focus the last invalid input
                ignore: "",
                rules: {
                    team_name_edit: {
                        required: true,
                    },
                    team_position_edit: {
                        required: true,
                    },
                },
                messages: {
                    team_name_edit: {
                        required: "Nama tim tenant harus diisi."
                    },
                    team_position_edit: {
                        required: "Posisi tim tenant harus diisi."
                    },
                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                    $('.alert-danger', $(this)).fadeIn();
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
                    if (element.parents(".form-group").size() > 0) {
                        element.parents(".form-group").append(error);
                    } else if (element.attr("data-error-container")) {
                        error.appendTo(element.attr("data-error-container"));
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavior
                    }
                },
                submitHandler: function (form) {
                    processEditTenantTeam($('#tenantteamedit'));
                }
            });

            if( $('#tenantteamedit').valid() ){
                $('#tenantteamedit').submit();
            }
        });

        var processEditTenantTeam = function( form ) {
            var url     = form.attr( 'action' );
            var data    = $('#tenantteamedit').serialize();
            var msg     = $('#alert');

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

                    if( response.status == 'error' ){
                        msg.html(response.message);
                        msg.removeClass('alert-success').addClass('alert-danger').fadeIn('fast').delay(3000).fadeOut();
                    }else{
                        msg.html(response.message);
                        msg.removeClass('alert-danger').addClass('alert-success').fadeIn('fast').delay(3000).fadeOut();
                        $('button#btn_list_tenant_team_reset').trigger('click');
                    }
    			}
    		});
            return false;
        };
    };

    var handleAddBlogTenantValidation = function(){
        $('#addblogtenant').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_product: {
                    required: true,
                },
                reg_title: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                reg_thumbnail: {
                    required: true,
                },
                reg_details: {
                    required: true,
                },
            },
            messages: {
                reg_product: {
                    required: 'Usulan Kegiatan harus di isi',
                },
                reg_title: {
                    required: 'Nama Tenant harus di isi',
                },
                reg_desc: {
                    required: 'Email harus di isi',
                },
                reg_thumbnail: {
                    required: 'Thumbnail harus di isi',
                },
                reg_details: {
                    required: 'Gambar harus di isi',
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
                $('#save_addtenantblog').modal('show');
            }
        });
    };

    var handleAddTeamTenantValidation = function(){
        $('#addteamtenant').validate({
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
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
                if (element.is('input[type=file]')) {
                    $(element).parent().parent().parent().parent().parent().append(error);
                } else {
                    $(element).parents('.input-group').append(error);
                }
            },
            submitHandler: function (form) {
                bootbox.confirm("Apakah data tim tenant sudah benar?", function(result) {
                    if( result == true ){
                        var teamform    = $('#addteamtenant');
                        var teamdata    = new FormData(teamform[0]);
                        var url         = teamform.attr('action');
                        var container   = $('div.body-team');

                        $.ajax({
                            type:   "POST",
                            url:    url,
                            data:   teamdata,
                            cache : false,
                            contentType : false,
                            processData : false,
                            beforeSend: function (){
                                $("div.page-loader-wrapper").fadeIn();
                            },
                            success: function( response ){
                                $("div.page-loader-wrapper").fadeOut();
                                response = $.parseJSON(response);

                                if( response.status == 'error' ){
                                    App.alert({
                                        type: 'danger',
                                        icon: 'warning',
                                        message: response.message,
                                        container: container,
                                        place: 'prepend',
                                        closeInSeconds: 3
                                    });
                                }else{
                                    App.alert({
                                		type: 'success',
                                		icon: 'check',
                                		message: response.message,
                                		container: container,
                                		place: 'prepend',
                                		closeInSeconds: 3
                                	});

                                    var wrapper         = $(".addteam_container");
                                    var content         = '<div class="card">' +
                                        '<div class="header header-team bg-cyan">' +
                                            '<h5>Data Tim Tenant</h5>' +
                                        '</div>' +
                                        '<div class="body">' +
                                            '<div class="row bottom0">' +
                                                '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 bottom0-lg" >' +
                                                    '<div class="input-group">' +
                                                        '<input name="team_image_1" id="team_image_1" class="form-control team_image" type="file" />' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 bottom0" >' +
                                                    '<div class="input-group">' +
                                                        '<label class="form-label">Name *</label>' +
                                                        '<div class="form-line">' +
                                                            '<input type="text" class="form-control team_name" name="team_name_1" id="team_name_1">' +
                                                        '</div>' +
                                                    '</div>' +
                                                    '<div class="input-group bottom0">' +
                                                        '<label class="form-label">Jabatan/Posisi/Peran *</label>' +
                                                        '<div class="form-line">' +
                                                            '<input type="text" class="form-control team_position" name="team_position_1" id="team_position_1">' +
                                                        '</div>' +
                                                    '</div>' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>';

                                    wrapper.empty().html(content).fadeIn();
                                    $('input[name=team_count]').val(1);

                                    $(".team_image").fileinput({
                                        showUpload : false,
                                        showUploadedThumbs : false,
                                        'theme': 'explorer',
                                        'uploadUrl': '#',
                                        fileType: "any",
                                        overwriteInitial: false,
                                        initialPreviewAsData: true,
                                        allowedFileExtensions: ['jpg', 'jpeg', 'png'],
                                        fileActionSettings : {
                                            showUpload: false,
                                            showZoom: false,
                                        },
                                        maxFileSize: 1024,
                                    });

                                }
                            }
                        });
                    }
                });
            }
        });

        $('input.team_image').each(function() {
            $(this).rules('add', {
                required: true,
                messages: {
                    required:  "Foto tim harus di isi",
                }
            });
        });

        $('input.team_name').each(function() {
            $(this).rules('add', {
                required: true,
                messages: {
                    required:  "Nama tim harus di isi",
                }
            });
        });

        $('input.team_position').each(function() {
            $(this).rules('add', {
                required: true,
                messages: {
                    required:  "Jabatan/posisi/peran tim harus di isi",
                }
            });
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleAddTenantValidation();
            handleAddTenantUserValidation();
            handleLogoTenantValidation();
            handleAddBlogTenantValidation();
            handleAddTeamTenantValidation();
            handleAvatarTenantTeamValidation();
            handleEditTenantDetailValidation();
            handleEditTenantTeamValidation();
        }
    };
}();

var ScoreUserValidation = function () {
    var handleScoreUserStep1Validation = function(){
        $('#selection_score_step1').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                nilai_dokumen: {
                    required: true,
                },
                nilai_target: {
                    required: true,
                },
                nilai_perlingungan: {
                    required: true,
                },
                nilai_penelitian: {
                    required: true,
                },
                nilai_market: {
                    required: true,
                },
            },
            messages: {
                nilai_dokumen: {
                    required: 'Penilaian Dokumen harus di isi',
                },
                nilai_target: {
                    required: 'Penilaian Target dan Biaya harus di isi',
                },
                nilai_perlingungan: {
                    required: 'Penilaian Perlindungan harus di isi',
                },
                nilai_penelitian: {
                    required: 'Penilaian Penelitian Lanjutan harus di isi',
                },
                nilai_market: {
                    required: 'Penilaian Market harus di isi',
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn().delay(3000).fadeOut();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.input-group').addClass('error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).parents('.input-group').removeClass('error');
            },
            success: function (label) {
                label.closest('.input-group').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                if (element.parent(".input-group").size() > 0) {
                    element.parent(".input-group").append(error);
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) {
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            submitHandler: function (form) {
                $('#save_scoreuser').modal('show');
            }
        });

        $('#selectionincubation_score_step1').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                nilai_dokumen: {
                    required: true,
                },
                nilai_target: {
                    required: true,
                },
                nilai_perlingungan: {
                    required: true,
                },
                nilai_penelitian: {
                    required: true,
                },
                nilai_market: {
                    required: true,
                },
            },
            messages: {
                nilai_dokumen: {
                    required: 'Penilaian Dokumen harus di isi',
                },
                nilai_target: {
                    required: 'Penilaian Target dan Biaya harus di isi',
                },
                nilai_perlingungan: {
                    required: 'Penilaian Perlindungan harus di isi',
                },
                nilai_penelitian: {
                    required: 'Penilaian Penelitian Lanjutan harus di isi',
                },
                nilai_market: {
                    required: 'Penilaian Market harus di isi',
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn().delay(3000).fadeOut();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.input-group').addClass('error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).parents('.input-group').removeClass('error');
            },
            success: function (label) {
                label.closest('.input-group').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                if (element.parent(".input-group").size() > 0) {
                    element.parent(".input-group").append(error);
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) {
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            submitHandler: function (form) {
                $('#save_scoreuserincubation').modal('show');
            }
        });
    };

    var handleScoreUserStep2Validation = function(){
        /*
        $.validator.addMethod('require-one', function(value) {
            return $('input.require-one:checked').size() > 0;
        }, 'Silahkan pilih minimal 1 IRL.');

        var checkboxes = $('input.require-one');
    	var checkbox_names = $.map(checkboxes, function(e, i) {
    		return $(e).attr("name")
    	}).join(" ");
        */

        $('#selection_score_step2').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            /*
            groups: {
    			checks: checkbox_names
    		},
            */
            rules: {
                nilai_juri_comment: {
                    required: true,
                },
                irl: {
                    required: true,
                },
            },
            messages: {
                nilai_juri_comment: {
                    required: 'Komentar juri harus di isi',
                },
                irl: {
                    required: 'Silahkan pilih IRL',
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn().delay(3000).fadeOut();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.input-group').addClass('error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).parents('.input-group').removeClass('error');
            },
            success: function (label) {
                label.closest('.input-group').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                if (element.parent(".input-group").size() > 0) {
                    element.parent(".input-group").append(error);
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) {
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            submitHandler: function (form) {
                $('#save_scoreuserpraincubation2').modal('show');
            }
        });

        $('#selectionincubation_score_step2').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                nilai_juri_comment: {
                    required: true,
                },
            },
            messages: {
                nilai_juri_comment: {
                    required: 'Komentar juri harus di isi',
                },
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                $('.alert-danger', $(this)).fadeIn().delay(3000).fadeOut();
            },
            highlight: function (element) { // hightlight error inputs
                console.log(element);
                $(element).parents('.input-group').addClass('error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).parents('.input-group').removeClass('error');
            },
            success: function (label) {
                label.closest('.input-group').removeClass('error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                if (element.parent(".input-group").size() > 0) {
                    element.parent(".input-group").append(error);
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) {
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            submitHandler: function (form) {
                $('#save_scoreuserincubation2').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleScoreUserStep1Validation();
            handleScoreUserStep2Validation();
        }
    };
}();

var NotesValidation = function () {
    var handleNotesValidation = function(){
        $('#notesadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_event: {
                    required: true,
                },
                reg_title: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                reg_selection_files: {
                    required: true,
                },
                companion_id: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: 'Judul Notulensi harus di isi',
                },
                reg_event: {
                    required: 'Usulan Kegiatan harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Notulensi harus di isi',
                },
                reg_selection_files: {
                    required: 'Berkas Notulensi harus di isi',
                },
                companion_id: {
                    required: 'Nama Pendamping harus di isi',
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
                $('#save_notesadd').modal('show');
            }
        });
    };

    var handleNotesIncubationValidation = function(){
        $('#notesincubationadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_event: {
                    required: true,
                },
                reg_title: {
                    required: true,
                },
                reg_desc: {
                    required: true,
                },
                reg_selection_files: {
                    required: true,
                },
                companion_id: {
                    required: true,
                },
            },
            messages: {
                reg_title: {
                    required: 'Judul Notulensi harus di isi',
                },
                reg_event: {
                    required: 'Usulan Kegiatan harus di isi',
                },
                reg_desc: {
                    required: 'Deskripsi Notulensi harus di isi',
                },
                reg_selection_files: {
                    required: 'Berkas Notulensi harus di isi',
                },
                companion_id: {
                    required: 'Nama Pendamping harus di isi',
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
                $('#save_noteincubationadd').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleNotesValidation();
            handleNotesIncubationValidation();
        }
    };
}();

var ReportValidation = function () {
    var handleReportPraincubationValidation = function(){
        $('#reportpraincubationadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_event: {
                    required: true,
                },
                reg_month: {
                    required: true,
                },
                reg_selection_files: {
                    required: true,
                },
            },
            messages: {
                reg_event: {
                    required: 'Usulan Kegiatan harus di isi',
                },
                reg_month: {
                    required: 'Bulan harus di isi',
                },
                reg_selection_files: {
                    required: 'Berkas Laporan harus di isi',
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
                $('#save_reportpraincubationadd').modal('show');
            }
        });
    };

    var handleReportTenantValidation = function(){
        $('#reporttenantadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_event: {
                    required: true,
                },
                reg_month: {
                    required: true,
                },
                reg_selection_files: {
                    required: true,
                },
            },
            messages: {
                reg_event: {
                    required: 'Nama Tenant harus di isi',
                },
                reg_month: {
                    required: 'Bulan harus di isi',
                },
                reg_selection_files: {
                    required: 'Berkas Laporan harus di isi',
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
                $('#save_reporttenantadd').modal('show');
            }
        });
    };

    var handleReportActionPlanValidation = function(){
        $('#reportactionplanadd').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_event: {
                    required: true,
                },
                reg_year: {
                    required: true,
                },
                reg_month: {
                    required: true,
                },
                reg_name_actionplan: {
                    required: true,
                },
            },
            messages: {
                reg_event: {
                    required: 'Nama Tenant harus di isi',
                },
                reg_year: {
                    required: 'Tahun harus di isi',
                },
                reg_month: {
                    required: 'Bulan harus di isi',
                },
                reg_name_actionplan: {
                    required: 'Nama Action Plan harus di isi',
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
                $('#save_reportactionplanadd').modal('show');
            }
        });
    };

    var handleReportActionPlanFilesValidation = function(){
        $('#reportactionplanaddfiles').validate({
            focusInvalid: true, // do not focus the last invalid input
            ignore: "",
            rules: {
                reg_actionplan_files: {
                    required: true,
                },
            },
            messages: {
                reg_actionplan_files: {
                    required: 'Bukti Berkas Action Plan harus di isi',
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
                $('#save_reportactionplanaddfiles').modal('show');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleReportPraincubationValidation();
            handleReportTenantValidation();
            handleReportActionPlanValidation();
            handleReportActionPlanFilesValidation();
        }
    };
}();
