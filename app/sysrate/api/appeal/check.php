<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_appeal_check {

    /**
     * 接口作用说明
     */
    public $apiDescription = '平台对商家申诉的评论进行审核';

    public function getParams()
    {
        $return['params'] = array(
            'appeal_id' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'需要申诉评论ID'],
            'result' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'审核结果,true同意或者false驳回'],
            'reject_reason' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'如果是驳回需要填写驳回的理由'],
        );

        return $return;
    }

    public function check($params)
    {
        $data['result'] = $params['result'];
        $data['reject_reason'] = $params['reject_reason'];
        kernel::single('sysrate_appeal')->check($params['appeal_id'], $data);
    }

}

