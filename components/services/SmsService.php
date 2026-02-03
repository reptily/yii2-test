<?php

namespace app\components\services;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

class SmsService extends Component
{
    public $apiKey;
    public $apiUrl;

    public function send($to, $text)
    {
        $params = [
            'send' => $text,
            'to' => $to,
            'apikey' => $this->apiKey,
            'format' => 'json'
        ];

        try {
            $url = $this->apiUrl . '?' . http_build_query($params);
            $response = file_get_contents($url);

            if ($response === false) {
                Yii::error("Ошибка соединения с SMSPilot", __METHOD__);
                return false;
            }

            return Json::decode($response);
        } catch (\Exception $e) {
            Yii::error("SMSPilot Exception: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }
}
