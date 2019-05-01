<?PHP

if (!defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
}

class RequestController {

    public static $PROCESS_NOTHING = -1;
    public static $PROCESS_STRING = 0;
    public static $PROCESS_JSON = 1;
    public static $PROCESS_INT = 2;
    private $error = false;
    private $dataPost = false;
    private $dataGet = false;
    private $payload = false;

    public function __construct() {
        $this->dataGet = $_GET;
        $this->dataPost = $_POST;
    }

    public function requestPayload() {
        $this->payload = true;
        $request_body = file_get_contents('php://input');
        $this->dataGet = array();
        $this->dataPost = (array) json_decode($request_body);
    }

    public function verifyPOST($parameters) {
        $this->error = array();
        foreach ($parameters as $parameter) {
            if (!isset($this->dataPost[$parameter])) {
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
            if (!isset($this->dataGet[$parameter])) {
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
            $value = filter_var($this->dataPost[$name], FILTER_SANITIZE_STRING);
        } else if ($process == RequestController::$PROCESS_JSON) {
            $value = json_decode($this->dataPost[$name]);
        } else if ($process == RequestController::$PROCESS_INT) {
            $value = (int) filter_var($this->dataPost[$name], FILTER_SANITIZE_NUMBER_INT);
        } else {
            $value = $this->dataPost[$name];
        }
        return $value;
    }

    public function takeGET($name, $process = -1) {
        $value = false;
        if ($process == RequestController::$PROCESS_STRING) {
            $value = filter_var($this->dataGet[$name], $name, FILTER_SANITIZE_STRING);
        } else if ($process == RequestController::$PROCESS_JSON) {
            $value = json_decode($this->dataGet[$name]);
        } else if ($process == RequestController::$PROCESS_INT) {
            $value = (int) filter_var($this->dataGet[$name], FILTER_SANITIZE_NUMBER_INT);
        } else {
            $value = $this->dataGet[$name];
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
