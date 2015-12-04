<?php

interface search_interface_policy
{
    public function link();

    public function select($cols='*', $offset=0, $limit=-1, $orderBy=null, $groupBy='');

    public function insert($val=array());

    public function checkColumnsReturn($columns);

    public function count();

    public function update($val=array(),$where);

    public function delete($val=array());

    public function status(&$msg);

    public function queryFilter($filter);

}
