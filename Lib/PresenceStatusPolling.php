#!/usr/bin/php
<?php

require_once('../Config/Config.php');
require_once('MNMemcache.php');

error_reporting(0);
$poll = new PresenceStatusPolling();
$poll->ejabberdVersion = EJABBERD_VERSION;
$poll->pollingTime = PRESENCE_STATUS_POOLING_INTRVAL; //Sleep in seconds
$poll->start(); // We simply start process !

class PresenceStatusPolling {

    var $debug = true;           /* Debug mode */
    var $pollingTime; // Minutes polling time.

    private $cacheKey = 'app_mnchat_online_users';

    function start() {
        while ($this->getPresenceStatus()){
            sleep($this->pollingTime);
        }
    }

    function getPresenceStatus(){

        $output = shell_exec('/opt/ejabberd-'. $this->ejabberdVersion .'/bin/ejabberdctl --node ejabberd connected_users');
        $output = explode("\n", $output);
        array_pop($output);
        foreach($output as $key=>$value){
            $output[$key] = explode('@', $value)[0];
        }
        $output = array_unique($output);
        $this->saveOnlineUsers($output);
        return true;
    }

    function saveOnlineUsers($output){
        if(MNMemcache::write($this->cacheKey, $output, 0, 0)) {            
            if ($this->debug) {
                echo 'Saving data at ' . date('m/d/Y h:i:s a', time());
                print_r($output);
            }
        }else{
            echo 'Failed writing cache';
        }
    }

}
?>

