<?php
class defaultListObject {
    public $datarows = [];
    //~ public $objects = [];
    public $sensitivedatarow = [];
    public $labelrow = [];
    public $table = ['name'=>'','label'=>'','primarykey'=>'','userkey'=>''];
    public $order = '';
    public $direction = 'DESC';
    public $defaultFilters = [];
    public $filters = [];
    
    function __construct($order='',$direction='',$filters=''){
        $this->init($order,$direction,$filters);
    }
    
    function init($order='',$direction='',$filters=''){
        $this->order = ($order=='' ? $this->order : $order);
        $this->direction = ($direction=='' ? $this->direction : $direction);
        
        $this->initFilters($filters);
        
        $this->sqlString = $this->getSqlString();
        $this->sqlParams = $this->getSqlParams();
        
        $this->datarows = $this->getDatarows();
        $this->objects = $this->getObjects();
    }
    
    function setDefaultFilters(){
        //~ $this->defaultFilters = ['='=>[$this->table['userkey']=>getUserId()]];
        $this->defaultFilters = [];
    }
    
    function initFilters(){
        //~ pass ''(any string) or nothing get $this->filters
        $filters = (!is_string($filters) ? $this->filters : $filters);
        
        //~ pass blank array clears filters (except default filters)
        $filters = ($filters!=[] ? $this->filters : $filters);
        
        $this->setDefaultFilters();
        //~ default filters
        foreach($this->defaultFilters as $operator=>$array){
            foreach($array as $column=>$value){
                $filters[$operator][$column] = $value;
            }
        }
        $this->filters = $filters;
    }
    
    function getSqlString(){
        $whereArray = [];
        foreach($this->filters as $operator=>$array){
            foreach($array as $column=>$value){
                $whereArray[] = $column.$operator.'?';
            }
        }

        $sql = "SELECT * FROM ".$this->table['name']
                    .(count($whereArray)==0 ? "" : " WHERE ".implode(' AND ', $whereArray))
                    .($this->order=='' ? "" : " ORDER BY ".$this->order." ".$this->direction); 
        return $sql;
    }
    
    function getSqlParams(){
        foreach($this->filters as $operator=>$array){
            foreach($array as $column=>$value){
                $paramsArray[] = $value;
            }
        }
        
        return $paramsArray;
    }
    
    function getDatarows(){
        $datarows = [];
        
        $db = openDb();
        $stmt = $db->prepare($this->sqlString);
        $stmt = bindParameters($stmt,$this->sqlParams);
        $stmt->execute();
        $rcd = $stmt->get_result();
        
        while($row = $rcd->fetch_assoc()){
            $datarows[] = $row;
        }
        
        $db->close();
        
        return $datarows;
    }
    
    function getObjects(){
        $objects=[];
        $datarows = $this->getDatarows();
        foreach($datarows as $k=>$datarow){
            $id = $datarow[$this->table['primarykey']];
            $obj = new $this->table['label']($id);
            $objects[] = $obj->getJson();
        }
        return $objects;
    }
    
    function getSafeDatarows(){
        if(count($this->sensitivedatarow)==0){return $this->datarows;}
        
        $datarows = [];
        foreach($this->datarows as $k1=>$datarow){
            foreach($this->sensitivedatarow as $column=>$value){
                $datarow[$column] = $value;
            }
            $datarows[] = $datarow;
        }
        return $datarows;
    }
    
    function getJson(){
		$datarows = $this->getSafeDatarows();
         
		$json = [
				'labelrow'=>$this->labelrow,
				'order'=>$this->order,
				'direction'=>$this->direction,
				'filters'=>$this->filters,
				'datarows'=>$datarows,
				'objects'=>$this->objects
			];
            
        return $json;
    }
    
    function sendJson(){
        return json_encode($this->getJson());
    }
}
?>
