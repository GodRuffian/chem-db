<?php
/**
* @file Chem.php
* @brief 危化品的商品数据导入
*        正常情况先，初始化方法只在部署时执行
* @author Jinlin Li <jinlin.li@geneegroup.com>
* @version 0.1.0
* @date 2016-01-13
 */

namespace Gini\Controller\CLI\Product;

class Chem extends \Gini\Controller\CLI
{
    /**
        * @brief 命令help
        *
        * @return
     */
    public function __index($params)
    {
        echo "Available commands:\n";
        echo "  gini product chem initproduct: 初始化危化品商品名录表\n";
    }

	/*
	 ** gini product chem initproduct
	 ** 生成危化品商品名录
	 */
	public function actionInitProduct()
	{
		$db = \Gini\Database::db();
        $db->query('truncate product');
        $types = ['drug_precursor', 'highly_toxic', 'hazardous', 'explosive'];
        foreach ($types as $type) {
            $csv = new \Gini\CSV(APP_PATH.'/'.DATA_DIR.'/product/'.$type.'.csv', 'r');
            $csv->read();
            while ($row = $csv->read()) {
                $cas_no = trim($row[1]);
                if (strstr($cas_no,'；')) {
                    $cas_nos = explode('；', $cas_no);
                    foreach ($cas_nos as $cas_no) {
                        if (!$cas_no) continue;
                        $sql = "INSERT INTO `product` (`name`, `cas_no`, `type`) values (".$db->quote($row[0]).",".$db->quote($cas_no).", ".$db->quote($type).")";
                        if (!$db->query($sql)) {
                            var_dump($sql);
                            echo $type."初始化失败 中断\n";
                            die;
                        }
                    }
                }
                else {
                    if (!$cas_no) continue;
                    $sql = "INSERT INTO `product` (`name`, `cas_no`, `type`) values (".$db->quote($row[0]).",".$db->quote($cas_no).", ".$db->quote($type).")";
                    if (!$db->query($sql)) {
                        var_dump($sql);
                        echo $type."初始化失败 中断\n";
                        die;
                    }
                }
            }
            echo $type."初始化完毕\n";
        }
    }

    public function actionCacheProducts()
    {
        $chemicals = those('chemical')->orderBy('cas_no', 'asc');
        $cacher = \Gini\Cache::of('chemdb');
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
    }
}
