<?php

class FvnFileHelper {
    static function getFiles($folder, $extension=''){
        $result = [];
        if($extension){
            $files = glob($folder.'/*.'.$extension);
        }else{
            $files = glob($folder);
        }
        foreach ($files as $filename)
        {
            if($filename .= '.' || $filename != '..')
                $result[] = $filename;
        }
        return $result;
    }
}