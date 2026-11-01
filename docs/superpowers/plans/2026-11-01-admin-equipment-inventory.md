# Equipment Inventory View for Admin Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the admin equipment inventory view matching `designs/equipment-inventory-deisgn.png`, reusing the admin sidebar, topbar, department selector, and profile dropdown while adopting the card grid layout from the equipment catalog with admin management controls.

**Architecture:** CodeIgniter 4 MVC architecture with route protection. The admin route `admin/equipment` will be registered under the `admin` filter group and handled by `AdminController::equipment()`. It queries equipment filtered by department, category, status, and condition, computes total assets count, and renders `app/Views/admin/equipment/index.php`. The styling extends Bootstrap 5 with existing `dashboard.css`, `catalog.css`, and a dedicated `admin-inventory.css`.

**Tech Stack:** CodeIgniter 4, PHP 8+, Bootstrap 5.3, Bootstrap Icons 1.11, Custom CSS.

**Spec:** `designs/equipment-inventory-deisgn.png`

## Global Constraints

- Tech Stack: CodeIgniter 4, Bootstrap 5, and custom CSS.
- Reuse sidebar, footer, department, and profile dropdown from `app/Views/admin/dashboard.php`.
- Reuse cards and grid design from user equipment catalog (`app/Views/equipment/catalog.php` & `public/css/catalog.css`).
- Active sidebar navigation link must highlight "Equipment Inventory".
- Preserve existing working modules and route conventions without unnecessary rewrites.
- Support department scoping, search query, category filter, status filter, and condition filter with pagination.

## Review Focus

1. **Department filter persistence:** Selecting or switching department maintains the active filters and updates the inventory view for that department.
2. **Category, Status & Condition filters:** Filtering by category, status (`AVAILABLE`, `RESERVED`, `IN_USE`), and condition (`GOOD`, `EXCELLENT`, `FAIR`, `DAMAGED`) correctly narrows equipment records and preserves search terms.
3. **Card visuals matching reference:** Equipment cards display image, code, name, model, color-coded category badge with category icon, status dot + label, condition dot + label, and a "Manage" action button with wrench icon.
4. **Header badge and action button:** Page header displays "Equipment Management" with the total asset count badge ("X Total Assets"), descriptive subtitle, and "+ Add New Equipment" button.
5. **Pagination & footer:** Reuses `catalog_full` pager and shows "Showing X–Y of Z equipment" with functioning previous/next and page number controls.

---

### Task 1: Route Registration and Admin Controller Inventory Action

**Files:**
- Modify: `app/Config/Routes.php:32-37`
- Modify: `app/Controllers/AdminController.php`
- Modify: `app/Views/admin/dashboard.php:58-64`

**Interfaces:**
- Consumes: `DepartmentModel`, `CategoryModel`, `EquipmentModel`, `session()`
- Produces: `AdminController::equipment()` method rendering `admin/equipment/index` with view parameters:
  - `equipmentList`: `array`
  - `categories`: `array`
  - `departmentList`: `array`
  - `selectedDepartmentId`: `int|null`
  - `totalAssets`: `int`
  - `userName`: `string`
  - `userRole`: `string`
  - `search`: `string`
  - `categoryId`: `int|null`
  - `statusFilter`: `string`
  - `conditionFilter`: `string`
  - `pager`: `\CodeIgniter\Pager\Pager`
  - `paginationDetails`: `array`

- [ ] **Step 1: Register route `admin/equipment` in `app/Config/Routes.php`**
  Add `$routes->get('equipment', 'AdminController::equipment');` inside the `admin` route group protected by `admin` filter. Also update `EquipmentController::index()` so that if an admin accesses `/equipment`, it gracefully forwards to `admin/equipment` with query strings.

- [ ] **Step 2: Implement `AdminController::equipment()` method**
  In `app/Controllers/AdminController.php`:
  - Retrieve assigned/requested department.
  - Query total equipment count (unfiltered by status/condition, scoped by department) for the "Total Assets" header badge.
  - Apply search filter across `equipment_name`, `equipment_code`, `brand`, `model`, and `category_name`.
  - Apply category, status, and condition filters.
  - Paginate results (8 items per page) using the `'inventory'` pager group.
  - Return view `admin/equipment/index` with all necessary data.

- [ ] **Step 3: Update sidebar link in `app/Views/admin/dashboard.php`**
  Update the "Equipment Inventory" link from `base_url('equipment')` to `base_url('admin/equipment')`.

- [ ] **Step 4: Verify route via spark command**
  Run: `php spark routes`
  Expected: `GET admin/equipment` handled by `\App\Controllers\AdminController::equipment` with `auth` and `admin` filters.

---

### Task 2: Custom CSS for Admin Equipment Inventory

**Files:**
- Create: `public/css/admin-inventory.css`

**Interfaces:**
- Consumes: CSS variables and base styles from `dashboard.css` and `catalog.css`.
- Produces: Styles for:
  - `.admin-inventory-header`: Flex row with title, asset count pill badge (`.admin-assets-badge`), subtitle, and "+ Add New Equipment" button (`.btn-add-equipment`).
  - `.admin-inventory-card`: Container card with subtle border, shadow, and white background matching reference.
  - `.admin-inventory-toolbar`: Header area inside card featuring the "Equipment Inventory" title with cube icon on left, and search + dropdown filters on right.
  - Category badge color variants (`.badge-cat-computers`, `.badge-cat-av`, `.badge-cat-photo`, `.badge-cat-audio`, `.badge-cat-tools`, `.badge-cat-office`).
  - `.admin-manage-btn`: Action button with wrench icon (`bi-wrench` or `bi-tools`) aligned in the card footer.
  - Responsive media queries for tablet and mobile screens.

- [ ] **Step 1: Write `public/css/admin-inventory.css`**
  Implement the exact styling matching `equipment-inventory-deisgn.png`:
  - Assets count badge: pill shape, light blue background `#eaf2ff`, bold blue text `#2463eb`.
  - Add equipment button: vibrant blue `#2463eb`, rounded, white text with icon.
  - Inventory card toolbar: clean inline alignment of search input, category select, status select, condition select, and filter button.
  - Manage button: subtle styled button/link with wrench icon and hover state.

- [ ] **Step 2: Verify CSS syntax and styles**
  Check that classes don't conflict with existing rules in `catalog.css` or `admin-dashboard.css`.

---

### Task 3: Admin Equipment Inventory View

**Files:**
- Create: `app/Views/admin/equipment/index.php`

**Interfaces:**
- Consumes: View parameters passed from `AdminController::equipment()`.
- Produces: Complete responsive HTML view adhering to CodeIgniter 4 view conventions and Bootstrap 5.

- [ ] **Step 1: Implement Admin Sidebar**
  Reuse the admin sidebar from `app/Views/admin/dashboard.php`:
  - CPC Equipment Tracker brand.
  - "Dashboard" link (`admin`).
  - "Equipment Inventory" link (`admin/equipment`) with `active` class and `aria-current="page"`.
  - "Transactions" link (`admin/reservations#transactions`).
  - "Logout" link (`logout`).

- [ ] **Step 2: Implement Admin Topbar**
  Reuse the admin topbar from `app/Views/admin/dashboard.php`:
  - Global search field pointing to `admin/equipment`.
  - Department dropdown with selected department, department switcher list, and query persistence.
  - Admin profile dropdown with avatar, name, role, and logout.

- [ ] **Step 3: Implement Page Header**
  - Left: Heading "Equipment Management", asset pill badge ("<?= esc($totalAssets) ?> Total Assets"), and subtitle.
  - Right: "+ Add New Equipment" button (`bi-box-seam` / `bi-plus-lg`).

- [ ] **Step 4: Implement Inventory Card & Filter Toolbar**
  - Card container matching `admin-approval-card` styling.
  - Section header: Box icon + "Equipment Inventory".
  - Filter form: Search input, Category select, Status select, Condition select, and filter toggle button.
  - Preserve `department_id` and search filters on change.

- [ ] **Step 5: Implement Cards Grid & Manage Action**
  - 4-column responsive grid matching `catalog.php`.
  - Image thumbnail using `equipment_image_url($item['image'])`.
  - Equipment code, equipment name, brand & model.
  - Category pill badge with dynamic icon and matching pastel color scheme.
  - Card footer with status dot indicator, condition dot indicator, and `Manage` button with wrench icon (`bi-wrench`).

- [ ] **Step 6: Implement Pagination Footer**
  - "Showing <?= $from ?>–<?= $to ?> of <?= $total ?> equipment".
  - Reused `catalog_full` pager links: `<?= $pager->links('inventory', 'catalog_full') ?>`.

---

### Task 4: Integration Verification and Polish

**Files:**
- Test via CLI and browser simulation:
  - Route testing via `php spark routes`.
  - View syntax check via `php -l`.
  - Smoke test verification of controller and view data rendering.

- [ ] **Step 1: Syntax check all modified and created PHP files**
  Run: `php -l app/Controllers/AdminController.php`
  Run: `php -l app/Config/Routes.php`
  Run: `php -l app/Views/admin/equipment/index.php`
  Expected: No syntax errors detected.

- [ ] **Step 2: Execute model smoke tests**
  Run: `php spark test:models`
  Expected: All tests pass.

- [ ] **Step 3: Verify end-to-end rendering and data flow**
  Create a temporary smoke-check script or run spark verification to ensure `AdminController::equipment` instantiates, runs queries, and renders HTML without errors.

