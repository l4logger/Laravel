<?php
namespace L4Logger\Laravel\Logging;
use Monolog\Logger as MonoLogger ;
class Logger{
/**
     * Create a custom Monolog instance.
     *
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config){
        $logger = new MonoLogger("Handler");
        return $logger->pushHandler(new Handler());
    }
}