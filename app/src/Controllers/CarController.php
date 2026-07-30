<?php
namespace GTA\Controllers;

use GTA\Models\CarModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;
use GTA\Helpers\ValidationHelper;

class CarController
{
    private const ALLOWED_IMAGE_EXT = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    private const MAX_IMAGE_BYTES   = 8 * 1024 * 1024;

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
            'lease_available' => isset($_GET['lease_available']) ? (int)$_GET['lease_available'] : null,
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

    private function validateUploadedFiles(array $filesInput): ?string
    {
        $names = (array) $filesInput['name'];
        $tmps  = (array) $filesInput['tmp_name'];
        $sizes = (array) $filesInput['size'];
        $errs  = (array) $filesInput['error'];

        foreach ($names as $i => $name) {
            if ($errs[$i] !== UPLOAD_ERR_OK) continue;

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, self::ALLOWED_IMAGE_EXT, true)) {
                return "\"$name\" has an unsupported file type. Allowed: " . implode(', ', self::ALLOWED_IMAGE_EXT);
            }
            if ($sizes[$i] > self::MAX_IMAGE_BYTES) {
                return "\"$name\" exceeds the 8MB size limit";
            }
            if (@getimagesize($tmps[$i]) === false) {
                return "\"$name\" is not a valid image";
            }
        }
        return null;
    }

    private function saveUploadedFiles(array $filesInput): array
    {
        $paths = [];
        $dir   = __DIR__ . '/../../public/assets/images/';
        $names = (array) $filesInput['name'];
        $tmps  = (array) $filesInput['tmp_name'];
        $errs  = (array) $filesInput['error'];

        foreach ($names as $i => $name) {
            if ($errs[$i] !== UPLOAD_ERR_OK) continue;
            $ext    = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $clean  = preg_replace('/[^a-zA-Z0-9\-_]/', '_', pathinfo($name, PATHINFO_FILENAME));
            $unique = uniqid('car_', true) . '_' . $clean . '.' . $ext;
            if (move_uploaded_file($tmps[$i], $dir . $unique)) {
                $paths[] = 'assets/images/' . $unique;
            }
        }
        return $paths;
    }

    public function store(): void
    {
        AuthMiddleware::require('employee');

        $requiredFields = ['brand','model','year','transmission','price','status'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) ResponseHelper::error("Missing field: $field", 400);
        }
        if (!isset($_POST['on_sale']))  ResponseHelper::error('Missing field: on_sale', 400);
        if (!isset($_POST['discount'])) ResponseHelper::error('Missing field: discount', 400);

        if (!ValidationHelper::isValidYear($_POST['year'])) {
            ResponseHelper::error('Year must be a whole number between 1900 and ' . ((int)date('Y') + 1), 422);
        }
        if (!ValidationHelper::isPositiveNumber($_POST['price'])) {
            ResponseHelper::error('Price must be a positive number', 422);
        }
        if (!in_array((string)$_POST['on_sale'], ['0', '1'], true)) {
            ResponseHelper::error('on_sale must be 0 or 1', 422);
        }
        if (!ValidationHelper::isNonNegativeNumber($_POST['discount']) || (float)$_POST['discount'] > 100) {
            ResponseHelper::error('Discount must be a number between 0 and 100', 422);
        }
        if (isset($_POST['lease_available']) && !in_array((string)$_POST['lease_available'], ['0', '1'], true)) {
            ResponseHelper::error('lease_available must be 0 or 1', 422);
        }

        $fileInput = $_FILES['images'] ?? null;
        if ($fileInput) {
            $fileError = $this->validateUploadedFiles($fileInput);
            if ($fileError) ResponseHelper::error($fileError, 422);
        }
        $imagePaths = $fileInput ? $this->saveUploadedFiles($fileInput) : [];
        $thumbnail  = $imagePaths[0] ?? '';

        $data = [
            ':brand'           => $_POST['brand'],
            ':model'           => $_POST['model'],
            ':year'            => $_POST['year'],
            ':transmission'    => $_POST['transmission'],
            ':engine_spec'     => $_POST['engine_spec']   ?? '',
            ':car_condition'   => $_POST['car_condition'] ?? '',
            ':description'     => $_POST['description']   ?? '',
            ':color'           => $_POST['color']         ?? '',
            ':price'           => $_POST['price'],
            ':on_sale'         => (int) $_POST['on_sale'],
            ':discount'        => (float) $_POST['discount'],
            ':lease_available' => (int) ($_POST['lease_available'] ?? 0),
            ':lease_terms'     => $_POST['lease_terms']   ?? '',
            ':status'          => $_POST['status'],
            ':image_path'      => $thumbnail,
        ];

        $id = $this->cars->create($data);
        if ($imagePaths) $this->cars->syncImages($id, $imagePaths);

        ResponseHelper::json($this->cars->findById($id), 201);
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

            if (isset($_FILES['images'])) {
                $fileError = $this->validateUploadedFiles($_FILES['images']);
                if ($fileError) ResponseHelper::error($fileError, 422);

                $newPaths = $this->saveUploadedFiles($_FILES['images']);
                if ($newPaths) {
                    $this->cars->syncImages($id, $newPaths);
                    $data['image_path'] = $newPaths[0];
                }
            }
        } else {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        if (isset($data['year']) && !ValidationHelper::isValidYear($data['year'])) {
            ResponseHelper::error('Year must be a whole number between 1900 and ' . ((int)date('Y') + 1), 422);
        }
        if (isset($data['price']) && !ValidationHelper::isPositiveNumber($data['price'])) {
            ResponseHelper::error('Price must be a positive number', 422);
        }
        if (isset($data['discount']) && (!ValidationHelper::isNonNegativeNumber($data['discount']) || (float)$data['discount'] > 100)) {
            ResponseHelper::error('Discount must be a number between 0 and 100', 422);
        }
        if (isset($data['on_sale']) && !in_array((string)$data['on_sale'], ['0', '1'], true)) {
            ResponseHelper::error('on_sale must be 0 or 1', 422);
        }
        if (isset($data['lease_available']) && !in_array((string)$data['lease_available'], ['0', '1'], true)) {
            ResponseHelper::error('lease_available must be 0 or 1', 422);
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
