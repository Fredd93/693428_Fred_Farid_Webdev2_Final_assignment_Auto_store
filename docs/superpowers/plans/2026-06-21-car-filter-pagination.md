# Car Filter Pagination Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add server-side LIMIT/OFFSET pagination (12 cars/page, Prev/Next) to the car catalog filter page so the DB only returns 12 rows per request.

**Architecture:** `CarModel.php` gets two new methods for paginated fetch and count. `get_cars.php` reads `page`/`limit` GET params and returns a JSON envelope. `carFilter.js` drives all rendering (initial load + filter + pagination) through a single `fetchCars()` function, eliminating the PHP server-side car render loop.

**Tech Stack:** PHP 8, PDO/MySQL, vanilla JS (ES6), Bootstrap 5

## Global Constraints

- 12 cars per page (hardcoded as `LIMIT = 12` in JS, default in PHP)
- Prev/Next only — no numbered pages
- Filter change resets to page 1
- Existing `getFilteredCars()` must remain untouched (other code may call it)
- No new dependencies — plain PHP + vanilla JS only

---

### Task 1: Add paginated query methods to CarModel

**Files:**
- Modify: `app/public/models/CarModel.php`

**Interfaces:**
- Consumes: existing `$filters` array shape `['brand', 'year', 'transmission', 'on_sale', 'price_min', 'price_max']`
- Produces:
  - `getFilteredCarsPaginated(array $filters, int $page, int $limit): CarDTO[]`
  - `getFilteredCarsCount(array $filters): int`

- [ ] **Step 1: Add `getFilteredCarsCount()` method**

Open `app/public/models/CarModel.php`. Add this method after `getFilteredCars()`:

```php
public function getFilteredCarsCount($filters)
{
    $sql = "SELECT COUNT(*) FROM cars WHERE 1=1";
    $params = [];

    if (!empty($filters['brand'])) {
        $sql .= " AND brand = :brand";
        $params[':brand'] = $filters['brand'];
    }
    if (!empty($filters['year'])) {
        $sql .= " AND year = :year";
        $params[':year'] = $filters['year'];
    }
    if (!empty($filters['transmission'])) {
        $sql .= " AND transmission = :transmission";
        $params[':transmission'] = $filters['transmission'];
    }
    if (!empty($filters['on_sale'])) {
        $sql .= " AND on_sale = :on_sale";
        $params[':on_sale'] = $filters['on_sale'];
    }
    if (!empty($filters['price_min'])) {
        $sql .= " AND price >= :price_min";
        $params[':price_min'] = $filters['price_min'];
    }
    if (!empty($filters['price_max'])) {
        $sql .= " AND price <= :price_max";
        $params[':price_max'] = $filters['price_max'];
    }

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}
```

- [ ] **Step 2: Add `getFilteredCarsPaginated()` method**

Add this method directly after `getFilteredCarsCount()`:

```php
public function getFilteredCarsPaginated($filters, $page, $limit)
{
    $sql = "SELECT * FROM cars WHERE 1=1";
    $params = [];

    if (!empty($filters['brand'])) {
        $sql .= " AND brand = :brand";
        $params[':brand'] = $filters['brand'];
    }
    if (!empty($filters['year'])) {
        $sql .= " AND year = :year";
        $params[':year'] = $filters['year'];
    }
    if (!empty($filters['transmission'])) {
        $sql .= " AND transmission = :transmission";
        $params[':transmission'] = $filters['transmission'];
    }
    if (!empty($filters['on_sale'])) {
        $sql .= " AND on_sale = :on_sale";
        $params[':on_sale'] = $filters['on_sale'];
    }
    if (!empty($filters['price_min'])) {
        $sql .= " AND price >= :price_min";
        $params[':price_min'] = $filters['price_min'];
    }
    if (!empty($filters['price_max'])) {
        $sql .= " AND price <= :price_max";
        $params[':price_max'] = $filters['price_max'];
    }

    $offset = ($page - 1) * $limit;
    $sql .= " LIMIT :limit OFFSET :offset";

    $stmt = self::$pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = new CarDTO(
            $row['car_id'],
            $row['brand'],
            $row['model'],
            $row['price'],
            $row['on_sale'],
            $row['discount'],
            $row['image_path']
        );
    }
    return $results;
}
```

- [ ] **Step 3: Verify manually**

Load the site and confirm no PHP errors appear. The new methods are not yet called anywhere so no visible change is expected.

- [ ] **Step 4: Commit**

```bash
git add app/public/models/CarModel.php
git commit -m "feat: add getFilteredCarsPaginated and getFilteredCarsCount to CarModel"
```

---

### Task 2: Update get_cars.php to return paginated JSON envelope

**Files:**
- Modify: `app/public/api/get_cars.php`

**Interfaces:**
- Consumes: `GET page` (int, default 1), `GET limit` (int, default 12), existing filter params
- Produces: JSON `{ "cars": [...], "total": N, "page": N, "limit": N, "totalPages": N }`

- [ ] **Step 1: Replace get_cars.php contents**

Replace the entire file `app/public/api/get_cars.php` with:

```php
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
```

- [ ] **Step 2: Verify the endpoint manually**

In a browser or with curl, visit:
```
/api/get_cars?page=1&limit=12
```
Expected response shape:
```json
{
  "cars": [ { "car_id": 1, "brand": "...", ... }, ... ],
  "total": 47,
  "page": 1,
  "limit": 12,
  "totalPages": 4
}
```
Confirm `cars` has at most 12 items and `totalPages` is correct.

- [ ] **Step 3: Commit**

```bash
git add app/public/api/get_cars.php
git commit -m "feat: get_cars returns paginated JSON envelope with total and totalPages"
```

---

### Task 3: Update cars.php — remove PHP car loop, add pagination container

**Files:**
- Modify: `app/public/views/pages/cars.php`

**Interfaces:**
- Consumes: nothing new — JS will populate `#results` and `#pagination` on load
- Produces: empty `#results` div + new `#pagination` div for JS to target

- [ ] **Step 1: Replace the PHP car loop and results section**

In `app/public/views/pages/cars.php`, find and replace the entire `<section id="all-cars">` block (lines 49–88) with:

```php
  <!-- Results Container -->
  <section id="all-cars">
    <h2>All Cars Available</h2>
    <div id="results" class="card-grid"></div>
    <div id="pagination" class="pagination-controls"></div>
  </section>
```

Also remove the `require_once` and `$cars = $carModel->getAllCars();` lines at the top of the file (lines 7–10), since PHP no longer needs to load cars. The file top should become:

```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../partials/header.php';

$isEmployee = isset($_SESSION['role']) && in_array($_SESSION['role'], ['employee', 'manager']);
?>
```

- [ ] **Step 2: Verify page loads without PHP errors**

Load `/cars` in the browser. The page should show the filter panel, the "All Cars Available" heading, and an empty grid. No PHP errors in the console or on screen.

- [ ] **Step 3: Commit**

```bash
git add app/public/views/pages/cars.php
git commit -m "feat: cars.php — remove server-side car loop, add empty #results and #pagination"
```

---

### Task 4: Rewrite carFilter.js with fetchCars() and Prev/Next pagination

**Files:**
- Modify: `app/public/assets/js/carFilter.js`

**Interfaces:**
- Consumes: `GET /api/get_cars` JSON envelope `{ cars, total, page, limit, totalPages }`
- Produces: populated `#results` card grid + Prev/Next buttons in `#pagination`

- [ ] **Step 1: Replace carFilter.js with the full paginated implementation**

Replace the entire contents of `app/public/assets/js/carFilter.js` with:

```js
document.addEventListener('DOMContentLoaded', function () {
  const brandSelect        = document.getElementById('brandSelect');
  const yearSelect         = document.getElementById('yearSelect');
  const transmissionSelect = document.getElementById('transmissionSelect');
  const onSaleSelect       = document.getElementById('onSaleSelect');
  const priceMinInput      = document.getElementById('priceMin');
  const priceMaxInput      = document.getElementById('priceMax');
  const filterBtn          = document.getElementById('filterBtn');
  const resultsContainer   = document.getElementById('results');
  const paginationContainer = document.getElementById('pagination');

  const LIMIT = 12;
  let currentPage = 1;

  // Populate filter dropdowns from API
  fetch('/api/car_filter.php')
    .then(response => response.json())
    .then(data => {
      data.brands.forEach(brand => {
        const opt = document.createElement('option');
        opt.value = brand;
        opt.textContent = brand;
        brandSelect.appendChild(opt);
      });
      data.years.forEach(year => {
        const opt = document.createElement('option');
        opt.value = year;
        opt.textContent = year;
        yearSelect.appendChild(opt);
      });
      data.transmissions.forEach(transmission => {
        const opt = document.createElement('option');
        opt.value = transmission;
        opt.textContent = transmission;
        transmissionSelect.appendChild(opt);
      });
      data.on_sale_values.forEach(val => {
        const opt = document.createElement('option');
        opt.value = val;
        opt.textContent = val;
        onSaleSelect.appendChild(opt);
      });
      if (data.price_bounds) {
        priceMinInput.placeholder = data.price_bounds.min_price;
        priceMaxInput.placeholder = data.price_bounds.max_price;
      }
    })
    .catch(error => console.error('Error fetching filter data:', error));

  function buildParams() {
    const params = new URLSearchParams();
    if (brandSelect.value)        params.append('brand', brandSelect.value);
    if (yearSelect.value)         params.append('year', yearSelect.value);
    if (transmissionSelect.value) params.append('transmission', transmissionSelect.value);
    if (onSaleSelect.value)       params.append('on_sale', onSaleSelect.value);
    if (priceMinInput.value)      params.append('price_min', priceMinInput.value);
    if (priceMaxInput.value)      params.append('price_max', priceMaxInput.value);
    params.append('page', currentPage);
    params.append('limit', LIMIT);
    return params;
  }

  function renderCards(cars) {
    resultsContainer.innerHTML = '';

    if (cars.length === 0) {
      resultsContainer.innerHTML = '<p>No cars match the selected filters.</p>';
      return;
    }

    cars.forEach(car => {
      const cardDiv = document.createElement('div');
      cardDiv.classList.add('card');

      const price    = Number(car.price);
      const discount = Number(car.discount);
      let priceHTML  = `<p class="card-text">Price: $${price.toFixed(2)}</p>`;

      if (car.on_sale === 'yes' && discount > 0) {
        const newPrice = price * (1 - discount / 100);
        priceHTML = `<p class="card-text">Price: <del>$${price.toFixed(2)}</del> <span class="text-success">$${newPrice.toFixed(2)}</span></p>`;
      }

      cardDiv.innerHTML = `
        <img src="/assets/images/${car.image_path}" class="card-img-top" alt="${car.brand} ${car.model}">
        <div class="card-body text-center">
          <h5 class="card-title">${car.brand} ${car.model}</h5>
          ${priceHTML}
          ${car.on_sale === 'yes' && discount > 0 ? `<p class="text-danger">On Sale: ${discount}% off</p>` : ''}
          <a href="/car/${encodeURIComponent(car.car_id)}" class="btn btn-outline-accent">View Details</a>
        </div>
      `;
      resultsContainer.appendChild(cardDiv);
    });
  }

  function renderPagination(page, totalPages) {
    paginationContainer.innerHTML = '';

    if (totalPages <= 1) return;

    const prevBtn = document.createElement('button');
    prevBtn.textContent = 'Previous';
    prevBtn.className = 'btn btn-outline-accent';
    prevBtn.disabled = page === 1;
    prevBtn.addEventListener('click', function () {
      currentPage--;
      fetchCars();
    });

    const nextBtn = document.createElement('button');
    nextBtn.textContent = 'Next';
    nextBtn.className = 'btn btn-outline-accent';
    nextBtn.disabled = page === totalPages;
    nextBtn.addEventListener('click', function () {
      currentPage++;
      fetchCars();
    });

    const pageInfo = document.createElement('span');
    pageInfo.className = 'pagination-info';
    pageInfo.textContent = `Page ${page} of ${totalPages}`;

    paginationContainer.appendChild(prevBtn);
    paginationContainer.appendChild(pageInfo);
    paginationContainer.appendChild(nextBtn);
  }

  function fetchCars() {
    const params = buildParams();
    fetch('/api/get_cars?' + params.toString())
      .then(response => response.json())
      .then(data => {
        renderCards(data.cars);
        renderPagination(data.page, data.totalPages);
      })
      .catch(error => console.error('Error fetching cars:', error));
  }

  filterBtn.addEventListener('click', function () {
    currentPage = 1;
    fetchCars();
  });

  // Initial load
  fetchCars();
});
```

- [ ] **Step 2: Test initial page load**

Load `/cars` in the browser. Expected:
- 12 car cards appear without clicking Filter
- "Page 1 of N" label and Next button appear below the grid
- Prev button is disabled on page 1

- [ ] **Step 3: Test pagination**

Click Next. Expected:
- Grid updates to the next 12 cars
- "Page 2 of N" shown
- Prev becomes enabled, Next disabled on last page

- [ ] **Step 4: Test filter + pagination reset**

Select a brand, click Filter. Expected:
- Results reset to page 1 of the filtered set
- Prev is disabled again

- [ ] **Step 5: Test empty results**

Set a price range with no matches (e.g. Min 9999999), click Filter. Expected:
- "No cars match the selected filters." message shown
- Pagination container is empty

- [ ] **Step 6: Commit**

```bash
git add app/public/assets/js/carFilter.js
git commit -m "feat: carFilter.js — fetchCars() with server-side pagination and Prev/Next controls"
```

---

### Task 5: Add pagination CSS styles

**Files:**
- Modify: find the main stylesheet. Check `app/public/assets/css/` for the primary CSS file.

**Interfaces:**
- Consumes: `.pagination-controls` and `.pagination-info` class names from Task 4
- Produces: styled Prev/Next button row

- [ ] **Step 1: Locate the main stylesheet**

```bash
ls app/public/assets/css/
```

- [ ] **Step 2: Add pagination styles**

Append to the main CSS file (e.g. `app/public/assets/css/style.css`):

```css
.pagination-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin: 2rem 0;
}

.pagination-info {
  font-size: 0.95rem;
  color: var(--text-muted, #6c757d);
}
```

- [ ] **Step 3: Verify styling in browser**

Load `/cars`. Confirm Prev/Next buttons and page info are centered below the grid with reasonable spacing.

- [ ] **Step 4: Commit**

```bash
git add app/public/assets/css/style.css
git commit -m "feat: add pagination control styles"
```
