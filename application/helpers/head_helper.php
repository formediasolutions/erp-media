<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('erp_scripts') ){
    /**
     * set the script dynamicly
     *
     * @param   array       $scripts      Array of Script
     * @return  $loadscript Load Script   string
     */
    function erp_scripts($scripts, $main='', $add='')
    {
    	$CI =& get_instance();
        
        $loadscript = '';
        if( !$scripts || empty($scripts) ) return $loadscript;
        
        if( !empty($main) ) $loadscript .= $main;
    
    	foreach($scripts as $s){
            $loadscript .= '<script type="text/javascript" src="'.$s.'"></script>';
    	}
        
        if( !empty($add) ) $loadscript .= $add;
        
        return $loadscript;
    }
}

if ( !function_exists('erp_scripts_init') ){
    /**
     * set the script init dynamicly
     *
     * @param   array       $scripts      Array of Script Init
     * @return  $loadscript Load Script   string
     */
    function erp_scripts_init($scripts)
    {
    	$CI =& get_instance();
        
        $scripts_init = '';
        if( !$scripts || empty($scripts) ) return $scripts_init;
    
        $scripts_init .= '
        <script type="text/javascript">
            jQuery(document).ready(function() {';
            	foreach($scripts as $s){
                    $scripts_init .= $s;
            	}
        $scripts_init .= '
            });
        </script>';
        
        return $scripts_init;
    }
}

if ( !function_exists('erp_headstyles') ){
    /**
     * set the head styles dynamicly
     *
     * @param   array       $styles       Array of Styles
     * @return  $loadstyles Head Styles   string
     */
    function erp_headstyles($styles)
    {
    	$CI =& get_instance();
        
        $loadstyles = '';
        if( !$styles || empty($styles) ) return $loadstyles;
        
    	foreach($styles as $s){
            $loadstyles .= '<link rel="stylesheet" type="text/css" href="'.$s.'" />';
    	}
        
        
        return $loadstyles;
    }
}

/*
CHANGELOG
---------
Insert new changelog at the top of the list.
-----------------------------------------------
Version YYYY/MM/DD  Person Name     Description
-----------------------------------------------
1.0.0   2017/02/10  Iqbal           - Create this changelog.
*/