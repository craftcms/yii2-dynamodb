<?php

namespace pixelandtonic\dynamodb\drivers;

use Aws\DynamoDb\SessionHandler;
use pixelandtonic\dynamodb\WithDynamoDbClient;
use Yii;
use yii\web\Session;

class DynamoDbSession extends Session
{
    use WithDynamoDbClient;

    /**
     * @inheritDoc
     */
    public function init()
    {
        if ($this->keyPrefix === null) {
            $this->keyPrefix = substr(md5(Yii::$app->id), 0, 5);
        }

        $this->client = $this->getClient();
        $this->handler = SessionHandler::fromClient($this->client);
        parent::init();
    }
}
