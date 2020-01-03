<?php

namespace pixelandtonic\dynamodb\drivers;

use Aws\Credentials\CredentialProvider;
use Aws\DynamoDb\DynamoDbClient;
use Yii;

trait WithDynamoDbClient
{
    /**
     * DynamoDB table name to use for the data.
     *
     * @var string
     */
    public $table;

    /**
     * DynamoDB table attribute to use for the id.
     *
     * @var string
     */
    public $tableIdAttribute = 'id';

    /**
     * DynamoDB table attribute to use for data.
     *
     * @var string
     */
    public $tableDataAttribute = 'data';

    /**
     * AWS access key.
     *
     * @var string|null
     */
    public $key;

    /**
     * AWS secret.
     *
     * @var string|null
     */
    public $secret;

    /**
     * Region where dynamodb table is hosted.
     *
     * @var string
     */
    public $region = '';

    /**
     * Endpoint to DynamoDB (used for local development or when using DAX).
     *
     * @var string
     */
    public $endpoint;

    /**
     * API version.
     *
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
