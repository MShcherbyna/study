<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RdKafka\KafkaConsumer;
use RdKafka\Conf;
use App\Models\Bookings;
use App\Models\Topics;
use App\Services\TopicDataHandler;

class KafkaStatusConsumer extends Command
{
    const RETRY = 3;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kafka-status-consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for booking status updates from Kafka';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $conf = new Conf();
        $conf->set('group.id', 'bookingStatus');
        $conf->set('metadata.broker.list', config('kafka.brokers'));
        $conf->set('enable.auto.commit', 'false');

        $consumer = new KafkaConsumer($conf);
        $topic = config('kafka.topics.status');
        $consumer->subscribe([$topic]);
        $this->info("Listening to topic: $topic");
        while (true) {
            $message = $consumer->consume(1000);
            $data = json_decode($message->payload, true);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    if (!isset($data['id']) && !isset($data['status'])) {
                        \Log::error('Incorrect message data.');

                        $this->info("Incorrect message data", );
                        break;
                    }

                    $handler = (new TopicDataHandler())->setTopic($data['id']);
                    if ($handler) {
                        $handler->incrementRetryNumber();
                        if ($handler->getRetryNumber() > self::RETRY) {
                            $consumer->commit($message);
                            $handler->close(Topics::ERROR, 'The retry number exhausted');
                        }

                        $result = $handler->handle($data);
                        if ($result) {
                            $consumer->commit($message);
                            $this->info("Booking {$data['id']} updated to status '{$data['status']}'");
                        }
                    }
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    break;
                default:
                    $this->error($message->errstr());
                    break;
            }
        }
    }
}
