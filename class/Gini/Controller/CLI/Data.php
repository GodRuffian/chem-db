<?php

namespace Gini\Controller\CLI;

class Data extends \Gini\Controller\CLI
{
    public function actionFill()
    {
        $types = ['narcotic', 'psychotropic'];
        foreach ($types as $type) {
            self::_fill($type);
        }
    }

    private static function _fill($type)
    {
        $file = dirname(__FILE__).'/'.$type.'.data';
        $handler = fopen($file, 'r');
        while (($line=fgets($handler))!==false) {
            $data = explode('#', $line);
            $name = trim($data[0]);
            $enAliases = trim($data[1]);
            $casNO = trim($data[2]);
            $aliases = trim($data[3]);
            if (!$casNO || !$name) {
                continue;
            }
            $chemical = a('chemical', ['cas_no'=>$casNO]);
            if (!$chemical->id) {
                // create new chemical
                $chemical->cas_no = $casNO;
                $chemical->name = $name;
                $chemical->aliases = $aliases;
                $chemical->en_aliases = $enAliases;
                if (!$chemical->save()) {
                    echo "\n{$casNO} create product failed.\n";
                    continue;
                }
            }
            $ct = a('chemical/type', [
                'cas_no'=> $casNO,
                'name'=> $type
            ]);
            if ($ct->id) continue;
            $ct->cas_no = $casNO;
            $ct->name = $type;
            if (!$ct->save()) {
                echo "\n{$casNO} create type failed.\n";
            }
        }
        fclose($handler);
    }
}
