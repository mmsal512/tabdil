# **Product Requirements Document (PRD)**

### **TABDIL - Currency Converter Platform**

**Currencies Supported:**  
Saudi Riyals (SAR), Yemeni Rials (YER), Omani Rials (OMR), US Dollars (USD), UAE Dirhams (AED), Kuwaiti Dinars (KWD)

---

# **1. Purpose & Overview**

**TABDIL** is a robust, user-friendly, fully responsive currency converter platform that enables users to convert between six key regional and international currencies. The platform supports real-time exchange rates, multi-currency comparison, user accounts with favorites, and comprehensive admin controls.

The platform targets:

* **General consumers**
* **International travelers**
* **Users who frequently check FX rates or compare currencies**
* **Administrative teams who need to manually control exchange rates when required**

---

# **2. Core Features & Functional Requirements**

## **2.1 Real-Time Currency Conversion**

* Convert between any of the six supported currencies instantly.
* Use a **live FX API** (exchangerate-api) to fetch current rates.
* Display accurate, real-time conversion results as the user types.
* Show **timestamp of the last rate update** in precise date/time format:
  * **Arabic:** `2025-12-03 - 01:20 ص`
  * **English:** `December 3, 2025 - 01:20 AM`
* All converted amounts displayed as **whole numbers (integers)** without decimals.

## **2.2 Manual Exchange Rate Management (Admin)**

* Admin users can override API-provided rates.
* **Manual rates are set against YER (Yemeni Rial) only** - all other conversions are calculated through cross-rates.
* Manual overrides stored as backup rates in `api_settings` table.
* Admin interface includes:
  * Currency list management
  * Manual value entry per currency (vs YER)
  * Activation/deactivation of manual overrides
  * Audit logs for changes (recent admin activity)

## **2.3 Multi-Currency Comparison**

* User enters amount in base currency.
* System shows instant conversions into **all other 5 currencies**.
* Comparison table displays:
  * Currency name (translated)
  * Current exchange rate
  * Converted amount (integers only)
* **Instant loading** - all rows appear simultaneously without progressive animation.

## **2.4 Favorites System (User Accounts)**

Users can:

* Save frequently used conversions.
* Name or label saved conversions.
* Access a "Favorites" dashboard.
* Edit or delete favorites.

Requires user authentication (see Section 6).

## **2.5 Language Support**

* **Arabic and English**
* Entire interface translatable via Laravel localization.
* **Automatic RTL/LTR switching** when language changes (Turbo.js integration).
* User preference saved via:
  * Account settings
  * Browser session for guests

## **2.6 Performance Enhancements**

* **Turbo.js integration** for instant page transitions without full reloads.
* Currency converter re-initializes automatically after Turbo navigation.
* HTML direction (RTL/LTR) updates dynamically without page refresh.

## **2.7 Responsive UI/UX**

* Built using **Laravel Blade** templates with **TailwindCSS**.
* Fully responsive design for:
  * Mobile (primary use-case)
  * Tablet
  * Desktop
* Modern, clean design with gradient backgrounds and glassmorphism effects.
* **Custom branding:** TABDIL logo and identity throughout.

---

# **3. Admin Panel Requirements**

Admin panel (`/admin/*`) includes:

## **3.1 Authentication**

* Dedicated admin login page.
* Email/password authentication ONLY.
* Single admin role (Super Admin).

## **3.2 Dashboard Overview**

* **Total Users Count** - Display total registered users.
* **Recent Admin Activity Logs** - Show latest admin actions.
* **API Status** - Show current API provider and connection status.
* **Quick Actions** - Links to manage users, rates, and API settings.

## **3.3 Backup Rate Controls**

* CRUD interface for manual exchange rates (vs YER).
* Backup rates stored in `api_settings` table.
* Used as fallback when API fails.
* Toggle between:
  * Live API mode
  * Manual backup mode

## **3.4 User Management**

* View all registered users.
* Filter between authenticated and guest users.
* Basic user information display.

## **3.5 API Settings Management**

* Configure API provider (`api_provider`).
* Set API key (`api_key`).
* Configure cache duration (`cache_duration` in minutes).
* Enable/disable API (`api_enabled` boolean).
* **Backup rates** for all currencies vs YER.

## **3.6 Audit Logs**

* Track all admin actions.
* Display:
  * Admin user
  * Action type
  * Timestamp
  * Data before/after changes

---

# **4. Non-Functional Requirements**

## **4.1 Performance**

* Converter must respond within **200–500ms**.
* Page load under **3 seconds** on 4G/mobile.
* Cache API responses (configurable via `cache_duration` in `api_settings`).
* **Turbo.js** reduces navigation time to near-instant.

## **4.2 Scalability**

* System supports future additions:
  * More currencies
  * Additional API providers
  * Mobile apps

## **4.3 Availability**

* Target availability: **99.9% uptime**.
* **Fallback mechanism:** Use backup rates from `api_settings` when API fails.

## **4.4 Accessibility**

* WCAG-compliant contrast.
* Full RTL support for Arabic with automatic switching.
* No progressive animations on comparison table to avoid jarring UX.

---

# **5. Technical Environment**

## **5.1 Programming Languages & Frameworks**

* **Backend:** PHP 8.2
* **Framework:** Laravel 11
* **Frontend:** Laravel Blade templates
* **JavaScript:** 
  * Vanilla JavaScript for core logic
  * **Turbo.js** for instant navigation
* **Styling:** TailwindCSS with custom configuration
* **Build Tool:** **Vite** (for asset compilation)
* **NO Charts library** (historical charts removed)

## **5.2 Key Libraries & Tools**

* **Turbo.js** - Instant page transitions
* **FreeCurrencyAPI** - Real-time exchange rates
* **TailwindCSS** - Utility-first CSS framework
* **Vite** - Modern frontend build tool

---

# **6. Authentication System**

## **6.1 User Authentication Options**

Users can choose:

1. **Email + Password**
   * Standard Laravel authentication
   * Email verification enabled

2. **Anonymous/Guest Access**
   * Guest users can use converter without account
   * Favorites require account creation
   * Guests can create account at any time

**Note:** Social login (Google/Facebook) is **NOT implemented** in current version.

## **6.2 Admin Authentication**

* Admins use **email/password only**
* Admin accounts created manually in database
* Access to `/admin/*` routes protected by `admin` middleware

## **6.3 Authentication Implementation**

* Laravel Breeze for authentication scaffolding
* Middleware:
  * `auth` - Protected user routes
  * `admin` - Admin-only routes
  * `guest` - Public routes

---

# **7. Database Environment**

## **7.1 Database Type**

* **MySQL 8.0+**

## **7.2 Hosting**

* **Local Development:** XAMPP Control Panel
* **Production:** Laravel Cloud (planned deployment target)
* PRD allows flexibility for future hosting providers

## **7.3 Core Tables**

### **users**
* id, name, email, email_verified_at
* password
* remember_token
* created_at, updated_at

### **favorites**
* id
* user_id
* base_currency
* target_currency
* amount
* converted_amount
* label (optional)
* created_at, updated_at

### **exchange_rates**
* id
* base_currency
* target_currency
* rate_value
* source (api/manual)
* created_at, updated_at

### **api_settings**
* id
* key (e.g., 'api_provider', 'api_key', 'cache_duration', 'api_enabled', backup rates)
* value
* created_at, updated_at

**Backup rates stored as:** `backup_rate_SAR`, `backup_rate_USD`, etc.

### **admin_logs**
* id
* admin_id
* action
* description
* created_at

### **historical_rates**
* id
* date
* base_currency
* target_currency
* rate_value

### **Other Laravel Tables**
* cache, cache_locks
* failed_jobs, jobs, job_batches
* migrations
* password_reset_tokens
* sessions

---

# **8. API Integration Requirements**

## **8.1 FX Rate API**

**Primary Provider:** FreeCurrencyAPI

The system must:

* Pull real-time rates from FreeCurrencyAPI.
* Cache responses based on `cache_duration` setting.
* Validate API response integrity.
* **Fallback handling if API fails:**
  * Use backup rates from `api_settings` table
  * Log failure in system logs
  * Continue serving conversions with backup data

## **8.2 API Configuration**

Stored in `api_settings` table:

* `api_provider` - API provider name
* `api_key` - API authentication key
* `cache_duration` - Cache time in minutes (e.g., 60)
* `api_enabled` - Toggle API on/off
* `backup_rate_*` - Backup rates for each currency vs YER

## **8.3 API Failure Modes**

* Use cached/backup rates immediately
* Log error for admin review
* Display last update timestamp to users
* Admin notified via dashboard widget

---

# **9. Buy/Sell Rate Logic**

**All rates stored as sell rates (YER → Foreign Currency)**

### **Conversion Logic:**

1. **Foreign → YER:**
   * Use **buy rate** = `1 / sell_rate`
   * Example: If USD sell = 250 YER, then USD buy = 1/250

2. **YER → Foreign:**
   * Use **sell rate** directly from database

3. **Foreign → Foreign (Cross Rate):**
   * Convert through YER as intermediary
   * Example: USD → SAR = (USD → YER) × (YER → SAR)

### **Manual Rate Entry:**

* Admin enters **sell rates only** (YER → Foreign)
* System calculates buy rates automatically
* Cross-rates calculated dynamically

---

# **10. Recent System Improvements**

## **10.1 Branding Update**
* Application rebranded from generic "Currency Converter" to **"TABDIL"**
* Custom logo implemented
* Updated favicon and app identity

## **10.2 Turbo.js Integration**
* Instant page navigation without full reloads
* Currency converter re-initializes on Turbo events
* Improved perceived performance

## **10.3 Date/Time Display Format**
* Changed from relative time ("منذ يوم") to absolute format
* Arabic: `2025-12-03 - 01:20 ص`
* English: `December 3, 2025 - 01:20 AM`
* Removed colored status indicators for cleaner UI

## **10.4 Comparison Table Optimization**
* Removed progressive row loading animation
* All currency rows load instantly
* Eliminated jarring "jumping" effect
* Smoother, more professional UX

## **10.5 RTL/LTR Auto-Switching**
* Language changes trigger automatic direction update
* No page refresh required
* Powered by Turbo.js event listeners

---

# **11. Deployment Architecture**

## **11.1 Primary Hosting**

* **Production Target:** Laravel Cloud

## **11.2 Architecture**

* Laravel application container
* Cloud-managed MySQL database
* CDN for static assets (logo, images)
* Session storage in database
* Queue support for:
  * API rate syncing
  * Email notifications (if needed)

## **11.3 Deployment Flexibility**

The system can also be deployed to:

* VPS (DigitalOcean, Linode, etc.)
* Shared hosting with PHP support
* Docker containers
* AWS / Google Cloud / Azure

---

# **12. Success Metrics**

* **Conversion Accuracy:** ±0.01% from API source
* **Page Load Time:** < 3 seconds on 4G
* **Turbo Navigation:** < 500ms perceived load
* **API Uptime vs. Backup Mode:** Track usage ratio
* **User Retention:** Measure favorites usage
* **Admin Efficiency:** Track manual override frequency

---

# **13. Removed Features**

The following features were **removed** from the original PRD and are **NOT** implemented:

* ❌ **Historical Charts & Trends** - Removed completely
* ❌ **Social Login (Google/Facebook)** - Not implemented
* ❌ **2FA for Admin** - Not implemented
* ❌ **Chart.js/ApexCharts** - Not used (charts removed)
* ❌ **Time range options for charts** - N/A
* ❌ **Export charts as image/PDF** - N/A

---

# **14. Future Considerations**

Potential future enhancements (not in current scope):

* Historical rate charts (if needed)
* Social login integration
* Mobile app (iOS/Android)
* Additional currencies
* Multiple API providers with auto-failover
* Advanced analytics dashboard
* Email notifications for rate alerts
* API rate limiting controls

---

**Document Version:** 2.0  
**Last Updated:** December 3, 2025  
**Status:** Reflects current production system (TABDIL)
