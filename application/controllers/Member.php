<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Member Controller.
 * 
 * @class     Rifal
 * @author    Iqbal
 * @version   1.0.0
 * @copyright Copyright (c) 2016 BKEV Global Network (http://www.bkev-globalnetwork.com)
 */
class Member extends CI_Controller {
    /**
	 * Constructor.
	 */
    function __construct()
    {       
        parent::__construct();
        
        $ip_lost_permission = config_item('ip_lost_permission');
        if( is_coming_soon() ){
            if( !in_array($this->input->ip_address(), $ip_lost_permission) ){
                redirect(base_url(), 'location');
            }
        }
        
        if( is_maintenance() ){
            if( !in_array($this->input->ip_address(), $ip_lost_permission) ){
                redirect(base_url(), 'location'); 
            }
        }
    }
    
    /**
	 * Index function.
	 */
    public function login()
    {
        if( is_member_logged_in() ){
            redirect('dashboard', 'location'); die();
        }
        
        $data['title']          = TITLE . 'Login Anggota';
        $data['main_content']   = 'login';
        
        $this->load->view(VIEW_BACK . 'login', $data);
    }
    
    /**
	 * Logout member function.
     * @return URL redirect page
	 */
    public function logout()
    {   
        if ($this->session->userdata('member_logged_in'))
        {
            $this->session->unset_userdata('member_logged_in');
            $this->session->sess_update();
        }
        
        erp_clear_auth_cookie();
        redirect( base_url(), 'refresh' );
    }
    
    /**
	 * Validate Login member function.
     * @return AJAX String
	 */
    public function validate()
    {
        // Set credential variable param
        $username   = erp_isset($this->input->post("username"), '');
        $password   = md5( erp_isset($this->input->post("password"), '') );
        $remember   = erp_isset($this->input->post("remember"), '');
        
        // Set Credential for login
        $credentials['username']    = strtolower($username);
        $credentials['password']    = $password;
        $credentials['remember']    = $remember;
        
        // Sign On member
        $member     = $this->Model_Member->signon($credentials);
        $response   = array(
            'success'   => false,
            'msg'       => 'Failed',
		);

        // Response of signon member
        if ( $member == 'not_active' ){
            $response['msg'] = 'Not Active';
        } elseif ( $member == 'banned' ){
            $response['msg'] = 'Banned';
        } elseif ( $member == 'deleted' ){
            $response['msg'] = 'Deleted';
        } elseif ( $member ) {
            $member         = $this->erp_member->member($member->id);

            $last_activity  = date('Y-m-d H:i:s', time() );   
            $login_update   = array( 'last_login' => $last_activity );
            $this->Model_Member->update_data($member->id, $login_update);
            
            // Set session data
            $session_data   = array(
                'id'                => $member->id,
                'username'          => $member->username,
                'phone'             => $member->phone,
                'name'              => $member->name,
                'email'             => $member->email,
                'id_adm_group'      => $member->id_adm_group,
                'id_adm_company'    => $member->id_adm_company,
                'id_adm_module'     => $member->default_id_adm_module,
                'last_login'        => $member->last_login
            );
            
            // Set session
            $this->session->set_userdata('member_logged_in', $session_data);
            
            // Set cookie domain
            $cookie_domain  = str_replace(array('http://', 'https://', 'www.'), '', base_url());
            $cookie_domain  = '.' . str_replace('/', '', $cookie_domain);
            $expire         = 0;
            // Set cookie data
            $cookie         = array(
                'name'      => 'logged_in_'.md5('nonssl'),
                'value'     => $member->id,
                'expire'    => $expire,
                'domain'    => $cookie_domain,
                'path'      => '/',
                'secure'    => false,
            );
            
            // set cookie
            setcookie($cookie['name'], $cookie['value'],$cookie['expire'],$cookie['path'],$cookie['domain'],$cookie['secure']);
            
            // log logged in user
			erp_log( 'LOGGED_IN', $username, maybe_serialize( array( 'creds' => $credentials, 'member' => $member, 'ip' => erp_get_current_ip(), 'cookie' => $_COOKIE ) ) );
            
            $response['success'] = true;
			$response['msg']     = base_url('dashboard');
        } else {
            $response['error'] = true;
			$response['msg']     = 'failed';
        }
        
        // print response in JSON format
		die( json_encode( $response ) );
    }
    
    /**
	 * Profile Member function.
	 */
    function profile( $id=0 )
    {
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        $is_consultant          = as_consultant($current_member);
        
        if ( $id > 0 && $is_consultant ) redirect( base_url('profile'), 'location' );
        
        if ( $id > 0 && $is_admin ){
            $member_data        = bgn_get_memberdata_by_id($id);
        } elseif( $id > 0 && !$is_admin ) {
            $is_down            = $this->Model_Member->get_is_downline($id, $current_member->tree);
            
            if( !$is_down ){
                redirect( base_url('profile'), 'location' );
            }
        }
            
        $data['title']          = TITLE . 'Profil Anggota';
        $data['member']         = $current_member;
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        $data['main_content']   = 'profile';        
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Profile Personal Info Update function.
	 */
    function personalinfo()
    {
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        $id_member              = ( bgn_isset($this->input->post('member_id'), '') > 0 ? bgn_isset($this->input->post('member_id'), '') : $current_member->id );
        $username               = bgn_isset($this->input->post('member_username'), '');
        
        $this->form_validation->set_rules('member_name','Nama Anggota','required');
        $this->form_validation->set_rules('member_email','Alamat Email','required');
        $this->form_validation->set_rules('member_address','Alamat','required');
        $this->form_validation->set_rules('member_city','Kota','required');
        $this->form_validation->set_rules('member_phone','Telp/HP','required');
        $this->form_validation->set_rules('member_bill_name','Pemilik No. Rekening Bank','required');
        $this->form_validation->set_rules('member_branch','Cabang Bank','required');
        
        if( bgn_isset($this->input->post('member_bank'), '') ){
            $this->form_validation->set_rules('member_bank','Nama Bank','required');
        }
        
        $this->form_validation->set_message('required', '%s harus di isi');
        $this->form_validation->set_error_delimiters('', '');
        
        if($this->form_validation->run() == FALSE){
            // Set JSON data
            $data = array(
                'message'       => 'error',
                'data'          => '<button class="close" data-close="alert"></button>Pendaftaran anggota baru tidak berhasil. '.validation_errors().'',
            );
            // JSON encode data
            die(json_encode($data));
        }else{
            $curdate            = date("Y-m-d H:i:s");
            $memberdata         = array(
                'name'          => bgn_isset($this->input->post('member_name'), ''),
                'email'         => bgn_isset($this->input->post('member_email'), ''),
                'address'       => bgn_isset($this->input->post('member_address'), ''),
                'city'          => bgn_isset($this->input->post('member_city'), ''),
                'phone'         => bgn_isset($this->input->post('member_phone'), ''),
                'idcard'        => bgn_isset($this->input->post('member_idcard'), ''),
                'npwp'			=> bgn_isset($this->input->post('member_npwp'), ''),
                'bbpin'         => bgn_isset($this->input->post('member_bbpin'), ''),
                'bill_name'     => bgn_isset($this->input->post('member_bill_name'), ''),
                'branch'        => bgn_isset($this->input->post('member_branch'), ''),
                'datemodified'  => $curdate,
            );
            
            if( bgn_isset($this->input->post('member_bank'), '') ){
                $memberdata['bank']         = bgn_isset($this->input->post('member_bank'), '');
            }
            
            if( bgn_isset($this->input->post('member_bill'), '') ){
                $memberdata['bill']         = bgn_isset($this->input->post('member_bill'), '');
            }
            
            if( $save_member    = $this->Model_Member->update_data($id_member, $memberdata) ){
                // Set Message
                $msg            = ( $id_member != $current_member->id ? 'Data profil Anggota <strong>'. $username .'</strong> sudah tersimpan.' : 'Data profil Anda sudah tersimpan.' );
                
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => '<button class="close" data-close="alert"></button>Validasi formulir Anda berhasil! '.$msg.'',
                    'name'      => ( !empty($id_member) ? '' : bgn_isset($this->input->post('member_name'), '') ),
                );
            }else{
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => '<button class="close" data-close="alert"></button>Validasi formulir Anda tidak berhasil! Silahkan periksa kembali data formulir Anda!',
                );
            }
            
            // JSON encode data
            die(json_encode($data));
        }
    }
    
    /**
	 * Change Password function.
	 */
    function changepassword()
    {
        auth_redirect();
        
        if( bgn_isset($this->input->post('id_member_other'), '') != '' ){
            $id_member          = bgn_isset($this->input->post('id_member_other'), '');
            $username           = bgn_isset($this->input->post('username_other'), '');
            $curdate            = date("Y-m-d H:i:s");
            
            $memberdata         = bgn_get_memberdata_by_id($id_member);
            if( !$memberdata || empty($memberdata) ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Data anggota <strong>'.$username.'</strong> tidak ditemukan!',
                );
            }
            
            $global_pass        = get_option('global_password');
            $passdata           = array(
                'password'      => md5($global_pass),
                'datemodified'  => $curdate
            );
            
            if( $save_pass      = $this->Model_Member->update_data($id_member, $passdata) ){
                // Send SMS Confirmation
                $this->bgn_sms->sms_cpassword($memberdata->phone, $username, $global_pass);
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => '<button class="close" data-close="alert"></button>Reset/Atur ulang password anggota <strong>'.$username.'</strong> berhasil!',
                );
            }else{
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Reset/Atur ulang password anggota <strong>'.$username.'</strong> tidak berhasil!',
                );
            }
            // JSON encode data
            die(json_encode($data));
            
        }
        
        $current_member         = bgn_get_current_member();
        
        $this->form_validation->set_rules('cur_pass','Password Lama','required');
        $this->form_validation->set_rules('new_pass','Pasword Baru','required');
        $this->form_validation->set_rules('cnew_pass','Konfirmasi Password Baru','required');
        
        if($this->form_validation->run() == FALSE){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Anda memiliki beberapa kesalahan. Silakan cek di formulir bawah ini!',
            );
            // JSON encode data
            die(json_encode($data));
        }else{
            // Set Variable
            $cur_pass       = bgn_isset($this->input->post('cur_pass'), '');
            $new_pass       = bgn_isset($this->input->post('new_pass'), '');
            $new_pass_sms   = bgn_isset($this->input->post('new_pass'), '');
            $cnew_pass      = bgn_isset($this->input->post('cnew_pass'), '');
            
            // Check Member Password
            $check_pass     = $this->Model_Member->authenticate($current_member->username, md5($cur_pass));
            
            if ( !$check_pass ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Password lama yang anda masukkan salah!',
                );
                // JSON encode data
                die(json_encode($data));
            }else{
                if( $new_pass != $cnew_pass ){
                    // Set JSON data
                    $data = array(
                        'message'   => 'error',
                        'data'      => '<button class="close" data-close="alert"></button>Konfirmasi password tidak sesuai dengan password baru!',
                    );
                    // JSON encode data
                    die(json_encode($data));
                }else{
                    $curdate            = date("Y-m-d H:i:s");
                    $passdata           = array(
                        'password'      => md5($new_pass),
                        'datemodified'  => $curdate,
                    );
                    
                    if( $save_pass      = $this->Model_Member->update_data($current_member->id, $passdata) ){
                        // Send SMS Confirmation
                        $this->bgn_sms->sms_cpassword($current_member->phone, $current_member->username, $new_pass_sms);
                        // Clear Session and Cookies
                        if ($this->session->userdata('member_logged_in'))
                        {
                            $this->session->unset_userdata('member_logged_in');
                            $this->session->sess_update();
                        }
                        bgn_clear_auth_cookie();
                        // Set JSON data
                        $data = array(
                            'message'   => 'success',
                            'data'      => base_url('login'),
                        );
                    }else{
                        // Set JSON data
                        $data = array(
                            'message'   => 'error',
                            'data'      => '<button class="close" data-close="alert"></button>Validasi formulir Anda tidak berhasil! Silahkan periksa kembali data formulir Anda!',
                        );
                    }
                    // JSON encode data
                    die(json_encode($data));
                }
            }
        }
    }
    
    /**
	 * Change Password PIN function.
	 */
    function changepasswordpin()
    {
        auth_redirect();
        
        if( bgn_isset($this->input->post('id_member_other'), '') != '' ){
            $id_member          = bgn_isset($this->input->post('id_member_other'), '');
            $username           = bgn_isset($this->input->post('username_other'), '');
            $curdate            = date("Y-m-d H:i:s");
            
            $memberdata         = bgn_get_memberdata_by_id($id_member);
            if( !$memberdata || empty($memberdata) ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Data anggota <strong>'.$username.'</strong> tidak ditemukan!',
                );
            }
            
            $global_pass        = get_option('global_password');
            $passdata           = array(
                'password_pin'  => md5($global_pass),
                'datemodified'  => $curdate
            );
            
            if( $save_pass      = $this->Model_Member->update_data($id_member, $passdata) ){
                // Send SMS Confirmation
                $this->bgn_sms->sms_cpassword($memberdata->phone, $username, $global_pass);
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => '<button class="close" data-close="alert"></button>Reset/Atur ulang password PIN anggota <strong>'.$username.'</strong> berhasil!',
                );
            }else{
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Reset/Atur ulang password PIN anggota <strong>'.$username.'</strong> tidak berhasil!',
                );
            }
            // JSON encode data
            die(json_encode($data));
            
        }
        
        $current_member         = bgn_get_current_member();
        
        $this->form_validation->set_rules('cur_passpin','Password PIN Lama','required');
        $this->form_validation->set_rules('new_passpin','Pasword PIN Baru','required');
        $this->form_validation->set_rules('cnew_passpin','Konfirmasi Password PIN Baru','required');
        
        if($this->form_validation->run() == FALSE){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Anda memiliki beberapa kesalahan. Silakan cek di formulir bawah ini!',
            );
            // JSON encode data
            die(json_encode($data));
        }else{
            // Set Variable
            $cur_passpin    = bgn_isset($this->input->post('cur_passpin'), '');
            $new_passpin    = bgn_isset($this->input->post('new_passpin'), '');
            $cnew_passpin   = bgn_isset($this->input->post('cnew_passpin'), '');
            
            // Check Member Password
            $check_pass     = ( md5($cur_passpin) == $current_member->password_pin ? TRUE : FALSE );
            
            if ( !$check_pass ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Password PIN lama yang anda masukkan salah!',
                );
                // JSON encode data
                die(json_encode($data));
            }else{
                if( $new_passpin != $cnew_passpin ){
                    // Set JSON data
                    $data = array(
                        'message'   => 'error',
                        'data'      => '<button class="close" data-close="alert"></button>Konfirmasi password tidak sesuai dengan password baru!',
                    );
                    // JSON encode data
                    die(json_encode($data));
                }else{
                    $curdate            = date("Y-m-d H:i:s");
                    $passpindata        = array(
                        'password_pin'  => md5($new_passpin),
                        'datemodified'  => $curdate,
                    );
                    
                    if( $save_pass      = $this->Model_Member->update_data($current_member->id, $passpindata) ){
                        // Set JSON data
                        $data = array(
                            'message'   => 'success',
                            'data'      => '<button class="close" data-close="alert"></button>Validasi formulir Anda berhasil! Data Password PIN Anda sudah diubah.',
                        );
                    }else{
                        // Set JSON data
                        $data = array(
                            'message'   => 'error',
                            'data'      => '<button class="close" data-close="alert"></button>Validasi formulir Anda tidak berhasil! Silahkan periksa kembali data formulir Anda!',
                        );
                    }
                    // JSON encode data
                    die(json_encode($data));
                }
            }
        }
    }
    
    /**
	 * Reset / forget Password function tambahan baru.
	 */
    function resetpassword()
    {
        $username           = trim( strtolower( bgn_isset($this->input->post('username'), '') ) );
        $email              = trim( bgn_isset($this->input->post('email'), '') );
        
        $this->form_validation->set_rules('username','Username','required');
        $this->form_validation->set_rules('email','Email','required');
        
        $this->form_validation->set_message('required', '%s is required');
        $this->form_validation->set_error_delimiters('', '');

        // -------------------------------------------------
        // Check Form Validation
        // -------------------------------------------------
        if( $this->form_validation->run() == FALSE){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<strong>Error Validation!</strong><br />'.validation_errors().'',
            ); die(json_encode($data));
        }
        
        $checkmember        = $this->Model_Member->get_member_by('login',strtolower($username));
        if( !$checkmember || empty($checkmember) ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<strong>Error Validation!</strong><br />Wrong username or not exist',
            ); die(json_encode($data));
        }
        
        if( trim($email) != $checkmember->email ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<strong>Error Validation!</strong><br />Email is not match or empty',
            ); die(json_encode($data));
        }
        
        if( $checkmember->status != 1 ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<strong>Member Status!</strong><br />Member is not active or freeze status',
            ); die(json_encode($data));
        }
        
        // Begin Reset New Password of Member
        $this->db->trans_begin();
        
        $newpass            = bgn_generate_rand_string(6);
        $datapass           = new stdClass();
        $datapass->newpass  = $newpass;
        $datapass->email    = $checkmember->email;
        $datapass->username = $checkmember->username;
        
        $trans_reset_pass   = FALSE;
        $datapassmember     = array(
            'password'      => md5($newpass),
            'datemodified'  => date('Y-m-d H:i:s')
        );
        $updatepassmember   = $this->Model_Member->update_data($checkmember->id, $datapassmember);
        if( !$updatepassmember ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<strong>Reset Process!</strong><br />Reset password is not success',
            ); die(json_encode($data));
        }
        $trans_reset_pass   = TRUE;

        if( $trans_reset_pass ){
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
            }else{
                $this->db->trans_commit();      
                $this->bgn_sms->sms_respassword($checkmember->phone, $checkmember->username, $newpass);          
                $this->bgn_email->send_email_reset_password($datapass);
                
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => '<strong>Success!</strong><br />Please check email to get new password',
                ); die(json_encode($data));
            }
        }else{
            $this->db->trans_rollback();
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<strong>Reset Process!</strong><br />Reset password is not success',
            ); die(json_encode($data));
        }
    }
    
    /**
	 * List Member function.
	 */
    function lists()
    {
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        
        $data['title']          = TITLE . 'Daftar Anggota';
        $data['member']         = $current_member;
        $data['main_content']   = 'lists';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * List Data Member function.
	 */
    function listsdata()
    {
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $condition          = 'WHERE %type% = 1 AND %status% = 1';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        
        $sAction            = bgn_isset($_REQUEST['sAction'],'');
        $sEcho              = intval($_REQUEST['sEcho']);
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;

        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_sponsor          = bgn_isset($this->input->post('search_sponsor'), '');
        $s_upline           = bgn_isset($this->input->post('search_upline'), '');
        $s_type             = bgn_isset($this->input->post('search_type'), '');
        $s_package          = bgn_isset($this->input->post('search_package'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_username) )       { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )           { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_sponsor) )        { $condition .= str_replace('%s%', $s_sponsor, ' AND %sponsor_username% LIKE "%%s%%"'); }
        if( !empty($s_upline) )         { $condition .= str_replace('%s%', $s_upline, ' AND %upline_username% LIKE "%%s%%"'); }
        if( !empty($s_type) )           { $condition .= str_replace('%s%', $s_type, ' AND %as_stockist% = '.($s_type=='stockist' ? 1 : 0).''); }
        if( !empty($s_package) )        { $condition .= str_replace('%s%', $s_package, ' AND %package% LIKE "%s%"'); }
        
        if ( !empty($s_date_min) )      { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if ( !empty($s_date_max) )      { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        
        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%sponsor% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%upline% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%as_stockist% ' . $sort; }
        elseif( $column == 6 )  { $order_by .= '%package% ' . $sort; }
        elseif( $column == 7 )  { $order_by .= '%datecreated% ' . $sort; }
        
        $member_list        = $this->Model_Member->get_all_member_data($limit, $offset, $condition, $order_by);

        $records            = array();
        $records["aaData"]  = array();
        
        if( !empty($member_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            
            $i = $offset + 1;
            foreach($member_list as $row){          
                if( $row->type != 2 ){
                    $sponsor        = ( $row->sponsor != 1 ? '<a href="'.base_url('profile/'.$row->sponsor).'">' . $row->sponsor_username . '</a>' : $row->sponsor_username );
                    $upline         = ( $row->parent != 1 ? '<a href="'.base_url('profile/'.$row->parent).'">' . $row->upline_username . '</a>' : $row->upline_username );
                    $tree           = '<a href="'.base_url('member/tree/'.$row->id).'" class="btn btn-xs btn-primary"><i class="fa fa-search"></i> Lihat Tree</a>';
                    $stockist       = ( $row->as_stockist == 0 ? ' <a href="'.base_url('member/asstockist/'.$row->id).'" class="btn btn-xs btn-primary asstockist"><i class="fa fa-archive"></i> Stockist</a>' : ' <a href="'.base_url('member/asmember/'.$row->id).'" class="btn btn-xs btn-success asmember"><i class="fa fa-user"></i> Member</a>' );
                    $status         = ( $row->as_stockist == 1 ? '<span class="text-success"><strong>STOCKIST</strong></span>' : '<strong>MEMBER</strong>' );
                    
                    if($row->package == PACKAGE_SAPPHIRE)           { $package = '<center><span class="label label-sm sapphire">'.strtoupper($row->package).'</span></center>'; }
                    elseif($row->package == PACKAGE_RUBY)           { $package = '<center><span class="label label-sm ruby">'.strtoupper($row->package).'</span></center>'; }
                    elseif($row->package == PACKAGE_DIAMOND)        { $package = '<center><span class="label label-sm diamond">'.strtoupper($row->package).'</span></center>'; }
                    elseif($row->package == PACKAGE_BLACK_DIAMOND)  { $package = '<center><span class="label label-sm blackdiamond">'.strtoupper($row->package).'</span></center>'; }
                    elseif($row->package == PACKAGE_CASH_REWARD)    { $package = '<center><span class="label label-sm cashreward">'.strtoupper($row->package).'</span></center>'; }
                    
					$records["aaData"][] = array(
                        '<center>'.$i.'</center>',
                        '<center><a href="'.base_url('profile/'.$row->id).'">' . $row->username . '</a></center>',
                        ( $row->as_stockist == 1 ? '<span class="text-success"><strong>' .strtoupper($row->name). '</strong></span>' : strtoupper($row->name) ),
                        '<center>'.$sponsor.'</center>',
                        '<center>'.$upline.'</center>',
                        '<center>'.$status.'</center>',
                        '<center>'.$package.'</center>',
                        '<center>'.$row->datecreated.'</center>',
                        '<center>'.$tree. ( $is_admin ? $stockist : '' ).'</center>',
                    );
                    $i++;
                }
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if( $sAction == 'export_excel' ){
            $order_by .= '%datecreated% ' . 'DESC';
            $data_list                      = $this->Model_Member->get_all_member_data(0, 0, $condition, $order_by);
            $export                         = $this->bgn_excel->member_export($data_list, true);
            
            $records["sStatus"]             = "EXPORTED"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]            = $export; // pass custom message(useful for getting status of group actions)
        }

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * As Stockist function.
	 */
    function asstockist( $id=0 ){
        auth_redirect();
        
        if( !$id ) { echo 'failed'; die(); }

        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if( !$is_admin ) { echo 'failed'; die(); }
        
        $memberdata             = $this->Model_Member->get_memberdata($id);
        
        if( !$memberdata ) { echo 'failed'; die(); }
        
        $datamember             = array('as_stockist' => 1);

        if( $this->Model_Member->update_data($id, $datamember) ){
            echo 'success'; die();
        }else{
            echo 'failed'; die();
        }
    }
    
    /**
	 * As Member function.
	 */
    function asmember( $id=0 ){
        auth_redirect();
        
        if( !$id ) { echo 'failed'; die(); }

        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if( !$is_admin ) { echo 'failed'; die(); }
        
        $memberdata             = $this->Model_Member->get_memberdata($id);
        
        if( !$memberdata ) { echo 'failed'; die(); }
        
        $datamember             = array('as_stockist' => 0);

        if( $this->Model_Member->update_data($id, $datamember) ){
            echo 'success'; die();
        }else{
            echo 'failed'; die();
        }
    }
    
    /**
	 * Tree Member function.
	 */
    function tree( $id=0 )
    {
        auth_redirect();
        
        $member_data            = '';
        $message                = '';
        $is_down                = false;
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id);
            
            if ( !$is_admin ){
                $is_down        = $this->Model_Member->get_is_downline($id, $current_member->tree);
                if ( !$is_down ) 
                    $message    = "ID Member anggota <strong>".$member_data->username."</strong> bukan jaringan Anda. Silahkan cari anggota lainnya!";      
            }else{
                $is_down        = true;
            }
        }
            
        $id_member              = ( $id > 0 ? $id : $current_member->id );
            
        $data['title']          = TITLE . 'Pohon Jaringan';
        $data['member']         = $current_member;
        $data['member_other']   = $member_data;
        $data['message']        = $message;
        $data['is_down']        = $is_down;
        $data['is_admin']       = $is_admin;
        $data['main_content']   = 'tree';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Tree Search Member function.
	 */
    function treesearch()
    {
        $current_member         = bgn_get_current_member();
        $username               = bgn_isset($this->input->post('username'), '');
        $member                 = $this->Model_Member->get_member_by('login', strtolower($username));
        
        if( !empty($member) ){
            $is_downline        = $this->Model_Member->get_is_downline($member->id, $current_member->tree);
            
            if( $is_downline ){
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => base_url('member/tree/' . $member->id),
                );
            }else{
                // Set JSON data
                $data = array(
                    'message'   => 'failed',
                    'data'      => '<button class="close" data-close="alert"></button>Username Anggota ini bukan jaringan Anda.',
                );
            }
        }elseif( empty($username) ){
            // Set JSON data
            $data = array(
                'message'       => 'failed',
                'data'          => '<button class="close" data-close="alert"></button>Username Anggota harus di isi. Silahkan masukkan Username Anggota',
            );
        }else{
            // Set JSON data
            $data = array(
                'message'       => 'failed',
                'data'          => '<button class="close" data-close="alert"></button>Username Anggota tidak ditemukan atau belum terdaftar.',
            );
        }
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * New Member function.
	 */
    function newmember()
    {
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        $data['title']          = TITLE . 'Anggota Baru';
        $data['member']         = $current_member;
        $data['areafee']        = config_item('areafee');
        $data['is_admin']       = $is_admin;
        $data['main_content']   = 'register';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
	/**
	 * New Member Register function.
	 */
    function newmemberreg()
    {
        $xhr = TRUE; // if we decide to use XHR ajax
    	
    	// Saving new member
    	if ($xhr) {
    		header( 'Content-type: text/html; charset=utf-8' );
			if (ob_get_level() == 0) ob_start();
			
			bgn_flush('process-0');
    	}
        
        // Set Variable
        // -------------------------------------------------
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        $pin_id                 = 0;
        $sponsordata            = '';
        $uplinedata             = '';
        
        $package                = trim( bgn_isset($this->input->post('reg_member_package'), '') );
        $username               = trim( bgn_isset($this->input->post('reg_member_username'), '') );
        $password               = trim( bgn_isset($this->input->post('reg_member_password'), '') );
        $name                   = trim( bgn_isset($this->input->post('reg_member_name'), '') );
        $email                  = trim( bgn_isset($this->input->post('reg_member_email'), '') );
        $address                = trim( bgn_isset($this->input->post('reg_member_address'), '') );
        $city                   = trim( bgn_isset($this->input->post('reg_member_city'), '') );
        $phone                  = trim( bgn_isset($this->input->post('reg_member_phone'), '') );
        $idcard                 = trim( bgn_isset($this->input->post('reg_member_idcard'), '') );
        $npwp					= trim( bgn_isset($this->input->post('reg_member_npwp'), '') );
        $bbpin                  = trim( bgn_isset($this->input->post('reg_member_bbpin'), '') );
        $bank                   = trim( bgn_isset($this->input->post('reg_member_bank'), '') );
        $bill                   = trim( bgn_isset($this->input->post('reg_member_bill'), '') );
        $bill_name              = trim( bgn_isset($this->input->post('reg_member_bill_name'), '') );
        $branch                 = trim( bgn_isset($this->input->post('reg_member_branch'), '') );
        
        $this->form_validation->set_rules('reg_member_password','Password','required');
        $this->form_validation->set_rules('reg_member_name','Nama Anggota','required');
        $this->form_validation->set_rules('reg_member_email','Alamat Email','required');
        $this->form_validation->set_rules('reg_member_address','Alamat','required');
        $this->form_validation->set_rules('reg_member_city','Kota','required');
        $this->form_validation->set_rules('reg_member_phone','No.Telp/HP','required');
        $this->form_validation->set_rules('reg_member_bank','Bank','required');
        $this->form_validation->set_rules('reg_member_bill','Nomor Rekening Bank','required');
        $this->form_validation->set_rules('reg_member_bill_name','Pemilik No. Rekening Bank','required');
        
        $this->form_validation->set_message('required', '%s harus di isi');
        $this->form_validation->set_error_delimiters('', '');

        // -------------------------------------------------
        // Check Form Validation
        // -------------------------------------------------
        if( $this->form_validation->run() == FALSE){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => array(
                    'field' => '',
                    'msg'   => '<button class="close" data-close="alert"></button>Pendaftaran anggota baru tidak berhasil. '.validation_errors().''
                )
            ); die(json_encode($data));
        }
        
        // -------------------------------------------------
        // Check NPWP
        // -------------------------------------------------
        if ( $npwp == '__.___.___._-___.___' ) $npwp = '';
        
        // -------------------------------------------------
        // Check Package
        // -------------------------------------------------
        if( !$package || empty($package) ){
            // Rollback Transaction
            sleep(1);
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => array(
                    'field' => '',
                    'msg'   => '<button class="close" data-close="alert"></button>Silahkan pilih paket pendaftaran Anda.'
                )
            ); die(json_encode($data));
        }
        
        // -------------------------------------------------
        // Begin Transaction
        // -------------------------------------------------
        $this->db->trans_begin();
        
        // -------------------------------------------------
        // Check Sponsor
        // -------------------------------------------------
        $sponsordata            = $current_member;
        $sponsor_id             = $current_member->id;
        $sponsor_username       = $current_member->username;
        $sponsor_sponsor        = $current_member->sponsor;
        $sponsor_phone          = $current_member->phone;
        
        if( bgn_isset($this->input->post('sponsored'), '') == 'other_sponsor' ){
            $sponsor            = bgn_isset($this->input->post('reg_member_sponsor'), '');
            $sponsordata        = $this->Model_Member->get_member_by('login', strtolower($sponsor));
            if( !$sponsordata ){
                // Rollback Transaction
                $this->db->trans_rollback(); sleep(1);
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => array(
                        'field' => 'sponsor',
                        'msg'   => '<button class="close" data-close="alert"></button>Username anggota sponsor invalid! Silahkan masukkan Username anggota sponsor yang lain!',
                    )
                ); die(json_encode($data));
            }

            if( $sponsordata->id != 1 ){
                $is_sp_downline     = $this->Model_Member->get_is_downline($sponsordata->id, $current_member->tree);
                if( !$is_sp_downline ){
                    // Rollback Transaction
                    $this->db->trans_rollback(); sleep(1);
                    // Set JSON data
                    $data = array(
                        'message'   => 'error',
                        'data'      => array(
                            'field' => '',
                            'msg'   => '<button class="close" data-close="alert"></button>Username anggota sponsor ini bukan jaringan Anda! Silahkan masukkan Username anggota sponsor yang lain!'
                        )
                    ); die(json_encode($data));
                }
            }

            $sponsor_id         = $sponsordata->id;
            $sponsor_username   = $sponsordata->username;
            $sponsor_sponsor    = $sponsordata->sponsor;
            $sponsor_phone      = $sponsordata->phone;
        }

        // -------------------------------------------------
        // Check Placement Position
        // -------------------------------------------------
        if( bgn_isset($this->input->post('reg_upline_id'), 0) ){
            // -------------------------------------------------
            // Set Upline ID
            // -------------------------------------------------
            $upline_id              = trim( bgn_isset($this->input->post('reg_upline_id'), 0) );
            $position               = trim( bgn_isset($this->input->post('reg_member_position'), '') );
            
            // -------------------------------------------------
            // Check Position
            // -------------------------------------------------
            if( empty($position) ){
                // Rollback Transaction
                $this->db->trans_rollback(); sleep(1);
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => array(
                        'field' => '',
                        'msg'   => '<button class="close" data-close="alert"></button>Kedua kaki upline ini sudah terisi.'
                    )
                ); die(json_encode($data));
            }
            
            $position_exist     = bgn_check_node($upline_id, $position);
            if( $position_exist || !empty($position_exist) ){
                // Rollback Transaction
                $this->db->trans_rollback(); sleep(1);
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => array(
                        'field' => '',
                        'msg'   => '<button class="close" data-close="alert"></button>Posisi tidak tersedia di bawah sponsor ini.'
                    )
                ); die(json_encode($data));
            }
        }
        
        // -------------------------------------------------
        // Check If Upline is Downline
        // -------------------------------------------------
        if( !$is_admin ){
            $is_downline        = $this->Model_Member->get_is_downline($upline_id, $current_member->tree);
            if( !$is_downline ){
                // Rollback Transaction
                $this->db->trans_rollback(); sleep(1);
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => array(
                        'field' => 'upline',
                        'msg'   => '<button class="close" data-close="alert"></button>Username anggota ini bukan jaringan Anda! Silahkan masukkan Username anggota lain!'
                    )
                ); die(json_encode($data));
            }
        }

        // -------------------------------------------------
        // Check PIN
        // -------------------------------------------------
        if( bgn_isset($this->input->post('pin')) == 'pin' ){
            $pin_id             = bgn_isset($this->input->post('reg_member_pin'));
            $pin                = $this->Model_Member->get_pin_by_id($pin_id);
            if( !$pin || empty($pin) ){
                // Rollback Transaction
                $this->db->trans_rollback(); sleep(1);
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => array(
                        'field' => '',
                        'msg'   => '<button class="close" data-close="alert"></button>PIN belum tersedia. Silahkan pesan PIN terlebih dahulu!',
                    )
                ); die(json_encode($data));
            }
            
            if( $pin->status == 0 ){
                // Rollback Transaction
                $this->db->trans_rollback(); sleep(1);
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => array(
                        'field' => '',
                        'msg'   => '<button class="close" data-close="alert"></button>PIN belum aktif. Silahkan hubungi administrator untuk mengaktifkan PIN!',
                    )
                ); die(json_encode($data));
            }
            
            if( $pin->status == 2 ){
                // Rollback Transaction
                $this->db->trans_rollback(); sleep(1);
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => array(
                        'field' => '',
                        'msg'   => '<button class="close" data-close="alert"></button>PIN sudah digunakan. Silahkan gunakan PIN aktif lainnya!',
                    )
                ); die(json_encode($data));
            }
            
            $pin_id             = $pin->id;
        }
        
        // -------------------------------------------------
        // Check Username
        // -------------------------------------------------
        $check_username         = bgn_check_username($username);
        if( $check_username == 'invalid' ){
            // Rollback Transaction
            $this->db->trans_rollback(); sleep(1);
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => array(
                    'field' => '',
                    'msg'   => '<button class="close" data-close="alert"></button>Username tidak sesuai dengan kriteria.',
                )
            ); die(json_encode($data));
        }elseif( $check_username == 'notavailable' ){
            // Rollback Transaction
            $this->db->trans_rollback(); sleep(1);
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => array(
                    'field' => '',
                    'msg'   => '<button class="close" data-close="alert"></button>Username tidak dapat digunakan karena sudah terdaftar.',
                )
            ); die(json_encode($data));
        }
        
        // -------------------------------------------------
        // Set Data Member
        // -------------------------------------------------
        $upline                 = $this->Model_Member->get_memberdata($upline_id);
        $password               = ( !empty($password) ? $password : bgn_generate_password(6,TRUE) );
        $unique                 = bgn_generate_unique();
        $investment             = config_item('investment');
        $datetime               = date('Y-m-d H:i:s');
        $data_member            = array(
            'username'          => strtolower($username),
            'password'          => md5($password),
            'password_pin'      => md5($password),
            'name'              => strtoupper($name),
            'email'             => $email,
            'sponsor'           => $sponsor_id,
            'parent'            => $upline_id,
            'position'          => $position,
            'status'            => ( $pin_id > 0 || $is_admin ? 1 : 0 ),
            'last_login'        => $datetime,
            'address'           => strtoupper($address),
            'city'              => strtoupper($city),
            'phone'             => $phone,
            'bbpin'             => $bbpin,
            'bank'              => $bank,
            'bill'              => $bill,
            'bill_name'         => strtoupper($bill_name),
            'branch'            => strtoupper($branch),
            'idcard'            => $idcard,
            'npwp'				=> $npwp,
            'package_old'       => $package,
            'package'           => $package,
            'uniquecode'        => $unique,
            'nominal'           => absint($investment[$package]),
            'datecreated'       => $datetime,
            'datemodified'      => $datetime,
        );

        // -------------------------------------------------
        // Save Member
        // -------------------------------------------------
        $trans_save_member      = FALSE;
        $trans_update_tree  	= FALSE;
		
        if( $member_save_id     = $this->Model_Member->save_data($data_member) ){
            $trans_save_member  = TRUE;
            
            // Get member/downline data
            // -------------------------------------------------
            $downline           = bgn_get_memberdata_by_id($member_save_id);
            
            // Update Member Tree
            // -------------------------------------------------
            $tree               = bgn_generate_tree($member_save_id, $upline->tree);
            $data_tree          = array('tree' => $tree);
            $update_tree        = $this->Model_Member->update_data($member_save_id, $data_tree);
            
            if( !$update_tree ){
                // Rollback Transaction
                $this->db->trans_rollback(); sleep(1);
                // Set JSON data
                $data = array(
                    'message'       => 'error',
                    'data'          => array(
                        'field'     => '',
                        'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran tidak berhasil. Terjadi kesalahan data.'
                    )
                ); die(json_encode($data));
            }
            $trans_update_tree  = TRUE;
            
            // Update PIN Used
            // -------------------------------------------------
            if ( $pin_id > 0 ) {
                $update_pin          = $this->Model_Member->update_pin_used($pin_id, $member_save_id, $current_member->id);
                if( !$update_pin ){
                    // Rollback Transaction
                    $this->db->trans_rollback(); sleep(1);
                    // Set JSON data
                    $data = array(
                        'message'   => 'error',
                        'data'      => array(
                            'field' => '',
                            'msg'   => '<button class="close" data-close="alert"></button>Pendaftaran tidak berhasil. Terjadi kesalahan data',
                        )
                    ); die(json_encode($data));
                }
            }
        }else{
            // Rollback Transaction
            $this->db->trans_rollback(); sleep(1);
            // Set JSON data
            $data = array(
                'message'       => 'error',
                'data'          => array(
                    'field'     => '',
                    'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran tidak berhasil. Terjadi kesalahan data.'
                )
            ); die(json_encode($data));
        }

        // -------------------------------------------------
        // Save Member Confirmation
        // -------------------------------------------------
        $trans_save_memberconf  = FALSE;
        $data_member_confirm    = array(
            'id_member'         => $current_member->id,
            'member'            => $current_member->username,
            'id_sponsor'        => $sponsor_id,
            'sponsor'           => $sponsor_username,
            'id_downline'       => $member_save_id,
            'downline'          => $downline->username,
            'status'            => ( $pin_id > 0 || $is_admin ? 1 : 0 ),
            'access'            => ( $pin_id > 0 ? 'pin' : 'admin' ),
            'package_old'       => $package,
            'package'           => $package,
            'datecreated'       => $datetime,
            'datemodified'      => $datetime,
        );
		
        $insert_member_confirm  = $this->Model_Member->save_data_confirm($data_member_confirm);
        if( !$insert_member_confirm ){
            // Rollback Transaction
            $this->db->trans_rollback(); sleep(1);
            // Set JSON data
            $data = array(
                'message'       => 'error',
                'data'          => array(
                    'field'     => '',
                    'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran tidak berhasil. Terjadi kesalahan data.'
                )
            ); die(json_encode($data));
        }
        $trans_save_memberconf  = TRUE;
        
        // -------------------------------------------------
		// Updating member tree 
        // ------------------------------------------------- 
        if ($xhr) bgn_flush('1');
		bgn_update_member_tree($member_save_id);
        
        // -------------------------------------------------
		// Calculate Bonus Sponsor, Bonus Matching, Bonus Input and Auto Global Reward 
        // -------------------------------------------------
		if ($xhr) bgn_flush('2');
        $trans_insert_bonus     = FALSE;
        if( !$is_admin ){
            bgn_count_input_bonus($member_save_id, $current_member->id);
        }
        
        
        
        if( $sponsor_id != 1 ){
            bgn_count_sponsor_bonus($member_save_id, $sponsor_id);
            bgn_count_matching_bonus($member_save_id, $sponsor_id);
            bgn_check_autoglobal_reward($sponsor_id);
        }
        
        // -------------------------------------------------
		// Calculate Update Poin and Bonus Pairing 
        // ------------------------------------------------- 
        if ($xhr) bgn_flush('3');
        bgn_update_poin($upline->id, $member_save_id, $position);
        if( $upline->id != 1 ){
            bgn_count_pair_bonus($upline->id, $datetime, FALSE, FALSE, TRUE);
        }
        $trans_insert_bonus     = TRUE;
        
        // -------------------------------------------------
		// Reward Process 
        // ------------------------------------------------- 
        if ($xhr) bgn_flush('4');
        $trans_reward_process   = FALSE;
        bgn_reward($upline->id,TRUE, FALSE, TRUE);
        bgn_cashreward($upline->id,TRUE, FALSE, TRUE);
        $trans_reward_process   = TRUE;

        // -------------------------------------------------
		// Process Auto Global Reward 
        // -------------------------------------------------
        if( !bgn_auto_global_process($member_save_id) ){
            // Rollback Transaction
            $this->db->trans_rollback(); sleep(1);
            // Set JSON data
            $data = array(
                'message'       => 'error',
                'data'          => array(
                    'field'     => '',
                    'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran tidak berhasil. Proses Auto Global terjadi kesalahan data.'
                )
            ); die(json_encode($data));
        }
        
        // -------------------------------------------------
        // Commit or Rollback Transaction
        // -------------------------------------------------
        if( $trans_save_member && $trans_update_tree && $trans_save_memberconf && $trans_insert_bonus && $trans_reward_process ){
            if ($this->db->trans_status() === FALSE){
                // Rollback Transaction
                $this->db->trans_rollback(); sleep(1);
                // Set JSON data
                $data = array(
                    'message'       => 'error',
                    'data'          => array(
                        'field'     => '',
                        'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran tidak berhasil. Terjadi kesalahan data.'
                    )
                ); die(json_encode($data));
            }else{
                if ($xhr) bgn_flush('5'); sleep(1);
                // Commit Transaction
                $this->db->trans_commit();
                // Send Email to Downline
                //$this->bgn_email->send_email_downline($downline, $sponsordata, $password);
                // Send Email to Sponsor
                //$this->bgn_email->send_email_sponsor($downline, $sponsordata, $password);
                // Send SMS Notification to Downline
                //$this->bgn_sms->sms_newmember($downline->phone,$password,$username);
                // Send SMS Notification to Sponsor
                //$this->bgn_sms->sms_newmember_spon($sponsordata->phone,$downline->name,$username);
                // Set JSON data
                $memberinfo = '<strong>Username :</strong>' . $username . '<br /><strong>Password :</strong>' . $password . '';
                $data       = array('message' => 'success','data' => array('msg' => 'success', 'memberinfo' => $memberinfo)); die(json_encode($data));
            }
        }else{
            // Rollback Transaction
            $this->db->trans_rollback(); sleep(1);
            // Set JSON data
            $data = array(
                'message'       => 'error',
                'data'          => array(
                    'field'     => '',
                    'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran tidak berhasil. Terjadi kesalahan data.'
                )
            ); die(json_encode($data));
        }
    }
    
    /**
	 * New Member Register function.
	 */
    function newmemberregrep()
    {
        // This is for AJAX request
    	if ( ! $this->input->is_ajax_request() ) exit('No direct script access allowed');

        // Set Variable
        // -------------------------------------------------
        $sponsordata            = '';
        
        $package                = bgn_isset($this->input->post('rep_member_package'), '');
        $username               = bgn_isset($this->input->post('rep_member_username'), '');
        $password               = bgn_isset($this->input->post('rep_member_password'), '');
        $name                   = bgn_isset($this->input->post('rep_member_name'), '');
        $email                  = bgn_isset($this->input->post('rep_member_email'), '');
        $address                = bgn_isset($this->input->post('rep_member_address'), '');
        $city                   = bgn_isset($this->input->post('rep_member_city'), '');
        $phone                  = bgn_isset($this->input->post('rep_member_phone'), '');
        $bbpin                  = bgn_isset($this->input->post('rep_member_bbpin'), '');
        $idcard                 = bgn_isset($this->input->post('rep_member_idcard'), '');
        $bank                   = bgn_isset($this->input->post('rep_member_bank'), '');
        $bill                   = bgn_isset($this->input->post('rep_member_bill'), '');
        $bill_name              = bgn_isset($this->input->post('rep_member_bill_name'), '');
        $branch                 = bgn_isset($this->input->post('rep_member_branch'), '');

        $this->form_validation->set_rules('rep_member_name','Nama Anggota','required');
        $this->form_validation->set_rules('rep_member_username','Username','required');
        $this->form_validation->set_rules('rep_member_password','Password','required');
        $this->form_validation->set_rules('rep_member_email','Email','required|valid_email');
        $this->form_validation->set_rules('rep_member_address','Alamat','required');
        $this->form_validation->set_rules('rep_member_city','Kota','required');
        $this->form_validation->set_rules('rep_member_phone','Telp/HP','required|numeric');
        $this->form_validation->set_rules('rep_member_bank','Nama Bank','required');
        $this->form_validation->set_rules('rep_member_bill','Rekening Bank','required|numeric');
        $this->form_validation->set_rules('rep_member_bill_name','Pemilik No. Rekening Bank','required');
        
        $this->form_validation->set_message('required', '%s harus di isi');
        $this->form_validation->set_error_delimiters('', '');
		
		// Check captcha
		$captcha_response 	= $this->input->post('g-recaptcha-response');
		$ip_address 		= $this->input->ip_address();
		$secret_key 		= config_item('captcha_secret_key');

		$verify_url			= 'https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s';
		$verify_url			= sprintf($verify_url, $secret_key, $captcha_response, $ip_address);
        
		$captcha_verify 	= url_get_contents($verify_url);
		$captcha_verify		= json_decode($captcha_verify);

		if( empty($captcha_verify->success) ) {
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => array(
                    'field' => '',
                    'msg'   => '<button class="close" data-close="alert"></button>Captcha tidak berhasil diverifikasi!'
                )
            ); die(json_encode($data));
		}

        if( $this->form_validation->run() == FALSE){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => array(
                    'field' => '',
                    'msg'   => '<button class="close" data-close="alert"></button>Pendaftaran anggota baru tidak berhasil. '.validation_errors().''
                )
            ); die(json_encode($data));
        }else{
            // Begin Transaction
            // -------------------------------------------------
            $this->db->trans_begin();
            
            // Check Sponsor
            // -------------------------------------------------
            $sponsor                = bgn_isset($this->input->post('rep_member_sponsor'), 0);
            $sponsordata            = $this->Model_Member->get_memberdata($sponsor);
            if( !$sponsordata ){
                // Rollback Transaction
                $this->db->trans_rollback();
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => array(
                        'field' => '',
                        'msg'   => '<button class="close" data-close="alert"></button>Member ID sponsor invalid! Silahkan masukkan member ID sponsor lainnya.'
                    )
                ); die(json_encode($data));
            }
    
            $sponsor_id             = $sponsordata->id;
            $sponsor_username       = $sponsordata->username;
            $sponsor_sponsor        = $sponsordata->sponsor;
            $sponsor_tree           = $sponsordata->tree;
            
            // -------------------------------------------------
            // Check Username
            // -------------------------------------------------
            $check_username         = bgn_check_username($username);
            if( $check_username == 'invalid' ){
                // Rollback Transaction
                $this->db->trans_rollback();
                // Set JSON data
                $data = array(
                    'message'       => 'error',
                    'data'          => array(
                        'field'     => '',
                        'msg'       => '<button class="close" data-close="alert"></button>Username tidak sesuai dengan kriteria.',
                    )
                ); die(json_encode($data));
            }elseif( $check_username == 'notavailable' ){
                // Rollback Transaction
                $this->db->trans_rollback();
                // Set JSON data
                $data = array(
                    'message'       => 'error',
                    'data'          => array(
                        'field'     => '',
                        'msg'       => '<button class="close" data-close="alert"></button>Username tidak dapat digunakan karena sudah terdaftar.',
                    )
                ); die(json_encode($data));
            }
            
            // Set Data Member
            // -------------------------------------------------
            $password               = !empty($password) ? $password : bgn_generate_password(6);
            $unique                 = bgn_generate_unique();
            $investment             = config_item('investment');
            $datetime               = date('Y-m-d H:i:s');
            $data_member            = array(
                'username'          => strtolower($username),
                'password'          => $password,
                'password_pin'      => $password,
                'name'              => strtoupper($name),
                'email'             => $email,
                'sponsor'           => $sponsor_id,
                'position'          => strtolower(bgn_generate_password(10)),
                'status'            => 0,
                'last_login'        => $datetime,
                'address'           => strtoupper($address),
                'city'              => strtoupper($city),
                'phone'             => $phone,
                'bbpin'             => $bbpin,
                'bank'              => $bank,
                'bill'              => $bill,
                'bill_name'         => strtoupper($bill_name),
                'branch'            => strtoupper($branch),
                'idcard'            => $idcard,
                'package_old'       => $package,
                'package'           => $package,
                'uniquecode'        => $unique,
                'nominal'           => absint($investment[$package]),
                'datecreated'       => $datetime,
                'datemodified'      => $datetime,
            );
            
            // Save Member
            // -------------------------------------------------
            $trans_save_member      = FALSE;
            if( $member_save_id     = $this->Model_Member->save_data($data_member) ){
                $trans_save_member  = TRUE;
                
                // Get member/downline data
                // -------------------------------------------------
                $downline           = bgn_get_memberdata_by_id($member_save_id);
            }else{
                // Rollback Transaction
                $this->db->trans_rollback();
                // Set JSON data
                $data = array(
                    'message'       => 'error',
                    'data'          => array(
                        'field'     => '',
                        'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran member baru tidak berhasil! Terjadi kesalahan data member.',
                    )
                ); die(json_encode($data));
            }

            // Save Member Confirmation
            // -------------------------------------------------
            $trans_save_memberconf  = FALSE;
            $data_member_confirm    = array(
                'id_member'         => $sponsor_id,
                'member'            => $sponsor_username,
                'id_sponsor'        => $sponsor_id,
                'sponsor'           => $sponsor_username,
                'id_downline'       => $member_save_id,
                'downline'          => $username,
                'status'            => 0,
                'access'            => 'replika',
                'package_old'       => $package,
                'package'           => $package,
                'datecreated'       => $datetime,
                'datemodified'      => $datetime,
            );

            $insert_member_confirm  = $this->Model_Member->save_data_confirm($data_member_confirm);
            if( !$insert_member_confirm ){
                // Rollback Transaction
                $this->db->trans_rollback();
                // Set JSON data
                $data = array(
                    'message'       => 'error',
                    'data'          => array(
                        'field'     => '',
                        'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran member baru tidak berhasil! Terjadi kesalahan data confirm member.',
                    )
                ); die(json_encode($data));
            }
            $trans_save_memberconf  = TRUE;
    
            if( $trans_save_member && $trans_save_memberconf ){
                if ($this->db->trans_status() === FALSE){
                    // Rollback Transaction
                    $this->db->trans_rollback();
                    // Set JSON data
                    $data = array(
                        'message'       => 'error',
                        'data'          => array(
                            'field'     => '',
                            'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran member baru tidak berhasil! Terjadi kesalahan save data.',
                        )
                    ); die(json_encode($data));
                }else{
                    // Commit Transaction
                    $this->db->trans_commit();
                    // Complete Transaction
                    $this->db->trans_complete();
                    // Send Email to Downline
                    $this->bgn_email->send_email_downline($downline, $sponsordata, $password);
                    // Send Email to Sponsor
                    $this->bgn_email->send_email_sponsor_replika($downline, $sponsordata, $password);
                    // Send SMS Notification to Downline
                    $this->bgn_sms->sms_newmember_rep($downline->phone, $downline->name, bgn_accounting($nominal,'Rp'));
                    // Send SMS Notification to Sponsor
                    $this->bgn_sms->sms_newmember_rep_spon($sponsordata->phone, $downline->name, $downline->phone);
                    // Set JSON data
                    $data = array(
                        'message'       => 'success',
                        'data'          => array(
                            'field'     => '',
                            'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran member baru berhasil!',
                        )
                    ); die(json_encode($data));
                }
            }else{
                // Rollback Transaction
                $this->db->trans_rollback();
                // Set JSON data
                $data = array(
                    'message'       => 'error',
                    'data'          => array(
                        'field'     => '',
                        'msg'       => '<button class="close" data-close="alert"></button>Pendaftaran member baru tidak berhasil! Terjadi kesalahan save data.',
                    )
                ); die(json_encode($data));
            }
        }
    }
    
    /**
	 * Search Upline function.
	 */
    function searchupline()
    {
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        $up_username            = bgn_isset($this->input->post('upline_username'));
        $message                = '';
        $info                   = '';
        
        if( !empty($up_username) ){
            $memberdata         = $this->Model_Member->get_member_by('login', strtolower($up_username));
            
            if( !$memberdata ) { 
                $message        = 'invalid'; 
            }else{
                $node           = bgn_check_node($memberdata->id);
                if( !empty($node) ){
                    $message    = 'available';
                    $info      .= '
                    <input type="hidden" name="reg_upline_id" class="form-control" value="'.$memberdata->id.'" />
                    <div class="form-group">
        				<label class="col-md-3 control-label">Nama&nbsp;&nbsp;&nbsp;&nbsp;</label>
        				<div class="col-md-7">
        					<input type="text" name="reg_member_nama_dsb" class="form-control" placeholder="Nama Anggota" disabled="" value="'.strtoupper($memberdata->name).'" />
        				</div>
        			</div>';
                    
                    if( count($node) > 1 ){
                        $info  .= '
                        <div class="form-group">
    						<label class="col-md-3 control-label">Posisi Tersedia <span class="required">*</span></label>
    						<div class="col-md-7">
								<select class="form-control" name="reg_member_position">';
                                
                                foreach($node as $n){
                                    $info .= '<option value="'.$n.'">'. ucfirst(strtolower($n)) .'</option>';
                                }
                                
                                $info .= '</select>
    						</div>
    					</div>';
                    }elseif( count($node) == 1 ){
                        $info  .= '
                        <div class="form-group">
            				<label class="col-md-3 control-label">Posisi Tersedia&nbsp;&nbsp;&nbsp;</label>
            				<div class="col-md-7">
                                <input type="hidden" name="reg_member_position" class="form-control" value="'.$node.'" />
            					<input type="text" name="reg_member_position_dsb" class="form-control" placeholder="Posisi Anggota" disabled="" value="'.$node.'" />
            				</div>
            			</div>';
                    }
                }else{
                    $message    = 'not_available';
                }
            }
        }
        
        // Set JSON data
        $data = array(
            'message'   => $message,
            'info'      => $info
        );
        
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Search Upline Group function.
	 */
    function searchuplinetree()
    {
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        $id_parent              = bgn_isset($this->input->post('id_parent'));
        $position               = bgn_isset($this->input->post('position'));
        $info                   = '';
        
        if( !empty($id_parent) ){
            $memberdata         = bgn_get_memberdata_by_id($id_parent);
            $node               = ( $position == 'left' ? 'kiri' : 'kanan' );
             
            if( !$memberdata ) { 
                // Set JSON data
                $data = array(
                    'message'   => 'failed',
                    'data'      => '<button class="close" data-close="alert"></button>Member ID upline tidak ditemukan!'
                );
                // JSON encode data
                die(json_encode($data)); 
            }else{
                if( !$is_admin ){
                    $is_down    = $this->Model_Member->get_is_downline($memberdata->id, $current_member->tree);
                    
                    if( !$is_down ){
                        // Set JSON data
                        $data = array(
                            'message'   => 'failed',
                            'data'      => '<button class="close" data-close="alert"></button>Upline member ID ini bukan jaringan Anda!'
                        );
                        // JSON encode data
                        die(json_encode($data));
                    }
                }
                
                $info      .= '
                <input type="hidden" name="reg_upline_id" class="form-control" value="'.$memberdata->id.'" />
                <div class="form-group">
    				<label class="col-md-3 control-label">Member ID&nbsp;&nbsp;&nbsp;</label>
    				<div class="col-md-7">
                        <input type="hidden" name="reg_search_upline" class="form-control" value="'.$memberdata->username.'" />
    					<input type="text" name="reg_upline_username" class="form-control" placeholder="Member ID Upline" disabled="" value="'.$memberdata->username.'" />
    				</div>
    			</div>
                <div class="form-group">
    				<label class="col-md-3 control-label">Nama&nbsp;&nbsp;&nbsp;</label>
    				<div class="col-md-7">
    					<input type="text" name="reg_member_nama_dsb" class="form-control" placeholder="Nama Anggota" disabled="" value="'.strtoupper($memberdata->name).'" />
    				</div>
    			</div>
                <div class="form-group">
    				<label class="col-md-3 control-label">Posisi&nbsp;&nbsp;&nbsp;</label>
    				<div class="col-md-7">
                        <input type="hidden" name="reg_member_position" class="form-control" value="'.$position.'" />
    					<input type="text" name="reg_member_position_dsb" class="form-control" placeholder="Posisi" disabled="" value="'.ucfirst(strtolower($node)).'" />
    				</div>
    			</div>';
                
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => $info
                );
                // JSON encode data
                die(json_encode($data));
            }
        }
        
        // Set JSON data
        $data = array(
            'message'   => 'failed',
            'data'      => '<button class="close" data-close="alert"></button>Member ID upline tidak ditemukan!'
        );
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Search Downline function.
	 */
    function searchdownline()
    {
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        $up_username            = bgn_isset($this->input->post('downline_username'));
        $message                = '';
        $info                   = '';
        
        if( !empty($up_username) ){
            $memberdata         = $this->Model_Member->get_member_by('login', strtolower($up_username));
            
            if( !$memberdata ) { 
                $message        = 'invalid'; 
            }else{
                if( $is_admin ){
                    if($current_member->id != $memberdata->id){
                        if($memberdata->as_stockist == 0){
                            // Set JSON data
                            $data = array(
                                'message'   => 'not_stockist',
                                'info'      => $info
                            );
                            // JSON encode data
                            die(json_encode($data));
                        } 
                    }
                }
                
                if( !$is_admin ){
                    $is_admin_down      = as_administrator($memberdata);
                    if( $is_admin_down ){
                        // Set JSON data
                        $data = array(
                            'message'   => 'admin',
                            'info'      => $info
                        );
                        // JSON encode data
                        die(json_encode($data));
                    }
                }
                
                if($current_member->id == $memberdata->id){
                    // Set JSON data
                    $data = array(
                        'message'   => 'self',
                        'info'      => $info
                    );
                    // JSON encode data
                    die(json_encode($data));
                }
                
                $message    = 'available';
                $info      .= '
                <input type="hidden" name="pin_downline_id" class="form-control" value="'.$memberdata->id.'" />
                <input type="hidden" name="downline_id" class="form-control" value="'.$memberdata->id.'" />
                <div class="form-group">
    				<label class="col-md-3 control-label">Nama&nbsp;&nbsp;&nbsp;</label>
    				<div class="col-md-7">
    					<input type="text" name="pin_member_nama_dsb" class="form-control" placeholder="Nama Anggota" disabled="" value="'.strtoupper($memberdata->name).'" />
    				</div>
    			</div>';
            }
        }
        
        // Set JSON data
        $data = array(
            'message'   => $message,
            'info'      => $info
        );
        
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Search Downline function.
	 */
    function searchdownlinestockist()
    {
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        $up_username            = bgn_isset($this->input->post('downline_username'));
        $message                = '';
        $info                   = '';
        
        if( !empty($up_username) ){
            $memberdata         = $this->Model_Member->get_member_by('login', strtolower($up_username));
            
            if( !$memberdata ) { 
                $message        = 'invalid'; 
            }else{
                if($memberdata->id == 1){
                    // Set JSON data
                    $data = array(
                        'message'   => 'admin',
                        'info'      => $info
                    );
                    // JSON encode data
                    die(json_encode($data));
                }
                
                if($current_member->id == $memberdata->id){
                    // Set JSON data
                    $data = array(
                        'message'   => 'self',
                        'info'      => $info
                    );
                    // JSON encode data
                    die(json_encode($data));
                }
                
                $message    = 'available';
                $info      .= '
                <input type="hidden" name="pin_downline_id" class="form-control" value="'.$memberdata->id.'" />
                <input type="hidden" name="downline_id" class="form-control" value="'.$memberdata->id.'" />
                <div class="form-group">
    				<label class="col-md-3 control-label">Nama</label>
    				<div class="col-md-7">
    					<input type="text" name="pin_member_nama_dsb" class="form-control" placeholder="Nama Anggota" disabled="" value="'.strtoupper($memberdata->name).'" />
    				</div>
    			</div>';
            }
        }
        
        // Set JSON data
        $data = array(
            'message'   => $message,
            'info'      => $info
        );
        
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Get Member PIN function.
	 */
    function memberpin($id)
    {
        if( !$id ){
            // Set JSON data
            $data = array(
                'message'   => 'failed',
                'data'      => '<p class="text-warning"><strong>Maaf, Anda tidak memiliki PIN. Silahkan pesan terlebih dahulu!</strong></p>',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $pindata    = bgn_member_pin($id, 'active' );
        $pin        = '';
                                    
        if( !empty($pindata) ){
            $pin   .= '<select class="form-control" name="reg_member_pin">';
                foreach($pindata as $p){
                    $pin .= '<option value="'.$p->id.'">'.$p->id_pin.'</option>';
                }
            $pin   .= '</select>';
        }else{
            $pin   .= '<p class="text-warning"><strong>Maaf, Anda tidak memiliki PIN. Silahkan pesan terlebih dahulu!</strong></p>';
        }
        
        // Set JSON data
        $data = array(
            'message'   => 'success',
            'data'      => $pin,
        );
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Search Sponsor function.
	 */
    function searchsponsor()
    {
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        $spon_username          = bgn_isset($this->input->post('sponsor_username'));
        $upline_id              = bgn_isset($this->input->post('upline_id'));
        $message                = '';
        $info                   = '';
        
        $upline_data            = $upline_id == $current_member->id ? $current_member : bgn_get_memberdata_by_id($upline_id);
        
        
        if( !$upline_data ){
            $message            = 'invalid_upline';
            // Set JSON data
            $data = array(
                'message'   => $message,
                'info'      => $info
            );
        }
        
        if( !empty($spon_username) ){
            $memberdata         = $this->Model_Member->get_member_by('login', strtolower($spon_username));
            
            if( !$memberdata ) { 
                $message        = 'invalid'; 
            }else{
                if( $memberdata->id != 1 ){
                    /*
                    if( !$is_admin ){
                        $is_sp_downline = $this->Model_Member->get_is_downline($memberdata->id, $current_member->tree);    
                    }else{
                        $is_sp_downline = $this->Model_Member->get_is_downline($memberdata->id, $upline_data->tree);    
                    }
                    */
                    
                    
                    if($upline_data->id == $memberdata->id){
                        $message    = 'available';
                        $info      .= '
                        <input type="hidden" name="pin_downline_id" class="form-control" value="'.$memberdata->id.'" />
                        <div class="form-group">
            				<label class="col-md-3 control-label">Sponsor Name&nbsp;&nbsp;&nbsp;&nbsp;</label>
            				<div class="col-md-7">
            					<input type="text" name="reg_member_sponsor_nama_dsb" class="form-control" placeholder="Member Name" disabled="" value="'.strtoupper($memberdata->name).'" />
            				</div>
            			</div>';    
                    }else{
                        $ancestry   = $this->Model_Member->get_ancestry($upline_data->id);
                        $ancestry   = explode(',' ,$ancestry);
                        
                        $i  = 0;
                        foreach($ancestry as $id){
                            if( $memberdata->id == $id){
                                $i++;
                                $message    = 'available';
                                $info      .= '
                                <input type="hidden" name="pin_downline_id" class="form-control" value="'.$memberdata->id.'" />
                                <div class="form-group">
                    				<label class="col-md-3 control-label">Sponsor Name&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    				<div class="col-md-7">
                    					<input type="text" name="reg_member_sponsor_nama_dsb" class="form-control" placeholder="Member Name" disabled="" value="'.strtoupper($memberdata->name).'" />
                    				</div>
                    			</div>';
                            }else{
                                 continue;
                            }
                        }
                        
                        if( $i == 0 ){
                            $message    = 'error';    
                        }    
                    }
                    
                    /*
                    if( !$is_sp_downline ){
                        $message    = 'error'; 
                    }else{
                        $message    = 'available';
                        $info      .= '
                        <input type="hidden" name="pin_downline_id" class="form-control" value="'.$memberdata->id.'" />
                        <div class="form-group">
            				<label class="col-md-3 control-label">Sponsor Name&nbsp;&nbsp;&nbsp;&nbsp;</label>
            				<div class="col-md-7">
            					<input type="text" name="reg_member_sponsor_nama_dsb" class="form-control" placeholder="Member Name" disabled="" value="'.strtoupper($memberdata->name).'" />
            				</div>
            			</div>';    
                    }
                    */
                }else{
                    $message        = 'error_admin';
                }
            }
        }
        
        // Set JSON data
        $data = array(
            'message'   => $message,
            'info'      => $info
        );
        
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Get PIN by Package function.
	 */
    function pinget()
    {
        $current_member     = bgn_get_current_member();
        $package            = bgn_isset($this->input->post('package'));
        $pin                = '';
        $pinorder           = '';
        $data               = array();
        
        if( !$package || empty($package) ){
            // Set JSON data
            $data = array(
                'result'    => '<option value="">Tidak Ada Pilihan PIN</option>',
            );
        }else{
            $pindata        = bgn_member_pin($current_member->id, 'active', FALSE, $package );
            if( $pindata || !empty($pindata) ){
                foreach($pindata as $p){
                    $pin .= '<option value="'.$p->id.'">'.$p->id_pin.'</option>';
                }
                // Set JSON data
                $data = array(
                    'result'    => $pin,
                );
            }else{
                // Set JSON data
                $data = array(
                    'result'    => '<option value="">Tidak Ada Pilihan PIN</option>',
                );
            }
        }
        
        $pin_order          = bgn_member_order_pin($current_member->id, 'review');
        if( !empty($pin_order) ){
            $pinorder       = '
            <div class="alert alert-success">
                <strong>
                    Anda memiliki '.count($pin_order).' pesanan PIN. 
                    Pesanan sedang dalam proses review dan akan segera di aktifkan. Berikut detail pesanan PIN Anda.
                </strong>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <ul class="list-group">';
                        foreach($pin_order as $row){
                            $pinorder .= '
                            <li class="list-group-item '.$row->package.'">
                                '.date('Y-m-d', strtotime($row->datecreated)).' : 
                                <strong>PAKET '.strtoupper($row->package).'</strong> 
                                <span class="badge badge-info"> Qty : '.$row->qty.' </span>
                            </li>';
                        }
                    $pinorder .= '</ul>
                </div>
            </div>';
            
            $data['pinorder'] = $pinorder;
        }else{
            $data['pinorder'] = $pinorder;
        }
        
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Check Member PIN function.
	 */
    function checkpin()
    {
        $current_member     = bgn_get_current_member();
        $pindata            = bgn_member_pin($current_member->id, 'active', FALSE );
        $pin                = '';
        
        if( !$pindata || empty($pindata) ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'result'    => base_url('member/newmember'),
            );
            // JSON encode data
            die(json_encode($data));
        }else{
            foreach($pindata as $p){
                $pin .= '<option value="'.$p->id.'">'.$p->id_pin.'</option>';
            }
            // Set JSON data
            $data = array(
                'message'   => 'success',
                'result'    => $pin,
            );
            // JSON encode data
            die(json_encode($data));
        }
    }
    
    /**
	 * Clone Data Anggota function.
	 */
    function cloning()
    {
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $username           = bgn_isset($this->input->post('username'), '');
        
        if( empty($username) ){
            // Set JSON data
            $data = array(
                'message'   => 'failed',
                'alert'     => '<button class="close" data-close="alert"></button>Member ID tidak boleh kosong. Silahkan inputkan member ID lainnya!',
                'data'      => '',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $memberdata         = $this->Model_Member->get_member_by('login', strtolower($username));
        if( !$memberdata ){
            // Set JSON data
            $data = array(
                'message'   => 'failed',
                'alert'     => '<button class="close" data-close="alert"></button>Member ID tidak ditemukan. Silahkan inputkan member ID lainnya!',
                'data'      => '',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        // Check If Member is Downline
        // -------------------------------------------------
        if( !$is_admin ){
            $is_downline        = $this->Model_Member->get_is_downline($memberdata->id, $current_member->tree);
            if( !$is_downline ){
                // Set JSON data
                $data = array(
                    'message'   => 'failed',
                    'alert'     => '<button class="close" data-close="alert"></button>Member ID bukan jaringan Anda. Clone data hanya bisa dilakukan dari jaringan Anda!',
                    'data'      => '',
                );
                // JSON encode data
                die(json_encode($data));
            }
        }
        
        // Set JSON data
        $data = array(
            'message'       => 'Success',
            'alert'         => '<button class="close" data-close="alert"></button>Member ID ditemukan dan data sudah di clone',
            'data'          => $memberdata,
        );
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Check Username function.
	 */
    function checkusername()
    {
        $username               = bgn_isset($this->input->post('username'));
        $message                = '';
        
        if( !empty($username) ){
            $memberdata         = $this->Model_Member->get_member_by('login', strtolower($username));
            
            if( $memberdata ) { 
                $message        = 'notavailable'; 
            }else{
                if (preg_match('/^[a-z]{1}[a-z0-9]{5,31}$/', $username)){
                    $message    = 'available';
                }else{
                    $message    = 'invalid';
                }
            }
        }
        
        // Set JSON data
        $data = array(
            'message'   => $message,
        );
        
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Member Generation function.
	 */
    function generation()
    {
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
		$is_admin				= as_administrator( $current_member );
        $levels					= $is_admin ? 0 : 2;
		
        $data['title']          = TITLE . 'Generasi Anggota';
        $data['member']         = $current_member;
        $data['main_content']   = 'generation';
		$data['is_admin']		= $is_admin;
		$data['levels']			= $levels;
		
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Load sponsors more
	 */
	function generation_loadmore( $offset = 0, $limit = 10 ) {
		auth_redirect();
		
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator( $current_member );
        $data                   = array();
        $package                = config_item('paket');

		if ( $is_admin ) {
			$conditions = ' WHERE %type% <> 2';
            if ( $username      = $this->input->post( 'username' ) ) {
                $conditions    .= ' AND %username% LIKE "%' . $username . '%"';
			}
			
			if ( ! $parents = $this->Model_Member->get_all_member_data( $limit, $offset, $conditions, '%id% ASC' ) )
            $parents            = array();

			foreach( $parents as $parent ) {
                $downlines      = bgn_get_member_gen_sponsor($parent->id, 10);
                $data[] = array(
                    'text'      => $this->generation_text($parent),
                    'nodes'     => $this->generation_nodes($parent, $downlines),
                    'tags'      => array($package[$parent->package])
                );
			}
		} else {
            $downlines  = bgn_get_member_gen_sponsor( $current_member->id, 10 );
            $data[] = array(
                'text'  => $this->generation_text($current_member) . ' <span class="label label-info">Anda</span>',
                'nodes' => $this->generation_nodes($current_member, $downlines),
                'tags'  => array($package[$current_member->package])
            );
		}
		
		$success = count( $data ) ? true : false;
		$response = array( 'success' => $success, 'data' => $data );
		echo json_encode( $response );
	}
    
    /**
	 * Get generation nodes function
	 */
	private function generation_nodes($member, $downlines) {
        $package    = config_item('paket');
        $nodes      = array();
		
		foreach($downlines as $gen => $members) {
			foreach($members as $downline) {
				if ($downline->sponsor != $member->id) continue;
				
                $nodes[] = array(
                    'text'  => $this->generation_text($downline, ($gen + 1)),
                    'nodes' => $this->generation_nodes($downline, $downlines),
                    'tags'  => array($package[$downline->package])
                );
			}
		}

		return $nodes;
	}
	
	/**
	 * Get generation text function
	 */
	private function generation_text($member, $gen = 0) {
		return '<strong>' . $member->name . '</strong> <small>(' . $member->username . ')</small> ' . ( $gen ? ' <span class="label label-warning">Gen-' . $gen . '</span>' : '' );
	}
}

/* End of file member.php */
/* Location: ./application/controllers/member.php */