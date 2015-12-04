<?php
class dev_docbuilder_dd
{
    public function export()
    {
        $titleName = "BBC数据词典\n\n";
        $tablesOutlineHtml = $this->getDocumentTablesOutlineHtml();
        $appsDocumentHtml = $this->getAppsDocumentHtml();
        return view::make('dev/data_dictionary/main.html', compact('titleName', 'tablesOutlineHtml', 'tablesDocumentHtml', 'appsDocumentHtml'))->render();
    }

    public function getDocumentTablesOutlineHtml()
    {
        $appsOutline = [];
        $count = 0;
        foreach ($this->getAppIds() as $appId)
        {
            $tablesOutline = [];
            foreach(kernel::single('base_application_dbtable')->detect($appId) as $item)
            {
                $tablesOutline[$item->real_table_name()] = $item->load();
                $count++;
            }
            $appsOutline[$appId] = $tablesOutline;
        }
        
        return view::make('dev/data_dictionary/outline.html', compact('appsOutline', 'count'))->render();
    }

    public function getAppsDocumentHtml()
    {
        $apps = [];
        foreach ($this->getAppIds() as $appId)
        {
            $db = app::get($appId)->database();
            $platform = $db->getDatabasePlatform();
            
            $tables = [];
            foreach(kernel::single('base_application_dbtable')->detect($appId) as $item)
            {
                $tableDefine = [];

                $table = $item->createTableSchema()->getTable($item->real_table_name());
                $tableSchemaDefine = $item->load();
                foreach($table->getColumns() as $columnName => $column)
                {
                    $tableDefine['columns'][$columnName] = array(
                        'name' => $columnName,
                        'type' => $column->getType()->getSQLDeclaration($column->toArray(), $platform),
                        'comment' => $column->getComment(),
                        'notnull' => $column->getNotnull() ?  'Yes' : 'No',
                        'default' => $column->getDefault(),
                        'autoincrement' => $column->getAutoincrement() ? 'Yes' : 'No',
                    );
                    if (is_array($tableSchemaDefine['columns'][$columnName]['type']))
                    {
                        if ($tableDefine['columns'][$columnName]['comment'])  $tableDefine['columns'][$columnName]['comment'] .= '\|';

                        $tableDefine['columns'][$columnName]['comment'] .= $this->convertEnumArrayToComment($tableSchemaDefine['columns'][$columnName]['type']);
                    }
                }

                foreach ($table->getIndexes() as $indexName => $index)
                {
                    $tableDefine['index'][$indexName]['columns'] = implode(', ', $index->getColumns());
                    $tableDefine['index'][$indexName]['isUnique'] = $index->isUnique() ? 'Yes' : 'No';
                }
                // 表comment
                $tableDefine['comment'] = $tableSchemaDefine['comment'];
                
                $tables[$item->real_table_name()] = $tableDefine;
            }
            $apps[$appId] = $tables;
            
        }
        //        var_dump($apps);exit;
        return view::make('dev/data_dictionary/document_apps.html', compact('apps'))->render();
    }

    public function getTableNamesByAppId($appId)
    {
        $tableNames = [];
        foreach (kernel::single('base_application_dbtable')->detect($appId) as $item)
        {
            $tableNames[] = $item->real_table_name();
        }
        return $tableNames;
    }

    public function getAppIds()
    {
        $d = dir(APP_DIR);
        while (false !== ($entry = $d->read())) {
            if ($entry!='.' && $entry!='..') {
                if (is_dir(APP_DIR.'/'.$entry)) {
                    $apps[] = $entry;
                }
            }
        }
        $d->close();
        return $apps;
    }

    public function convertEnumArrayToComment($enum)
    {
        $output = '';
        foreach($enum as $key => $value )
        {
            $output .= $key.':'.$value.';';
        }
        return $output;
    }
}