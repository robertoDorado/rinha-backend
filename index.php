<?php

use Src\Core\Router;

require __DIR__ . "/vendor/autoload.php";

$router = new Router();
$router->namespace("Src\Controllers");
$router->group("/");
$router->post("/pessoas", "Pessoa::setPessoa");
$router->get("/pessoas", "Pessoa::searchPessoa");
$router->get("/contagem-pessoas", "Pessoa::countTotalData");
$router->get("/pessoas/{id}", "Pessoa::getPessoaById");

$router->execute();
$router->errorRouter();
