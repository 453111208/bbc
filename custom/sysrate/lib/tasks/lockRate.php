<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysrate_tasks_lockRate extends base_task_abstract implements base_interface_task {

    //申诉成功开通之后有15天的修改权限，如果不修改则自动关闭
    public function exec($params=null)
    {
        $objMdlTraderate = app::get('sysrate')->model('traderate');

        //修改 出15天之内申诉成功，并且没有修改的评价
        $filter['modified_time|sthan'] = strtotime('-15 days');
        $filter['appeal_status'] = 'SUCCESS';
        $filter['is_lock'] = '0';
        $filter['disabled'] = '0';

        $updateData['is_lock'] = '1';
        $updateData['modified_time'] = time();

        $objMdlTraderate->update($updateData, $filter);

        return true;
    }
}

