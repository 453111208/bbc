<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    'bool'=>array(
        'doctrineType' => ['boolean'],
        'searchparams'=>array('has'=>app::get('base')->_('包含'),'nohas'=>app::get('base')->_('不包含')),
    ),
    'money'=>array(
        'doctrineType' => ['decimal', ['precision' => 20, 'scale' => 3]],
        'searchparams'=>array('than'=>app::get('base')->_('大于'),'lthan'=>app::get('base')->_('小于'),'nequal'=>app::get('base')->_('等于'),'sthan'=>app::get('base')->_('小于等于'),'bthan'=>app::get('base')->_('大于等于'),'between'=>app::get('base')->_('介于')),
        'match'=>'[0-9]{1,18}(\.[0-9]{1,3}|)',
    ),
    'email'=>array(
        'doctrineType' => ['string'],
        'searchparams'=>array('has'=>app::get('base')->_('包含'),'tequal'=>app::get('base')->_('等于'),'head'=>app::get('base')->_('开头等于'),'foot'=>app::get('base')->_('结尾等于'),'nohas'=>app::get('base')->_('不包含')),
    ),
    'time'=>array(
        'doctrineType' => ['integer', ['unsigned' => true]],
        'searchparams'=>array('than'=>app::get('base')->_('晚于'),'lthan'=>app::get('base')->_('早于'),'nequal'=>app::get('base')->_('是'),'between'=>app::get('base')->_('介于')),
    ),
    'region'=>array(
        'doctrineType' => ['string'],
    ),
    'password'=>array(
        'doctrineType' => ['string', ['length' => 32]],
    ),
    'number'=>array(
        'doctrineType' => ['integer', ['unsigned' => true]],
        'searchparams'=>array('than'=>app::get('base')->_('大于'),'lthan'=>app::get('base')->_('小于'),'nequal'=>app::get('base')->_('等于'),'sthan'=>app::get('base')->_('小于等于'),'bthan'=>app::get('base')->_('大于等于'),'between'=>app::get('base')->_('介于')),
    ),
    'float'=>array(
        'doctrineType' => ['float'],
        'searchparams'=>array('than'=>app::get('base')->_('大于'),'lthan'=>app::get('base')->_('小于'),'nequal'=>app::get('base')->_('等于'),'sthan'=>app::get('base')->_('小于等于'),'bthan'=>app::get('base')->_('大于等于'),'between'=>app::get('base')->_('介于')),
    ),
    'gender'=>array(
        //'sql'=>'enum(\'male\',\'female\')',
        'doctrineType' => ['string', ['length' => 6]]
    ),
    'ipaddr'=>array(
        'doctrineType' => ['string', ['length' => 20]]

    ),
    'serialize'=>array(
        'doctrineType' => ['text'],
        'func_output' => function($value){
            if ($value !== null) return unserialize($value);
        },
        'func_input' => function($value){
            if ($value !== null) return serialize($value);
        }

    ),
    'last_modify'=>array(
        'doctrineType' => ['integer', ['unsigned' => true]],
        'searchparams'=>array('than'=>app::get('base')->_('大于'),'lthan'=>app::get('base')->_('小于'),'nequal'=>app::get('base')->_('等于')),
        'func_input' => function($value){
            return time();
        }
    ),
);
