<?php

namespace pixelandtonic\dynamodb\drivers;

use Aws\DynamoDb\Exception\DynamoDbException;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\queue\Queue;

class DynamoDbQueue extends Queue
{
    public DynamoDBConnection|string|array $dynamoDb = 'dynamoDb';
    public string $dataAttribute = 'data';

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->dynamoDb = Instance::ensure($this->dynamoDb, DynamoDbConnection::class);
    }

    /**
     * @inheritDoc
     */
    protected function pushMessage($message, $ttr, $delay, $priority): ?string
    {
        $id = uniqid('', true);

        try {
            $this->dynamoDb->updateItem($id, [
                'job' => $message,
                'ttr' => $ttr,
                'delay' => $delay,
                'priority' => $priority,
                'pushed_at' => time(),
            ]);
        } catch (DynamoDbException $e) {
            Yii::warning("Unable to push message: {$e->getMessage()}", __METHOD__);

            return null;
        }

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function status($id): int
    {
        if (!$id) {
            return self::STATUS_WAITING;
        }

        $item = $this->dynamoDb->getItem($id);

        if ($item) {
            return self::STATUS_WAITING;
        }

        // if (!$item['reserved_at']) {
        //     return self::STATUS_WAITING;
        // }
        //
        // if (!$item['done_at']) {
        //     return self::STATUS_RESERVED;
        // }

        return self::STATUS_DONE;
    }
}
