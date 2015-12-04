<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_finder_builder_tag extends desktop_finder_builder_prototype{

    function main(){
        $tagctl = app::get('desktop')->model('tag');
        $tag_rel = app::get('desktop')->model('tag_rel');
        $tags = $tagctl->getList('tag_id,tag_name,tag_abbr,tag_bgcolor,tag_fgcolor',array('tag_type'=>$this->object->table_name(),
            'app_id'=>$this->app->app_id
//            ,'tag_mode'=>'normal'
        ));
        $filter['tag_type'] = $this->object->table_name();
        $rel_mdl = $this->app->model($filter['tag_type']);
        $filter['app_id'] = $this->app->app_id;
        $filter['rel_id|in'] = $_POST[$rel_mdl->idColumn];
        if($_POST['isSelectedAll']=='_ALL_'){
            $view_filter = $this->finder_get_view_filter();
            $_POST = array_merge($_POST,$view_filter);
            unset($_POST['isSelectedAll']);
            $_filter = $_POST;
            if($_filter['_finder']) unset($_filter['_finder']);
            $filter = array_merge($_filter,$filter);
        }else{
            $filter[$this->dbschema['idColumn']] = $_POST[$this->dbschema['idColumn']];
        }

        $rel_tag_list = $tag_rel->getList('*',$filter,0,-1);
        if($rel_tag_list){
            foreach($rel_tag_list as $k=>$v){
                $tmp[$v['rel_id']][] = $v['tag_id'];
                $used_tag[$v['tag_id']] = 1;
            }
        }
        $i=0;

        if($tmp){
            if(is_array($_POST['goods_id'])){
                foreach($_POST['goods_id'] as $gk=>$gv){//计算标签的交集
                    if(!isset($tmp[$gv])){
                         $tmp[$gv][0] = null;
                    }
                }
            }
            foreach($tmp as $rel_id=>$rel_tags){//计算标签的交集
                if($i++==0){
                    $intersect = $rel_tags;
                }else{
                    $intersect = array_intersect($intersect,$rel_tags);

                }
                if(!$intersect) break;
            }
        }


        $filter = $_POST;
        unset($filter['_finder']);
        $pagedata['count'] = $this->object->count($filter);
        $pagedata['tags'] = (array)$tags;
        $pagedata['filter'] = serialize($filter);
        $pagedata['object_name'] = $this->object_name;
        $pagedata['used_tag'] = array_keys((array)$used_tag);
        $pagedata['intersect'] = (array)$intersect;
        $pagedata['url'] = $this->url;

        return view::make('desktop/common/tagsetter.html', $pagedata)->render();
    }

}
