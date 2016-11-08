<?php
/**
 * Description of Groupchat API
 *
 * @author Abhishek Pandit
 */
	require_once("../Lib/Rest.inc.php");
	require_once("../Controller/EjabberdController.php");

	class API extends REST {

		
		private $user = "admin";
		private $pass = "password";
	

		public function __construct(){
			parent::__construct();
		}

		public function login(){
			
			if(isset($this->_request['username']))
				$username = $this->_request['username'];

			if(isset($this->_request['password']))
				$password = $this->_request['password'];
			
			
			if($username == $this->user && $password == $this->pass){

				$success = array('status' => "Success");
				$this->response($this->json($success), 200);
			}else {
				$error = array('status' => "Failed", "msg" => "Incorrect username/pass");
				$this->response($this->json($error), 200);
			}				
			
		}

		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);
		}


		private function get_rooms(){

			$ejc = new EjabberdController();
			$result = $ejc->getMucOnlineRooms();

			if($result) {
				$success = array('status' => "Success", 'data' => $result);
				$this->response($this->json($success), 200);
			}
			else {
				$error = array('status' => "Failed", "msg" => "Invalid parameters sent");
				$this->response($this->json($error), 400);
			}

		}
		

		//Create Room
		/**
		 *
		 * Example : http://localhost/groupchat/create_room?room_name=test
		 *
		 * Set room_name to name of the room
		 *
		 */

		private function create_room(){

			$ejc = new EjabberdController();


			if(isset($this->_request['room_name']))
				$roomName = $this->_request['room_name'];

			if(isset($this->_request['service_name']))
				$serviceName = $this->_request['service_name'];

			if(isset($this->_request['host_name']))
				$hostName = $this->_request['host_name'];

			if(!isset($serviceName))
				$result = $ejc->createMucRoom($roomName);
			else
				$result = $ejc->createMucRoom($roomName, $serviceName, $hostName);


			if($result == '') {
				$success = array('status' => "Success");
				$this->response($this->json($success), 200);
			}
			else {
				$error = array('status' => "Failed", "msg" => "Invalid parameters sent");
				$this->response($this->json($error), 400);
			}
		}

		//Destroy Room
		/**
		 *
		 * Example : http://localhost/groupchat/destroy_room?room_name=test
		 *
		 * Set room_name to name of the room you want to destroy
		 *
		 */

		private function destroy_room(){

			$ejc = new EjabberdController();


			if(isset($this->_request['room_name']))
				$roomName = $this->_request['room_name'];

			if(isset($this->_request['service_name']))
				$serviceName = $this->_request['service_name'];

			if(isset($this->_request['host_name']))
				$hostName = $this->_request['host_name'];

			if(!isset($serviceName))
				$result = $ejc->destroyMucRoom($roomName);
			else
				$result = $ejc->destroyMucRoom($roomName, $serviceName, $hostName);

			if($result == '') {
				$success = array('status' => "Success");
				$this->response($this->json($success), 200);
			}
			else {
				$error = array('status' => "Failed", "msg" => "Invalid parameters sent");
				$this->response($this->json($error), 400);
			}
		}

		//Chage Room Options
		/**
		 *
		 * Example : localhost/groupchat/change_option?room_name=test&option=persistent&value=true
		 *
		 *
		 * Set room_name to name of the room you want to destroy
		 * Option to the ejabberd options (persistent, mam)
		 * Value " give option's value.
		 *
		 */


		private function change_option(){

			$ejc = new EjabberdController();


			if(isset($this->_request['room_name']))
				$roomName = $this->_request['room_name'];

			if(isset($this->_request['option']))
				$option = $this->_request['option'];

			if(isset($this->_request['value']))
				$value = $this->_request['value'];

			if(isset($this->_request['service_name']))
				$serviceName = $this->_request['service_name'];

			if(isset($this->_request['host_name']))
				$hostName = $this->_request['host_name'];

			if(!isset($option)){
				$error = array('status' => "Failed", "msg" => "Options not sent");
				$this->response($this->json($error), 400);
			}

			if(!isset($value)){
				$error = array('status' => "Failed", "msg" => "Value not sent");
				$this->response($this->json($error), 400);
			}


			if(!isset($serviceName))
				$result = $ejc->changeMucRoomOption($roomName, $option, $value);
			else
				$result = $ejc->changeMucRoomOption($roomName, $option, $value, $serviceName, $hostName);


			if($result == '')
				$this->response($this->json($result), 200);
			else {
				$error = array('status' => "Failed", "msg" => "Invalid parameters sent");
				$this->response($this->json($error), 400);
			}
		}

		//ADD OR REMOVE USER
		/**
		 *
		 * Example : http://{host}/update_user?room_name=newroom&user_jid=12&affiliation=outcast
		 *
		 * Set affiliation to "outcast" to remove user
		 *Set affiliation to "member" to add user.
		 */

		private function update_user(){

			$ejc = new EjabberdController();


			if(isset($this->_request['room_name']))
				$roomName = $this->_request['room_name'];

			if(isset($this->_request['user_jid']))
				$user_jid = $this->_request['user_jid'];

			if(isset($this->_request['affiliation']))
				$affiliation = $this->_request['affiliation'];

			if(isset($this->_request['service_name']))
				$serviceName = $this->_request['service_name'];

			if(isset($this->_request['host_name']))
				$hostName = $this->_request['host_name'];

			if(!isset($user_jid)){
				$error = array('status' => "Failed", "msg" => "User Jid not sent");
				$this->response($this->json($error), 400);
			}

			if(!isset($affiliation)){
				$error = array('status' => "Failed", "msg" => "Affiliation not sent");
				$this->response($this->json($error), 400);
			}

			if (strpos($user_jid, ',') !== false) {

				$user_jid = explode(",", $user_jid);

			}

			if(!isset($serviceName))
				$result = $ejc->updateUsertoMucRoom($roomName, $user_jid, $affiliation);
			else
				$result = $ejc->updateUsertoMucRoom($roomName, $user_jid, $affiliation, $serviceName, $hostName);


			if($result == '') {
				$success = array('status' => "Success", 'data' => $result);
				$this->response($this->json($success), 200);
			}
			else {
				$error = array('status' => "Failed", "msg" => "Invalid parameters sent");
				$this->response($this->json($error), 400);
			}
		}

		private function get_room_users(){

			$ejc = new EjabberdController();


			if(isset($this->_request['room_name']))
				$roomName = $this->_request['room_name'];

			if(isset($this->_request['service_name']))
				$serviceName = $this->_request['service_name'];

			if(isset($this->_request['host_name']))
				$hostName = $this->_request['host_name'];

			if(!isset($serviceName))
				$result = $ejc->listOccupantsOfRoom($roomName);
			else
				$result = $ejc->listOccupantsOfRoom($roomName, $serviceName, $hostName);


			if($result || empty($result)) {
				$success = array('status' => "Success", 'data' => $result);
				$this->response($this->json($success), 200);
			}
			else {
				$error = array('status' => "Failed", "msg" => "Invalid parameters sent");
				$this->response($this->json($error), 400);
			}
		}

		private function get_room_options(){

			$ejc = new EjabberdController();


			if(isset($this->_request['room_name']))
				$roomName = $this->_request['room_name'];

			if(isset($this->_request['service_name']))
				$serviceName = $this->_request['service_name'];

			if(isset($this->_request['host_name']))
				$hostName = $this->_request['host_name'];

			if(!isset($serviceName))
				$result = $ejc->getMucRoomOptions($roomName);
			else
				$result = $ejc->getMucRoomOptions($roomName, $serviceName, $hostName);


			if($result || empty($result)) {
				$success = array('status' => "Success", 'data' => $result);
				$this->response($this->json($success), 200);
			}
			else {
				$error = array('status' => "Failed", "msg" => "Invalid parameters sent");
				$this->response($this->json($error), 400);
			}
		}

		private function save_multiple_options(){


			$ejc = new EjabberdController();


			if(isset($this->_request['room_name']))
				$roomName = $this->_request['room_name'];

			if(isset($this->_request['data']))
				$option = $this->_request['data'];

			if(isset($this->_request['service_name']))
				$serviceName = $this->_request['service_name'];

			if(isset($this->_request['host_name']))
				$hostName = $this->_request['host_name'];


			if(!isset($serviceName))
				$result = $ejc->saveMultipleMucRoomOption($roomName, $option);
			else
				$result = $ejc->saveMultipleMucRoomOption($roomName, $option, $serviceName, $hostName);


			if($result == '') {
				$success = array('status' => "Success", 'data' => $result);
				$this->response($this->json($success), 200);
			}
			else {
				$error = array('status' => "Failed", "msg" => "Invalid parameters sent");
				$this->response($this->json($error), 400);
			}

		}

		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}


		
	
	}
	
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>
