<?php
// app/public/api/get_cars.php

header('Content-Type: application/json');

require_once __DIR__ . '/../models/CarModel.php';

$carModel = new CarModel();

$filters = [
    'brand'        => $_GET['brand'] ?? null,
    'year'         => $_GET['year'] ?? null,
    'transmission' => $_GET['transmission'] ?? null,
    'on_sale'      => $_GET['on_sale'] ?? null,
    'price_min'    => $_GET['price_min'] ?? null,
    'price_max'    => $_GET['price_max'] ?? null,
];

$page  = max(1, (int) ($_GET['page'] ?? 1));
$limit = max(1, (int) ($_GET['limit'] ?? 12));

$total      = $carModel->getFilteredCarsCount($filters);
$totalPages = $total > 0 ? (int) ceil($total / $limit) : 1;
$cars       = $carModel->getFilteredCarsPaginated($filters, $page, $limit);

$output = [];
foreach ($cars as $car) {
    $output[] = [
        'car_id'     => $car->getCarId(),
        'brand'      => $car->getBrand(),
        'model'      => $car->getModel(),
        'price'      => $car->getPrice(),
        'on_sale'    => $car->getOnSale(),
        'discount'   => $car->getDiscount(),
        'image_path' => $car->getImage(),
    ];
}

echo json_encode([
    'cars'       => $output,
    'total'      => $total,
    'page'       => $page,
    'limit'      => $limit,
    'totalPages' => $totalPages,
]);
exit;
?>
