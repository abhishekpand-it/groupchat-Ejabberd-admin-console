<?php
include_once('Controller/GroupChatController.php');

if(!empty($_SERVER['QUERY_STRING'])){
    //reads and explodes the querystring into array eg
    //index.php?Controller/getAllCars
    //where Controller end up in $querArray[0]=Controller
// and getAllCars end up in $queryArray[1]=getAllCars
    $queryArray=explode('/',$_SERVER['QUERY_STRING']);
  
    $query=explode('&',$_SERVER['QUERY_STRING']);
    $query = explode('=', $query[1]);
    $query = explode('/', $query[1]);

    $action = $query[0];
		
	switch ($action) {
	    case "groupchat":

		    $controller=new GroupChatController();

		    if(isset($query[1]) && !empty($query[1])){
			$controller->$query[1]();
		    }
		    else{
			header('Location: /groupchat/login');
		    }
		break;
	  
	    default:
		    echo "Page not found";
	}
    

}else{
header('Location: index.php?login');
die;  
}
?>
