<?php
class View {
    private $data=array();
    function assign($varname,$vardata){
        $this->data[$varname]=$vardata;
    }
    function display($filename){
        //extract the data that the controller got from the model
        extract($this->data);
        //include the file that presents the extracted data
        include($filename);
    }

}


?>