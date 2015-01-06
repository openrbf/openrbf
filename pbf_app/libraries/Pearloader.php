<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pearloader{
  function load($package,$options = null){
		
        require_once('pbf_app/pear/Spreadsheet/Excel/Writer.php');
        $classname = 'Spreadsheet_Excel_Writer';
        if(is_null($options)){
            return new $classname();
        }
        elseif (is_array($options)) {
            $reflector = new ReflectionClass($classname);
            return $reflector->newInstanceArgs($options);
        }
        else {
            return new $classname($options);
        }
    }
}
