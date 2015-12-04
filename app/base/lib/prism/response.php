<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_prism_response
{
    public function send($params, $format = 'json')
    {
        return $this->_out(['error'=>null, 'result'=>$params], $format);
    }

    public function sendError($code, $message, $format = 'json', $exception = null)
    {
        $params = [
                'error' => [
                    'code' => $code,
                    'message' => $message,
                ],
                'result' => null,
            ];
        if( isset($exception) )
        {
            $params['error']['exception'] = $exception;
        }
        return $this->_out($params, $format);
    }

    private function _out($params, $format = 'json')
    {
        if($format == 'xml')
        {
   //         $xml = kernel::single('site_utility_xml')->array2xml($params);

            $xmlEngine = kernel::single('base_xml');
            $xml = $xmlEngine->array2xml( $params, 'xml' );
            echo $xml;
            exit;
        }else{
            echo json_encode($params);
            exit;
        }
        exit;
    }
}

