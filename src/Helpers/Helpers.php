<?php

trait Helpers
{
    public function processStmt(PDOStatement $stmt)
    {
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as &$value) {
            $value["stack"] = json_decode($value["stack"]);
        }
        return $data;
    }
    
    public function getServer()
    {
        return new \Src\Core\Server();
    }
}

trait Logs
{
    public function notFound(string $message)
    {
        header("HTTP/1.1 404 Not Found");
        throw new \Exception($message);
    }

    public function created($id)
    {
        header("HTTP/1.1 201 Created");
        header("Location: /pessoas/{$id}");
        echo json_encode(["success" => true]);
    }

    public function pdoException(string $message)
    {
        header("HTTP/1.1 500 Internal Server Error");
        throw new \PDOException($message);
    }

    public function badRequest(string $message)
    {
        header("HTTP/1.1 400 Bad Request");
        throw new \Exception($message);
    }

    public function invalidRequest(string $message)
    {
        header("HTTP/1.1 422 Unprocessable Entity");
        throw new \Exception($message);
    }

    public function errorException(string $message)
    {
        header("HTTP/1.1 500 Internal Server Error");
        throw new \Exception($message);
    }

    public function errorMessage(string $message)
    {
        header("HTTP/1.1 500 Internal Server Error");
        throw new \Error($message);
    }
}
