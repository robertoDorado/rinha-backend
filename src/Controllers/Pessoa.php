<?php

namespace Src\Controllers;

use DateTime;
use Logs;
use Ramsey\Uuid\Nonstandard\UuidV6;
use Src\Core\Model;
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
        $bodyData = ["id" => UuidV6::uuid6()->toString()] + $bodyData;

        $model = new Model();
        $model->insert($bodyData);
    }
}
