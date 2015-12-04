<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysitem_tasks_userItemNotify extends base_task_abstract implements base_interface_task{

    // 每个队列执行100条订单信息
    var $limit = 100;
    public function exec($params=null)
    {

        $filter = array(
            'item_id'=>$params['item_id'],
            'shop_id'=>$params['shop_id'],
        );
        $offset = 0;
        while( $listFlag = $this->__itemNotifyList($data, $filter, $offset) ){
            $offset++;
            // 把分页得到的缺货Id加入相关队列
            try
            {
                $this->__implement($data);
            }
            catch(Exception $e)
            {
                throw new \LogicException($e->getMessage());
            }
        }
        //
    }
    /**
     * 分页获取缺货ID
     * @param  array $data 引用获取一页缺货Id
     * @param  array $filter          缺货ID过滤条件
     * @param  int $offset          页数
     * @return bool                  [description]
     */
    private function __itemNotifyList(&$data , $filter, $offset)
    {
        $params['item_id'] = $filter['item_id'];
        $params['shop_id'] = $filter['shop_id'];
        $params['sendstatus'] = 'ready';
        $params['page_no'] = $offset;
        $params['page_size'] = $this->limit;
        $params['fields'] = 'email,item_id,shop_id,gnotify_id';
        $itemdata = app::get('sysitem')->rpcCall('user.notifyItemList',$params);
        if(!$itemdata)
        {
            return false;
        }
        else
        {
            $data = $itemdata;
            return true;
        }
    }
    //执行
    private function __implement($data)
    {
        $tmpl = 'user-item';
        foreach ($data as $key => $value)
        {
            $sendData['vcode'] = $value['vcode'] ;
            $result = messenger::sendEmail($value['email'],$tmpl,$sendData);
            if($result['rsp'] == "fail")
            {
                throw new \LogicException(app::get('sysitem')->_('邮件发送失败'));
            }
            else
            {
                $params['gnotify_id'] = $value['gnotify_id'];
                app::get('sysitem')->rpcCall('user.updatenotifyitem',$params);
            }
        }
        return true;
    }
    /*//去重复
    public function unique_arr($array2D,$stkeep=false,$ndformat=true)  
    {
        // 判断是否保留一级数组键 (一级数组键可以为非数字)  
        if($stkeep) $stArr = array_keys($array2D);  
        // 判断是否保留二级数组键 (所有二级数组键必须相同)  
        if($ndformat) $ndArr = array_keys(end($array2D));  
        //降维,也可以用implode,将一维数组转换为用逗号连接的字符串  
        foreach ($array2D as $v){  
            $v = join(",",$v);   
            $temp[] = $v;  
        }  
        //去掉重复的字符串,也就是重复的一维数组  
        $temp = array_unique($temp);
        //再将拆开的数组重新组装  
        foreach ($temp as $k => $v)  
        {  
            if($stkeep) $k = $stArr[$k];  
            if($ndformat)  
            {  
                $tempArr = explode(",",$v);
                foreach($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;  
            }  
            else $output[$k] = explode(",",$v);
        }
        return $output;
    }*/

}