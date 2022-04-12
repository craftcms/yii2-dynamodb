<?php

namespace pixelandtonic\dynamodb;

use Aws\DynamoDb\Exception\DynamoDbException;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\queue\sqs\DynamoDbQueueCommand;

class DynamoDbQueue extends \yii\queue\cli\Queue
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
        $this->dynamoDb->tableName = $this->dynamoDb->tableName ?? 'queue';
    }

    /**
     * @inheritDoc
     */
    public function status($id): int
    {
        $item = $this->dynamoDb->getItem($id);

        if ($item) {
            return self::STATUS_WAITING;
        }

        return self::STATUS_DONE;
    }

    public function execute($id, $message, $ttr, $attempt, $workerPid): bool
    {
        $success = parent::execute($id, $message, $ttr, $attempt, $workerPid);

        if ($success) {
            $this->dynamoDb->deleteItem($id);
        }

        return $success;
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
}
