<?php

namespace Gini\ORM;

class Chemical extends \Gini\ORM\Object
{
    public $cas_no = 'string:40'; // 化学试剂CAS号
    public $name = 'string:150'; // 化学品名称
    public $state = 'string:20';

    public $en_name = 'string:150';

    public $aliases = 'string:250';
    public $en_aliases = 'string:250';

    public $mol_weight = 'double';
    public $mol_formula = 'string';

    public $einecs = 'string:40';
    public $inchi = 'string:250,null';
    
    public $melting_point = 'double,null';
    public $boiling_point = 'double,null';
    public $flash_point = 'double,null';

    // 以下暂时进入扩展属性
    // public $ec_hazard_codes = 'string';
    // public $ec_risk_codes = 'string';
    // public $ec_safety_codes = 'string';

    // public $hmis_rating = 'string';

    // public $density = 'string:80';
    // public $solubility = 'string:80';

    protected static $db_index = [
        'unique:cas_no',
        'unique:inchi',
    ];

    public function types() {
        return array_values(those('chemical/type')->whose('cas_no')->is($this->cas_no)->get('id', 'name'));
    }
}
