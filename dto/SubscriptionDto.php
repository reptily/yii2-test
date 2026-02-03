<?php

namespace app\dto;


class SubscriptionDto
{
    public function __construct(
        public int $authorId,
        public string $phone,
    ) {
    }
}