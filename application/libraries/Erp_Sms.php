<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BKEV Global Network SMS class.
 *
 * @class bgn_sms
 * @author Iqbal
 */
class Erp_Sms 
{
	var $CI;
    var $username;
    var $password;
    var $url;
	var $active;
    
	/**
	 * Constructor - Sets up the object properties.
	 */
	function __construct()
    {
        // Set Get CI Instance
        $this->CI       =& get_instance();
        // Set Username
        $this->username = trim(config_item('sms_username'));
        // Set Password
        $this->password = trim(config_item('sms_password'));
        // Set SMS URL
        $this->url      = trim(config_item('sms_url'));
		// Set if service is active
		$this->active	= config_item('sms_active');
	}
    
    /**
	 * Send SMS function.
	 *
     * @param string    $to         (Required)  To SMS destination
     * @param string    $message    (Required)  Message of SMS
	 * @return Mixed
	 */
	function send_sms($to, $message){
		if ( !$this->active ) return false;
        if ( !is_numeric($to) ) return false;
       
        $to = substr($to,1);
        $to = '62' . $to;

        $postfield  = 'username='.$this->username.'&password='.$this->password.'&hp='.$to.'&message='.urlencode($message).'';
        $request    = $this->url . $postfield;
        
        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_URL, $request);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        
        $results    = curl_exec($curlHandle);
        curl_close($curlHandle);
		return $results;
	}
    
    /**
	 * Send SMS New Member function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $password   (Required)  Password of New Member
     * @param string    $username   (Required)  Member ID of New Member
	 * @return Mixed
	 */
    function sms_newmember($to, $password, $username){
        if ( !$to ) return false;
        if ( !$password ) return false;
        if ( !$username ) return false;
        
        $message    = trim(get_option('sms_format_new_member'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%password%",     $password, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS New Member Sponsor function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $name       (Required)  Name of New Member
     * @param string    $username   (Required)  Member ID of New Member
	 * @return Mixed
	 */
    function sms_newmember_spon($to, $name, $username){
        if ( !$to ) return false;
        if ( !$name ) return false;
        if ( !$username ) return false;
        
        $message    = trim(get_option('sms_format_new_member_sponsor'));
        $message    = str_replace("%name%",         $name, $message);
        $message    = str_replace("%username%",     $username, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS New Member from Replica Site function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $name       (Required)  Name of New Member
     * @param string    $nominal    (Required)  Nominal Transfer for New Member
	 * @return Mixed
	 */
    function sms_newmember_rep($to, $name, $nominal){
        if ( !$to ) return false;
        if ( !$name ) return false;
        if ( !$nominal ) return false;
        
        $message    = trim(get_option('sds_format_new_member_rep'));
        $message    = str_replace("%name%",         $name, $message);
        $message    = str_replace("%nominal%",      $nominal, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS New Member Sponsor from Replica Site function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $name       (Required)  Name of New Member
     * @param string    $phone      (Required)  Phone of New Member
	 * @return Mixed
	 */
    function sms_newmember_rep_spon($to, $name, $phone){
        if ( !$to ) return false;
        if ( !$name ) return false;
        if ( !$phone ) return false;
        
        $message    = trim(get_option('sds_format_new_member_rep_sponsor'));
        $message    = str_replace("%name%",     $name, $message);
        $message    = str_replace("%phone%",    $phone, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS Bonus function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $username   (Required)  Member ID of New Member
     * @param string    $nominal    (Required)  Nominal of Bonus
     * @param string    $node       (Required)  Node of Member
	 * @return Mixed
	 */
    function sms_bonus($to, $username, $nominal, $node){
        if ( !$to ) return false;
        if ( !$username ) return false;
        if ( !$nominal ) return false;
        if ( !$node ) return false;
        
        $message    = trim(get_option('sms_format_bonus'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%nominal%",      $nominal, $message);
        $message    = str_replace("%node%",         $node, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS Bonus function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $username   (Required)  Member ID of New Member
     * @param string    $nominal    (Required)  Nominal of Bonus
     * @param string    $node       (Required)  Node of Member
	 * @return Mixed
	 */
    function sms_bonus_poin_atm($to, $username, $poin, $node){
        if ( !$to ) return false;
        if ( !$username ) return false;
        if ( !$poin ) return false;
        if ( !$node ) return false;
        
        $message    = trim(get_option('sms_format_bonus_poin_atm'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%poin%",      	$poin, $message);
        $message    = str_replace("%node%",         $node, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
	
	/**
	 * Send SMS Bonus function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $username   (Required)  Member ID of New Member
     * @param string    $nominal    (Required)  Nominal of Bonus
     * @param string    $node       (Required)  Node of Member
	 * @return Mixed
	 */
    function sms_bonus_poin_ro($to, $username, $poin, $node){
        if ( !$to ) return false;
        if ( !$username ) return false;
        if ( !$poin ) return false;
        if ( !$node ) return false;
        
        $message    = trim(get_option('sms_format_bonus_poin_ro'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%poin%",      	$poin, $message);
        $message    = str_replace("%node%",         $node, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS Withdrawal function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $username   (Required)  Member ID of New Member
     * @param string    $nominal    (Required)  Nominal of Withdrawal
     * @param string    $bank       (Required)  Bank of Member
     * @param string    $bill       (Required)  Bank Bill of Member
     * @param string    $name       (Required)  Name of Member
	 * @return Mixed
	 */
    function sms_withdrawal($to, $username, $nominal, $bank, $bill, $name){
        if ( !$to ) return false;
        if ( !$username ) return false;
        if ( !$nominal ) return false;
        if ( !$bank ) return false;
        if ( !$bill ) return false;
        if ( !$name ) return false;
        
        $message    = trim(get_option('sms_format_withdrawal'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%nominal%",      $nominal, $message);
        $message    = str_replace("%bank%",         $bank, $message);
        $message    = str_replace("%bill%",         $bill, $message);
        $message    = str_replace("%name%",         $name, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS Withdrawal function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $username   (Required)  Member ID of New Member
     * @param string    $nominal    (Required)  Nominal of Withdrawal
     * @param string    $saldo      (Required)  Saldo of Member
	 * @return Mixed
	 */
    function sms_withdrawal_transfer($to, $username, $nominal){
        if ( !$to ) return false;
        if ( !$username ) return false;
        if ( !$nominal ) return false;
        
        $message    = trim(get_option('sms_format_withdrawal_all'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%nominal%",      $nominal, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS Reward function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $username   (Required)  Member ID of New Member
     * @param string    $reward     (Required)  Reward of Member
	 * @return Mixed
	 */
    function sms_reward($to, $username, $reward){
        if ( !$to ) return false;
        if ( !$username ) return false;
        if ( !$reward ) return false;
        
        $message    = trim(get_option('sms_format_reward'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%reward%",       $reward, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS Reward Auto Global function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $username   (Required)  Member ID of New Member
     * @param string    $reward     (Required)  Reward of Member
	 * @return Mixed
	 */
    function sms_reward_autoglobal($to, $username, $reward){
        if ( !$to ) return false;
        if ( !$username ) return false;
        if ( !$reward ) return false;
        
        $message    = trim(get_option('sms_format_reward_autoglobal'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%reward%",       $reward, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS Change Password function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $username   (Required)  Member ID of New Member
     * @param string    $password   (Required)  Password of Member
	 * @return Mixed
	 */
    function sms_cpassword($to, $username, $password){
        if ( !$to ) return false;
        if ( !$username ) return false;
        if ( !$password ) return false;
        
        $message    = trim(get_option('sms_format_cpassword'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%password%",     $password, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS Reset Password function.
	 *
     * @param string    $to         (Required)  Data of New Member
     * @param string    $username   (Required)  Username of Member
     * @param string    $password   (Required)  Password of Member
	 * @return Mixed
	 */
    function sms_respassword($to, $username, $password){
        if ( !$to ) return false;
        if ( !$username ) return false;
        if ( !$password ) return false;
        
        $message    = trim(get_option('sms_format_respassword'));
        $message    = str_replace("%username%",     $username, $message);
        $message    = str_replace("%password%",     $password, $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
    
    /**
	 * Send SMS Qualified Auto-CBI function.
	 *
     * @param string    $to         (Required)  Destination phone number
     * @param string    $data       (Required)  Data of Qualified Auto-CBI
	 * @return Mixed
	 */
    function sms_autocbi($to, $data){
        if ( !$to ) return false;
        if ( !$data ) return false;
        
        $message    = trim(get_option('sms_format_qualified_autocbi'));
        $message    = str_replace("%username%",     bgn_isset($data->username,''), $message);
        $message    = str_replace("%cbi_before%",   bgn_isset($data->cbi_before,''), $message);
        $message    = str_replace("%cbi_after%",    bgn_isset($data->cbi_after,''), $message);
        $message    = str_replace("%nominal%",      bgn_isset($data->nominal,''), $message);
        $send       = $this->send_sms($to, $message);
        return true;
    }
}

/*
CHANGELOG
---------
Insert new changelog at the top of the list.
-----------------------------------------------
Version	YYYY/MM/DD  Person Name		Description
-----------------------------------------------
1.0.0   2014/12/12  Iqbal           - Created this changelog
*/