<?php
return array(
    'columns'=>array(
        'matrixset_id'=>array(
            'type'=>'number',
            //'pkey' => true,
            'autoincrement' => true,
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'node_id'=>array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'status'=>array(
            'type'=>array('active'=>'active','dead'=>'dead'),
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'api_url'=>array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'in_list'=>true,
            'default_in_list'=>true,

        ),
        'iframe_url'=>array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'in_list'=>true,
            'default_in_list'=>true,

        ),
        'token'=>array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'node_type'=>array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'shopname'=>array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'in_list'=>true,
            'default_in_list'=>true,
        ),
    ),
    
    'primary' => 'matrixset_id',
);
