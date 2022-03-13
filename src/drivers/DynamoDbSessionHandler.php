<?php

namespace pixelandtonic\dynamodb\drivers;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\SessionHandler;

class DynamoDbSessionHandler extends SessionHandler
{
    /**
     * Creates a new DynamoDB Session Handler.
     *
     * The configuration array accepts the following array keys and values:
     * - table_name:                    Name of table to store the sessions.
     * - hash_key:                      Name of hash key in table. Default: "id".
     * - data_attribute:                Name of the data attribute in table. Default: "data".
     * - session_lifetime:              Lifetime of inactive sessions expiration.
     * - session_lifetime_attribute:    Name of the session life time attribute in table. Default: "expires".
     * - consistent_read:               Whether or not to use consistent reads.
     * - batch_config:                  Batch options used for garbage collection.
     * - locking:                       Whether or not to use session locking.
     * - max_lock_wait_time:            Max time (s) to wait for lock acquisition.
     * - min_lock_retry_microtime:      Min time (µs) to wait between lock attempts.
     * - max_lock_retry_microtime:      Max time (µs) to wait between lock attempts.
     *
     * You can find the full list of parameters and defaults within the trait
     * Aws\DynamoDb\SessionConnectionConfigTrait
     *
     * @param DynamoDbClient $client Client for doing DynamoDB operations
     * @param array          $config Configuration for the Session Handler
     *
     * @return SessionHandler
     */
    public static function fromClient(DynamoDbClient $client, array $config = []): SessionHandler
    {
        $config += ['locking' => false];
        if ($config['locking']) {
            $connection = new LockingSessionConnection($client, $config);
        } else {
            $connection = new StandardSessionConnection($client, $config);
        }

        return new static($connection);
    }
}
