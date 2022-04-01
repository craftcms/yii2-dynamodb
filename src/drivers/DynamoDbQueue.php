<?php

namespace pixelandtonic\dynamodb\drivers;

use pixelandtonic\dynamodb\WithDynamoDbClient;
use Yii;
use yii\base\InvalidArgumentException;
use yii\queue\Queue;

class DynamoDbQueue extends Queue
{
    use WithDynamoDbClient;

    /**
     * @inheritDoc
     */
    protected function pushMessage(string $message, int $ttr, int $delay, $priority): ?string
    {
        try {
            $id = uniqid($this->keyPrefix, true);

            $item = $this->buildItem($id, $message, $ttr, $delay, $priority);

            $this->client->putItem([
                'TableName' => $this->table,
                'Item' => $item,
            ]);
        } catch (\Exception $e) {
            Yii::warning("Unable to push message: {$e->getMessage()}", __METHOD__);

            return null;
        }

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function status(string $id): int
    {
        try {
            $result = $this->client->getItem([
                'TableName' => $this->table,
                'Key' => [
                    $this->tableIdAttribute => [
                        'S' => $id,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Yii::warning("Unable to retrieve status of the job: {$e->getMessage()}", __METHOD__);

            return null;
        }

        $item = $result->get('Item');

        // no job means its done
        if (is_null($item)) {
            Yii::warning("Unknown message ID: $id.");

            throw new InvalidArgumentException("Unknown message ID: $id.");
        }

        // is the job reserved?
        if ($item['reserved_at']['N'] !== 0) {
            return self::STATUS_WAITING;
        }

        // is the job marked as done?
        if ($item['done_at']['N'] !== 0) {
            return self::STATUS_RESERVED;
        }

        return self::STATUS_DONE;
    }

    /**
     * Builds an item to place into DynamoDB.
     *
     * @param $id
     * @param $message
     * @param $ttr
     * @param $delay
     * @param $priority
     * @return array
     */
    protected function buildItem($id, $message, $ttr, $delay, $priority): array
    {
        $now = time();

        return [
            $this->tableIdAttribute => [
                'S' => $id,
            ],
            'channel' => [
                'S' => $this->keyPrefix,
            ],
            'job' => [
                'S' => $message,
            ],
            'pushed_at' => [
                'N' => $now,
            ],
            'ttr' => [
                'N' => $ttr ?? 0,
            ],
            'delay' => [
                'N' => $delay,
            ],
            'priority' => [
                'S' => $priority ?? 'default',
            ],
            'reserved_at' => [
                'N' => 0,
            ],
            'attempt' => [
                'N' => 0,
            ],
            'done_at' => [
                'N' => 0,
            ],
        ];
    }
}
