<?php

namespace pixelandtonic\dynamodb\drivers;

use pixelandtonic\dynamodb\WithDynamoDbClient;
use Yii;
use yii\web\Session;

class DynamoDbSession extends Session
{
    use WithDynamoDbClient;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        if ($this->keyPrefix === null) {
            $this->keyPrefix = substr(md5(Yii::$app->id), 0, 5);
        }

        $this->client = $this->getClient();
        $this->handler = DynamoDbSessionHandler::fromClient($this->client, [
            'keyPrefix' => $this->keyPrefix
        ]);
        parent::init();
    }
}
