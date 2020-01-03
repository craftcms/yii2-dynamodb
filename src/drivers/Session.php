<?php

namespace pixelandtonic\dynamodb\drivers;

use Yii;

class Session extends \yii\web\Session
{
    use WithDynamoDbClient;

    /**
     * @var string a string prefixed to every cache key so that it is unique. If not set,
     * it will use a prefix generated from [[Application::id]]. You may set this property to be an empty string
     * if you don't want to use key prefix. It is recommended that you explicitly set this property to some
     * static value if the cached data needs to be shared among multiple applications.
     */
    public $keyPrefix;

    /**
     * @inheritDoc
     */
    public function init()
    {
        if ($this->keyPrefix === null) {
            $this->keyPrefix = substr(md5(Yii::$app->id), 0, 5);
        }

        $this->client = $this->getClient();

        parent::init();
    }

    /**
     * @inheritDoc
     */
    public function readSession($id)
    {
        try {
            $key = $this->calculateKey($id);

            $result = $this->client->getItem([
                'TableName' => $this->table,
                'Key' => [
                    $this->tableIdAttribute => ['S' => $key]
                ]
            ]);
        } catch (\Exception $e) {
            Yii::warning("Unable to get session data: {$e->getMessage()}", __METHOD__);

            return null;
        }

        if (is_null($result['Item'])) {
            return false;
        }

        return $result['Item'][$this->tableDataAttribute]['S'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function writeSession($id, $data)
    {
        try {
            $key = $this->calculateKey($id);

            $this->client->putItem([
                'TableName' => $this->table,
                'Item' => [
                    $this->tableIdAttribute => ['S' => $key],
                    $this->tableDataAttribute => ['S' => $data],
                ]
            ]);
        } catch (\Exception $e) {
            Yii::warning("Unable to write session: {$e->getMessage()}", __METHOD__);

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function destroySession($id)
    {
        try {
            $key = $this->calculateKey($id);

            $this->client->deleteItem([
                'TableName' => $this->table,
                'Key' => [
                    $this->tableIdAttribute => ['S' => $key],
                ]
            ]);
        } catch (\Exception $e) {
            Yii::warning("Unable to destroy session: {$e->getMessage()}", __METHOD__);

            return false;
        }

        return true;
    }

    /**
     * Generates a unique key used for storing session data in cache.
     * @param string $id session variable name
     * @return string a safe cache key associated with the session variable name
     */
    protected function calculateKey($id)
    {
        return $this->keyPrefix . md5(json_encode([__CLASS__, $id]));
    }
}
