<?php

namespace App\Services;

use RdKafka\Producer;

class KafkaProducer
{
    protected Producer $producer;
    protected $topic;

    public function __construct(?string $topic = null)
    {
        $conf = new \RdKafka\Conf();
        $this->producer = new Producer($conf);

        $this->producer->addBrokers(config('kafka.brokers'));
        $this->topic = $this->producer->newTopic($topic ?? config('kafka.topics.booking'));
    }

    public function send(array $data): void
    {
        $payload = json_encode($data);
        $this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $payload);
        $this->producer->poll(0);
        //10 seconds for sending
        $result = $this->producer->flush(10000);
        if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
            \Log::error('[KafkaProducer::send] ', $result);

            throw new \RuntimeException('Kafka flush failed, messages might be lost!');
        }
    }
}
