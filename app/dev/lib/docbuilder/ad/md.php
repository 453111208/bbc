<?php
class dev_docbuilder_ad_md
{

    public function gendoc($api)
    {
        return view::make('dev/api_data/apiDetail.html', $api)->render();
    }

}
