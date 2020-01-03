<?php

namespace pixelandtonic\dynamodb\drivers;

use Yii;

class Queue extends \yii\queue\Queue
{
    use WithDynamoDbClient;

    protected $prefix = 'queue-prefix';

    /**
     * @inheritDoc
     */
    protected function pushMessage($message, $ttr, $delay, $priority)
    {
        try {
            $id = uniqid($this->prefix);

            $params = $this->buildItem($id, $message, $ttr, $delay, $priority);
            $this->client->putItem($params);
        } catch (\Exception $e) {
            Yii::warning("Unable to push message: {$e->getMessage()}", __METHOD__);

            return null;
        }

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function status($id)
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
            Yii::warning("Unable to push message: {$e->getMessage()}", __METHOD__);

            return null;
        }

        return $result['Item'];
    }

    protected function buildItem($id, $message, $ttr, $delay, $priority): array
    {
        $now = time();

        return [
            'TableName' => $this->table,
            'Item' => [
                $this->tableIdAttribute => [
                    'S' => $id,
                ],
                'channel' => [
                    'S' => $this->prefix,
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
            ]
        ];
    }
}
