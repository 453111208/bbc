<?php

class search_object
{
    /**
     * 提供搜索的server
     */
    public $searchServer = null;

    public $searchExtends = false;

    public function __construct()
    {
        $policyName = app::get('search')->getConf('search_server_policy');
        if( !$policyName )
        {
            //默认使用mysql
            $policyName = 'search_policy_mysql';
        }

        $this->searchServer = kernel::single($policyName);

        if( !$this->searchServer->status() )
        {
            throw new \Exception('搜索服务连接异常');
        }

    }

    /**
     * 初始化调用对应索引
     *
     * @param string $index
     *
     * @return object
     */
    public function instance($index)
    {
        $indexConf = config::get('search.index.'.$index);
        if( !$indexConf )
        {
            throw new \Exception('执行的搜索索引未定义');
        }

        $this->page(0,200);
        $this->orderBy();
        $this->groupBy();
        $this->buildExcerpts();

        $this->extends = $indexConf['extends'] ? kernel::single($indexConf['extends']) : false;

        if( $this->searchServer->type == 'mysql' )
        {
            if( $indexConf['extends'] )
            {
                $this->index = $this->extends;
            }
            else
            {
                $this->index = app::get($indexConf['app'])->model($indexConf['model']);
            }
        }
        else
        {
            $this->index = $indexConf['name'];
            $this->model = app::get($indexConf['app'])->model($indexConf['model']);
        }

        return $this;
    }//End Function

    public function count($filter)
    {
        $total = $this->searchServer->index($this->index)
            ->queryFilter($filter)
            ->count();

        return $total;
    }

    /**
     * 执行搜索
     *
     * @param $cols 需要返回的字段
     */
    public function search($cols='*', $filter)
    {
        $searchPolicy = $this->searchServer->index($this->index);

        //判断搜索引擎是否能够返回所需要返回的字段
        if( $searchPolicy->checkColumnsReturn($cols) )
        {
            $resultData = $searchPolicy->queryFilter($filter)
                ->select($cols, $this->offset, $this->limit, $this->orderBy, $this->groupBy);
        }
        else
        {
            $tmpResultData = $searchPolicy->queryFilter($filter)
                ->select('id', $this->offset, $this->limit, $this->orderBy, $this->groupBy);

            $resultData = $this->__preResultData($tmpResultData, $cols);
        }

        $data['list'] = $this->__buildExcerptsString($resultData['list']);
        $data['total_found'] = $resultData['total_found'];

        return $data;
    }

    private function __buildExcerptsString($data)
    {

        if( empty($data) ) return $data;

        foreach( $data as $key => &$row )
        {
            if( !$this->buildExcerptsCols ) break;

            if( $row[$this->buildExcerptsCols] && is_object($this->searchServer) && method_exists($this->searchServer, 'buildExcerpts') )
            {
                $row[$this->buildExcerptsCols] = $this->searchServer->index($this->index)->buildExcerpts($row[$this->buildExcerptsCols]);
            }
        }

        return $data;
    }

    /**
     * 如果使用sphinx搜索，则因为返回的为ID主键，因此需要进行查询处理
     *
     * @param $resultData sphinx查询返回的数据
     * @param $cols 搜索需要返回的字段
     */
    protected function __preResultData($resultData, $cols)
    {
        if( empty($resultData['list']) ) return array();

        $idColumn = $this->model->idColumn;
        $data = array();
        foreach($resultData['list'] as $row)
        {
            $filter[$idColumn][] = $row['id'];
        }

        if( is_object($this->extends) && method_exists($this->extends, 'getList') )
        {
            $mysqlResult = $this->extends->getList($cols,$filter);
        }
        else
        {
            $mysqlResult = $this->model->getList($cols,$filter);
        }

        //排序
        $sortarr = array_flip($filter[$idColumn]);
        foreach($mysqlResult as $row)
        {
            $k = $sortarr[$row[$idColumn]];
            $data['list'][$k] = $row;
        }
        ksort($data['list']);
        $data['total_found'] = $resultData['total_found'];

        return $data;
    }

    /**
     * 对搜索的数据进行高亮显示
     *
     * @param $isBuildExcerpts 是否需要高亮
     * @param $cols 需要高亮的字段
     */
    public function buildExcerpts($isBuildExcerpts, $cols)
    {
        if( $isBuildExcerpts )
        {
            $this->buildExcerptsCols = $cols;
        }
        return $this;
    }

    public function page($offset, $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        return $this;
    }

    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function groupBy($groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }
}

