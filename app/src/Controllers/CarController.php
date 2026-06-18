<?php
namespace GTA\Controllers;

use GTA\Models\CarModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class CarController
{
    private CarModel $cars;

    public function __construct() { $this->cars = new CarModel(); }

    public function index(): void
    {
        $page    = max(1, (int)($_GET['page']  ?? 1));
        $limit   = min(50, max(1, (int)($_GET['limit'] ?? 12)));
        $filters = array_filter([
            'brand'        => $_GET['brand']        ?? null,
            'year'         => $_GET['year']          ?? null,
            'transmission' => $_GET['transmission']  ?? null,
            'min_price'    => isset($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price'    => isset($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'on_sale'      => isset($_GET['on_sale'])   ? (int)$_GET['on_sale']     : null,
        ], fn($v) => $v !== null);

        ResponseHelper::json($this->cars->listPaginated($filters, $page, $limit));
    }

    public function filters(): void
    {
        ResponseHelper::json($this->cars->getFilterOptions());
    }

    public function show(int $id): void
    {
        $car = $this->cars->findById($id);
        $car ? ResponseHelper::json($car) : ResponseHelper::error('Car not found', 404);
    }

    public function store(): void
    {
        AuthMiddleware::require('employee');

        $requiredFields = ['brand','model','year','transmission','price','status'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) ResponseHelper::error("Missing field: $field", 400);
        }
        // on_sale and discount are numeric — 0 is valid, so check isset not empty
        if (!isset($_POST['on_sale']))  ResponseHelper::error('Missing field: on_sale', 400);
        if (!isset($_POST['discount'])) ResponseHelper::error('Missing field: discount', 400);

        if (!isset($_FILES['image_path']) || $_FILES['image_path']['error'] !== UPLOAD_ERR_OK) {
            ResponseHelper::error('Image upload failed or missing', 400);
        }

        $originalName = basename($_FILES['image_path']['name']);
        $cleanName    = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $originalName);
        $uniqueName   = uniqid('car_', true) . '_' . $cleanName;
        $uploadPath   = __DIR__ . '/../../public/assets/images/' . $uniqueName;
        $relativePath = 'assets/images/' . $uniqueName;

        if (!move_uploaded_file($_FILES['image_path']['tmp_name'], $uploadPath)) {
            ResponseHelper::error('Failed to save image', 500);
        }

        $data = [
            ':brand'           => $_POST['brand'],
            ':model'           => $_POST['model'],
            ':year'            => $_POST['year'],
            ':transmission'    => $_POST['transmission'],
            ':engine_spec'     => $_POST['engine_spec']     ?? '',
            ':car_condition'   => $_POST['car_condition']   ?? '',
            ':description'     => $_POST['description']     ?? '',
            ':color'           => $_POST['color']           ?? '',
            ':price'           => $_POST['price'],
            ':on_sale'         => $_POST['on_sale'],
            ':discount'        => $_POST['discount'],
            ':lease_available' => ($_POST['lease_available'] ?? '') === 'yes' ? 1 : 0,
            ':lease_terms'     => $_POST['lease_terms']     ?? '',
            ':status'          => $_POST['status'],
            ':image_path'      => $relativePath,
        ];

        $id  = $this->cars->create($data);
        $car = $this->cars->findById($id);
        ResponseHelper::json($car, 201);
    }

    public function update(int $id): void
    {
        AuthMiddleware::require('employee');

        $car = $this->cars->findById($id);
        if (!$car) ResponseHelper::error('Car not found', 404);

        if (!empty($_POST)) {
            $data = array_intersect_key($_POST, array_flip([
                'brand','model','year','transmission','engine_spec','car_condition',
                'description','color','price','on_sale','discount','lease_available','lease_terms','status'
            ]));

            if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
                $originalName = basename($_FILES['image_path']['name']);
                $cleanName    = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $originalName);
                $uniqueName   = uniqid('car_', true) . '_' . $cleanName;
                $uploadPath   = __DIR__ . '/../../public/assets/images/' . $uniqueName;
                move_uploaded_file($_FILES['image_path']['tmp_name'], $uploadPath);
                $data['image_path'] = 'assets/images/' . $uniqueName;
            }
        } else {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        $this->cars->update($id, $data);
        ResponseHelper::json($this->cars->findById($id));
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::require('admin');
        $car = $this->cars->findById($id);
        if (!$car) ResponseHelper::error('Car not found', 404);
        $this->cars->delete($id);
        ResponseHelper::json(['message' => 'Car deleted']);
    }
}
