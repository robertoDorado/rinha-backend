<?php

namespace Src\Core;

use Helpers;
use Logs;
use PDO;
use PDOException;

/**
 * Model Core
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Src\Core
 */
class Model
{
    /** @var PDO objeto pdo */
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connect::getInstance();
    }

    use Logs;
    use Helpers;

    public function getPessoaByQueryString(string $term): array
    {
        $columns = "uuid AS id, apelido, nome, nascimento, stack";
        $sql = "SELECT {$columns} FROM pessoas WHERE nome LIKE ? LIMIT 50";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, "%{$term}%");
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $this->processStmt($stmt);
        }
        
        $sql = "SELECT {$columns} FROM pessoas WHERE apelido LIKE ? LIMIT 50";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, "%{$term}%");
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $this->processStmt($stmt);
        }

        $sql = "SELECT {$columns} FROM pessoas WHERE stack LIKE ? LIMIT 50";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, "%{$term}%");
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $this->processStmt($stmt);
        }

        return [];
    }

    public function getPessoaByUuid(string $uuid): array
    {
        $sql = "SELECT uuid AS id, apelido, nome, nascimento, stack FROM pessoas WHERE uuid = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $uuid);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $this->notFound("objeto não encontrado");
        }

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $data["stack"] = json_decode($data["stack"]);

        return $data;
    }

    public function countTotalData()
    {
        $sql = "SELECT COUNT(id) AS total_pessoas FROM pessoas";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert(array $bodyData)
    {
        if (empty($bodyData)) {
            $this->invalidRequest("o corpo da requisição não pode estar vazio");
        }

        $requestKeys = ["apelido", "nome", "nascimento", "stack"];
        $bodyKeys = array_keys($bodyData);

        foreach ($requestKeys as $reqKey) {
            if (!in_array($reqKey, $bodyKeys)) {
                $this->invalidRequest("o corpo da requisição está inválida, a chave {$reqKey} não está presente");
            }
        }

        if (empty($bodyData["nome"])) {
            $this->invalidRequest("o campo nome não pode estar vazio");
        }

        if (empty($bodyData["apelido"])) {
            $this->invalidRequest("o campo apelido não pode estar vazio");
        }

        if (!is_string($bodyData["nome"])) {
            $this->badRequest("a chave nome deve ser somente do tipo string");
        }

        if (!is_string($bodyData["apelido"])) {
            $this->badRequest("a chave apelido deve ser somente do tipo string");
        }

        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $bodyData["nascimento"])) {
            $this->invalidRequest("campo nacimento inválido");
        }

        if (!empty($bodyData["stack"])) {
            foreach ($bodyData["stack"] as $value) {
                if (!is_string($value)) {
                    $this->invalidRequest("todos os valores na chave stack precisam ser do tipo string");
                }
            }

            $bodyData["stack"] = json_encode($bodyData["stack"]);
        }

        $query = "SELECT * FROM pessoas WHERE apelido = ? AND nome= ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(1, $bodyData["apelido"]);
        $stmt->bindValue(2, $bodyData["nome"]);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $this->invalidRequest("este usuário já foi criado");
        }

        try {
            $bodyData["stack"] = empty($bodyData["stack"]) ? null : $bodyData["stack"];
            $query = "INSERT INTO pessoas (uuid, apelido, nome, nascimento, stack) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(1, $bodyData["id"]);
            $stmt->bindValue(2, $bodyData["apelido"]);
            $stmt->bindValue(3, $bodyData["nome"]);
            $stmt->bindValue(4, $bodyData["nascimento"]);
            $stmt->bindValue(5, $bodyData["stack"]);
            $stmt->execute();
            
            $id = $this->pdo->lastInsertId();
            $query = "SELECT uuid FROM pessoas WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(1, $id);
            $stmt->execute();

            $data = $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            $this->created($data["uuid"]);
        } catch (PDOException $e) {
            $this->pdoException($e->getMessage());
        }
    }
}
