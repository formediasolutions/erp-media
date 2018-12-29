<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Backend Controller.
 * 
 * @class     Backend
 * @author    Iqbal
 * @version   1.0.0
 * @copyright Copyright (c) 2016 BKEV Global Network (http://www.bkev-globalnetwork.com)
 */
class Backend extends CI_Controller {
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
	public function index()
	{
        auth_redirect();
        $current_member         = erp_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        $headstyles             = '';
        $loadscripts            = '';
        $scripts_add            = '';
        $scripts_init           = '';
        
        $data['title']          = TITLE . 'Dashboard';
        $data['member']         = $current_member;
        $data['is_admin']       = $is_admin;
        $data['id_adm_group']   = $current_member->id_adm_group;
        $data['id_adm_company'] = $current_member->id_adm_company;
        $data['id_adm_module']  = $current_member->default_id_adm_module;
        
        $data['headstyles']     = $headstyles;
        $data['scripts']        = $loadscripts;
        $data['scripts_add']    = $scripts_add;
        $data['scripts_init']   = $scripts_init;
        $data['main_content']   = 'dashboard';
        
        // Log for dashboard
		if ( ! $this->session->userdata( 'log_dashboard' ) ) {
			$this->session->set_userdata( 'log_dashboard', true );
		}
        
        $this->load->view(VIEW_BACK . 'template', $data);
	}
    
    /**
	 * Process Change Module
	 */
	public function changeModule( $id_module=''  )
	{
        auth_redirect();
        $member_data            = '';
        $current_member         = erp_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = erp_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('dashboard'), 'refresh' );
        }
            
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        // Check Module Member
        $moduledata     = get_module($id_module);
        // Set session data
        $session_data   = array(
            'id'                => $current_member->id,
            'username'          => $current_member->username,
            'phone'             => $current_member->phone,
            'name'              => $current_member->name,
            'email'             => $current_member->email,
            'id_adm_group'      => $current_member->id_adm_group,
            'id_adm_company'    => $current_member->id_adm_company,
            'id_adm_module'     => $id_module,
            'last_login'        => $current_member->last_login
        );
        
        // Set session
        $this->session->set_userdata('member_logged_in', $session_data);
        
        $headstyles             = '';
        $loadscripts            = '';
        $scripts_add            = '';
        $scripts_init           = '';
        
        $data['title']          = TITLE . 'Beranda';
        $data['member']         = $current_member;
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        $data['id_adm_group']   = $current_member->id_adm_group;
        $data['id_adm_company'] = $current_member->id_adm_company;
        $data['id_adm_module']  = $id_module;
        
        $data['headstyles']     = $headstyles;
        $data['scripts']        = $loadscripts;
        $data['scripts_add']    = $scripts_add;
        $data['scripts_init']   = $scripts_init; 
        $data['main_content']   = 'dashboard';

        redirect( base_url('dashboard'), 'refresh' );
        //yang gw butuhin kita ngereplace session id modulenya 
        //l$this->load->view(VIEW_BACK . 'template', $data);
    }
    
    
    
    /**
	 * Adm Menu function.
	 */
	public function admmenu( $id=0  )
	{
        auth_redirect();

        //include library
        $this->load->library('PHPGrid');
        //require_once(LIBRARY_PATH .'phpGrid_Lite/conf.php');
        include_once(PHPGRID_LIBPATH. "/inc/jqgrid_dist.php"); 
 
        $member_data            = '';
        $current_member         = erp_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = erp_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/bonus'), 'refresh' );
        }
            
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        
        // Database config file to be passed in phpgrid constructor
        $db_conf = array(
            "type" 		=> PHPGRID_DBTYPE,
            "server" 	=> PHPGRID_DBHOST,
            "user" 		=> PHPGRID_DBUSER,
            "password" 	=> PHPGRID_DBPASS,
            "database" 	=> PHPGRID_DBNAME
        );
        $datagrid = new jqgrid($db_conf);

        $opt["caption"]     = "Master Menu";
        $opt["autowidth"]   = true;
        $opt["altRows"]     = true; 
        $opt["multiselect"] = true; 
        $opt["scroll"]      = true; 
        $datagrid->set_options($opt);

        $datagrid->table    = "adm_menu";
        $out = $datagrid->render("listmenu");
        
        $headstyles             = erp_headstyles(array(
            // Default CSS Plugin
            PLUGIN_PATH . 'node-waves/waves.css',
            PLUGIN_PATH . 'animate-css/animate.css',
            // DataTable Plugin
            //PLUGIN_PATH . 'jquery-datatable/dataTables.bootstrap.css',
            // Datetime Picker Plugin
            //PLUGIN_PATH . 'bootstrap-daterangepicker/daterangepicker.css',
            //'../../lib/bootstrap/css/bootstrap.min.css',
            //'../../lib/js/themes/base/jquery-ui.custom.css',
            //'../../lib/js/themes/base/js/jqgrid/css/ui.jqgrid.css',
        ));
        
        $loadscripts            = erp_scripts(array(
            // Default JS Plugin
            PLUGIN_PATH . 'node-waves/waves.js',
            //PLUGIN_PATH . 'jquery-slimscroll/jquery.slimscroll.js',
            // DataTable Plugin
            //PLUGIN_PATH . 'jquery-datatable/jquery.dataTables.min.js',
            //PLUGIN_PATH . 'jquery-datatable/dataTables.bootstrap.js',
            //PLUGIN_PATH . 'jquery-datatable/datatable.js',
            // Datetime Picker Plugin
            //PLUGIN_PATH . 'momentjs/moment.js',
            //PLUGIN_PATH . 'bootstrap-daterangepicker/moment.min.js',
            //PLUGIN_PATH . 'bootstrap-daterangepicker/daterangepicker.js',
            // Always placed at bottom
            //JS_PATH . 'admin.js',
            // Put script based on current page
            //'../../lib/js/jquery.min.js',
            //'../../lib/js/jqgrid/js/i18n/grid.locale-en.js',
            //'../../lib/js/jqgrid/js/jquery.jqGrid.min.js',
            //'../../lib/js/themes/jquery-ui.custom.min.js',

            //'../../lib/bootstrap/js/jquery.js',
            //'../../lib/bootstrap/js/bootstrap.min.js',
            //JS_PATH . 'pages/table/table-ajax.js',
        ));
        
        $scripts_add            = '';
        $scripts_init           = erp_scripts_init(array(
            //'App.init();',
            //'TableAjax.init();',
        ));
        
        //$datagrid = new C_DataGrid("SELECT * FROM adm_menu", 'id_adm_menu', "adm_menu");

        $data['title']          = TITLE . 'Master Menu';
        $data['member']         = $current_member;
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        $data['id_adm_group']   = $current_member->id_adm_group;
        $data['id_adm_company'] = $current_member->id_adm_company;
        $data['id_adm_module']  = $current_member->default_id_adm_module;
        
        $data['headstyles']     = $headstyles;
        $data['scripts']        = $loadscripts;
        $data['scripts_add']    = $scripts_add;
        $data['scripts_init']   = $scripts_init;
        $data['phpgrid']        = $out;
        $data['main_content']   = 'adm/test';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }

    function get_country_dropdown()
    {
        $str = array();
        $countries = array('AF' => 'Afghanistan', 'AX' => 'Aland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua And Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia And Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, Democratic Republic', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Cote D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island & Mcdonald Islands', 'VA' => 'Holy See (Vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic Of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle Of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KR' => 'Korea', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States Of', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Reunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthelemy', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts And Nevis', 'LC' => 'Saint Lucia', 'MF' => 'Saint Martin', 'PM' => 'Saint Pierre And Miquelon', 'VC' => 'Saint Vincent And Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome And Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia And Sandwich Isl.', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard And Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad And Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks And Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'UK', 'US' => 'USA', 'UM' => 'United States Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis And Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe');
        foreach ($countries as $k => $v)
            $str[] = "$v:$v";

        return implode(";",$str);
    }
    
    /**
	 * Menu list data function.
	 */
    function menulistdata(){
        $current_member     = erp_get_current_member();
        $is_admin           = as_administrator($current_member);
        $condition          = '';
        
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        
        $sAction            = erp_isset($_REQUEST['sAction'],'');
        $sEcho              = intval($_REQUEST['sEcho']);
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_name             = $this->input->post('search_name');
        $s_name             = erp_isset($s_name, '');
        $s_folder           = $this->input->post('search_folder');
        $s_folder           = erp_isset($s_folder, '');
        
        if( !empty($s_name) )           { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_folder) )         { $condition .= str_replace('%s%', $s_folder, ' AND %folder% LIKE "%%s%%"'); }
        
        if( $column == 1 )      { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%folder% ' . $sort; }

        $menu_list        = $this->Model_Member->get_all_menu($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array();
        
        if( !empty($menu_list) ){
            $iTotalRecords  = erp_get_last_found_rows();
            //$cfg_type       = config_item('user_type');
            $cfg_status     = config_item('status');
            
            $i = $offset + 1;
            foreach($menu_list as $row){
                
                $btn_action         = '<a href="'.base_url('pengguna/profil/'.$row->id).'" class="btn btn-info btn-sm" data-placement="left" title="Edit"><i class="flaticon-edit-1"></i></a>';
                
                if($row->visible == NONACTIVE)   { 
                    $status         = '<span class="label label-default">'.strtoupper($cfg_status[$row->visible]).'</span>'; 
                }elseif($row->visible == ACTIVE)  { 
                    $status         = '<span class="label label-success">'.strtoupper($cfg_status[$row->visible]).'</span>'; 
                }
                
                $records["aaData"][] = array(
                    erp_center('<input name="modulelist[]" class="cblist filled-in chk-col-blue" id="cblist'.$row->id.'" value="' . $row->id . '" type="checkbox"/>
                    <label for="cblist'.$row->id.'"></label>'),
                    erp_center($i),
                    '<a href="'.base_url('pengguna/profil/'.$row->id).'">' . $row->name . '</a>',
                    erp_center($status),
                    $row->folder,
                    erp_center("<span><i class='".$row->icon_class."'></i>"),
                    erp_center($btn_action)
                );
                $i++;
            } 
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $sGroupActionName       = $_REQUEST['sGroupActionName'];
            $userlist               = $_REQUEST['userlist'];
            
            $proses                 = $this->useraction($sGroupActionName, $userlist);
            $records["sStatus"]     = $proses['status']; 
            $records["sMessage"]    = $proses['message']; 
        }elseif(isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "export_excel"){
            $data_list                      = $this->Model_User->get_all_user(0, 0, $condition, $order_by);
            if( !empty($data_list) ){
                $export                     = $this->smit_excel->exportUserList( $data_list );
                $records["sStatus"]         = "EXPORTED";
                $records["sMessage"]        = $export;
            }else{
                $records["sStatus"]         = "ERROR";
                $records["sMessage"]        = 'Tidak ada data pengguna untuk di export';
            }
        }elseif(isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "export_pdf"){
            $data_list                      = $this->Model_User->get_all_user(0, 0, $condition, $order_by);
            if( !empty($data_list) ){
                $export                     = $this->smit_excel->exportUserList( $data_list, true );
                $records["sStatus"]         = "EXPORTED";
                $records["sMessage"]        = $export;
            }else{
                $records["sStatus"]         = "ERROR";
                $records["sMessage"]        = 'Tidak ada data pengguna untuk di export';
            }
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Adm Module function.
	 */
	public function admmodule( $id=0  )
	{
        auth_redirect();
        $member_data            = '';
        $current_member         = erp_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = erp_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/bonus'), 'refresh' );
        }
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        
        $headstyles             = erp_headstyles(array(
            // Default CSS Plugin
            PLUGIN_PATH . 'node-waves/waves.css',
            PLUGIN_PATH . 'animate-css/animate.css',
            // DataTable Plugin
            PLUGIN_PATH . 'jquery-datatable/dataTables.bootstrap.css',
            // Datetime Picker Plugin
            PLUGIN_PATH . 'bootstrap-daterangepicker/daterangepicker.css',
        ));
        
        $loadscripts            = erp_scripts(array(
            // Default JS Plugin
            PLUGIN_PATH . 'node-waves/waves.js',
            //PLUGIN_PATH . 'jquery-slimscroll/jquery.slimscroll.js',
            // DataTable Plugin
            PLUGIN_PATH . 'jquery-datatable/jquery.dataTables.min.js',
            PLUGIN_PATH . 'jquery-datatable/dataTables.bootstrap.js',
            PLUGIN_PATH . 'jquery-datatable/datatable.js',
            // Datetime Picker Plugin
            PLUGIN_PATH . 'momentjs/moment.js',
            PLUGIN_PATH . 'bootstrap-daterangepicker/moment.min.js',
            PLUGIN_PATH . 'bootstrap-daterangepicker/daterangepicker.js',
            // Always placed at bottom
            //JS_PATH . 'admin.js',
            // Put script based on current page
            JS_PATH . 'pages/table/table-ajax.js',
        ));
        
        $scripts_add            = '';
        $scripts_init           = erp_scripts_init(array(
            //'App.init();',
            'TableAjax.init();',
        ));
        
        $data['title']          = TITLE . 'Master Module';
        $data['member']         = $current_member;
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        $data['id_adm_group']   = $current_member->id_adm_group;
        $data['id_adm_company'] = $current_member->id_adm_company;
        $data['id_adm_module']  = $current_member->default_id_adm_module;
        
        $data['headstyles']     = $headstyles;
        $data['scripts']        = $loadscripts;
        $data['scripts_add']    = $scripts_add;
        $data['scripts_init']   = $scripts_init;
        $data['main_content']   = 'adm/admmodule';
                    
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Module list data function.
	 */
    function modulelistdata(){
        $current_member     = erp_get_current_member();
        $is_admin           = as_administrator($current_member);
        $condition          = '';
        
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        
        $sAction            = erp_isset($_REQUEST['sAction'],'');
        $sEcho              = intval($_REQUEST['sEcho']);
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_name             = $this->input->post('search_name');
        $s_name             = erp_isset($s_name, '');
        $s_folder           = $this->input->post('search_folder');
        $s_folder           = erp_isset($s_folder, '');
        
        if( !empty($s_name) )           { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_folder) )         { $condition .= str_replace('%s%', $s_folder, ' AND %folder% LIKE "%%s%%"'); }
        
        if( $column == 1 )      { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%folder% ' . $sort; }

        $module_list        = $this->Model_Member->get_all_module($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array();
        
        if( !empty($module_list) ){
            $iTotalRecords  = erp_get_last_found_rows();
            //$cfg_type       = config_item('user_type');
            $cfg_status     = config_item('status');
            
            $i = $offset + 1;
            foreach($module_list as $row){
                
                $btn_action         = '<a href="'.base_url('pengguna/profil/'.$row->id).'" class="btn btn-info btn-sm" data-placement="left" title="Edit"><i class="flaticon-edit-1"></i></a>';
                
                if($row->visible == NONACTIVE)   { 
                    $status         = '<span class="label label-default">'.strtoupper($cfg_status[$row->visible]).'</span>'; 
                }elseif($row->visible == ACTIVE)  { 
                    $status         = '<span class="label label-success">'.strtoupper($cfg_status[$row->visible]).'</span>'; 
                }
                
                $records["aaData"][] = array(
                    erp_center('<input name="modulelist[]" class="cblist filled-in chk-col-blue" id="cblist'.$row->id.'" value="' . $row->id . '" type="checkbox"/>
                    <label for="cblist'.$row->id.'"></label>'),
                    erp_center($i),
                    '<a href="'.base_url('pengguna/profil/'.$row->id).'">' . $row->name . '</a>',
                    erp_center($status),
                    $row->folder,
                    erp_center("<span><i class='".$row->icon_class."'></i>"),
                    erp_center($btn_action)
                );
                $i++;
            } 
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $sGroupActionName       = $_REQUEST['sGroupActionName'];
            $userlist               = $_REQUEST['userlist'];
            
            $proses                 = $this->useraction($sGroupActionName, $userlist);
            $records["sStatus"]     = $proses['status']; 
            $records["sMessage"]    = $proses['message']; 
        }elseif(isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "export_excel"){
            $data_list                      = $this->Model_User->get_all_user(0, 0, $condition, $order_by);
            if( !empty($data_list) ){
                $export                     = $this->smit_excel->exportUserList( $data_list );
                $records["sStatus"]         = "EXPORTED";
                $records["sMessage"]        = $export;
            }else{
                $records["sStatus"]         = "ERROR";
                $records["sMessage"]        = 'Tidak ada data pengguna untuk di export';
            }
        }elseif(isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "export_pdf"){
            $data_list                      = $this->Model_User->get_all_user(0, 0, $condition, $order_by);
            if( !empty($data_list) ){
                $export                     = $this->smit_excel->exportUserList( $data_list, true );
                $records["sStatus"]         = "EXPORTED";
                $records["sMessage"]        = $export;
            }else{
                $records["sStatus"]         = "ERROR";
                $records["sMessage"]        = 'Tidak ada data pengguna untuk di export';
            }
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
	
    /**
	 * Bonus function.
	 */
	public function bonus( $id=0, $poin=false )
	{
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/bonus'), 'refresh' );
        }
            
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        $bonus_total            = $this->model_member->get_all_my_bonus_total($id_member);
        
        $data['title']          = TITLE . 'Bonus';
        $data['member']         = $current_member;
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        $data['bonus_total']    = ( !empty($bonus_total->total) ? $bonus_total->total : 0 );
        $data['main_content']   = 'bonus';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Bonus List Member Bonus function.
	 */
    function bonuslist()
    {
        $condition          = 'WHERE %type% = 1 AND %nominal% > 0';
        $order_by           = '';
        $iTotalRecords      = 0;

        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username        = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        $s_bank             = bgn_isset($this->input->post('search_bank'), '');
        $s_branch           = bgn_isset($this->input->post('search_branch'), '');
        $s_bill             = bgn_isset($this->input->post('search_bill'), '');
        
        if( !empty($s_username) )  { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%s%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_bank) )       { $condition .= str_replace('%s%', $s_bank, ' AND %bank% LIKE "%%s%%"'); }
        if( !empty($s_branch) )     { $condition .= str_replace('%s%', $s_branch, ' AND %branch% LIKE "%%s%%"'); }
        if( !empty($s_bill) )       { $condition .= str_replace('%s%', $s_bill, ' AND %bill% LIKE "%%s%%"'); }
        
        if ( !empty($s_nominal_min) )	{ $condition .= ' AND %nominal% >= '.$s_nominal_min.''; }
        if ( !empty($s_nominal_max) )	{ $condition .= ' AND %nominal% <= '.$s_nominal_max.''; }
        
        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%nominal% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%bank% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%branch% ' . $sort; }
        elseif( $column == 6 )  { $order_by .= '%bill% ' . $sort; }
		
        $bonus_list         = $this->model_member->get_all_member_bonus($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($bonus_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            
            $i = $offset + 1;
            foreach($bonus_list as $row){          
                if( $row->type != 2 ){
                	
                    $detailbutton   = '<a href="'.base_url('report/bonus/'.$row->id).'" class="btn btn-xs btn-primary">Detail</a>';
                    $bonus_amount   = bgn_accounting( ($row->total == "" ? 0 : $row->total), config_item('currency'), true );
                    $username      = ( $row->id == 1 ? $row->username : '<a href="'.base_url('profile/'.$row->id).'">' . $row->username . '</a>' );
                    $name           = ( $row->id == 1 ? strtoupper($row->name) : '<a href="'.base_url('profile/'.$row->id).'">' . strtoupper($row->name) . '</a>' );
                    $bank           = bgn_banks($row->bank);              
                    
                    $records["aaData"][]    = array(
                        '<center>'.$i.'</center>',
                        '<center>'.$username.'</center>',
                        $name,
                        $bonus_amount,
                        '<center>'.strtoupper($bank->nama).'</center>',
                        strtoupper($row->branch),
                        $row->bill,
                        '<center>'.$detailbutton.'</center>',
                    );
                    $i++;
                }
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Bonus List My Bonus function.
	 */
    function bonuslistmine( $id=0 )
    {
        $member_data        = '';
        
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        
        if ( $id > 0 )
            $member_data    = bgn_get_memberdata_by_id($id);
            
        $id_member          = ( $id > 0 ? $member_data->id : $current_member->id );
        
        $condition          = '';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        
        $sEcho              = intval($_REQUEST['sEcho']);
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_id_bonus         = bgn_isset($this->input->post('search_id_bonus'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        $s_desc             = bgn_isset($this->input->post('search_desc'), '');
        $s_type             = bgn_isset($this->input->post('search_type'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        
        if( !empty($s_id_bonus) )   { $condition .= str_replace('%s%', $s_id_bonus, ' AND %id_bonus% LIKE "%%s%%"'); }
        if( !empty($s_desc) )       { $condition .= str_replace('%s%', $s_desc, ' AND %desc% LIKE "%%s%%"'); }
        if( !empty($s_status) )     { $condition .= str_replace('%s%', $s_status, ' AND %status% = %s%'); }
        if( !empty($s_type) )       { $condition .= str_replace('%s%', $s_type, ' AND %type% = %s%'); }
        
        if ( !empty($s_date_min) )	{ $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if ( !empty($s_date_max) )	{ $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; } 
        
        if ( !empty($s_nominal_min) )	{ $condition .= ' AND %nominal% >= '.$s_nominal_min.''; }
        if ( !empty($s_nominal_max) )	{ $condition .= ' AND %nominal% <= '.$s_nominal_max.''; }
        
        if( $column == 1 )      { $order_by .= '%id_bonus% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%datecreated% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%nominal% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%type% ' . $sort; }
		
        $bonus_list         = $this->model_member->get_all_my_bonus($id_member, $limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 

        if( !empty($bonus_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($bonus_list as $row){     
                if($row->type == BONUSTYPE_SPONSOR)             { $type = '<center><span class="label label-sm label-primary">SPONSOR</span></center>'; }
                elseif($row->type == BONUSTYPE_PAIRING)         { $type = '<center><span class="label label-sm label-warning">PASANGAN</span></center>'; }
                elseif($row->type == BONUSTYPE_MATCHING)        { $type = '<center><span class="label label-sm label-danger">MATCHING</span></center>'; }
                elseif($row->type == BONUSTYPE_INPUT)           { $type = '<center><span class="label label-sm label-info">INPUT</span></center>'; }
                elseif($row->type == BONUSTYPE_PASSUP)          { $type = '<center><span class="label label-sm label-default">PASS UP</span></center>'; }
                elseif($row->type == BONUSTYPE_AUTOCBI)         { $type = '<center><span class="label label-sm label-success">AUTO-CBI</span></center>'; }
                
				$bonus_amount   = bgn_accounting( ($row->amount == "" ? 0 : $row->amount), config_item('currency'), true );
                
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    $row->id_bonus,
                    '<center>'.$row->datecreated.'</center>',
                    $bonus_amount,
                    $row->desc,
                    $type,
                    '',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Pay bonus of member function.
	 */
    function bonuspaid( $id=0 )
    {
        auth_redirect();
        
        if( !$id ) { echo 'failed'; die(); }

        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if( !$is_admin ) { echo 'failed'; die(); }
        
        $databonus              = array('status' => 1);

        if( $this->model_member->update_data_bonus($id, $databonus) ){
            echo 'success'; die();
        }else{
            echo 'failed'; die();
        }
    }
    
    /**
	 * Pay all bonus of member function.
	 */
    function bonuspaidall( $id=0 )
    {
        auth_redirect();
        
        if( !$id ) { echo 'failed'; die(); }

        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        if( !$is_admin ) { echo 'failed'; die(); }
        
        $member                 = $this->model_member->get_memberdata($id);
        if ( !$member ) { echo 'failed'; die(); }
        
        $member_bonus           = $this->model_member->get_all_member_bonus_by_id($id);
        
        if( !empty($member_bonus) ){
            foreach($member_bonus as $row){
                $databonus              = array('status' => 1);
                $this->model_member->update_data_bonus($row->id, $databonus);
            }
            echo 'success'; die();
        }
        echo 'failed'; die();
    }
    
    /**
	 * Pay all bonus all of member function.
	 */
    function bonuspaidallmember()
    {
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        if( !$is_admin ) { echo 'failed'; die(); }
        
        $bonus              = $this->model_member->get_all_bonus();
        
        if( empty($bonus) || !$bonus ){ echo 'failed'; die(); }
        
        foreach($bonus as $row){
            $databonus      = array('status' => 1);
            $this->model_member->update_data_bonus( $row->id, $databonus);
        }
        echo 'success'; die();
    }
    
    /**
	 * Register Confirmation function.
	 */
	function register( $id=0  )
	{
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/register'), 'refresh' );
        }
            
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );

        
        $data['title']          = TITLE . 'Pendaftaran';
        $data['member']         = $current_member;
        $data['main_content']   = 'registerconfirm';
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Register Confirmation List function.
	 */
    function registerlist(){
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $condition          = '';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_member           = bgn_isset($this->input->post('search_member'), '');
        $s_sponsor          = bgn_isset($this->input->post('search_sponsor'), '');
        $s_downline         = bgn_isset($this->input->post('search_downline'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        $s_access           = bgn_isset($this->input->post('search_access'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_member) )     { $condition .= str_replace('%s%', $s_member, ' AND %member% LIKE "%%s%%"'); }
        if( !empty($s_sponsor) )    { $condition .= str_replace('%s%', $s_sponsor, ' AND %sponsor% LIKE "%%s%%"'); }
        if( !empty($s_downline) )   { $condition .= str_replace('%s%', $s_downline, ' AND %downline% LIKE "%%s%%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_nominal) )    { $condition .= str_replace('%s%', $s_nominal, ' AND %nominal% LIKE "%%s%%"'); }
        if( !empty($s_status) )     { $condition .= str_replace('%s%', ( $s_status == 'pending' ? 0 : 1 ), ' AND %status% = %s%'); }
        if( !empty($s_access) )     { $condition .= str_replace('%s%', $s_access, ' AND %access% = "%s%"'); }
        
        if ( !empty($s_nominal_min) )	{ $condition .= ' AND %nominal% >= '.$s_nominal_min.''; }
        if ( !empty($s_nominal_max) )	{ $condition .= ' AND %nominal% <= '.$s_nominal_max.''; }
        if ( !empty($s_date_min) )      { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if ( !empty($s_date_max) )      { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        
        if( !empty($condition) ){
            $condition      = substr($condition, 4);
            $condition      = 'WHERE' . $condition;
        }
        
        if( $column == 1 )      { $order_by .= '%member% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%sponsor% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%downline% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%nominal% ' . $sort; }
        elseif( $column == 6 )  { $order_by .= '%status% ' . $sort; }
        elseif( $column == 7 )  { $order_by .= '%access% ' . $sort; }
        elseif( $column == 8 )  { $order_by .= '%datecreated% ' . $sort; }
        
        $confirm_list       = $this->model_member->get_all_member_confirm($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($confirm_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            $investment     = config_item('investment');
            
            foreach($confirm_list as $row){                
                $confirmbutton  = ( $row->status == 0 && $is_admin ? '<a href="'.base_url('backend/registerconfirm/'.$row->id).'" class="btn btn-xs btn-primary registerconfirm">Confirm</a>' : '<a href="#" class="btn btn-xs btn-default" disabled="">Confirm</a>' );
                $deletebutton   = ( $row->status == 0 && $is_admin ? ' <a href="'.base_url('backend/deleteconfirm/'.$row->id).'" class="btn btn-xs btn-danger deleteconfirm">Delete</a>' : ' <a href="#" class="btn btn-xs btn-default" disabled="">Delete</a>' );
                $member         = ( $row->id_member == 1 ? $row->member : '<a href="'.base_url('profile/'.$row->id_member).'">' . $row->member . '</a>' );
                $sponsor        = ( $row->id_sponsor == 1 ? $row->sponsor : '<a href="'.base_url('profile/'.$row->id_sponsor).'">' . $row->sponsor . '</a>' );
                $downline       = ( !empty($row->downline) ? '<a href="'.base_url('profile/'.$row->id_downline).'">'.$row->downline.'</a>' : '-');
                $status         = ( $row->status == 0 ? 'PENDING' : 'CONFIRMED' );
                $status         = '<span class="label label-sm label-'.( $status == 'PENDING' ? 'default' : 'success' ).'">'.$status.'</span>';
				
                $nilai_paket	= bgn_center('-');
				$nilai_paket    = bgn_accounting( $row->jumlah, config_item('currency'), TRUE);
                
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    '<center>'.$member.'</center>',
                    '<center>'.$sponsor.'</center>',
                    '<center>'.$downline.'</center>',
                    $row->name,
                    $nilai_paket,
                    '<center>'.$status.'</center>',
                    '<center>'.strtoupper($row->access).'</center>',
                    '<center>'.$row->datecreated.'</center>',
                    '<center>'.$confirmbutton.$deletebutton.'</center>',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Register Confirmation List function.
	 */
    function registerlistmine()
    {
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $id_member          = $current_member->id;
        
        $condition          = ' AND %id_member% = ' . $id_member;
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_sponsor          = bgn_isset($this->input->post('search_sponsor'), '');
        $s_downline         = bgn_isset($this->input->post('search_downline'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_nominal          = bgn_isset($this->input->post('search_nominal'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        $s_access           = bgn_isset($this->input->post('search_access'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_sponsor) )    { $condition .= str_replace('%s%', $s_sponsor, ' AND %sponsor% LIKE "%%s%%"'); }
        if( !empty($s_downline) )   { $condition .= str_replace('%s%', $s_downline, ' AND %downline% LIKE "%%s%%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_nominal) )    { $condition .= str_replace('%s%', $s_nominal, ' AND %nominal% LIKE "%%s%%"'); }
        if( !empty($s_status) )     { $condition .= str_replace('%s%', ( $s_status == 'pending' ? 0 : 1 ), ' AND %status% = %s%'); }
        if( !empty($s_access) )     { $condition .= str_replace('%s%', $s_access, ' AND %access% = "%s%"'); }
        
        if ( !empty($s_date_min) && empty($s_date_max) )            { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        elseif ( empty($s_date_min) && !empty($s_date_max) )        { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        elseif ( !empty($s_date_min) && !empty($s_date_max) )       { $condition .= ' AND %datecreated% BETWEEN '.strtotime($s_date_min).' AND '.strtotime($s_date_max).''; }
        
        if( !empty($condition) ){
            $condition      = substr($condition, 4);
            $condition      = 'WHERE' . $condition;
        }
        
        if( $column == 1 )      { $order_by .= '%sponsor% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%downline% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%nominal% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%status% ' . $sort; }
        elseif( $column == 6 )  { $order_by .= '%access% ' . $sort; }
        elseif( $column == 7 )  { $order_by .= '%datecreated% ' . $sort; }
        
        $confirm_list       = $this->model_member->get_all_member_confirm($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($confirm_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            $investment     = config_item('investment');
            
            foreach($confirm_list as $row){                
                $confirmbutton  = ( $row->status == 0 && $is_admin ? '<center><a href="'.base_url('backend/registerconfirm/'.$row->id).'" class="btn btn-xs btn-primary registerconfirmmine">Confirm</a></center>' : '<center><a href="#" class="btn btn-xs btn-default" disabled="">Confirm</a></center>' );
                $sponsor        = ( $row->id_sponsor == 1 ? $row->sponsor : '<a href="'.base_url('profile/'.$row->id_sponsor).'">' . $row->sponsor . '</a>' );
                $downline       = ( $row->id_downline == 1 ? $row->name : '<a href="'.base_url('profile/'.$row->id_downline).'">' . $row->name . '</a>' );
                $status         = ( $row->status == 0 ? 'PENDING' : 'CONFIRMED' );
                $status         = '<span class="label label-sm label-'.( $status == 'PENDING' ? 'default' : 'success' ).'">'.$status.'</span>';
                
                $nilai_paket	= bgn_center('-');
				$nilai_paket    = bgn_accounting( $row->jumlah, config_item('currency'), TRUE);
    
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    '<center>'.$sponsor.'</center>',
                    '<center>' . ( !empty($row->downline) ? '<a href="'.base_url('profile/'.$row->id_downline).'">' . $row->downline . '</a>' : '-' ) . '</center>',
                    $downline,
                    $nilai_paket,
                    '<center>'.$status.'</center>',
                    '<center>' . strtoupper($row->access) . '</center>',
                    $row->datecreated,
                    $confirmbutton,
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }

	/**
	 * Register Confirmation function.
	 */
    function registerconfirm( $id=0 )
    {
        // This is for AJAX request
    	if ( ! $this->input->is_ajax_request() ) exit('No direct script access allowed');
        
        if ( !$id ){
            // Set JSON data
            $data = array('message' => 'failed','data' => 'ID Konfirmasi Pendaftaran tidak boleh kosong!');
            // JSON encode data
            die(json_encode($data));
        }
        
        // -------------------------------------------------
        // Set Variable
        // -------------------------------------------------
        $upline                 = new stdClass();
        $position               = '';
        $curdate                = date('Y-m-d H:i:s');
        $member_confirm         = $this->model_member->get_member_confirm($id);

        if( !$member_confirm ){
            // Set JSON data
            $data = array('message' => 'failed','data' => 'Data konfirmasi pendaftaran tidak ditemukan!');
            // JSON encode data
            die(json_encode($data));
        }
        if( $member_confirm->status == 1 ){
            // Set JSON data
            $data = array('message' => 'failed','data' => 'Pendaftaran sudah dikonfirmasi! Silahkan cek data member');
            // JSON encode data
            die(json_encode($data));
        }
        
        // -------------------------------------------------
        // Downline Data
        // -------------------------------------------------
        $downline_id            = $member_confirm->id_downline;
        $downline               = $this->model_member->get_memberdata($downline_id);
        if( !$downline ){
            // Set JSON data
            $data = array('message' => 'failed','data' => 'Data member pada pendaftaran ini tidak ditemukan! Silahkan hubungi Administrator');
            // JSON encode data
            die(json_encode($data));
        }
        
        // -------------------------------------------------
        // Sponsor Data
        // -------------------------------------------------
        $sponsor_id             = $downline->sponsor;
        $sponsor                = $this->model_member->get_memberdata($sponsor_id);
        if( !$sponsor ){
            // Set JSON data
            $data = array('message' => 'failed','data' => 'Data sponsor tidak ditemukan! Silahkan hubungi Administrator');
            // JSON encode data
            die(json_encode($data));
        }
        $sponsor_username       = $sponsor->username;
        $sponsor_sponsor        = $sponsor->sponsor;
        $sponsor_phone          = $sponsor->phone;

        // Search Position Available
        // -------------------------------------------------
        $sponsor_downline       = bgn_downline($sponsor->id);
        if( count($sponsor_downline) == 2 ){
            $upline_data        = bgn_downline_minimun($sponsor->id, $sponsor_downline);
            $upline             = $upline_data->upline;
            $position           = $upline_data->position;
        }elseif( count($sponsor_downline) == 1 ){
            foreach($sponsor_downline as $down){
                $upline         = $sponsor;
                $position       = ( $down->position == POS_KIRI ? POS_KANAN : POS_KIRI );
            }
        }else{
            $upline             = $sponsor;
            $position           = POS_KIRI;
        }

		// -------------------------------------------------
        // Begin Transaction
        // -------------------------------------------------
		$this->db->trans_begin();
        
        // Update Data Member and Confirm Member
        // -------------------------------------------------
        $username               = $downline->username;
        $upline_id              = $upline->id;
        $upline                 = $this->model_member->get_memberdata($upline_id);
        $data_member            = array(
            'password'          => md5($downline->password),
            'password_pin'      => md5($downline->password_pin),
            'parent'            => $upline_id, 
            'position'          => $position,
            'tree'              => bgn_generate_tree($downline_id, $upline->tree),
            'status'            => 1,
            'datemodified'      => $curdate,
        );
        if( !$this->model_member->update_data($downline_id, $data_member) ){
            // Rollback Transaction
            $this->db->trans_rollback();
            // Set JSON data
            $data = array('message' => 'failed','data' => 'Update data member tidak berhasil');
            // JSON encode data
            die(json_encode($data));
        }
        
        $data_confirm           = array('status' => 1);
        if( !$this->model_member->update_data_confirm($id, $data_confirm) ){
            // Rollback Transaction
            $this->db->trans_rollback();
            // Set JSON data
            $data = array('message' => 'failed','data' => 'Update data konfirmasi pendaftaran tidak berhasil');
            // JSON encode data
            die(json_encode($data));
        }
        
        // -------------------------------------------------
		// Updating member tree 
        // ------------------------------------------------- 
		bgn_update_member_tree($downline_id);

        // -------------------------------------------------
        // Calculate Bonus Sponsorship
        // -------------------------------------------------
        if( $sponsor_id != 1 ){
            bgn_count_sponsor_bonus($downline_id, $sponsor_id);
            bgn_count_matching_bonus($downline_id, $sponsor_id);
            bgn_check_autoglobal_reward($sponsor_id);
        }
        
        // -------------------------------------------------
        // Update Poin and Count Pair Bonus
        // -------------------------------------------------
        bgn_update_poin($upline_id, $downline_id, $position);
        if( $upline_id != 1 ){
            bgn_count_pair_bonus($upline_id, $datetime, FALSE, FALSE, TRUE);
        }
        
        // -------------------------------------------------
        // Reward Process
        // -------------------------------------------------
        bgn_reward($upline_id,TRUE, FALSE, TRUE);
        
        // -------------------------------------------------
		// Process Auto Global Reward 
        // -------------------------------------------------
        if( !bgn_auto_global_process($downline_id) ){
            // Rollback Transaction
            $this->db->trans_rollback();
            // Set JSON data
            $data = array('message' => 'failed','data' => 'Pendaftaran tidak berhasil. Proses Auto Global terjadi kesalahan data.');
            // JSON encode data
            die(json_encode($data));
        }
        
        // -------------------------------------------------
        // Commit or Rollback Transaction
        // -------------------------------------------------
        if ($this->db->trans_status() === FALSE){
            // Set JSON data
            $data = array('message' => 'failed','data' => 'Konfirmasi pendaftaran tidak berhasil. Terjadi kesalahan data.');
            // JSON encode data
            die(json_encode($data));
        }else{
            // -------------------------------------------------
            // Complete Transaction
            // -------------------------------------------------
            $this->db->trans_commit();
    		$this->db->trans_complete();
            
            // Send SMS Notification to Downline
            $this->bgn_sms->sms_newmember($downline->phone, $downline->password, $username);
            // Send SMS Notification to Sponsor
            $this->bgn_sms->sms_newmember_spon($sponsor->phone, $downline->name, $username);
            
            // Set JSON data
            $data = array('message' => 'success','data' => 'Konfirmasi pendaftaran member <strong>'.$username.'</strong> telah berhasil.');
            // JSON encode data
            die(json_encode($data));
        }
    }
    
    /**
	 * Delete Confirmation function.
	 */
    function deleteconfirm( $id=0 )
    {
        if( !$id ){ echo 'failed'; die(); }
        
        $curdate                = date('Y-m-d H:i:s');
        $member_confirm         = $this->model_member->get_member_confirm($id);
    
        if( !$member_confirm ){ echo 'failed'; die(); }
        if( $member_confirm->status == 1 ){ echo 'failed'; die(); }
        
        $id_member              = $member_confirm->id_downline;
        $member                 = $this->model_member->get_memberdata($id_member);
        
        if( !$member ){ echo 'failed'; die(); }
        
        if( $this->model_member->delete_member($member->id) && $this->model_member->delete_register_confirm($id) ){
            echo 'success'; die();
        }
        
        echo 'failed'; die();
    }
    
    /**
	 * Reward function.
	 */
	function reward( $id=0  )
	{
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/reward'), 'refresh' );
        }
            
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        
        $data['title']          = TITLE . 'Penghargaan';
        $data['member']         = $current_member;
        $data['main_content']   = 'reward';
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Cash Reward Promo function Add by Jony
	 */
	function cashreward( $id=0  )
	{
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/cashreward'), 'refresh' );
        }
            
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        
        $data['title']          = TITLE . 'Cash Reward';
        $data['member']         = $current_member;
        $data['main_content']   = 'cashreward';
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Reward List Member function.
	 */
    function cashrewardlist($cat='')
    {
        $condition          = '';
        $order_by           = '';
        
        $iTotalRecords      = 0;
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_bill             = bgn_isset($this->input->post('search_bill'), '');
        $s_bill_name        = bgn_isset($this->input->post('search_bill_name'), '');
        $s_reward           = bgn_isset($this->input->post('search_reward'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        $s_qualified        = bgn_isset($this->input->post('search_qualified'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_username) )   { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_bank) )       { $condition .= str_replace('%s%', $s_bank, ' AND %bank% LIKE "%%s%%"'); }
        if( !empty($s_bill) )       { $condition .= str_replace('%s%', $s_bill, ' AND %bill% LIKE "%%s%%"'); }
        if( !empty($s_bill_name) )  { $condition .= str_replace('%s%', $s_bill_name, ' AND %bill_name% LIKE "%%s%%"'); }
        if( !empty($s_reward) )     { $condition .= str_replace('%s%', $s_reward, ' AND %type% = %s%'); }
        if( !empty($s_status) )     { 
            if( $s_status == 'pending' )        { $stat = 0; }
            elseif( $s_status == 'confirmed' )  { $stat = 1; }
            elseif( $s_status == 'expired' )    { $stat = 2; }
            $condition .= str_replace('%s%', $stat, ' AND %status% = %s%'); 
        }
        if( !empty($s_qualified) )  { $condition .= str_replace('%s%', ( $s_qualified == 'qualified' ? 1 : 0 ), ' AND %qualified% = %s%'); }
        
        if( !empty($s_date_min) )   { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if( !empty($s_date_max) )   { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        
        if( empty($s_reward) ){
            if( !empty($cat) ){
                if($cat == 'promo')     { $option = '1,2,3,4,5,6,7,8'; }
                elseif($cat == 'auto')  { $option = '9,10,11,12,13,14,15,16'; }
                $condition .= ' AND %type% IN('.$option.') ';
            }
        }
        
        if( !empty($condition) ){
            $condition      = substr($condition, 4);
            $condition      = 'WHERE' . $condition;
        }
        
        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%bank% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%bill% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%bill_name% ' . $sort; }
        elseif( $column == 6 )  { $order_by .= '%type% ' . $sort; }
        elseif( $column == 7 )  { $order_by .= '%status% ' . $sort; }
        elseif( $column == 8 )  { $order_by .= '%qualified% ' . $sort; }
        elseif( $column == 9 )  { $order_by .= '%datecreated% ' . $sort; }
        
        $reward_list        = $this->model_member->get_all_member_cashreward($limit, $offset, $condition, $order_by);
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($reward_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($reward_list as $row){
				$reward 		= $row->reward_name;
                if( $row->status == 0 )     { $status = '<span class="label label-sm label-default">PENDING</span>'; }
                elseif( $row->status == 1 ) { $status = '<span class="label label-sm label-success">CONFIRMED</span>'; }
                elseif( $row->status == 2 ) { $status = '<span class="label label-sm label-danger">EXPIRED</span>'; }
                
                if( $row->qualified == 0 )      { $qualified = '<span class="label label-sm label-default">NOT QUALIFIED</span>'; }
                elseif( $row->qualified == 1 )  { $qualified = '<span class="label label-sm label-primary">QUALIFIED</span>'; }
                
                $confirmbutton  = ( $row->status == 0 ? '<center><a href="'.base_url('backend/cashrewardconfirm/'.$row->id).'" class="btn btn-xs btn-primary rewardconfirm">Confirm</a></center>' : '<center><a href="#" class="btn btn-xs btn-default" disabled="">Confirm</a></center>' );
                $bank           = bgn_banks($row->bank);
                
                if( !empty($cat) && $cat == 'promo' ){
                    $records["aaData"][]    = array(
                        '<center>'.$i.'</center>',
                        '<center><a href="'.base_url('profile/'.$row->id_member).'">' . $row->username . '</a></center>',
                        '<a href="'.base_url('profile/'.$row->id_member).'">' . strtoupper($row->name) . '</a>',
                        '<center>'.strtoupper($bank->nama).'</center>',
                        $row->bill,
                        strtoupper($row->bill_name),
                        '<center>'.$reward.'</center>',
                        '<center>'.$status.'</center>',
                        ( $row->status == 2 ? '<span class="text-danger"><strong>'.$row->datecreated.'</strong></span>' : $row->datecreated ),
                        $confirmbutton,
                    );
                }else{
                    $records["aaData"][]    = array(
                        '<center>'.$i.'</center>',
                        '<center><a href="'.base_url('profile/'.$row->id_member).'">' . $row->username . '</a></center>',
                        '<a href="'.base_url('profile/'.$row->id_member).'">' . strtoupper($row->name) . '</a>',
                        '<center>'.strtoupper($bank->nama).'</center>',
                        $row->bill,
                        strtoupper($row->bill_name),
                        '<center>'.$reward.'</center>',
                        '<center>'.$status.'</center>',
                        '<center>'.$qualified.'</center>',
                        ( $row->status == 2 ? '<span class="text-danger"><strong>'.$row->datecreated.'</strong></span>' : $row->datecreated ),
                        $confirmbutton,
                    );
                }
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
	
	/**
	 * Reward Promo function Add by Jony
	 */
	function rewardpromo( $id=0  )
	{
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/rewardpromo'), 'refresh' );
        }
            
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        
        $data['title']          = TITLE . 'Reward Promo';
        $data['member']         = $current_member;
        $data['main_content']   = 'rewardpromo';
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Reward List Member function.
	 */
    function rewardlist($cat='')
    {
        $condition          = '';
        $order_by           = '';
        
        $iTotalRecords      = 0;
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_bill             = bgn_isset($this->input->post('search_bill'), '');
        $s_bill_name        = bgn_isset($this->input->post('search_bill_name'), '');
        $s_reward           = bgn_isset($this->input->post('search_reward'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        $s_qualified        = bgn_isset($this->input->post('search_qualified'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_username) )   { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_bank) )       { $condition .= str_replace('%s%', $s_bank, ' AND %bank% LIKE "%%s%%"'); }
        if( !empty($s_bill) )       { $condition .= str_replace('%s%', $s_bill, ' AND %bill% LIKE "%%s%%"'); }
        if( !empty($s_bill_name) )  { $condition .= str_replace('%s%', $s_bill_name, ' AND %bill_name% LIKE "%%s%%"'); }
        if( !empty($s_reward) )     { $condition .= str_replace('%s%', $s_reward, ' AND %type% = %s%'); }
        if( !empty($s_status) )     { 
            if( $s_status == 'pending' )        { $stat = 0; }
            elseif( $s_status == 'confirmed' )  { $stat = 1; }
            elseif( $s_status == 'expired' )    { $stat = 2; }
            $condition .= str_replace('%s%', $stat, ' AND %status% = %s%'); 
        }
        if( !empty($s_qualified) )  { $condition .= str_replace('%s%', ( $s_qualified == 'qualified' ? 1 : 0 ), ' AND %qualified% = %s%'); }
        
        if( !empty($s_date_min) )   { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if( !empty($s_date_max) )   { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        
        if( empty($s_reward) ){
            if( !empty($cat) ){
                if($cat == 'promo')     { $option = '1,2,3,4,5,6,7,8'; }
                elseif($cat == 'auto')  { $option = '9,10,11,12,13,14,15,16'; }
                $condition .= ' AND %type% IN('.$option.') ';
            }
        }
        
        if( !empty($condition) ){
            $condition      = substr($condition, 4);
            $condition      = 'WHERE' . $condition;
        }
        
        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%bank% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%bill% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%bill_name% ' . $sort; }
        elseif( $column == 6 )  { $order_by .= '%type% ' . $sort; }
        elseif( $column == 7 )  { $order_by .= '%status% ' . $sort; }
        elseif( $column == 8 )  { $order_by .= '%qualified% ' . $sort; }
        elseif( $column == 9 )  { $order_by .= '%datecreated% ' . $sort; }
        
        $reward_list        = $this->model_member->get_all_member_reward($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($reward_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($reward_list as $row){
				$reward 		= $row->reward_name;
                if( $row->status == 0 )     { $status = '<span class="label label-sm label-default">PENDING</span>'; }
                elseif( $row->status == 1 ) { $status = '<span class="label label-sm label-success">CONFIRMED</span>'; }
                elseif( $row->status == 2 ) { $status = '<span class="label label-sm label-danger">EXPIRED</span>'; }
                
                if( $row->qualified == 0 )      { $qualified = '<span class="label label-sm label-default">NOT QUALIFIED</span>'; }
                elseif( $row->qualified == 1 )  { $qualified = '<span class="label label-sm label-primary">QUALIFIED</span>'; }
                
                $confirmbutton  = ( $row->status == 0 ? '<center><a href="'.base_url('backend/rewardconfirm/'.$row->id).'" class="btn btn-xs btn-primary rewardconfirm">Confirm</a></center>' : '<center><a href="#" class="btn btn-xs btn-default" disabled="">Confirm</a></center>' );
                $bank           = bgn_banks($row->bank);
                
                if( !empty($cat) && $cat == 'promo' ){
                    $records["aaData"][]    = array(
                        '<center>'.$i.'</center>',
                        '<center><a href="'.base_url('profile/'.$row->id_member).'">' . $row->username . '</a></center>',
                        '<a href="'.base_url('profile/'.$row->id_member).'">' . strtoupper($row->name) . '</a>',
                        '<center>'.strtoupper($bank->nama).'</center>',
                        $row->bill,
                        strtoupper($row->bill_name),
                        '<center>'.$reward.'</center>',
                        '<center>'.$status.'</center>',
                        ( $row->status == 2 ? '<span class="text-danger"><strong>'.$row->datecreated.'</strong></span>' : $row->datecreated ),
                        $confirmbutton,
                    );
                }else{
                    $records["aaData"][]    = array(
                        '<center>'.$i.'</center>',
                        '<center><a href="'.base_url('profile/'.$row->id_member).'">' . $row->username . '</a></center>',
                        '<a href="'.base_url('profile/'.$row->id_member).'">' . strtoupper($row->name) . '</a>',
                        '<center>'.strtoupper($bank->nama).'</center>',
                        $row->bill,
                        strtoupper($row->bill_name),
                        '<center>'.$reward.'</center>',
                        '<center>'.$status.'</center>',
                        '<center>'.$qualified.'</center>',
                        ( $row->status == 2 ? '<span class="text-danger"><strong>'.$row->datecreated.'</strong></span>' : $row->datecreated ),
                        $confirmbutton,
                    );
                }
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Confirm reward of member function.
	 */
    function rewardconfirm( $id=0 )
    {
        // Check for AJAX Request
        if( !$this->input->is_ajax_request() ){
            redirect(base_url(), 'location');    
        }
        
        // Set Required Variable
        $data                   = array();
        $datetime               = date('Y-m-d H:i:s');
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        // Check Curret Member Login
        if( !$current_member || empty($current_member) ){
            $data = array('result' => 'login', 'message' => base_url());
            die(json_encode($data));
        }
        
        // Check if current member is member
        if( !$is_admin ) {
            $data = array('result' => 'failed', 'message' => 'Proses konfirmasi reward hanya dapat dilakukan oleh Administrator!');
            die(json_encode($data));
        }
        
        // Check ID Reward Requirement
        if( !$id ) {
            $data = array('result' => 'failed', 'message' => 'Proses konfirmasi reward tidak dapat dilakukan karena ID reward kosong atau tidak tercantum!');
            die(json_encode($data));
        }
        
        // Check Reward Data exist
        $reward_data                = $this->model_member->get_rewarddata($id);
        if( !$reward_data ){
            $data = array('result' => 'failed', 'message' => 'Proses konfirmasi reward gagal. Data reward tidak ditemukan atau belum terdaftar!');
            die(json_encode($data));
        }
        // Check Reward status
        if( $reward_data->status == 1 ){
            $data = array('result' => 'failed', 'message' => 'Proses konfirmasi reward gagal. Staus reward telah dikonfirmasi.');
            die(json_encode($data));
        }
        // Check Reward member
        $memberdata = $this->model_member->get_memberdata($reward_data->id_member);
        if( !$memberdata ){
            $data = array('result' => 'failed', 'message' => 'Data member reward tidak ditemukan atau belum terdaftar.');
            die(json_encode($data));
        }

        // Count Sponsored of Member for Auto Global Reward
        $qualified      = FALSE;
        $expired        = FALSE;
        $type           = absint($reward_data->type);
        $option         = '9,10,11,12,13,14,15,16';
        $option         = explode(',',$option);  
        $date           = $reward_data->datecreated;
        $date_expired   = strtotime($date);
        $date_expired   = date('Y-m-d H:i:s', strtotime('+1 month', $date_expired));
         
        if( in_array($type, $option) ){
            if( $reward_data->qualified == 1 ){
                $qualified      = TRUE;
            }else{
                $sponsored      = $this->model_member->count_sponsored($reward_data->id_member);
                if( $type == 9 )
                { 
                    $qualified = $sponsored >= 1 && (
                    $memberdata->package == PACKAGE_SAPPHIRE || 
                    $memberdata->package == PACKAGE_RUBY || 
                    $memberdata->package == PACKAGE_DIAMOND || 
                    $memberdata->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
                }
                elseif( $type == 10 )
                { 
                    $qualified = $sponsored >= 3 && (
                    $memberdata->package == PACKAGE_SAPPHIRE || 
                    $memberdata->package == PACKAGE_RUBY || 
                    $memberdata->package == PACKAGE_DIAMOND || 
                    $memberdata->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
                }
                elseif( $type == 11 )
                { 
                    $qualified = $sponsored >= 5 && ( 
                    $memberdata->package == PACKAGE_SAPPHIRE || 
                    $memberdata->package == PACKAGE_RUBY || 
                    $memberdata->package == PACKAGE_DIAMOND || 
                    $memberdata->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
                }
                elseif( $type == 12 )
                { 
                    $qualified = $sponsored >= 7 && (
                    $memberdata->package == PACKAGE_SAPPHIRE || 
                    $memberdata->package == PACKAGE_RUBY || 
                    $memberdata->package == PACKAGE_DIAMOND || 
                    $memberdata->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
                }
                elseif( $type == 13 )
                { 
                    $qualified = $sponsored >= 9 && (
                    $memberdata->package == PACKAGE_SAPPHIRE || 
                    $memberdata->package == PACKAGE_RUBY || 
                    $memberdata->package == PACKAGE_DIAMOND || 
                    $memberdata->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
                }
                elseif( $type == 14 )
                { 
                    $qualified = $sponsored >= 11 && (
                    $memberdata->package == PACKAGE_RUBY || 
                    $memberdata->package == PACKAGE_DIAMOND || 
                    $memberdata->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
                }
                elseif( $type == 15 )
                { 
                    $qualified = $sponsored >= 13 && (
                    $memberdata->package == PACKAGE_RUBY || 
                    $memberdata->package == PACKAGE_DIAMOND || 
                    $memberdata->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
                }
                elseif( $type == 16 )
                { 
                    $qualified = $sponsored >= 15 && ( 
                    $memberdata->package == PACKAGE_DIAMOND || 
                    $memberdata->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
                }
            }
            
            if( strtotime($datetime) > strtotime($date_expired) ){
                $expired = TRUE;
            }
        }else{
            $qualified = TRUE;
        }
        
        if( $expired ){
            $datareward             = array('status' => 2, 'datemodified' => $datetime);
            $this->model_member->update_data_reward($id, $datareward);
            $data = array('result' => 'failed', 'message' => 'Proses konfirmasi Reward gagal. Reward sudah expired');
            die(json_encode($data));
        }else{
            if( !$qualified ){
                $data = array('result' => 'failed', 'message' => 'Proses konfirmasi reward gagal. Jumlah member yang disponsori belum memenuhi kualifikasi.');
                die(json_encode($data));
            }
            
            $datareward             = array('status' => 1, 'qualified' => 1, 'datemodified' => $datetime);
            if( $this->model_member->update_data_reward($id, $datareward) ){
                $data = array('result' => 'success', 'message' => 'Proses konfirmasi Reward berhasil.');
                die(json_encode($data));
            }else{
                $data = array('result' => 'failed', 'message' => 'Proses konfirmasi Reward gagal. Terjadi kesalahan data konfirmasi');
                die(json_encode($data));
            }
        }
    }
    
    /**
	 * Confirm all reward all of member function.
	 */
    function rewardconfirmall()
    {
        // Check for AJAX Request
        if( !$this->input->is_ajax_request() ){
            redirect(base_url(), 'location');    
        }
        
        // Set Required Variable
        $data                   = array();
        $datetime               = date('Y-m-d H:i:s');
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        $agr_opt                = '9,10,11,12,13,14,15,16';
        
        // Check Curret Member Login
        if( !$current_member || empty($current_member) ){
            $data = array('result' => 'login', 'message' => base_url());
            die(json_encode($data));
        }
        
        // Check if current member is member
        if( !$is_admin ) {
            $data = array('result' => 'failed', 'message' => 'Proses konfirmasi reward hanya dapat dilakukan oleh Administrator!');
            die(json_encode($data));
        }

        // Get Reward data
        $reward              = $this->model_member->get_all_member_reward(0, 0, ' WHERE %status% = 0 AND %type% IN('.$agr_opt.')');
        if( empty($reward) || !$reward ){
            $data = array('result' => 'failed', 'message' => 'Tidak ada data reward untuk dikonfirmasi!');
            die(json_encode($data));
        }
        
        $qualified              = FALSE;
        $expired                = FALSE;
        $qualified_total        = 0;
        $not_qualified_total    = 0;
        $expired_total          = 0;
        $datareward_total       = 0;
        
        foreach($reward as $row){
            $type               = absint($row->type);
            $date               = $row->datecreated;
            $date_expired       = strtotime($date);
            $date_expired       = date('Y-m-d H:i:s', strtotime('+1 month', $date_expired));
            $sponsored          = $this->model_member->count_sponsored($row->id_member);
            
            if( $type == 9 )
            { 
                $qualified = $sponsored >= 1 && (
                $row->package == PACKAGE_SAPPHIRE || 
                $row->package == PACKAGE_RUBY || 
                $row->package == PACKAGE_DIAMOND || 
                $row->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
            }
            elseif( $type == 10 )
            { 
                $qualified = $sponsored >= 3 && (
                $row->package == PACKAGE_SAPPHIRE || 
                $row->package == PACKAGE_RUBY || 
                $row->package == PACKAGE_DIAMOND || 
                $row->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
            }
            elseif( $type == 11 )
            { 
                $qualified = $sponsored >= 5 && ( 
                $row->package == PACKAGE_SAPPHIRE || 
                $row->package == PACKAGE_RUBY || 
                $row->package == PACKAGE_DIAMOND || 
                $row->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
            }
            elseif( $type == 12 )
            { 
                $qualified = $sponsored >= 7 && (
                $row->package == PACKAGE_SAPPHIRE || 
                $row->package == PACKAGE_RUBY || 
                $row->package == PACKAGE_DIAMOND || 
                $row->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
            }
            elseif( $type == 13 )
            { 
                $qualified = $sponsored >= 9 && (
                $row->package == PACKAGE_SAPPHIRE || 
                $row->package == PACKAGE_RUBY || 
                $row->package == PACKAGE_DIAMOND || 
                $row->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
            }
            elseif( $type == 14 )
            { 
                $qualified = $sponsored >= 11 && (
                $row->package == PACKAGE_RUBY || 
                $row->package == PACKAGE_DIAMOND || 
                $row->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
            }
            elseif( $type == 15 )
            { 
                $qualified = $sponsored >= 13 && (
                $row->package == PACKAGE_RUBY || 
                $row->package == PACKAGE_DIAMOND || 
                $row->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
            }
            elseif( $type == 16 )
            { 
                $qualified = $sponsored >= 15 && ( 
                $row->package == PACKAGE_DIAMOND || 
                $row->package == PACKAGE_BLACK_DIAMOND ) ? TRUE : FALSE; 
            }
            
            if( strtotime($datetime) > strtotime($date_expired) ){
                $expired = TRUE;
            }
            
            if( $expired ){
                $datareward     = array('status' => 2, 'datemodified' => $datetime);
                $this->model_member->update_data_reward($row->id, $datareward);
                $expired_total++;
            }else{
                if( $qualified ){
                    $datareward     = array('status' => 1, 'qualified' => 1, 'datemodified' => $datetime);
                    $this->model_member->update_data_reward($row->id, $datareward);
                    $qualified_total++;
                }else{
                    $not_qualified_total++;
                }
            }
            $datareward_total++;
        }
        
        $data = array(
            'result'    => 'success', 
            'message'   => '
                Proses konfirmasi Reward berhasil. Jumlah data reward yang dikonfirmasi '.$datareward_total.' data.'.br().
                'Total Qualified : '.$qualified_total.br().
                'Total Tidak Qualified : '.$not_qualified_total.br().
                'Total Expired : '.$expired_total
        );
        die(json_encode($data));
        
    }
    
    /**
	 * History function.
	 */
	function history( $id=0  )
	{
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/history'), 'refresh' );
        }
        
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        
        $data['title']          = TITLE . 'History';
        $data['member']         = $current_member;
        $data['main_content']   = 'history';
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * History PIN Transfer List Member function.
	 */
    function historylist()
    {
        $condition          = '';
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
        
        $s_username_send    = bgn_isset($this->input->post('search_username_sender'), '');
        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_qty              = bgn_isset($this->input->post('search_qty'), '');
        $s_package          = bgn_isset($this->input->post('search_package'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_username_send) )  { $condition .= str_replace('%s%', $s_username_send, ' AND %username_sender% LIKE "%%s%%"'); }
        if( !empty($s_username) )       { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_qty) )            { $condition .= str_replace('%s%', $s_name, ' AND %qty% = %s%'); }
        if( !empty($s_package) )        { $condition .= str_replace('%s%', $s_package, ' AND %package% LIKE "%s%"'); }
        
        if ( !empty($s_date_min) )      { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if ( !empty($s_date_max) )      { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        
        if( $column == 1 )      { $order_by .= '%datecreated% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%username_sender% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%username% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%qty% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%package% ' . $sort; }
        
        if( !empty($condition) ){
            $condition      = substr($condition, 4);
            $condition      = 'WHERE' . $condition;
        }

        $transfer_list      = $this->model_member->get_all_member_pin_transfer($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array();

        if( !empty($transfer_list) ){
            $iTotalRecords = bgn_get_last_found_rows();
            
            $i = $offset + 1;
            foreach($transfer_list as $row){
                if($row->package == PACKAGE_SAPPHIRE)           { $package = '<center><span class="label label-sm sapphire">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_RUBY)           { $package = '<center><span class="label label-sm ruby">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_DIAMOND)        { $package = '<center><span class="label label-sm diamond">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_BLACK_DIAMOND)  { $package = '<center><span class="label label-sm blackdiamond">'.strtoupper($row->package).'</span></center>'; }
                
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    '<center>'.$row->datecreated.'</center>',
                    '<center><a href="'.base_url('profile/'.$row->id_member_sender).'">' . $row->username_sender . '</a></center>',
                    '<center><a href="'.base_url('profile/'.$row->id_member).'">' . $row->username . '</a></center>',
                    '<center>'.$row->qty.'</center>',
                    '<center>'.$package.'</center>',
                    '',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * History List My PIN Transfer function.
	 */
    function historylistmine( $id=0 )
    {
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $id_member          = $current_member->id;
        
        $condition          = ' WHERE %id_member_sender% = ' . $id_member;
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_qty              = bgn_isset($this->input->post('search_qty'), '');
        $s_package          = bgn_isset($this->input->post('search_package'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_username) )       { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_qty) )            { $condition .= str_replace('%s%', $s_name, ' AND %qty% = %s%'); }
        if( !empty($s_package) )        { $condition .= str_replace('%s%', $s_package, ' AND %package% LIKE "%s%"'); }
        
        if ( !empty($s_date_min) )      { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if ( !empty($s_date_max) )      { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        
        if( $column == 1 )      { $order_by .= '%datecreated% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%username% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%qty% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%package% ' . $sort; }

        $transfer_list      = $this->model_member->get_all_member_pin_transfer($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 

        if( !empty($transfer_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($transfer_list as $row){
                if($row->package == PACKAGE_SAPPHIRE)           { $package = '<center><span class="label label-sm sapphire">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_RUBY)           { $package = '<center><span class="label label-sm ruby">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_DIAMOND)        { $package = '<center><span class="label label-sm diamond">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_BLACK_DIAMOND)  { $package = '<center><span class="label label-sm blackdiamond">'.strtoupper($row->package).'</span></center>'; }
                
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    '<center>'.$row->datecreated.'</center>',
                    '<center><a href="'.base_url('profile/'.$row->id).'">' . $row->username . '</a></center>',
                    '<center>'.$row->qty.'</center>',
                    '<center>'.$package.'</center>',
                    '',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Omzet function.
	 */
	function omzet()
	{       
        auth_redirect();

        $package                        = config_item('paket');
        $current_member                 = bgn_get_current_member();
        
        $total_omzet                    = $this->model_member->count_all_omzet();
        $total_omzet_sapphire           = $this->model_member->count_all_omzet(PACKAGE_SAPPHIRE);
        $total_omzet_ruby               = $this->model_member->count_all_omzet(PACKAGE_RUBY);
        $total_omzet_diamond            = $this->model_member->count_all_omzet(PACKAGE_DIAMOND);
        $total_omzet_blackdiamond       = $this->model_member->count_all_omzet(PACKAGE_BLACK_DIAMOND);

        $total_omzet_sapphire_tm        = $this->model_member->count_all_omzet(PACKAGE_SAPPHIRE, date('Y-m'));
        $total_omzet_ruby_tm            = $this->model_member->count_all_omzet(PACKAGE_RUBY, date('Y-m'));
        $total_omzet_diamond_tm         = $this->model_member->count_all_omzet(PACKAGE_DIAMOND, date('Y-m'));
        $total_omzet_blackdiamond_tm    = $this->model_member->count_all_omzet(PACKAGE_BLACK_DIAMOND, date('Y-m'));

        $total_sapphire                 = $this->model_member->count_member_old(PACKAGE_SAPPHIRE);
        $total_ruby                     = $this->model_member->count_member_old(PACKAGE_RUBY);
        $total_diamond                  = $this->model_member->count_member_old(PACKAGE_DIAMOND);
        $total_blackdiamond             = $this->model_member->count_member_old(PACKAGE_BLACK_DIAMOND);
        $total_sapphire_tm              = $this->model_member->count_member_old(PACKAGE_SAPPHIRE, date('Y-m'));
        $total_ruby_tm                  = $this->model_member->count_member_old(PACKAGE_RUBY, date('Y-m'));
        $total_diamond_tm               = $this->model_member->count_member_old(PACKAGE_DIAMOND, date('Y-m'));
        $total_blackdiamond_tm          = $this->model_member->count_member_old(PACKAGE_BLACK_DIAMOND, date('Y-m'));
        
        $total_bonus                    = $this->model_member->count_all_bonus_total();
        $total_deposite                 = $this->model_member->count_all_deposite();
		$total_deposite_less_data       = $this->model_member->get_all_member_deposite(0,0,'','',' AND %total% > 1 AND %total% < 100000');
        $total_deposite_less            = 0;
        if( !empty($total_deposite_less_data) ){
    		foreach($total_deposite_less_data as $row){
                $total_deposite_less   += $row->total;
			}
        }
		
        $data['title']                      = TITLE . 'Omzet';
        $data['member']                     = $current_member;
        
        $data['total_omzet']                = $total_omzet;
        $data['total_omzet_sapphire']       = $total_omzet_sapphire;
        $data['total_omzet_ruby']           = $total_omzet_ruby;
        $data['total_omzet_diamond']        = $total_omzet_diamond;
        $data['total_omzet_blackdiamond']   = $total_omzet_blackdiamond;
        $data['total_omzet_sapphire_tm']    = $total_omzet_sapphire_tm;
        $data['total_omzet_ruby_tm']        = $total_omzet_ruby_tm;
        $data['total_omzet_diamond_tm']     = $total_omzet_diamond_tm;
        $data['total_omzet_blackdiamond_tm']= $total_omzet_blackdiamond_tm;
        
        $data['total_sapphire']             = $total_sapphire;
        $data['total_ruby']                 = $total_ruby;
        $data['total_diamond']              = $total_diamond;
        $data['total_blackdiamond']         = $total_blackdiamond;
        $data['total_sapphire_tm']          = $total_sapphire_tm;
        $data['total_ruby_tm']              = $total_ruby_tm;
        $data['total_diamond_tm']           = $total_diamond_tm;
        $data['total_blackdiamond_tm']      = $total_blackdiamond_tm;
        
        $data['total_bonus']                = $total_bonus;
        $data['total_deposite']             = $total_deposite;
        $data['total_deposite_less']        = $total_deposite_less;
		
        $data['main_content']               = 'omzet';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * PIN function.
	 */
	function pin( $id=0  )
	{
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/pin'), 'refresh' );
        }
            
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        
        $data['title']          = TITLE . 'PIN';
        $data['member']         = $current_member;
        $data['main_content']   = 'pin';
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * PIN List Member function.
	 */
    function pinlist()
    {
        $condition          = 'WHERE %type% = 1 AND %total% > 0';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username        = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_total            = bgn_isset($this->input->post('search_total'), '');
        
        if( !empty($s_username) )  { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_total) )      { $condition .= str_replace('%s%', $s_total, ' AND %total% = %s%'); }
        
        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%total% ' . $sort; }
        
        $pin_list        = $this->model_member->get_all_member_pin($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
    
        if( !empty($pin_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            
            foreach($pin_list as $row){ 
                if( $row->type != 2 ){
                    $records["aaData"][]    = array(
                        '<center>'.$i.'</center>',
                        '<center><a href="'.base_url('profile/'.$row->id).'">' . $row->username . '</a></center>',
                        '<a href="'.base_url('profile/'.$row->id).'">' . strtoupper($row->name) . '</a>',
                        '<center>' .( !empty($row->total) ? $row->total : 0 ) . '</center>',
                        '<center><a href="'.base_url('report/pin/'.$row->id).'" class="btn btn-xs btn-success">Detail</a></center>',
                    );
                    $i++;
                }
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * My PIN List function.
	 */
    function pinlistmine( $id=0 )
    {
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $id_member          = ( $id > 0 ? $id : $current_member->id );
        
        $condition          = '';
        $order_by           = '';
        $status_pin         = 0;
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_id_pin           = bgn_isset($this->input->post('search_id_pin'), '');
        $s_sender           = bgn_isset($this->input->post('search_sender'), '');
        $s_package          = bgn_isset($this->input->post('search_package'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        $s_trans_min        = bgn_isset($this->input->post('search_datetransfer_min'), '');
        $s_trans_max        = bgn_isset($this->input->post('search_datetransfer_max'), '');
        
        if( !empty($s_id_pin) )     { $condition .= str_replace('%s%', $s_id_pin, ' AND %id_pin% LIKE "%%s%%"'); }
        if( !empty($s_sender) )     { $condition .= str_replace('%s%', $s_sender, ' AND %username_sender% LIKE "%%s%%"'); }
        if( !empty($s_package) )    { $condition .= str_replace('%s%', $s_package, ' AND %package% = "%s%"'); }
        if( !empty($s_status) )     { 
            if( $s_status == 'pending' )    { $status_pin = 0; }
            elseif( $s_status == 'active' ) { $status_pin = 1; }
            elseif( $s_status == 'used' )   { $status_pin = 2; }
            $condition .= str_replace('%s%', $status_pin, ' AND %status% = %s%'); 
        }
        
        if ( !empty($s_date_min) )  { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if ( !empty($s_date_max) )  { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        
        if ( !empty($s_trans_min) ) { $condition .= ' AND %datetransfer% >= '.strtotime($s_trans_min).''; }
        if ( !empty($s_trans_max) ) { $condition .= ' AND %datetransfer% <= '.strtotime($s_trans_max).''; }
        
        if( $column == 1 )      { $order_by .= '%id_pin% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%username_sender% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%package% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%status% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%datecreated% ' . $sort; }
        elseif( $column == 6 )  { $order_by .= '%datetransfer% ' . $sort; }
        
        $pin_list           = $this->model_member->get_all_my_pin($id_member, $limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($pin_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($pin_list as $row){ 
                if($row->status == 0)       { $status = '<center><span class="label label-sm label-info">PENDING</span></center>'; }
                elseif($row->status == 1)   { $status = '<center><span class="label label-sm label-success">ACTIVE</span></center>'; }
                elseif($row->status == 2)   { $status = '<center><span class="label label-sm label-danger">USED</span></center>'; }
               
                if($row->package == PACKAGE_SAPPHIRE)           { $package = '<center><span class="label label-sm sapphire">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_RUBY)           { $package = '<center><span class="label label-sm ruby">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_DIAMOND)        { $package = '<center><span class="label label-sm diamond">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_BLACK_DIAMOND)  { $package = '<center><span class="label label-sm blackdiamond">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_CASH_REWARD)    { $package = '<center><span class="label label-sm cashreward">'.strtoupper($row->package).'</span></center>'; }
               
                $confirmbutton  = ( $row->status == 0 ? '<a href="'.base_url('backend/pinactivate/'.$row->id).'" class="btn btn-xs btn-primary pinactivate">Activate</a>' : '<a href="#" class="btn btn-xs btn-default" disabled="">Activate</a>' );
                $deletebutton   = ( $row->status != 2 ? ' <a href="'.base_url('backend/pindelete/'.$row->id).'" class="btn btn-xs btn-danger pindelete">Delete</a>' : ' <a href="#" class="btn btn-xs btn-default" disabled="">Delete</a>' );
                
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    '<center>'.$row->id_pin.'</center>',
                    '<center>'. ( $row->username_sender != "admin" ? '<a href="'.base_url('profile/'.$row->id_member_sender).'">'.$row->username_sender.'</a>' : $row->username_sender ) . '</center>',
                    $package,
                    $status, 
                    '<center>'.$row->datecreated.'</center>',
                    '<center>'. ( $row->datetransfer == "0000-00-00 00:00:00" ? $row->datecreated : $row->datetransfer ) .'</center>',
                    '<center>'.( $is_admin ? $confirmbutton . $deletebutton : '' ).'</center>',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * PIN Used List function.
	 */
    function pinlistused()
    {
        $condition          = 'WHERE %id_member_registered% > 0';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_id_pin           = bgn_isset($this->input->post('search_id_pin'), '');
        $s_username        = bgn_isset($this->input->post('search_username'), '');
        $s_memberres        = bgn_isset($this->input->post('search_member_register'), '');
        $s_memberreg        = bgn_isset($this->input->post('search_member_registered'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_id_pin) )     { $condition .= str_replace('%s%', $s_id_pin, ' AND %id_pin% LIKE "%%s%%"'); }
        if( !empty($s_username) )  { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_memberres) )  { $condition .= str_replace('%s%', $s_memberres, ' AND %memberres% LIKE "%%s%%"'); }
        if( !empty($s_memberreg) )  { $condition .= str_replace('%s%', $s_memberreg, ' AND %memberreg% LIKE "%%s%%"'); }
        
        if ( !empty($s_date_min) && empty($s_date_max) )            { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        elseif ( empty($s_date_min) && !empty($s_date_max) )        { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        elseif ( !empty($s_date_min) && !empty($s_date_max) )       { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).' AND %datecreated% <= '.strtotime($s_date_max).''; } 
        
        if( $column == 1 )      { $order_by .= '%id_pin% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%username% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%memberres% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%memberreg% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%datecreated% ' . $sort; }
        
        $pin_list           = $this->model_member->get_all_pin($limit, $offset, $condition, $order_by);

        $records            = array();
        $records["aaData"]  = array(); 
    
        if( !empty($pin_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($pin_list as $row){ 
                if( $row->type != 2 ){
                    $records["aaData"][]    = array(
                        '<center>'.$i.'</center>',
                        '<center>'.$row->id_pin.'</center>',
                        '<center><a href="'.base_url('profile/'.$row->id).'">' . $row->username . '</a></center>',
                        '<center><a href="'.base_url('profile/'.$row->id_register).'">' . $row->username_register . '</a></center>',
                        '<center><a href="'.base_url('profile/'.$row->id_registered).'">' . $row->username_registered . '</a></center>',
                        '<center>' . $row->datecreated . '</center>',
                        '',
                    );
                    $i++;
                }
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * PIN Order List function.
	 */
    function pinorder( $id=0 )
    {        
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $id_member          = ( $id > 0 ? $id : $current_member->id );
        $condition          = ( $id > 0 ? 'WHERE %id_member% = ' . $id_member : '' );
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_package          = bgn_isset($this->input->post('search_package'), '');
        $s_qty              = bgn_isset($this->input->post('search_qty'), '');
		$s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        
        if( !empty($s_username) )   { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_qty) )        { $condition .= str_replace('%s%', $s_qty, ' AND %qty% = %s%'); }
        if( !empty($s_package) )    { $condition .= str_replace('%s%', $s_package, ' AND %package% LIKE "%s%"'); }
        if( !empty($s_status) )     { 
            if( $s_status == 'review' )         { $s_status = 0; }
            elseif( $s_status == 'confirmed' )  { $s_status = 1; } 
            $condition .= str_replace('%s%', $s_status, ' AND %status% = %s%'); 
        }
        if( !empty($s_date_min) )	{ $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if( !empty($s_date_max) )	{ $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
		
        if($id > 0){
            if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
            elseif( $column == 2 )  { $order_by .= '%package% ' . $sort; }
            elseif( $column == 3 )  { $order_by .= '%qty% ' . $sort; }
			elseif( $column == 4 )  { $order_by .= '%datecreated% ' . $sort; }
            elseif( $column == 5 )  { $order_by .= '%status% ' . $sort; }
        }else{
            if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
            elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
            elseif( $column == 3 )  { $order_by .= '%package% ' . $sort; }
            elseif( $column == 4 )  { $order_by .= '%qty% ' . $sort; }
			elseif( $column == 5 )  { $order_by .= '%datecreated% ' . $sort; }
            elseif( $column == 6 )  { $order_by .= '%status% ' . $sort; }
        }
        
        if( $id == 0 && !empty($condition) ){
            $condition      = substr($condition, 4);
            $condition      = 'WHERE' . $condition;
        }
        
        $porder_list        = $this->model_member->get_all_member_pin_order($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($porder_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($porder_list as $row){          
                $status         = ( $row->status == 0 ? 'REVIEW' : 'CONFIRMED' );
                $confirmbutton  = ( $row->status == 0 ? '<a href="'.base_url('backend/pinorderconfirm/'.$row->id).'" title="Confirm" class="btn btn-xs btn-primary pinconfirm"><i class="fa fa-check"></i></a>' : '<a href="#" title="Confirm" class="btn btn-xs btn-default" disabled=""><i class="fa fa-check"></i></a>' );
                $editbutton     = ( $row->status == 0 ? '<a href="'.base_url('backend/pinorderedit/'.$row->id).'" title="Edit" class="btn btn-xs btn-warning pinorderedit"><i class="fa fa-edit"></i></a>' : '<a href="#" title="Edit" class="btn btn-xs btn-default" disabled=""><i class="fa fa-edit"></i></a>' );
                $deletebutton   = ( $row->status == 0 ? '<a href="'.base_url('backend/pinorderdelete/'.$row->id).'" title="Delete" class="btn btn-xs btn-danger pinorderdelete"><i class="fa fa-times"></i></a>' : '<a href="#" title="Edit" class="btn btn-xs btn-default" disabled=""><i class="fa fa-times"></i></a>' );
                
                if($row->package == PACKAGE_SAPPHIRE)           { $package = '<center><span class="label label-sm sapphire">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_RUBY)           { $package = '<center><span class="label label-sm ruby">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_DIAMOND)        { $package = '<center><span class="label label-sm diamond">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_BLACK_DIAMOND)  { $package = '<center><span class="label label-sm blackdiamond">'.strtoupper($row->package).'</span></center>'; }
                elseif($row->package == PACKAGE_CASH_REWARD)    { $package = '<center><span class="label label-sm cashreward">'.strtoupper($row->package).'</span></center>'; }
                    
                if( $id > 0 ){
                    $records["aaData"][]    = array(
                        '<center>'.$i.'</center>',
                        '<center><a href="'.base_url('profile/'.$row->id).'">' . $row->username . '</a></center>',
                        '<center>'.$package.'</center>',
                        '<center>' . ( !empty($row->qty) ? $row->qty : 0 ) . '</center>',
                        '<center>'.$row->datecreated.'</center>',
						'<center><span class="label label-sm label-'.( $status == 'REVIEW' ? 'default' : 'success' ).'">'.$status.'</span></center>',
                        '<center>' . ( $is_admin ? $confirmbutton . ' ' . $editbutton . ' ' . $deletebutton : '' ) . '</center>',
                    );
                }else{
                    $records["aaData"][]    = array(
                        '<center>'.$i.'</center>',
                        '<center><a href="'.base_url('profile/'.$row->id).'">' . $row->username . '</a></center>',
                        '<a href="'.base_url('profile/'.$row->id).'">' . strtoupper($row->name) . '</a>',
                        '<center>'.$package.'</center>',
                        '<center>' . ( !empty($row->qty) ? $row->qty : 0 ) . '</center>',
						'<center>'.$row->datecreated.'</center>',
                        '<center><span class="label label-sm label-'.( $status == 'REVIEW' ? 'default' : 'success' ).'">'.$status.'</span></center>',
                        '<center>' . ( $is_admin ? $confirmbutton . ' ' . $editbutton . ' ' . $deletebutton : '' ) . '</center>',
                    );   
                }
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Manage PIN function.
	 */
	function pinmanage()
	{
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if( !$is_admin && $current_member->as_stockist == 0 ){
            redirect(base_url(), 'location'); die();
        }
        
        $data['title']          = TITLE . ($is_admin ? 'Manajemen PIN' : 'Pemesanan PIN' );
        $data['member']         = $current_member;
        $data['is_admin']       = $is_admin;
        $data['main_content']   = 'pinmanage';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Confirm PIN Order function.
	 */
	function pinorderconfirm( $id=0 )
	{
        if( !$id ) { echo 'failed'; die(); }
        
        $pin_order              = $this->model_member->get_pin_order($id);
        
        if( !$pin_order ){
            echo 'failed'; die();
        }
        
        if( $pin_order->status == 1 ){
            echo 'failed'; die();
        }
        
        $package                = $pin_order->package;
        $qty                    = $pin_order->qty;
        $qty                    = absint($qty);
        $curdate                = date('Y-m-d H:i:s');
        
        for($i=1; $i <= $qty; $i++){
            $data_pin           = array(
                'id_pin'        => strtoupper(substr($package,0,1)) . strtoupper(random_string('alnum',10)),
                'id_order_pin'  => $id,
                'id_member'     => $pin_order->id_member,
                'status'        => 1,
                'package_old'   => $package,
                'package'       => $package,
                'datecreated'   => $curdate,
                'datemodified'  => $curdate,
            );
            $this->model_member->save_data_pin($data_pin);
        }
        
        $this->model_member->update_pin_order_confirmed($id);
        
        echo 'success'; die();
    }
    
    /**
	 * Edit PIN Order function.
	 */
	function pinorderedit( $id=0 )
	{
        if( !$id ) { echo 'failed'; die(); }
        
        $pin_order              = $this->model_member->get_pin_order($id);
        
        if( !$pin_order ){
            // Set JSON data
            $data = array('message' => 'failed','data' => '');
            // JSON encode data
            die(json_encode($data));
        }
        
        if( $pin_order->status == 1 ){
            // Set JSON data
            $data = array('message' => 'failed','data' => '');
            // JSON encode data
            die(json_encode($data));
        }
        
        $member                 = bgn_get_memberdata_by_id($pin_order->id_member);
        $pin_order->username    = $member->username;
        
        // Set JSON data
        $data = array('message' => 'success','data' => $pin_order);
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Save Edit Order PIN function.
	 */
    function pinordereditact()
    {
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        $curdate                = date('Y-m-d H:i:s');
        
        $id_order_pin           = bgn_isset($this->input->post('pin_id'));
        $username               = bgn_isset($this->input->post('pin_username'));
        $package                = bgn_isset($this->input->post('pin_package'));
        $qty                    = bgn_isset($this->input->post('pin_qty'));
        $qty                    = absint($qty);
        
        if( !empty($username) ){
            $memberdata         = $this->model_member->get_member_by('login', strtolower($username));
            $is_admin           = as_administrator($memberdata);

            if( !$memberdata ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Username tidak ditemukan. Silahkan input Username lainnya!',
                );
            }elseif( $is_admin ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Admin tidak memerlukan PIN. Silahkan input Username lainnya!',
                );
            }elseif( $memberdata->as_stockist == 0 ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Username bukan stockist. Silahkan input stockist Username lainnya!',
                );
            }else{
                $data_pin_order     = array(
                    'package'       => $package,
                    'qty'           => $qty,
                    'datemodified'  => $curdate,
                );
                if( !empty($username) ){
                    $data_pin_order['id_member'] = $memberdata->id;
                }
                $save_order_pin     = $this->model_member->update_pin_order($id_order_pin, $data_pin_order);
                
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => '<button class="close" data-close="alert"></button>Save Order PIN sudah berhasil!',
                );
            }
            // JSON encode data
            die(json_encode($data));
        } 
    }
    
    /**
	 * PIN Order Delete function.
	 */
    function pinorderdelete( $id=0 )
    {
        if( !$id ) { echo 'failed'; die(); }
        
        $pin_order              = $this->model_member->get_pin_order($id);
        
        if( !$pin_order ){ echo 'failed'; die(); }
        if( $pin_order->status == 1 ){ echo 'failed'; die(); }
        
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if( !$is_admin ) { echo 'failed'; die(); }

        if( $this->model_member->delete_pin_order($id) ){
            echo 'success'; die();
        }else{
            echo 'failed'; die();
        }
    }
    
    /**
	 * PIN Activate function.
	 */
	function pinactivate( $id=0 )
	{
        if( !$id ) { echo 'failed'; die(); }
        
        $pin_data              = $this->model_member->get_pin_by_id($id);
        
        if( !$pin_data ){ echo 'failed'; die(); }
        if( $pin_data->status == 1 ){ echo 'failed'; die(); }

        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if( !$is_admin ) { echo 'failed'; die(); }

        if( $this->model_member->update_pin_active($id) ){
            echo 'success'; die();
        }else{
            echo 'failed'; die();
        }
    }
    
    /**
	 * PIN Delete function.
	 */
    function pindelete( $id=0 )
    {
        if( !$id ) { echo 'failed'; die(); }
        
        $pin_data              = $this->model_member->get_pin_by_id($id);
        
        if( !$pin_data ){ echo 'failed'; die(); }
        if( $pin_data->status == 2 ){ echo 'failed'; die(); }
        
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if( !$is_admin ) { echo 'failed'; die(); }

        if( $this->model_member->delete_pin($id) ){
            echo 'success'; die();
        }else{
            echo 'failed'; die();
        }
    }
    
    /**
	 * Manage Generate PIN function.
	 */
    function pingenerate()
    {
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        $curdate                = date('Y-m-d H:i:s');
        
        $username               = bgn_isset($this->input->post('pin_search_downline'));
        $package                = bgn_isset($this->input->post('pin_package'));
        $qty                    = bgn_isset($this->input->post('pin_qty'));
        $qty                    = absint($qty);
        
        if( !empty($username) ){
            $memberdata         = $this->model_member->get_member_by('login', strtolower($username));
            $is_admin           = as_administrator($memberdata);
            $stockist          = $memberdata->as_stockist;
            
            // If member data doesn't exist or result is empty
            if( !$memberdata || empty($memberdata) ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Member ID tidak ditemukan. Silahkan input member ID lainnya!',
                );
                // JSON encode data
                die(json_encode($data));
            }
            
            // If member isn's stockist, generate member just for stockist
            if( $stockist == 0 ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Member ID bukan stockist. Silahkan input stockist ID lainnya!',
                );
                // JSON encode data
                die(json_encode($data));
            }
            
            // If member is admin
            if( $is_admin ){
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Admin tidak memerlukan kupon. Silahkan input member ID lainnya!',
                );
                // JSON encode data
                die(json_encode($data));
            }
            
            // Begin Transaction
            // -------------------------------------------------
            $this->db->trans_begin();

            // Process generate PIN
            $data_pin_order     = array(
                'id_member'     => $memberdata->id,
                'qty'           => $qty,
                'package_old'   => $package,
                'package'       => $package,
                'status'        => 1,
                'datecreated'   => $curdate,
                'datemodified'  => $curdate,
            );
            $id_order_pin       = $this->model_member->save_data_pin_order($data_pin_order);
            
            $data_pin           = array();
            for($i=1; $i<=$qty; $i++){
                $data_pin[]     = array(
                    'id_pin'            => strtoupper(substr($package,0,1)) . strtoupper(random_string('alnum',10)),
                    'id_order_pin'      => $id_order_pin,
                    'id_member'         => $memberdata->id,
                    'id_member_owner'   => $memberdata->id,
                    'package_old'       => $package,
                    'package'           => $package,
                    'status'            => 1,
                    'datecreated'       => $curdate,
                    'datemodified'      => $curdate,
                );
            }
            
            if( !empty($data_pin) ){
                foreach($data_pin as $row){
                    $this->model_member->save_data_pin($row);
                }
            }else{
                // Rollback Transaction
                $this->db->trans_rollback();
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => '<button class="close" data-close="alert"></button>Generate PIN tidak berhasil!',
                );
                // JSON encode data
                die(json_encode($data));
            }
            
            // Commit Transaction
            $this->db->trans_commit();
            // Complete Transaction
            $this->db->trans_complete();
            
            // Set JSON data
            $data = array(
                'message'   => 'success',
                'data'      => '<button class="close" data-close="alert"></button>Generate PIN sudah berhasil!',
            );
            // JSON encode data
            die(json_encode($data));
        } 
    }
    
    /**
	 * Manage Generate Order PIN function.
	 */
    function pingenerateorder()
    {
        auth_redirect();
        
        $current_member     = bgn_get_current_member();
        $curdate            = date('Y-m-d H:i:s');
        $package            = bgn_isset($this->input->post('pin_package'));
        $qty                = bgn_isset($this->input->post('pin_qty'));
        $qty                = absint($qty);
        
        // If member isn's stockist, generate member just for stockist
        if( $current_member->as_stockist == 0 ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Order PIN hanya bisa dilakukan oleh Stockist!',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $data_pin_order     = array(
            'id_member'     => $current_member->id,
            'qty'           => $qty,
            'package_old'   => $package,
            'package'       => $package,
            'datecreated'   => $curdate,
            'datemodified'  => $curdate,
        );
        $id_order_pin       = $this->model_member->save_data_pin_order($data_pin_order);
        
        if( !$id_order_pin ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Generate order PIN tidak berhasil!',
            );
        }else{
            // Set JSON data
            $data = array(
                'message'   => 'success',
                'data'      => '<button class="close" data-close="alert"></button>Generate order PIN sudah berhasil!',
            );
        }
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Check PIN Before Transfer function.
	 */
    function pincheck(){
        auth_redirect();
        
        $current_member     = bgn_get_current_member();
        $package            = bgn_isset($this->input->post('package'));
        
        if( !$package || empty($package) ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $my_pin_active      = $this->model_member->get_pins($current_member->id,'active',true,$package);
        // Set JSON data
        $data = array(
            'message'   => 'success',
            'data'      => '
            <div class="form-group">
				<label class="col-md-3 control-label">PIN Stock</label>
				<div class="col-md-7">
					<input type="text" name="pin_stock_dsb" class="form-control" placeholder="PIN Stock" disabled="" value="'.$my_pin_active.'" />
				</div>
			</div>',
        );
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * Transfer PIN function.
	 */
    function pintransfer()
    {
        auth_redirect();
        
        $current_member     = bgn_get_current_member();
        $curdate            = date('Y-m-d H:i:s');
        $password           = bgn_isset($this->input->post('pin_password'));
        $username           = bgn_isset($this->input->post('pin_search_downline'));
        $id_member          = bgn_isset($this->input->post('pin_downline_id'));
        $package            = bgn_isset($this->input->post('pin_package'));
        $qty                = bgn_isset($this->input->post('pin_qty'));
        $qty                = absint($qty);
        
        if( md5($password) != $current_member->password_pin ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Password Transfer PIN Anda tidak sesuai!',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $member             = $this->model_member->get_memberdata( absint($id_member) );
        if( !$member || empty($member) ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Data downline tidak sesuai!',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $my_pin_active      = $this->model_member->get_pins($current_member->id,'active',false,$package);
        $total_pin			= count( $my_pin_active );
        
        if( $total_pin == 0 ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Anda tidak memiliki PIN</strong> untuk di transfer!',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        if( $qty > $total_pin ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Anda hanya dapat men-transfer maksimal '.$total_pin.' PIN!',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $this->db->trans_begin();
        
        // Select some pins
        $transferred_pins       = array_slice( $my_pin_active, 0, $qty );
        $transferred_pin_ids    = array();
        
        foreach( $transferred_pins as $pin ) {
            $data_pin_transfer  = array(
                'id_member_sender'      => $current_member->id,
                'username_sender'       => $current_member->username,
                'id_member'             => $member->id,
                'username'              => $member->username,
                'id_pin'                => $pin->id,
                'package_old'           => $package,
                'package'               => $package,
                'datecreated'           => $curdate,
                'datemodified'          => $curdate,
            );
            
            if( $this->model_member->save_data_pin_tansfer($data_pin_transfer) ){
                $transferred_pin_ids[]  = $pin->id;
            }
        }
        
        // Update pins owner
		$this->model_member->update_pin( $transferred_pin_ids, array( 'id_member' => $id_member ) );
        
        $this->db->trans_commit();
        $this->db->trans_complete();
        
        // Set JSON data
        $data = array(
            'message'   => 'success',
            'data'      => '<button class="close" data-close="alert"></button>Transfer PIN sudah berhasil!',
        );
        // JSON encode data
        die(json_encode($data));
    }
    
    /**
	 * PIN Data function.
	 */
	function pindata()
	{
        auth_redirect();
        
        $pin_pending            = $this->model_member->count_all_pin('','pending');
        $pin_active             = $this->model_member->count_all_pin('','active');
        $pin_used               = $this->model_member->count_all_pin('','used');
        
        // Set JSON data
        $data = array(
            'pin_pending'       => $pin_pending,
            'pin_active'        => $pin_active,
            'pin_used'          => $pin_used,
        );
        // JSON encode data
        die(json_encode($data));
	}
    
    /**
	 * Commission function.
	 */
	function commission( $id=0, $date='' )
	{
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('commission'), 'refresh' );
        }
        
        $data['title']          = TITLE . 'Komisi';
        $data['member']         = $current_member;
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        $data['main_content']   = 'commission';
        $data['date']			= $date;
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Commission List function.
	 */
    function commissionlist(){
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        
        $condition          = '';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username        = trim( bgn_isset($this->input->post('search_username'), '') );
        $s_name             = trim( bgn_isset($this->input->post('search_name'), '') );
        $s_nominal_min      = trim( bgn_isset($this->input->post('search_nominal_min'), '') );
        $s_nominal_max      = trim( bgn_isset($this->input->post('search_nominal_max'), '') );
        
        if( !empty($s_username) )  { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        
        if ( !empty($s_nominal_min) && empty($s_nominal_max) )      { $condition .= ' AND %total% >= '.$s_nominal_min.''; }
        elseif ( empty($s_nominal_min) && !empty($s_nominal_max) )  { $condition .= ' AND %total% <= '.$s_nominal_max.''; }
        elseif ( !empty($s_nominal_min) && !empty($s_nominal_max) ) { $condition .= ' AND %total% BETWEEN '.$s_nominal_min.' AND '.$s_nominal_max.''; }

        if ($date = $this->input->post('search_date_commission')) $condition .= ' AND DATE(%date%)="' . $date . '"';
        
        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%total% ' . $sort; }
        
        $commission_list    = $this->model_member->get_all_member_commission($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($commission_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($commission_list as $row){

                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    '<center><a href="'.base_url('profile/'.$row->id_member).'">' . $row->username . '</a></center>',
                    strtoupper($row->name),
                    bgn_accounting($row->total, config_item('currency'), TRUE),
                    '<center><a href="'.base_url('backend/commission/'.$row->id_member. '/' . $date).'" class="btn btn-xs btn-warning">Detail</a></center>',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Deposite function.
	 */
	function deposite()
	{
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        $data['title']          = TITLE . 'Deposite';
        $data['member']         = $current_member;
        $data['is_admin']       = $is_admin;
        $data['main_content']   = 'deposite';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Deposite List function.
	 */
    function depositelist(){
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        
        $condition          = '';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        
        if( !empty($s_username) )       { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )           { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        
        if ( !empty($s_nominal_min) )   { $condition .= ' AND %total% >= '.$s_nominal_min.''; }
        if ( !empty($s_nominal_max) )   { $condition .= ' AND %total% <= '.$s_nominal_max.''; }
        
        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%total% ' . $sort; }
        
        $deposite_list      = $this->model_member->get_all_member_deposite($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($deposite_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $currency		= config_item('currency');
            $i = $offset + 1;
            foreach($deposite_list as $row){

                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    '<center><a href="'.base_url('profile/'.$row->id).'">' . $row->username . '</a></center>',
                    $row->name,
                    bgn_accounting($row->total, $currency, TRUE),
                    '',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }	
    
    /**
	 * Deposite function.
	 */
	function poinreward()
	{
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
		
		if ($amount = $this->input->post('poinreward_amount')) {
			$poin_available = $this->model_member->count_bonus_poin($current_member->id);
			
			if ($amount <= $poin_available) {
				// Insert to bonus poin usage
				if ($this->model_member->use_bonus_poin($current_member->id, $amount)) {
					// Check if this bonus usage generate reward
					if ($reward_option = bgn_reward_option($amount)) {
						// Insert reward
						$datetime = date('Y-m-d H:i:s');
						$save_reward = $this->model_member->save_data_reward(array(
							'id_member' 	=> $current_member->id,
							'type' 			=> $amount,
							'status'		=> 0,
							'datecreated'	=> $datetime,
							'datemodified'	=> $datetime,
						));
						
						if ($save_reward) {
							$this->bgn_sms->sms_reward(
								$current_member->phone, 
								$current_member->username, 
								$reward_option->reward_name . ' (' . $reward_option->reward_nominal . ')'
							);
						}
					}
					
					// redirect
					$this->session->set_flashdata('insert_poinreward', true);
					redirect('poinreward');
				}
			}
		}
		
		$reward_option			= bgn_reward_option();
        
        $data['title']          = TITLE . 'Tukar Poin';
        $data['member']         = $current_member;
        $data['is_admin']       = $is_admin;
        $data['main_content']   = 'poinreward';
		$data['reward_option']	= $reward_option;
		
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Deposite List function.
	 */
    function poinrewardlist(){
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        
        $condition          = '';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username        = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        
        if( !empty($s_username) )  { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        
        if ( !empty($s_nominal_min) )	{ $condition .= ' AND %total% >= '.$s_nominal_min.''; }
        if ( !empty($s_nominal_max) )	{ $condition .= ' AND %total% <= '.$s_nominal_max.''; }
        
        if( !empty($condition) ){
            $condition      = substr($condition, 4);
            $condition      = 'WHERE' . $condition;
        }
        
        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%total% ' . $sort; }
        
        $deposite_list      = $this->model_member->get_all_member_poin_usage($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($deposite_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($deposite_list as $row){

                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    '<center><a href="'.base_url('profile/'.$row->id).'">' . $row->username . '</a></center>',
                    $row->name,
                    $row->amount,
                    $row->message,
                    '',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Poinreward List Mine function.
	 */
    function poinrewardlistmine(){
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        
        $condition          = ' WHERE id_member = ' . $current_member->id;
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        
        if( !empty($s_status) )     	{ $condition .= str_replace('%s%', ($s_status == 'pending' ? 0 : 1), ' AND %status% = %s%'); }
        if ( !empty($s_nominal_min) )	{ $condition .= ' AND %amount% >= '.$s_nominal_min.''; }
        if ( !empty($s_nominal_max) )	{ $condition .= ' AND %amount% <= '.$s_nominal_max.''; }

        
        if( $column == 1 )      { $order_by .= '%amount% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%status% ' . $sort; }
        
        $withdraw_list      = $this->model_member->get_all_member_bonus_poin_usage($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($withdraw_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($withdraw_list as $row){

                $status     = ( $row->status == 0 ? 'Pending' : 'Transfered' );
                
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    $row->amount . ' Poin',
                    '<center><span class="label label-sm label-'.( $status == 'Pending' ? 'default' : 'success' ).'">'.$status.'</span></center>',
                    $row->message,
                    '',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Withdraw function.
	 */
	function withdraw()
	{
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        $data['title']          = TITLE . 'Withdraw';
        $data['member']         = $current_member;
        $data['is_admin']       = $is_admin;
        $data['main_content']   = 'withdraw';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Withdraw function.
	 */
	function withdrawal()
	{
        auth_redirect();
        
        $curdate                = date('Y-m-d H:i:s');
        $current_member         = bgn_get_current_member();
        $currency               = config_item('currency');
        $amount                 = bgn_isset($this->input->post('withdraw_amount'));
        $amount                 = absint($amount);
        $withdraw_min           = absint(config_item('withdraw_min'));
		//$transfer_fund          = 0;
        $transfer_fund          = round( absint(config_item('withdraw_fee'))/100 * $amount );
        $tax_amount 			= bgn_calc_tax( $amount, $current_member->npwp );
        
        $amount_receipt         = $amount;
        $amount_receipt        -= $transfer_fund;
        $amount_receipt        -= $tax_amount;
        
        $bonus_total            = $this->model_member->count_bonus($current_member->id);
        $with_total             = $this->model_member->count_withdrawal($current_member->id);
        $saldo                  = round($bonus_total - $with_total);
        
        if( $amount >= $withdraw_min ){
            if( $amount <= $saldo ){
                $data_withdraw  = array(
                    'id_member'         => $current_member->id,
                    'username'          => $current_member->username,
                    'nominal'           => $amount,
                    'nominal_receipt'   => $amount_receipt,
                    'transfer_fund'     => $transfer_fund,
                    'tax'				=> $tax_amount,
                    'datecreated'       => $curdate,
                    'datemodified'      => $curdate,            
                );
                $save_withdraw  = $this->model_member->save_data_withdraw($data_withdraw);
                
                if( $save_withdraw ){
                    $success_data       = '<button class="close" data-close="alert"></button>Withdrawal berhasil! Berikut data withdrawal Anda :';
                    $success_data      .= '<br />Jumlah yang diterima : <strong>Rp ' . bgn_accounting($amount_receipt, $currency) . '</strong>';
                    if( $transfer_fund > 0 ){
                        $success_data  .= '<br />Biaya Index : <strong>Rp ' . bgn_accounting($transfer_fund, $currency) . '</strong>';
                    }
                    $success_data      .= '<br />Pajak : <strong>Rp ' . bgn_accounting($tax_amount, $currency) . '</strong>';
                    
                    $bonus_total        = $this->model_member->count_bonus($current_member->id);
                    $with_total         = $this->model_member->count_withdrawal($current_member->id);
                    $saldo              = round($bonus_total - $with_total);
                    
                    $bank_data          = bgn_banks($current_member->bank); 
                    $bank               = $bank_data->nama;
                    $this->bgn_sms->sms_withdrawal($current_member->phone, $current_member->username, 'Rp '.number_format($amount, 0,',','.'), $bank, $current_member->bill, $current_member->name );
                    
                    // Set JSON data
                    $data = array(
                        'message'   => 'success',
                        'data'      => $success_data,
                        'saldo'     => '<strong>Rp ' . number_format($saldo, 2, '.', ',') . '</strong>',
                    );
                    // JSON encode data
                    die(json_encode($data));
                }else{
                    // Set JSON data
                    $data = array(
                        'message'   => 'error',
                        'data'      => '<button class="close" data-close="alert"></button>Save data withdrawal tidak berhasil.',
                    );
                    // JSON encode data
                    die(json_encode($data));
                }
            }else{
                // Set JSON data
                $data = array(
                    'message'   => 'error',
                    'data'      => '<button class="close" data-close="alert"></button>Jumlah withdrawal melebihi saldo deposite yang Anda miliki.',
                );
                // JSON encode data
                die(json_encode($data));
            }
        }else{
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Minimal withdrawal sebesar '.bgn_accounting($withdraw_min,config_item('currency')).'!',
            );
            // JSON encode data
            die(json_encode($data));
        }
    }
    
    /**
	 * Withdrawal List function.
	 */
    function withdrawallist(){
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        
        $condition          = '';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $sAction            = bgn_isset($_REQUEST['sAction'],'');
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;

        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_bank             = bgn_isset($this->input->post('search_bank'), '');
        $s_bill             = bgn_isset($this->input->post('search_bill'), '');
        $s_bill_name        = bgn_isset($this->input->post('search_bill_name'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_username) )   { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )       { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_bank) )       { $condition .= str_replace('%s%', $s_bank, ' AND %bank% LIKE "%%s%%"'); }
        if( !empty($s_bill) )       { $condition .= str_replace('%s%', $s_bill, ' AND %bill% LIKE "%%s%%"'); }
        if( !empty($s_bill_name) )  { $condition .= str_replace('%s%', $s_bill_name, ' AND %bill_name% LIKE "%%s%%"'); }
        if( !empty($s_status) )     { $condition .= str_replace('%s%', ($s_status == 'pending' ? 0 : 1), ' AND %status% = %s%'); }
        
        if ( !empty($s_nominal_min) && empty($s_nominal_max) )      { $condition .= ' AND %total% >= '.$s_nominal_min.''; }
        elseif ( empty($s_nominal_min) && !empty($s_nominal_max) )  { $condition .= ' AND %total% <= '.$s_nominal_max.''; }
        elseif ( !empty($s_nominal_min) && !empty($s_nominal_max) ) { $condition .= ' AND %total% BETWEEN '.$s_nominal_min.' AND '.$s_nominal_max.''; }
        
        if ( !empty($s_date_min) )	{ $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if ( !empty($s_date_max) )	{ $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        
        if( !empty($condition) ){
            $condition      = substr($condition, 4);
            $condition      = ' WHERE' . $condition;
        }

        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%bank% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%bill% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%bill_name% ' . $sort; }
        elseif( $column == 6 )  { $order_by .= '%total% ' . $sort; }
        elseif( $column == 7 )  { $order_by .= '%status% ' . $sort; }
        elseif( $column == 8 )  { $order_by .= '%datecreated% ' . $sort; }
        
        $withdraw_list      = $this->model_member->get_all_member_withdraw($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($withdraw_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            $currency       = config_item('currency');
            foreach($withdraw_list as $row){

                $status         = ( $row->status == 0 ? 'PENDING' : 'TRANSFERED' );
                $detail         = 'Jumlah Withdrawal : <strong>'.bgn_accounting($row->nominal,$currency).'</strong><br />';
                if( $row->transfer_fund > 0 ){
                    $detail    .= 'Biaya Index : <strong>'.bgn_accounting($row->transfer_fund,$currency).'</strong><br />';
                }
                $detail        .= 'Biaya Pajak : <strong>'.bgn_accounting($row->tax,$currency).'</strong>';
                $transferbtn    = ( $row->status == 0 ? '<a href="'.base_url('backend/withdrawaltransfer/'.$row->id).'" class="btn btn-xs btn-warning withdrawaltransfer">Transfer</a>' : '<a href="#" class="btn btn-xs btn-default" disabled="">Transfer</a>' );
                $bank           = bgn_banks($row->bank);
                
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    '<center><a href="'.base_url('profile/'.$row->id_member).'">' . $row->username . '</a></center>',
                    strtoupper($row->name),
                    '<center>'.strtoupper($bank->nama).'</center>',
                    '<center>'.$row->bill.'</center>',
                    strtoupper($row->bill_name),
                    bgn_accounting($row->nominal_receipt, config_item('currency'),TRUE),
                    '<center><span class="label label-sm label-'.( $status == 'PENDING' ? 'default' : 'success' ).'">'.$status.'</span></center>',
                    '<center>'.$row->datecreated.'</center>',
                    $detail,
                    '<center>'.$transferbtn.'</center>',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        if( $sAction == 'export_excel' ){
            $order_by .= '%datecreated% ' . 'DESC';
            $data_list                      = $this->model_member->get_all_member_withdraw(0, 0, $condition, $order_by);
            $export                         = $this->bgn_excel->withdraw_export_simple($data_list);

            $records["sStatus"]             = "EXPORTED"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]            = $export; // pass custom message(useful for getting status of group actions)
        }

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Withdrawal List Mine function.
	 */
    function withdrawallistmine(){
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        
        $condition          = ' WHERE id_member = ' . $current_member->id;
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_status) )         { $condition .= str_replace('%s%', ($s_status == 'pending' ? 0 : 1), ' AND %status% = %s%'); }
        
        if ( !empty($s_nominal_min) )   { $condition .= ' AND %total% >= '.$s_nominal_min.''; }
        if ( !empty($s_nominal_max) )   { $condition .= ' AND %total% <= '.$s_nominal_max.''; }
        
        if ( !empty($s_date_min) )      { $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if ( !empty($s_date_max) )      { $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; }
        
        if( $column == 1 )      { $order_by .= '%total% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%status% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%datecreated% ' . $sort; }
        
        $withdraw_list      = $this->model_member->get_all_member_withdraw($limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if( !empty($withdraw_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            $currency       = config_item('currency');
            foreach($withdraw_list as $row){
                $status         = ( $row->status == 0 ? 'PENDING' : 'TRANSFERED' );
                $detail         = 'Jumlah Withdrawal : <strong>'.bgn_accounting($row->nominal,$currency).'</strong><br />';
                if( $row->transfer_fund > 0 ){
                    $detail    .= 'Biaya Index : <strong>'.bgn_accounting($row->transfer_fund,$currency).'</strong><br />';
                }
                $detail        .= 'Biaya Pajak : <strong>'.bgn_accounting($row->tax,$currency).'</strong>';
                
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    bgn_accounting($row->nominal_receipt,config_item('currency'),TRUE),
                    '<center><span class="label label-sm label-'.( $status == 'PENDING' ? 'default' : 'success' ).'">'.$status.'</span></center>',
                    '<center>'.$row->datecreated.'</center>',
                    $detail,
                    '',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Withdrawal Transfer function.
	 */
    function withdrawaltransfer( $id=0 )
    {
        auth_redirect();
        
        if( !$id ) { echo 'failed'; die(); }

        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        if( !$is_admin ) { echo 'failed'; die(); }
        
        $withdraw_data          = $this->model_member->get_all_member_withdraw(0,0,'WHERE %status% = 0 AND %id% = '.$id.'');
        if( !$withdraw_data || empty($withdraw_data) ) { echo 'failed'; die(); }
          
        $withdraw               = '';
        foreach($withdraw_data as $row){
            $withdraw           = $row;
        }
        $curdate                = date('Y-m-d H:i:s');
        $datawithdraw           = array('status' => 1, 'datemodified' => $curdate);
        
        $id                     = $withdraw->id;
        $phone                  = $withdraw->phone;
        $username               = $withdraw->username;
        $nominal_transfer       = $withdraw->nominal_receipt;

        if( $this->model_member->update_data_withdraw($id, $datawithdraw) ){
            $this->bgn_sms->sms_withdrawal_transfer($phone, $username, bgn_accounting($nominal_transfer, config_item('currency')));
            echo 'success'; die();
        }else{
            echo 'failed'; die();
        }
    }
    
    /**
	 * Withdrawal Transfer function.
	 */
    function withdrawaltransferall( )
    {
        auth_redirect();

        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        if( !$is_admin ) { echo 'failed'; die(); }
        
        $curdate                = date('Y-m-d H:i:s');
        $datawithdraw           = array('status' => 1, 'datemodified' => $curdate);

        $withdraw_list          = $this->model_member->get_all_member_withdraw(0,0,'WHERE %status% = 0');
        foreach($withdraw_list AS $row){
            $id                 = $row->id;
            $phone              = $row->phone;
            $username           = $row->username;
            $nominal_transfer   = $row->nominal_receipt;

            if( $this->model_member->update_data_withdraw($id, $datawithdraw) ){
                $this->bgn_sms->sms_withdrawal_transfer($phone, $username, bgn_accounting($nominal_transfer, config_item('currency')));
            }
        }
        echo 'success'; die();
    }
    
    /**
	 * Setting General function.
	 */
	function general()
	{       
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        
        $data['title']          = TITLE . 'Pengaturan Umum';
        $data['member']         = $current_member;
        $data['main_content']   = 'generalsetting';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Tree Auto Global Reward function.
	 */
    function autoglobalreward( $id=0 )
    {
        auth_redirect();
        
        $is_down                        = FALSE;
        $autoglobal_data                = '';
        $current_member                 = bgn_get_current_member();
        $current_autoglobal             = $this->model_member->get_auto_global_by('member',$current_member->id);
        $current_autoglobal->username   = $current_member->username;
        $current_autoglobal->name       = $current_member->name;
        $is_admin                       = as_administrator($current_member);
        
        if ( $id > 0 ){
            $autoglobal_data        = $this->model_member->get_autoglobaldata($id);
            
            if( !$is_admin ){
                $is_down            = $this->model_member->get_is_downline_auto_global($id, $current_autoglobal->tree);
                if ( !$is_down ) redirect( base_url('backend/autoglobalreward'), 'location' );
            }else{
                $is_down            = TRUE;
            }
        }
            
        $id_auto                    = ( $id > 0 ? $id : $current_autoglobal->id );
            
        $data['title']              = TITLE . 'Pohon Jaringan Auto Global Reward';
        $data['member']             = $current_member;
        $data['autoglobal']         = $current_autoglobal;
        $data['autoglobal_other']   = $autoglobal_data;
        $data['is_down']            = $is_down;
        $data['is_admin']           = $is_admin;
        $data['main_content']       = 'autoglobalreward';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Tree Search Auto Global function.
	 */
    function treesearchauto()
    {
        $current_member         = bgn_get_current_member();
        $current_autoglobal     = $this->model_member->get_auto_global_by('member',$current_member->id);
        if( !$current_autoglobal || empty($current_autoglobal) ){
            // Set JSON data
            $data = array(
                'message'   => 'failed',
                'data'      => '<button class="close" data-close="alert"></button>Anda belum terdaftar di Auto Global Reward.',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $username               = bgn_isset($this->input->post('username'), '');
        $member                 = $this->model_member->get_member_by('login', strtolower($username));
        
        if( !empty($member) ){
            // Get Auto Global Data
            $autoglobaldata     = $this->model_member->get_auto_global_by('member',$member->id);
            if( !$autoglobaldata || empty($autoglobaldata) ){
                // Set JSON data
                $data = array(
                    'message'   => 'failed',
                    'data'      => '<button class="close" data-close="alert"></button>Username Anggota belum terdaftar di Auto Global Reward.',
                );
                // JSON encode data
                die(json_encode($data));
            }
            
            $is_downline        = $this->model_member->get_is_downline_auto_global($autoglobaldata->id, $current_autoglobal->tree);
            if( $is_downline ){
                // Set JSON data
                $data = array(
                    'message'   => 'success',
                    'data'      => base_url('backend/autoglobalreward/' . $autoglobaldata->id),
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
	 * Tree Search Auto-CBI function.
	 */
    function treesearchautocbi()
    {
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        $current_autocbi        = $this->model_member->get_autocbi_by('member',$current_member->id);
        if( ( !$current_autocbi || empty($current_autocbi) ) && !$is_admin ){
            // Set JSON data
            $data = array(
                'message'   => 'failed',
                'data'      => '<button class="close" data-close="alert"></button>Anda belum terdaftar di Auto-Reward.',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $username               = bgn_isset($this->input->post('username'), '');
        $member                 = $this->model_member->get_member_by('login', strtolower($username));
        
        if( !empty($member) ){
            // Get Auto-CBI Data
            $autocbidata        = $this->model_member->get_autocbi_by('member',$member->id);
            if( !$autocbidata || empty($autocbidata) ){
                // Set JSON data
                $data = array(
                    'message'   => 'failed',
                    'data'      => '<button class="close" data-close="alert"></button>Username Anggota belum terdaftar di Auto-CBI.',
                );
                // JSON encode data
                die(json_encode($data));
            }
            
            if( !$is_admin && $autocbidata->board > $current_autocbi->board ){
                // Set JSON data
                $data = array(
                    'message'   => 'failed',
                    'data'      => '<button class="close" data-close="alert"></button>Anda belum qualified untuk melihat CBT Tree username <strong>'.$autocbidata->username.'</strong>.',
                );
                // JSON encode data
                die(json_encode($data));
            }

            // Set JSON data
            $data = array(
                'message'   => 'success',
                'data'      => base_url('report/autocbitree/' . $autocbidata->id),
            );
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
	 * Tax function.
	 */
	public function tax( $mode = '', $id_tax = 0 )
	{
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        $is_consultant          = as_consultant($current_member);
		
		if ( $mode == 'export' ) {
			$conditions = ' AND %id_tax% = ' . $id_tax;
			
			if ( ! $id_tax ) {
				if ( ! $id_taxes = $this->input->get( 't' ) )
					redirect( 'report/tax' );
				$conditions = ' AND %id_tax% IN (' . implode( ',', $id_taxes ) . ')';
			}

			// get data tax
			if ( ! $taxes = $this->model_member->get_taxes_all( 0, 0, $conditions ) )
				redirect( 'report/tax' );
			
			// filters
			if ( ! $is_admin || ! $is_consultant ) {
				foreach( $taxes as $key => $val )
					if ( $val->id != $current_member->id )
						unset( $taxes[ $key ] );
			}

			$type = $this->input->get( 'type' );

			// export
			if ( $type == 'xls' ) {
				if ( $file = $this->bgn_excel->tax_export( $taxes ) )
					redirect( $file );
			} else {
				$this->load->library( 'bgn_pdf' );
				if ( ! $file = $this->bgn_pdf->tax_export( $taxes ) )
					redirect( 'report/tax' );
			}
		}
        
        $data['title']          = TITLE . 'Pajak';
        $data['member']         = $current_member;
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        $data['is_consultant']  = $is_consultant;
        $data['main_content']   = 'tax';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * Tax List Member function.
	 */
    function taxlist( $id_member = 0 )
    {
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $is_consultant      = as_consultant($current_member);
        
        $condition          = '';
        $order_by           = '';
        $iTotalRecords      = 0;

        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        $sEcho              = intval($_REQUEST['sEcho']);
		$sAction            = bgn_isset($_REQUEST['sAction'],'');
        
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_username        	= bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        $s_tax_min          = bgn_isset($this->input->post('search_tax_min'), '');
        $s_tax_max          = bgn_isset($this->input->post('search_tax_max'), '');
        $s_receipt_min      = bgn_isset($this->input->post('search_receipt_min'), '');
        $s_receipt_max      = bgn_isset($this->input->post('search_receipt_max'), '');
        $s_date_min         = bgn_isset($this->input->post('search_periode_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_periode_max'), '');
		
        if( !empty($s_username) )       { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )           { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        
        if ( !empty($s_nominal_min) )	{ $condition .= ' AND %nominal% >= '.$s_nominal_min.''; }
        if ( !empty($s_nominal_max) )	{ $condition .= ' AND %nominal% <= '.$s_nominal_max.''; }
        
        if ( !empty($s_tax_min) )       { $condition .= ' AND %tax% >= '.$s_tax_min.''; }
        if ( !empty($s_tax_max) )       { $condition .= ' AND %tax% <= '.$s_tax_max.''; }
        
        if ( !empty($s_receipt_min) )	{ $condition .= ' AND %receipt% >= '.$s_receipt_min.''; }
        if ( !empty($s_receipt_max) )	{ $condition .= ' AND %receipt% <= '.$s_receipt_max.''; }
        
        if ( !empty($s_date_min) )		{ $condition .= ' AND %period% >= "'.$s_date_min.'"'; }
        if ( !empty($s_date_max) )		{ $condition .= ' AND %period% <= "'.$s_date_max.'"'; }
        
        if( $column == 1 )      { $order_by .= '%periode% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%username% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%nominal% ' . $sort; }
		elseif( $column == 5 )  { $order_by .= '%tax% ' . $sort; }
        elseif( $column == 6 )  { $order_by .= '%receipt% ' . $sort; }
		
		$tax_list = $this->model_member->get_taxes_all( $limit, $offset, $condition, $order_by, $id_member );
        
        $records            = array();
        $records["aaData"]  = array(); 
        
        if ( ! empty( $tax_list ) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $currency		= config_item('currency');
            $i = $offset + 1;
			
            foreach ( $tax_list as $row ) {          
                if ( $row->type != 2 ) {
					$download = anchor( 'report/tax/export/' . $row->id_tax, '<i class="fa fa-file-pdf-o"></i> PDF', 
						array( 'class' => 'btn btn-xs btn-danger' ) );
					$download .= ' ' . anchor( 'report/tax/export/' . $row->id_tax . '/?type=xls', '<i class="fa fa-file-excel-o"></i> XLS', 
						array( 'class' => 'btn btn-xs btn-success' ) );
					
					if ( $id_member ) {
						$records["aaData"][]    = array(
	                        '<center>' . $i .'</center>',
	                        '<center><input name="download[]" class="cbdownload" value="' . $row->id_tax . '" type="checkbox" /></center>',
	                        '<center>' . $row->period_name . '</center>',
	                        bgn_accounting( $row->total_nominal, $currency, TRUE),
	                        bgn_accounting( $row->total_tax, $currency, TRUE),
	                        bgn_accounting( $row->total_received, $currency, TRUE),
	                        '<center>' . $download . '</center>',
	                    );
					} else {
	                    $username	= $is_consultant ? $row->username : '<a href="'.base_url( 'profile/' . $row->id ) . '">' . $row->username . '</a>';
	                    $profile	= $is_consultant ? strtoupper( $row->name ) : '<a href="'.base_url( 'profile/' . $row->id ) . '">' . strtoupper( $row->name ) . '</a>';
	                    $records["aaData"][] = array(
	                        '<center>' . $i .'</center>',
	                        '<center><input name="download[]" class="cbdownload" value="' . $row->id_tax . '" type="checkbox" /></center>',
	                        '<center>' . $row->period_name . '</center>',
	                        '<center>' . $username . '</center>',
	                        $profile,
	                        bgn_accounting( $row->total_nominal, $currency, TRUE),
	                        bgn_accounting( $row->total_tax, $currency, TRUE),
	                        bgn_accounting( $row->total_received, $currency, TRUE),
	                        '<center>' . $download . '</center>',
	                    );
                    }
					
                    $i++;
                }
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
		
		if( $sAction == 'export_pdf' ){
			$this->load->library( 'bgn_pdf' );
            $taxes                      	= $this->model_member->get_taxes_all( 0, 0, $condition, $order_by, $id_member );
            $export                         = $this->bgn_pdf->tax_export( $taxes, 'F' );

            $records["sStatus"]             = "EXPORTED"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]            = $export; // pass custom message(useful for getting status of group actions)
        }
		
		if( $sAction == 'export_excel' ){
            $taxes                      	= $this->model_member->get_taxes_all( 0, 0, $condition, $order_by, $id_member );
            $export                         = $this->bgn_excel->tax_export( $taxes );
            $records["sStatus"]             = "EXPORTED"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]            = $export; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }

	/**
	 * Tax list mine
	 */
	function taxlistmine() {
		// auth redirect
        auth_redirect();
		
		// get current member
		$current_member = bgn_get_current_member();
        
		return $this->taxlist( $current_member->id );
	}
    
    /**
	 * List Auto-CBI function.
	 */
    function autocbi( $id=0 )
    {
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        
        if ( $id > 0 ){
            $member_data        = bgn_get_memberdata_by_id($id); 
            if ( !$member_data ) redirect( base_url('report/auto_ro'), 'refresh' );
        }
            
        $id_member              = ( $id > 0 ? $member_data->id : $current_member->id );
        $autocbi_total          = $this->model_member->get_all_my_autocbi_total($id_member);
        
        $data['title']          = TITLE . 'Auto-CBI List';
        $data['member']         = $current_member;
        $data['member_other']   = $member_data;
        $data['is_admin']       = $is_admin;
        $data['autocbi_total']  = ( !empty($autocbi_total) ? $autocbi_total : 0 );
        $data['main_content']   = 'autocbi';

        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * List Auto-CBI Member function.
	 */
    function autocbilists()
    {
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $condition          = 'WHERE %status% = 1';
        $order_by           = '';
        $iTotalRecords      = 0;

        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        
        $sEcho              = intval($_REQUEST['sEcho']);
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;

        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        
        if( !empty($s_username) )       { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )           { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_status) )         { $condition .= str_replace('%s%', ( $s_status == 'notqualified' ? 0 : 1 ), ' AND %autocbi_qualified% = %s%'); }
        if( !empty($s_date_min) )       { $condition .= ' AND %autocbi_qualified_date% >= "'.$s_date_min.' 00:00:00"'; }
        if( !empty($s_date_max) )       { $condition .= ' AND %autocbi_qualified_date% <= "'.$s_date_max.' 23:59:59"'; }
        
        if( !empty($s_nominal_min) )	{ $condition .= ' AND %total% >= '.$s_nominal_min.''; }
        if( !empty($s_nominal_max) )	{ $condition .= ' AND %total% <= '.$s_nominal_max.''; }

        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%autocbi_qualified% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%autocbi_qualified_date% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%total% ' . $sort; }

        $autocbi_list       = $this->model_member->get_all_member_autocbi($limit, $offset, $condition, $order_by);

        $records            = array();
        $records["aaData"]  = array();
        
        if( !empty($autocbi_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            
            $i = $offset + 1;
            foreach($autocbi_list as $row){          
                $status = ( $row->autocbi_qualified == 1 ? '<span class="label label-sm label-success">QUALIFIED</span>' : '<span class="label label-sm label-default">NOT QUALIFIED</span>' );
                $detail = '<a href="'.base_url('backend/autocbi/'.$row->id_member).'" class="btn btn-xs btn-info" title="Detail">Detail</a>';
                $datequalified  = ( $row->autocbi_qualified_date != "0000-00-00 00:00:00" ? '<strong>'.$row->autocbi_qualified_date.'</strong>' : 'N/A' );
                
                $autocbi_data   = '';
                $btn_cbitree    = ' <a href="javascript:;" class="btn btn-xs btn-default" disabled="">CBI Tree</a>';
                if( $row->autocbi_qualified == 1 ){
                    $autocbi_data = $this->model_member->get_autocbi_by('member',$row->id_member);
                    if( !empty($autocbi_data) ){
                        $btn_cbitree = ' <a target="_blank" href="'.base_url('report/autocbitree/'.$autocbi_data->id).'" class="btn btn-xs btn-success">CBI Tree</a>';
                    }
                }

                $records["aaData"][] = array(
                    bgn_center($i),
                    bgn_center('<a href="'.base_url('profile/'.$row->id_member).'">' . $row->username . '</a>'),
                    '<a href="'.base_url('profile/'.$row->id_member).'">' . strtoupper($row->name) . '</a>',
                    bgn_center($status),
                    bgn_center($datequalified),
                    bgn_accounting($row->total, 'Rp', TRUE),
                    bgn_center($detail . $btn_cbitree),
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Auto-CBI List My Bonus function.
	 */
    function autocbilistsmine( $id=0 )
    {
        $member_data        = '';
        
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        
        if ( $id > 0 )
            $member_data    = bgn_get_memberdata_by_id($id);
            
        $id_member          = ( $id > 0 ? $member_data->id : $current_member->id );
        
        $condition          = '';
        $order_by           = '';
        $iTotalRecords      = 0;
        
        $iDisplayLength     = intval($_REQUEST['iDisplayLength']);
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        
        $sEcho              = intval($_REQUEST['sEcho']);
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;
        
        $s_id_bonus         = bgn_isset($this->input->post('search_id_bonus'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        $s_nominal_min      = bgn_isset($this->input->post('search_nominal_min'), '');
        $s_nominal_max      = bgn_isset($this->input->post('search_nominal_max'), '');
        $s_desc             = bgn_isset($this->input->post('search_desc'), '');
        $s_status           = bgn_isset($this->input->post('search_status'), '');
        
        if( !empty($s_id_bonus) )   { $condition .= str_replace('%s%', $s_id_bonus, ' AND %id_bonus% LIKE "%%s%%"'); }
        if( !empty($s_desc) )       { $condition .= str_replace('%s%', $s_desc, ' AND %desc% LIKE "%%s%%"'); }
        if( !empty($s_status) )     { $condition .= str_replace('%s%', $s_status, ' AND %status% = %s%'); }
        
        if ( !empty($s_date_min) )	{ $condition .= ' AND %datecreated% >= '.strtotime($s_date_min).''; }
        if ( !empty($s_date_max) )	{ $condition .= ' AND %datecreated% <= '.strtotime($s_date_max).''; } 
        
        if ( !empty($s_nominal_min) )	{ $condition .= ' AND %nominal% >= '.$s_nominal_min.''; }
        if ( !empty($s_nominal_max) )	{ $condition .= ' AND %nominal% <= '.$s_nominal_max.''; }
        
        if( $column == 1 )      { $order_by .= '%id_bonus% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%datecreated% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%nominal% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%type% ' . $sort; }
		
        $autocbi_list       = $this->model_member->get_all_my_autocbi($id_member, $limit, $offset, $condition, $order_by);
        
        $records            = array();
        $records["aaData"]  = array(); 

        if( !empty($autocbi_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            $i = $offset + 1;
            foreach($autocbi_list as $row){     
				$bonus_amount   = bgn_accounting( ($row->amount == "" ? 0 : $row->amount), config_item('currency'), true );
                
                $records["aaData"][]    = array(
                    '<center>'.$i.'</center>',
                    bgn_center($row->id_bonus),
                    bgn_center($row->datecreated),
                    $bonus_amount,
                    $row->desc,
                    '',
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;

        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        
        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Auto-CBI Tree function.
	 */
    function autocbitree( $id=0 )
    {
        auth_redirect();
        
        $autocbi_data               = '';
        $current_member             = bgn_get_current_member();
        $current_autocbi            = '';
        $is_admin                   = as_administrator($current_member);
        
        $autocbiall                 = $this->model_member->get_all_autocbi();
        if( $autocbiall || !empty($autocbiall) ){
            $current_autocbi            = $this->model_member->get_autocbi_by('member',$current_member->id);
            if( $is_admin ){
                $current_autocbi        = $this->model_member->get_autocbidata(1);
            }
        }
        
        if ( $id > 0 ){
            $autocbi_data           = $this->model_member->get_autocbidata($id);
        }
        
        if( !empty($current_autocbi) && !empty($autocbi_data) ){
            if( !$is_admin && $autocbi_data->board > $current_autocbi->board ){
                redirect(base_url('report/autocbitree'));
            }
        }

        $id_autocbi                 = ( $id > 0 ? $id : $current_autocbi->id );
            
        $data['title']              = TITLE . 'Pohon Jaringan Auto-CBI';
        $data['member']             = $current_member;
        $data['autocbi']            = $current_autocbi;
        $data['autocbi_other']      = $autocbi_data;
        $data['is_admin']           = $is_admin;
        $data['main_content']       = 'autocbitree';

        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * List Auto-CBI History function.
	 */
    function autocbihistory( $id=0 )
    {
        // auth redirect
        auth_redirect();
        
        $member_data            = '';
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        if( !$is_admin) redirect('dashboard');
        
        $data['title']          = TITLE . 'Auto-CBI History List';
        $data['member']         = $current_member;
        $data['is_admin']       = $is_admin;
        $data['main_content']   = 'autocbihistory';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * List Auto-CBI History Member function.
	 */
    function autocbihistorylists()
    {
        $current_member     = bgn_get_current_member();
        $is_admin           = as_administrator($current_member);
        $condition          = '';
        $order_by           = '';
        $iTotalRecords      = 0;

        $iDisplayLength     = intval($_REQUEST['iDisplayLength']); 
        $iDisplayStart      = intval($_REQUEST['iDisplayStart']);
        
        $sEcho              = intval($_REQUEST['sEcho']);
        $sort               = $_REQUEST['sSortDir_0'];
        $column             = intval($_REQUEST['iSortCol_0']);
        
        $limit              = ( $iDisplayLength == '-1' ? 0 : $iDisplayLength );
        $offset             = $iDisplayStart;

        $s_username         = bgn_isset($this->input->post('search_username'), '');
        $s_name             = bgn_isset($this->input->post('search_name'), '');
        $s_proses           = bgn_isset($this->input->post('search_proses'), '');
        $s_before           = bgn_isset($this->input->post('search_before'), '');
        $s_after            = bgn_isset($this->input->post('search_after'), '');
        $s_date_min         = bgn_isset($this->input->post('search_datecreated_min'), '');
        $s_date_max         = bgn_isset($this->input->post('search_datecreated_max'), '');
        
        if( !empty($s_username) )       { $condition .= str_replace('%s%', $s_username, ' AND %username% LIKE "%%s%%"'); }
        if( !empty($s_name) )           { $condition .= str_replace('%s%', $s_name, ' AND %name% LIKE "%%s%%"'); }
        if( !empty($s_proses) )         { $condition .= str_replace('%s%', $s_proses, ' AND %process% = "%s%"'); }
        if( !empty($s_before) )         { $condition .= str_replace('%s%', $s_before, ' AND %board_before% = %s%'); }
        if( !empty($s_after) )          { $condition .= str_replace('%s%', $s_after, ' AND %board_after% = %s%'); }
        if( !empty($s_date_min) )       { $condition .= ' AND %datecreated% >= "'.$s_date_min.' 00:00:00"'; }
        if( !empty($s_date_max) )       { $condition .= ' AND %datecreated% <= "'.$s_date_max.' 23:59:59"'; }

        if( $column == 1 )      { $order_by .= '%username% ' . $sort; }
        elseif( $column == 2 )  { $order_by .= '%name% ' . $sort; }
        elseif( $column == 3 )  { $order_by .= '%process% ' . $sort; }
        elseif( $column == 4 )  { $order_by .= '%board_before% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%board_after% ' . $sort; }
        elseif( $column == 5 )  { $order_by .= '%datecreated% ' . $sort; }
        
        if( !empty($condition) ){
            $condition      = substr($condition, 4);
            $condition      = 'WHERE' . $condition;
        }

        $autocbihis_list    = $this->model_member->get_all_autocbi_board($limit, $offset, $condition, $order_by);

        $records            = array();
        $records["aaData"]  = array();
        
        if( !empty($autocbihis_list) ){
            $iTotalRecords  = bgn_get_last_found_rows();
            
            $i = $offset + 1;
            foreach($autocbihis_list as $row){          
                $process    = ( $row->process == CBI_PROCESS_UP ? '<span class="text-success"><strong>UP</strong></span>' : '<span class="text-danger"><strong>ROLL</strong></span>' );
                $detail     = '<a href="'.base_url('report/autocbitree/'.$row->id_autocbi).'" class="btn btn-xs btn-info" title="CBI Tree">CBI Tree</a>';
                
                if( $row->board_before == BOARD_CBI_ONE )       { $board_before = '<span class="label label-sm cbi-1">CBI-'.$row->board_before.'</span>'; }
                elseif( $row->board_before == BOARD_CBI_TWO )   { $board_before = '<span class="label label-sm cbi-2">CBI-'.$row->board_before.'</span>'; }
                elseif( $row->board_before == BOARD_CBI_THREE ) { $board_before = '<span class="label label-sm cbi-3">CBI-'.$row->board_before.'</span>'; }
                
                if( $row->board_after == BOARD_CBI_ONE )        { $board_after = '<span class="label label-sm cbi-1">CBI-'.$row->board_after.'</span>'; }
                elseif( $row->board_after == BOARD_CBI_TWO )    { $board_after = '<span class="label label-sm cbi-2">CBI-'.$row->board_after.'</span>'; }
                elseif( $row->board_after == BOARD_CBI_THREE )  { $board_after = '<span class="label label-sm cbi-3">CBI-'.$row->board_after.'</span>'; }

                $records["aaData"][] = array(
                    bgn_center($i),
                    bgn_center('<a href="'.base_url('profile/'.$row->id_member).'">' . $row->username . '</a>'),
                    '<a href="'.base_url('profile/'.$row->id_member).'">' . strtoupper($row->name) . '</a>',
                    bgn_center($process),
                    bgn_center($board_before),
                    bgn_center($board_after),
                    bgn_center($row->datecreated),
                    bgn_center($detail),
                );
                $i++;
            }   
        }
        
        $end                = $iDisplayStart + $iDisplayLength;
        $end                = $end > $iTotalRecords ? $iTotalRecords : $end;
        
        if (isset($_REQUEST["sAction"]) && $_REQUEST["sAction"] == "group_action") {
            $records["sStatus"]     = "OK"; // pass custom message(useful for getting status of group actions)
            $records["sMessage"]    = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }

        $records["sEcho"]                   = $sEcho;
        $records["iTotalRecords"]           = $iTotalRecords;
        $records["iTotalDisplayRecords"]    = $iTotalRecords;
        
        echo json_encode($records);
    }
    
    /**
	 * Download
	 */
	function download() {
		// auth redirect
        auth_redirect();
		
		// get current member
		$current_member = bgn_get_current_member();
		
		if ( ! $file_path = $this->input->get( 'f' ) ) die( 'Invalid file!' );
		
		$file_name = end( explode( '/', $file_path ) );
		bgn_download( $file_path, $file_name, 'application/x-download' );
	}
    
    /**
	 * Update Setting General function.
	 */
    function updatesetting()
    {
        $field      = bgn_isset($this->input->post('field'), '');
        $value      = bgn_isset($this->input->post('value'), '');
        
        if( $field == 'email_down_nonactive' ){
            update_option('send_email_down_nonactive', $value);
        }elseif( $field == 'email_down_active' ){
            update_option('send_email_down_active', $value);
        }elseif( $field == 'email_down_nonactive_html' ){
            update_option('send_email_down_nonactive_html', $value);
        }elseif( $field == 'email_down_active_html' ){
            update_option('send_email_down_active_html', $value);
        }elseif( $field == 'email_sponsor' ){
            update_option('send_email_sponsor', $value);
        }elseif( $field == 'email_sponsor_html' ){
            update_option('send_email_sponsor_html', $value);
        }elseif( $field == 'email_sponsor_replika' ){
            update_option('send_email_sponsor_replika', $value);
        }elseif( $field == 'email_sponsor_replika_html' ){
            update_option('send_email_sponsor_replika_html', $value);
        }elseif( $field == 'email_admin' ){
            update_option('send_email_admin', $value);
        }elseif( $field == 'email_admin_html' ){
            update_option('send_email_admin_html', $value);
        }elseif( $field == 'be_dashboard_member' ){
            update_option('be_dashboard_member', $value);
        }elseif( $field == 'be_dashboard_stockist' ){
            update_option('be_dashboard_stockist', $value);
        }elseif( $field == 'sms_format_new_member' ){
            update_option('sms_format_new_member', $value);
        }elseif( $field == 'sms_format_new_member_sponsor' ){
            update_option('sms_format_new_member_sponsor', $value);
        }elseif( $field == 'sms_format_new_member_rep' ){
            update_option('sms_format_new_member_rep', $value);
        }elseif( $field == 'sms_format_new_member_rep_sponsor' ){
            update_option('sms_format_new_member_rep_sponsor', $value);
        }elseif( $field == 'sms_format_bonus' ){
            update_option('sms_format_bonus', $value);
        }elseif( $field == 'sms_format_withdrawal' ){
            update_option('sms_format_withdrawal', $value);
        }elseif( $field == 'sms_format_reward' ){
            update_option('sms_format_reward', $value);
        }elseif( $field == 'sms_format_reward_autoglobal' ){
            update_option('sms_format_reward_autoglobal', $value);
        }elseif( $field == 'sms_format_bonus_poin_atm' ){
            update_option('sms_format_bonus_poin_atm', $value);
        }elseif( $field == 'sms_format_bonus_poin_ro' ){
            update_option('sms_format_bonus_poin_ro', $value);
        }elseif( $field == 'sms_format_qualified_autocbi' ){
            update_option('sms_format_qualified_autocbi', $value);
        }
    }
    
    /**
	 * Update Setting Frontend function.
	 */
    function updatesettingfe()
    {
        if( bgn_isset($this->input->post('field'), '') == 'fe_stockist' ){
            $top        = bgn_isset($this->input->post('top'), '');
            $bottom     = bgn_isset($this->input->post('bottom'), '');
            update_option('fe_stockist_top', $top);
            update_option('fe_stockist_bottom', $bottom);
        }
    }
    
    /**
	 * Setting Front function.
	 */
	function frontend()
	{
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        
        $data['title']          = TITLE . 'Pengaturan Halaman Depan';
        $data['member']         = $current_member;
        $data['main_content']   = 'frontendsetting';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * SMS Blast function.
	 */
	function smsblast()
	{
        auth_redirect();
        
        $current_member         = bgn_get_current_member();
        $is_admin               = as_administrator($current_member);
        if( !$is_admin ) { redirect( base_url('backend'), 'refresh' ); }
        
        $data['title']          = TITLE . 'SMS Blast';
        $data['member']         = $current_member;
        $data['main_content']   = 'smsblast';
        
        $this->load->view(VIEW_BACK . 'template', $data);
    }
    
    /**
	 * SMS Blast Action function.
	 */
	function smsblastact()
	{
        $message        = trim( bgn_isset($this->input->post('message'), '') );
        $sql            = 'SELECT phone, COUNT(phone) AS total FROM bgn_member WHERE type != 2 and status = 1 GROUP BY phone ORDER BY id ASC';
        $members        = $this->db->query($sql)->result();

        if( !$members || empty($members) ){
            // Set JSON data
            $data = array(
                'message'   => 'error',
                'data'      => '<button class="close" data-close="alert"></button>Data member belum ada.',
            );
            // JSON encode data
            die(json_encode($data));
        }
        
        $count          = 0;
        foreach($members as $member){
            $to = trim($member->phone);
            //$this->bgn_sms->send_sms($to, $message);
            $count++;
        }
        
        // Set JSON data
        $data = array(
            'message'   => 'success',
            'data'      => '<button class="close" data-close="alert"></button>SMS berhasil di blast ke '.$count.' phone number.',
        );
        // JSON encode data
        die(json_encode($data));
    }
	
	/**
	 * Test email functionality
	 */
	function testmail() {
		$to = 'muhammadiqbal1917@gmail.com';
		
		// using PHP mailer
		echo 'sending email using PHP mailer...' . br();
		@mail($to, 'Test Email PHP Mail', 'This is test email using PHP mailer.');
		
		// using Swiftmailer
		$message = new stdClass();
		$message->plain = 'This is test email using Swiftmailer.';
		$message->html = $message->plain;
		
		echo 'sending email using Swiftmailer...' . br();
		$response = $this->bgn_email->send($to, 'Test Email Swiftmailer', $message, get_option('mail_sender_admin'), get_option('company_name'));
		if( is_array($response)) {
			echo 'failed:' . br();
			var_dump($response);
		} else {
			echo 'success.';
		}
	}
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */