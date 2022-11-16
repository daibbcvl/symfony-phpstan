<?php

namespace App\Session;

class Memcached extends \Memcached
{
    /**
     * To make sure persistent data
     * @param array $servers
     * @return bool
     */
    public function addServers(array $servers): bool
    {
        if (count($this->getServerList()) == 0) {
            return parent::addServers($servers);
        }
        return false;
    }

    /**
     * @param string $host
     * @param int $port
     * @param int $weight
     * @return bool
     */
    public function addServer($host, $port, $weight = 0): bool
    {
        foreach ($this->getServerList() as $server) {
            if ($server['host'] == $host && $server['port'] == $port) {
                return false;
            }
        }

        return parent::addServer($host, $port, $weight);
    }


}