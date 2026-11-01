# Admin Transactions View Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the unified admin transactions view by integrating the functionality from `app/Views/admin/reservations/index.php` into the modern admin layout with sidebar, topbar, department selector, profile dropdown, and styled workflow cards (Pending Approvals, Checkout Queue, and Active Checkouts & Returns).

**Architecture:** CodeIgniter 4 MVC pattern. The route `admin/transactions` (with `admin/reservations` as backwards-compatible alias) is protected by `auth` and `admin` filters and handled by `AdminController::transactions()`. The controller handles department scoping, user search query, and fetches pending reservations, approved reservations awaiting checkout, and active checkouts. It renders `app/Views/admin/transactions/index.php` (or updated `app/Views/admin/reservations/index.php`) styled with Bootstrap 5, `dashboard.css`, `catalog.css`, and a dedicated `admin-transactions.css`.

**Tech Stack:** CodeIgniter 4, PHP 8+, Bootstrap 5.3, Bootstrap Icons 1.11, Custom CSS.

**Spec & Reference:** Reuses layout and styling conventions from `admin/dashboard.php`, `admin/equipment/index.php`, and workflows from `app/Views/admin/reservations/index.php`.

## Global Constraints

- Tech Stack: CodeIgniter 4, Bootstrap 5, and custom CSS.
- Reuse sidebar, topbar, department dropdown, and profile dropdown ("System Admin") from admin dashboard and inventory views.
- Active sidebar navigation link must highlight **Transactions**.
- Retain all working functionality from `app/Views/admin/reservations/index.php`:
  - Reservation approval and rejection (`admin/reservations/{id}/status/{status}`)
  - Equipment checkout (`admin/reservations/{id}/checkout`)
  - Equipment return with condition inspection (`admin/transactions/{id}/return`)
- Support department scoping and search query across user name, ID number, and equipment name/code.
- Preserve flash message alerts for success and error states.

## Review Focus

1. **Working Module Integrity:** All existing POST actions (Approve, Reject, Checkout, Return) continue to operate without breaking CSRF tokens or redirect URLs.
2. **Department Scoping & Search:** Switching department or entering search terms filters all three queues (Pending, Checkout, Return) appropriately.
3. **Sidebar & Topbar Consistency:** The admin sidebar has "Transactions" highlighted as active; topbar includes global search, department switcher, and "System Admin" profile dropdown.
4. **Checkout Availability State:** Equipment that is not in `AVAILABLE` state correctly indicates unavailability and prevents checkout.
5. **Return Flow Usability:** Condition input (`GOOD`, `FAIR`, `DAMAGED`) is required and cleanly formatted with the Return button.

---

### Task 1: Controller and Route Setup for Admin Transactions

**Files:**
- Modify: `app/Config/Routes.php`
- Modify: `app/Controllers/AdminController.php`
- Modify: `app/Views/admin/dashboard.php`
- Modify: `app/Views/admin/equipment/index.php`

**Interfaces:**
- Consumes: `DepartmentModel`, `ReservationModel`, `BorrowTransactionModel`, `EquipmentModel`, `BorrowItemModel`, `session()`
- Produces: `AdminController::transactions()` method rendering the transactions view with:
  - `departmentList`: `array`
  - `selectedDepartmentId`: `int|null`
  - `userName`: `string`
  - `userRole`: `'System Admin'`
  - `search`: `string`
  - `reservations`: `array` (pending approvals)
  - `approvedReservations`: `array` (awaiting checkout)
  - `transactions`: `array` (active checkouts awaiting return)
  - `counts`: `array{pending: int, checkout: int, active: int}`

- [ ] **Step 1: Register route `admin/transactions` in `app/Config/Routes.php`**
  Add `$routes->get('transactions', 'AdminController::transactions');` and ensure `admin/reservations` routes to the same handler.

- [ ] **Step 2: Implement `AdminController::transactions()` method**
  In `app/Controllers/AdminController.php`:
  - Handle department scoping (`department_id`).
  - Handle search query across borrower name, ID number, equipment name, and equipment code.
  - Query pending reservations (`status = 'PENDING'`).
  - Query approved reservations awaiting checkout (`status = 'APPROVED'`).
  - Query active checkouts awaiting return (`status = 'CHECKED_OUT'`).
  - Compute counts for tab badges / summary statistics.
  - Set `'userRole' => 'System Admin'`.

- [ ] **Step 3: Update sidebar navigation links**
  In `admin/dashboard.php` and `admin/equipment/index.php`, update the "Transactions" link to `base_url('admin/transactions')`.

- [ ] **Step 4: Verify route via spark command**
  Run: `php spark routes | Select-String "admin/transactions"`
  Expected: `GET admin/transactions` mapped to `AdminController::transactions`.

---

### Task 2: Custom CSS for Admin Transactions

**Files:**
- Create: `public/css/admin-transactions.css`

**Interfaces:**
- Consumes: Base styles from `dashboard.css`, `catalog.css`, and `admin-dashboard.css`.
- Produces: Styles for:
  - `.admin-transactions-banner`: Top header banner card with icon, title, description, and summary pill badges.
  - `.admin-tabs-nav`: Tab navigation to toggle between "All Queues", "Pending Approvals", "Awaiting Checkout", and "Active Checkouts & Returns".
  - `.admin-tx-card`: Card container with white background, subtle border, rounded corners, and elevated headers.
  - `.admin-tx-table`: Clean table styling with avatar initials, requester badge, equipment code meta, and schedule formatting.
  - Condition dropdown and return action button layout.
  - Responsive styles for mobile and tablet screens.

- [ ] **Step 1: Write `public/css/admin-transactions.css`**
  Implement polished styling consistent with `admin-dashboard.css` and `admin-inventory.css`.

- [ ] **Step 2: Verify CSS compatibility**
  Ensure no class collisions with existing stylesheets.

---

### Task 3: Admin Transactions View

**Files:**
- Create: `app/Views/admin/transactions/index.php`
- Modify: `app/Views/admin/reservations/index.php` (render or redirect to new view so legacy links still function)

**Interfaces:**
- Consumes: View variables passed from `AdminController::transactions()`.
- Produces: Complete responsive HTML view with admin sidebar, topbar, and 3 integrated transaction modules.

- [ ] **Step 1: Setup Admin Sidebar and Topbar**
  - Dark floating sidebar with "Transactions" highlighted as active.
  - Topbar with global search input, department dropdown, and "System Admin" profile dropdown.

- [ ] **Step 2: Add Header Banner Card**
  - Banner card styled with icon (`bi-arrow-left-right`), title "Transaction Management", subtitle, and queue counts badge pills (`Pending: X`, `Checkout: Y`, `Active: Z`).

- [ ] **Step 3: Implement Queue Filter Tabs & Search**
  - Section tabs allowing the admin to view:
    - All Sections
    - Pending Approvals (`<?= count($reservations) ?>`)
    - Awaiting Checkout (`<?= count($approvedReservations) ?>`)
    - Active Checkouts (`<?= count($transactions) ?>`)

- [ ] **Step 4: Implement Pending Approval Queue Card**
  - Form table with requester details (name, role, ID), requested equipment, reservation period, purpose, status pill, and Approve / Reject buttons with CSRF tokens.

- [ ] **Step 5: Implement Approved Checkout Queue Card**
  - Form table with user info, equipment info, reservation schedule, purpose, equipment availability check, and "Check Out" button with CSRF token.

- [ ] **Step 6: Implement Active Checkouts & Returns Card**
  - Form table with user info, equipment info, borrowed date, expected return date (with overdue warning pill if overdue), condition select input (`GOOD`, `FAIR`, `DAMAGED`), and "Return" button with CSRF token.

---

### Task 4: Verification and Smoke Testing

**Files:**
- Test via CLI and browser simulation:
  - Route validation with `php spark routes`.
  - Syntax check with `php -l`.
  - Smoke test verification of controller data queries and HTML rendering.

- [ ] **Step 1: Syntax check all modified and created PHP files**
  Run: `php -l app/Controllers/AdminController.php`
  Run: `php -l app/Config/Routes.php`
  Run: `php -l app/Views/admin/transactions/index.php`

- [ ] **Step 2: Run model smoke test**
  Run: `php spark test:models`

- [ ] **Step 3: Verify end-to-end rendering and workflow actions**
  Ensure controller loads, queries data across all 3 tables with department filtering, and renders HTML without errors.

