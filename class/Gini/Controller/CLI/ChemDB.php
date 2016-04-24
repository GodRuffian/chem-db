<?php

namespace Gini\Controller\CLI;

class ChemDB extends \Gini\Controller\CLI {

    public function actionSearch($args) {
        
        $criteria = [
            'keyword' => '111',
            'type' => 'drug_precursor',
        ];
        
        $shChemical = new \Gini\Those\SQLHelper('chemical');

        $where = [];

        if (isset($criteria['keyword'])) {
            $orWhere = [
                $shChemical->whose('keyword')->contains($criteria['keyword'])->fragment(),
                $shChemical->whose('cas_no')->contains($criteria['keyword'])->fragment(),
                $shChemical->whose('name')->contains($criteria['keyword'])->fragment(),
            ];
            
            $where[] = '('.implode(' OR ', $orWhere).')';
        }
        
        $shType = new \Gini\Those\SQLHelper('chemical/type');
        if (isset($criteria['type'])) {
            $where[] = $shChemical->whose('type')->is($criteria['type'])->fragment();
        }

        $SQL = strtr('SELECT COUNT(DISTINCT :aliasChemical."id") FROM :tableChemical AS :aliasChemical'
            . ' LEFT JOIN :tableType AS :aliasType ON :aliasType."cas_no"=:aliasChemical."cas_no"'
            . ' WHERE '.implode(' AND ', $where), [
                ':tableChemical' => $shChemical->table(),
                ':aliasChemical' => $shChemical->tableAlias(),
                ':tableType' => $shType->table(),
                ':aliasType' => $shType->tableAlias(),            
            ]);

        echo $SQL."\n";
    }
}