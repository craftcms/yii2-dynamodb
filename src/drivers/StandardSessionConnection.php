<?php
namespace pixelandtonic\dynamodb\drivers;

/**
 * The standard connection performs the read and write operations to DynamoDB.
 */
class StandardSessionConnection extends \Aws\DynamoDb\StandardSessionConnection
{
    private ?string $_keyPrefix = null;

    /**
     * @param string $key
     *
     * @return array
     */
    protected function formatKey($key): array
    {
        return [$this->getHashKey() => ['S' => $this->calculateKey($key)]];
    }

    /**
     * Generates a unique key used for storing session data in cache.
     * @param string $id session variable name
     * @return string a safe cache key associated with the session variable name
     */
    protected function calculateKey($id): string
    {
        if (!$this->_keyPrefix) {
            return $id;
        }

        return $this->_keyPrefix . md5(json_encode([__CLASS__, $id]));
    }

    public function setKeyPrefix($value): void
    {
        $this->_keyPrefix = $value;
    }
}
