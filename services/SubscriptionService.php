<?php

namespace app\services;

use app\dto\SubscriptionDto;
use app\models\Subscription;

class SubscriptionService
{
    public function subscribe(Subscription $model, SubscriptionDto $dto): bool
    {
        $model->author_id = $dto->authorId;
        $model->phone = $dto->phone;

        if ($model->validate()) {
            $exists = Subscription::find()
                ->where(['author_id' => $dto->authorId, 'phone' => $dto->phone])
                ->exists();

            return $exists ?: $model->save(false);
        }

        return false;
    }
}