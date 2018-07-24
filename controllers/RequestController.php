<?PHP

if (!defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
}

function encode_all_strings($arr) {
    if(!is_array($arr)){
       return utf8_decode($arr);
    } else {
        foreach($arr as $key => $value) {
            if(!is_array($value)){
                $arr[$key] = utf8_encode($value);
            } else {
                $arr[$key] = encode_all_strings($value);
            }
        }
    }
    return $arr;
}

class RequestController {

    public static $PROCESS_NOTHING = -1;
    public static $PROCESS_STRING = 0;
    public static $PROCESS_JSON = 1;
    public static $PROCESS_INT = 2;
    private $errors = false;

    public function verifyPOST($parameters) {
        $this->error = array();
        foreach ($parameters as $parameter) {
            if (!isset($_POST[$parameter])) {
                $this->error[] = "Missing Argument POST::$parameter";
            }
        }
        if (count($this->error) != 0) {
            return false;
        } else {
            $this->error = false;
        }
        return true;
    }

    public function verifyGET($parameters) {
        $this->error = array();
        foreach ($parameters as $parameter) {
            if (!isset($_GET[$parameter])) {
                $this->error[] = "Missing Argument GET::$parameter";
            }
        }
        if (count($this->error) != 0) {
            return false;
        } else {
            $this->error = false;
        }
        return true;
    }

    public function takePOST($name, $process = -1) {
        $value = false;
        if ($process == RequestController::$PROCESS_STRING) {
            $value = filter_input(INPUT_POST, $name, FILTER_SANITIZE_STRING);
        } else if ($process == RequestController::$PROCESS_JSON) {
            $value = json_decode($_POST[$name]);
        } else if ($process == RequestController::$PROCESS_INT) {
            $value = (int)filter_input(INPUT_POST, $name, FILTER_SANITIZE_NUMBER_INT);
        } else {
            $value = $_POST[$name];
        }
        return $value;
    }

    public function takeGET($name, $process = -1) {
        $value = false;
        if ($process == RequestController::$PROCESS_STRING) {
            $value = filter_input(INPUT_GET, $name, FILTER_SANITIZE_STRING);
        } else if ($process == RequestController::$PROCESS_JSON) {
            $value = json_decode($_POST[$name]);
        } else {
            $value = $_POST[$name];
        }
        return $value;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function responseSuccess($message, $data = "") {
        
        if (is_array($message)) {
            $message = json_encode($message);
        }
        if(is_array($data)){
            $data = array_map("encode_all_strings",$data);
        }
        $packet = array("success" => true, "message" => $message, "data" => $data);
        echo json_encode($packet);
    }

    public function responseError($reason, $data = "") {
        if (is_array($reason)) {
            $reason = json_encode($reason);
        }
        $packet = array("success" => false, "message" => $reason, "data" => $data);
        echo json_encode($packet);
    }

}
