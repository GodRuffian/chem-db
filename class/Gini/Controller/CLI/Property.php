<?php

namespace Gini\Controller\CLI;

class Property extends \Gini\Controller\CLI
{
    public function actionFillData($params =[])
    {
        $csv = new \Gini\CSV(APP_PATH.'/'.DATA_DIR.'/'.$params[0], 'r');
        $csv->read();
        $line = 2;
        while ($data = $csv->read()) {
            if ($data['1']) {
                $type = a('chemical/type');
                $type->cas_no = trim($data['1']);
                $type->name = trim($data['2']);

                if (!$type->save()) {
                    echo $line."--fail \n";
                    $line++;
                    continue ;
                }
                echo $line."--done \n";
            }
            $line++;
        }
        $csv->close();
    }
}
