<?php

return [
    'host' => env('ELASTICSEARCH_HOST', 'http://localhost:9200'),
    'product_index' => env('ELASTICSEARCH_PRODUCT_INDEX', 'products'),
];
