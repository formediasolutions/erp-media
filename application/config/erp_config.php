<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This is additional config settings
 * Please only add additional config here
 * 
 * @author	Iqbal
 */

/**
 * Coming soon
 */
$config['coming_soon']          = FALSE;

/**
 * Maintenance
 */
$config['maintenance']          = FALSE;

/**
 * Month
 */
$config['month']                = array(
	1  => 'Januari',
	2  => 'Februari',
	3  => 'Maret',
	4  => 'April',
	5  => 'Mei',
	6  => 'Juni',
	7  => 'Juli',
	8  => 'Agustus',
	9  => 'September',
	10 => 'Oktober',
	11 => 'Nopember',
	12 => 'Desember',
);

/**
 * Captcha
 */
$config['captcha_site_key']         = '6Lc6PicTAAAAAKSU3GvdUWvcTDUxgwZkwby-sS4v';
$config['captcha_secret_key']       = '6Lc6PicTAAAAAKLgYJLwjR6aVh0GgpC5t_fhpCpX';

/**
 * Currency
 */
$config['currency']                 = 'Rp';

/**
 * Status
 */
$config['status']             = array(
    NONACTIVE                       => 'Belum Aktif',
    ACTIVE                          => 'Aktif',
);

/**
 * SMS config
 */
$config['sms_active']               = TRUE;
$config['sms_username']             = 'SDS';
$config['sms_password']             = 'bkev22';
$config['sms_url']                  = 'http://103.16.199.187/masking/send.php?';

/**
 * Email config
 */
$config['email_active']             = TRUE;
$config['mailserver_host']		    = '';
$config['mailserver_username'] 	    = '';
$config['mailserver_password'] 	    = '';

// Taxes Config
$config['taxes']    = array(
    'npwp'          => 2.5,	// 2.5% for npwp members
    'non_npwp'      => 3 	// 3% for non npwp members
);

/**
 * Lost Permission
 */
$config['ip_lost_permission']       = array(
    '127.0.0.1',
    '202.62.17.244'
);

/* End of file bgn_config.php */
/* Location: ./application/config/bgn_config.php */