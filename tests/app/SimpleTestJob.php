<?php

namespace tests\app;

use yii\base\BaseObject;
use yii\queue\JobInterface;

class SimpleTestJob extends BaseObject implements JobInterface
{
    public $uid;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        // TODO: Implement execute() method.
    }
}
