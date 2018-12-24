<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BKEV Global Network Member class.
 *
 * @class bgn_member
 * @author Iqbal
 */
class Erp_Member
{
	var $CI;
	var $data;
	var $id_member = 0;

	/**
	 * Constructor - Sets up the object properties.
	 */
	function __construct()
	{
		$this->CI =& get_instance();
	}

	/**
	 * Sets up the object properties.
	 *
	 * Retrieves the memberdata and then assigns all of the data keys to direct
	 * properties of the object.
	 *
	 * @param int $id      Optional    Member's ID
	 * @param int $email   Optional    Member's Email
	 * @return object
	 */
	function member($id=0, $email='')
	{
		if (empty($id) && empty($email))
		{
			return false;
		}
        
        if (!is_numeric($id))
		{
            $email  = $id;
            $id     = 0;
		}
		
		if (!empty($id))
		{
			$this->data = $this->CI->Model_Member->get_memberdata($id);
		}
        else
		{
			$this->data = $this->CI->Model_Member->get_member_by('email', $email);
		}
	
		if (!isset($this->data->id) || empty($this->data->id))
		{
			return false;
		}
		
		$this->id_member = $this->data->id;

		return $this->data;
	}
}