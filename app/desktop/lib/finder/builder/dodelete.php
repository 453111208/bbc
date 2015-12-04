<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_finder_builder_dodelete extends desktop_finder_builder_prototype{

    function main(){

        $this->controller->begin();

        $model = $this->app->model($this->object->table_name());

        $this->dbschema = $this->object->get_schema();

        $pkey = $this->dbschema['idColumn'];

        $pkey_value = $_POST[$pkey];
        $filter = array($pkey=>$pkey_value);
        if( $_POST['isSelectedAll']=='_ALL_')
        {
            $_filter = $_POST;
            unset($_filter['isSelectedAll']);
            if($_filter['_finder']) unset($_filter['_finder']);
            if(is_null($filter[$pkey]))
            {
                $filter = $_filter;
            }
            else
            {
                $filter = array_merge($_filter,$filter);
            }
        }
        else
        {
            $filter[$this->dbschema['idColumn']] = $_POST[$this->dbschema['idColumn']];
        }

        if($_GET['view'])
        {
            $views = $this->getViews();
            $filter = array_merge((array)$views[$_GET['view']]['filter'],(array)$filter);
        }

        $delete = kernel::single('desktop_system_delete');

        $result = $delete->dodelete(get_class($this->object),$filter);

        if($result)
        {
            return $this->controller->end(true,app::get('desktop')->_('删除成功'),'javascript:finderGroup["'.$_GET['finder_id'].'"].unselectAll();finderGroup["'.$_GET['finder_id'].'"].refresh();');
        }
        else
        {
            return $this->controller->end(false,$model->delete_msg?$model->delete_msg:app::get('desktop')->_('删除失败！'));
        }

    }

}
