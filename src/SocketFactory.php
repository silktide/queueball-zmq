<?php
/**
 * @package queueball-zmq
 */

namespace Silktide\QueueBall\ZeroMq;

class SocketFactory
{

    protected $context;

    public function __construct(\ZMQContext $context)
    {
        $this->context = $context;
    }

    public function createPushSocket()
    {
        return new \ZMQSocket($this->context, \ZMQ::SOCKET_PUSH);
    }

    public function createPullSocket()
    {
        return new \ZMQSocket($this->context, \ZMQ::SOCKET_PULL);
    }

} 