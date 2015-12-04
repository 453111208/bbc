<?php
// 商品招标
return array (
    'columns' =>
    array (
        'tenderinfo_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('投标信息主键'),
        ),
        'tender_man_id' => array(
            'type' => 'string',
            'default' => '0',
            //'pkey' => true,
            'comment' => app::get('sysshoppubt')->_('投标企业ID'),
        ), 

        'tender_id' => array(
            'type' => 'string',
            'default' => '0',
            //'pkey' => true,
            'comment' => app::get('sysshoppubt')->_('交易ID'),
        ),
        'price' => array(
            'type' => 'money',
            'default' => '0',
            //'pkey' => true,
            'label' => app::get('sysshoppubt')->_('投标价格'),
            'comment' => app::get('sysshoppubt')->_('投标价格'),
            'order' => 5,
        ),
        'tender_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('投标时间'),
            'comment' => app::get('sysshoppubt')->_('投标时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>4,
        ),
        'chrule_id'=>
        array (
            'type' => 'string',
            'comment' => app::get('sysshoppubt')->_('招标规则选用记录id'),
        ),
        'data' => array(
            'type' => 'string',
            'length'=>800,
            'comment' =>app::get('sysshoppubt')->_('上传的数据'),
        ),
    ),
    'primary' => 'tenderinfo_id',
    'comment' => app::get('sysshoppubt')->_('投标信息'),
);
