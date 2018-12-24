<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Member extends CI_Model{
    /**
     * Initialize table and primary field variable
     */
    var $member                 = "adm_member";
    var $member_confirm         = "adm_member_confirm";
    var $member_tree            = "adm_member_tree";
    var $adm_module             = "adm_module";
    var $adm_menu               = "adm_menu";
    var $adm_group              = "adm_group";
    var $adm_group_menu         = "adm_group_menu";
    var $adm_company            = "adm_company";
    var $adm_log                = "adm_log";
    var $adm_option             = "adm_option";
    
    // Primary Fields
    var $id                     = "id";
    var $parent                 = "parent";
    
    /**
	* Constructor - Sets up the object properties.
	*/
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Sign In
     * 
     * Authenticate member and drop login cookie if member is valid.
     * 
     * @author  Iqbal
     * @param   Array   $credential     (Optional)  Associative array of member credential. It contains member_email, member_password, and remember
     * @return  Mixed   False on invalid member, otherwise object of member.
     */
	function signon($credentials)
	{
		if ( empty($credentials) || !is_array($credentials) ) return false;
        
        if ( !empty($credentials['remember']) ) 
			$credentials['remember'] = true;
		else
			$credentials['remember'] = false;

		$member = $this->authenticate( $credentials['username'], $credentials['password'] );
        
		if ( empty($member) ) 
			return false;
        
        if(!empty($member->id)) erp_set_auth_cookie( $member->id, $credentials['remember'], '' );
		
		return $member;
	}
    
    /**
     * Authenticate member
     * 
     * @author  Iqbal
     * @param   String  $username       (Required)  Member UID
     * @param   String  $password       (Required)  Password
     * @return  Mixed   False on invalid member, otherwise object of member.
     */
	function authenticate( $username, $password )
	{		
		$username     = trim($username);
		$password     = trim($password);
        
		if ( empty($username) || empty($password) )
			return false;
            
        $memberdata = $this->get_member_by('login', $username);
        
        if( !$memberdata )
            return false;

		if( $memberdata && $memberdata->status == 0 )
			return 'not_active';
            
        if( $memberdata && $memberdata->status == 2 )
			return 'banned';
        
        if( $memberdata && $memberdata->status == 3 )
            return 'deleted';
            
        // For Assume Function
        if ($password != md5('erp_assume_')){
            // Check password        
            if ( $password != $memberdata->password )
                return false;
        }
        
		return $memberdata;
	}
    
    /**
     * Retrieve all member data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member list
     */
    function get_all($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id%",                       "id", $conditions);
            $conditions = str_replace("%type%",                     "type", $conditions);
            $conditions = str_replace("%status%",                   "status", $conditions);
            $conditions = str_replace("%username%",                 "username", $conditions);
            $conditions = str_replace("%name%",                     "name", $conditions);
            $conditions = str_replace("%position%",                 "position", $conditions);
            $conditions = str_replace("%datecreated%",              "datecreated", $conditions);
            $conditions = str_replace("%as_stockist%",              "as_stockist", $conditions);
            $conditions = str_replace("%city%",                     "city",  $conditions);
            $conditions = str_replace("%package%",                  "package",  $conditions);
			$conditions = str_replace("%autocbi_qualified%",	    "autocbi_qualified", $conditions);
            $conditions = str_replace("%autocbi_board%",            "autocbi_board", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id%",                       "id", $order_by);
            $order_by   = str_replace("%username%",                 "username",  $order_by);
            $order_by   = str_replace("%name%",                     "name",  $order_by);
            $order_by   = str_replace("%position%",                 "position",  $order_by);
            $order_by   = str_replace("%datecreated%",              "datecreated",  $order_by);
            $order_by   = str_replace("%as_stockist%",              "as_stockist",  $order_by);
            $order_by   = str_replace("%city%",                     "city",  $order_by);
            $order_by   = str_replace("%package%",                  "package",  $order_by);
            $order_by   = str_replace("%autocbi_qualified%",        "autocbi_qualified", $order_by);
            $order_by   = str_replace("%autocbi_qualified_date%",   "autocbi_qualified_date", $order_by);
            $order_by   = str_replace("%autocbi_board%",            "autocbi_board", $order_by);
        }
        
        $sql = 'SELECT * FROM ' . $this->member . '';
        
        if( !empty($conditions) ){ $sql .= $conditions; }
        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all module data
     *
     * @author  Rifal
     * @param   Int     $limit              Limit of user               default 0
     * @param   Int     $offset             Offset ot user              default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of user list
     */
    function get_all_module($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id_adm_module%",        "id_adm_module", $conditions);
            $conditions = str_replace("%folder%",               "folder", $conditions);
            $conditions = str_replace("%name%",                 "name", $conditions);
        }

        if( !empty($order_by) ){
            $order_by   = str_replace("%id_adm_module%",        "id_adm_module", $order_by);
            $order_by   = str_replace("%folder%",               "folder",  $order_by);
            $order_by   = str_replace("%name%",                 "name",  $order_by);
        }

        $sql = 'SELECT * FROM ' . $this->adm_module . '';

        if( !empty($conditions) ){ $sql .= $conditions; }
        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'id_adm_module ASC');

        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;

        return $query->result();
    }
    
    /**
     * Retrieve all menu data
     *
     * @author  Rifal
     * @param   Int     $limit              Limit of user               default 0
     * @param   Int     $offset             Offset ot user              default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of user list
     */
    function get_all_menu($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id_adm_menu%",          "id_adm_menu", $conditions);
            $conditions = str_replace("%folder%",               "folder", $conditions);
            $conditions = str_replace("%name%",                 "name", $conditions);
        }

        if( !empty($order_by) ){
            $order_by   = str_replace("%id_adm_menu%",        "id_adm_menu", $order_by);
            $order_by   = str_replace("%folder%",               "folder",  $order_by);
            $order_by   = str_replace("%name%",                 "name",  $order_by);
        }

        $sql = 'SELECT mn.*, mdl.name AS module_name FROM ' . $this->adm_menu . ' AS mn
                LEFT JOIN ' . $this->adm_module.' AS mdl ON mdl.id_adm_module = mn.id_adm_module
        ';

        if( !empty($conditions) ){ $sql .= $conditions; }
        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'id_adm_menu ASC');

        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;

        return $query->result();
    }
    
    /**
     * Retrieve all auto global data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of auto global        default 0
     * @param   Int     $offset             Offset ot auto global       default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of auto global list
     */
    function get_all_auto_global($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id%",                   "A.id", $conditions);
            $conditions = str_replace("%id_member%",            "A.id_member", $conditions);
            $conditions = str_replace("%parent%",               "A.parent", $conditions);
            $conditions = str_replace("%position%",             "A.position", $conditions);
            $conditions = str_replace("%tree%",                 "A.tree", $conditions);
            $conditions = str_replace("%package%",              "A.package", $conditions);
            $conditions = str_replace("%username%",             "B.username", $conditions);
            $conditions = str_replace("%name%",                 "B.name", $conditions);
            $conditions = str_replace("%datecreated%",          "A.datecreated", $conditions);
            $conditions = str_replace("%datemodified%",         "A.datemodified", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id%",                   "A.id", $order_by);
            $order_by   = str_replace("%id_member%",            "A.id_member", $order_by);
            $order_by   = str_replace("%parent%",               "A.parent", $order_by);
            $order_by   = str_replace("%position%",             "A.position", $order_by);
            $order_by   = str_replace("%tree%",                 "A.tree", $order_by);
            $order_by   = str_replace("%package%",              "A.package", $order_by);
            $order_by   = str_replace("%username%",             "B.username", $order_by);
            $order_by   = str_replace("%name%",                 "B.name", $order_by);
            $order_by   = str_replace("%datecreated%",          "A.datecreated", $order_by);
            $order_by   = str_replace("%datemodified%",         "A.datemodified", $order_by);
        }
        
        $sql = '
            SELECT A.*, B.username, B.name 
            FROM ' . $this->auto_global . ' AS A 
            LEFT JOIN '.$this->member.' AS B 
            ON B.id = A.id_member';
        
        if( !empty($conditions) ){ $sql .= $conditions; }
        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all auto-cbi data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of auto-cbi           default 0
     * @param   Int     $offset             Offset ot auto-cbi          default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of auto-cbi list
     */
    function get_all_autocbi($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id%",                   "id", $conditions);
            $conditions = str_replace("%id_member%",            "id_member", $conditions);
            $conditions = str_replace("%parent%",               "parent", $conditions);
            $conditions = str_replace("%position%",             "position", $conditions);
            $conditions = str_replace("%tree%",                 "tree", $conditions);
            $conditions = str_replace("%package%",              "package", $conditions);
            $conditions = str_replace("%username%",             "username", $conditions);
            $conditions = str_replace("%name%",                 "name", $conditions);
            $conditions = str_replace("%board%",                "board", $conditions);
            $conditions = str_replace("%type%",                 "type", $conditions);
            $conditions = str_replace("%datecreated%",          "datecreated", $conditions);
            $conditions = str_replace("%datemodified%",         "datemodified", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id%",                   "id", $order_by);
            $order_by   = str_replace("%id_member%",            "id_member", $order_by);
            $order_by   = str_replace("%parent%",               "parent", $order_by);
            $order_by   = str_replace("%position%",             "position", $order_by);
            $order_by   = str_replace("%tree%",                 "tree", $order_by);
            $order_by   = str_replace("%package%",              "package", $order_by);
            $order_by   = str_replace("%username%",             "username", $order_by);
            $order_by   = str_replace("%name%",                 "name", $order_by);
            $order_by   = str_replace("%board%",                "board", $order_by);
            $order_by   = str_replace("%type%",                 "type", $order_by);
            $order_by   = str_replace("%datecreated%",          "datecreated", $order_by);
            $order_by   = str_replace("%datemodified%",         "datemodified", $order_by);
        }
        
        $sql = 'SELECT * FROM '.$this->autocbi.' ';
        
        if( !empty($conditions) ){ $sql .= $conditions; }
        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all auto-cbi bonus data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of auto-cbi bonus     default 0
     * @param   Int     $offset             Offset ot auto-cbi bonus    default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of auto-cbi bonus list
     */
    function get_all_autocbi_bonus($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id%",                   "A.id", $conditions);
            $conditions = str_replace("%id_member%",            "A.id_member", $conditions);
            $conditions = str_replace("%username%",             "B.username", $conditions);
            $conditions = str_replace("%name%",                 "B.name", $conditions);
            $conditions = str_replace("%amount%",               "A.amount", $conditions);
            $conditions = str_replace("%datecreated%",          "A.datecreated", $conditions);
            $conditions = str_replace("%datemodified%",         "A.datemodified", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id%",                   "A.id", $order_by);
            $order_by   = str_replace("%id_member%",            "A.id_member", $order_by);
            $order_by   = str_replace("%username%",             "B.username", $order_by);
            $order_by   = str_replace("%name%",                 "B.name", $order_by);
            $order_by   = str_replace("%amount%",               "A.amount", $order_by);
            $order_by   = str_replace("%datecreated%",          "A.datecreated", $order_by);
            $order_by   = str_replace("%datemodified%",         "A.datemodified", $order_by);
        }
        
        $sql = '
        SELECT A.*, B.username, B.name FROM '.$this->autocbi_bonus.' AS A 
        LEFT JOIN '.$this->member.' AS B ON B.id = A.id_member';
        
        if( !empty($conditions) ){ $sql .= $conditions; }
        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all auto-cbi board data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of auto-cbi board     default 0
     * @param   Int     $offset             Offset ot auto-cbi board    default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of auto-cbi board list
     */
    function get_all_autocbi_board($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id%",                   "id", $conditions);
            $conditions = str_replace("%id_member%",            "id_member", $conditions);
            $conditions = str_replace("%username%",             "username", $conditions);
            $conditions = str_replace("%name%",                 "name", $conditions);
            $conditions = str_replace("%process%",              "process", $conditions);
            $conditions = str_replace("%board_before%",         "board_before", $conditions);
            $conditions = str_replace("%board_after%",          "board_after", $conditions);
            $conditions = str_replace("%datecreated%",          "datecreated", $conditions);
            $conditions = str_replace("%datemodified%",         "datemodified", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id%",                   "id", $order_by);
            $order_by   = str_replace("%id_member%",            "id_member", $order_by);
            $order_by   = str_replace("%username%",             "username", $order_by);
            $order_by   = str_replace("%name%",                 "name", $order_by);
            $order_by   = str_replace("%process%",              "process", $order_by);
            $order_by   = str_replace("%board_before%",         "board_before", $order_by);
            $order_by   = str_replace("%board_after%",          "board_after", $order_by);
            $order_by   = str_replace("%datecreated%",          "datecreated", $order_by);
            $order_by   = str_replace("%datemodified%",         "datemodified", $order_by);
        }
        
        $sql = 'SELECT * FROM '.$this->autocbi_board.' ';
        
        if( !empty($conditions) ){ $sql .= $conditions; }
        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member list
     */
    function get_all_member_data($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%type%",                 "A.type", $conditions);
            $conditions = str_replace("%status%",               "A.status", $conditions);
            $conditions = str_replace("%username%",             "A.username", $conditions);
            $conditions = str_replace("%name%",                 "A.name", $conditions);
            $conditions = str_replace("%sponsor_username%",     "sponsor_username", $conditions);
            $conditions = str_replace("%upline_username%",      "upline_username", $conditions);
            $conditions = str_replace("%position%",             "A.position", $conditions);
            $conditions = str_replace("%ranking%",              "A.ranking", $conditions);
            $conditions = str_replace("%as_stockist%",          "A.as_stockist", $conditions);
            $conditions = str_replace("%package%",              "A.package", $conditions);
            $conditions = str_replace("%datecreated%",          "A.datecreated", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id%",                   "A.id",  $order_by);
            $order_by   = str_replace("%username%",             "A.username",  $order_by);
            $order_by   = str_replace("%name%",                 "A.name",  $order_by);
            $order_by   = str_replace("%investment%",           "A.investment",  $order_by);
            $order_by   = str_replace("%sponsor%",              "sponsor_username",  $order_by);
            $order_by   = str_replace("%upline%",               "upline_username",  $order_by);
            $order_by   = str_replace("%position%",             "A.position",  $order_by);
            $order_by   = str_replace("%ranking%",              "A.ranking",  $order_by);
            $order_by   = str_replace("%datecreated%",          "A.datecreated",  $order_by);
            $order_by   = str_replace("%as_stockist%",          "A.as_stockist",  $order_by);
            $order_by   = str_replace("%city%",                 "A.city",  $order_by);
            $order_by   = str_replace("%package%",              "A.package",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS 
                A.*, 
                B.username AS sponsor_username,
                C.username AS upline_username 
            FROM ' . $this->member . ' AS A 
            LEFT JOIN ' . $this->member . ' AS B ON B.id = A.sponsor 
            LEFT JOIN ' . $this->member . ' AS C ON C.id = A.parent ';
        
        if( !empty($conditions) ){ $sql .= $conditions; }
        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all bonus data
     * 
     * @author  Iqbal
     * @return  Object  Result of member bonus list
     */
    function get_all_bonus(){
        $this->db->where('status', 0);
        $this->db->where('id_member !=', 1);
        $query = $this->db->get($this->bonus);
        return $query->result();
    }
    
    /**
     * Retrieve all member bonus data
     * 
     * @author  Iqbal
     * @param   Integer $member_id          Member ID
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member bonus list
     */
    function get_all_my_bonus($id_member, $limit=0, $offset=0, $conditions='', $order_by=''){
        if ( !is_numeric($id_member) ) return false;

        $id_member = absint($id_member);
        if ( !$id_member ) return false;
        
        if( !empty($conditions) ){
            $conditions = str_replace("%id_bonus%",         "id_bonus", $conditions);
            $conditions = str_replace("%nominal%",          "amount", $conditions);
            $conditions = str_replace("%desc%",             "`desc`", $conditions);
            $conditions = str_replace("%type%",             "type", $conditions);
            $conditions = str_replace("%status%",           "status", $conditions);
            $conditions = str_replace("%datecreated%",      "UNIX_TIMESTAMP(datecreated)", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id_bonus%",         "id_bonus",  $order_by);
            $order_by   = str_replace("%nominal%",          "amount",  $order_by);
            $order_by   = str_replace("%type%",             "type",  $order_by);
            $order_by   = str_replace("%status%",           "status",  $order_by);
            $order_by   = str_replace("%datecreated%",      "datecreated",  $order_by);
        }
        
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $this->bonus . ' WHERE id_member = ' . $id_member . '';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member bonus total
     * 
     * @author  Iqbal
     * @param   Integer $member_id          Member ID
     * @return  Object  Result of member bonus total
     */
    function get_all_my_bonus_total($id_member){
        if ( !is_numeric($id_member) ) return false;

        $id_member = absint($id_member);
        if ( !$id_member ) return false;
        
        $sql    = 'SELECT SUM(amount) AS total FROM '.$this->bonus.' WHERE id_member=?';
        $query  = $this->db->query($sql, array($id_member));
        
        if ( !$query || !$query->num_rows() )
            return false;
        
        return $query->row();
    }
    
    /**
     * Retrieve all member bonus data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member bonus list
     */
    function get_all_member_bonus($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%type%",             "A.type", $conditions);
            $conditions = str_replace("%username%",         "A.username", $conditions);
            $conditions = str_replace("%name%",             "A.name", $conditions);
            $conditions = str_replace("%bank%",             "A.bank", $conditions);
            $conditions = str_replace("%branch%",           "A.branch", $conditions);
            $conditions = str_replace("%bill%",             "A.bill", $conditions);
            $conditions = str_replace("%bill_name%",        "A.bill_name", $conditions);
            $conditions = str_replace("%status%",           "A.status", $conditions);
            $conditions = str_replace("%datecreated%",      "UNIX_TIMESTAMP(A.datecreated)", $conditions);
            $conditions = str_replace("%nominal%",          "A.total", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",         "A.username",  $order_by);
            $order_by   = str_replace("%name%",             "A.name",  $order_by);
            $order_by   = str_replace("%bank%",             "A.bank",  $order_by);
            $order_by   = str_replace("%branch%",           "A.branch",  $order_by);
            $order_by   = str_replace("%bill%",             "A.bill",  $order_by);
            $order_by   = str_replace("%bill_name%",        "A.bill_name",  $order_by);
            $order_by   = str_replace("%datecreated%",      "A.datecreated",  $order_by);
            $order_by   = str_replace("%status%",           "A.status",  $order_by);
            $order_by   = str_replace("%nominal%",          "A.total",  $order_by);
        }
        
        $sql = '
        SELECT SQL_CALC_FOUND_ROWS A.*
        FROM (
            SELECT 
                A.*,
                SUM(IFNULL(B.amount,0)) AS total
            FROM ' . $this->member . ' AS A 
            LEFT JOIN ' . $this->bonus . ' AS B ON B.id_member = A.id 
            GROUP BY A.id 
        ) AS A ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.total DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member auto-cbi fess data
     * 
     * @author  Iqbal
     * @param   Integer $member_id          Member ID
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member auto-cbi fess list
     */
    function get_all_my_autocbi($id_member, $limit=0, $offset=0, $conditions='', $order_by=''){
        if ( !is_numeric($id_member) ) return false;

        $id_member = absint($id_member);
        if ( !$id_member ) return false;
        
        if( !empty($conditions) ){
            $conditions = str_replace("%id_bonus%",         "B.id_bonus", $conditions);
            $conditions = str_replace("%nominal%",          "A.amount", $conditions);
            $conditions = str_replace("%desc%",             "A.`desc`", $conditions);
            $conditions = str_replace("%datecreated%",      "UNIX_TIMESTAMP(datecreated)", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id_bonus%",         "B.id_bonus",  $order_by);
            $order_by   = str_replace("%nominal%",          "A.amount",  $order_by);
            $order_by   = str_replace("%datecreated%",      "A.datecreated",  $order_by);
        }
        
        $sql = '
        SELECT SQL_CALC_FOUND_ROWS A.*,B.id_bonus 
        FROM ' . $this->autocbi_fees . ' AS A 
        LEFT JOIN ' . $this->bonus . ' AS B
        ON B.id = A.bonus_id
        WHERE A.id_member = ' . $id_member . '';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member confirm data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member confirm list
     */
    function get_all_member_confirm($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id%",               "A.id", $conditions);
            $conditions = str_replace("%id_member%",        "A.id_member", $conditions);
            $conditions = str_replace("%member%",           "A.member", $conditions);
            $conditions = str_replace("%sponsor%",          "A.sponsor", $conditions);
            $conditions = str_replace("%downline%",         "A.downline", $conditions);
            $conditions = str_replace("%name%",             "B.name", $conditions);
            $conditions = str_replace("%nominal%",          "IFNULL(B.nominal + B.uniquecode, 0)", $conditions);
            $conditions = str_replace("%status%",           "A.status", $conditions);
            $conditions = str_replace("%access%",           "A.access", $conditions);
            $conditions = str_replace("%datecreated%",      "UNIX_TIMESTAMP(A.datecreated)", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id%",               "A.id",  $order_by);
            $order_by   = str_replace("%member%",           "A.member",  $order_by);
            $order_by   = str_replace("%sponsor%",          "A.sponsor",  $order_by);
            $order_by   = str_replace("%downline%",         "A.downline",  $order_by);
            $order_by   = str_replace("%name%",             "B.name",  $order_by);
            $order_by   = str_replace("%nominal%",          "IFNULL(B.nominal + B.uniquecode, 0)", $order_by);
            $order_by   = str_replace("%status%",           "A.status",  $order_by);
            $order_by   = str_replace("%access%",           "A.access",  $order_by);
            $order_by   = str_replace("%datecreated%",      "A.datecreated",  $order_by);
        }
        
        $sql    = '
            SELECT SQL_CALC_FOUND_ROWS A.*, B.name, B.uniquecode, IFNULL(B.nominal + B.uniquecode, 0) AS jumlah
            FROM ' . $this->member_confirm . ' AS A 
            INNER JOIN ' . $this->member . ' AS B ON B.id = A.id_downline ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member reward data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member reward list
     */
    function get_all_member_reward($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id_member%",        "A.id_member", $conditions);
            $conditions = str_replace("%username%",         "B.username", $conditions);
            $conditions = str_replace("%name%",             "B.name", $conditions);
            $conditions = str_replace("%bank%",             "B.bank", $conditions);
            $conditions = str_replace("%bill%",             "B.bill", $conditions);
            $conditions = str_replace("%bill_name%",        "B.bill_name", $conditions);
            $conditions = str_replace("%type%",             "A.type", $conditions);
            $conditions = str_replace("%status%",           "A.status", $conditions);
            $conditions = str_replace("%qualified%",        "A.qualified", $conditions);
            $conditions = str_replace("%datecreated%",      "A.datecreated", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",         "B.username",  $order_by);
            $order_by   = str_replace("%name%",             "B.name",  $order_by);
            $order_by   = str_replace("%bank%",             "B.bank",  $order_by);
            $order_by   = str_replace("%bill%",             "B.bill",  $order_by);
            $order_by   = str_replace("%bill_name%",        "B.bill_name",  $order_by);
            $order_by   = str_replace("%type%",             "A.type",  $order_by);
            $order_by   = str_replace("%status%",           "A.status",  $order_by);
            $order_by   = str_replace("%qualified%",        "A.qualified",  $order_by);
            $order_by   = str_replace("%datecreated%",      "A.datecreated",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS A.*, B.username, B.name, B.bank, B.bill, B.bill_name, B.package, C.reward_name, C.reward_poin 
            FROM ' . $this->reward . ' AS A 
            LEFT JOIN ' . $this->member . ' AS B ON B.id = A.id_member
        	LEFT JOIN ' . $this->reward_option . ' C ON C.reward_id = A.type ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member reward data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member reward list
     */
    function get_all_member_cashreward($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id_member%",        "A.id_member", $conditions);
            $conditions = str_replace("%username%",         "B.username", $conditions);
            $conditions = str_replace("%name%",             "B.name", $conditions);
            $conditions = str_replace("%bank%",             "B.bank", $conditions);
            $conditions = str_replace("%bill%",             "B.bill", $conditions);
            $conditions = str_replace("%bill_name%",        "B.bill_name", $conditions);
            $conditions = str_replace("%type%",             "A.type", $conditions);
            $conditions = str_replace("%status%",           "A.status", $conditions);
            $conditions = str_replace("%qualified%",        "A.qualified", $conditions);
            $conditions = str_replace("%datecreated%",      "A.datecreated", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",         "B.username",  $order_by);
            $order_by   = str_replace("%name%",             "B.name",  $order_by);
            $order_by   = str_replace("%bank%",             "B.bank",  $order_by);
            $order_by   = str_replace("%bill%",             "B.bill",  $order_by);
            $order_by   = str_replace("%bill_name%",        "B.bill_name",  $order_by);
            $order_by   = str_replace("%type%",             "A.type",  $order_by);
            $order_by   = str_replace("%status%",           "A.status",  $order_by);
            $order_by   = str_replace("%qualified%",        "A.qualified",  $order_by);
            $order_by   = str_replace("%datecreated%",      "A.datecreated",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS A.*, B.username, B.name, B.bank, B.bill, B.bill_name, B.package, C.reward_name, C.reward_poin 
            FROM ' . $this->cashreward . ' AS A 
            LEFT JOIN ' . $this->member . ' AS B ON B.id = A.id_member
        	LEFT JOIN ' . $this->cashreward_option . ' C ON C.reward_id = A.type ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member withdraw data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member withdraw list
     */
    function get_all_member_withdraw($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id%",               "A.id", $conditions);
            $conditions = str_replace("%username%",         "A.username", $conditions);
            $conditions = str_replace("%name%",             "B.name", $conditions);
            $conditions = str_replace("%bank%",             "B.bank", $conditions);
            $conditions = str_replace("%bill%",             "B.bill", $conditions);
            $conditions = str_replace("%bill_name%",        "B.bill_name", $conditions);
            $conditions = str_replace("%total%",            "A.nominal_receipt", $conditions);
            $conditions = str_replace("%status%",           "A.status", $conditions);
            $conditions = str_replace("%datecreated%",		"UNIX_TIMESTAMP(A.datecreated)", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",         "A.username",  $order_by);
            $order_by   = str_replace("%name%",             "B.name",  $order_by);
            $order_by   = str_replace("%bank%",             "B.bank",  $order_by);
            $order_by   = str_replace("%bill%",             "B.bill",  $order_by);
            $order_by   = str_replace("%bill_name%",        "B.bill_name",  $order_by);
            $order_by   = str_replace("%total%",            "A.nominal_receipt",  $order_by);
            $order_by   = str_replace("%status%",           "A.status",  $order_by);
            $order_by   = str_replace("%datecreated%",      "A.datecreated",  $order_by);
        }
                
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS A.*, B.name, B.bank, B.bill, B.bill_name, B.phone  
            FROM ' . $this->withdraw . ' AS A 
            LEFT JOIN ' . $this->member . ' AS B 
            ON B.id = A.id_member ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member commission data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member commission list
     */
    function get_all_member_commission($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%username%",         "B.username", $conditions);
            $conditions = str_replace("%name%",             "B.name", $conditions);
            $conditions = str_replace("%total%",            "SUM(A.amount)", $conditions);
            $conditions = str_replace("%date%",            	"A.datecreated", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",         "B.username",  $order_by);
            $order_by   = str_replace("%name%",             "B.name",  $order_by);
            $order_by   = str_replace("%total%",            "SUM(A.amount)",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS A.*, SUM(A.amount) AS total, B.username, B.name 
            FROM ' . $this->bonus . ' AS A 
            LEFT JOIN ' . $this->member . ' AS B ON B.id = A.id_member 
            WHERE 1=1 ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' GROUP BY A.id_member ORDER BY '. ( !empty($order_by) ? $order_by : 'total DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member deposite data
     * 
     * @author  Iqbal
	 * @author	Ahmad
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member deposite list
     */
    function get_all_member_deposite_old($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%username%",         "A.username", $conditions);
            $conditions = str_replace("%name%",             "A.name", $conditions);
            $conditions = str_replace("%total%",            "( IFNULL(B.bonus, 0) - IFNULL(C.withdraw, 0) )", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",         "A.username",  $order_by);
            $order_by   = str_replace("%name%",             "A.name",  $order_by);
            $order_by   = str_replace("%total%",            "( IFNULL(B.bonus, 0) - IFNULL(C.withdraw, 0) )",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS 
            	A.id, A.username, A.name, A.phone, A.bank, A.bill, A.npwp,
                IFNULL(B.bonus, 0) AS bonus, 
                IFNULL(C.withdraw, 0) AS withdraw, 
                ( IFNULL(B.bonus, 0) - IFNULL(C.withdraw, 0) ) AS total
            FROM `'. $this->member .'` AS A
            LEFT JOIN (SELECT SUM(amount) AS bonus, id_member FROM `'. $this->bonus .'` GROUP BY id_member ) AS B ON B.id_member = A.id 
            LEFT JOIN (SELECT SUM(nominal) AS withdraw, id_member FROM `'. $this->withdraw .'` GROUP BY id_member) AS C ON C.id_member = A.id 
            WHERE A.type != 2 AND ( IFNULL(B.bonus, 0) - IFNULL(C.withdraw, 0) ) > 0';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : '( IFNULL(B.bonus, 0) - IFNULL(C.withdraw, 0) ) DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member deposite data
     * 
     * @author  Iqbal
	 * @author	Ahmad
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member deposite list
     */
    function get_all_member_deposite($limit=0, $offset=0, $conditions='', $order_by='', $total_conditions = ''){
        if ( ! empty( $conditions ) ){
            $conditions = str_replace("%username%",         "M.username", $conditions);
            $conditions = str_replace("%name%",             "M.name", $conditions);
        }
		
		$total_sql = 'SUM(IFNULL(D.bonus, 0)) - SUM(IFNULL(D.withdraw, 0)) - SUM(IFNULL(D.autocbi, 0))';

		if ( $total_conditions ) {
			$total_conditions = str_replace("%total%",		$total_sql, $total_conditions);
		}
		
		if ( empty( $order_by ) )
			$order_by = '%total% DESC';
        
        if ( ! empty( $order_by ) ){
            $order_by   = str_replace("%username%",         "M.username",  $order_by);
            $order_by   = str_replace("%name%",             "M.name",  $order_by);
            $order_by   = str_replace("%total%",            "11",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS 
				M.id,
				M.username,
				M.name,
				M.npwp,
                M.phone,
                M.bank,
                M.bill,
				SUM( IFNULL( D.bonus, 0 ) ) AS bonus,
				SUM( IFNULL( D.withdraw, 0 ) ) AS withdraw,
				SUM( IFNULL( D.autocbi, 0 ) ) AS autocbi,
				' . $total_sql . ' AS total
			FROM (
				SELECT 
					id_member,
					M.amount AS bonus, -- bonus
					0 AS withdraw, -- wd
					0 AS autocbi -- autocbi
				FROM '.$this->bonus.' M
				UNION ALL
				SELECT
					id_member,
					0, -- bonus
					W.nominal, -- wd
					0 -- autocbi
				FROM '.$this->withdraw.' W
				UNION ALL
				SELECT
					id_member,
					0, -- bonus
					0, -- wd
					A.amount -- autocbi
				FROM '.$this->autocbi_fees.' A
			) AS D
			INNER JOIN '.$this->member.' M ON M.id = D.id_member
			WHERE M.type != 2 ' . $conditions . '
			GROUP BY 1';
		
		// having conditions
		if ( $total_conditions ) {
            $sql .= ' HAVING ' . ltrim( $total_conditions, ' AND' );
        }else{
            $sql .= ' HAVING ' . $total_sql . ' > 0';
        }
		
		// order
        $sql .= ' ORDER BY ' . $order_by;
		
		// limit
        if ( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query( $sql );
        if ( ! $query || ! $query->num_rows() ) return false;
        
        return $query->result();
    }
    
    /**
     * Get user data by conditions
     * 
     * @author  Iqbal
     * @param   String  $field  (Required)  Database field name or special field name defined inside this function
     * @param   String  $value  (Optional)  Value of the field being searched
     * @return  Mixed   Boolean false on failed process, invalid data, or data is not found, otherwise StdClass of member
     */
    function get_member_by($field, $value='')
    {
        $id = '';
        
        switch ($field) {
            case 'id':
                $id     = $value;
                break;
            case 'email':
                $value  = sanitize_email($value);
                $id     = '';
                $field  = 'email';
                break;
            case 'login':
                $value  = $value;
                $id     = '';
                $field  = 'login';
                break;
            default:
                return false;
        }
        
        if ( $id != '' && $id > 0 )
            return $this->get_memberdata($id);
        
        if( empty($field) ) return false;
        
        $db     = $this->db;
        
        if( $field == 'login' ){
            $db->where('username LIKE BINARY', $value);
        }else{
            $db->where($field, $value);
        }

        $query  = $db->get($this->member);
        
        if ( !$query->num_rows() )
            return false;

        foreach ( $query->result() as $row ) {
            $member = $row;
        }

        return $member;
    }
    
    /**
     * Get member data by member ID
     * 
     * @author  Iqbal
     * @param   Integer $member_id  (Required)  Member ID
     * @return  Mixed   False on failed process, otherwise object of member.
     */
    function get_memberdata($member_id){
        if ( !is_numeric($member_id) ) return false;

        $member_id = absint($member_id);
        
        
        if ( !$member_id ) return false;
        
        $query = $this->db->get_where($this->member, array($this->id => $member_id));
        if ( !$query->num_rows() )
            return false;

        foreach ( $query->result() as $row ) {
            $member = $row;
        }
        
        return $member;
    }
    
    /**
     * Get auto global data by conditions
     * 
     * @author  Iqbal
     * @param   String  $field  (Required)  Database field name or special field name defined inside this function
     * @param   String  $value  (Optional)  Value of the field being searched
     * @return  Mixed   Boolean false on failed process, invalid data, or data is not found, otherwise StdClass of auto global
     */
    function get_auto_global_by($field, $value='')
    {
        $id = '';
        
        switch ($field) {
            case 'id':
                $id     = $value;
                break;
            case 'member':
                $value  = $value;
                $id     = '';
                $field  = 'id_member';
                break;
            case 'index':
                $value  = $value;
                $id     = '';
                $field  = 'index';
                break;
            default:
                return false;
        }
        
        if ( $id != '' && $id > 0 ) return $this->get_autoglobaldata($id);
        
        if( empty($field) ) return false;
        
        $db     = $this->db;
        
        if( $field == 'id_member' ){
            $db->where('id_member', $value);
        }else{
            $db->where($field, $value);
        }

        $query  = $db->get($this->auto_global);
        
        if ( !$query->num_rows() ) return false;

        return $query->row();
    }
    
    /**
     * Get auto global data by auto global ID
     * 
     * @author  Iqbal
     * @param   Integer $auto_id    (Required)  Auto Global ID
     * @return  Mixed   False on failed process, otherwise object of member.
     */
    function get_autoglobaldata($auto_id){
        if ( !is_numeric($auto_id) ) return false;

        $auto_id = absint($auto_id);
        if ( !$auto_id ) return false;

        $this->db->select('A.*,B.username,B.name');
        $this->db->from($this->auto_global . ' AS A');
        $this->db->join($this->member . ' AS B', 'B.id = A.id_member');
        $this->db->where(array('A.'.$this->id => $auto_id));
        $query = $this->db->get();
        
        if ( !$query->num_rows() ) return false;

        $autoglobal = $query->row();
        
        return $autoglobal;
    }
    
    /**
     * Get auto-cbi data by conditions
     * 
     * @author  Iqbal
     * @param   String  $field  (Required)  Database field name or special field name defined inside this function
     * @param   String  $value  (Optional)  Value of the field being searched
     * @return  Mixed   Boolean false on failed process, invalid data, or data is not found, otherwise StdClass of auto-cbi
     */
    function get_autocbi_by($field, $value='')
    {
        $id = '';
        
        switch ($field) {
            case 'id':
                $id     = $value;
                break;
            case 'member':
                $value  = $value;
                $id     = '';
                $field  = 'id_member';
                break;
            case 'index':
                $value  = $value;
                $id     = '';
                $field  = 'index';
                break;
            default:
                return false;
        }
        
        if ( $id != '' && $id > 0 ) return $this->get_autocbidata($id);
        
        if( empty($field) ) return false;
        
        $db     = $this->db;
        
        if( $field == 'id_member' ){
            $db->where('id_member', $value);
        }else{
            $db->where($field, $value);
        }

        $query  = $db->get($this->autocbi);
        
        if ( !$query->num_rows() ) return false;

        return $query->row();
    }
    
    /**
     * Get auto-cbi data by auto-cbi ID
     * 
     * @author  Iqbal
     * @param   Integer $autocbi_id (Required)  Auto-CBI ID
     * @return  Mixed   False on failed process, otherwise object of member.
     */
    function get_autocbidata($autocbi_id){
        if ( !is_numeric($autocbi_id) ) return false;

        $autocbi_id = absint($autocbi_id);
        if ( !$autocbi_id ) return false;

        $this->db->select('*');
        $this->db->from($this->autocbi);
        $this->db->where(array($this->id => $autocbi_id));
        $query = $this->db->get();
        
        if ( !$query->num_rows() ) return false;

        $autocbi = $query->row();
        
        return $autocbi;
    }
    
    /**
     * Get member confirm
     * 
     * @author  Iqbal
     * @param   Int     $id  (Required)  Member Confirm ID
     * @return  Mixed   False on invalid onfirm id, otherwise array of member confirm.
     */
    function get_member_confirm($id){
        if ( !is_numeric($id) ) return false;

        $id  = absint($id);
        if ( !$id ) return false;
        
        $data       = array($this->id => $id);
        $this->db->where($data);
        
        $query      = $this->db->get($this->member_confirm);
        
        if ( !$query->num_rows() )
            return false;
        
        return $query->row();
    }
    
    /**
     * Get member random
     * 
     * @author  Iqbal
     * @return  stdClass of member random.
     */
    function get_member_random(){
        $sql        = 'SELECT username FROM '.$this->member.' WHERE status = 1 AND type != 2 ORDER BY RAND() LIMIT 1';
        $query      = $this->db->query($sql);
        
        if ($query->num_rows() > 0) { 
            return $query->row();
        }else{
            $sql    = 'SELECT username FROM '.$this->member.' WHERE type = 2 ORDER BY RAND() LIMIT 1';
            $query  = $this->db->query($sql);
            return $query->row();
        }
    }
    
    /**
     * Get member newest
     * 
     * @author  Iqbal
     * @return  stdClass of member newest.
     */
    function get_member_newest(){
        $sql    = 'SELECT id, name, type FROM '.$this->member.' WHERE status = 1 AND type = 1 ORDER BY datecreated DESC LIMIT 1';
        $query  = $this->db->query($sql);
        
        if ($query->num_rows() > 0) { 
            return $query->row();
        }else{
            $sql    = 'SELECT id, name, type FROM '.$this->member.' WHERE type = 2 ORDER BY datecreated LIMIT 1';
            $query  = $this->db->query($sql);
            return $query->row();
        }
    }
    
    /**
     * Get member newest
     * 
     * @author  Iqbal
     * @param   Int     $limit  (Optional)  Limit of Member
     * @return  stdClass of member newest.
     */
    function get_member_newest_all($limit=1){
        $sql    = 'SELECT * FROM '.$this->member.' WHERE status = 1 AND type = 1 ORDER BY datecreated DESC LIMIT ' . $limit;
        $query  = $this->db->query($sql);
        
        return $query->result();
    }
    
    /**
     * Get member newest
     * 
     * @author  Iqbal
     * @param   Int     $limit  (Optional)  Limit of Member
     * @return  stdClass of member newest.
     */
    function get_member_newest_bonus($limit=1){
        $sql    = '
            SELECT * FROM '.$this->bonus.' AS B
            LEFT JOIN '.$this->member.' AS M 
            ON M.id = B.id_member 
            WHERE M.status = 1 AND M.type = 1 ORDER BY B.datecreated DESC LIMIT ' . $limit;
        $query  = $this->db->query($sql);
        
        return $query->result();
    }
    
    /**
     * Get member reward
     * 
     * @author  Iqbal
     * @param   Int     $id         (Required)  Member ID
     * @param   Array   $condition  (Optional)  Data Condition
     * @return  Mixed   False on invalid onfirm id, otherwise array of member reward.
     */
    function get_member_reward($id, $condition=array()){
        if ( !is_numeric($id) ) return false;

        $id  = absint($id);
        if ( !$id ) return false;
        
        $data       = array('id_member' => $id);
        $this->db->where($data);
        
        if ( !empty($condition) ) { $this->db->where($condition); }
        
        $query      = $this->db->get($this->reward);
        
        if ( !$query->num_rows() )
            return false;
        
        return $query->row();
    }
    
    /**
     * Get reward data by reward ID
     * 
     * @author  Iqbal
     * @param   Integer $reward_id  (Required)  Reward ID
     * @return  Mixed   False on failed process, otherwise object of reward.
     */
    function get_rewarddata($reward_id){
        if ( !is_numeric($reward_id) ) return false;

        $reward_id = absint($reward_id);
        if ( !$reward_id ) return false;
        
        $query = $this->db->get_where($this->reward, array($this->id => $reward_id));
        if ( !$query->num_rows() )
            return false;

        foreach ( $query->result() as $row ) {
            $reward = $row;
        }
        
        return $reward;
    }

    /**
     * Get member withdraw
     * 
     * @author  Iqbal
     * @param   Int     $id         (Required)  Member ID
     * @param   Array   $condition  (Optional)  Data Condition
     * @return  Mixed   False on invalid onfirm id, otherwise array of member reward.
     */
    function get_member_withdraw($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%username%",         "username", $conditions);
            $conditions = str_replace("%status%",           "status", $conditions);
            $conditions = str_replace("%datecreated%",      "UNIX_TIMESTAMP(datecreated)", $conditions);
            $conditions = str_replace("%nominal%",          "nominal_receipt", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",         "username",  $order_by);
            $order_by   = str_replace("%datecreated%",      "datecreated",  $order_by);
            $order_by   = str_replace("%status%",           "status",  $order_by);
            $order_by   = str_replace("%nominal%",          "nominal_receipt",  $order_by);
        }
        
        $sql = 'SELECT SQL_CALC_FOUND_ROWS *, SUM(nominal_receipt) AS sum_receipt FROM '. $this->withdraw .'';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' GROUP BY id_member ORDER BY '. ( !empty($order_by) ? $order_by : 'sum_receipt DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Get Sponsored of Member
     * 
     * @author  Iqbal
     * @param   Int         $id_member  (Required) ID Member
     * @return  Int of total all sponsored
     */
    function get_sponsored($id_member){  
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $sql    = 'SELECT id,username FROM '.$this->member.' WHERE sponsor = '.$id_member . '';
        $query  = $this->db->query($sql);
        
        if ( !$query || $query->num_rows == 0 ) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member pin data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member pin list
     */
    function get_all_member_pin($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%type%",                 "A.type", $conditions);
            $conditions = str_replace("%username%",             "A.username", $conditions);
            $conditions = str_replace("%id_member_registered%", "A.id_member_registered", $conditions);
            $conditions = str_replace("%name%",                 "A.name", $conditions);
            $conditions = str_replace("%total%",                "B.total", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",             "A.username",  $order_by);
            $order_by   = str_replace("%id_member_registered%", "A.id_member_registered",  $order_by);
            $order_by   = str_replace("%name%",                 "A.name",  $order_by);
            $order_by   = str_replace("%total%",                "B.total",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS A.id, A.username, A.name, A.type, B.total
            FROM ' . $this->member . ' AS A 
            LEFT JOIN (
                SELECT id_member, COUNT(id) AS total 
                FROM ' . $this->pin . '  
                GROUP BY id_member
            ) AS B ON B.id_member = A.id ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all pin order data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member pin list
     */
    function get_all_member_pin_order($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id_member%",        "A.id_member", $conditions);
            $conditions = str_replace("%username%",         "B.username", $conditions);
            $conditions = str_replace("%name%",             "B.name", $conditions);
            $conditions = str_replace("%package%",          "A.package", $conditions);
            $conditions = str_replace("%qty%",              "A.qty", $conditions);
            $conditions = str_replace("%status%",           "A.status", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",         "B.username",  $order_by);
            $order_by   = str_replace("%name%",             "B.name",  $order_by);
            $order_by   = str_replace("%package%",          "A.package",  $order_by);
            $order_by   = str_replace("%qty%",              "A.qty",  $order_by);
            $order_by   = str_replace("%status%",           "A.status",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS A.*, B.username, B.name, B.type
            FROM ' . $this->pin_order . ' AS A 
            LEFT JOIN ' . $this->member . ' AS B 
            ON B.id = A.id_member ';
        
        if( !empty($conditions) ){ $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all pin transfer data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member pin transfer list
     */
    function get_all_member_pin_transfer($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%username_sender%",  "username_sender", $conditions);
            $conditions = str_replace("%username%",         "username", $conditions);
            $conditions = str_replace("%id_member_sender%", "id_member_sender", $conditions);
            $conditions = str_replace("%id_member%",        "id_member", $conditions);
            $conditions = str_replace("%qty%",              "qty", $conditions);
            $conditions = str_replace("%package%",          "package", $conditions);
            $conditions = str_replace("%datecreated%",      "UNIX_TIMESTAMP(datecreated)", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username_sender%",  "username_sender",  $order_by);
            $order_by   = str_replace("%username%",         "username",  $order_by);
            $order_by   = str_replace("%qty%",              "qty",  $order_by);
            $order_by   = str_replace("%package%",          "package",  $order_by);
            $order_by   = str_replace("%datecreated%",      "datecreated",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS *, COUNT(id) AS qty 
            FROM ' . $this->pin_transfer . ' ';
        
        if( !empty($conditions) ){ $sql .= $conditions; }

        $sql   .= ' GROUP BY id_member, datecreated ORDER BY '. ( !empty($order_by) ? $order_by : 'datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member pin data
     * 
     * @author  Iqbal
     * @param   Integer $member_id          Member ID
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member bonus list
     */
    function get_all_my_pin($id_member, $limit=0, $offset=0, $conditions='', $order_by=''){
        if ( !is_numeric($id_member) ) return false;

        $id_member = absint($id_member);
        if ( !$id_member ) return false;
        
        if( !empty($conditions) ){
            $conditions = str_replace("%id_pin%",           "A.id_pin", $conditions);
            $conditions = str_replace("%username%",         "C.username", $conditions);
            $conditions = str_replace("%package%",          "A.package", $conditions);
            $conditions = str_replace("%status%",           "A.status", $conditions);
            $conditions = str_replace("%datecreated%",      "UNIX_TIMESTAMP(A.datecreated)", $conditions);
            $conditions = str_replace("%datetransfer%",     "UNIX_TIMESTAMP(B.datecreated)", $conditions);
            $conditions = str_replace("%username_sender%",  "B.username_sender", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id_pin%",           "A.id_pin",  $order_by);
            $order_by   = str_replace("%username%",         "C.username",  $order_by);
            $order_by   = str_replace("%package%",          "A.package",  $order_by);
            $order_by   = str_replace("%status%",           "A.status",  $order_by);
            $order_by   = str_replace("%datecreated%",      "A.datecreated",  $order_by);
            $order_by   = str_replace("%datetransfer%",     "B.datecreated",  $order_by);
            $order_by   = str_replace("%username_sender%",  "B.username_sender",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS A.*,
            	IFNULL(B.id_member_sender, 0) AS id_member_sender,
            	IFNULL(B.username_sender, "admin") AS username_sender,
            	IFNULL(B.id_member, 0) AS id_member_receiver,
            	IFNULL(B.username, 0) AS username_receiver,
            	IFNULL(B.id_pin, 0) AS pin_transfered,
            	IFNULL(B.datecreated, "0000-00-00 00:00:00") AS datetransfer,
            	C.username  
            FROM `'. $this->pin .'` AS A 
            LEFT JOIN (
            	SELECT 
                	id_member_sender,
                	username_sender,
                	id_member,
                	username,
                	id_pin,
                	datecreated
               	FROM `'. $this->pin_transfer .'` 
                WHERE id_member = '. $id_member .'
                GROUP BY id_pin
                ORDER BY datecreated DESC
            ) AS B ON B.id_pin = A.id 
            LEFT JOIN `'. $this->member .'` AS C ON C.id = A.id_member 
            WHERE A.id_member = '. $id_member .' ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }
        
        $sql   .= ' ORDER BY '. ( !empty($order_by) ? 
            ( substr($order_by,0,13) == "B.datecreated" ? 
                $order_by . ', A.datecreated '. ( substr($order_by,-3)=='asc' ? 'DESC' : 'ASC' ) : $order_by ) : 'B.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all pin data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of pin                default 0
     * @param   Int     $offset             Offset ot pin               default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of pin list
     */
    function get_all_pin($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%id_member_registered%", "A.id_member_registered", $conditions);
            $conditions = str_replace("%id_pin%",               "A.id_pin", $conditions);
            $conditions = str_replace("%username%",             "C.username", $conditions);
            $conditions = str_replace("%memberres%",            "D.username", $conditions);
            $conditions = str_replace("%memberreg%",            "B.username", $conditions);
            $conditions = str_replace("%datecreated%",          "UNIX_TIMESTAMP(A.datecreated)", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%id_pin%",               "A.id_pin",  $order_by);
            $order_by   = str_replace("%username%",             "C.username",  $order_by);
            $order_by   = str_replace("%memberres%",            "D.username",  $order_by);
            $order_by   = str_replace("%memberreg%",            "B.username",  $order_by);
            $order_by   = str_replace("%datecreated%",          "A.datecreated",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS *,
                B.id AS id_registered, 
                B.username AS username_registered,
                D.id AS id_register,
                D.username AS username_register 
            FROM ' . $this->pin . ' AS A 
            LEFT JOIN (
                SELECT id, username FROM ' . $this->member . '  
            ) AS B ON B.id = A.id_member_registered 
            LEFT JOIN ' . $this->member . ' AS C ON C.id = A.id_member 
            LEFT JOIN (
                SELECT id, username FROM ' . $this->member . '  
            ) AS D ON D.id = A.id_member_register ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datemodified DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Retrieve all member tax data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member tax list
     */
    function get_all_member_tax($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%type%",             "A.type", $conditions);
            $conditions = str_replace("%memberuid%",        "A.memberuid", $conditions);
            $conditions = str_replace("%name%",             "A.name", $conditions);
            $conditions = str_replace("%nominal%",          "B.nominal", $conditions);
            $conditions = str_replace("%tax%",              "B.nominal_tax", $conditions);
            $conditions = str_replace("%receipt%",          "C.receipt", $conditions);
            $conditions = str_replace("%datecreated%",      "UNIX_TIMESTAMP(B.datecreated_bonus) AND UNIX_TIMESTAMP(C.datecreated_receipt)", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%memberuid%",        "A.memberuid",  $order_by);
            $order_by   = str_replace("%name%",             "A.name",  $order_by);
            $order_by   = str_replace("%nominal%",          "B.nominal",  $order_by);
            $order_by   = str_replace("%tax%",              "B.nominal_tax",  $order_by);
            $order_by   = str_replace("%receipt%",          "C.receipt",  $order_by);
            $order_by   = str_replace("%datecreated%",      "UNIX_TIMESTAMP(B.datecreated_bonus) AND UNIX_TIMESTAMP(C.datecreated_receipt)",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS 
                A.id, A.memberuid, A.name, A.datecreated, A.type,
                B.nominal, B.nominal_tax, B.datecreated_bonus,
                C.receipt, C.datecreated_receipt
            FROM ' . $this->table . ' AS A 
            LEFT JOIN (
                SELECT 
                    id_member, 
                    SUM(amount) AS nominal,
                    SUM(tax) As nominal_tax,
                    datecreated AS datecreated_bonus
                FROM ' . $this->bonus . '
                GROUP BY id_member
            ) AS B ON B.id_member = A.id 
            LEFT JOIN (
                SELECT
                    id_member,
                    SUM(nominal_receipt) AS receipt,
                    datecreated AS datecreated_receipt
                FROM ' . $this->withdraw . '
                GROUP BY id_member
            ) AS C ON C.id_member = A.id ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.datecreated DESC');
        
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql);
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
	
	/**
	 * Retrieve all member tax data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @param   Int     $id_member          ID Member                   default 0
     * @return  Object  Result of member tax list
	 */
	function get_member_tax_all( $limit=0, $offset=0, $conditions='', $order_by='', $id_member = 0 ) {
        if ( !empty( $conditions ) ){
            $conditions = str_replace("%type%",             "T.type", $conditions);
            $conditions = str_replace("%username%",        	"T.username", $conditions);
            $conditions = str_replace("%name%",             "T.name", $conditions);
            $conditions = str_replace("%nominal%",          "T.total_nominal", $conditions);
            $conditions = str_replace("%tax%",              "T.total_tax", $conditions);
            $conditions = str_replace("%receipt%",          "T.total_nominal_receipt", $conditions);
            $conditions = str_replace("%period%",			"T.period", $conditions);
        }
        
        if ( ! empty( $order_by ) ){
        	$order_by   = str_replace("%period%",      		"2",  $order_by);
            $order_by   = str_replace("%username%",        	"5",  $order_by);
            $order_by   = str_replace("%name%",             "6",  $order_by);
            $order_by   = str_replace("%nominal%",          "11",  $order_by);
            $order_by   = str_replace("%tax%",              "12",  $order_by);
            $order_by   = str_replace("%receipt%",          "13",  $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS *
        	FROM (
        		SELECT
					M.id,
					DATE_FORMAT( W.datecreated, "%Y-%m" ) AS period,
					DATE_FORMAT( W.datecreated, "%b %Y" ) AS period_name,
					M.type,
					M.username,
					M.`name`,
					M.address,
					M.city,
					M.idcard,
					M.npwp,
					IFNULL( SUM( W.nominal ), 0 ) AS total_nominal,
					IFNULL( SUM( W.tax ), 0 ) AS total_tax,
					IFNULL( SUM( W.nominal_receipt ), 0 ) AS total_received
				FROM '.$this->member.' M
				INNER JOIN '.$this->withdraw.' W ON W.id_member = M.id
				' . ( $id_member ? ' WHERE M.id = ?' : '' ) . '
				GROUP BY 1, 2
			) AS T WHERE 1=1 ' . $conditions;
        
        $sql .= ' ORDER BY '. ( ! empty( $order_by ) ? $order_by : '2 DESC');
        if ( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query( $sql, array( $id_member ) );
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
	 * Retrieve all member tax data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @param   Int     $id_member          ID Member                   default 0
     * @return  Object  Result of member tax list
	 */
    function get_taxes_all( $limit=0, $offset=0, $conditions='', $order_by='', $id_member = 0 ) {
		if ( !empty( $conditions ) ){
            $conditions = str_replace("%type%",             "T.type", $conditions);
            $conditions = str_replace("%id%",				"M.id", $conditions);
            $conditions = str_replace("%id_tax%",			"T.id", $conditions);
            $conditions = str_replace("%username%",        	"M.username", $conditions);
            $conditions = str_replace("%name%",             "M.name", $conditions);
            $conditions = str_replace("%nominal%",          "T.total_nominal", $conditions);
            $conditions = str_replace("%tax%",              "T.total_tax", $conditions);
            $conditions = str_replace("%receipt%",          "T.total_receipt", $conditions);
            $conditions = str_replace("%period%",			"T.period", $conditions);
        }
        
        if ( ! empty( $order_by ) ){
        	$order_by   = str_replace("%period%",      		"T.period",  $order_by);
            $order_by   = str_replace("%username%",        	"M.username",  $order_by);
            $order_by   = str_replace("%name%",             "6",  $order_by);
            $order_by   = str_replace("%nominal%",          "11",  $order_by);
            $order_by   = str_replace("%tax%",              "12",  $order_by);
            $order_by   = str_replace("%receipt%",          "13",  $order_by);
        }
        
		if ( $id_member )
			$conditions .= ' AND M.id = ?';
        
        $sql = 'SELECT SQL_CALC_FOUND_ROWS
        		M.id,
				T.period,
				DATE_FORMAT( CONCAT( T.period_year, "-", T.period_month, "-01" ), "%b %Y" ) AS period_name,
				M.type,
				M.username,
				M.`name`,
				M.address,
				M.city,
				M.idcard,
				M.npwp,
				T.total_nominal,
				T.total_tax,
				T.total_received,
				T.sequence_num,
				T.period_month,
				T.period_year,
				T.id AS id_tax
        	FROM '.$this->taxes.' T
        	INNER JOIN '.$this->member.' M ON M.id = T.id_member
			WHERE 1=1 ' . $conditions;
        
        $sql .= ' ORDER BY '. ( ! empty( $order_by ) ? $order_by : '2 DESC');
        if ( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query( $sql, array( $id_member ) );
        if ( ! $query || ! $query->num_rows() ) return false;
        
        return $query->result();
	}

    /**
	 * Retrieve all member tax data by ID member
     * 
     * @author  Iqbal
     * @param   Int     $id_member          ID Member                   default 0
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member tax list
	 */
	function get_member_tax( $id_member, $limit=0, $offset=0, $conditions='', $order_by='' ) {
		return $this->get_member_tax_all( $limit, $offset, $conditions, $order_by, $id_member );
	}
    
    /**
     * Retrieve all member autocbi data
     * 
     * @author  Iqbal
     * @param   Int     $limit              Limit of member             default 0
     * @param   Int     $offset             Offset ot member            default 0
     * @param   String  $conditions         Condition of query          default ''
     * @param   String  $order_by           Column that make to order   default ''
     * @return  Object  Result of member autocbi list
     */
    function get_all_member_autocbi($limit=0, $offset=0, $conditions='', $order_by=''){
        if( !empty($conditions) ){
            $conditions = str_replace("%member_id%",				"A.id_member", $conditions);
            $conditions = str_replace("%username%",                 "A.username", $conditions);
            $conditions = str_replace("%name%",             		"A.name", $conditions);
            $conditions = str_replace("%status%",                   "A.status", $conditions);
            $conditions = str_replace("%total%",					"A.total", $conditions);
			$conditions = str_replace("%autocbi_qualified%",		"A.autocbi_qualified", $conditions);
            $conditions = str_replace("%autocbi_qualified_date%",	"UNIX_TIMESTAMP(A.autocbi_qualified_date)", $conditions);
			$conditions = str_replace("%datecreated%",				"A.datecreated", $conditions);
        }
        
        if( !empty($order_by) ){
            $order_by   = str_replace("%username%",                 "A.username",  $order_by);
            $order_by   = str_replace("%name%",                     "A.name", $order_by);
            $order_by   = str_replace("%status%",                   "A.status", $order_by);
            $order_by   = str_replace("%total%",                    "total", $order_by);
            $order_by   = str_replace("%autocbi_qualified%",        "A.autocbi_qualified", $order_by);
            $order_by   = str_replace("%autocbi_qualified_date%",   "A.autocbi_qualified_date", $order_by);
        }
        
        $sql = '
            SELECT SQL_CALC_FOUND_ROWS A.* FROM (
				SELECT 
    				A.id_member,
    				SUM(A.amount) AS total,
    				B.username,
    				B.`name`,
    				B.email,
                    B.phone,
    				B.type AS member_type,
    				B.autocbi_qualified,
                    B.autocbi_qualified_date,
                    B.status
                FROM '.$this->autocbi_fees.' A
                INNER JOIN '.$this->member.' B ON B.id = A.id_member
                WHERE B.type=?
                GROUP BY 1
			) AS A ';
        
        if( !empty($conditions) )   { $sql .= $conditions; }

        $sql   .= ' ORDER BY '. ( !empty($order_by) ? $order_by : 'A.`autocbi_qualified` DESC, A.`autocbi_qualified_date` ASC');
        if( $limit ) $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        
        $query = $this->db->query($sql, array(1));
        if(!$query || !$query->num_rows()) return false;
        
        return $query->result();
    }
    
    /**
     * Get node available
     * 
     * @author  Iqbal
     * @param   Int         $id_member  (Required)  Member ID
     * @param   Boolean     $count      (Optional)  Count of node
     * @param   String      $position   (Optional)  Position of Node
     * @return  Mixed   False on invalid member id, otherwise array of node available.
     */
    function get_node_available($id_member, $count=false, $position=''){
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $memberdata = $this->get_memberdata($id_member);
        if( !$memberdata ) return false;
        
        $arr        = array($this->parent => $id_member);
        if( !empty($position) ) $arr = array($this->parent => $id_member, 'position' => $position);
            
        $query = $this->db->get_where($this->member, $arr);
        
        if( $count ) return $query->num_rows();
        
        return $query->result();
    }
    
    /**
     * Get auto global node available
     * 
     * @author  Iqbal
     * @param   Int     $id_auto    (Required)  Auto Global ID
     * @param   Count   $count      (Optional)  Count of node
     * @return  Mixed   False on invalid auto global id, otherwise array of node available.
     */
    function get_auto_global_node_available($id_auto, $count=false){
        if ( !is_numeric($id_auto) ) return false;

        $id_auto  = absint($id_auto);
        if ( !$id_auto ) return false;
        
        $autoglobaldata = $this->get_autoglobaldata($id_auto);        
        if( !$autoglobaldata ) return false;
            
        $query = $this->db->get_where($this->auto_global, array($this->parent => $id_auto));
        
        if( $count ) return $query->num_rows();
        
        return $query->result();
    }
    
    /**
     * Get all member pin
     * 
     * @author  Iqbal
     * @param   Int     $id_member  (Required)  Member ID
     * @param   String  $status     (Optional)  Status of Pin, default 'all'
     * @param   Boolean $count      (Optional)  Count PIN, default 'false'
     * @param   String  $package    (Optional)  Package of PIN
     * @return  Mixed   False on invalid member id, otherwise array of all pin.
     */
    function get_pins($id_member, $status='all', $count=false, $package=''){
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $data       = array('id_member' => $id_member);

        if ( $status == 'active' ){
            $data['status'] = 1;
        } elseif ( $status == 'pending' ){
            $data['status'] = 0;
        } elseif ( $status == 'used' ){
            $data['status'] = 2;
        }
        
        if ( !empty($package) ) $data['package'] = $package;
        
        $this->db->where($data);
        $query = $this->db->get($this->pin);

        if( $count ){
            if ( $query->num_rows() > 0 )
                return $query->num_rows();
            
            return 0;
        }else{
            if ( !$query->num_rows() )
                return false;
            
            return $query->result();
        }
    }
    
    /**
     * Get all member pin order
     * 
     * @author  Iqbal
     * @param   Int     $id_member  (Required)  Member ID
     * @param   String  $status     (Optional)  Status of Pin, default 'all'
     * @return  Mixed   False on invalid member id, otherwise array of all pin order.
     */
    function get_pin_orders($id_member, $status='all'){
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $data       = array('id_member' => $id_member);

        if ( $status == 'confirmed' ){
            $data['status'] = 1;
        } elseif ( $status == 'review' ){
            $data['status'] = 0;
        }
        
        $this->db->where($data);
        
        $query = $this->db->get($this->pin_order);
        
        if ( !$query->num_rows() )
            return false;
        
        return $query->result();
    }
    
    /**
     * Get pin order
     * 
     * @author  Iqbal
     * @param   Int     $id_pin  (Required)  Pin ID
     * @return  Mixed   False on invalid pin id, otherwise array of pin order.
     */
    function get_pin_order($id_pin){
        if ( !is_numeric($id_pin) ) return false;

        $id_pin  = absint($id_pin);
        if ( !$id_pin ) return false;
        
        $data       = array($this->id => $id_pin);
        $this->db->where($data);
        
        $query      = $this->db->get($this->pin_order);
        
        if ( !$query->num_rows() )
            return false;
        
        return $query->row();
    }
    
    /**
     * Get member pin by id pin
     * 
     * @author  Iqbal
     * @param   Int     $pin_id  (Required)  Pin ID
     * @return  Mixed   False on invalid pin id, otherwise array of pin.
     */
    function get_pin_by_id($pin_id){
        if ( !is_numeric($pin_id) ) return false;

        $pin_id  = absint($pin_id);
        if ( !$pin_id ) return false;
        
        $query = $this->db->get_where($this->pin, array($this->id => $pin_id));
        if ( !$query->num_rows() )
            return false;

        foreach ( $query->result() as $row ) {
            $pin = $row;
        }
        
        return $pin;
    }
    
    /**
     * Get member pin by id order
     * 
     * @author  Iqbal
     * @param   Int     $pin_order_id  (Required)  Pin Order ID
     * @return  Mixed   False on invalid pin id, otherwise array of pin.
     */
    function get_pin_by_order($pin_order_id){
        if ( !is_numeric($pin_order_id) ) return false;

        $pin_order_id  = absint($pin_order_id);
        if ( !$pin_order_id ) return false;
        
        $sql    = '
        SELECT 
            A.*,
            B.username AS member_username,
            B.name AS member_name,
            C.username AS member_username_registered,
            C.name AS member_name_registered
        FROM '.$this->pin.' AS A
        LEFT JOIN '.$this->member.' AS B ON B.id = A.id_member
        LEFT JOIN '.$this->member.' AS C ON C.id = A.id_member_registered
        WHERE A.id_order_pin = '.$pin_order_id.'';
        
        $query = $this->db->query($sql);
        if ( !$query->num_rows() )
            return false;
        
        return $query->result();
    }
    
    /**
     * Get max id member
     * 
     * @author  Iqbal
     * @return  Int of max id.
     */
    function get_max_id(){
        $this->db->select_max($this->id);
        $query  = $this->db->get($this->member);
        
        $row    = $query->row();
        
        return $row->id;
    }
    
    /**
     * Get is dowline
     * 
     * @author  Iqbal
     * @param   Int     $id_member  (Required)  ID Member
     * @param   String  $up_tree    (Required)  Tree of upline
     * @return  Boolean false if invalid data, otherwise true if is downline.
     */
    function get_is_downline($id_member, $up_tree){
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        if ( empty($up_tree) || !$up_tree ) return false;
        
        $this->db->where($this->id, $id_member);
        $this->db->like('`tree`', $up_tree, 'after');
        $query  = $this->db->get($this->member);
        
        if ( $query->num_rows() > 0 ) 
            return true;
        
        return false;
    }
    
    /**
     * Get is dowline auto global
     * 
     * @author  Iqbal
     * @param   Int     $id_auto    (Required)  Auto Global ID
     * @param   String  $up_tree    (Required)  Tree of upline auto global
     * @return  Boolean false if invalid data, otherwise true if is downline.
     */
    function get_is_downline_auto_global($id_auto, $up_tree){
        if ( !is_numeric($id_auto) ) return false;

        $id_auto  = absint($id_auto);
        if ( !$id_auto ) return false;
        
        if ( empty($up_tree) || !$up_tree ) return false;
        
        $this->db->where($this->id, $id_auto);
        $this->db->like('`tree`', $up_tree, 'after');
        $query  = $this->db->get($this->auto_global);
        
        if ( $query->num_rows() > 0 ) 
            return true;
        
        return false;
    }
    
    /**
     * Get bonus data
     * 
     * @author  Iqbal
     * @param   Int     $id  (Required)  Bonus ID
     * @return  Mixed   False on invalid bonus id, otherwise array of bonus.
     */
    function get_bonusdata($id){
        if ( !is_numeric($id) ) return false;

        $id  = absint($id);
        if ( !$id ) return false;
        
        $data       = array($this->id => $id);
        $query      = $this->db->get_where($this->bonus, $data);

        if ( !$query->num_rows() )
            return false;
        
        return $query->row();
    }
    
    /**
     * Get downline data or count downline (child level 1)
     * 
     * @author  Iqbal
     * @param   Int     $id             (Required)  Member ID
     * @param   String  $group          (Optional)  Group of downline, default ''
     * @param   String  $status         (Optional)  Status of Downline, value ('active' or 'pending')
     * @param   Boolean $count          (Optional)  Get Count of downline
     * @return  Mixed   False on invalid member id, otherwise array of downline.
     */
    function get_downline($id_member, $position='', $status='', $count=false) {
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $this->db->where("parent", $id_member);
        if( !empty($status) ) $this->db->where("status", ( $status == 'active' ? 1 : 0 ) );
        
        if( !empty($position) ) $this->db->where("position", $position);
        $this->db->order_by("position", "DESC"); 
        
        $query      = $this->db->get($this->member);
        
        if ( $count ) return $query->num_rows();
        if ( !empty($position) ) return $query->row();
        
        return $query->result();        
    }
    
    /**
     * Get downline data or count downline (child level 1) of auto global
     * 
     * @author  Iqbal
     * @param   Int     $id_auto        (Required)  Auto Global ID
     * @param   String  $position       (Optional)  Position of downline, default ''
     * @param   Boolean $count          (Optional)  Get Count of downline
     * @return  Mixed   False on invalid auto global id, otherwise array of downline.
     */
    function get_downline_auto_global($id_auto, $position='', $count=false) {
        if ( !is_numeric($id_auto) ) return false;

        $id_auto  = absint($id_auto);
        if ( !$id_auto ) return false;
        
        $this->db->select('A.*,B.username,B.name');
        $this->db->from($this->auto_global . ' AS A');
        $this->db->join($this->member . ' AS B', 'B.id = A.id_member');
        $this->db->where("A.parent", $id_auto);
        
        if( !empty($position) ) $this->db->where("A.position", $position);
        $this->db->order_by("A.position", "DESC");
        
        $query = $this->db->get();
        
        if ( $count ) return $query->num_rows();
        if ( !empty($position) ) return $query->row();
        
        return $query->result();        
    }
    
    /**
     * Get downline data or count downline (child level 1) of auto-cbi
     * 
     * @author  Iqbal
     * @param   Int     $id_autocbi     (Required)  Auto-CBI ID
     * @param   String  $position       (Optional)  Position of downline, default ''
     * @param   Boolean $count          (Optional)  Get Count of downline
     * @return  Mixed   False on invalid auto-cbi id, otherwise array of downline.
     */
    function get_downline_autocbi($id_autocbi, $position='', $count=false, $board=0) {
        if ( !is_numeric($id_autocbi) ) return false;

        $id_autocbi  = absint($id_autocbi);
        if ( !$id_autocbi ) return false;
        
        $this->db->select('*');
        $this->db->from($this->autocbi);
        $this->db->where("parent", $id_autocbi);
        
        if( !empty($position) ) $this->db->where("position", $position);
        if( $board > 0 ) $this->db->where( $board == BOARD_CBI_TWO ? "(board = ".BOARD_CBI_TWO." OR board = ".BOARD_CBI_THREE.")" : "(board = ".$board.")"  );
        
        $this->db->order_by("position", "DESC");
        
        $query = $this->db->get();
        
        if ( $count ) return $query->num_rows();
        if ( !empty($position) ) return $query->row();
        
        return $query->result();        
    }
    
    /**
     * Get downline data or count downline (child level 1) of auto-cbi by board
     * 
     * @author  Iqbal
     * @param   Int     $id_autocbi     (Required)  Auto-CBI ID
     * @param   Boolean $board          (Optional)  Board of downline
     * @return  Mixed   False on invalid auto-cbi id, otherwise array of downline.
     */
    function get_downline_autocbi_by_board($id_autocbi, $board) {
        if ( !is_numeric($id_autocbi) ) return false;
        if ( !is_numeric($board) ) return false;

        $id_autocbi  = absint($id_autocbi);
        if ( !$id_autocbi ) return false;
        
        $board  = absint($board);
        if ( !$board ) return false;
        
        $down   = 0;
        $sql    = '
        SELECT COUNT(id) AS total FROM '.$this->autocbi.' 
        WHERE parent = ? AND (type = "'.CBI_TYPE_POINT.'" OR (type = "'.CBI_TYPE_MEMBER.'" AND board = ?))';

        $query = $this->db->query($sql, array($id_autocbi, $board));
        if ( !$query->num_rows() )
            return $down;
        
        $down   = $query->row()->total;
        return $down;
    }
    
    /**
     * Get count of node
     * 
     * @author  Iqbal
     * @param   Int     $id             (Required)  Member ID
     * @param   String  $group          (Optional)  Position of downline, default ''
     * @param   Booleah $count          (Optional)  To Get count or list of member
     * @return  Mixed   False on invalid member id, otherwise count or array of node.
     */
    function get_countnode($id_member, $group='', $count=true) {
        if ( !is_numeric($id_member) ) return false;

        $id_member      = absint($id_member);
        if ( !$id_member ) return false;
        
        $down           = erp_downline($id_member,$group,'active');
        
        if ( !empty($down) ){
            $tree       = $down->tree;
            $this->db->like('tree', $tree, 'after'); 
            $this->db->where('tree !=',$tree);
            $query      = $this->db->get($this->member);
            
            if ( $count ) return $query->num_rows();
            return $query->result();  
        }
        return 0;
    }
    
    /**
	 * Get member sponsored by another member
     * @param   Int     $id_members     Array of member ID
	 * @author	Iqbal
	 */
	function get_sponsored_by($id_members) {
        if ( !is_array($id_members) ) return false;
		
		$sql = '
            SELECT 
                A.*, 
                B.name AS sponsor_name, 
                B.username As sponsor_username
			FROM '.$this->member.' A
			INNER JOIN '.$this->member.' B ON B.id = A.sponsor 
			WHERE A.status = 1 AND A.sponsor IN (' . implode(',', $id_members) . ')
			ORDER BY B.id, A.id';
		$qry = $this->db->query($sql);
		
		if (!$qry || !$qry->num_rows()) return false;
		return $qry->result();
	}
    
	
    /**
     * Count members poin pairing
     * 
     * @author  Ahmad
     * @param   Int     $id                 (Required)  Member ID
     * @param   String  $position           (Optional)  Position of downline, default ''
     * @param   Boolean $only_non_qualified (Optional)  Get only qualified if true
     * @return  Mixed   False on invalid member id, otherwise count or array of invest.
     */
    function count_cashreward($id_member, $position='', $only_non_qualified=true) {
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
		
		$childs = 0;
        
        if ( !$member = $this->get_memberdata($id_member) ) return false;
        
        $childs = absint($member->{$position . '_cashrewardpoin'});
                
		if (!$only_non_qualified || empty($childs)) return $childs;

        $sql    = 'SELECT SUM(qualified) total_qualified FROM '.$this->cashreward_qualified.' WHERE id_member=?';
        $qry    = $this->db->query($sql, array($id_member));

		if ( empty($qry->row()->total_qualified) || $qry->row()->total_qualified == 0 ) return $childs;
		
        $row    = $qry->row();
        $p      = $childs - absint($row->total_qualified);
        $p      = ( $p < 0 ? 0 : $p );
		return $p;
    }
    
	/**
     * Count members child
     * 
     * @author  Ahmad
     * @param   Int     $id             (Required)  Member ID
     * @param   String  $position       (Optional)  Position of downline, default ''
     * @param   Boolean $count          (Optional)  To Get count or list of member
     * @param   Int     $status         (Optional)  Status of investment
     * @return  Mixed   False on invalid member id, otherwise count or array of invest.
     */
    function count_childs($id_member, $position='', $only_non_qualified=true, $pairing=FALSE) {
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
		
		$childs = 0;
        
        $sql    = 'SELECT * FROM '.$this->member_tree.' WHERE id_member=?';
        $qry    = $this->db->query($sql, array($id_member));
		
		if (!$qry || !$qry->num_rows()) return $childs;
        
        $row    = $qry->row();
        
        if ($pairing){
            if( !empty($row->{$position . '_nodes'}) ){
                // Calculate Package
        		$sql = 'SELECT package_old, COUNT(package_old) AS member_count FROM '.$this->member.' WHERE id IN (' . $row->{$position . '_nodes'} . ') GROUP BY package_old';
        		$qry = $this->db->query($sql);

        		if ($qry && $qry->num_rows()) {
        			$cfg_poin = config_item('poin');    			
        			foreach($qry->result() as $row) {
        				if (empty($cfg_poin[$row->package_old])) continue;
        				$childs += $cfg_poin[$row->package_old] * absint($row->member_count);
        			}
        		}
            }
        }else{
            $childs = absint($row->{$position . '_count'});
        }

		if (!$only_non_qualified || empty($childs)) return $childs;

        $sql    = 'SELECT SUM(qualified) total_qualified FROM '.$this->bonus_qualified.' WHERE id_member=?';
        $qry    = $this->db->query($sql, array($id_member));

		if ( empty($qry->row()->total_qualified) || $qry->row()->total_qualified == 0 ) return $childs;
		
        $row    = $qry->row();
        $p      = $childs - absint($row->total_qualified);
        $p      = ( $p < 0 ? 0 : $p );
		return $p;
    }
    
    /**
     * Count members child
     * 
     * @author  Ahmad
     * @param   Int     $id             (Required)  Member ID
     * @param   String  $position       (Optional)  Position of downline, default ''
     * @param   Boolean $count          (Optional)  To Get count or list of member
     * @param   Int     $status         (Optional)  Status of investment
     * @return  Mixed   False on invalid member id, otherwise count or array of invest.
     */
    function count_childs_all($id_member, $position='', $only_non_qualified=true, $as_sponsor=FALSE) {
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
		
		$childs = 0;
		
        $sql    = 'SELECT * FROM erp_member_tree WHERE id_member=?';
        $qry    = $this->db->query($sql, array($id_member));
		
		if (!$qry || !$qry->num_rows()) return $childs;
		
        $row    = $qry->row();
        $childs = absint($row->{$position . '_count'});
		
		if (!$only_non_qualified || empty($childs)) return $childs;
        
		return $childs;
    }
    
    /**
     * Count auto global child
     * 
     * @author  Iqbal
     * @param   Int     $id_auto        (Required)  Auto Global ID
     * @param   String  $position       (Required)  Position of downline
     * @return  Mixed   False on invalid auto global id, otherwise count or array of invest.
     */
    function count_childs_auto_global($id_auto, $position) {
        if ( !is_numeric($id_auto) ) return false;
        if ( !$position ) return false;

        $id_auto  = absint($id_auto);
        if ( !$id_auto ) return false;
		
		$childs = 0;
		
        $sql    = 'SELECT * FROM '.$this->auto_global_tree.' WHERE id_auto=?';
        $qry    = $this->db->query($sql, array($id_auto));
		
		if (!$qry || !$qry->num_rows()) return $childs;
		
        $row    = $qry->row();
        $childs = absint($row->{$position . '_count'});

		return $childs;
    }
    
    /**
     * Count auto-cbi child
     * 
     * @author  Iqbal
     * @param   Int     $id_auto        (Required)  Auto-CBI ID
     * @param   String  $position       (Required)  Position of downline
     * @return  Mixed   False on invalid auto-cbi id, otherwise count or array of childs.
     */
    function count_childs_autocbi($id_autocbi, $position) {
        if ( !is_numeric($id_autocbi) ) return false;
        if ( !$position ) return false;

        $id_autocbi  = absint($id_autocbi);
        if ( !$id_autocbi ) return false;
		
		$childs = 0;
		
        $sql    = 'SELECT * FROM '.$this->autocbi_tree.' WHERE id_autocbi=?';
        $qry    = $this->db->query($sql, array($id_autocbi));
		
		if (!$qry || !$qry->num_rows()) return $childs;
        
        if ( $position == POS_CBI_ONE )         { $position = 'one'; }
        elseif ( $position == POS_CBI_TWO )     { $position = 'two'; }
        elseif ( $position == POS_CBI_THREE )   { $position = 'three'; }
        elseif ( $position == POS_CBI_FOUR )    { $position = 'four'; }
		
        $row    = $qry->row();
        $childs = absint($row->{$position . '_count'});

		return $childs;
    }
    
    /**
     * Count auto-cbi child CBI-2 and CBI-3
     * 
     * @author  Iqbal
     * @param   Int     $id_auto        (Required)  Auto-CBI ID
     * @param   String  $position       (Required)  Position of downline
     * @param   Int     $board          (Required)  Board of CBI
     * @return  Mixed   False on invalid auto-cbi id, otherwise count or array of childs.
     */
    function count_childs_autocbi_up($id_autocbi, $position, $board) {
        if ( !is_numeric($id_autocbi) ) return false;
        if ( !is_numeric($board) ) return false;
        if ( !$position ) return false;

        $id_autocbi  = absint($id_autocbi);
        if ( !$id_autocbi ) return false;
        
        $board  = absint($board);
        if ( !$board ) return false;
		
		$childs = 0;
		
        $sql    = 'SELECT * FROM '.$this->autocbi_tree.' WHERE id_autocbi=?';
        $qry    = $this->db->query($sql, array($id_autocbi));
		
		if (!$qry || !$qry->num_rows()) return $childs;
        
        if ( $position == POS_CBI_ONE )         { $position = 'one'; }
        elseif ( $position == POS_CBI_TWO )     { $position = 'two'; }
        elseif ( $position == POS_CBI_THREE )   { $position = 'three'; }
        elseif ( $position == POS_CBI_FOUR )    { $position = 'four'; }
		
        $row    = $qry->row();
        $nodes  = $row->{$position . '_nodes'};
        
        if( empty($nodes) ) return $childs;

        $sql_tree   = 'SELECT id,board FROM '.$this->autocbi.' WHERE id IN('.$nodes.')';
        $qry_tree   = $this->db->query($sql_tree);
        if (!$qry_tree || !$qry_tree->num_rows()) return $childs;

        foreach( $qry_tree->result() as $node ){
            if( $node->board == $board ) $childs += 1;
        }
		return $childs;
    }
    
    /**
     * Count members poin pairing
     * 
     * @author  Ahmad
     * @param   Int     $id                 (Required)  Member ID
     * @param   String  $position           (Optional)  Position of downline, default ''
     * @param   Boolean $only_non_qualified (Optional)  Get only qualified if true
     * @return  Mixed   False on invalid member id, otherwise count or array of invest.
     */
    function count_poin_pairing($id_member, $position='', $only_non_qualified=true) {
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
		
		$childs = 0;
        
        if ( !$member = $this->get_memberdata($id_member) ) return false;
        
        $childs = absint($member->{$position . '_poin'});
                
		if (!$only_non_qualified || empty($childs)) return $childs;

        $sql    = 'SELECT SUM(qualified) total_qualified FROM '.$this->bonus_qualified.' WHERE id_member=?';
        $qry    = $this->db->query($sql, array($id_member));

		if ( empty($qry->row()->total_qualified) || $qry->row()->total_qualified == 0 ) return $childs;
		
        $row    = $qry->row();
        $p      = $childs - absint($row->total_qualified);
        $p      = ( $p < 0 ? 0 : $p );
		return $p;
    }
    
    /**
     * Count members poin reward auto global
     * 
     * @author  Iqbal
     * @param   Int     $id             (Required)  Member ID
     * @param   String  $position       (Required)  Position of downline
     * @return  Mixed   False on invalid member id, otherwise count or array of point.
     */
    function count_poin_reward_auto($id_member, $position) {
        if ( !is_numeric($id_member) ) return false;
        if ( !$position ) return false;
        
        $poinreward     = 0;
        $id_member      = absint($id_member);
        if ( !$id_member ) return $poinreward;
        
        $memberdata     = $this->get_memberdata($id_member);
        if ( !$memberdata ) return $poinreward;
        
        $autoglobaldata = $this->get_auto_global_by('member',$id_member);
        if ( !$autoglobaldata ) return $poinreward;
        
        $poinreward     = erp_count_childs_auto_global($autoglobaldata->id,$position);
        return $poinreward;
    }
	
    /**
     * Count members poin reward
     * 
     * @author  Iqbal
     * @param   Int     $id             (Required)  Member ID
     * @param   String  $position       (Optional)  Position of downline, default ''
     * @param   String  $package        (Optional)  Package of Member
     * @param   Boolean $as_sponsor     (Optional)  Get only member sponsored
     * @return  Mixed   False on invalid member id, otherwise count or array of point.
     */
    function count_poin_reward($id_member, $position='', $package='', $as_sponsor=FALSE) {
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
		
        $point  = 0;
		
        $sql    = 'SELECT * FROM '.$this->member_tree.' WHERE id_member=?';
        $qry    = $this->db->query($sql, array($id_member));
		
		if (!$qry || !$qry->num_rows()) return $point;
		
        $row    = $qry->row();
        $point  = absint($row->{$position . '_count'});
        
        if ($package) {
			$point = 0;
			
			// Means nodes count for particular member type
			$sql = 'SELECT package_old, COUNT(package_old) AS member_count FROM '.$this->member.' WHERE id IN (' . $row->{$position . '_nodes'} . ') AND package_old=?';
			if ($as_sponsor) $sql .= ' AND sponsor=?';
			$sql .= ' GROUP BY package_old';
			$qry = $this->db->query($sql, array($package, $id_member));
			
			if (!$qry || !$qry->num_rows()) return $point;
			return absint($qry->row()->member_count);
		}
        
        if( !empty($row->{$position . '_nodes'}) ){
            // Calculate points
    		$sql = 'SELECT package_old, COUNT(package_old) AS member_count FROM '.$this->member.' WHERE id IN (' . $row->{$position . '_nodes'} . ') GROUP BY package_old';
    		$qry = $this->db->query($sql);
    		
    		if ($qry && $qry->num_rows()) {
    			$cfg_point = config_item('happypoin');
    			$point = 0;
    			
    			foreach($qry->result() as $row) {
    				if (empty($cfg_point[$row->package_old])) continue;
    				$point += $cfg_point[$row->package_old] * absint($row->member_count);
    			}
    		}
        }
		return $point;
    }
    
	/**
     * Count members child
     * 
     * @author  Ahmad
     * @param   Int     $id             (Required)  Member ID
     * @param   Boolean $count          (Optional)  To Get count or list of member
     * @param   Int     $status         (Optional)  Status of investment
     * @return  Mixed   False on invalid member id, otherwise count or array of invest.
     */
    function markas_qualified($id_member, $child_number, $datetime='') {
        if ( !is_numeric($id_member) ) return false;

        $id_member  	= absint($id_member);
		$child_number  	= absint($child_number);
        if ( !$id_member || !$child_number ) return false;
        if ( !$datetime ) $datetime = date('Y-m-d H:i:s');
		
		$data = array(
			'id_member' 	=> $id_member,
			'qualified' 	=> $child_number,
			'datecreated'	=> $datetime,
		);
		$this->db->insert('erp_bonus_qualified', $data);
    }
    
    /**
     * Get count today member registered
     * 
     * @author  Iqbal
     * @param   String  $date   (Required)  Today date
     * @return  Mixed   False on invalid date parameter, otherwise count of member.
     */
    function get_counttoday($date) {
        if ( !$date ) return false;
        
        $this->db->where('type !=', 2);
        $this->db->like('datecreated', $date, 'after');
        $query      = $this->db->get($this->member);
        $count      = $query->num_rows();
        
        return $count;  
    }
    
    /**
     * Get banks
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  ID of bank
     * @return  Mixed   False on invalid date parameter, otherwise data of bank(s).
     */
    function get_banks($id=''){
        if ( !empty($id) ) { 
            $id = absint($id); 
            $this->db->where('id', $id);
        };
        
        $this->db->order_by("kode", "ASC"); 
        $query      = $this->db->get($this->bank);        
        return ( !empty($id) ? $query->row() : $query->result() );
    }
    
    /**
     * Get banks
     * 
     * @author  Iqbal
     * @param   String      $code   (Required)  Code of bank
     * @return  Mixed   False on invalid date parameter, otherwise data of bank(s).
     */
    function get_bank_by_code($code){
        if ( !$code ) return false;
        $this->db->where('kode', $code);
        $query      = $this->db->get($this->bank);        
        return $query->row();
    }
    
    /**
     * Get Rewards Option
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  ID of reward option
     * @return  Mixed   False on invalid date parameter, otherwise data of bank(s).
     */
    function get_rewards_option($id=''){
        if ( !empty($id) ) { 
            $id = absint($id); 
            $this->db->where('reward_id', $id);
        };
        
        $this->db->order_by("reward_id", "ASC"); 
        $query      = $this->db->get($this->reward_option);        
        return ( !empty($id) ? $query->row() : $query->result() );
    }
    
    /**
     * Get Cash Rewards Option
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  ID of reward option
     * @return  Mixed   False on invalid date parameter, otherwise data of bank(s).
     */
    function get_cashrewards_option($id=''){
        if ( !empty($id) ) { 
            $id = absint($id); 
            $this->db->where('reward_id', $id);
        };
        
        $this->db->order_by("reward_id", "ASC"); 
        $query      = $this->db->get($this->cashreward_option);        
        return ( !empty($id) ? $query->row() : $query->result() );
    }
    
    /**
	 * Get ancestry of member
	 * @author	Ahmad
	 */
	function get_ancestry($id_member) {
		$id_member = absint($id_member);
        if ( !$id_member ) return false;
		
		$sql = 'SELECT GetAncestry(id) AS ancestry FROM '.$this->member.' WHERE id=?';
		$qry = $this->db->query($sql, array($id_member));
		
		if (!$qry || !$qry->num_rows()) return false;
		return $qry->row()->ancestry;
	}
    
    /**
	 * Get ancestry sponsor of member
	 * @author	Ahmad
	 */
	function get_ancestry_sponsor($id_member) {
		$id_member = absint($id_member);
        if ( !$id_member ) return false;
		
		$sql = 'SELECT GetAncestrySponsor(id) AS ancestry FROM '.$this->member.' WHERE id=?';
		$qry = $this->db->query($sql, array($id_member));
		
		if (!$qry || !$qry->num_rows()) return false;
		return $qry->row()->ancestry;
	}
    
    /**
	 * Get ancestry of auto global
	 * @author	Iqbal
	 */
	function get_ancestry_auto_global($id_auto_global) {
		$id_auto_global = absint($id_auto_global);
        if ( !$id_auto_global ) return false;
		
		$sql = 'SELECT GetAncestryAutoGlobal(id) AS ancestryauto FROM '.$this->auto_global.' WHERE id=?';
		$qry = $this->db->query($sql, array($id_auto_global));
		
		if (!$qry || !$qry->num_rows()) return false;
		return $qry->row()->ancestryauto;
	}
    
    /**
	 * Get ancestry of auto-cbi
	 * @author	Iqbal
	 */
	function get_ancestry_autocbi($id_autocbi) {
		$id_autocbi = absint($id_autocbi);
        if ( !$id_autocbi ) return false;
		
		$sql = 'SELECT GetAncestryAutoCBI(id) AS ancestryauto FROM '.$this->autocbi.' WHERE id=?';
		$qry = $this->db->query($sql, array($id_autocbi));
		
		if (!$qry || !$qry->num_rows()) return false;
		return $qry->row()->ancestryauto;
	}
    
    /**
     * Save data of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of member
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data($data){
        if( empty($data) ) return false;
        if( $this->db->insert($this->member,$data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Save data of member confirmation
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of member confirmation
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_confirm($data){
        if( empty($data) ) return false;
        if( $this->db->insert($this->member_confirm,$data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Save data bonus of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of bonus
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_bonus($data){
        if( empty($data) ) return false;
        
		$bonus_id = 0;
        if( $this->db->insert($this->bonus, $data) ) {
            $bonus_id = $this->db->insert_id();
        };
        
        if ( erp_autocbi() ){
            $id_member  = $data['id_member'];
            $amount     = $data['amount'];
            $desc       = 'Auto-CBI Fees of Bonus ' . $data['id_bonus'];
    		$this->calc_autocbi_fees( $id_member, $amount, $bonus_id, $desc );
        }
        
		return $bonus_id;
    }
    
    /**
     * Save data pin of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of pin
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_pin($data){
        if( empty($data) ) return false;
        
        if( $this->db->insert($this->pin, $data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Save data pin order of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of pin order
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_pin_order($data){
        if( empty($data) ) return false;
        
        if( $this->db->insert($this->pin_order, $data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Save data pin transfer of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of pin transer
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_pin_tansfer($data){
        if( empty($data) ) return false;
        
        if( $this->db->insert($this->pin_transfer, $data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Save data pin reward of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of reward
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_reward($data){
        if( empty($data) ) return false;
        
        if( $this->db->insert($this->reward, $data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Save data pin reward of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of reward
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_cashreward($data){
        if( empty($data) ) return false;
        
        if( $this->db->insert($this->cashreward, $data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Save data pin withdraw of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of withdraw
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_withdraw($data){
        if( empty($data) ) return false;
        
        if( $this->db->insert($this->withdraw, $data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Save data of auto global
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of auto global
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_auto_global($data){
        if( empty($data) ) return false;
        if( $this->db->insert($this->auto_global,$data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Save data of taxes
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data taxes
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_tax( $data ) {
		if ( empty( $data ) ) return false;
		
		$sql = '
            INSERT IGNORE INTO '.$this->taxes.'(
                `id_member`, 
                `period`, 
                `period_month`, 
                `period_year`, 
                `sequence_num`, 
                `total_nominal`, 
                `total_tax`, 
                `total_received`, 
                `datecreated` 
            ) SELECT 
                ?, 
                ?, 
                ?, 
                ?, 
                ( IFNULL( MAX(sequence_num), 0 ) + 1 ), 
                ?, 
                ?, 
                ?,  
                ? 
            FROM '.$this->taxes.' WHERE `period_year` = ?
  			ON DUPLICATE KEY UPDATE 
                `total_nominal` = VALUES( `total_nominal` ),  
                `total_tax` = VALUES( `total_tax` ), 
                `total_received` = VALUES( `total_received` )';
		
		$param = array(
			$data['id_member'],
			$data['period'],
			$data['period_month'],
			$data['period_year'],
			$data['total_nominal'],
			$data['total_tax'],
			$data['total_received'],
			$data['datecreated'],
			// where
			$data['period_year']
		);		
		$this->db->query( $sql, $param );
	}
    
    /**
     * Save data Auto-CBI of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of Auto-CBI
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_autocbi($data){
        if( empty($data) ) return false;
		
		// We have UNIQUE index on this table so we can't use Active Record to do insert
		$sql = 'INSERT IGNORE INTO '.$this->autocbi.'(`' . implode('`,`', array_keys($data)) . '`)
	            VALUES(' . rtrim(str_repeat('?,', count($data)), ',') . ')';
		
		$data_values = array_values($data);
        $this->db->query($sql, $data_values);
		
		if ($this->db->affected_rows()) {
			$id = $this->db->insert_id();
            return $id;
		}
        return false;
    }
    
    /**
     * Save data Auto-CBI Fees of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of Auto-CBI Fees
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_autocbi_fees($data){
        if( empty($data) ) return false;
		
		// We have UNIQUE index on this table so we can't use Active Record to do insert
		$sql = 'INSERT IGNORE INTO '.$this->autocbi_fees.'(`' . implode('`,`', array_keys($data)) . '`)
	            VALUES(' . rtrim(str_repeat('?,', count($data)), ',') . ')';
		
		$data_values = array_values($data);
        $this->db->query($sql, $data_values);
		
		if ($this->db->affected_rows()) {
			$id = $this->db->insert_id();
            return $id;
		}
        return false;
    }
    
    /**
     * Save data Auto-CBI bonus of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of Auto-CBI bonus
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_autocbi_bonus($data){
        if( empty($data) ) return false;
        
		$autocbi_bonus_id = 0;
        if( $this->db->insert($this->autocbi_bonus, $data) ) {
            $autocbi_bonus_id = $this->db->insert_id();
        };
 
		return $autocbi_bonus_id;
    }
    
    /**
     * Save data Auto-CBI Board Process of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of Auto-CBI Board Process
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_autocbi_board($data){
        if( empty($data) ) return false;
		
		// We have UNIQUE index on this table so we can't use Active Record to do insert
		$sql = 'INSERT IGNORE INTO '.$this->autocbi_board.'(`' . implode('`,`', array_keys($data)) . '`)
	            VALUES(' . rtrim(str_repeat('?,', count($data)), ',') . ')';
		
		$data_values = array_values($data);
        $this->db->query($sql, $data_values);
		
		if ($this->db->affected_rows()) {
			$id = $this->db->insert_id();
            return $id;
		}
        return false;
    }
    
    /**
     * Save data Auto-CBI Poin Process of member
     * 
     * @author  Iqbal
     * @param   Array   $data   (Required)  Array data of Auto-CBI Poin Process
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function save_data_autocbi_poin($data){
        if( empty($data) ) return false;
		
		// We have UNIQUE index on this table so we can't use Active Record to do insert
		$sql = 'INSERT INTO '.$this->autocbi_poin.'(`' . implode('`,`', array_keys($data)) . '`)
	            VALUES(' . rtrim(str_repeat('?,', count($data)), ',') . ')';
		
		$data_values = array_values($data);
        $this->db->query($sql, $data_values);
		
		if ($this->db->affected_rows()) {
			$id = $this->db->insert_id();
            return $id;
		}
        return false;
    }
    
    /**
     * Update data of member
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Member ID
     * @param   Array   $data   (Required)  Array data of user
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_data($id, $data){
        if(empty($id) || empty($data)) 
            return false;
            
        $this->db->where($this->id, $id);
        if( $this->db->update($this->member, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update data of confirm member
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Confirm Member ID
     * @param   Array   $data   (Required)  Array data of confirm member
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_data_confirm($id, $data){
        if(empty($id) || empty($data)) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->update($this->member_confirm, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update data of bonus
     * 
     * @author  Iqbal
     * @param   Int     $id         (Required)  Bonus ID
     * @param   Array   $data       (Required)  Array data of bonus
     * @param   Array   $condition  (Optional)  Array data of bonus condition
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_data_bonus($id, $data, $condition=array()){
        if(empty($id) || empty($data)) 
            return false;

        $this->db->where($this->id, $id);
        if( !empty($condition) ) { $this->db->where($condition); }
        if( $this->db->update($this->bonus, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update data of reward
     * 
     * @author  Iqbal
     * @param   Int     $id         (Required)  Reward ID
     * @param   Array   $data       (Required)  Array data of reward
     * @param   Array   $condition  (Optional)  Array data of reward condition
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_data_reward($id, $data, $condition=array()){
        if(empty($id) || empty($data)) 
            return false;

        $this->db->where($this->id, $id);
        if( !empty($condition) ) { $this->db->where($condition); }
        if( $this->db->update($this->reward, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update data of withdraw
     * 
     * @author  Iqbal
     * @param   Int     $id         (Required)  Withdraw ID
     * @param   Array   $data       (Required)  Array data of withdraw
     * @param   Array   $condition  (Optional)  Array data of withdraw condition
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_data_withdraw($id, $data, $condition=array()){
        if(empty($id) || empty($data)) 
            return false;

        $this->db->where($this->id, $id);
        if( !empty($condition) ) { $this->db->where($condition); }
        
        if( $this->db->update($this->withdraw, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update data of auto global
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Auto Global ID
     * @param   Array   $data   (Required)  Array data of auto global
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_data_auto_global($id, $data){
        if(empty($id) || empty($data)) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->update($this->auto_global, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update data of auto-cbi
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Auto-CBI ID
     * @param   Array   $data   (Required)  Array data of auto-cbi
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_data_autocbi($id, $data){
        if(empty($id) || empty($data)) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->update($this->autocbi, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update pin
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Pin ID
     * @param   Array   $data   (Required)  Data Pin ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_pin($id, $data){
        if( !$id || empty($id) ) return false;
        if( !$data || empty($data) ) return false;
        
        if ( is_array($id) ) $this->db->where_in($this->id, $id);
		else $this->db->where($this->id, $id);

        if( $this->db->update($this->pin, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update pin used
     * 
     * @author  Iqbal
     * @param   Int     $id         (Required)  Pin ID
     * @param   Int     $id_member  (Required)  Member ID
     * @param   Int     $register   (Required)  Register Member ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_pin_used($id, $id_member=0, $register=0){
        if( empty($id) ) 
            return false;
        
        $data = array(
            'status'                => 2,
            'id_member_register'    => $register,
            'id_member_registered'  => $id_member,
            'datemodified'          => date('Y-m-d H:i:s')
        );

        $this->db->where($this->id, $id);
        if( $this->db->update($this->pin, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update pin active
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Pin ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_pin_active($id){
        if( empty($id) ) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->update($this->pin, array('status' => 1)) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update pin order
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Pin Order ID
     * @param   Array   $data   (Required)  Data Pin Order ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_pin_order($id, $data){
        if( !$id || empty($id) ) return false;
        
        if( !$data || empty($data) ) return false;

        $this->db->where($this->id, $id);
        if( $this->db->update($this->pin_order, $data) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update pin order confirmed
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Pin Order ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_pin_order_confirmed($id){
        if( empty($id) ) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->update($this->pin_order, array('status' => 1)) ) 
            return true;
            
        return false;
    }
    
    /**
     * Update member confirm confirmed
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Member Confirm ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function update_member_confirm_confirmed($id){
        if( empty($id) ) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->update($this->member_confirm, array('status' => 1)) ) 
            return true;
            
        return false;
    }
    
    /**
	 * Update member tree for member
	 * @param	$id_member	Member ID (pass 0 to update all members tree)
	 * @author	Iqbal
	 */
	function update_member_tree($id_member=0) {
		$id_member    = absint($id_member);
		$member_data  = array();
		$id_member_in = $id_member;
		if ($id_member_in) {
			if ($ancestry = erp_ancestry($id_member)) $id_member_in .= ',' . $ancestry;
		}
        
        $sql = '
            SELECT 
                T1.id, 
                T1.parent, 
                T1.position, 
                IFNULL(T2.id, 0) AS "left", 
                IFNULL(T3.id, 0) AS "right", 
        		IFNULL(T4.left_nodes, "") AS "left_nodes", 
                IFNULL(T4.right_nodes, "") AS "right_nodes", 
                GetAncestry(T1.id) AS ancestry
			FROM '.$this->member.' T1
			LEFT JOIN '.$this->member.' T2 ON T2.parent = T1.id AND T2.position=?
			LEFT JOIN '.$this->member.' T3 ON T3.parent = T1.id AND T3.position=?
			LEFT JOIN '.$this->member_tree.' T4 ON T4.id_member = T1.id
			WHERE T1.`status` = 1';
		
		if ($id_member_in) $sql .= ' AND T1.id IN (' . $id_member_in . ')';
		
		$qry = $this->db->query($sql, array(POS_KIRI, POS_KANAN));
        
		if ($qry && $qry->num_rows()) {
			foreach($qry->result() as $row) {
				$id = absint($row->id);
				$member_data[$id] = $row;
			}
		}
		
		krsort($member_data);
		
		// to build the tree we need to start from tails
		// tails are any node without both left and right
		$tails = array();
		foreach($member_data as $id => $data) {
			if (!empty($data->left) || !empty($data->right)) continue;
			$tails[] = $id;
		}
		
		// if update member partial and member is not a tail, return false
		if ($id_member && !in_array($id_member, $tails)) return false;
		
		// we are now building the tree
		$member_tree = array();
		foreach($tails as $id) {
            $member_tree[$id]   = array(
                'left_nodes' 	=> array(),
                'right_nodes' 	=> array(),
			);
			
			$child_id = $id;
			$current_id = $member_data[$id]->parent;
			
			while ($current_id>0) {
				if (!isset($member_tree[$current_id])) {
					$member_tree[$current_id] = array(
						'left_nodes' 	=> array_filter(explode(',', $member_data[$current_id]->left_nodes)),
						'right_nodes' 	=> array_filter(explode(',', $member_data[$current_id]->right_nodes)),
					);
				}
				
				$pos = trim( $member_data[$child_id]->position );
				
				// if this id already exists on parent skip process
				if (in_array($id, $member_tree[$current_id][$pos . '_nodes'])) break;
				
				$child_left     = $member_tree[$child_id]['left_nodes'];
				$child_right    = $member_tree[$child_id]['right_nodes'];
				
				$child_nodes    = array_unique(array_merge($child_left, $child_right));
				$child_nodes[]  = $child_id;
				$current_nodes  = $member_tree[$current_id][$pos . '_nodes'];
				$member_tree[$current_id][$pos . '_nodes'] = array_unique(array_merge($current_nodes, $child_nodes));
                
                sort($member_tree[$current_id][$pos . '_nodes']);
                
                // we need to update the member data so the next iteration uses latest tree nodes
				$member_data[$current_id]->{$pos . '_nodes'} = implode(',', $member_tree[$current_id][$pos . '_nodes']);
				
				unset($pos);
				unset($child_left);
				unset($child_right);
				unset($child_nodes);
				unset($current_nodes);
				
				$child_id = $current_id;
				$current_id = $member_data[$current_id]->parent;
			}
			
			unset($child_id);
			unset($current_id);
		}

		if (empty($member_tree)) return false;
		ksort($member_tree);
		
		$sql  = 'INSERT IGNORE INTO '.$this->member_tree.' VALUES';
		foreach($member_tree as $id => $tree) {
			$left_nodes  = implode(',', $tree['left_nodes']);
			$right_nodes = implode(',', $tree['right_nodes']);
			
			$values = array(
				$id,
				'"' . $left_nodes . '"',
				'"' . $right_nodes . '"',
				count($tree['left_nodes']),
				count($tree['right_nodes']),
				'"' . $member_data[$id]->ancestry . '"'
			);
			$sql .= '(' . implode(',', $values) . '),';
			
			unset($left_nodes);
			unset($right_nodes);
			unset($values);
		}
		
		$sql = rtrim($sql, ',') . '
			ON DUPLICATE KEY UPDATE 
			left_nodes  = VALUES(left_nodes),
			right_nodes = VALUES(right_nodes),
			left_count  = VALUES(left_count),
			right_count = VALUES(right_count),
			ancestry    = VALUES(ancestry)
		';
		
		return $this->db->query($sql);
	}
    
    /**
	 * Update auto global tree of member
	 * @param	$id_auto_global  Auto Global ID  (pass 0 to update all auto global tree)
	 * @author	Iqbal
	 */
	function update_auto_global_tree($id_auto_global=0) {
		$id_auto_global       = absint($id_auto_global);
		$auto_global_data     = array();
		$id_auto_global_in    = $id_auto_global;
		if ($id_auto_global_in) {
			if ($ancestry = erp_ancestry_auto_global($id_auto_global)) $id_auto_global_in .= ',' . $ancestry;
		}

        $sql = '
            SELECT 
                T1.id, 
                T1.parent, 
                T1.position, 
                IFNULL(T2.id, 0) AS "left", 
                IFNULL(T3.id, 0) AS "right", 
        		IFNULL(T4.left_nodes, "") AS "left_nodes", 
                IFNULL(T4.right_nodes, "") AS "right_nodes", 
                GetAncestryAutoGlobal(T1.id) AS ancestry
			FROM '.$this->auto_global.' T1
			LEFT JOIN '.$this->auto_global.' T2 ON T2.parent = T1.id AND T2.position=?
			LEFT JOIN '.$this->auto_global.' T3 ON T3.parent = T1.id AND T3.position=?
			LEFT JOIN '.$this->auto_global_tree.' T4 ON T4.id_auto = T1.id';
		
		if ($id_auto_global_in) $sql .= ' WHERE T1.id IN (' . $id_auto_global_in . ')';
        
        
		
		$qry = $this->db->query($sql, array(POS_KIRI, POS_KANAN));
        
        
        
		if ($qry && $qry->num_rows()) {
			foreach($qry->result() as $row) {
				$id = absint($row->id);
				$auto_global_data[$id] = $row;
			}
		}
		
		krsort($auto_global_data);
		
		// to build the tree we need to start from tails
		// tails are any node without both left and right
		$tails = array();
		foreach($auto_global_data as $id => $data) {
			if (!empty($data->left) || !empty($data->right)) continue;
			$tails[] = $id;
		}
		
		// if update auto global partial and auto global is not a tail, return false
		if ($id_auto_global && !in_array($id_auto_global, $tails)) return false;
		
		// we are now building the tree
        $auto_global_tree           = array();
		foreach($tails as $id) {
            $auto_global_tree[$id]  = array(
                'left_nodes'        => array(),
                'right_nodes'       => array(),
			);
			
			$child_id = $id;
			$current_id = $auto_global_data[$id]->parent;
			
			while ($current_id>0) {
				if (!isset($auto_global_tree[$current_id])) {
					$auto_global_tree[$current_id] = array(
						'left_nodes' 	=> array_filter(explode(',', $auto_global_data[$current_id]->left_nodes)),
						'right_nodes' 	=> array_filter(explode(',', $auto_global_data[$current_id]->right_nodes)),
					);
				}
				
				$pos = trim( $auto_global_data[$child_id]->position );
				
				// if this id already exists on parent skip process
				if (in_array($id, $auto_global_tree[$current_id][$pos . '_nodes'])) break;
				
				$child_left     = $auto_global_tree[$child_id]['left_nodes'];
				$child_right    = $auto_global_tree[$child_id]['right_nodes'];
				
				$child_nodes    = array_unique(array_merge($child_left, $child_right));
				$child_nodes[]  = $child_id;
				$current_nodes  = $auto_global_tree[$current_id][$pos . '_nodes'];
				$auto_global_tree[$current_id][$pos . '_nodes'] = array_unique(array_merge($current_nodes, $child_nodes));
                
                sort($auto_global_tree[$current_id][$pos . '_nodes']);
                
                // we need to update the member data so the next iteration uses latest tree nodes
				$auto_global_data[$current_id]->{$pos . '_nodes'} = implode(',', $auto_global_tree[$current_id][$pos . '_nodes']);
				
				unset($pos);
				unset($child_left);
				unset($child_right);
				unset($child_nodes);
				unset($current_nodes);
				
				$child_id   = $current_id;
				$current_id = $auto_global_data[$current_id]->parent;
			}
			
			unset($child_id);
			unset($current_id);
		}

		if (empty($auto_global_tree)) return false;
		ksort($auto_global_tree);
		
		$sql  = 'INSERT IGNORE INTO '.$this->auto_global_tree.' VALUES';
		foreach($auto_global_tree as $id => $tree) {
			$left_nodes  = implode(',', $tree['left_nodes']);
			$right_nodes = implode(',', $tree['right_nodes']);
			
			$values = array(
				$id,
				'"' . $left_nodes . '"',
				'"' . $right_nodes . '"',
				count($tree['left_nodes']),
				count($tree['right_nodes']),
				'"' . $auto_global_data[$id]->ancestry . '"'
			);
			$sql .= '(' . implode(',', $values) . '),';
			
			unset($left_nodes);
			unset($right_nodes);
			unset($values);
		}
		
		$sql = rtrim($sql, ',') . '
			ON DUPLICATE KEY UPDATE 
			left_nodes  = VALUES(left_nodes),
			right_nodes = VALUES(right_nodes),
			left_count  = VALUES(left_count),
			right_count = VALUES(right_count),
			ancestry    = VALUES(ancestry)
		';
		
		return $this->db->query($sql);
	}
    
    /**
	 * Update auto-cbi tree of member
	 * @param	$id_autocbi  Auto-CBI ID  (pass 0 to update all auto-cbi tree)
	 * @author	Iqbal
	 */
	function update_autocbi_tree($id_autocbi=0) {
		$id_autocbi           = absint($id_autocbi);
		$autocbi_data         = array();
		$id_autocbi_in        = $id_autocbi;
		if ($id_autocbi_in) {
			if ($ancestry = erp_ancestry_autocbi($id_autocbi)) $id_autocbi_in .= ',' . $ancestry;
		}

        $sql = '
            SELECT 
                T1.id, 
                T1.parent, 
                T1.position, 
                IFNULL(T2.id, 0) AS "one", 
                IFNULL(T3.id, 0) AS "two",
                IFNULL(T4.id, 0) AS "three",
                IFNULL(T5.id, 0) AS "four",
        		IFNULL(T6.one_nodes, "") AS "one_nodes", 
                IFNULL(T6.two_nodes, "") AS "two_nodes", 
                IFNULL(T6.three_nodes, "") AS "three_nodes", 
                IFNULL(T6.four_nodes, "") AS "four_nodes",
                GetAncestryAutoCBI(T1.id) AS ancestry
			FROM '.$this->autocbi.' T1
			LEFT JOIN '.$this->autocbi.' T2 ON T2.parent = T1.id AND T2.position=?
			LEFT JOIN '.$this->autocbi.' T3 ON T3.parent = T1.id AND T3.position=?
            LEFT JOIN '.$this->autocbi.' T4 ON T4.parent = T1.id AND T4.position=?
            LEFT JOIN '.$this->autocbi.' T5 ON T5.parent = T1.id AND T5.position=?
			LEFT JOIN '.$this->autocbi_tree.' T6 ON T6.id_autocbi = T1.id';
		
		if ($id_autocbi_in) $sql .= ' WHERE T1.id IN (' . $id_autocbi_in . ')';
		$qry = $this->db->query($sql, array(POS_CBI_ONE, POS_CBI_TWO, POS_CBI_THREE, POS_CBI_FOUR));

		if ($qry && $qry->num_rows()) {
			foreach($qry->result() as $row) {
				$id = absint($row->id);
				$autocbi_data[$id] = $row;
			}
		}

		krsort($autocbi_data);

		// to build the tree we need to start from tails
		// tails are any node without pos one, two, three, four
		$tails = array();
		foreach($autocbi_data as $id => $data) {
			if (!empty($data->one) || !empty($data->two) || !empty($data->three) || !empty($data->four)) continue;
			$tails[] = $id;
		}

		// if update auto-cbi partial and auto-cbi is not a tail, return false
		if ($id_autocbi && !in_array($id_autocbi, $tails)) return false;
		
		// we are now building the tree
        $autocbi_tree               = array();
		foreach($tails as $id) {
            $autocbi_tree[$id]      = array(
                'one_nodes'         => array(),
                'two_nodes'         => array(),
                'three_nodes'       => array(),
                'four_nodes'        => array(),
			);

			$child_id = $id;
			$current_id = $autocbi_data[$id]->parent;

			while ($current_id>0) {
				if (!isset($autocbi_tree[$current_id])) {
					$autocbi_tree[$current_id] = array(
						'one_nodes' 	=> array_filter(explode(',', $autocbi_data[$current_id]->one_nodes)),
						'two_nodes' 	=> array_filter(explode(',', $autocbi_data[$current_id]->two_nodes)),
                        'three_nodes' 	=> array_filter(explode(',', $autocbi_data[$current_id]->three_nodes)),
                        'four_nodes' 	=> array_filter(explode(',', $autocbi_data[$current_id]->four_nodes)),
					);
				}
                
				$pos = trim( $autocbi_data[$child_id]->position );
                
                if( $pos == POS_CBI_ONE )       { $pos = 'one'; }
                elseif( $pos == POS_CBI_TWO )   { $pos = 'two'; }
                elseif( $pos == POS_CBI_THREE ) { $pos = 'three'; }
                elseif( $pos == POS_CBI_FOUR )  { $pos = 'four'; }
				
				// if this id already exists on parent skip process
				if (in_array($id, $autocbi_tree[$current_id][$pos . '_nodes'])) break;
				
				$child_one      = $autocbi_tree[$child_id]['one_nodes'];
				$child_two      = $autocbi_tree[$child_id]['two_nodes'];
                $child_three    = $autocbi_tree[$child_id]['three_nodes'];
                $child_four     = $autocbi_tree[$child_id]['four_nodes'];
				
				$child_nodes    = array_unique(array_merge($child_one, $child_two, $child_three, $child_four));
                
				$child_nodes[]  = $child_id;
				$current_nodes  = $autocbi_tree[$current_id][$pos . '_nodes'];
				$autocbi_tree[$current_id][$pos . '_nodes'] = array_unique(array_merge($current_nodes, $child_nodes));
                
                sort($autocbi_tree[$current_id][$pos . '_nodes']);
                
                // we need to update the member data so the next iteration uses latest tree nodes
				$autocbi_data[$current_id]->{$pos . '_nodes'} = implode(',', $autocbi_tree[$current_id][$pos . '_nodes']);
				
				unset($pos);
				unset($child_one);
				unset($child_two);
                unset($child_three);
                unset($child_four);
				unset($child_nodes);
				unset($current_nodes);
				
				$child_id   = $current_id;
				$current_id = $autocbi_data[$current_id]->parent;
			}
			
			unset($child_id);
			unset($current_id);
		}

		if (empty($autocbi_tree)) return false;
		ksort($autocbi_tree);
		
		$sql  = 'INSERT IGNORE INTO '.$this->autocbi_tree.' VALUES';
		foreach($autocbi_tree as $id => $tree) {
            $one_nodes      = implode(',', $tree['one_nodes']);
            $two_nodes      = implode(',', $tree['two_nodes']);
            $three_nodes    = implode(',', $tree['three_nodes']);
            $four_nodes     = implode(',', $tree['four_nodes']);
			
			$values = array(
				$id,
				'"' . $one_nodes . '"',
				'"' . $two_nodes . '"',
                '"' . $three_nodes . '"',
                '"' . $four_nodes . '"',
				count($tree['one_nodes']),
				count($tree['two_nodes']),
                count($tree['three_nodes']),
                count($tree['four_nodes']),
				'"' . $autocbi_data[$id]->ancestry . '"'
			);
			$sql .= '(' . implode(',', $values) . '),';
			
			unset($one_nodes);
			unset($two_nodes);
            unset($three_nodes);
            unset($four_nodes);
			unset($values);
		}
		
		$sql = rtrim($sql, ',') . '
            ON DUPLICATE KEY UPDATE 
            one_nodes   = VALUES(one_nodes),
            two_nodes   = VALUES(two_nodes),
            three_nodes = VALUES(three_nodes),
            four_nodes  = VALUES(four_nodes),
            one_count   = VALUES(one_count),
            two_count   = VALUES(two_count),
            three_count = VALUES(three_count),
            four_count  = VALUES(four_count),
            ancestry    = VALUES(ancestry)
		';
		
		return $this->db->query($sql);
	}
    
    /**
     * Delete member
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Member ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function delete_member($id){
        if( empty($id) ) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->delete($this->member) ) 
            return true;
            
        return false;
    }
    
    /**
     * Delete register confirmation
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  Register Confirmation ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function delete_register_confirm($id){
        if( empty($id) ) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->delete($this->member_confirm) ) 
            return true;
            
        return false;
    }
    
    /**
     * Delete PIN
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  PIN ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function delete_pin($id){
        if( empty($id) ) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->delete($this->pin) ) 
            return true;
            
        return false;
    }
    
    /**
     * Delete PIN Order
     * 
     * @author  Iqbal
     * @param   Int     $id     (Required)  PIN Order ID
     * @return  Boolean Boolean false on failed process or invalid data, otherwise true
     */
    function delete_pin_order($id){
        if( empty($id) ) 
            return false;

        $this->db->where($this->id, $id);
        if( $this->db->delete($this->pin_order) ) 
            return true;
            
        return false;
    }
    
    /**
     * Count All Rows
     * 
     * @author  Iqbal
     * @param   String  $status (Optional) Status of member, default 'all'
     * @param   Int     $type   (Optional) Type of member, default 'all'
     * @return  Int of total rows member
     */
    function count_all($status='all', $type=0){
        if ( $status != 'all' ) { $this->db->where('status', $status); }
        if ( $type != 0 )       { $this->db->where('type', $type); }
        
        $query = $this->db->get($this->member);
        
        return $query->num_rows();
    }
    
    /**
     * Count All Rows
     * 
     * @author  Iqbal
     * @param   String  $status (Optional) Status of member, default 'all'
     * @return  Int of total rows member
     */
    function count_all_member($status='all'){
        $sql    = 'SELECT COUNT(id) AS total FROM '.$this->member.' WHERE type = 1';
        if ( $status != 'all' ) { $sql .= ' AND status = '.$status.''; }
        
        $query = $this->db->query($sql);
        $row = $query->row();
        
        return $row->total;
    }
    
    /**
     * Count All Bonus Rows
     * 
     * @author  Iqbal
     * @param   Int $id_member  (Optional) ID of member
     * @param   Int $status     (Optional) Status of member, value ('active' or 'pending')
     * @return  Int of total rows bonus member
     */
    function count_all_bonus($id_member='', $status=''){
        if ( !empty($id_member) ) { $this->db->where('id_member', $id_member); }
        
        if ( !empty($status) ) { $this->db->where('status', ( $status == 'active' ? 1 : 0 ) ); }
        
        $query = $this->db->get($this->bonus);
        
        return $query->num_rows();
    }
    
    /**
     * Count All Bonus Rows
     * 
     * @author  Iqbal
     * @return  Int of total all bonus
     */
    function count_all_bonus_total(){
        $sql    = 'SELECT SUM(amount) AS total FROM '.$this->bonus.'';
        
        $query  = $this->db->query($sql);
        $row    = $query->row();
        
        if ( empty($row->total) ) return 0;
        
        return $row->total;
    }
	
	/**
     * Calculates member bonus
     * 
     * @author  Ahmad
     * @return  Int of total member bonus
     */
    function count_member_bonus($member_id, $period_month=0, $period_year=0){
        $sql    = 'SELECT SUM(amount) AS total FROM '.$this->bonus.' WHERE id_member=?';
		
		$param 	= array($member_id);
		if(!empty($period_month) && !empty($period_year)){
			$sql		.= ' AND datecreated>=? AND datecreated<?';
			$param[] 	= sprintf('%s-%02s', $period_year, $period_month);
			$param[] 	= sprintf('%s-%02s', $period_year, ($period_month+1));
		}
        
        $query  = $this->db->query($sql, $param);
        $row    = $query->row();
        
        if ( empty($row->total) ) return 0;
        return absint($row->total);
    }
    
    /**
     * Count All Pin Rows
     * 
     * @author  Iqbal
     * @param   Int     $id_member  (Optional) ID of member
     * @param   String  $status     (Optional) Status of PIN ('pending','active' and 'used')
     * @return  Int of total rows pin member
     */
    function count_all_pin($id_member='', $status=''){
        if ( !empty($id_member) ) { $this->db->where('id_member', $id_member); }
        
        if ( !empty($status) && $status == 'pending' ){
            $this->db->where('status', 0);
        } elseif( !empty($status) && $status == 'active' ) {
            $this->db->where('status', 1);
        } elseif( !empty($status) && $status == 'used' ) {
            $this->db->where('status', 2);
        }
        
        $query = $this->db->get($this->pin);
        
        return $query->num_rows();
    }
    
    /**
     * Count All Bonus Rows
     * 
     * @author  Iqbal
     * @param   Int     $id_member   (Optional) ID of member
     * @return  Int of total rows pin member
     */
    function count_all_pin_order($id_member=''){
        if ( !empty($id_member) ) { $this->db->where('id_member', $id_member); }
        
        $query = $this->db->get($this->pin);
        
        return $query->num_rows();
    }
    
    /**
     * Count All Reward Rows
     * 
     * @author  Iqbal
     * @param   Int     $id_member   (Optional) ID of member
     * @return  Int of total rows reward member
     */
    function count_all_reward($id_member=''){
        if ( !empty($id_member) ) { $this->db->where('id_member', $id_member); }
        
        $query = $this->db->get($this->reward);
        
        return $query->num_rows();
    }
    
    /**
     * Count All Omzet
     * 
     * @author  Iqbal
     * @return  Int of total omzet
     */
    function count_all_omzet($package='', $this_month=''){        
        $total_omzet = 0;
		
        $sql    = 'SELECT type, package, COUNT(id) AS member_count FROM '.$this->member.' WHERE type=1 '; 
        if( !empty($package) ) $sql .= ' AND package_old LIKE "'.$package.'" ';
        if( !empty($this_month) ) $sql .= ' AND datecreated LIKE "'.$this_month.'%" ';
        $sql   .= 'GROUP BY 1';
		$qry = $this->db->query($sql);
		
		if(!$qry || !$qry->num_rows()) return $total_omzet;
		
		$cfg_investment = config_item('investment');
		foreach($qry->result() as $row) {
			$total_omzet += $row->member_count * absint($cfg_investment[$row->package]);
		}
        return $total_omzet;
    }
    
    /**
     * Count All Deposite
     * 
     * @author  Iqbal
     * @return  Int of total omzet
     */
    function count_all_deposite(){        
        $sql_bonus          = 'SELECT SUM(amount) AS total_bonus FROM ' . $this->bonus;
        $query_bonus        = $this->db->query($sql_bonus);
        $total_bonus        = $query_bonus->row()->total_bonus;
        
        $sql_withdraw       = 'SELECT SUM(nominal) AS total_withdraw FROM ' . $this->withdraw;
        $query_withdraw     = $this->db->query($sql_withdraw);
        $total_withdraw     = $query_withdraw->row()->total_withdraw;
        
        $total_deposite     = absint($total_bonus) - absint($total_withdraw);
        
        return $total_deposite;
    }
    
    /**
     * Count All Member
     * 
     * @author  Iqbal
     * @return  Int of total rows of member
     */
    function count_member($package='', $this_month=''){
        $this->db->where('status', 1);
        $this->db->where('type !=', 2);
        if( !empty($package) ) $this->db->where('package', $package);
        if( !empty($this_month) ) $this->db->like('datecreated', $this_month, 'after');
        $query = $this->db->get($this->member);
        
        return $query->num_rows();
    }
    
    /**
     * Count All Member
     * 
     * @author  Iqbal
     * @return  Int of total rows of member
     */
    function count_member_old($package='', $this_month=''){
        $this->db->where('status', 1);
        $this->db->where('type !=', 2);
        if( !empty($package) ) $this->db->where('package_old', $package);
        if( !empty($this_month) ) $this->db->like('datecreated', $this_month, 'after');
        $query = $this->db->get($this->member);
        
        return $query->num_rows();
    }
    
    /**
     * Count Bonus by Type
     * 
     * @author  Iqbal
     * @param   Int         $id_member  (Required) ID Member
     * @param   Mixed       $type       (Optional) Type of Bonus
     * @param   Datetime    $date       (Optional) Datetime of Bonus
     * @param   Boolean     $groupby    (Optional) Group By of Bonus
     * @return  Int of total all bonus
     */
    function count_bonus($id_member, $type='all',$date='',$groupby=false){  
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $sql    = '
            SELECT SUM(amount) AS total FROM '.$this->bonus.' 
            WHERE id_member = '.$id_member. ( !empty($date) ? ' AND datecreated LIKE "'.$date.'%"' : '' ) . ( $type != 'all' ? ' AND type = '.$type.'' : '' ).'';
            
        ( $groupby ? $sql .= ' GROUP BY amount' : '' );

        $query  = $this->db->query($sql);
        $row    = $query->row();
        
        if ( empty($row->total) ) return 0;
        
        return $row->total;
    }
    
    /**
     * Count Bonus by Type
     * 
     * @author  Iqbal
     * @param   Int         $id_member  (Required) ID Member
     * @param   Datetime    $date       (Optional) Datetime of Bonus
     * @return  Int of total all bonus
     */
    function count_withdrawal($id_member, $date=''){  
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $sql    = '
            SELECT SUM(nominal) AS total FROM '.$this->withdraw.' 
            WHERE id_member = '.$id_member. ( !empty($date) ? ' AND datecreated LIKE "'.$date.'%"' : '' ) . '';

        $query  = $this->db->query($sql);
        $row    = $query->row();
        
        if ( empty($row->total) ) return 0;
        
        return $row->total;
    }
    
    /**
     * Count Auto-CBI Fees by ID Member
     * 
     * @author  Iqbal
     * @param   Int         $id_member  (Required) ID Member
     * @return  Int of total all Auto-CBI Fees
     */
    function count_autocbi($id_member){  
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $sql    = '
            SELECT SUM(amount) AS total FROM '.$this->autocbi_fees.' 
            WHERE id_member=?';

        $query  = $this->db->query($sql, array($id_member));
        $row    = $query->row();
        
        if ( empty($row->total) ) return 0;
        
        return $row->total;
    }
    
    /**
     * Count Auto-CBI by Type
     * 
     * @author  Iqbal
     * @param   Int         $id_member  (Required) ID Member
     * @param   Datetime    $date       (Optional) Datetime of Auto-CBI
     * @return  Int of total all Auto-CBI
     */
    function count_autocbi_poin(){  
        $sql    = 'SELECT COUNT(id) AS total FROM '.$this->autocbi.' WHERE type=?';
        $query  = $this->db->query($sql, array(CBI_TYPE_POINT));
        $row    = $query->row();
        
        if ( empty($row->total) ) return 0;
        
        return $row->total;
    }
    
    /**
     * Count Auto-CBI Bonus by Type
     * 
     * @author  Iqbal
     * @param   Int         $id_member  (Required) ID Member
     * @param   Datetime    $date       (Optional) Datetime of Auto-CBI Bonus
     * @param   Boolean     $groupby    (Optional) Group By of Auto-CBI Bonus
     * @return  Int of total all auto-cbi bonus
     */
    function count_autocbi_bonus($id_member,$date='',$groupby=false){  
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $sql    = '
            SELECT SUM(amount) AS total FROM '.$this->autocbi_bonus.' 
            WHERE id_member = '.$id_member. ( !empty($date) ? ' AND datecreated LIKE "'.$date.'%"' : '' );
            
        ( $groupby ? $sql .= ' GROUP BY amount' : '' );

        $query  = $this->db->query($sql);
        $row    = $query->row();
        
        if ( empty($row->total) ) return 0;
        
        return $row->total;
    }
    
    /**
     * Count Sponsored of Member
     * 
     * @author  Iqbal
     * @param   Int         $id_member  (Required) ID Member
     * @return  Int of total all sponsored
     */
    function count_sponsored($id_member){  
        if ( !is_numeric($id_member) ) return false;

        $id_member  = absint($id_member);
        if ( !$id_member ) return false;
        
        $sql    = 'SELECT COUNT(id) AS total FROM '.$this->member.' WHERE sponsor = '.$id_member . '';
        $query  = $this->db->query($sql);
        $total  = $query->row()->total;
        
        return $total;
    }
    
    /**
     * Count All Member Confirm Rows
     * 
     * @author  Iqbal
     * @param   Int     $id_member   (Optional) ID of member
     * @return  Int of total rows member confirm
     */
    function count_all_confirm($id_member=''){
        if ( !empty($id_member) ) { $this->db->where('id_member', $id_member); }
        
        $query = $this->db->get($this->member_confirm);
        
        return $query->num_rows();
    }
    
    /**
     * Count Pairing Qualified Today
     * 
     * @author  Iqbal
     * @param   Int     $id_member   (Optional) ID of member
     * @return  Int of total rows pairing qualified
     */
    function count_qualified_today($id_member) {
		$id_member = absint($id_member);
        if ( !$id_member ) return false;
		
		$sql = 'SELECT IFNULL(SUM(A.qualified),0) AS total_qualified FROM erp_bonus_qualified A WHERE A.id_member=? AND DATE(A.datecreated)=CURRENT_DATE';
		
		$qry = $this->db->query($sql, array($id_member));
		$row = $qry->row();
		
		return absint($row->total_qualified);
	}
	
	/**
	 * Get member data of parent of member
	 * @author	Ahmad
	 */
	function get_parentdata($member_id) {
        if ( !is_numeric($member_id) ) return false;

        $member_id = absint($member_id);
        if ( !$member_id ) return false;
        
        $query = $this->db->get_where($this->member, array($this->id => $member_id));
		
		$sql = 'SELECT * FROM ' . $this->member . ' WHERE id=(SELECT parent FROM ' . $this->member . ' WHERE id=?)';
		$qry = $this->db->query($sql, array($member_id));
		
        if ( !$qry->num_rows() )
            return false;
        
        return $qry->row();
	}
    
    /**
     * Retrieve all member reward data
     * 
     * @author  Iqbal
     * @param   Integer $member_id  (Required)  Member ID
     * @param   String  $type       (Optional)  Type of Reward
     * @return  Object  Result of member reward list
     */
    function get_all_my_reward($id_member, $type=''){
        if ( !is_numeric($id_member) ) return false;

        $id_member = absint($id_member);
        if ( !$id_member ) return false;
		
		$sql = '
        SELECT * FROM ' . $this->reward . ' A
		LEFT JOIN '.$this->reward_option.' B ON B.reward_id = A.type
		WHERE id_member=?';
        
        if ( !empty($type) ) $sql .= ' AND type=?';
        
        $query = $this->db->query($sql, array($id_member, $type));
        
        if ( !$query->num_rows() )
            return false;
            
        if ( !empty($type) ) return $query->row();
        return $query->result();
    }
    
    /**
     * Retrieve all member autocbi total
     * 
     * @author  Ahmad
     * @param   Integer $member_id          Member ID
     * @param   Integer $status             Status of Bonus
     * @return  Object  Result of member bonus total
     */
    function get_all_my_autocbi_total($id_member){
        if ( !is_numeric($id_member) ) return false;

        $id_member = absint($id_member);
        if ( !$id_member ) return false;
        
        $sql    = 'SELECT SUM(amount) AS total FROM '.$this->autocbi_fees.' WHERE id_member=?';
        $query  = $this->db->query($sql, array($id_member));
        
        if ( !$query || !$query->num_rows() )
            return 0;
        
        return absint($query->row()->total);
    }
    
    /**
	 * Calculate Auto-CBI Fees
	 * 
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param int $id_member. ID Member.
	 * @param float $amount. Amount of bonus.
     * @param int $bonus_id. Bonus ID.
	 * @param string $desc. Description of Auto-CBI fees.
     * @param boolean $debug. Debug mode option.
	 * @return float Auto-CBI fees.
	 * @author Iqbal
	 */
	function calc_autocbi_fees( $id_member, $amount, $bonus_id, $desc = '', $debug = FALSE ) {
		if ( empty( $id_member ) || empty( $amount ) || empty( $bonus_id ) )
			return false;
		
		if ( ! $member = $this->get_memberdata( $id_member ) )
			return false;
		
		if ( ! $debug && $member->autocbi_qualified )
			return false;
		
		// All bonus has to pay fees
        $autocbi_fee_percentage     = intval( config_item( 'autocbi_fee' ) );
        $autocbi_fee_max            = intval( config_item( 'autocbi_fee_max' ) );
		
		// We need to know how much autocbi fees required for member to be qualified
        $autocbi_total_amt          = $this->get_autocbi_total_amount( $id_member );
		// Calculate true autocbi fees for this period
        $autocbi_fees_ori           = ( $autocbi_fee_percentage / 100 ) * $amount;
        $autocbi_fees_qualified     = $autocbi_fee_max - $autocbi_total_amt;
        $autocbi_fees               = min( $autocbi_fees_ori, $autocbi_fees_qualified );
        $autocbi_total_amt         += $autocbi_fees;
		
		if ( ! $debug && $autocbi_fees > 0 ) {
			$datecreated = date( 'Y-m-d H:i:s' );
			$desc = ! empty( $desc ) ? $desc : 'Auto-CBI Fees ' . $datecreated;
            $data_autocbi       = array(
                'id_member'		=> $id_member,
                'desc'			=> $desc,
                'amount'		=> $autocbi_fees,
                'datecreated'	=> $datecreated,
                'bonus_id'		=> $bonus_id,
            );
			$this->save_data_autocbi_fees( $data_autocbi );
            
			$autocbi_qualified	= $autocbi_total_amt == $autocbi_fee_max;
			if ( $autocbi_qualified ) {
				// set qualified to true
				$sql = 'UPDATE ' . $this->member . ' SET autocbi_qualified=1, autocbi_qualified_date=? WHERE id=?';
				$this->db->query( $sql, array( $datecreated, $id_member ) );
                
                // Auto-CBI Process
                if( $member->autocbi_qualified == 0 ){
                    erp_auto_cbi_process($id_member);
                }
			}
		}
		
		// return Auto-M fees
		return $autocbi_fees;
	}
    
    /**
	 * Get Auto-CBI Total Amount
	 * @author	Iqbal
	 */
    function get_autocbi_total_amount($member_id) {
		if ( !is_numeric($member_id) ) return false;

        $member_id = absint($member_id);
        if ( !$member_id ) return false;
		
		$sql = 'SELECT SUM(amount) AS total_amount FROM '.$this->autocbi_fees.' WHERE id_member=?';
		$qry = $this->db->query($sql, array($member_id));
		
		if(!$qry || !$qry->num_rows()) return 0;

		$row = $qry->row();
		return absint($row->total_amount);
	}
    
    /**
	 * Get Auto-CBI Last index
	 * @author	Iqbal
	 */
    function get_autocbi_last_index() {
		$sql = 'SELECT `index` FROM '.$this->autocbi.' ORDER BY `index` DESC';
		$qry = $this->db->query($sql);
		
		if(!$qry || !$qry->num_rows()) return false;

		$row = $qry->row();
		return absint($row->index);
	}
    
    /**
	 * Truncate Auto-CBI Tree
	 * @author	Iqbal
	 */
    function truncate_autocbi_tree() {
		$sql = 'TRUNCATE TABLE '.$this->autocbi_tree.'';
		$qry = $this->db->query($sql);
        return true;
	}
}
/* End of file model_member.php */
/* Location: ./application/models/model_member.php */
