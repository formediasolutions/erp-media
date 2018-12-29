<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ERP MEDIA class.
 *
 * @class phpgriflte
 * @author Rifal
 */
class PHPGridLite 
{
	var $CI;
    var $active;
    
	/**
	 * Constructor - Sets up the object properties.
	 */
	function __construct()
    {
        $this->CI       =& get_instance();
        //$this->active	= config_item('email_active');
		
        require_once PHPGRID_LITESERVER;
	}
}
?>