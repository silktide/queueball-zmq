<?php
/**
 * @package queueball-zmq
 */

namespace Silktide\QueueBall\ZeroMq;

class SocketFactory
{
    protected $context;
    protected $sendTimeout;

    public function __construct(\ZMQContext $context, $sendTimeout=null)
    {
        $this->context = $context;
        $this->sendTimeout = $sendTimeout;
    }

    public function createPushSocket()
    {
        $zmqSocket = new \ZMQSocket($this->context, \ZMQ::SOCKET_PUSH);
        if ($this->sendTimeout) {
            $zmqSocket->setSockOpt(\ZMQ::SOCKOPT_SNDTIMEO, $this->sendTimeout);
        }
        return $zmqSocket;
    }

    public function createPullSocket()
    {
        return new \ZMQSocket($this->context, \ZMQ::SOCKET_PULL);
    }

} 