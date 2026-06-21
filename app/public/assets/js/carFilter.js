document.addEventListener('DOMContentLoaded', function () {
  const brandSelect         = document.getElementById('brandSelect');
  const yearSelect          = document.getElementById('yearSelect');
  const transmissionSelect  = document.getElementById('transmissionSelect');
  const onSaleSelect        = document.getElementById('onSaleSelect');
  const priceMinInput       = document.getElementById('priceMin');
  const priceMaxInput       = document.getElementById('priceMax');
  const filterBtn           = document.getElementById('filterBtn');
  const resultsContainer    = document.getElementById('results');
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
      const cardDiv  = document.createElement('div');
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

    const pageInfo = document.createElement('span');
    pageInfo.className = 'pagination-info';
    pageInfo.textContent = `Page ${page} of ${totalPages}`;

    const nextBtn = document.createElement('button');
    nextBtn.textContent = 'Next';
    nextBtn.className = 'btn btn-outline-accent';
    nextBtn.disabled = page === totalPages;
    nextBtn.addEventListener('click', function () {
      currentPage++;
      fetchCars();
    });

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

  // Initial load — page 1, no filters
  fetchCars();
});
