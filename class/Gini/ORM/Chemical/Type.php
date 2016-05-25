<?php

namespace Gini\ORM\Chemical;

// INSERT INTO chemical_type (`cas_no`, `name`) SELECT `cas_no`, `type` AS `name` FROM `product`
class Type extends \Gini\ORM\Object
{
    public $cas_no = 'string:40';
    public $name = 'string:20';     // 类型名称: highly_toxic, drug_precursor, hazardous

    protected static $db_index = [
        'unique:cas_no,name',
        'cas_no',
        'name',
    ];

	public static $titles = [
	    'drug_precursor' => '易制毒',
	    'hazardous' => '危险品',
	    'highly_toxic' => '剧毒品',
	    'explosive' => '易制爆'
	];
}
