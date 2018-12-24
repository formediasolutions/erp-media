<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BKEV Global Network Email class.
 *
 * @class bgn_email
 * @author Iqbal
 */
class Erp_Email 
{
	var $CI;
    var $active;
    
	/**
	 * Constructor - Sets up the object properties.
	 */
	function __construct()
    {
        $this->CI       =& get_instance();
        $this->active	= config_item('email_active');
        
        require_once SWIFT_MAILSERVER;
	}
	
    /**
	 * Send email function.
	 *
     * @param string    $to         (Required)  To email destination
     * @param string    $subject    (Required)  Subject of email
     * @param string    $message    (Required)  Message of email
     * @param string    $from       (Optional)  From email
     * @param string    $from_name  (Optional)  From name email
	 * @return Mixed
	 */
	function send($to, $subject, $message, $from = '', $from_name = ''){
        if (!$this->active) return false;
       
		$transport = false;	
		if ($mailserver_host = get_option('mailserver_host')) {
			$transport = Swift_SmtpTransport::newInstance($mailserver_host, 25)
	    		->setUsername(get_option('mailserver_username'))
	     		->setPassword(get_option('mailserver_password'));
		}
		
		try{
            //Create the Transport
            if(!$transport) $transport  = Swift_MailTransport::newInstance();
			else $transport  = Swift_MailTransport::newInstance($transport);
            //Create the message
            $mail       = Swift_Message::newInstance();
            //Give the message a subject
            $mail->setSubject($subject)
                 ->setFrom(array($from => $from_name))
                 ->setTo($to)
                 ->setBody($message->plain)
                 ->addPart($message->html, 'text/html');
			
	        //Create the Mailer using your created Transport
	        $mailer     = Swift_Mailer::newInstance($transport);
	        //Send the message
	        $result     = $mailer->send($mail);	
	        
			return $result;
		}catch (Exception $e){
			// should be database log in here
			return $e->getMessage(); // 'failed to gather MAILDATA';
		}

		return false;
	}
    
    /**
	 * Send email to downline function.
	 *
     * @param   Object  $member     (Required)  Member Data of Downline
     * @param   Object  $sponsor    (Required)  Member Data of Sponsor
     * @param   Object  $password   (Required)  Password of Downline
	 * @return  Mixed
	 */
    function send_email_downline($member, $sponsor, $password, $debug=false){
        $invest             = absint(get_option('investment'));
        $email              = trim($member->email);
        
        if( $member->status == 1 ){
            $plain_down     = get_option('send_email_down_active');
            $html_down      = get_option('send_email_down_active_html');
        }else{
            $plain_down     = get_option('send_email_down_nonactive');
            $html_down      = get_option('send_email_down_nonactive_html');
        }
        
        $plain_down         = str_replace("%username%",             $member->username, $plain_down);
        $plain_down         = str_replace("%password%",             $password, $plain_down);
        $plain_down         = str_replace("%sponsor_username%",     $sponsor->username, $plain_down);
        $plain_down         = str_replace("%sponsor_email%",        $sponsor->email, $plain_down);
        $plain_down         = str_replace("%sponsor_phone%",        $sponsor->phone, $plain_down);
        $plain_down         = str_replace("%nominal%",              number_format( $invest, 0, ",", "." ), $plain_down);
        $plain_down         = str_replace("%login_url%",            base_url('login'), $plain_down);
        
        $html_down          = str_replace("%username%",             $member->username, $html_down);
        $html_down          = str_replace("%password%",             $password, $html_down);
        $html_down          = str_replace("%sponsor_username%",     $sponsor->username, $html_down);
        $html_down          = str_replace("%sponsor_email%",        $sponsor->email, $html_down);
        $html_down          = str_replace("%sponsor_phone%",        $sponsor->phone, $html_down);
        $html_down          = str_replace("%nominal%",              number_format( $invest, 0, ",", "." ), $html_down);
        $html_down          = str_replace("%login_url%",            base_url('login'), $html_down);
        
        $message            = new stdClass();
        $message->plain     = $plain_down;
        $message->html      = $html_down;
        
        $send               = $this->send($email, 'Informasi Pendaftaran', $message, get_option('mail_sender_admin'), 'Admin ' . get_option('company_name'));
        
        if( $debug ){
            print_r($send);
        }else{
            return $send;
        }
    }
    
    /**
	 * Send email to sponsor function.
	 *
     * @param   Object  $member     (Required)  Member Data of Downline
     * @param   Object  $sponsor    (Required)  Member Data of Sponsor
     * @param   Object  $password   (Required)  Password of Downline
	 * @return  Mixed
	 */
    function send_email_sponsor($member, $sponsor, $password, $debug=false){
        $email              = trim($sponsor->email);
        
        // Sponsor Email
        $plain_spon         = get_option('send_email_sponsor');
        $html_spon          = get_option('send_email_sponsor_html');
        
        $plain_spon         = str_replace("%username%",         $member->username, $plain_spon);
        $plain_spon         = str_replace("%name%",             $member->name, $plain_spon);
        $plain_spon         = str_replace("%email%",            $member->email, $plain_spon);
        $plain_spon         = str_replace("%phone%",            $member->phone, $plain_spon);
        
        $html_spon          = str_replace("%username%",         $member->username, $html_spon);
        $html_spon          = str_replace("%name%",             $member->name, $html_spon);
        $html_spon          = str_replace("%email%",            $member->email, $html_spon);
        $html_spon          = str_replace("%phone%",            $member->phone, $html_spon);
        
        $message            = new stdClass();
        $message->plain     = $plain_spon;
        $message->html      = $html_spon;

        $send_to_sponsor    = $this->send($email, 'Informasi Pendaftaran', $message, get_option('mail_sender_admin'), 'Admin ' . get_option('company_name'));
        
        if( $debug ){
            print_r($send_to_sponsor);
        }else{
            if($send_to_sponsor){
                return true;
            }
        }
		
        return false;
    }
    
    /**
	 * Send email to sponsor replika function.
	 *
     * @param   Object  $member     (Required)  Member Data of Downline
     * @param   Object  $sponsor    (Required)  Member Data of Sponsor
     * @param   Object  $password   (Required)  Password of Downline
	 * @return  Mixed
	 */
    function send_email_sponsor_replika($member, $sponsor, $password, $debug=false){
        $email              = trim($sponsor->email);
        
        // Sponsor Email
        $plain_spon         = get_option('send_email_sponsor_replika');
        $html_spon          = get_option('send_email_sponsor_replika_html');
        
        $plain_spon         = str_replace("%username%",         $member->username, $plain_spon);
        $plain_spon         = str_replace("%name%",             $member->name, $plain_spon);
        $plain_spon         = str_replace("%email%",            $member->email, $plain_spon);
        $plain_spon         = str_replace("%phone%",            $member->phone, $plain_spon);
        
        $html_spon          = str_replace("%username%",         $member->username, $html_spon);
        $html_spon          = str_replace("%name%",             $member->name, $html_spon);
        $html_spon          = str_replace("%email%",            $member->email, $html_spon);
        $html_spon          = str_replace("%phone%",            $member->phone, $html_spon);
        
        $message            = new stdClass();
        $message->plain     = $plain_spon;
        $message->html      = $html_spon;

        $send_to_sponsor    = $this->send($email, 'Informasi Pendaftaran', $message, get_option('mail_sender_admin'), 'Admin ' . get_option('company_name'));
        
        if( $debug ){
            print_r($send_to_sponsor);
        }else{
            if($send_to_sponsor){
                return true;
            }
        }
		
        return false;
    }
    
    /**
	 * Send email contact function.
	 *
     * @param   Object  $data       (Required)  Data of Contact Form
	 * @return  Mixed
	 */
    function send_email_contact($data){ 
        if ( !$data ) return false;
        
        $pesan_plain    = "\n" . 
        	'Nama        : '.$data->contact_nama."\n".
        	'Email       : '.$data->contact_email."\n".
			'Alamat      : '.$data->contact_alamat."\n\n".
			'Pesan       : '.$data->contact_pesan."\n";

        $pesan_html     = '
        <table style="width: 60%; font: 12px Arial; margin: 0 auto; border: 1px solid #0751AD;">
            <thead>
                <tr>
                    <th colspan="3" style="text-align: center; text-transform: uppercase; font-weight: bold; color: #FFFFFF; background: #0751AD; padding: 5px;">Kontak Pesan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 28%; padding: 5px; ">Nama</td>
                    <td style="width: 2%; padding: 5px; text-align: center:">:</td>
                    <td style="width: 70%; padding: 5px;">'.$data->contact_nama.'</td>
                </tr>
                <tr>
                    <td style="width: 28%; padding: 5px;">Email</td>
                    <td style="width: 5%; padding: 5px; text-align: center:">:</td>
                    <td style="width: 70%; padding: 5px;">'.$data->contact_email.'</td>
                </tr>
                <tr>
                    <td style="width: 28%; padding: 5px;">Alamat</td>
                    <td style="width: 2%; padding: 5px; text-align: center:">:</td>
                    <td style="width: 70%; padding: 5px;">'.$data->contact_alamat.'</td>
                </tr>
                <tr>
                    <td style="width: 28%; padding: 5px;">Pesan</td>
                    <td style="width: 2%; padding: 5px; text-align: center:">:</td>
                    <td style="width: 70%; padding: 5px;">'.$data->contact_pesan.'</td>
                </tr>
            </tbody>
        </table>';

        $message            = new stdClass();
        $message->plain     = $pesan_plain;
        $message->html      = $pesan_html;

        $send_contact       = $this->send(get_option('mail_sender_admin'), 'Kontak Pesan', $message, trim($data->contact_email), trim($data->contact_nama) );
        
        if($send_contact){
            return true;
        }
		
        return false;
    }
    
    /**
	 * Send email contact function.
	 *
     * @param   Object  $data       (Required)  Data of Contact Form
	 * @return  Mixed
	 */
    function send_email_reset_password($data){ 
        if ( !$data ) return false;
        
        $pesan_plain        = "\n" .
        'Informasi Reset Password'."\n" .
        '-------------------------------------------------'."\n\n" .
        'Password username Anda : '.$data->username.' sudah berhasil di reset. Silahkan login dengan menggunakan password '.$data->newpass.''."\n\n" .
        '-------------------------------------------------'."\n\n" .
        'Salam Sukses,'."\n" .
        'Manajemen BKEV Global Network';

        $pesan_html         = '
        <div style="width: 80%; text-align: center; margin: 0 auto 20px auto;"><img src="http://bkev-globalnetwork.com/assets/img/logo_small.png" /></div>
        <div style="width: 80%; border: 2px solid #FCB322; padding: 0; margin: 0 auto;">
            <div style="background-color: #FCB322; padding: 5px; color: #FFF; text-align: center; font: bold 13px Arial;">Informasi Reset Password</div>
            
            <div style="padding 10px; color: #666666; font: 12px/20px Arial;">
            <p style="padding: 0 10px;">Password username Anda : <strong>'.$data->username.'</strong> sudah berhasil di reset. Silahkan login dengan menggunakan password <strong>'.$data->newpass.'</strong></p>
            
            <p style="padding: 10px 10px 0 10px; color: #888888; font-size: 11px;">-------------------------------------------------<br />
            Salam Sukses,<br />
            Manajemen BKEV Global Network</p>
            
            <p style="text-align: center; margin: 15px 0 0 0; font: 10px Arial; color: #888888; border-top: 1px solid #EEE; padding: 15px 0; background-color: #F7F7F7;">Copyright &copy; 2016. BKEV Global Network</p>
            </div>
        </div>';

        $message            = new stdClass();
        $message->plain     = $pesan_plain;
        $message->html      = $pesan_html;

        $send_resetpass     = $this->send($data->email, 'Konfirmasi Reset Password', $message, trim(get_option('mail_sender_admin')), COMPANY_NAME );
        
        if($send_resetpass){
            return true;
        }
        return false;
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