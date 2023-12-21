<?php
namespace Src\Core;

/**
 * Request Core
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Src\Core
 */
class Request
{
    /** @var array global post */
    private array $post;

    /** @var array global get */
    private array $get;

    /** @var array parametros do endpoint */
    private array $parameters = [];

    /** @var mixed Corpo da requisição post */
    private $body;

    /**
     * Request constructor
     */
    public function __construct($body = null)
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->body = $body;
    }

    public function __get($propName)
    {
        return empty($this->parameters[$propName]) ? [] : $this->parameters[$propName];
    }

    public function __set($propName, $propValue)
    {
        $this->parameters[$propName] = $propValue;
    }

    public function body(string $key = "")
    {
        return empty($key) ? $this->body : $this->body[$key];
    }

    public function parameters()
    {
        return $this->parameters;
    }

    public function get(string $key = "")
    {
        return empty($key) ? $this->get : $this->get[$key];
    }

    public function post(string $key = "")
    {
        return empty($key) ? $this->post : $this->post[$key];
    }
}
