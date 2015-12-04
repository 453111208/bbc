<?php
/**
 *
 */
class syslogistics_data_tracker {

    public $hqepayApiUrl = 'http://port.hqepay.com/Ebusiness/EbusinessOrderHandle.aspx';

    /**
     * @brief 从华强宝提供的的物流跟踪API，获取物流轨迹
     *
     * @param string $LogisticCode 物流单号
     * @param string $ShipperCode  快递公司编号
     *
     * @return array
     */
    public function pullFromHqepay($LogisticCode, $ShipperCode)
    {
        //请求类型 1002表示查询订单轨迹
        $RequestType = 1002;

        //华强宝配置数据
        $hqepayParams = app::get('syslogistics')->getConf('syslogistics.order.hqepay');

        //电商ID
        $EBusinessID =  !empty($hqepayParams['id']) ? $hqepayParams['id'] : '1226825';

        //AppKey
        $appkey = !empty($hqepayParams['appkey']) ? $hqepayParams['appkey'] : '9326bc57-8964-4f59-88fe-b5ced1dfd66a';

        //参数内容
        $content = "<Content><OrderCode></OrderCode><ShipperCode>{$ShipperCode}</ShipperCode><LogisticCode>{$LogisticCode}</LogisticCode></Content>";

        //签名
        $DataSign = $this->__hqepayEncrypt($content,$appkey);

        $post = array(
            'RequestType' => $RequestType,
            'EBusinessID' => $EBusinessID,
            'RequestData' => urlencode($content),
            'DataSign' => urlencode($DataSign),
        );

        $httpclient = new base_httpclient();
        $response = $httpclient->post($this->hqepayApiUrl, $post);

        $responseData = kernel::single('site_utility_xml')->xml2arrayValues($response);

        if( $responseData )
        {

            if( $responseData['Response']['Success']['value'] == 'false' )
            {
                throw new \LogicException($responseData['Response']['Reason']['value']);
            }

            if( $responseData['Response']['Success']['value'] == 'true' )
            {
                $traces = array();
                foreach( $responseData['Response']['Traces']['Trace'] as $key=>$value)
                {
                    $traces[$key]['AcceptTime'] = $value['AcceptTime']['value'];
                    $traces[$key]['AcceptStation'] = strip_tags($value['AcceptStation']['value']);
                }
                return $traces;
            }
        }
        else
        {
            throw new \LogicException(app::get('syslogistics')->_('查询失败，请到快递公司官网查询'));
        }
    }

    private function __hqepayEncrypt($content, $appkey)
    {
        return base64_encode(md5($content.$appkey));
    }
}

