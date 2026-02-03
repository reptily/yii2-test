<?php

namespace app\dto;

use yii\web\UploadedFile;

class BookDto
{
    public function __construct(
        public string $title,
        public int $year,
        public ?string $description = null,
        public ?string $isbn = null,
        public array $authorIds = [],
        public ?UploadedFile $imageFile = null,
    ) {
    }
}