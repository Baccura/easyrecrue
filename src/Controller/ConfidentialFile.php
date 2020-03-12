<?php

class ConfidentialFile
{
    private $requestMethod;
    private $file;

    private $personGateway;

    public function __construct($requestMethod, $file)
    {
        $this->requestMethod = $requestMethod;
        $this->file = $file;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->file) {
                    $response = $this->getFile($this->file);
                } else {
                    $response = $this->listFiles();
                };
                break;
            case 'POST':
                $response = $this->createFile();
            case 'DELETE':
                $response = $this->deleteFile($this->filename);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function listFiles()
    {
        $allFiles = array_diff(scandir($rootDir . "/"), [".", ".."]);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($allFiles);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
