import test from 'node:test'
import assert from 'node:assert/strict'

import {
  buildCarsQuery,
  mapCarDetail,
  mapCarListResponse,
  normalizeCarImagePath,
} from './cars.js'

test('buildCarsQuery translates Vue filters into backend query params', () => {
  assert.deepEqual(
    buildCarsQuery({
      page: 2,
      limit: 12,
      brand: 'BMW',
      year: '2024',
      transmission: 'Automatic',
      max_price: '65000',
      on_sale: true,
    }),
    {
      brand: 'BMW',
      limit: 12,
      max_price: '65000',
      on_sale: 1,
      page: 2,
      transmission: 'Automatic',
      year: '2024',
    },
  )
})

test('normalizeCarImagePath maps stored filenames and relative paths to public asset urls', () => {
  assert.equal(normalizeCarImagePath('ghost.jpg'), '/assets/images/ghost.jpg')
  assert.equal(
    normalizeCarImagePath('../../assets/images/phantom.jpg'),
    '/assets/images/phantom.jpg',
  )
  assert.equal(
    normalizeCarImagePath('/assets/images/spectre.jpg'),
    '/assets/images/spectre.jpg',
  )
})

test('mapCarListResponse normalizes paginated PHP responses for Vue pagination and cards', () => {
  assert.deepEqual(
    mapCarListResponse({
      data: [
        {
          id: 7,
          brand: 'Rolls-Royce',
          model: 'Spectre',
          year: 2025,
          transmission: 'Automatic',
          price: '420000',
          on_sale: 1,
          discount: '10',
          image_path: 'spectre.jpg',
          thumbnail: 'assets/images/spectre-thumb.jpg',
        },
      ],
      meta: {
        total: 14,
        page: 2,
        limit: 12,
        pages: 2,
      },
    }),
    {
      data: [
        {
          id: 7,
          brand: 'Rolls-Royce',
          model: 'Spectre',
          year: 2025,
          transmission: 'Automatic',
          price: '420000',
          on_sale: true,
          discount: '10',
          image_path: '/assets/images/spectre.jpg',
          thumbnail: '/assets/images/spectre-thumb.jpg',
          status: '',
        },
      ],
      meta: {
        total: 14,
        page: 2,
        limit: 12,
        pages: 2,
      },
    },
  )
})

test('mapCarDetail normalizes a raw car row for the detail page', () => {
  assert.deepEqual(
    mapCarDetail({
      id: 11,
      brand: 'Bentley',
      model: 'Flying Spur',
      year: 2023,
      transmission: 'Automatic',
      price: '195000',
      on_sale: 0,
      discount: '0',
      image_path: 'assets/images/spur.jpg',
      lease_available: 1,
      lease_terms: '48 months',
      car_condition: 'used',
      color: 'Black',
      description: 'Luxury sedan',
      engine_spec: 'V8',
      status: 'available',
      images: ['assets/images/spur.jpg', 'assets/images/spur-2.jpg'],
    }),
    {
      id: 11,
      brand: 'Bentley',
      model: 'Flying Spur',
      year: 2023,
      transmission: 'Automatic',
      price: '195000',
      on_sale: false,
      discount: '0',
      image_path: '/assets/images/spur.jpg',
      thumbnail: '/assets/images/spur.jpg',
      lease_available: true,
      lease_terms: '48 months',
      car_condition: 'used',
      color: 'Black',
      description: 'Luxury sedan',
      engine_spec: 'V8',
      status: 'available',
      images: ['/assets/images/spur.jpg', '/assets/images/spur-2.jpg'],
    },
  )
})
