<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_finder_tagcols{
    var $column_tag = '标签';
    var $column_tag_order = COLUMN_IN_TAIL;
    public function column_tag(&$colList, $list)
    {
        //todo  如果list为空则 retunn
        if (!$list) return;
        
        $idColumnKey = $list[0]['idColumn'];
        $appId = $list[0]['app_id'];
        $tagType = $list[0]['tag_type'];

        $list = utils::array_change_key($list, $idColumnKey);

        // 获取所有当前finder主键列表
        $relatedIds = array_keys(utils::array_change_key($list, $idColumnKey));

        $filter = array('rel_id'=>$relatedIds, 'tag_type'=>$tagType,'app_id'=>$appId);
        // 获取tag列表
        $tagRows = app::get('desktop')->model('tag_rel')->getList('tag_id, rel_id',$filter);

        /*-----start------>*/
        $tagIds = array_keys(utils::array_change_key($tagRows, 'tag_id'));

        
        if ($tagIds)
        {
            $tagList = app::get('desktop')->model('tag')->getList('*',array('tag_id'=>$tagIds));
            $tagList = utils::array_change_key($tagList, 'tag_id');
        }
        /*<----end-------->*/

        foreach($tagRows as $row)
        {
            $relatedRows[$row['rel_id']][] = $tagList[$row['tag_id']];
        }

        $i = 0;
        foreach($relatedIds as $id)
        {
            $colList[$i] = $this->getColumnTagHtml($relatedRows[$id]);
            $i++;
        }
    }

    private function getColumnTagHtml($rows)
    {
        foreach($rows as $row){
            $color_str = '';
                
            if($row['tag_fgcolor']){
                $color[] = 'color:'.$row['tag_fgcolor'];
            }
            if($row['tag_bgcolor']){
                $color[] = 'background-color:'.$row['tag_bgcolor'];
            }
            if($row['tag_bgcolor']&&is_array($color)) $color_str = implode(';',$color);
            $return .= '<span class="tag-label" style="'.$color_str.'">'.$row['tag_name'].'</span>';
        }
        return $return;
        
    }
}
