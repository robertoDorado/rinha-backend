<?php

namespace Src\Core;

use Logs;

/**
 * Router Core
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Src\Core
 */
class Router
{
    /** @var Server Objeto server */
    private Server $server;

    /** @var Request Objeto request */
    private Request $request;

    /** @var array Namespace do controller */
    private array $namespaceData = [];

    /** @var array Uri */
    private array $uris = [];

    /** @var array Controller e método */
    private array $controllerAndMethod = [];

    /** @var string endpoint grupo */
    private string $endpointGroup = "";

    /** @var string path retornado pelo server */
    private string $path;

    /** @var string Método da requisição retornado pelo server */
    private string $method;

    use Logs;

    public function __construct()
    {
        $this->server = new Server();
        $this->request = new Request(json_decode(file_get_contents("php://input"), true));
        $this->path = $this->server->getServerByKey('REQUEST_URI')['path'];
        $this->method = strtolower($this->server->getServerByKey("REQUEST_METHOD"));
    }

    public function post(string $uri, string $controllerAndMethod)
    {
        if (!$this->server instanceof Server) {
            $this->errorException("A instância do servidor não existe");
        }

        if (!preg_match("/^[A-Za-z]+::[A-Za-z]+$/", $controllerAndMethod)) {
            $this->errorException("Caracteres inválidos");
        }

        if (empty($this->namespaceData)) {
            $this->errorException("Namespace não foi preenchido corretamente");
        }

        $uriFormated = preg_match("/^\/{1}$/", $this->endpointGroup) ? $uri :
            $this->endpointGroup . $uri;

        array_push($this->uris, ["post" => $uriFormated]);
        array_push($this->controllerAndMethod, $controllerAndMethod);
    }

    public function group(string $endpoint)
    {
        $this->endpointGroup = $endpoint;
    }

    public function execute()
    {
        if (!empty($this->uris)) {
            $requestUri = explode("/", $this->path);
            $parameter = array_pop($requestUri);
            $requestUri = implode("/", $requestUri);

            foreach ($this->uris as $key => $urisValue) {
                preg_match("/\{\w+\}$/", $urisValue['get'], $uriParameter);
                $uriParameter = str_replace(["{", "}"], "", $uriParameter);
                $uriValueWithoutParam = preg_replace("/\/\{\w+\}/", "", $urisValue['get']);

                if (!empty($uriParameter)) {
                    foreach ($uriParameter as $param) {
                        if ($requestUri == $uriValueWithoutParam) {
                            $this->request->$param = $parameter;
                        }
                    }
                }

                $uriComparation = empty($this->request->parameters()) ?
                    $uriValueWithoutParam : $uriValueWithoutParam . "/" . $parameter;

                $uriVerified = $this->method == "get" ? $uriComparation : $urisValue[$this->method];
                if ($uriVerified != $this->path) {
                    continue;
                }

                $invokeControllerAndMethod = explode("::", $this->controllerAndMethod[$key]);
                if (count($invokeControllerAndMethod) != 2) {
                    $this->errorException("Validação do controller inválida");
                }

                try {
                    if (!empty($this->namespaceData)) {
                        foreach ($this->namespaceData as $namespace) {
                            $controller = $namespace . "\\" . $invokeControllerAndMethod[0];
                            if (!class_exists($controller)) {
                                continue;
                            }
        
                            $method = $invokeControllerAndMethod[1];
                            if (!method_exists($controller, $method)) {
                                continue;
                            }

                            (new $controller())->$method($this->request);
                        }
                    }
                } catch (\Error $e) {
                    $this->errorMessage($e->getMessage());
                }
            }
        }
    }

    public function errorRouter()
    {
        $request[$this->method] = $this->path;
        foreach ($this->uris as $key => $value) {
            if ($request[$key] != $value) {
                http_response_code(404);
                die;
            }
        }
    }

    public function namespace(string $namespaceData)
    {
        array_push($this->namespaceData, $namespaceData);
    }

    public function get(string $uri, string $controllerAndMethod)
    {
        if (!$this->server instanceof Server) {
            $this->errorException("A instância do servidor não existe");
        }

        if (!preg_match("/^[A-Za-z]+::[A-Za-z]+$/", $controllerAndMethod)) {
            $this->errorException("Caracteres inválidos");
        }

        if (empty($this->namespaceData)) {
            $this->errorException("namespace não foi preenchido corretamente");
        }

        $uriFormated = preg_match("/^\/{1}$/", $this->endpointGroup) ? $uri :
            $this->endpointGroup . $uri;

        array_push($this->uris, ["get" => $uriFormated]);
        array_push($this->controllerAndMethod, $controllerAndMethod);
    }
}
