<?php

namespace Netzleuchten\DynDns;

use INWX\Domrobot;
use Netzleuchten\DynDns\Exceptions\UpdateRecordException;

class Api
{
    /**
     * @var Domrobot
     */
    private $domrobot;

    /**
     * Indicates if login at INWX was successfull
     * @var bool
     */
    private $login;

    /**
     * @param Domrobot $domrobot
     */
    public function injectDomrobot(Domrobot $domrobot)
    {
        $this->domrobot = $domrobot;
        $this->domrobot->setLanguage('en');
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function login($username, $password)
    {
        $result = $this->domrobot->login($username, $password);

        if ($result['code'] == 1000) {
            $this->login = true;
        } else {
            $this->login = false;

        }

        return $this->login;
    }

    public function validateKey($key)
    {
        return $key === $_ENV['SECRET_KEY'];
    }

    public function validateIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * Updates the content of the domain record
     * @param string $domain
     * @param string $ip
     * @throws UpdateRecordException
     */
    public function updateNameserverRecord($domain, $ip)
    {
        if ($this->login === true) {

            $masterDomain = $this->returnMasterDomain($domain);

            $arguments = ['domain' => $masterDomain, 'name' => $domain, 'type' => 'A'];
            $result = $this->domrobot->call('nameserver', 'info', $arguments);

            if ($result['code'] !== 1000) {
                throw new UpdateRecordException('Master domain not found.', $masterDomain, 1459592839);
            }
            
            if (isset($result['resData']['record'][0]['id'])) {

                $arguments = ['id' => $result['resData']['record'][0]['id'],  'content' => $ip];
                $result = $this->domrobot->call('nameserver', 'updateRecord', $arguments);

                if ($result['code'] !== 1000) {
                    throw new UpdateRecordException('Record could not be updated.', $domain, 1459592833);
                }

            } else {
                throw new UpdateRecordException('Record not found.', $domain, 1459592839);
            }
        }
    }

    public function finish()
    {
        $this->domrobot->logout();
    }

    private function returnMasterDomain($domain)
    {
        $parts = array_reverse(explode('.', $domain));
        return $parts[1] . '.' . $parts[0];
    }
}