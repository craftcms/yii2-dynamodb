<?php

namespace pixelandtonic\dynamodb\drivers;

use Yii;

class Queue extends \yii\queue\Queue
{
    use WithDynamoDbClient;

    /**
     * @inheritDoc
     */
    protected function pushMessage($message, $ttr, $delay, $priority)
    {
        try {
            $prefix = 'queue-prefix';
            $id = uniqid($prefix);

            $params = $this->buildItem($id, $message, $ttr, $delay, $priority);
            $result = $this->client->putItem($params);
        } catch (\Exception $e) {
            Yii::warning("Unable to push message: {$e->getMessage()}", __METHOD__);

            return '';
        }

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function status($id)
    {
        // TODO: Implement status() method.
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
                    'B' => $prefix,
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
                    'S' => $priority,
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
