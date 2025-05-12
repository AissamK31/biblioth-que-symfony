<?php

use App\Kernel;

// Augmenter les limites d'upload pour les fichiers volumineux
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '12M');

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
