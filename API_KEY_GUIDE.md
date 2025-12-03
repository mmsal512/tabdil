# API Key Storage & Usage Guide

## Where is the API Key Stored?

### Database Location
- **Table:** `api_settings`
- **Key Field:** `key` = 'api_key'
- **Value Field:** `value` = (your actual API key)

### How to View the API Key in Database

```sql
SELECT * FROM api_settings WHERE `key` = 'api_key';
```

## How the System Uses the API Key

### 1. Admin Panel Entry
When you enter an API key at `/admin/rates`:
```
User enters key → POST /admin/api/update → Stored in api_settings table
```

### 2. Currency Service Retrieval
When converting currencies, the system:

```php
// Step 1: Fetch config from database (cached for 5 mins)
$config = DB::table('api_settings')->pluck('value', 'key')->toArray();

// Step 2: Extract values
$apiKey = $config['api_key'] ?? null;
$apiProvider = $config['api_provider'] ?? 'exchangerate-api';
$apiEnabled = $config['api_enabled'] ?? 'false';
```

### 3. API Request
```php
// Build URL based on provider
switch ($provider) {
    case 'freecurrencyapi':
        $params['apikey'] = $apiKey; // <- API key used here
        break;
        
    case 'fixer':
        $params['access_key'] = $apiKey; // <- API key used here
        break;
        
    case 'exchangerate-api':
        // No key needed (free endpoint)
        break;
}

Http::get($url, $params); // Request sent with key
```

## API Providers & Their Key Requirements

| Provider | Requires Key? | Key Parameter | Free Tier |
|----------|--------------|---------------|-----------|
| **ExchangeRate-API** | ❌ No | N/A | Unlimited |
| **FreeCurrencyAPI** | ✅ Yes | `apikey` | 5000 req/month |
| **Fixer.io** | ✅ Yes | `access_key` | 100 req/month |

## Current Configuration

To check your current settings:

```php
// In Laravel console (php artisan tinker)
DB::table('api_settings')->get();
```

Expected output:
```
[
    ['key' => 'api_provider', 'value' => 'freecurrencyapi'],
    ['key' => 'api_key', 'value' => 'your-key-here-or-null'],
    ['key' => 'cache_duration', 'value' => '60'],
    ['key' => 'api_enabled', 'value' => 'true']
]
```

## Files Modified

1. **`app/Services/CurrencyConversionService.php`**
   - Added `getApiConfig()` - Fetches settings from database
   - Added `getApiUrl()` - Maps provider to URL
   - Modified `fetchRatesFromApi()` - Uses dynamic API key

2. **`app/Http/Controllers/AdminController.php`**
   - `apiConfig()` - Displays settings page
   - `updateApiConfig()` - Saves API key to database
   - `checkApiHealth()` - Tests API connection

## How to Get an API Key

### FreeCurrencyAPI (Recommended)
1. Visit: https://freecurrencyapi.com/
2. Click "Get Free API Key"
3. Sign up with email
4. Copy your API key
5. Paste in admin panel

### Fixer.io
1. Visit: https://fixer.io/
2. Click "Get Free API Key"
3. Sign up
4. Copy key from dashboard

### ExchangeRate-API
- No key needed! Already working by default
- Uses: `https://api.exchangerate-api.com/v4/latest/USD`

## Testing Your API Key

After entering a key in the admin panel:

1. Click "Test Connection" button
2. Check Laravel logs: `storage/logs/laravel.log`
3. Look for:
   - ✅ "Successfully fetched rates from {provider}"
   - ❌ "API request failed"

## Troubleshooting

**API not working?**
- Check if `api_enabled` is set to `'true'` (string, not boolean)
- Verify API key has no extra spaces
- Ensure API provider is spelled exactly as shown above
- Check Laravel logs for detailed errors

**Where to check logs:**
```bash
tail -f storage/logs/laravel.log
```
