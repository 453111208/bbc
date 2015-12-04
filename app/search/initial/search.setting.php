<?php

return array(
    'search_index_setting_brand'=>
    array(
        'ranker'=>'proximity_bm25',
        'order_value'=>'sold_quantity',
        'order_type'=>'desc',
        'max_limit'=>'100'
    ),
    'search_index_setting_search_associate'=>
    array(
        'ranker'=>'proximity_bm25',
        'order_value'=>'id',
        'order_type'=>'desc',
        'max_limit'=>'10'
    ),
);
