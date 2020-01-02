<?php

namespace pixelandtonic\dynamodb\drivers;

use Aws\Credentials\CredentialProvider;
use Aws\DynamoDb\DynamoDbClient;
use Yii;
use yii\helpers\ArrayHelper;

class Cache extends \yii\caching\Cache
{
    /**
     * DynamoDB table name to use for the cache.
     *
     * @var string
     */
    public $table;

    /**
     * DynamoDB table name to use for the cache.
     *
     * @var string
     */
    public $tableKeyAttribute;

    /**
     * DynamoDB table name to use for the cache.
     *
     * @var string
     */
    public $tableValueAttribute;

    /**
     * AWS access key.
     * @var string|null
     */
    public $key;

    /**
     * AWS secret.
     * @var string|null
     */
    public $secret;

    /**
     * Region where queue is hosted.
     * @var string
     */
    public $region = '';

    /**
     * Endpoint to DynamoDB (used for local development or when using DAX).
     * @var string
     */
    public $endpoint;

    /**
     * API version.
     * @var string
     */
    public $version = 'latest';

    /**
     * DynamoDB client use for making requests.
     *
     * @var DynamoDbClient
     */
    protected $client;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->client = $this->getClient();
    }

    /**
     * @inheritDoc
     */
    protected function getValue($key)
    {
        $key = $this->buildKey($key);

        try {
            $result = $this->client->getItem([
                'TableName' => $this->table,
                'Key' => [
                    $this->tableKeyAttribute => ['S' => $key]
                ]
            ]);
        } catch (\Exception $e) {
            Yii::warning("Unable to get cache value: {$e->getMessage()}", __METHOD__);

            return null;
        }

        if (is_null($result['Item'])) {
            return false;
        }

        return $result['Item'][$this->tableValueAttribute]['S'] ?? null;
    }

    /**
     * @inheritDoc
     */
    protected function setValue($key, $value, $duration)
    {
        $key = $this->buildKey($key);

        try {
            $this->client->putItem([
                'TableName' => $this->table,
                'Item' => [
                    $this->tableKeyAttribute => ['S' => $key],
                    $this->tableValueAttribute => ['S' => $value],
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
                    $this->tableKeyAttribute => ['S' => $key],
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
        throw new \RuntimeException('flushValues is not implemented yet');
//        try {
//            $result = $this->client->scan([
//                'TableName' => $this->table,
//            ]);
//
//            foreach ($result['Items'] as $item) {
////                $this->client->deleteItem([
////                    'TableName' => $this->table,
////                    'Key' => ['S' => $item[$this->tableKeyAttribute]],
////                ]);
//            }
//
//        } catch (\Exception $e) {
//            Yii::error($e->getMessage(), 'cache');
//
//            return false;
//        }
//
//        return true;
    }

    /**
     * Returns a DynamoDB client.
     *
     * @return DynamoDbClient
     */
    protected function getClient()
    {
        try {
            if ($this->client) {
                return $this->client;
            }

            if ($this->key !== null && $this->secret !== null) {
                $credentials = [
                    'key' => $this->key,
                    'secret' => $this->secret,
                ];
            } else {
                // use default provider if no key and secret passed
                // see - http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/credentials.html#credential-profiles
                $credentials = CredentialProvider::defaultProvider();
            }

            $config = [
                'credentials' => $credentials,
                'region' => $this->region,
                'version' => $this->version,
            ];

            if (!is_null($this->endpoint)) {
                $config['endpoint'] = $this->endpoint;
            }

            $this->client = new DynamoDbClient($config);
        } catch (\Exception $e) {
            Yii::error("Unable to create cache client: {$e->getMessage()}", __METHOD__);
        }

        return $this->client;
    }
}
