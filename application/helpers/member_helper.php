<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('erp_set_current_member') ){
    /**
     * set the current member by ID.
     *
     * Some Kopi Ampuh functionality is based on the current member and not based on
     * the signed in member. Therefore, it opens the ability to edit and perform
     * actions on members who aren't signed in.
     *
     * @param   int     $id     Member ID
     * @return  erp_Member Current member erp_Member object
     */
    function erp_set_current_member($id)
    {
    	$CI =& get_instance();
    
    	$current_member = $CI->erp_member->member($id);
    
        unset($current_member->password);
    
    	return $current_member;
    }
}

if ( !function_exists('erp_get_current_member') ){
    /**
     * Retrieve the current member object.
     *
     * @return erp_Member Current member erp_Member object
     */
    function erp_get_current_member() 
    {
        $CI =& get_instance();
        if(!empty($CI->current_member)) return $CI->current_member;
        
    	$current_member = get_currentmemberinfo();
       
    	if ( !$current_member ){
    		if ($id = erp_isset($_COOKIE['logged_in_'.md5('nonssl')], false, true)){
        		if (erp_isset($CI->session->userdata('member_logged_in')) != "")
                    return erp_set_current_member($id);
        	}
    		return false;
    	}
    	return $current_member;
    }
}

if ( !function_exists('get_currentmemberinfo') ){
    /**
     * Populate global variables with information about the currently logged in member.
     *
     * Will set the current member, if the current member is not set. The current member
     * will be set to the logged in person. If no member is logged in, then it will
     * set the current member to 0, which is invalid and won't have any permissions.
     *
     * @uses erp_validate_auth_cookie() Retrieves current logged in member.
     *
     */
    function get_currentmemberinfo() {
    	
    	if ( !$id_member = erp_validate_auth_cookie() ) { 
    		 if ( empty($_COOKIE[LOGGED_IN_COOKIE]) || !$id_member = erp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in') ) {
    		 	erp_set_current_member(0);
    		 	return false;
    		 }
    	}
        
    	return erp_set_current_member($id_member);
    }
}

if ( !function_exists('erp_validate_auth_cookie') ){
    /**
     * Validates authentication cookie.
     *
     * The checks include making sure that the authentication cookie is set and
     * pulling in the contents (if $cookie is not used).
     *
     * Makes sure the cookie is not expired. Verifies the hash in cookie is what is
     * should be and compares the two.
     *
     * @param string $cookie Optional. If used, will validate contents instead of cookie's
     * @param string $scheme Optional. The cookie scheme to use: auth, secure_auth, or logged_in
     * @return bool|int False if invalid cookie, Member ID if valid.
     */
    function erp_validate_auth_cookie( $cookie = '', $scheme = '' )
    {
    	if ( !$cookie_elements = erp_parse_auth_cookie($cookie, $scheme) )
    		return false;
            
    	extract($cookie_elements, EXTR_OVERWRITE);
    
    	$expired = $expiration;
        
    	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) 
    		$expired += 3600;
    
    	if ( $expired < time() )
    		return false;
    
    	$CI =& get_instance();
    
    	$member = $CI->Model_Member->get_member_by('login', $username);
        
    	if ( !$member || empty($member)) 
    		return false;
    
    	$pass_frag = substr($member->password, 8, 4);
    	$key       = erp_hash($username . $pass_frag . '|' . $expiration, $scheme);
    	$hash      = hash_hmac('md5', $username . '|' . $expiration, $key);
    	
    	if ( $hmac != $hash )
    		return false;
    
    	return $member->id;
    }
}

if ( !function_exists('erp_parse_auth_cookie') ){
    /**
     * Parse a cookie into its components
     *
     * @param string $cookie
     * @param string $scheme Optional. The cookie scheme to use: auth, secure_auth, or logged_in
     * @return array Authentication cookie components
     */
    function erp_parse_auth_cookie($cookie = '', $scheme = '')
    {
    	$CI =& get_instance();
    	if( empty($cookie) ) {
    		switch ($scheme) {
    			case 'auth':
                    $cookie_name        = AUTH_COOKIE;
    				break;
    			case 'secure_auth':
                    $cookie_name        = SECURE_AUTH_COOKIE;
                    break;
    			case 'logged_in':
                    $cookie_name        = LOGGED_IN_COOKIE;
    				break;
    			default:
    				if ( is_ssl() ) {
                        $cookie_name    = SECURE_AUTH_COOKIE;
                        $scheme         = 'secure_auth';
    				} else {
                        $cookie_name    = AUTH_COOKIE;
                        $scheme         = 'auth';
    				}
                    break;
    		}
    
    		if ( empty($_COOKIE[$cookie_name]) )
    			return false;
    		$cookie = $_COOKIE[$cookie_name];
    	}
        
    	$cookie_elements = explode('|', $cookie);
    	if ( count($cookie_elements) != 4 )
    		return false;
      
    	list($username, $expiration, $hmac, $id_member) = $cookie_elements;
    
    	return compact('username', 'expiration', 'hmac', 'id_member', 'scheme');
    }
}

if ( !function_exists('erp_set_auth_cookie') ){
    /**
     * Sets the authentication cookies based Member ID.
     *
     * The $remember parameter increases the time that the cookie will be kept. The
     * default the cookie is kept without remembering is two days. When $remember is
     * set, the cookies will be kept for 14 days or two weeks.
     *
     *
     * @param int $id_member Member ID
     * @param bool $remember Whether to remember the member
     */
    
    function erp_set_auth_cookie( $id_member, $remember = false, $secure = '' )
    {    
        $CI =& get_instance();
        
    	if ( $remember ) {
            $expiration = $expire = 2147483647; // maximum expired value (PHP limitation)
    	} else {
    		$expiration = time() + 172800;
    		$expire = 0;
    	}
        
        if ( '' === $secure )
    		$secure = is_ssl();
    
    	if ( $secure ) {
            $auth_cookie_name   = SECURE_AUTH_COOKIE;
            $scheme             = 'secure_auth';
    	} else {
            $auth_cookie_name   = AUTH_COOKIE;
            $scheme             = 'auth';
    	}
        
        $auth_cookie            = erp_generate_auth_cookie($id_member, $expiration, $scheme);
        $logged_in_cookie       = erp_generate_auth_cookie($id_member, $expiration, 'logged_in');
        
    	if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', base_url(), $regs)){
            $cookie_domain      = '.' . $regs['domain'];
    	}else{
            $cookie_domain      = str_replace(array('http://', 'https://', 'www.'), '', base_url());
            $cookie_domain      = '.' . str_replace('/', '', $cookie_domain);
    	}
     
    	$cookie = array(
    		'name'   => $auth_cookie_name,
    		'value'  => $auth_cookie,
    		'expire' => $expire,
    		'path'   => '/',
    		'domain' => $cookie_domain,
    		'secure' => false
    	);
              
    	$CI->input->set_cookie($cookie);
     
    	unset($cookie);
    	
    	$cookie = array(
    		'name'   => LOGGED_IN_COOKIE,
    		'value'  => $logged_in_cookie,
    		'expire' => $expire,
    		'path'   => '/',
    		'domain' => $cookie_domain,
    		'secure' => false
    	);
        
    	$CI->input->set_cookie($cookie);
    }
}

if ( !function_exists('erp_clear_auth_cookie') ){
    /**
     * Removes all of the cookies associated with authentication.
     *
     * @since 2.5
     */
    function erp_clear_auth_cookie()
    {
        $CI =& get_instance();
        $logged = false;
        
    	if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', base_url(), $regs))
            $cookie_domain  = '.'.$regs['domain'];
        else{
            $cookie_domain  = str_replace(array('http://', 'https://', 'www.'), '', base_url());
            $cookie_domain  = '.'.str_replace('/', '', $cookie_domain);
        }
        
        $id_member  = erp_get_current_member_id();
        
        if ( !$id_member ){
        	if ($id = erp_isset($_COOKIE['logged_in_'.md5('nonssl')], false, true)){
                if (erp_isset($CI->session->userdata('member_logged_in')) != "") $logged = true;
        	}
        }else{
            $logged = true;
        }

    	if( $logged ) {
    	   
        	$CI =& get_instance();
            
            $member     = erp_get_current_member();
         
            $name       = 'last_login_'.strtolower(date('F'));
            $cookie     = array(
                'name'      => md5($name),
                'value'     => $id_member . '-'.time(),
                'expire'    => time()+60*60*24*30,
                'path'      => '/',
                'domain'    => $cookie_domain,
                'secure'    => false
        	);
            
        	$CI->input->set_cookie($cookie);
        }
        
    	setcookie(AUTH_COOKIE, ' ', time() - 31536000, '/', $cookie_domain);
    	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, '/', $cookie_domain);
    	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, '/', $cookie_domain);
    
    	// Old cookies
    	setcookie(AUTH_COOKIE, ' ', time() - 31536000, '/', $cookie_domain);
    	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, '/', $cookie_domain);
    
    	// Even older cookies
    	setcookie(MEMBER_COOKIE, ' ', time() - 31536000, '/', $cookie_domain);
    	setcookie(PASS_COOKIE, ' ', time() - 31536000, '/', $cookie_domain);
    	
    	// Logged in unsecure
    	setcookie('logged_in_'.md5('nonssl'), ' ', time() - 31536000, '/', $cookie_domain);
    }
}


if ( !function_exists('erp_generate_auth_cookie') ){
    /**
     * Generate authentication cookie contents.
     *
     * @param int       $id_member      (Required)      Member ID
     * @param int       $expiration     (Required)      Cookie expiration in seconds
     * @param string    $scheme         (Optional}      The cookie scheme to use: auth, secure_auth, or logged_in
     * @return string Authentication cookie contents
     */
    function erp_generate_auth_cookie($id_member, $expiration, $scheme = 'auth') {
    	$CI =& get_instance();
    	$member    = $CI->Model_Member->get_memberdata($id_member);
        
    	$pass_frag = substr($member->password, 8, 4);
    
    	$key       = erp_hash($member->username . $pass_frag . '|' . $expiration, $scheme);
    	$hash      = hash_hmac('md5', $member->username . '|' . $expiration, $key);
    
    	$cookie    = $member->username . '|' . $expiration . '|' . $hash . '|' . $id_member;
    
    	return $cookie;
    }
}

if ( !function_exists('erp_hash') ){
    /**
     * Get hash of given string.
     *
     * @uses erp_salt() Get AC salt
     *
     * @param   string $data Plain text to hash
     * @return  string Hash of $data
     */
    function erp_hash($data, $scheme = 'auth') {
    	$salt = erp_salt($scheme);
    
    	return hash_hmac('md5', $data, $salt);
    }
}

if ( !function_exists('erp_salt') ){
    /**
     * Get salt to add to hashes to help prevent attacks.
     *
     * @param   string $scheme Authentication scheme
     * @return  string Salt value
     */
    function erp_salt($scheme = 'auth') {
    
    	$CI =& get_instance();
    
    	$secret_key = $CI->config->item('encryption_key');
    
    	if ( 'auth' == $scheme ) {
    		if ( defined('AUTH_KEY') && ('' != AUTH_KEY) )
    			$secret_key = AUTH_KEY;
    
    		if ( defined('AUTH_SALT') && ('' != AUTH_SALT) ) {
    			$salt = AUTH_SALT;
    		}
    	} else if ( 'secure_auth' == $scheme ) {
    		if ( defined('SECURE_AUTH_KEY') && ('' != SECURE_AUTH_KEY) )
    			$secret_key = SECURE_AUTH_KEY;
    
    		if ( defined('SECURE_AUTH_SALT') && ('' != SECURE_AUTH_SALT) ) {
    			$salt = SECURE_AUTH_SALT;
    		}
    	} else if ( 'logged_in' == $scheme ) {
    		if ( defined('LOGGED_IN_KEY') && ('' != LOGGED_IN_KEY) )
    			$secret_key = LOGGED_IN_KEY;
    
    		if ( defined('LOGGED_IN_SALT') && ('' != LOGGED_IN_SALT) ) {
    			$salt = LOGGED_IN_SALT;
    		}
    	} else if ( 'nonce' == $scheme ) {
    		if ( defined('NONCE_KEY') && ('' != NONCE_KEY) )
    			$secret_key = NONCE_KEY;
    
    		if ( defined('NONCE_SALT') && ('' != NONCE_SALT) ) {
    			$salt = NONCE_SALT;
    		}
    	} else {
    		// ensure each auth scheme has its own unique salt
    		$salt = hash_hmac('md5', $scheme, $secret_key);
    	}
    
    	return $secret_key . $salt;
    }
}

if ( !function_exists('is_member_logged_in') ){
    /**
     * Checks if the current visitor is a logged in user.
     *
     * @return bool True if user is logged in, false if not logged in.
     */
    function is_member_logged_in()
    {		
        $CI =& get_instance();
        $id_member  = erp_get_current_member_id();
        
        if ( !$id_member ){
        	if ($id = erp_isset($_COOKIE['logged_in_'.md5('nonssl')], false, true)){
                $member     = $CI->Model_Member->get_memberdata($id);
                $id_member  = $member->id;
        	}
            return false;
        }
        
    	return true;
    }
}

if (!function_exists('erp_get_last_logged_in')){
    /**
     * Get last login member via cookies
     * @return member id
     */
    function erp_get_last_logged_in(){
    	$CI 	=& get_instance();
    	$name 	= 'last_login_'.strtolower(date('F'));
    	$cookie = $CI->input->cookie($name);
    	
    	if(!$cookie) return false;
    	
    	return $cookie;
    }
}

if (!function_exists('erp_get_memberdata_by_id')){
    /**
     * Get member data by id
     *
     * @param integer $id Member ID
     * @return (object) member data
     */
    function erp_get_memberdata_by_id($id){
    	$CI =& get_instance();
    	return $CI->Model_Member->get_memberdata($id);
    }
}

if (!function_exists('as_active_member')){
    /**
     * 
     * Is current member is active member
     * @param Object $member
     * @return bool
     */
    function as_active_member( $member ){	
        if( !empty($member) ){
            return ( $member->status == 1 );
        }
        return false;
    }
}

if (!function_exists('as_administrator')){
    /**
     * 
     * Is current member is administrator
     * @param Object $member
     * @return bool
     */
    function as_administrator( $member ){
    	if (!$member)
    		return false;
        
        $CI =& get_instance();
        $member = $CI->erp_member->member($member->id);
    		
    	return ( ($member->type == 1) );	
    }
}

if (!function_exists('as_consultant')){
    /**
     * 
     * Is current member is administrator
     * @param Object $member
     * @return bool
     */
    function as_consultant( $member ){
    	if (!$member)
    		return false;
        
        $CI =& get_instance();
        $member = $CI->erp_member->member($member->id);
    		
    	return ( ($member->type == 3) );	
    }
}

if (!function_exists('erp_get_current_member_id')){
    /**
     * 
     * Get current logged in member id
     * @param none
     * @return integer member id
     */
    function erp_get_current_member_id(){
    	$auth_cookie = erp_parse_auth_cookie( erp_isset( $_COOKIE[LOGGED_IN_COOKIE] ), 'logged_in');
        
    	if( !is_array($auth_cookie) ) return false;
    	
    	return $auth_cookie['id_member'];
    }
}

if (!function_exists('erp_check_node')){
    /**
     * 
     * Check your first node available
     * @param   Int     $id_member  Member ID
     * @param   String  $position   Position of Member
     * @return Mixed, Boolean false if invalid member id, otherwise array of node available
     */
    function erp_check_node($id_member, $position=''){
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $CI =& get_instance();
        
        if( !empty($position) ){
            $nodedata       = $CI->Model_Member->get_node_available($id_member, FALSE, $position);
            if( !empty($nodedata) ){
                return $nodedata[0]; 
            }
            return false; 
        }else{
            $nodedata       = $CI->Model_Member->get_node_available($id_member);
            $rows           = count($nodedata);
            $node           = array();
            
            if( $rows == 0 ){
                $node       = array();
                $node[]     = 'left';
                $node[]     = 'right';
            }elseif( $rows == 1 ){
                $row        = $nodedata[0];
                $node       = ( $row->position == 'left' ? 'right' : 'left' );
            }else{
                $node       = '';
            }
            return $node;
        }
    }
}

if (!function_exists('erp_member_pin')){
    /**
     * 
     * Get member pin
     * @param   Int     $id_member  (Required)  Member ID
     * @param   String  $status     (Optional)  Status of Pin, default 'all'
     * @param   Boolean $count      (Optional)  Count PIN, default 'false'
     * @param   String  $package    (Optional)  Package of PIN
     * @return Mixed, Boolean false if invalid member id, otherwise array of member pin
     */
    function erp_member_pin($id_member, $status='all', $count=false, $package=''){
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $CI =& get_instance();

        $pins    = $CI->Model_Member->get_pins($id_member, $status, $count, $package);
        
        return $pins;
    }
}

if (!function_exists('erp_member_order_pin')){
    /**
     * 
     * Get member order pin
     * @param   Int     $id_member  (Required)  Member ID
     * @param   String  $status     (Optional)  Status of Pin, default 'all'
     * @param   Boolean $used       (Optional)  Used Pin, default 'false'
     * @return Mixed, Boolean false if invalid member id, otherwise array of member order pin
     */
    function erp_member_order_pin($id_member, $status='all'){
        if ( !is_numeric($id_member) ) return false;

        $id_member      = absint($id_member);
        if ( !$id_member ) return false;
        
        $CI =& get_instance();

        $pin_orders  = $CI->Model_Member->get_pin_orders($id_member, $status);
        
        return $pin_orders;
    }
}

if ( !function_exists('erp_generate_password') )
{
    /**
     * Generate password for member
     * @author  Iqbal
     * @param   int     $length     Random String Length
     * @return  String
     */
    function erp_generate_password($length = 0, $upper=FALSE) {
        $characters     = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        if( $upper ){
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $randomString   = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}

if ( !function_exists('erp_generate_tree') )
{
    /**
     * Generate tree for member
     * @author  Iqbal
     * @param   Int     $id_member  (Required)  Member ID
     * @param   int     $up_tree    (Required)  Upline Tree
     * @return  String
     */
    function erp_generate_tree($id_member, $up_tree) {
        if ( !$up_tree ) return false;
        
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $tree = $up_tree . '-' . $id_member;

        return $tree;
    }
}

if ( !function_exists('erp_generate_tree_auto_global') )
{
    /**
     * Generate tree for auto global
     * @author  Iqbal
     * @param   Int     $id_auto    (Required)  Auto Global ID
     * @param   int     $up_tree    (Required)  Upline Tree
     * @return  String
     */
    function erp_generate_tree_auto_global($id_auto, $up_tree) {
        if ( !$up_tree ) return false;
        
        if ( !is_numeric($id_auto) ) return false;

        $id_auto  = absint($id_auto);
        if ( !$id_auto ) return false;
        
        $tree = $up_tree . '-' . $id_auto;

        return $tree;
    }
}

if ( !function_exists('erp_count_node') )
{
    /**
     * Get point of count node of member
     * @author  Iqbal
     * @param   Int     $id_member      (Required)  Member ID
     * @param   String  $position       (Required)  Position Of Node, value ('kiri' or 'kanan')
     * @param   Booleah $count          (Optional)  To Get count or list of member
     * @return  Int of node
     */
    function erp_count_node($id_member, $position, $count=true) {
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        if ( !$position ) return false;
        
        $CI =& get_instance();
        
        $node       = $CI->Model_Member->get_countnode($id_member, $position, $count);
        
        return $node;
    }
}


if ( !function_exists('erp_count_cashreward') )
{
    /**
     * Counts childs of member
     * @author  Ahmad
     * @param   Int         $id_member              (Required)  Member ID
     * @param   String      $position               (Required)  Position Of Node, value ('kiri' or 'kanan')
     * @param   Boolean     $only_non_qualified     (Optional)  Get Only Non Qualified
     * @return  Int of child number
     */
    function erp_count_cashreward($id_member, $position='', $only_non_qualified=true) {
        $CI =& get_instance();
        return $CI->Model_Member->count_cashreward($id_member, $position, $only_non_qualified);
    }
}

if ( !function_exists('erp_count_childs') )
{
    /**
     * Counts childs of member
     * @author  Ahmad
     * @param   Int         $id_member              (Required)  Member ID
     * @param   String      $position               (Required)  Position Of Node, value ('kiri' or 'kanan')
     * @param   Boolean     $only_non_qualified     (Optional)  Get Only Non Qualified
     * @param   Boolean     $pairing                (Optional)  For Pairing Poin
     * @return  Int of child number
     */
    function erp_count_childs($id_member, $position='', $only_non_qualified=true, $pairing=FALSE) {
        $CI =& get_instance();
        return $CI->Model_Member->count_childs($id_member, $position, $only_non_qualified, $pairing);
    }
}

if ( !function_exists('erp_count_childs_all') )
{
    /**
     * Counts childs of member
     * @author  Ahmad
     * @param   Int         $id_member              (Required)  Member ID
     * @param   String      $position               (Required)  Position Of Node, value ('kiri' or 'kanan')
     * @param   Boolean     $only_non_qualified     (Optional)  Get Only Non Qualified
     * @param   Boolean     $as_sponsor             (Optional)  Get Child as Sponsor
     * @return  Int of child number
     */
    function erp_count_childs_all($id_member, $position='', $only_non_qualified=true, $as_sponsor=FALSE) {
        $CI =& get_instance();
        return $CI->Model_Member->count_childs_all($id_member, $position, $only_non_qualified, $as_sponsor);
    }
}

if ( !function_exists('erp_count_childs_auto_global') )
{
    /**
     * Counts childs of auto global
     * @author  Iqbal
     * @param   Int         $id_auto                (Required)  Auto Global ID
     * @param   String      $position               (Required)  Position Of Node, value ('kiri' or 'kanan')
     * @return  Int of child auto global
     */
    function erp_count_childs_auto_global($id_auto, $position) {
        $CI =& get_instance();
        return $CI->Model_Member->count_childs_auto_global($id_auto, $position);
    }
}

if ( !function_exists('erp_count_poinpairing') )
{
    /**
     * Counts childs of member
     * @author  Ahmad
     * @param   Int         $id_member              (Required)  Member ID
     * @param   String      $position               (Required)  Position Of Node, value ('kiri' or 'kanan')
     * @param   Boolean     $only_non_qualified     (Optional)  Get Only Non Qualified
     * @return  Int of child number
     */
    function erp_count_poinpairing($id_member, $position='', $only_non_qualified=true) {
        $CI =& get_instance();
        return $CI->Model_Member->count_poin_pairing($id_member, $position, $only_non_qualified);
    }
}

if ( !function_exists('erp_count_poinreward') )
{
    /**
     * Counts poin reward of member
     * @author  Ahmad
     * @param   Int         $id_member              (Required)  Member ID
     * @param   String      $position               (Required)  Position Of Node, value ('kiri' or 'kanan')
     * @param   String      $package                (Optional)  Package of Member
     * @param   Boolean     $as_sponsor             (Optional)  Get Child as Sponsor
     * @return  Int of child number
     */
    function erp_count_poinreward($id_member, $position='', $package='', $as_sponsor=FALSE) {
        $CI =& get_instance();
        return $CI->Model_Member->count_poin_reward($id_member, $position, $package, $as_sponsor);
    }
}

if ( !function_exists('erp_count_poinreward_auto') )
{
    /**
     * Counts poin auto global reward of member
     * @author  Ahmad
     * @param   Int         $id_member              (Required)  Member ID
     * @param   String      $position               (Required)  Position Of Node, value ('kiri' or 'kanan')
     * @return  Int of child number
     */
    function erp_count_poinreward_auto($id_member, $position) {
        $CI =& get_instance();
        return $CI->Model_Member->count_poin_reward_auto($id_member, $position);
    }
}

if ( !function_exists('erp_markas_qualified') )
{
    /**
     * Counts childs of member
     * @author  Ahmad
     * @param   Int     $id_member      (Required)  Member ID
     * @param   String  $position       (Required)  Position Of Node, value ('kiri' or 'kanan')
     * @return  Int of child number
     */
    function erp_markas_qualified($id_member, $child_number, $datetime='') {
        $CI =& get_instance();
        return $CI->Model_Member->markas_qualified($id_member, $child_number, $datetime);
    }
}

if ( !function_exists('erp_uplines') )
{
    /**
     * Get uplines of member
     * @author  Ahmad
     * @param   Int     $id_member      (Required)  Member ID
     * @param   Int  	$max_lookup     (Required)  How many upline you want to get
     * @return  Object of database row
     */
    function erp_uplines($id_member, $max_lookup) {
        $CI =& get_instance();
        return $CI->Model_Member->get_uplines($id_member, $max_lookup);
    }
}

if ( !function_exists('erp_member_bonus') )
{
    /**
     * Get total bonus per period
     * @author  Ahmad
     * @param   Int     $id_member      (Required)  Member ID
     * @param   Int  	$period_month   (required)  Period month - number of month
	 * @param   Int  	$period_year   	(required)  Period year - number of year
     * @return  Int total bonus
     */
    function erp_member_bonus($id_member, $period_month, $period_year) {
        $CI =& get_instance();
        return $CI->Model_Member->count_member_bonus($id_member, $period_month, $period_year);
    }
}

if ( !function_exists('erp_parent') )
{
    /**
     * Get parent data of member
     * @author  Ahmad
     * @param   Int     $id_member      (Required)  Member ID
     * @return  Object parent data
     */
    function erp_parent($id_member) {
        $CI =& get_instance();
        return $CI->Model_Member->get_parentdata($id_member);
    }
}

if ( !function_exists('erp_downline') )
{
    /**
     * Get downline of member
     * @author  Iqbal
     * @param   Int         $id_member      (Required)  Member ID (Parent)
     * @param   String      $position       (Optional)  Position of downline, value ('kiri' or 'kanan')
     * @param   String      $status         (Optional)  Status of Downline, value ('active' or 'pending')
     * @param   Boolean     $count          (Optional)  Get Count of Downline
     * @return  Mixed, Boolean if wrong data of id member, otherwise data or count of downline
     */
    function erp_downline($id_member, $position='', $status='', $count=false) {
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;

        $CI =& get_instance();        
        $member     = $CI->Model_Member->get_downline($id_member, $position, $status, $count);
        
        return $member;
    }
}

if ( !function_exists('erp_downline_minimun') )
{
    /**
     * Get downline minimum of member
     * @author  Iqbal
     * @param   Int     $id_member  (Required)  Member ID (Parent)
     * @param   Array   $downline   (Required)  Array of downline
     * @return  Mixed, Boolean if wrong data of id member, otherwise data or count of downline
     */
    function erp_downline_minimun($id_member, $downline) {
        if ( !is_numeric($id_member) ) return false;

        $id_member          = absint($id_member);
        if ( !$id_member ) return false;
        
        if ( !$downline ) return false;
        
        $count_left         = erp_count_node($id_member, POS_KIRI);
        $count_right        = erp_count_node($id_member, POS_KANAN);
        $upline_data        = new stdClass();
        $position           = '';
        
        $minimum            = $count_left;
        $side               = POS_KIRI;
        if($count_right < $minimum) {
            $minimum        = $count_right;
            $side           = POS_KANAN;
        }
        
        $member             = '';
        foreach($downline as $down){
            if( $down->position == $side ){
                $member     = $down;
            }
        }
        $down_member        = erp_downline($member->id);
        
        if( count($down_member) == 2 ){
            return erp_downline_minimun($member->id, $down_member);
        }elseif( count($down_member) == 1 ){
            foreach($down_member as $row){
                $position   = ( $member->position == POS_KIRI ? POS_KANAN : POS_KIRI );
            }
            $upline_data->upline    = $member;
            $upline_data->position  = $position;
        }elseif( count($down_member) == 0 ){
            $upline_data->upline    = $member;
            $upline_data->position  = POS_KIRI;
        }
        
        return $upline_data; 
    }
}

if ( !function_exists('erp_downline_auto_global') )
{
    /**
     * Get downline of auto global
     * @author  Iqbal
     * @param   Int         $id_auto        (Required)  Auto Global ID (Parent)
     * @param   String      $position       (Optional)  Position of downline, value ('kiri' or 'kanan')
     * @param   Boolean     $count          (Optional)  Get Count of Downline
     * @return  Mixed, Boolean if wrong data of id auto global, otherwise data or count of downline
     */
    function erp_downline_auto_global($id_auto, $position='', $count=false) {
        if ( !is_numeric($id_auto) ) return false;

        $id_auto    = absint($id_auto);
        if ( !$id_auto ) return false;

        $CI =& get_instance();        
        $autoglobal = $CI->Model_Member->get_downline_auto_global($id_auto, $position, $count);
        
        return $autoglobal;
    }
}

if (!function_exists('erp_reward')){
    /**
     * 
     * Check member reward
     * @param   Int     $id_member          Member ID
     * @param   Boolean $count_ancestry     Ancestry Process
     * @param   Boolean $debug              Debug Mode
     * @param   Boolean $send_notification  Send Notification Opt
     * @return Mixed, Boolean false if invalid member id, otherwise return void
     */
    function erp_reward($id_member, $count_ancestry=FALSE, $debug=FALSE, $send_notification=FALSE){
        if ( !is_numeric($id_member) ) return false;

        $id_member      = absint($id_member);
        if ( !$id_member ) return false;
        
        $CI =& get_instance();
        
        $memberdata     = erp_get_memberdata_by_id($id_member);
        if ( !$memberdata ) return false;
        
        $is_admin       = as_administrator($memberdata);
        if ( !$is_admin ){
            // Set Count Both Position
            $count_left         = $memberdata->left_happypoin;
            $count_right        = $memberdata->right_happypoin;
            
            $rewardtext         = '';
            $start              = '2016-12-16';
            $end                = '2017-12-31';
            $periode_start      = strtotime($start);
            $periode_end        = strtotime($end);
            $today              = strtotime(date('Y-m-d'));
            $curdate            = date('Y-m-d H:i:s');

            if( ($today >= $periode_start) && ($today <= $periode_end) ){
                /*
                $reward_opt                 = erp_rewards_option(1);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){   
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 1));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 1,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                $reward_opt                 = erp_rewards_option(2);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 2));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 2,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                $reward_opt                 = erp_rewards_option(3);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 3));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 3,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                $reward_opt                 = erp_rewards_option(4);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 4));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 4,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                $reward_opt                 = erp_rewards_option(5);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 5));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 5,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                $reward_opt                 = erp_rewards_option(6);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 6));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 6,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                $reward_opt                 = erp_rewards_option(7);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 7));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 7,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                
                $reward_opt                 = erp_rewards_option(8);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 8));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 8,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                */
                
                //Qualifikasi Happy Point Baru
                //Reward Option Baru
                //HP Samsung 
                $reward_opt                 = erp_rewards_option(17);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){   
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 1));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 17,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                
                //Motor Yamaha Mio
                $reward_opt                 = erp_rewards_option(18);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 2));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 18,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                
                //Motor Ninja 250 ABS
                $reward_opt                 = erp_rewards_option(19);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 3));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 19,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                
                //Mobil Honda Mobilio
                $reward_opt                 = erp_rewards_option(20);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 4));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 20,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                
                //Mobil Honda Accord
                $reward_opt                 = erp_rewards_option(21);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 5));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 21,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                
                //Rumah 1 M
                $reward_opt                 = erp_rewards_option(22);
                if( $count_left >= $reward_opt->reward_poin && $count_right >= $reward_opt->reward_poin ){
                    $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 6));
                    if( !$reward ){
                        $data_reward        = array(
                            'id_member'     => $memberdata->id,
                            'type'          => 22,
                            'datecreated'   => $curdate,
                            'datemodified'  => $curdate,
                        );
                        if( $debug ){
                            $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                        }else{
                            $CI->Model_Member->save_data_reward($data_reward);
                            if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                        }
                    }
                }
                
                if( $debug ){
                    echo "<pre>";
                    echo 'HP : '.$count_left.' - HP : '.$count_right.br();
                    echo 'Username : '.$memberdata->username . ' = ';
                    echo !empty($rewardtext) ? $rewardtext : 'Anda belum mendapatkan reward. Tingkatkan terus Jaringan Anda!' . br();
                    echo "</pre>";
                }
            }
        }
        
        /**
		 * We count the ancestry first since although this member has no pair, the ancestry may have
		 */
		if ($count_ancestry) {
			// check if ancestry available for this member
			if ($ancestry = erp_ancestry($id_member)) {
				// ancestry is returned in coma delimited
				$ancestry = explode(',', $ancestry);
                if( !empty($ancestry) ){
                    foreach($ancestry as $id_ancestry) {
    					$id_ancestry = absint($id_ancestry);
                        if ( $id_ancestry == 1 ) continue; 
    					erp_reward($id_ancestry, FALSE, FALSE, TRUE);
    				}
                }
			}
		}
    }
}

if (!function_exists('erp_cashreward')){
    /**
     * 
     * Check member reward
     * @param   Int     $id_member          Member ID
     * @param   Boolean $count_ancestry     Ancestry Process
     * @param   Boolean $debug              Debug Mode
     * @param   Boolean $send_notification  Send Notification Opt
     * @return Mixed, Boolean false if invalid member id, otherwise return void
     */
    function erp_cashreward($id_member, $count_ancestry=FALSE, $debug=FALSE, $send_notification=FALSE){
        if ( !is_numeric($id_member) ) return false;

        $id_member      = absint($id_member);
        if ( !$id_member ) return false;
        
        $CI =& get_instance();
        
        $memberdata     = erp_get_memberdata_by_id($id_member);
        if ( !$memberdata ) return false;
        
        $is_admin       = as_administrator($memberdata);
        if ( !$is_admin ){
            //tidak sama dengan paket cash reward
            if( $memberdata->package == PACKAGE_CASH_REWARD ){
                // Set Count Both Position
                $count_left         = $memberdata->left_cashrewardpoin;
                $count_right        = $memberdata->right_cashrewardpoin;
                
                $rewardtext         = '';
                $start              = '2016-12-16';
                $end                = '2017-12-31';
                //promo
                $cfg_promo          = config_item('promo');
                $startdate          = strtotime($cfg_promo);
                $enddate            = strtotime("+2 month", $startdate);
                $today              = strtotime(date('Y-m-d'));
                $curdate            = date('Y-m-d H:i:s');
    
                if( ($today >= $startdate) && ($today <= $enddate) ){
                    
                    //Qualifikasi Happy Point Baru
                    //Reward Option Baru
                    //HP Samsung 
                    $cashreward_opt             = erp_cashrewards_option(1);
                    if( $count_left >= $cashreward_opt->reward_poin && $count_right >= $cashreward_opt->reward_poin ){   
                        $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 1));
                        if( !$reward ){
                            $data_reward        = array(
                                'id_member'     => $memberdata->id,
                                'type'          => 1,
                                'datecreated'   => $curdate,
                                'datemodified'  => $curdate,
                            );
                            if( $debug ){
                                $rewardtext     = 'Cash Reward ' . $cashreward_opt->reward_name;
                            }else{
                                $CI->Model_Member->save_data_cashreward($data_reward);
                                //if($send_notification) $CI->erp_sms->sms_reward($memberdata->phone, $memberdata->username, $cashreward_opt->reward_name);
                            }
                        }
                    }
                    if( $debug ){
                        echo "<pre>";
                        echo 'CR : '.$count_left.' - CR : '.$count_right.br();
                        echo 'Username : '.$memberdata->username . ' = ';
                        echo !empty($rewardtext) ? $rewardtext : 'Anda belum mendapatkan cash reward. Tingkatkan terus Jaringan Anda!' . br();
                        echo "</pre>";
                    }
                }    
            }
            
        }
        
        /**
		 * We count the ancestry first since although this member has no pair, the ancestry may have
		 */
		if ($count_ancestry) {
			// check if ancestry available for this member
			if ($ancestry = erp_ancestry($id_member)) {
				// ancestry is returned in coma delimited
				$ancestry = explode(',', $ancestry);
                if( !empty($ancestry) ){
                    foreach($ancestry as $id_ancestry) {
    					$id_ancestry = absint($id_ancestry);
                        if ( $id_ancestry == 1 ) continue; 
    					erp_cashreward($id_ancestry, FALSE, FALSE, TRUE);
    				}
                }
			}
		}
    }
}

if ( !function_exists('erp_reward_option') )
{
    /**
     * Get reward option
     * @author  Iqbal
     * @param   Int         $point     (Required)  Poin Reward
     * @return  Boolean
     */
    function erp_reward_option($point=0) {
    	$point = absint($point);
		$CI =& get_instance();
		
		$sql = 'SELECT * FROM erp_reward_option';
		if(!empty($point)) $sql .= ' WHERE reward_poin=?';
		
		$qry = $CI->db->query($sql, array($point));
		if(!$qry || !$qry->num_rows()) return false;
		
		return $point ? $qry->row() : $qry->result();
	}
}

if ( !function_exists('erp_cron_executed') ) 
{
    /**
     * Check Cron Executed
     * @author  Iqbal
     * @param   String      $cron_name      (Required)  Name of Cron Job
     * @param   String      $interval       (Optional)  Interval of Cron Job, default 'monthly'
     * @return  Boolean
     */
	function erp_cron_executed($cron_name, $interval='monthly') {
		if (empty($cron_name)) return false;
		
		$CI =& get_instance();
		$sql = 'SELECT * FROM erp_log WHERE log_name=? AND YEAR(log_time)=YEAR(CURRENT_DATE) AND MONTH(log_time)=MONTH(CURRENT_DATE)';
		if ($interval == 'daily') {
			$sql .= ' AND DAY(log_time)=DAY(CURRENT_DATE)';
		} elseif ($interval == 'hourly') {
			$sql .= ' AND DAY(log_time)=DAY(CURRENT_DATE) AND HOUR(log_time)=HOUR(NOW())';
		}

		$qry = $CI->db->query($sql, array($cron_name));
		if(!$qry || !$qry->num_rows()) return false;
		
		return true;
	}
}

if ( !function_exists('erp_paket') ) 
{
    /**
     * Get packet
     * @author  Ahmad
     * @param   Int         $id     (Optional)  ID of Paket
     * @return  Paket data
     */
	function erp_paket($id='') {
		$paket = config_item('paket');
		if (!empty($id) && isset($paket[$id])) return $paket[$id];
		return $paket;
	}
}

if ( !function_exists('erp_banks') ) 
{
    /**
     * Get bank data
     * @author  Iqbal
     * @param   Int         $id     (Optional)  ID of bank
     * @return  Bank data
     */
	function erp_banks($id='') {
        $CI =& get_instance();
        $banks = $CI->Model_Member->get_banks($id); 
		return $banks;
	}
}

if ( !function_exists('erp_rewards_option') ) 
{
    /**
     * Get bank data
     * @author  Iqbal
     * @param   Int         $id     (Optional)  ID of bank
     * @return  Bank data
     */
	function erp_rewards_option($id='') {
        $CI =& get_instance();
        $banks = $CI->Model_Member->get_rewards_option($id); 
		return $banks;
	}
}

if ( !function_exists('erp_cashrewards_option') ) 
{
    /**
     * Get cash reward data
     * @author  Iqbal
     * @param   Int         $id     (Optional)  ID of bank
     * @return  Bank data
     */
	function erp_cashrewards_option($id='') {
        $CI =& get_instance();
        $banks = $CI->Model_Member->get_cashrewards_option($id); 
		return $banks;
	}
}

if ( !function_exists('erp_ancestry') )
{
    /**
     * Get ancestry data of member
     * @author  Ahmad
     * @param   Int     $id_member      (Required)  Member ID
     * @return  Object parent data
     */
    function erp_ancestry($id_member) {
        $CI =& get_instance();
        return $CI->Model_Member->get_ancestry($id_member);
    }
}

if ( !function_exists('erp_ancestry_sponsor') )
{
    /**
     * Get ancestry sponsor data of member
     * @author  Ahmad
     * @param   Int     $id_member      (Required)  Member ID
     * @return  Object parent data
     */
    function erp_ancestry_sponsor($id_member) {
        $CI =& get_instance();
        return $CI->Model_Member->get_ancestry_sponsor($id_member);
    }
}

if ( !function_exists('erp_ancestry_auto_global') )
{
    /**
     * Get ancestry data of auto global
     * @author  Iqbal
     * @param   Int     $id_auto_global     (Required)  Auto Global ID
     * @return  Object parent data of auto global
     */
    function erp_ancestry_auto_global($id_auto_global) {
        $CI =& get_instance();
        return $CI->Model_Member->get_ancestry_auto_global($id_auto_global);
    }
}

if ( !function_exists('erp_update_member_tree') )
{
    /**
     * Update member tree
     * @author  Iqbal
     * @param   Int     $id_member          (Optional)  Member ID
     * @return  Object parent data
     */
    function erp_update_member_tree($id_member=0) {
        $CI =& get_instance();
        return $CI->Model_Member->update_member_tree($id_member);
    }
}

if ( !function_exists('erp_update_auto_global_tree') )
{
    /**
     * Update auto global tree
     * @author  Iqbal
     * @param   Int     $id_auto_global     (Optional)  Auto Global ID
     * @return  Object parent data of auto global
     */
    function erp_update_auto_global_tree($id_auto_global=0) {
        $CI =& get_instance();
        return $CI->Model_Member->update_auto_global_tree($id_auto_global);
    }
}

if ( !function_exists('erp_node') )
{
    /**
     * Get node
     * @author  Iqbal
     * @param   Int         $id_member  (Required)  Member ID
     * @param   Boolean     $new        (Optional)  New Member
     * @return  Mixed, Boolean if wrong data of id member, otherwise data or node
     */
    function erp_node($id_member, $new=false) {
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $member     = erp_get_memberdata_by_id($id_member);
        if( !$member || empty($member) ) return false;
        
        if( $new == true ){
            /*
            $node = '
            <div class="phone-node">
            <div class="col-md-6 col-sm-6 col-xs-6 node-kiri">-<br />-<br />-</div>
            <div class="col-md-6 col-sm-6 col-xs-6 node-kanan">-<br />-<br />-</div>
            </div>';
            */
            $node = '
            <div class="phone-node">
            <div class="col-md-6 col-sm-6 col-xs-6 node-kiri">-<br />-<br />-</div>
            <div class="col-md-6 col-sm-6 col-xs-6 node-kanan">-<br />-<br />-</div>
            </div>';
            
        }else{
            /*
            $node = '
            <div class="phone-node">
            <div class="col-md-6 col-sm-6 col-xs-6 node-kiri">
                L:'.erp_count_childs($id_member, POS_KIRI, FALSE).'<br />
                HP:'.$member->left_happypoin.'
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 node-kanan">
                R:'.erp_count_childs($id_member, POS_KANAN, FALSE).'<br />
                HP:'.$member->right_happypoin.'
            </div>
            </div>';
            */
            $node = '
            <div class="phone-node">
            <div class="col-md-6 col-sm-6 col-xs-6 node-kiri">
                L:'.erp_count_childs($id_member, POS_KIRI, FALSE).'<br />
                PP:'.erp_count_poinpairing($id_member, POS_KIRI).'<br />
                HP:'.$member->left_happypoin.'
                CR:'.erp_count_cashreward($id_member, POS_KIRI, FALSE).'<br />
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 node-kanan">
                R:'.erp_count_childs($id_member, POS_KANAN, FALSE).'<br />
                PP:'.erp_count_poinpairing($id_member, POS_KANAN).'<br />
                HP:'.$member->right_happypoin.'
                CR:'.erp_count_cashreward($id_member, POS_KANAN, FALSE).'<br />
            </div>
            </div>'; 
            
        }

        return $node;
    }
}

if ( !function_exists('erp_node_auto_global') )
{
    /**
     * Get node auto global
     * @author  Iqbal
     * @param   Int         $id_auto    (Required)  Auto Global ID
     * @return  Mixed, Boolean if wrong data of id auto global, otherwise data or node
     */
    function erp_node_auto_global($id_auto=0) {
        $id_auto  = absint($id_auto);
        
        if( $id_auto == 0 ){
            $node = '
            <div class="phone-node">
                <div class="col-md-6 col-sm-6 col-xs-6 node-kiri">-</div>
                <div class="col-md-6 col-sm-6 col-xs-6 node-kanan">-</div>
            </div>';
        }else{
            $node = '
            <div class="phone-node">
                <div class="col-md-6 col-sm-6 col-xs-6 node-kiri">L:'.erp_count_childs_auto_global($id_auto, POS_KIRI).'</div>
                <div class="col-md-6 col-sm-6 col-xs-6 node-kanan">R:'.erp_count_childs_auto_global($id_auto, POS_KANAN).'</div>
            </div>';
        }
        return $node;
    }
}

if ( !function_exists('erp_update_poin') )
{
    /**
     * Update poin of upline member registered, Pass up
     * @author  Iqbal
     * @param   Int         $upline_id      (Required)  ID Upline of Member
     * @param   Int         $member_id      (Required)  ID Member Registered
     * @param   Boolean     $count_ancestry (Optional)  Count Upline Pass Up Poin
     * @param   String      $position       (Optional)  Position of Poin
     * @param   Boolean     $debug          (Optional)  Debug Mode
     * @return  Boolean
     */
    function erp_update_poin($upline_id, $member_id, $position='', $debug=false) {
        if ( !is_numeric($upline_id) ) return false;
        if ( !is_numeric($member_id) ) return false;

        $upline_id  = absint($upline_id);
        if ( !$upline_id ) return false;
        
        $member_id    = absint($member_id);
        if ( !$member_id ) return false;

        $CI =& get_instance();
        
        $uplinedata             = $CI->Model_Member->get_memberdata($upline_id);
        if ( !$uplinedata ) return false;
        
        $memberdata             = $CI->Model_Member->get_memberdata($member_id);
        if ( !$memberdata ) return false;
        
        $cfg_pairpoin           = config_item('poin');
        $cfg_happypoin          = config_item('happypoin');
        $cfg_cashrewardpoin     = config_item('cashrewardpoin');
        
        $member_package         = $memberdata->package;
        $member_position        = $memberdata->position;
        $member_pairpoin        = absint($cfg_pairpoin[$member_package]);
        $member_happypoin       = absint($cfg_happypoin[$member_package]);
        $member_cashrewardpoin  = absint($cfg_cashrewardpoin[$member_package]);
        
        $position               = !empty($position) ? $position : $member_position;
        
        $new_pairpoin           = $uplinedata->{$position . '_poin'} + $member_pairpoin;
        $new_happypoin          = $uplinedata->{$position . '_happypoin'} + $member_happypoin;
        $new_cashrewardpoin     = $uplinedata->{$position . '_cashrewardpoin'} + $member_cashrewardpoin;

        if( $debug ){
            echo 'Upline Username : '.$uplinedata->username.br();
            echo '------------------------------------------------'.br();
            echo '- New Pair Poin '.strtoupper($position).' : '.$new_pairpoin.br();
            echo '- New Happy Poin '.strtoupper($position).' : '.$new_happypoin.br();
            echo '- New Cash Happy Poin '.strtoupper($position).' : '.$new_cashrewardpoin.br(2);
        }else{
            $data_update = array(
                $position . '_poin'                 => $new_pairpoin,
                $position . '_happypoin'            => $new_happypoin,
                $position . '_cashrewardpoin'       => $new_cashrewardpoin
            );
            $CI->Model_Member->update_data($uplinedata->id, $data_update);
        }
        
        if( $uplinedata->parent > 0 ){
            return erp_update_poin($uplinedata->parent, $member_id, $uplinedata->position, $debug);
        }
        return true;
    }
}

if (!function_exists('erp_check_username')){
    /**
     * 
     * Check username available
     * @param   String  $username   Username
     * @return Mixed, Boolean false if invalid username, otherwise response of username available
     */
    function erp_check_username($username){
        if ( !$username || empty($username) ) return false;
        
        $CI =& get_instance();
        
        $message            = '';
        $memberdata         = $CI->Model_Member->get_member_by('login', strtolower($username));
        
        if( $memberdata ) { 
            $message        = 'notavailable'; 
        }else{
            if (preg_match('/^[a-z]{1}[a-z0-9]{5,31}$/', $username)){
                $message    = 'available';
            }else{
                $message    = 'invalid';
            }
        }
        
        return $message;
    }
}

if (!function_exists('erp_auto_global_node_available')){
    /**
     * 
     * Check your first node available
     * @param Int   $id_auto    Upline Auto Global ID
     * @return Mixed, Boolean false if invalid member id, otherwise array of node available
     */
    function erp_auto_global_node_available($id_auto){        
        if ( !is_numeric($id_auto) ) return false;

        $id_auto            = absint($id_auto);
        if ( !$id_auto ) return false;
        
        $CI =& get_instance();
        
        // Set variable
        $node               = array();
        $nodecount          = $CI->Model_Member->get_auto_global_node_available($id_auto, TRUE);

        if( $nodecount == 0 ){
            // Set left node of member available
            $node = array(
                'position'      => POS_KIRI,
                'id'            => $id_auto,
            );
        }elseif( $nodecount == 1 ){
            // Set left node of member available
            $node = array(
                'position'      => POS_KANAN,
                'id'            => $id_auto,
            );
        }else{
            $left_childs        = erp_count_childs_auto_global($id_auto, POS_KIRI);
            $right_childs       = erp_count_childs_auto_global($id_auto, POS_KANAN);
            $position           = $left_childs <= $right_childs ? POS_KIRI : POS_KANAN;
            $downline           = erp_downline_auto_global($id_auto, $position);
            
            return erp_auto_global_node_available($downline->id);
        }
        
        $node = (object) $node;
        return $node;
    }
}

if (!function_exists('erp_auto_global_process')){
    /**
     * 
     * Auto Global Process
     * @param   Int     $id_member  Member ID
     * @return Mixed, Boolean false if invalid username, otherwise response of username available
     */
    function erp_auto_global_process($id_member){
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;

        $CI =& get_instance();

        // Load Requirement Library
        // -------------------------------------------------
        $CI->load->library('erp_tree_auto');

        // Set Variables
        // -------------------------------------------------
        $datetime               = date('Y-m-d H:i:s');
        
        // Check Member Data
        // -------------------------------------------------
        $memberdata             = $CI->Model_Member->get_memberdata($id_member);
        if ( !$memberdata ) return false;
        
        // Set Sponsor Data
        // -------------------------------------------------
        $sponsorid              = $memberdata->sponsor;
        $sponsordata            = $CI->Model_Member->get_memberdata($sponsorid);
        if ( !$sponsordata ) return false;
        
        // Check Auto Global Admin Node
        // -------------------------------------------------
        $autoglobalall          = $CI->Model_Member->get_all_auto_global();
        if( !$autoglobalall || empty($autoglobalall) ){
            $autoglobal             = array(
                'id_member'         => 1,
                'parent'            => 0,
                'position'          => 'root',
                'index'             => 1,
                'level'             => 0,
                'tree'              => '1',
                'datecreated'       => $datetime,
                'datemodified'      => $datetime,
            );
            $CI->Model_Member->save_data_auto_global($autoglobal);
        }
        
        // Get Available Position
        // -------------------------------------------------
        $availablepos           = $CI->erp_tree_auto->getAvailablePosition($sponsorid);

        if ( !$availablepos || empty($availablepos) ) return false;
        //if ( !$availablepos->slot || empty($availablepos->slot) ) return false;

        $slot                   = $availablepos;
        //$slot                   = $availablepos->slot;
        //$sponsored_count        = $availablepos->sponsoredcount;
        
        /*
        // Check In or Out Auto Global Placement
        // -------------------------------------------------
        $count_sponsored        = $CI->Model_Member->count_sponsored($sponsorid);
        $number_divide          = round( $count_sponsored/2 );
        $node                   = $number_divide % 2 == 0 ? ( $sponsorid==1 ? 1 : $sponsorid ) : 1;

        // Check Auto Global Data
        // -------------------------------------------------
        $autoglobaldata         = $CI->Model_Member->get_auto_global_by('member',$node);
        if ( !$autoglobaldata ) return false;
        
        // Search Auto Global Upline Placement
        // -------------------------------------------------
        $upline                 = erp_auto_global_node_available( $autoglobaldata->id );
        if ( !$upline || empty($upline) ) return false;
        */
        
        // Check Upline Placement
        // -------------------------------------------------
        $uplinedata             = $CI->Model_Member->get_autoglobaldata($slot->parent);
        if ( !$uplinedata ) return false;

        // Set Auto Global Data for save
        // -------------------------------------------------
        $autoglobal             = array(
            'id_member'         => $id_member,
            'parent'            => $slot->parent,
            'position'          => $slot->position,
            'index'             => $slot->index,
            'level'             => $slot->level,
            'package'           => $memberdata->package,
            'datecreated'       => $datetime,
            'datemodified'      => $datetime,
        );

        if( $id_auto = $CI->Model_Member->save_data_auto_global($autoglobal) ){
            // Update Member Tree
            // -------------------------------------------------
            $tree               = erp_generate_tree_auto_global($id_auto, $uplinedata->tree);
            $data_tree          = array('tree' => $tree);
            $update_tree        = $CI->Model_Member->update_data_auto_global($id_auto, $data_tree);
            
            erp_update_auto_global_tree($id_auto);
            
            // Process Auto Global Reward Achievement
            // ------------------------------------------------------
            erp_reward_auto_global($uplinedata->id_member, TRUE, FALSE, TRUE);
            
            //$data_update        = array('sponsored' => $sponsored_count);
            //$CI->Model_Member->update_data($sponsorid, $data_update);
            return true;
        }
        
        return false;
    }
}

if (!function_exists('erp_reward_auto_global')){
    /**
     * 
     * Check member reward
     * @param   Int     $id_member          Member ID
     * @param   Boolean $count_ancestry     Ancestry Process
     * @param   Boolean $debug              Debug Mode
     * @param   Boolean $send_notification  Send Notification Opt
     * @return Mixed, Boolean false if invalid member id, otherwise return void
     */
    function erp_reward_auto_global($id_member, $count_ancestry=FALSE, $debug=FALSE, $send_notification=FALSE){
        if ( !is_numeric($id_member) ) return false;

        $id_member      = absint($id_member);
        if ( !$id_member ) return false;
        
        $CI =& get_instance();
        
        $memberdata     = erp_get_memberdata_by_id($id_member);
        if ( !$memberdata ) return false;
        
        $is_admin       = as_administrator($memberdata);
        if ( !$is_admin ){
            // Set Count Both Position
            $count_left         = erp_count_poinreward_auto($memberdata->id, POS_KIRI);
            $count_right        = erp_count_poinreward_auto($memberdata->id, POS_KANAN);
            
            $rewardtext         = '';
            $curdate            = date('Y-m-d H:i:s');

            // Reward Uang Tunai Rp 1 jt
            $reward_opt                 = erp_rewards_option(9);
            $reward_poin                = $reward_opt->reward_poin;
            if( $count_left >= $reward_poin && $count_right >= $reward_poin ){   
                $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 9));
                if( !$reward ){
                    $data_reward        = array(
                        'id_member'     => $memberdata->id,
                        'type'          => 9,
                        'datecreated'   => $curdate,
                        'datemodified'  => $curdate,
                    );
                    if( $debug ){
                        $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                    }else{
                        $CI->Model_Member->save_data_reward($data_reward);
                        if($send_notification) $CI->erp_sms->sms_reward_autoglobal($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                    }
                }
            }
            
            // Reward Uang Tunai Rp 5 jt
            $reward_opt                 = erp_rewards_option(10);
            $reward_poin                = $reward_opt->reward_poin;
            if( $count_left >= $reward_poin && $count_right >= $reward_poin ){
                $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 10));
                if( !$reward ){
                    $data_reward        = array(
                        'id_member'     => $memberdata->id,
                        'type'          => 10,
                        'datecreated'   => $curdate,
                        'datemodified'  => $curdate,
                    );
                    if( $debug ){
                        $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                    }else{
                        $CI->Model_Member->save_data_reward($data_reward);
                        if($send_notification) $CI->erp_sms->sms_reward_autoglobal($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                    }
                }
            }
            
            // Reward Uang Tunai Rp 15 jt
            $reward_opt                 = erp_rewards_option(11);
            $reward_poin                = $reward_opt->reward_poin;
            if( $count_left >= $reward_poin && $count_right >= $reward_poin ){
                $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 11));
                if( !$reward ){
                    $data_reward        = array(
                        'id_member'     => $memberdata->id,
                        'type'          => 11,
                        'datecreated'   => $curdate,
                        'datemodified'  => $curdate,
                    );
                    if( $debug ){
                        $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                    }else{
                        $CI->Model_Member->save_data_reward($data_reward);
                        if($send_notification) $CI->erp_sms->sms_reward_autoglobal($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                    }
                }
            }
            
            // Reward Uang Tunai Rp 100 jt
            $reward_opt                 = erp_rewards_option(12);
            $reward_poin                = $reward_opt->reward_poin;
            if( $count_left >= $reward_poin && $count_right >= $reward_poin ){
                $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 12));
                if( !$reward ){
                    $data_reward        = array(
                        'id_member'     => $memberdata->id,
                        'type'          => 12,
                        'datecreated'   => $curdate,
                        'datemodified'  => $curdate,
                    );
                    if( $debug ){
                        $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                    }else{
                        $CI->Model_Member->save_data_reward($data_reward);
                        if($send_notification) $CI->erp_sms->sms_reward_autoglobal($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                    }
                }
            }
            
            // Reward Uang Tunai Rp 300 jt
            $reward_opt                 = erp_rewards_option(13);
            $reward_poin                = $reward_opt->reward_poin;
            if( $count_left >= $reward_poin && $count_right >= $reward_poin ){
                $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 13));
                if( !$reward ){
                    $data_reward        = array(
                        'id_member'     => $memberdata->id,
                        'type'          => 13,
                        'datecreated'   => $curdate,
                        'datemodified'  => $curdate,
                    );
                    if( $debug ){
                        $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                    }else{
                        $CI->Model_Member->save_data_reward($data_reward);
                        if($send_notification) $CI->erp_sms->sms_reward_autoglobal($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                    }
                }
            }
            
            // Reward Uang Tunai Rp 500 Jt
            $reward_opt                 = erp_rewards_option(14);
            $reward_poin                = $reward_opt->reward_poin;
            if( $count_left >= $reward_poin && $count_right >= $reward_poin ){
                $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 14));
                if( !$reward && $memberdata->package == PACKAGE_RUBY ){
                    $data_reward        = array(
                        'id_member'     => $memberdata->id,
                        'type'          => 14,
                        'datecreated'   => $curdate,
                        'datemodified'  => $curdate,
                    );
                    if( $debug ){
                        $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                    }else{
                        $CI->Model_Member->save_data_reward($data_reward);
                        if($send_notification) $CI->erp_sms->sms_reward_autoglobal($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                    }
                }
            }
            
            // Reward Uang Tunai Rp 1 Milyar
            $reward_opt                 = erp_rewards_option(15);
            $reward_poin                = $reward_opt->reward_poin;
            if( $count_left >= $reward_poin && $count_right >= $reward_poin ){
                $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 15));
                if( !$reward && $memberdata->package == PACKAGE_RUBY ){
                    $data_reward        = array(
                        'id_member'     => $memberdata->id,
                        'type'          => 15,
                        'datecreated'   => $curdate,
                        'datemodified'  => $curdate,
                    );
                    if( $debug ){
                        $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                    }else{
                        $CI->Model_Member->save_data_reward($data_reward);
                        if($send_notification) $CI->erp_sms->sms_reward_autoglobal($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                    }
                }
            }
            
            // Reward Uang Tunai Rp 2 Milyar
            $reward_opt                 = erp_rewards_option(16);
            $reward_poin                = $reward_opt->reward_poin;
            if( $count_left >= $reward_poin && $count_right >= $reward_poin ){
                $reward                 = $CI->Model_Member->get_member_reward($memberdata->id, array('type' => 16));
                if( !$reward && $memberdata->package == PACKAGE_DIAMOND ){
                    $data_reward        = array(
                        'id_member'     => $memberdata->id,
                        'type'          => 15,
                        'datecreated'   => $curdate,
                        'datemodified'  => $curdate,
                    );
                    if( $debug ){
                        $rewardtext     = 'Reward ' . $reward_opt->reward_name;
                    }else{
                        $CI->Model_Member->save_data_reward($data_reward);
                        if($send_notification) $CI->erp_sms->sms_reward_autoglobal($memberdata->phone, $memberdata->username, $reward_opt->reward_name);
                    }
                }
            }
            
            if( $debug ){
                echo 'Username : '.$memberdata->username . ' = ';
                echo !empty($rewardtext) ? $rewardtext . br() : 'Anda belum mendapatkan reward. Tingkatkan terus Jaringan Anda!' . br();
            }
        }
        
        /**
		 * We count the ancestry first since although this member has no pair, the ancestry may have
		 */
		if ($count_ancestry) {
			// check if ancestry available for this member
			if ($ancestry = erp_ancestry($id_member)) {
				// ancestry is returned in coma delimited
				$ancestry = explode(',', $ancestry);
                if( !empty($ancestry) ){
                    foreach($ancestry as $id_ancestry) {
    					$id_ancestry = absint($id_ancestry);
                        if ( $id_ancestry == 1 ) continue; 
    					erp_reward_auto_global($id_ancestry, FALSE, FALSE, TRUE);
    				}
                }
			}
		}
    }
}

if ( !function_exists('erp_get_member_gen_sponsor') ) 
{
    /**
     * Get member generation by sponsor
     * 
     * Returns the downline of members based on sponsorship.
     * 
     * @since 1.0.0
     * @access public
     * 
     * @param int $id_member
     * @param int $total_gen (optional)
     * @return array of member object
     * 
     * @author Iqbal
     */
	function erp_get_member_gen_sponsor($id_member, $total_gen=0) {
		$id_member = absint($id_member);
		if (empty($id_member)) return false;
		
		$total_gen = absint($total_gen);
		if (empty($total_gen)) return false;
		
		$CI =& get_instance();
		
		$result       = array();
		$gen          = 0;
		$id_members   = array($id_member);
		
		while($gen < $total_gen) {
			if (!$members = erp_sponsored_by($id_members)) break;
			
			$result[$gen] = $members;
			$gen++;
			
			// renewing the id_members
			$id_members = array();
			foreach($members as $member) $id_members[] = $member->id;
			
			unset($members);
		}
		
		// array of member object
		return $result;
	}
}

if ( !function_exists('erp_sponsored_by') )
{
    /**
     * Get member sponsored by array of member ID
     * 
     * @since 1.0.0
     * @access public
     * @see model member
     * 
     * @param array $id_members
     * @return array 
     * 
     * @author Iqbal
     */
    function erp_sponsored_by($id_members) {
        $CI =& get_instance();
        return $CI->Model_Member->get_sponsored_by($id_members);
    }
}

if ( !function_exists('erp_check_autoglobal_reward') )
{
    /**
     * Check sponsor autoglobal reward
     * 
     * @since 1.0.0
     * @access public
     * @see model member
     * 
     * @param array $id_member
     * @param boolean $debug
     * @return array 
     * 
     * @author Iqbal
     */
    function erp_check_autoglobal_reward($id_member, $debug=false) {
        $id_member = absint($id_member);
		if (empty($id_member)) return false;
		
		$CI =& get_instance();
        
        $memberdata = erp_get_memberdata_by_id($id_member);
        if( !$memberdata ) return false;
        
        // Get Reward data by id member
        $datetime   = date('Y-m-d H:i:s');
        $agr_opt    = '9,10,11,12,13,14,15,16';
        $reward     = $CI->Model_Member->get_all_member_reward(0, 0, ' WHERE %id_member% = '.$id_member.' AND %status% = 0 AND %type% IN('.$agr_opt.')');
        if( empty($reward) || !$reward ) return false;
        
        $qualified              = FALSE;
        $expired                = FALSE;
        
        if($memberdata->package != PACKAGE_CASH_REWARD){
            foreach($reward as $row){
                $type               = absint($row->type);
                $date               = $row->datecreated;
                $date_expired       = strtotime($date);
                $date_expired       = date('Y-m-d H:i:s', strtotime('+1 month', $date_expired));
                $sponsored          = $CI->Model_Member->count_sponsored($row->id_member);
                
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
                    $datareward         = array('status' => 2, 'datemodified' => $datetime);
                    $CI->Model_Member->update_data_reward($row->id, $datareward);
                }else{
                    if( $qualified ){
                        $datareward     = array('qualified' => 1, 'datemodified' => $datetime);
                        $CI->Model_Member->update_data_reward($row->id, $datareward);
                    }
                }
                return true;
            }     
        }
        
           
        
    }
}

if (!function_exists('erp_calc_tax')){
    /**
     * 
     * Tax Calculate Process
     * @param   Int     $nominal    Member ID
     * @param   String  $npwp       NPWP Number
     * @return Mixed, Boolean false if invalid nominal, otherwise response of nominal available
     */
    function erp_calc_tax( $nominal, $npwp = '' ) {
    	$result = 0;
    	
    	if ( empty( $nominal ) )
    		return $result;
    	
        $cfg    = config_item( 'taxes' );
        $npwp   = trim( $npwp );
        $tax    = $cfg['non_npwp'];
    	
    	if ( ! empty( $npwp ) ) $tax = $cfg['npwp'];
    	
    	return round( $tax / 100 * $nominal );
    }
}

if (!function_exists('erp_grade')){
    /**
     * 
     * Member Grade
     * @param   Int     $id_member      Member ID
     * @return Mixed, Boolean false if invalid id member, otherwise response of id member available
     */
    function erp_grade( $id_member ) {
        if ( !is_numeric($id_member) ) return false;

        $id_member      = absint($id_member);
        if ( !$id_member ) return false;
        
        $CI =& get_instance();
        
        $memberdata     = erp_get_memberdata_by_id($id_member);
        if ( !$memberdata ) return false;

        $bonus_total    = $CI->Model_Member->get_all_my_bonus_total($id_member);
        $bonus_total    = $bonus_total->total;
        $grade          = '';

        if( $bonus_total >= 10000000 )      { $grade = GRADE_STARONE; }
        if( $bonus_total >= 30000000 )      { $grade = GRADE_STARTWO; }
        if( $bonus_total >= 50000000 )      { $grade = GRADE_STARTHREE; }
        if( $bonus_total >= 100000000 )     { $grade = GRADE_STARFOUR; }
        if( $bonus_total >= 300000000 )     { $grade = GRADE_STARFIVE; }
        if( $bonus_total >= 500000000 )     { $grade = GRADE_CROWN; }
        if( $bonus_total >= 1000000000 )    { $grade = GRADE_ELITE_CROWN; }
        if( $bonus_total >= 3000000000 )    { $grade = GRADE_PRESIDENTIAL; }
        
        return $grade;
    }
}


if ( !function_exists('erp_logout') ) {
    /**
     * Logout
     * @author	Iqbal
     */
	function erp_logout() 
    {
		$CI =& get_instance();
		
		if ( $CI->session->userdata( 'member_logged_in' ) ) {
            $CI->session->unset_userdata( 'member_logged_in' );
            $CI->session->sess_destroy();
        }
        
        jl_clear_auth_cookie();
	}
}

/*
CHANGELOG
---------
Insert new changelog at the top of the list.
-----------------------------------------------
Version	YYYY/MM/DD  Person Name		Description
-----------------------------------------------
1.0.0   2014/10/20  Iqbal           - Generate helper
*/