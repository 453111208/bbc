<?php
/**
 * 直接导出，不通过队列
 */
class importexport_export {

    public function fileDownload($filetype, $model, $filename, $filter)
    {
        $filetypeObj = kernel::single('importexport_type_'.$filetype);

        $filetypeObj->set_queue_header($filename.'.'.$filetype);

        if( method_exists($filetypeObj, 'setBom') )
        {
            $bom = $filetypeObj->setBom();
            echo $bom;
        }

        $this->export($filetype, $model, $filter);
    }

    private function export($filetype, $model, $filter)
    {
        //实例化导出数据类
        $dataObj = kernel::single('importexport_data_object',$model);


        //实例化导出文件类型类
        $filetypeObj = kernel::single('importexport_type_'.$filetype);

        //加入文件头部数据
        $fileHeader = $filetypeObj->fileHeader();
        if( $fileHeader )
        {
            echo $fileHeader;
        }

        //导出数据写到本地文件
        $offset = 0;
        while( $listFlag = $dataObj->fgetlist($data,$filter,$offset) )
        {
            $offset++;
            $rs = $filetypeObj->arrToExportType($data);
            echo $rs;
        }

        if( !$rs )
        {
            echo  '数据为空';
        }

        //加入文件尾部数据
        $fileFoot = $filetypeObj->fileFoot();
        if( $fileFoot )
        {
            echo $fileFoot;
        }
    }
}
