<?php

namespace src\Controller;

class ConfidentialFileController
{
    private $requestMethod;
    private $filename;

    public function __construct($requestMethod, $filename)
    {
        $this->requestMethod = $requestMethod;
        $this->filename = $filename;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->filename) {
                    $response = $this->filename !== null ? $this->getFile($this->filename) : $this->notFoundResponse();
                } else {
                    $response = $this->listFiles();
                };
                break;
            case 'POST':
                $response = $this->createFile();
                break;
            case 'DELETE':
                $response = $this->filename !== null ? $this->deleteFile($this->filename) : $this->notFoundResponse();
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
        $dir = env('UPLOAD_DIR');
        $allFiles = [];
        $page = $_GET['page'];
        $limit = $_GET['limit'];
        
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $path_parts = pathinfo("{$dir}{$file}");
                    
                    if (!in_array($file, ['.', '..']) && $path_parts['extension'] === 'txt') {
                        $allFiles[] = ['id' => $path_parts['filename']];
                    }
                }
                
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['body'] = json_encode(array_slice($allFiles, $page*$limit, $limit));
                
                closedir($dh);
            } else {
                $response['status_code_header'] = 'HTTP/1.1 204 No Content';
                $response['body'] = json_encode(['info_message' => 'Zero résultat']);
            }
        } else {
                $response['status_code_header'] = 'HTTP/1.1 204 No Content';
                $response['body'] = json_encode(['info_message' => 'Zero résultat']);
        }
        
        return $response;
    }
    
    private function getFile($filename)
    {
        $file_url = self::getFilePath($filename);
        
        if (file_exists($file_url)) {
            header('Content-Type: application/txt');
            header("Content-Transfer-Encoding: Binary"); 
            header ("Content-Length: ".filesize($file_url));
            header("Content-disposition: attachment; filename=\"" . $filename . "\""); 
            header("Cache-control: private");

            readfile($file_url);
        } else {
            return self::notFoundResponse();
        }
    }
    
    private function deleteFile($filename)
    {
        $file_url = self::getFilePath($filename);
        $deletedFile = self::getDeletedFilePath($filename);
        
        if (is_file($file_url)) {
            rename($file_url, $deletedFile);
            
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(['success_message' => 'Le fichier a bien été supprimé']);
        } else {
            $response = self::notFoundResponse();
        }
        
        return $response;
    }
    
    private function createFile()
    {
        $title = self::formatTitle($_POST['title']);
        $content = $_POST['content'];
        $file_url = self::getFilePath($title);
        
        if (!file_exists($file_url)) {
            $openFile = fopen($file_url, 'w') or die("Failed to create file");
            fwrite($openFile, $content) or die("Failed to create file");
            fclose($openFile);
            
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(['id' => $title]);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = json_encode(['info_message' => 'Vous ne pouvez pas faire cela']);
        }
        
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(['info_message' => 'Résultat introuvable']);
        
        return $response;
    }
    
    private function formatTitle($title)
    {
        foreach ([' ', '/', '&', '?'] as $value) {
            $title = str_replace($value, '_', $title);
        }
        
        return $title;
    }
    
    private function getFilePath($filename)
    {
        $file = $filename . '.' . env('EXT');
        
        return env('UPLOAD_DIR') . $file;
    }
    
    private function getDeletedFilePath($filename)
    {
        $file = $filename . '.' . env('EXT_DELETE');
        
        return env('UPLOAD_DIR') . $file;
    }
}