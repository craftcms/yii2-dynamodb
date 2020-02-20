<?php

namespace pixelandtonic\dynamodb\drivers;

use pixelandtonic\dynamodb\WithDynamoDbClient;
use Yii;
use yii\caching\Cache;

class DynamoDbCache extends Cache
{
    use WithDynamoDbClient;

    /**
     * @inheritDoc
     */
    protected function getValue($key)
    {
        try {
            $key = $this->buildKey($key);

            $result = $this->client->getItem([
                'TableName' => $this->table,
                'Key' => [
                    $this->tableIdAttribute => ['S' => $key]
                ]
            ]);
        } catch (\Exception $e) {
            Yii::warning("Unable to get cache value: {$e->getMessage()}", __METHOD__);

            return null;
        }

        if ($result['Item'] === null) {
            return false;
        }

        return $result['Item'][$this->tableDataAttribute]['S'] ?? null;
    }

    /**
     * @inheritDoc
     */
    protected function setValue($key, $value, $duration)
    {
        try {
            $key = $this->buildKey($key);

            $this->client->putItem([
                'TableName' => $this->table,
                'Item' => [
                    $this->tableIdAttribute => ['S' => $key],
                    $this->tableDataAttribute => ['S' => $value],
                ]
            ]);
        } catch (\Exception $e) {
            Yii::warning("Unable to set cache value: {$e->getMessage()}", __METHOD__);

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function addValue($key, $value, $duration)
    {
        return $this->set($key, $value, $duration);
    }

    /**
     * @inheritDoc
     */
    protected function deleteValue($key)
    {
        try {
            $key = $this->buildKey($key);

            $this->client->deleteItem([
                'TableName' => $this->table,
                'Key' => [
                    $this->tableIdAttribute => ['S' => $key],
                ],
            ]);
        } catch (\Exception $e) {
            Yii::warning("Unable to delete cache value: {$e->getMessage()}", __METHOD__);

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function flushValues()
    {
        try {
            $results = $this->client->scan([
                'TableName' => $this->table,
            ]);

            if ($results['Items'] === null) {
                Yii::error("No items to flush", __METHOD__);
            }

            foreach ($results['Items'] as $item) {
                $this->delete($this->buildKey(
                    $item[$this->tableIdAttribute]['S'])
                );
            }
        } catch (\Exception $e) {
            Yii::error("Unable to create flush cache: {$e->getMessage()}", __METHOD__);

            return false;
        }

        return true;
    }
}
