<?php

namespace tests\app;

use yii\base\BaseObject;
use yii\base\Exception;
use yii\queue\JobInterface;

class SimpleTestJob extends BaseObject implements JobInterface
{
    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
    }
}
