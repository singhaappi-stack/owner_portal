<?php
/* 
    Objective: Api return function class
    Author: Sing
    LUD: [07 May 2023] Init

*/

class apiReturn
{
    public $msgMapping = [
        200 => "Success",
        400 => "Bad Request",
        401 => "Unauthorized",
        403 => "Forbidden",
        404 => "Not Found",
        500 => "Unknown Error",

        4001 => "Compulsory field is missing.",

        4011 => "Account not found.", 
        4012 => "Password incorrect.", 
        4013 => "Verification code incorrect.", 
        4014 => "Verification code expired.", 
        4015 => "Username is used already.", 
        4016 => "Password must be at least 10 characters long, include at least one uppercase letter, one lowercase letter, one numerical digit and one special character.", 
        4017 => "Old password is incorrect.",

        4031 => "Staff created already",
        4032 => "IT ID already in use",
        4033 => "Property ID already in use",
        4034 => "No seat left",
        
        4051 => "Record is approved already." 
    ];

    private Array $statusCode = [
    ];

    private int $code;
    private ?Array $apiReturn;
    private ?string $msg = null;

    public function response($statusCode = null, $msgCode = null, $msg = null, $data = null)
    {
        if(!$msgCode) {
            $msgCode = $statusCode;
        }

        $payload = $data;
        $payload['_server_time'] = date('Y-m-d H:i:s');

        $payload['_msg'] = $msg;
        $payload['_code'] = $msgCode;
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        // ob_clean();
        print_r(json_encode($payload));
        die();
    }


    public function __construct($statusCode = 200, $msgCode = null, $data = null)
    {

        try {
            $jsonObj = $data;
            if ($jsonObj === null && json_last_error() !== JSON_ERROR_NONE) {
                $jsonObj = null;
            } else {
            }
        } catch (Exception $ex){
            $jsonObj = null;
        }
        if (!$msgCode)
            $msgCode = $statusCode;
        $msg = $this->msgMapping[$msgCode];
        if (!$msg){
            $msg = $msgCode;
            $msgCode = $statusCode;
        }

        $this->response($statusCode, $msgCode, $msg, $jsonObj);
    }
}
?>