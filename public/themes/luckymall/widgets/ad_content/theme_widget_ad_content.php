<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_ad_content(&$setting){
    $nodeid = $setting['nodeId'];
    $nodeData = app::get('topm')->rpcCall('syscontent.node.get.list', array('parent_id'=>$nodeid,'fields'=>'node_id'));
    if($setting['nodeSort']=='timedesc')
    {
        $orderBy = 'modified DESC';
    }
    else
    {
        $orderBy = 'modified ASC';
    }
    if($nodeData)
    {
        foreach ($nodeData as $key => $value)
        {
            $nodeIds[$key] = $value['node_id'];
        }
    }
    else
    {
        $nodeIds = $nodeid;
    }
    $params = array('node_id'=>$nodeIds,'fields'=>'title,article_id,article_logo','orderBy'=>$orderBy,'page_size'=>$setting['contentnum'],'platform'  =>'pc');
    $data = app::get('topc')->rpcCall('syscontent.content.get.list', $params);
    $setting['data'] = $data;
//echo '<pre>';print_r($data);exit();
    return $setting;
}
?>
