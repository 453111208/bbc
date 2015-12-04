<?php

class search_policy_mysql implements search_interface_policy {

    public $name = '开发测试环境';
    public $type = 'mysql';
    public  $description = '默认开发使用mysql搜索';

    public function index($class)
    {
        $this->index = $class;
        return $this;
    }

    public function checkColumnsReturn($columns)
    {
        return true;
    }

    public function select($cols='*', $offset=0, $limit=-1, $orderBy=null, $groupBy='')
    {
        $data['list'] = $this->index->getList($cols, $this->filter, $offset, $limit, $orderBy, $groupBy);
        $data['total_found'] = $this->count($filter);
        return $data;
    }

    public function buildExcerpts($text, $opts){
        if(empty($opts)){
            $opts=array(
                'before_match'=>'<span class="highlight">',
                'after_match'=>'</span>'
            );
        }

        $opts_str = $opts['before_match'].$this->word.$opts['after_match'];
        $text = str_ireplace($this->word,$opts_str,$text);

        return $text;
    }

    public function count()
    {
        return $this->index->count($this->filter);
    }

    public function queryFilter($filter)
    {
        $this->filter = $filter;
        $this->word = $filter['search_keywords'];
        return $this;
    }

    public function link()
    {
        return true;
    }//End Function

    public function insert($val=array())
    {
        return true;
    }

    public function update($val=array(),$where)
    {
        return true;
    }

    public function delete($val=array())
    {
        return true;
    }

    public function status(&$msg)
    {
        $msg = '已建立连接';
        return true;
    }
}//End Class

