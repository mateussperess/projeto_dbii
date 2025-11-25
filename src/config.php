<?php

use Error\APIException;
use Http\Response;

function autoload(string $className)
{
  $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

  $file = __DIR__ . "/" . $className . ".php";

  if (!file_exists($file)) {
    throw new Exception("Class not found: {$className}");
  }

  require_once $file;
}

spl_autoload_register("autoload");

function exceptionHandler(Throwable $exception)
{
  if ($exception instanceof APIException) {
    Response::send(["message" => $exception->getMessage()], $exception->getCode());
  } else {
    // error_log("EXCEPTION NÃO CAPTURADA: " . get_class($exception));
    // error_log("Message: " . $exception->getMessage());
    // error_log("File: " . $exception->getFile() . ":" . $exception->getLine());
    // error_log("Trace: " . $exception->getTraceAsString());
    Response::send(["message" => "Unable to process this request!"], 500);
  }
}

set_exception_handler("exceptionHandler");

function handleError($severity, $message, $file, $line)
{
  throw new ErrorException($message, 0, $severity, $file, $line);
}

set_error_handler("handleError");
