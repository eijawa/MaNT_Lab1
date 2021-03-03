<?php
include "./src/gateways/ServiceGateway.php";

class ServiceController {
    private $db;
    private $requestMethod;

    private $serviceId;

    private $serviceGateway;

    public function __construct($db, $requestMethod, $serviceId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;

        $this->serviceId = $serviceId;

        $this->serviceGateway = new ServiceGateway($db);
    }

    public function processRequest() {
        switch($this->requestMethod) {
            case 'GET':
                if ($this->serviceId) {
                    $response = $this->getServiceById();
                } else {
                    $response = $this->getAllServices();
                }
                break;
            case "POST":
                $response = $this->createService();
                break;
            case "PUT":
                if ($this->serviceId) {
                    $response = $this->updateService();
                }
                break;
            case "DELETE":
                if ($this->serviceId) {
                    $response = $this->deleteService();
                }
                break;
            default:
                $response = $this->badRequestResponse();
                break;
        }

        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllServices() {
        $result = $this->serviceGateway->readAll();
        $result = $this->buildTree($result);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getServiceById() {
        $result = $this->serviceGateway->readById($this->serviceId);

        if ($result == null) {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = null;

            return $response;
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

    private function createService() {
        parse_str(file_get_contents('php://input'), $input);

        $id = null;

        if ($this->validateInput($input)) {
            $id = $this->serviceGateway->create((array) $input);
        }

        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['location_header'] = "Location: /service/".$id;
        $response['body'] = null;

        return $response;
    }

    private function updateService() {
        parse_str(file_get_contents('php://input'), $input);
        
        $this->serviceGateway->update($this->serviceId, (array) $input);

        $response['status_code_header'] = 'HTTP/1.1 204 No Data';
        $response['body'] = null;

        return $response;
    }

    private function deleteService() {
        $this->serviceGateway->delete($this->serviceId);

        $response['status_code_header'] = 'HTTP/1.1 204 No Data';
        $response['body'] = null;

        return $response;
    }

    private function badRequestResponse() {
        $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
        $response['body'] = null;

        return $response;
    }

    // Modifiers
    private function buildTree($data) {
        $main_arr = [];
    
        foreach ($data as $d) {
            if ($d["parent_id"] == NULL) {
                $main_arr[] = $d;
            }
            else {
                $main_arr = $this->createBranch($main_arr, $d);
            }
        }
    
        return $main_arr;
    }
    
    private function createBranch($main_arr, $value) {
        for ($i = 0; $i < count($main_arr); ++$i) {
            if ($main_arr[$i]["id"] == $value["parent_id"]) {
                if ($main_arr[$i]["children"] == NULL) $main_arr[$i]["children"] = [];
                array_push($main_arr[$i]["children"], $value);
            }
            else if ($main_arr[$i]["children"] != NULL and count($main_arr[$i]["children"]) != 0) {
                $main_arr[$i]["children"] = $this->createBranch($main_arr[$i]["children"], $value);
            }
        }
    
        return $main_arr;
    }

    private function validateInput($input) {
        if (!is_numeric($input["parent_id"])) return false;

        return true;
    }
}
?>