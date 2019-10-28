<?php
class user {
    public $exists = False;
    public $datarow = [];
    public $sensitivedatarow = ['usr_password'=>''];
    public $labelrow = [];
    public $table = ['name'=>'ont_users','primarykey'=>'usr_id'];
    public $tablename = 'ont_users';
    public $tableprimarykey = 'usr_id';
    public $errors = [];
    public $valid = False;
	
    function __construct($id=''){
        $this->init($id);
    }
    
    function init($id=''){
        $this->populateDatarow();
        $this->labelrow = $this->getLabelRow();
        
        $this->datarow[$this->tableprimarykey] = ($id!='' ? $id : $this->datarow[$this->tableprimarykey]);
        $result = $this->getTableRowUsingId($this->datarow[$this->tableprimarykey]);
        
        if($result!==False){
            $this->datarow = $result;
            $this->exists = True;
        } else {
            $this->datarow = $this->getEmptyDatarow();
            $this->exists = False;
        }
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
    
    function submitLogin(){
        $this->updateDatarowFromRequest();
        $this->loginCheck();
        
        if($this->valid){
            $this->login();
        }
        return $this->getJson();
    }
    
    function login(){
        //~ $this->logout();
        if(!isset($_SESSION)){
            ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7);
            ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7);
            session_start();
        }
        global $projectName;
        $_SESSION['projectName'] = $projectName;
        $_SESSION['usr_id'] = $this->datarow['usr_id'];
        $_SESSION['usr_email'] = $this->datarow['usr_email'];
        $_SESSION['usr_first_name'] = $this->datarow['usr_first_name'];
        $_SESSION['usr_last_name'] = $this->datarow['usr_last_name'];
    }
    
    function loginCheck(){
        $this->valid = True;
        
        $emailResult = $this->getTableRowUsingEmail();
        
        if($emailResult===False){
            $this->valid = False;
            $this->errors['usr_email'][] = 'There is not an account registered to this email address';
        } 
        if(!password_verify($this->datarow['usr_password'],$emailResult['usr_password'])){
            $this->valid = False;
            $this->errors['usr_password'][] = 'Incorrect password';
        }
        if($this->valid){
            $this->exists = True;
            $this->datarow = $emailResult;
            $this->init();
        }
        return $this->valid;
    }
    
    function submitLogout(){
        $this->init();
        $this->logout();
        return $this->getJson();
    }
    
    function logout(){
        if(!isset($_SESSION)){session_start();}
        session_unset();
        session_destroy();
    }
    
    function getEmptyDatarow(){
        return getEmptyTableRow($this->tablename,$this->tableprimarykey);
    }
    
    function populateDatarow(){
        $updatedDatarow = $this->getEmptyDatarow();
        foreach($this->datarow as $k=>$v){
            $updatedDatarow[$k] = $v;
        }
        $this->datarow = $updatedDatarow;
    }
    
    function getTableRowUsingEmail($email=''){
        $email = ($email=='' ? $this->datarow['usr_email'] : $email);
        
        if($email!=''){
            $db = openDb();
            
            $stmt = $db->prepare("SELECT * FROM ".$this->tablename." WHERE usr_email=? LIMIT 1;");
            $stmt = bindParameters($stmt,$this->datarow['usr_email']);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $db->close(); 
            
            if($result->num_rows==1){
                return $result->fetch_assoc();
            }
        }
        return False;
    }
    
    function updateDatarowUsingEmail($email=''){
        $email = ($email=='' ? $this->datarow[$usr_email] : $email);
        
        $this->datarow = $this->getTableRowUsingEmail($email);
        if($this->datarow===False){
            $this->datarow = $this->getEmptyDatarow();
            return False;
        }
        return True;
    }
    
    function getTableRowUsingId($id=''){
        $id = ($id=='' ? $this->datarow[$this->tableprimarykey] : $id);
        return getTableRowUsingId($this->tablename, $this->tableprimarykey, $id);
    }
    
    function updateDatarowUsingId($id=''){
        $id = ($id=='' ? $this->datarow[$this->tableprimarykey] : $id);
        
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
    
    function save(){
        $this->updateValidityForSave();
            
        if($this->valid){
            $db = openDb();
            $this->updateDatarowBeforeSave();
            
            $datarow = $this->datarow;
            $id = $datarow[$this->tableprimarykey];
            unset($datarow[$this->tableprimarykey]);
            
            if($this->exists){
            $keyQmarkString = implode(array_keys($datarow),'=?,').'=?';
            $datarow[$this->tableprimarykey] = $id;
            $sql = "UPDATE ".$this->tablename." SET ".$keyQmarkString." WHERE ".$this->tableprimarykey."=?";
            
            } else {
            $keyString = implode(array_keys($datarow),',');
            $qmarkString = implode(array_fill(0,count($datarow),'?'),',');
            $sql = "INSERT INTO ".$this->tablename." (".$keyString.") VALUES (".$qmarkString.");"		;
            
            }
            
            $stmt = $db->prepare($sql);
            $stmt = bindParameters($stmt,$datarow);
            $stmt->execute();
            
            $id = $db->insert_id;
            $db->close();
            
            $this->init($id);
            return $id;
        }
    }
    
    function updateDatarowBeforeSave(){
        if($this->exists){
            
        } else {
            //~ hash password if valid
            if($this->valid){
            $this->datarow['usr_password'] = password_hash($this->datarow['usr_password'], PASSWORD_DEFAULT);
            }
        }
    }
    
    function updateValidityForSave(){
        $this->valid = True;
        $this->errors = [];
        
        if(empty($this->datarow['usr_password']) || empty($_REQUEST['usr_password_repeat']) || empty($this->datarow['usr_email']) || empty($this->datarow['usr_first_name']) || empty($this->datarow['usr_last_name'])){
            $this->valid = False;
            $this->errors[0][] = 'A mandatory field is empty';
        }
        
        if(!filter_var($this->datarow['usr_email'], FILTER_VALIDATE_EMAIL)){
            $this->valid = False;
            $this->errors['usr_email'][] = 'Email address invalid';
        }
        
        if($this->exists){
            //~ checks on existing users
            
        } else {
            //~ checks on new users
            
            //~ email must be unique
            if($this->getTableRowUsingEmail()!==False){
            $this->valid = False;
            $this->errors['usr_email'][] = 'An account with this email address already exists';
            }
            
            //~ password must be longer than x characters
            $x = 7;
            if(strlen($this->datarow['usr_password'])<=$x){
            $this->valid = False;
            $this->errors['usr_password'][] = 'Password less than '.$x.' characters long';
            }
            
            //~ passwords must match
            if(!isset($_REQUEST['usr_password_repeat']) || $_REQUEST['usr_password_repeat']!=$this->datarow['usr_password']){
            $this->valid = False;
            $this->errors['usr_password'][] = 'Passwords do not match';
            }
        }
        return $this->valid;
    }
    
    function submitSignup(){
        $this->updateDatarowFromRequest();
        $this->save();
        return $this->getJson();
    }
    
    function getJson(){
        $datarow = $this->datarow;
        foreach($this->sensitivedatarow as $k=>$v){$datarow[$k] = $v;}
        return json_encode([
            'exists'=>$this->exists,
            'datarow'=>$datarow,
            'labelrow'=>$this->labelrow,
            'errors'=>$this->errors,
            'valid'=>$this->valid
            ]);
    }
}

?>
