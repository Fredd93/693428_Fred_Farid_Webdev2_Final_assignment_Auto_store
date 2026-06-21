<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../partials/header.php';

$isEmployee = isset($_SESSION['role']) && in_array($_SESSION['role'], ['employee', 'manager']);
?>
<section class="heroCars">
  <div class="hero-overlay">
    <h1>A car selection fit for your taste</h1>
    <p>We host a wide range of cars for purchase and leasing</p>
  </div>
</section>
<div class="gt-container">
  <!-- Filter Panel -->
  <div class="filter-panel">
    <select id="brandSelect">
      <option value="">All Brands</option>
    </select>
    <select id="yearSelect">
      <option value="">All Years</option>
    </select>
    <select id="transmissionSelect">
      <option value="">All Transmissions</option>
    </select>
    <select id="onSaleSelect">
      <option value="">All Sale Status</option>
    </select>
    <input type="number" id="priceMin" placeholder="Min Price">
    <input type="number" id="priceMax" placeholder="Max Price">
    <button id="filterBtn" class="btn btn-primary">Filter</button>
    <?php if ($isEmployee): ?>
      <!-- Add Car button for employees -->
      <button id="addCarBtn" class="btn btn-success" style="margin-left: auto;">Add Car</button>
    <?php endif; ?>
  </div>

  <!-- Results Container -->
  <section id="all-cars">
    <h2>All Cars Available</h2>
    <div id="results" class="card-grid"></div>
    <div id="pagination" class="pagination-controls"></div>
  </section>
</div>

<!-- Include the Add Car Modal -->
<?php include __DIR__ . '/../partials/addCarModal.php'; ?>
<!-- edit bitch -->



<?php
// Include the footer partial (which closes the <body> and </html> tags)
include __DIR__ . '/../partials/footer.php';
?>

<!-- External JavaScript Files -->
<script src="../../assets/js/carFilter.js"></script>
<script src="../../assets/js/addCar.js"></script>
<script src="../../assets/js/car.js"></script>
