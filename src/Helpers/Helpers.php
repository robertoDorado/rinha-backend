<?php

trait Helpers {
    public function getServer() {
        return new \Src\Core\Server();
    }
}

trait Logs {
    public function badRequest(string $message) {
        header("HTTP/1.1 400 Bad Request");
        throw new \Exception($message);
    }

    public function invalidRequest(string $message) {
        header("HTTP/1.1 422 Unprocessable Entity");
        throw new \Exception($message);
    }

    public function errorException(string $message) {
        header("HTTP/1.1 500 Internal Server Error");
        throw new \Exception($message);
    }
    
    public function errorMessage(string $message) {
        header("HTTP/1.1 500 Internal Server Error");
        throw new \Error($message);
    }
}