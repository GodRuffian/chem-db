<?php

namespace Gini\Controller\API;

class ChemDB extends \Gini\Controller\API
{
    public function actionSearchChemicals(array $criteria)
    {
        $db = a('chemical')->db();
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
            if (is_array($criteria['type'])) {
                $where[] = $shChemical->whose('type')->isIn($criteria['type'])->fragment();
            } else {
                $where[] = $shChemical->whose('type')->is($criteria['type'])->fragment();
            }
        }

        if (count($where) > 0) {
            $whereSQL = ' WHERE '.implode(' AND ', $where);
        }

        $fromSQL = strtr('FROM :tableChemical AS :aliasChemical'
        . ' LEFT JOIN :tableType AS :aliasType '
        . ' ON :aliasType."cas_no"=:aliasChemical."cas_no"'. $whereSQL, [
                ':tableChemical' => $shChemical->table(),
                ':aliasChemical' => $shChemical->tableAlias(),
                ':tableType' => $shType->table(),
                ':aliasType' => $shType->tableAlias(),            
            ]);

        $countSQL = strtr('SELECT COUNT(DISTINCT :aliasChemical."id") ', [
            ':aliasChemical' => $shChemical->tableAlias(),
        ]).$fromSQL;

        $SQL = strtr('SELECT DISTINCT :aliasChemical."id" ', [
            ':aliasChemical' => $shChemical->tableAlias(),
        ]).$fromSQL;

        $count = $db->value($countSQL);

        $token = \Gini\Session::tempToken();
        $_SESSION[$token] = [
            'SQL' => $SQL,
        ];

        return [
            'token' => $token,
            'count' => $count,
        ];
    }

    private function _getData($c) {
        return [
            'cas_no' => $c->cas_no,
            'name' => $c->name,
            'types' => $c->types(),
            'titles'=> \Gini\ORM\Chemical\Type::$titles,
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
            'msds' => !!$c->msds,
        ];
    }

    public function actionGetChemicals($token, $start = 0, $per_page = 25)
    {
        $start = intval($start);
        $per_page = min(max(intval($per_page), 0), 500);
        
        $form =  $_SESSION[$token];
        $SQL = $form['SQL'].' LIMIT '.$start.','.$per_page;
        $chemicals = those('chemical')->query($SQL);

        $data = [];
        foreach ($chemicals as $chemical) {
            $data[$chemical->cas_no] = $this->_getData($chemical);
        }
        return $data;
    }

    public function actionGetChemical($cas_no)
    {
        if (!$cas_no) return false;
        $chemical = a('chemical', ['cas_no' => $cas_no]);
        if (!$chemical->id) return false;

        return $this->_getData($chemical);
    }

    // 'cas_no' => ['sss', 'ss', ...]
    public function actionGetChemicalTypes($cas_nos)
    {
        if (!is_array($cas_nos)) $cas_nos = [$cas_nos];
        $types = those('chemical/type')->whose('cas_no')->isIn($cas_nos);
        $data = [];
        foreach ($types as $type) {
            $data[$type->cas_no][] = $type->name;
        }
        return $data;
    }

    public function actionGetMSDS($cas_no)
    {
        if (!$cas_no) return false;
        $msds = a('chemical/msds', ['cas_no' => $cas_no]);
        if (!$msds->id) return false;
        return $msds->getData()['_extra'];
    }

}
?>
