<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class desktop_mdl_roles extends dbeav_model{

    function is_exists($role_name)
    {
        $row_data = $this->getList('role_id',array('role_name'=>$role_name));
        if($row_data)
            return true;
        else
            return false;
    }
    
   ####检查工作组名称
   function check_gname($name){
       $result = $this->getList('role_id',array('role_name'=>$name));
       if($result){

           return $result[0]['role_id'];
       }
       else{
           return false;
       }
   }

   function validate($aData,&$msg){
        if($aData['role_name']==''){
        $msg = app::get('desktop')->_("工作组名称不能为空");
        return false;
        }
        if(!$aData['workground']){
        $msg = app::get('desktop')->_("请至少选择一个权限");
        return false;
        }
        $opctl = $this->app->model('roles');
        $result = $opctl->check_gname($aData['role_name']);
        if($result){
        $msg = app::get('desktop')->_("该名称已经存在");
        return false;
         }
         return true;
     }
    //删除角色的判断
    public function delete($filter,$subSdf = 'delete')
    {
        $hasroleMdl = app::get('desktop')->model('hasrole');
        $roleId = $hasroleMdl->getList('role_id',array('role_id'=>$filter['role_id']));

        if($roleId)
        {
            $msg = "含有已被使用角色，不可以删除！";
            throw new \logicException($msg);
        }
        return parent::delete($filter);
    }
}
?>
