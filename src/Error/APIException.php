<?php

namespace Error;

use Exception;

class APIException extends Exception
{
  function __construct(string $message = "", int $code = 500) {
    parent::__construct($message, $code);
  }
}
