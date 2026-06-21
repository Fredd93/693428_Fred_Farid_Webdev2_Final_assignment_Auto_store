function isTruthyFlag(value) {
  return value === true || value === 1 || value === '1' || value === 'yes' || value === 'true'
}

export function normalizeCarImagePath(imagePath) {
  if (!imagePath) {
    return ''
  }

  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }

  const normalizedPath = imagePath
    .replace(/\\/g, '/')
    .replace(/^(\.\.\/)+/, '')
    .replace(/^\/+/, '')

  if (normalizedPath.startsWith('assets/')) {
    return `/${normalizedPath}`
  }

  return `/assets/images/${normalizedPath}`
}

function mapCarSummary(rawCar = {}) {
  const imagePath = normalizeCarImagePath(rawCar.image_path)
  const thumbnail = normalizeCarImagePath(rawCar.thumbnail || rawCar.image_path)

  return {
    id: Number(rawCar.id ?? rawCar.car_id),
    brand: rawCar.brand ?? '',
    model: rawCar.model ?? '',
    year: rawCar.year ?? '',
    transmission: rawCar.transmission ?? '',
    price: rawCar.price ?? 0,
    on_sale: isTruthyFlag(rawCar.on_sale),
    discount: rawCar.discount ?? 0,
    image_path: imagePath,
    thumbnail,
    status: rawCar.status ?? '',
  }
}

export function mapCarListResponse(payload = {}) {
  return {
    data: Array.isArray(payload.data) ? payload.data.map(mapCarSummary) : [],
    meta: {
      total: Number(payload.meta?.total ?? 0),
      page: Number(payload.meta?.page ?? 1),
      limit: Number(payload.meta?.limit ?? 12),
      pages: Number(payload.meta?.pages ?? 1),
    },
  }
}

export function mapCarDetail(rawCar = {}) {
  const summary = mapCarSummary(rawCar)
  const images = Array.isArray(rawCar.images) && rawCar.images.length
    ? rawCar.images.map(normalizeCarImagePath)
    : (summary.image_path ? [summary.image_path] : [])

  return {
    ...summary,
    lease_available: isTruthyFlag(rawCar.lease_available),
    lease_terms: rawCar.lease_terms ?? '',
    car_condition: rawCar.car_condition ?? '',
    color: rawCar.color ?? '',
    description: rawCar.description ?? '',
    engine_spec: rawCar.engine_spec ?? '',
    images,
  }
}

export function buildCarsQuery(filters = {}) {
  const query = {
    page: filters.page ?? 1,
    limit: filters.limit ?? 12,
  }

  if (filters.brand) {
    query.brand = filters.brand
  }
  if (filters.year) {
    query.year = filters.year
  }
  if (filters.transmission) {
    query.transmission = filters.transmission
  }
  if (filters.min_price) {
    query.min_price = filters.min_price
  }
  if (filters.max_price) {
    query.max_price = filters.max_price
  }
  if (filters.on_sale) {
    query.on_sale = 1
  }

  return query
}

export async function fetchCarsPage(filters = {}) {
  const { default: client } = await import('./client.js')
  const { data } = await client.get('/cars', {
    params: buildCarsQuery(filters),
  })

  return mapCarListResponse(data)
}

export async function fetchCarFilters() {
  const { default: client } = await import('./client.js')
  const { data } = await client.get('/cars/filters')
  return {
    brands: data.brands ?? [],
    years: data.years ?? [],
    transmissions: data.transmissions ?? [],
    price_bounds: {
      min_price: Number(data.price_bounds?.min ?? 0),
      max_price: Number(data.price_bounds?.max ?? 0),
    },
  }
}

export async function fetchCarById(id) {
  const { default: client } = await import('./client.js')
  const { data } = await client.get(`/cars/${id}`)
  return mapCarDetail(data)
}

export async function createCar(payload) {
  const { default: client } = await import('./client.js')
  const { data } = await client.post('/cars', payload, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return mapCarDetail(data)
}

export async function updateCar(id, payload, options = {}) {
  const { default: client } = await import('./client.js')

  if (options.hasFiles) {
    const { data } = await client.post(`/cars/${id}`, payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return mapCarDetail(data)
  }

  const { data } = await client.put(`/cars/${id}`, payload)
  return mapCarDetail(data)
}

export async function deleteCarById(id) {
  const { default: client } = await import('./client.js')
  return client.delete(`/cars/${id}`)
}
