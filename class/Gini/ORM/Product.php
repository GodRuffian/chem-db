<?php

namespace Gini\ORM;

class Product extends Object
{
    public $type = 'string:20'; // 化学试剂类别 易制毒、危险品、剧毒品、普通试剂
    public $name = 'string:150'; // 商品名称
    public $cas_no = 'string:40'; // 化学试剂CAS号
    public $state = 'string:20';

    protected static $db_index = [
        'unique:cas_no,type',
        'cas_no',
    ];


	public static $type_titles = [
	    'drug_precursor' => '易制毒',
	    'hazardous' => '危险品',
	    'highly_toxic' => '剧毒品',
	    'explosive' => '易制爆'
	];

}
