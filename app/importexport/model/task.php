<?php

class importexport_mdl_task extends dbeav_model{

    public function create_task($type='export',$params){
        $data = array(
            'name' => $params['name'],
            'key' => $params['key'],
            'filetype' => $params['filetype'],
            'create_date' => time(),
            'type' => $type,
            'status' => $params['status'] ? $params['status'] : 0,
            'is_display' => 1,
        );

        if( $this->save($data) ){
            return true;
        }else{
            return false;
        }
    }//end function
}
