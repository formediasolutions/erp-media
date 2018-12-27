<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Option extends CI_Model{
    /**
     * Initialize table and primary field variable
     */
    var $table              = "adm_options";
    var $tableCompany       = "adm_company";
    var $tableGroup         = "adm_group";
    var $tableGroupMenu     = "adm_group_menu";
    var $tableMenu          = "adm_menu";
    var $tableModule        = "adm_module";
    
    /**
	* Constructor - Sets up the object properties.
	*/
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Add Options
     * 
     * @author  Iqbal
     * @param   Array/Object    $data   (Required)  Data of option to add
     * @return  Mixed   Boolean false on failed process, invalid data, or data is not found, otherwise Int of Option ID
     */
    function add_option($data){
        if( empty($data) ) return false;
        if( $this->db->insert($this->table, $data) ) {
            $id = $this->db->insert_id();
            return $id;
        };
        return false;
    }
    
    /**
     * Update Options
     * 
     * @author  Iqbal
     * @param   Array/Object    $data   (Required)  Data of option to update
     * @param   Int             $id     (Required)  ID of Option
     * @return  Mixed   Boolean false on failed process, invalid data, or data is not found, otherwise Int of Option ID
     */
    function update_option($data, $id){
        if( empty($id) ) return false;
        if( empty($data) ) return false;
        if( $this->db->update($this->table, $data, array('id_option' => $id)) ) return true;
        return false;
    }
    
    
    //RHEVAL
    // Module Function
    function get_moduledata($module_id=''){
        if( !empty($module_id) ){
            if ( !is_numeric($module_id) ) return false;    
            $module_id = absint($module_id);
            if ( !$module_id ) return false;
            
            $query = $this->db->get_where($this->tableModule, array('id_adm_module' => $module_id));
            
            if ( !$query->num_rows() )
                return false;
            foreach ( $query->result() as $row ) {
                $member = $row;
            }
        }else{
            $query = $this->db->get($this->tableModule);
            if ( !$query->num_rows() )
                return false;
                
            return $query->result();
        }
        
        return $member;
    }
    
    // Group Menu Function
    function get_groupmenudata($group_id=''){
        if ( !is_numeric($group_id) ) return false;    
        
        $group_id = absint($group_id);
        if ( !$group_id ) return false;
        
        $query = $this->db->get_where($this->tableGroupMenu, array('id_adm_group' => $group_id));
        
        if ( !$query->num_rows() )
            return false;
        /*
        foreach ( $query->result() as $row ) {
            $member = $row;
        }
        */
        
        return $query->result();
    }
    
    // Main Menu Function
    function get_mainmenudata($menu_id='', $module_id=''){
        if ( !is_numeric($menu_id) ) return false;    
        if ( !is_numeric($module_id) ) return false;   
        
        $menu_id = absint($menu_id);
        if ( !$menu_id ) return false;
        
        $module_id = absint($module_id);
        if ( !$module_id ) return false;
        
        $query = $this->db->get_where($this->tableMenu, array('id_adm_menu' => $menu_id, 'id_adm_module' => $module_id));
        
        if ( !$query->num_rows() )
            return false;
        
        foreach ( $query->result() as $row ) {
            $menu = $row;
        }
        
        return $menu;
    }
}
/* End of file model_option.php */
/* Location: ./application/models/model_option.php */
