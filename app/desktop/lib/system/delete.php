<?php

class desktop_system_delete{

    public function dodelete($mdl_name,$filter=null)
    {
        list($app_id,$table) = explode('_mdl_',$mdl_name);
        $model = app::get($app_id)->model($table);

        $dbschema = $model->get_schema();
        $pkey = $dbschema['idColumn'];

        if(method_exists($model, 'doDelete'))
        {
            try
            {
                $model->doDelete($filter[$pkey]);
            }
            catch(Exception $e)
            {
                $model->delete_msg = $e->getMessage();
                return false;
            }
            return true;
        }
        $delete = $model->delete($filter);
        return $delete;
    }
}


