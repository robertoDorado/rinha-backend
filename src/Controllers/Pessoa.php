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
        $model = new Model();
        echo $model->countTotalData()["total_pessoas"];
    }

    public function searchPessoa(Request $request)
    {
        if (empty($request->get("t"))) {
            $this->badRequest("a query string não foi informada");
        }

        $model = new Model();
        echo json_encode($model->getPessoaByQueryString($request->get("t")));
    }

    public function getPessoaById(Request $request)
    {
        if (!empty($request->parameters())) {
            $model = new Model();
            echo json_encode($model->getPessoaByUuid($request->uuid));
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
