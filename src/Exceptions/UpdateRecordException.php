<?php

namespace Netzleuchten\DynDns\Exceptions;


class UpdateRecordException extends \Exception
{
    private $domain = null;

    /**
     * UpdateRecordException constructor.
     * @param string $message
     * @param string $domain
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message, $domain, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->domain = $domain;
    }

    /**
     * @return null|string
     */
    public function getDomain()
    {
        return $this->domain;
    }
}