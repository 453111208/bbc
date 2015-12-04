<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class desktop_mdl_tag extends dbeav_model{

    function __construct($app){
        parent::__construct($app);
    }

    var $defaultOrder = array('tag_id',' DESC');

    function save( &$item ,$mustUpdate = null,$mustInsert = false){
        $list = parent::getList('*',array('tag_name'=>$item['tag_name'],'tag_type'=>$item['tag_type'],'app_id'=>$item['app_id']));
        if($list && count($list)>0){
            $item['tag_id'] = $list[0]['tag_id'];
        }
        if(!$item['tag_fgcolor']&&$item['tag_bgcolor'])
              $item['tag_fgcolor'] = '#'.$this->fgcolor(substr($item['tag_bgcolor'],1));
        if(!$item['tag_bgcolor']&&$item['tag_fgcolor'])
              $item['tag_bgcolor'] = '#'.$this->fgcolor(substr($item['tag_fgcolor'],1));

        return parent::save($item);
    }
    function check_tag($item){
        return parent::count(array('tag_name'=>$item['tag_name'],'tag_type'=>$item['tag_type'],'app_id'=>$item['app_id']));
    }
    function fgcolor($rgb){
        return (hexdec($rgb{0}.$rgb{1})*14+hexdec($rgb{2}.$rgb{3})*90+hexdec($rgb{4}.$rgb{5})*14)>(30090/2)?'000000':'ffffff';
    }
    function modifier_tag_bgcolor(&$colList){
        foreach ($colList as $k => $field)
        {
            $colList[$k] = "<span style=\"background-color: $field;\" class=\"tag-label\">&nbsp;&nbsp;&nbsp;&nbsp;</span>";
        }
    }

    function modifier_tag_fgcolor(&$colList){
        foreach ($colList as $k => $field)
        {
            $colList[$k] = "<span style=\"background-color: $field;\" class=\"tag-label\">&nbsp;&nbsp;&nbsp;&nbsp;</span>";
        }
    }
}
