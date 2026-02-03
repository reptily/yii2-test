<?php

namespace app\components\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;

class SmsNotifyJob extends BaseObject implements JobInterface
{
    public $phones;
    public $message;

    public function execute($queue)
    {
        foreach ($this->phones as $phone) {
            \Yii::$app->sms->send($phone, $this->message);
        }
    }
}