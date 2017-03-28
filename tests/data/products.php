<?php
$products = [];

for ($product_number = 1; $product_number <= 10; $product_number++) {
    $products[] = [
        'name' => sprintf('Test Product %s', $product_number),
        'description' => sprintf('Test Product Description %s', $product_number),
        'price' => sprintf(rand(1000, 9999) / 100),
    ];
}

return $products;
