<?php

namespace Gini\ORM\Chemical;

//Material Safety Data Sheet
class MSDS extends \Gini\ORM\Object
{
    public $cas_no = 'string:40';

    // 1. Chemical product and company identification
    // 2. Hazards identification
    // 3. Composition/information on ingredients
    // 4. First-aid measures
    // 5. Fire-fighting measures
    // 6. Accidental release measures
    // 7. Handling and storage
    // 8. Exposure controls and personal protection
    // 9. Physical and chemical properties
    // 10. Stability and reactivity
    // 11. Toxicological information
    // 12. Ecological information
    // 13. Disposal considerations
    // 14. Transport information
    // 15. Regulatory information
    // 16. Other information

    protected static $db_index = [
        'unique:cas_no',
    ];

}
