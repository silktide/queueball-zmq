<?php

namespace Silktide\QueueBall\ZeroMq;

use Silktide\QueueBall\ZeroMq\Exception\NotImplementedException;
use Silktide\QueueBall\Message\QueueMessage;
use Silktide\QueueBall\Message\QueueMessageFactoryInterface;
use Silktide\QueueBall\Queue\AbstractQueue;

/**
 * @package queueball
 */
class Queue extends AbstractQueue
{

    protected $socketFactory;

    protected $messageFactory;

    /**
     * @var \ZMQSocket
     */
    protected $push;

    /**
     * @var \ZMQSocket
     */
    protected $pull;

    public function __construct(SocketFactory $socketFactory, QueueMessageFactoryInterface $messageFactory, $queueId = null)
    {
        $this->socketFactory = $socketFactory;
        $this->messageFactory = $messageFactory;
        parent::__construct($queueId);
    }

    protected function setupPushSocket($queueId)
    {
        $connect = true;
        if (!empty($this->push)) {
            $endpoints = $this->push->getendpoints();
            if (!empty($endpoints["connect"][0]) && $endpoints["connect"][0] != $queueId) {
                $this->push->disconnect($endpoints["connect"][0]);
            } else {
                $connect = false;
            }
        } else {
            $this->push = $this->socketFactory->createPushSocket();
        }

        if ($connect) {
            $this->push->connect($queueId);
        }
    }

    protected function setupPullSocket($queueId)
    {
        $connect = true;
        if (!empty($this->pull)) {
            $endpoints = $this->pull->getendpoints();
            if (!empty($endpoints["bind"][0]) && $endpoints["bind"][0] != $queueId) {
                $this->pull->unbind($endpoints["bind"][0]);
            } else {
                $connect = false;
            }
        } else {
            $this->pull = $this->socketFactory->createPullSocket();
        }
        if ($connect) {
            $this->pull->bind($queueId);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createQueue($queueId, $messageLockTimeout = 0, $options = [])
    {
        throw new NotImplementedException("ZeroMQ doesn't have the concept of 'creating' a queue");
    }

    /**
     * {@inheritDoc}
     */
    public function deleteQueue($queueId = null)
    {
        throw new NotImplementedException("ZeroMQ doesn't have the concept of 'deleting' a queue");
    }

    /**
     * @param mixed $messageBody
     * @param string|null $queueId
     */
    public function sendMessage($messageBody, $queueId = null)
    {
        $queueId = empty($queueId)? $this->getQueueId(): $queueId;
        $this->setupPushSocket($queueId);

        $this->push->send($messageBody);
    }

    /**
     * @param string|null $queueId
     *
     * @return QueueMessage
     */
    public function receiveMessage($queueId = null)
    {
        $queueId = empty($queueId)? $this->getQueueId(): $queueId;
        $this->setupPullSocket($queueId);

        $message = $this->pull->recv();
        return $this->messageFactory->createMessage($message, $queueId);
    }

    /**
     * @param QueueMessage $message
     */
    public function completeMessage(QueueMessage $message)
    {
        // ZeroMq doesn't have the concept of "completing" messages, but we don't need to throw an exception here
        return;
    }

    /**
     * @param QueueMessage $message
     */
    public function returnMessage(QueueMessage $message)
    {
        // add the message onto the queue again
        $this->sendMessage($message->getMessage(), $message->getQueueId());
    }

} 