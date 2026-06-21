# Car Filter Pagination Design

**Date:** 2026-06-21
**Feature:** Server-side pagination for the car filtering system

## Overview

Add server-side `LIMIT/OFFSET` pagination to the car catalog filter page. 12 cars per page, Prev/Next navigation, resets to page 1 on every new filter. The DB does all slicing — only 12 rows travel the wire per request.

---

## Backend

### CarModel.php — two new methods

**`getFilteredCarsPaginated($filters, $page, $limit)`**
- Same WHERE clause as `getFilteredCars()`
- Appends `LIMIT :limit OFFSET :offset` where offset = `($page - 1) * $limit`
- Returns array of CarDTO objects

**`getFilteredCarsCount($filters)`**
- Same WHERE clause as `getFilteredCars()`
- `SELECT COUNT(*) FROM cars WHERE ...`
- Returns integer total

The existing `getFilteredCars()` is left untouched so nothing else breaks.

### get_cars.php — two new GET params

- `page` (integer, default `1`)
- `limit` (integer, default `12`)

Response envelope:
```json
{
  "cars": [...],
  "total": 47,
  "page": 1,
  "limit": 12,
  "totalPages": 4
}
```

`totalPages` = `ceil(total / limit)`

---

## Frontend

### carFilter.js

Two state variables at module scope:
- `currentPage = 1`
- `LIMIT = 12`

A single `fetchCars()` function handles all fetching (initial load + filter clicks + pagination clicks):
1. Reads current filter values from the form controls
2. Appends `page=currentPage&limit=LIMIT` to the query params
3. Calls `/api/get_cars`
4. Renders car cards into `#results`
5. Renders Prev/Next controls into `#pagination`

**Prev/Next behaviour:**
- Prev button: disabled when `currentPage === 1`
- Next button: disabled when `currentPage === totalPages`
- Clicking either updates `currentPage` then calls `fetchCars()`

**Filter button behaviour:**
- Resets `currentPage = 1` before calling `fetchCars()`

**Initial load:**
- `fetchCars()` is called on `DOMContentLoaded` with no filters set, showing page 1 of all cars

### cars.php

- Remove the PHP `getAllCars()` loop that renders cars server-side — `#results` starts empty
- Add `<div id="pagination"></div>` below `<div id="results">` inside `#all-cars`

---

## Data Flow

```
DOMContentLoaded / Filter click / Prev / Next
        │
        ▼
fetchCars()
  builds URLSearchParams (filters + page + limit)
        │
        ▼
GET /api/get_cars?brand=...&page=2&limit=12
        │
        ▼
get_cars.php
  → CarModel::getFilteredCarsPaginated()   → 12 rows
  → CarModel::getFilteredCarsCount()       → total count
  → returns JSON envelope
        │
        ▼
carFilter.js renders cards + Prev/Next buttons
```

---

## Out of Scope

- Numbered page links (not requested)
- Load More / infinite scroll (not requested)
- URL query string sync (browser back/forward not preserved — acceptable for now)
- Sorting controls
