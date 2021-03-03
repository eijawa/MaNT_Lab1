<?php
include "./src/gateways/QueueGateway.php";

class QueueController {
    private $requestMethod;

    private $entityId;

    private $gateway;

    public function __construct($db, $requestMethod, $entityId) {
        date_default_timezone_set("UTC");

        $this->requestMethod = $requestMethod;
        
        $this->entityId = $entityId;

        $this->gateway = new QueueGateway($db);
    }

    public function processRequest() {
        switch($this->requestMethod) {
            case "GET":
                if ($this->entityId) {
                    $response = $this->getById();
                } else {
                    $response = $this->getAll();
                }
                break;
            case "POST":
                $response = $this->create();
                break;
            // case "PATCH":
            //     if ($this->entityId) {
            //         $response = $this->update();
            //     }
            //     break;
            case "DELETE":
                if ($this->entityId) {
                    $response = $this->delete();
                }
                break;
            default:
                $response = $this->badRequestResponse();
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getById() {
        $result = $this->gateway->readById($this->entityId);

        if ($result == null) {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = null;

            return $response;
        }

        #$result = $this->getConvert($result);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

    private function getAll() {
        $result = $this->gateway->readAll();

        if ($result == null) {
            $result = array();
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

    private function create() {
        parse_str(file_get_contents('php://input'), $input);

        // if ($this->validateInput($input)) {
        //     $input["services_ids"] = strval($input["services_ids"]);
        //     $this->gateway->create((array) $input);
        // }

        $this->gateway->create((array) $input);

        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['location_header'] = "Location: /window/".$input["id"];
        $response['body'] = null;

        return $response;
    }

    // private function update() {
    //     parse_str(file_get_contents('php://input'), $input);
        
    //     $this->gateway->update($this->entityId, (array) $input);

    //     $response['status_code_header'] = 'HTTP/1.1 204 No Data';
    //     $response['body'] = null;

    //     return $response;
    // }

    private function delete() {
        $this->gateway->delete($this->entityId);

        $response['status_code_header'] = 'HTTP/1.1 204 No Data';
        $response['body'] = null;

        return $response;
    }

    private function badRequestResponse() {
        $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
        $response['body'] = null;

        return $response;
    }
}
?>