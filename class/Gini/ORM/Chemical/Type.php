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
        'explosive' => '易制爆',
        'psychotropic'=> '精神药品',
        'narcotic'=> '麻醉药品',
        'gas'=> '气体'
    ];

    public static $availableTypes = [
        'drug_precursor' => [
            'title' => '易制毒',
            'abbr' => '毒',
        ],
        'hazardous' => [
            'title' => '危险品',
            'abbr' => '危'
        ],
        'highly_toxic' => [
            'title' => '剧毒品',
            'abbr' => '剧',
        ],
        'explosive' => [
            'title' => '易制爆',
            'abbr' => '爆',
        ],
        'psychotropic'=> [
            'title'=> '精神药品',
            'abbr'=> '精'
        ],
        'narcotic'=> [
            'title'=> '麻醉药品',
            'abbr'=> '麻'
        ],
        'gas'=> [
            'title'=> '气体',
            'abbr'=> '气'
        ]
    ];
}
