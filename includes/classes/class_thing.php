<?php

class thing extends defaultObject {
    public $datarow = [];
    public $errors = [];
    public $exists = False;
    public $valid = False;
    public $success = False;
    public $newlyAdded = False;

    public $checks = ['user'=>True];
    public $labelrow = [];
    public $sensitivedatarow = [];
    public $table = ['name'=>'ont_things','primarykey'=>'tng_id','userkey'=>'tng_usr_id'];
    
    function updateDatarowBeforeSave(){
        //~ edit datarow before save
        $this->datarow['tng_usr_id'] = getUserId();
		if($this->exists){
			
		} else {
			$this->datarow['tng_time_added'] = time();
            
            if($this->valid){
                
			}
		}
    }
    
    
    function updateValidityForSave(){
		$this->valid = True;
		$this->errors = [];
		
		if($this->datarow['tng_title']==''){
			$this->valid = False;
			$this->errors['tng_title'][] = $this->labelrow['tng_title'].' cannot be blank';
		}
		
		if($this->exists){}else{}
        
		return $this->valid;
    }
    
}

?>
