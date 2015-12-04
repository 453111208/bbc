<?php
class syscontent_api_getContentList {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取文章列表';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'page_no' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1','default'=>'','example'=>''],
            'page_size' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认20条','default'=>'','example'=>''],
            'fields'=> ['type'=>'field_list','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
            'node_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'节点id','default'=>'0','example'=>''],
            'orderBy' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序','default'=>'','example'=>''],
            'platform' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'发布平台','default'=>'','example'=>''],
        );

        return $return;
    }

    public function getContentList($params)
    {
        $syscontentLibNode = kernel::single('syscontent_article_article');
        return $syscontentLibNode->getArticleList($params);
    }
}
