<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class appealAdd extends PHPUnit_Framework_TestCase {

    public function testAppealAdd()
    {
        try
        {
            $params = array(
                'rate_id' =>'17',
                'is_again' => false,
                'appeal_type' => 'APPLY_UPDATE',
                'content' =>'请平台审核给用户修改该评论权限',
                //'evidence_pic' =>5,
            );
            $result = app::get('sysrate')->rpcCall('rate.appeal.add', $params,'seller');
            print_r($result);
            exit;
        }
        catch (\LogicException $e)
        {
            // 逻辑处理
            echo $e->getMessage();
        }
        catch (\RunException $e)
        {
            // 运行时错误处理
            echo $e->getMessage();
        }
        exit;
    }
}
