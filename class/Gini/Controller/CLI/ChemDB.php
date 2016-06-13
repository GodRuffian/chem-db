<?php

namespace Gini\Controller\CLI;

class ChemDB extends \Gini\Controller\CLI
{
    public function actionCache()
    {
        $chemicals = those('chemical')->orderBy('cas_no', 'asc');
        $cacher = \Gini\Cache::of('chemical');
        $timeout = 86400 * 30;
        foreach ($chemicals as $c) {
            $key = "chemical[{$c->cas_no}]";
            $data = [
                'cas_no' => $c->cas_no,
                'name' => $c->name,
                'types' => $c->types(),
                'state' => $c->state,
                'en_name' => $c->en_name,
                'aliases' => $c->aliases,
                'en_aliases' => $c->en_aliases,
                'einecs' => $c->einecs,
                'mol_formula' => $c->mol_formula,
                'mol_weight' => $c->mol_weight,
                'inchi' => $c->inchi,
                'melting_point' => $c->melting_point,
                'boiling_point' => $c->boiling_point,
                'flash_point' => $c->flash_point,
            ];
            $cacher->set($key, $data, $timeout);
        }
        $cacher->set('chemical[allcached]', time(), $timeout);
    }

    public function actionMSDS($args) {
        $cas_no = $args[0];
        $msds = a('chemical/msds', ['cas_no' => $cas_no]);
        if (!$msds->id) {
            die("MSDS not found for $cas_no!\n");
        }
        echo yaml_emit($msds->getData()['_extra'], YAML_UTF8_ENCODING);
    }
}
