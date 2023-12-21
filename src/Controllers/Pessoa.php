<?php

namespace Src\Controllers;

use DateTime;
use Logs;
use Ramsey\Uuid\Nonstandard\UuidV6;
use Src\Core\Request;

header("Content-Type: application/json");

/**
 * Pessoa Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Src\Controllers
 */
class Pessoa
{
    use Logs;

    public function countTotalData()
    {
        echo "total de 49596 pessoas";
    }

    public function searchPessoa(Request $request)
    {
        if (!empty($request->get())) {
            echo json_encode($request->get());
        } else {
            echo "123";
        }
    }

    public function getPessoaById(Request $request)
    {
        if (!empty($request->parameters())) {
            echo json_encode($request->id);
            die;
        }
    }

    public function setPessoa(Request $request)
    {
        $bodyData = $request->body();

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
        }

        

        $bodyData["id"] = UuidV6::uuid6()->toString();
        echo json_encode($bodyData);
    }
}
