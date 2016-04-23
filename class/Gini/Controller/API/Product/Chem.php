<?php

/**
 * @brief API
 * @author Jinlin Li
 * @date 2016-1-13
 */
namespace Gini\Controller\API\Product;

/**
 * @brief 继承自Gini\Controller\API
 */
class Chem extends \Gini\Controller\API
{
    public function actionSearchProducts(array $criteria)
    {
		$db = \Gini\Database::db();
		$params = [];
		$types = ['highly_toxic', 'drug_precursor', 'hazardous'];
		$sql = "SELECT * FROM product ";
		if ( (isset($criteria['type']) && in_array($criteria['type'], $types)) || isset($criteria['keyword'])) {
			$sql .= ' WHERE ';
            if (isset($criteria['type']) && in_array($criteria['type'], $types)) {
                $sql .= "type=:type ";
                $params['type'] = $type;
            }
            if (isset($criteria['keyword'])) {
                $keyword = '%'.$criteria['keyword'].'%';
                $sql .= 'cas_no LIKE :cas_no OR name LIKE :name ';
                $params[':cas_no'] = $keyword;
                $params[':name'] = $keyword;
            }
		}
		$products = $db->query($sql, null, $params)->rows();
		$count = count($products);
		$token = md5(J($criteria));
		$_SESSION[$token] = [
			'criteria' => $criteria,
			'sql' => $sql,
			'params' => $params
		];
		return [
			'token' => $token,
			'count' => $count,
		];
    }

    public function actionGetProducts($token, $start = 0, $perpage = 25)
    {
		$start = is_numeric($start) ? $start : 0;
		$perpage = min($perpage, 25);
		$db = \Gini\Database::db();
		$params =  $_SESSION[$token];
		$sql = $params['sql'].' LIMIT '.$start.','.$perpage;
		$params = $params['params'];
		$products = $db->query($sql, null, $params)->rows();
		$data = [];
		foreach ($products as $product) {
			$data[$product->id] = [
				'cas_no' => $product->cas_no,
				'name' => $product->name,
				'type' => $product->type,
				'state' => $product->state
			];
		}
		return $data;
    }

    public function actionGetProduct($cas_no)
    {
    	if (!$cas_no) return false;
    	$products = Those('product')->whose('cas_no')->is($cas_no);
		if (!count($products)) return false;
		$units = \Gini\ORM\Product::$state_units;
    	$data = [];
    	foreach ($products as $product) {
    		$data[$product->type] = [
				'cas_no' => $product->cas_no,
				'name' => $product->name,
				'type' => $product->type,
				'state' => $product->state,
				'units' => $units[$product->state]
    		];
    	}
    	return $data;
    }

    public function actionGetTypes($cas_nos)
    {
        if (!is_array($cas_nos)) $cas_nos = (array)$cas_nos;
        $products = those('product')->whose('cas_no')->isIn($cas_nos);
        $result = [];
        foreach ($products as $product) {
            $result[$product->cas_no] = $result[$product->cas_no] ?: [];
            if (!in_array($product->type, $result[$product->cas_no])) {
                $result[$product->cas_no] = $product->type;
            }
        }
        return $result;
    }
}
?>
