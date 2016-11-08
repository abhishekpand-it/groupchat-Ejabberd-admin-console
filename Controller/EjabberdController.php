<?php
//$obj = new EjabberdController();
//$obj->createMucRoom("yoyoo");

class EjabberdController {

    private $ejabberdVersion = '16.04';
    public $serviceName = 'conference';
    public $hostName = 'localhost';

    public function __construct(){
    }

    public function getMucOnlineRooms(){


        $output = shell_exec('sudo -u ejabberd /opt/ejabberd-'. $this->ejabberdVersion .'/bin/ejabberdctl muc_online_rooms global');

        $output = explode("\n", $output);
        if($output)
            array_pop($output);
        return $output;
    }

    public function createMucRoom($room_name, $service_name = null, $host_name = null){

        if(!isset($service_name))
            $service_name = $this->serviceName;
        if(!isset($host_name))
            $host_name = $this->hostName;
       // die();
        $output = shell_exec('sudo -u ejabberd /opt/ejabberd-'. $this->ejabberdVersion .'/bin/ejabberdctl create_room '. $room_name .' '. $service_name . '.' . $host_name . ' ' . $host_name);

        return trim($output);
    }

    public function destroyMucRoom($room_name, $service_name=null, $host_name=null){

        if(!isset($service_name))
            $service_name = $this->serviceName;
        if(!isset($host_name))
            $host_name = $this->hostName;


        $output = shell_exec('sudo -u ejabberd /opt/ejabberd-'. $this->ejabberdVersion .'/bin/ejabberdctl destroy_room '. $room_name .' '. $service_name . '.' . $host_name);

        return trim($output);
    }

    public function changeMucRoomOption($room_name, $option, $value, $service_name=null, $host_name=null){

        if(!isset($service_name))
            $service_name = $this->serviceName;
        if(!isset($host_name))
            $host_name = $this->hostName;


        $output = shell_exec('sudo -u ejabberd /opt/ejabberd-'. $this->ejabberdVersion .'/bin/ejabberdctl change_room_option '. $room_name .' '. $service_name . '.' . $host_name . ' ' . $option . ' ' . $value);

        return trim($output);
    }

    public function saveMultipleMucRoomOption($room_name, $option, $service_name=null, $host_name=null){

        if(!isset($service_name))
            $service_name = $this->serviceName;
        if(!isset($host_name))
            $host_name = $this->hostName;

        $option = json_decode($option);

        foreach ($option as $key => $value){

            $output = shell_exec('sudo -u ejabberd /opt/ejabberd-'. $this->ejabberdVersion .'/bin/ejabberdctl change_room_option '. $room_name .' '. $service_name . '.' . $host_name . ' ' . $key . ' ' . $value);

        }

        return trim($output);
    }

    public function updateUsertoMucRoom($room_name, $user_id, $affiliation,  $service_name=null, $host_name=null){

        //ejabberdctl set_room_affiliation room_name muc_service user_jid affiliation

        if(!isset($service_name))
            $service_name = $this->serviceName;
        if(!isset($host_name))
            $host_name = $this->hostName;

        if(is_array($user_id)){

            foreach ($user_id as $value){
                $value = $value . '@' . $host_name;
                $output = shell_exec('sudo -u ejabberd /opt/ejabberd-'. $this->ejabberdVersion .'/bin/ejabberdctl set_room_affiliation '. $room_name .' '. $service_name . '.' . $host_name . ' ' . $value . ' ' . $affiliation);
            }

        }else {
            $user_id = $user_id . '@' . $host_name;
            $output = shell_exec('sudo -u ejabberd /opt/ejabberd-' . $this->ejabberdVersion . '/bin/ejabberdctl set_room_affiliation ' . $room_name . ' ' . $service_name . '.' . $host_name . ' ' . $user_id . ' ' . $affiliation);
        }
        return trim($output);
    }

    public function listOccupantsOfRoom($room_name, $service_name=null, $host_name=null){

        //eejabberdctl get_room_occupants room_name muc_service

        if(!isset($service_name))
            $service_name = $this->serviceName;
        if(!isset($host_name))
            $host_name = $this->hostName;



        $output = shell_exec('sudo -u ejabberd /opt/ejabberd-'. $this->ejabberdVersion .'/bin/ejabberdctl get_room_occupants '. $room_name .' '. $service_name . '.' . $host_name);

        $output = explode("\n", $output);
        if($output)
            array_pop($output);

        return $output;
    }

    public function getMucRoomOptions($room_name, $service_name=null, $host_name=null){

        //eejabberdctl get_room_options room_name muc_service

        if(!isset($service_name))
            $service_name = $this->serviceName;
        if(!isset($host_name))
            $host_name = $this->hostName;



        $output = shell_exec('sudo -u ejabberd /opt/ejabberd-'. $this->ejabberdVersion .'/bin/ejabberdctl get_room_options '. $room_name .' '. $service_name . '.' . $host_name);

       // print_r($output);

        $parse = explode("\n", $output);
//        print_r($parse);
//        die();

        $result = array();

        foreach ($parse as $key=>$value){

            $parts = preg_split('/\s+/', $parse[$key]);

            if(isset($parts[1])){
                $result[$parts[0]] = $parts[1];
            }else {
                $result[$parts[0]] = "";
            }
        }


        if($result)
            array_pop($result);


        return $result;
    }

}
?>
