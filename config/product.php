<?php
return [
    'table_map' => [
        1 => 'product_food_beverages',
        2 => 'product_construction_hardware',
        3 => 'product_home_livings',
        4 => 'product_fashion_lifestyles',
        5 => 'product_automobiles',
        6 => 'product_education_stationery',
        7 => 'product_agricultures',
        8 => 'product_retails',
        9 => 'product_healths',
        10 => 'product_sports',
    ],

    'model_map' => [
        1 => \App\Models\ProductFoodBeverages::class,
        2 => \App\Models\ProductConstructionHardware::class,
        3 => \App\Models\ProductHomeLiving::class,
        4 => \App\Models\ProductFashionLifestyle::class,
        5 => \App\Models\ProductAutomobile::class,
        6 => \App\Models\EducationStationary::class,
        7 => \App\Models\ProductAgriculture::class,
        8 => \App\Models\ProductRetail::class,
        9 => \App\Models\ProductHealth::class,
        10 => \App\Models\ProductSports::class,
    ],
];
