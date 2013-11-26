<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Configurations
 *
 * @author manila
 */
class Configurations {
    
    public static function getConfiguration($key){
        if(!$key || empty($key) || !isset($key))
            return NULL;
        
        $sql = "Select value from global_configuration where name='".$key."'";
        $value = DB::getInstance()->executeS($sql);
        
        if($value && isset($value[0]))
            return $value[0]['value'];
        else 
            return @constant($key);
        
        return NULL;
    }
    public static function updateConfiguration($key,$value=NULL){
        if(!$key || empty($key) || !isset($key))
            return false;
        
        $sql = "Insert into global_configuration (name,value) values('".$key."', '".$value."')
            on duplicate key update value='".$value."'";
    
       $status= DB::getInstance()->execute($sql);
       
       return $status;
    }
}

?>
