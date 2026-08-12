<?php
/* 
    Objective: Abstract class for Routing 
    Author: Sing
    LUD:[09 May 2023 Sing] Init

*/

require_once ROOT.'/class/func/paramBuilder.class.php';
abstract class abstractRouting extends paramBuilder{

    
    private ?string $mode; // apis/php file routing
    private ?string $submode;
    private ?string $thirdmode; // additional 
    public db $DB;
    public $gthis;
    

    public function __construct($gthis) {
		$this->gthis = $gthis;
		$this->mode = $gthis->mode;
		$this->submode = $gthis->submode;
		$this->thirdmode = $gthis->thirdmode;
            
        $this->init();
	}

    function init(){
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (method_exists("abstractRouting", $method)){   
            $this->$method($this->submode);
        } else {
            new apiReturn(404);
        }
    }

    function get($submode){
        // Following method allow in get method
        switch($submode){
            default:
                new apiReturn(404);
                break;
        }
    }

    function post($submode){
        // Following method allow in post method
        switch($submode){
            default:
                new apiReturn(404);
                break;
        }
    }

    function delete($submode){
        // Following method allow in delete method
        switch($submode){
            default:
                new apiReturn(404);
                break;
        }
    }

    function patch($submode){
        // Following method allow in patch method
        switch($submode){
            default:
                new apiReturn(404);
                break;
        }
    }

    function put($submode){
        // Following method allow in patch method
        switch($submode){
            default:
                new apiReturn(404);
                break;
        }
    }
}