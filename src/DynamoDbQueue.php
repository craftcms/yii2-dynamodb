<?php

namespace craftcms\dynamodb;

use Aws\DynamoDb\Exception\DynamoDbException;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\queue\cli\Queue;

class DynamoDbQueue extends Queue
{
    public DynamoDbConnection|string|array $dynamoDb = 'dynamoDb';
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
    public function status($id): int
    {
        try {
            $item = $this->dynamoDb->getItem($id);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to get job status: {$e->getMessage()}", __METHOD__);
            return self::STATUS_WAITING;
        }

        if ($item) {
            return self::STATUS_WAITING;
        }

        return self::STATUS_DONE;
    }

    public function execute($id, $message, $ttr, $attempt, $workerPid): bool
    {
        $success = parent::execute($id, $message, $ttr, $attempt, $workerPid);

        if ($success) {
            try {
                $this->dynamoDb->deleteItem($id);
            } catch (DynamoDbException $e) {
                Yii::error("Unable to delete completed job: {$e->getMessage()}", __METHOD__);
            }
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
            Yii::error("Unable to push message: {$e->getMessage()}", __METHOD__);

            return null;
        }

        return $id;
    }
}
