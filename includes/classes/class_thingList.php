<?php
class thingList extends defaultListObject {
    public $datarows = [];
    public $sensitivedatarow = [];
    public $labelrow = [];
    public $table = ['name'=>'ont_things','primarykey'=>'tng_id','userkey'=>'tng_usr_id'];
    public $order = 'tng_time_added';
    public $direction = 'DESC';
    public $defaultFilters = [];
    public $filters = [];
    
    function setDefaultFilters(){
        $this->defaultFilters = ['='=>[$this->table['userkey']=>getUserId()]];
    }
}
?>
