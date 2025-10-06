# 🚨 API Endpoints Ontbreken

## Probleem
De MCP server kan geen klachten ophalen omdat de Laravel API endpoints nog niet bestaan:
- `/api/complaints` → 404
- `/api/statistics` → 404
- etc.

## ✅ Oplossing 1: Gebruik Browser Automation (Werkt nu al!)

In plaats van API calls, gebruik de browser tools:

```
"Ga naar gemeente.test en laat me zien wat er staat"
"Log in op het dashboard en extract de tabel data"
"Maak een screenshot van de homepage"
```

## 🔧 Oplossing 2: Maak de API Endpoints

Je moet Laravel API routes en controllers maken. Hier is wat je nodig hebt:

### 1. API Routes maken in `routes/api.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\StatisticsController;

Route::prefix('complaints')->group(function () {
    Route::get('/', [ComplaintController::class, 'index']);
    Route::get('/{id}', [ComplaintController::class, 'show']);
    Route::post('/', [ComplaintController::class, 'store']);
    Route::patch('/{id}/status', [ComplaintController::class, 'updateStatus']);
    Route::post('/{id}/notes', [ComplaintController::class, 'addNote']);
    Route::get('/search', [ComplaintController::class, 'search']);
    Route::get('/map', [ComplaintController::class, 'mapData']);
});

Route::get('/statistics', [StatisticsController::class, 'index']);
```

### 2. Maak API Controller:

```bash
php artisan make:controller Api/ComplaintController
```

### 3. Implementeer de methods in de controller

Zodra de API endpoints bestaan, werken alle 8 API tools automatisch!

## 🎯 Voor Nu: Gebruik Browser Tools

Tot je de API hebt gebouwd, gebruik deze MCP tools:
- `browser_goto` - Navigeer naar pagina's
- `browser_login` - Log in als admin
- `browser_extract_table` - Haal tabel data op
- `browser_extract_text` - Lees tekst van pagina
- `browser_screenshot` - Maak screenshots
- `browser_get_dashboard_stats` - Dashboard statistieken

**Probeer:**
```
"Ga naar gemeente.test"
"Log in op het dashboard"
"Maak een screenshot van de homepage"
```
