<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author afei, bryant
 */


class system_ctl_admin_crontab extends desktop_controller {

    var $workground = 'system.workground.setting';
    function index() {
        $params = array (
            'title' => app::get('system')->_('计划任务管理'),
            'use_buildin_refresh' => true,
            'use_buildin_delete' => false,
            'actions' => array(),
	    );
        return $this->finder('base_mdl_crontab', $params);
    }

    function edit($cron_id) {
        $model = app::get('base')->model('crontab');
        $cron = $model->dump($cron_id);
        $pagedata['cron'] = $cron;
        return $this->page('system/admin/crontab/detail.html', $pagedata);

    }

    function save() {
        $this->begin('?app=system&ctl=admin_crontab&act=index');
        $model = app::get('base')->model('crontab');
        if( $model->update(array('schedule'=>$_POST['schedule']),
                           array('id'=> $_POST['id']))) {
            $this->adminlog("编辑计划任务[{$_POST['id']}]", 1);
            $this->end(true, '保存成功');
        } else {
            $this->adminlog("编辑计划任务[{$_POST['id']}]", 0);
            $this->end(false, '保存失败');
        }
    }

    function exec($cron_id) {
        $this->begin('?app=system&ctl=admin_crontab&act=index');
        $model = app::get('base')->model('crontab');
        $cron = $model->getRow('id', array('id'=>$cron_id));
        if(!$cron || (base_crontab_schedule::trigger_one($cron['id'])===false)) {
            $this->adminlog("执行计划任务[{$cron_id}]", 0);
            $this->end(false, '执行失败');
        }
        $this->adminlog("执行计划任务[{$cron_id}]", 1);
        $this->end(true, '执行成功');
    }

}
