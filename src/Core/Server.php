<?php
namespace Src\Core;

/**
 * Server Core
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Src\Core
 */
class Server
{
    private array $server;

    /**
     * Server constructor
     */
    public function __construct()
    {
        $this->server = $_SERVER;    
    }

    public function getServerByKey(string $key)
    {
        if (empty($this->server)) {
            throw new \Exception("Global server não foi definida");
        }

        if ($key == "REQUEST_URI") {
            return parse_url($this->server[$key]);
        }

        return $this->server[$key];
    }
}
