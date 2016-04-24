<?php

/**
 * @brief API
 * @author Jinlin Li
 * @date 2016-1-13
 */
namespace Gini\Controller\API;

/**
 * @brief 继承自Gini\Controller\API
 */
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
            $where[] = $shChemical->whose('type')->is($criteria['type'])->fragment();
        }

        $fromSQL = strtr('FROM :tableChemical AS :aliasChemical'
            . ' LEFT JOIN :tableType AS :aliasType ON :aliasType."cas_no"=:aliasChemical."cas_no"'
            . ' WHERE '.implode(' AND ', $where), [
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

    private function _getData($chemical) {
        return [
            'cas_no' => $chemical->cas_no,
            'name' => $chemical->name,
            'types' => $chemical->types(),
            'state' => $chemical->state,
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

    public function actionGetChemicalTypes($cas_nos)
    {
        if (!is_array($cas_nos)) $cas_nos = [$cas_nos];
        return those('chemical/type')->whose('cas_no')->isIn($cas_nos)->get('cas_no', 'name');
    }
}
?>
