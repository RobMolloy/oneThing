<?php
class defaultObject {
    public $exists = False;
    public $datarow = [];
    public $errors = [];
    public $valid = False;
    public $success = False;
    public $newlyAdded = False;
    
    //~ rows below should be edited for each object
    public $checks = [];
    //~ public $checks = ['user'=>True];
    public $sensitivedatarow = [];
    public $labelrow = [];
    public $table = ['name'=>'','label'=>'','primarykey'=>'','userkey'=>''];
	
    function __construct($id=''){
        $this->init($id);
    }
    
    function init($id=''){
        //~ required when no id is passed into new object
        //~ populates the value of each key of this->datarow with current info or blank
		$this->populateDatarow();
        
        //~ populates labelrow with clean name or db name if $dev
		$this->labelrow = $this->getLabelRow();
		
        //~ get id from init parameter or this->datarow[..._id]
		$id = ($id!='' ? $id : $this->datarow[$this->table['primarykey']]);
        
        //~ get table row from db
		$result = $this->getTableRowUsingId($id);
        
        $resultValid = $this->resultValid($result);
		if($result!==False && $resultValid){
			$this->exists = True;
			$this->datarow = $result;
		} else {
			$this->exists = False;
            $this->datarow = $this->getEmptyDatarow();
            $this->datarow[$this->table['primarykey']] = $id;
        }
    }
    
    function resultValid($result){
        $valid = True;
        if($result===False){$valid = False;}
        
        $userCheck = (isset($this->checks['user']) ? $this->checks['user'] : False);
        if($userCheck){
            if($result[$this->table['userkey']]!=getUserId()){$valid = False;}
        }
        
        return $valid;
    }
    
    function getFromRequest(){
		$datarow = $this->datarow;
		foreach($datarow as $k=>$v){
			$datarow[$k] = (isset($_REQUEST[$k]) ? $_REQUEST[$k] : $v);
		}
		return $datarow;
    }
    
    function updateDatarowFromRequest(){
		$this->datarow = $this->getFromRequest();
    }
    
    function getEmptyDatarow(){
        $db = openDb();
        $stmt = $db->prepare("SELECT * ,IFNULL(max(".$this->table['primarykey']."),'') as thisid FROM ".$this->table['name']." LIMIT 1");
        $stmt->execute();
         
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        unset($row['thisid']);
        foreach($row as $k=>$v){$row[$k] = '';}
        
        $db->close();
        return $row;
    }
    
    function populateDatarow(){
		$updatedDatarow = $this->getEmptyDatarow();

		foreach($this->datarow as $k=>$v){
			$updatedDatarow[$k] = $v;
		}
		$this->datarow = $updatedDatarow;
    }
    
    function getTableRowUsingId($id=''){
		$id = ($id=='' ? $this->datarow[$this->table['primarykey']] : $id);
		return getTableRowUsingId($this->table['name'], $this->table['primarykey'], $id);
    }
    
    function updateDatarowUsingId($id=''){
		$id = ($id=='' ? $this->datarow[$this->table['primarykey']] : $id);
		
		$this->datarow = $this->getTableRowUsingId($id);
		if($this->datarow===False){
			$this->datarow = $this->getEmptyDatarow();
			return False;
		}
		return True;
    }
    
    function getLabelRow(){
		global $dev;
		if($this->labelrow==[] || $dev){
			$labelrow = $this->getEmptyDatarow();
			foreach($labelrow as $k=>$v){$labelrow[$k] = $k;}
			
		} else {
			$labelrow = $this->labelrow;
		}
		return $labelrow;
    }
    
    function delete(){
        $this->updateValidityForDelete();			

        $datarow = $this->datarow;
        $id = $datarow[$this->table['primarykey']];
        unset($datarow[$this->table['primarykey']]);
			
		if($this->valid){
			$db = openDb();
            $sql = "DELETE FROM ".$this->table['name']." WHERE ".$this->table['primarykey']."=?";
            $stmt = $db->prepare($sql);
			$stmt = bindParameters($stmt,$id);
			$stmt->execute();
            $id = $db->insert_id;
            $db->close();
        }
        
        $this->success = ((int)$id==0 ? True : False);
        $this->init($this->datarow[$this->table['primarykey']]);
        return $id;
        
    }
    
    function save(){
		$this->updateValidityForSave();
			
		//~ if($this->valid){
			//~ $this->updateValidityForSave();
        //~ }
			
		if($this->valid){
			$db = openDb();
			$this->updateDatarowBeforeSave();
			
			$datarow = $this->datarow;
			$id = $datarow[$this->table['primarykey']];
			unset($datarow[$this->table['primarykey']]);
			
			if($this->exists){
				$keyQmarkString = implode(array_keys($datarow),'=?,').'=?';
				$datarow[$this->table['primarykey']] = $id;
				$sql = "UPDATE ".$this->table['name']." SET ".$keyQmarkString." WHERE ".$this->table['primarykey']."=?";
			} else {
				$keyString = implode(array_keys($datarow),',');
				$qmarkString = implode(array_fill(0,count($datarow),'?'),',');
				$sql = "INSERT INTO ".$this->table['name']." (".$keyString.") VALUES (".$qmarkString.");";	
			}
			$stmt = $db->prepare($sql);
			$stmt = bindParameters($stmt,$datarow);
			$stmt->execute();
            if($this->exists){
                $this->success = ($db->affected_rows>0 ? True : False);
            } else {
                $id = $db->insert_id;
                $this->success = ((int)$id>0 ? True : False);
            }
            $this->newlyAdded = ($this->success && !$this->exists ? True : False);
			$db->close();
			
			$this->init($id);
			return $id;
		}
    }
    
    function updateDatarowBeforeSave(){
		if($this->exists){
			
		} else {
			//~ edit before save
			
            if($this->valid){
                
			}
		}
    }
    
    function updateValidityForDelete(){
        $this->valid = True;
        $this->errors = [];
        
        if($this->exists){
            $userCheck = (isset($this->checks['user']) ? $this->checks['user'] : False);
            if($userCheck){
                if($this->datarow[$this->table['userkey']]!=getUserId()){$this->valid=False;}
                $this->errors[0][] = 'This item could not be accessed';
            }
        } else {
            $this->valid = False;
            $this->errors[0][] = 'The item could not be accessed';
        }
    }
    
    function updateValidityForSave(){
		$this->valid = True;
		$this->errors = [];
		
		if(1==2){
			$this->valid = False;
			$this->errors[0][] = 'An error occurred';
		}
		
		if($this->exists){
			//~ checks on existing item
			if(1==2){
				$this->valid = False;
				$this->errors['datarow_key'][] = 'An error occurred';
			}
			
		} else {
			//~ checks on new items
			if(1==2){
				$this->valid = False;
				$this->errors['datarow_key_2'][] = 'An error occurred';
			}
		}
		return $this->valid;
    }
    
    function getSafeDatarow(){
        $datarow = $this->datarow;
		foreach($this->sensitivedatarow as $k=>$v){
            $datarow[$k] = $v;
        }
        return $datarow;
    }
    
    function getJson(){
		//~ $datarow = $this->datarow;
		//~ foreach($this->sensitivedatarow as $k=>$v){$datarow[$k] = $v;}
		$datarow = $this->getSafeDatarow();
        return [
				'exists'=>$this->exists,
				'valid'=>$this->valid,
				'success'=>$this->success,
                'newlyAdded'=>$this->newlyAdded,
				'errors'=>$this->errors,
				'datarow'=>$datarow,
				'labelrow'=>$this->labelrow
			];
    }
    
    function sendJson(){
        return json_encode($this->getJson());
    }
}
?>
