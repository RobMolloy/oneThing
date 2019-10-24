<?php
require_once('includes/common.php');

$nav = (isset($_REQUEST['nav']) ? $_REQUEST['nav'] : '');

switch($nav){
	case 'submitThing':
		$tng = new thing();
		$tng->updateDatarowFromRequest();
		$tng->save();
		echo $tng->getJson();
	break;
    
	case 'saveThing':
        $tng_id = (isset($_REQUEST['tng_id']) ? $_REQUEST['tng_id'] : '');
		$tng = new thing($tng_id);
		$tng->updateDatarowFromRequest();
		$tng->save();
		echo $tng->getJson();
	break;
    
	case 'deleteThing':
        $tng_id = (isset($_REQUEST['tng_id']) ? $_REQUEST['tng_id'] : '');
        $tng = new thing($tng_id);
		$tng->delete();
		echo $tng->getJson();
	break;

	case 'getThingList':
		$tng = new thingList();
		echo $tng->getJson();
	break;
    
    case 'getThingJson':
        $tng_id = (isset($_REQUEST['tng_id']) ? $_REQUEST['tng_id'] : '');
        $tng = new thing($tng_id);
        echo $tng->getJson();
    break;
}
?>
