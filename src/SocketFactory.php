<?php
/**
 * @package queueball-zmq
 */

namespace Silktide\QueueBall\ZeroMq;

class SocketFactory
{
    const TIMEOUT_INFINITE = -1;

    protected $context;
    protected $sendTimeout;

    public function __construct(\ZMQContext $context, $sendTimeout = self::TIMEOUT_INFINITE)
    {
        $this->context = $context;

        if (!is_int($sendTimeout)) {
            throw new \InvalidArgumentException("Variable sendTimeout is required to be an integer. '{$sendTimeout}'' received");
        }

        $this->sendTimeout = $sendTimeout;
    }

    public function createPushSocket()
    {
        $zmqSocket = new \ZMQSocket($this->context, \ZMQ::SOCKET_PUSH);
        $zmqSocket->setSockOpt(\ZMQ::SOCKOPT_SNDTIMEO, $this->sendTimeout);
        return $zmqSocket;
    }

    public function createPullSocket()
    {
        return new \ZMQSocket($this->context, \ZMQ::SOCKET_PULL);
    }

} 