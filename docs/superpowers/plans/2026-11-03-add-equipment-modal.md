# Add New Equipment Modal Dialog Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create the "Add New Equipment" modal dialog matching the layout, fields, and visual styling from `designs/add-equipment-modal-design.png`, connected to CodeIgniter 4 backend processing (`AdminController::storeEquipment`) with CSRF protection, file upload handling, and validation.

**Architecture:** CodeIgniter 4 MVC pattern. The modal is constructed in a dedicated view component `app/Views/admin/equipment/modal_add.php` and rendered within `app/Views/admin/equipment/index.php`. Form submissions are routed via `POST admin/equipment/store` to `AdminController::storeEquipment`, which validates inputs, processes uploaded image files into `public/uploads/equipment/`, inserts the asset into `EquipmentModel`, and redirects with flash feedback messages. Custom styling is added to `public/css/admin-inventory.css` and interactive JavaScript handles drag-and-drop file upload with live preview.

**Tech Stack:** CodeIgniter 4 (PHP 8+), MySQL, Bootstrap 5.3, Bootstrap Icons 1.11, JavaScript (Vanilla), Custom CSS.

**Spec:** `designs/add-equipment-modal-design.png`

## Global Constraints

- Tech Stack: CodeIgniter 4, Bootstrap 5, JavaScript, and custom CSS.
- Preserve existing working modules and route conventions without unnecessary rewrites.
- Include CSRF protection with `<?= csrf_field() ?>`.
- Form must support file uploads with `enctype="multipart/form-data"`.
- Validate required fields: Equipment Name, Department / Office, Category, Tracking / Asset Code.
- Upload images directly into `public/uploads/equipment/` with a unique, sanitized filename.
- Insert record into `EquipmentModel` and return flash message upon redirection.
- Accurately reproduce the layout, typography, input styling, lock icon on asset code, drag-and-drop image container, and button styling from `designs/add-equipment-modal-design.png`.

## Review Focus

1. **Database Schema & Enums:** MySQL `equipment` table enum columns for `status` and `condition_status` must support `MAINTENANCE` and `EXCELLENT` as specified in the mockup without SQL truncation errors.
2. **Form Validation & Error Handling:** When validation fails, redirect back with inputs intact (`old()`) and display validation feedback in the modal.
3. **Image Upload & Live Preview:** Drag-and-drop and file input selection render an immediate visual image preview inside the upload zone, with an easy clear/remove option.
4. **Tracking / Asset Code Generation:** Default pre-filled or auto-suggested unique asset code (e.g., `EQ-CPC-009` or next sequential identifier) with the lock icon badge matching the visual design.
5. **Department & Category Population:** Dropdown lists dynamically populated from `$departmentList` and `$categories`, defaulting to the currently selected department.

---

### Task 1: Database Migration and Model Enum Validation Updates

**Files:**
- Create: `app/Database/Migrations/2026-11-03-000001_AddMaintenanceAndExcellentToEquipmentEnums.php`
- Modify: `app/Models/EquipmentModel.php:33-45`

**Details:**
- Update `equipment.status` column to `ENUM('AVAILABLE','IN_USE','RESERVED','MAINTENANCE') NOT NULL DEFAULT 'AVAILABLE'`.
- Update `equipment.condition_status` column to `ENUM('EXCELLENT','GOOD','FAIR','DAMAGED') NOT NULL DEFAULT 'GOOD'`.
- Update `EquipmentModel::$validationRules` to allow `MAINTENANCE` in `status` and `EXCELLENT` in `condition_status`.

- [ ] **Step 1: Create migration to alter enum definitions**
  Run `php spark make:migration AddMaintenanceAndExcellentToEquipmentEnums` or create file directly modifying the column enums.
- [ ] **Step 2: Apply migration with `php spark migrate`**
  Verify table schema reflects updated enums for `status` and `condition_status`.
- [ ] **Step 3: Update validation rules in `EquipmentModel.php`**
  Add `'status' => 'permit_empty|in_list[AVAILABLE,IN_USE,RESERVED,MAINTENANCE]'` and `'condition_status' => 'permit_empty|in_list[EXCELLENT,GOOD,FAIR,DAMAGED]'`.

---

### Task 2: Route Registration and Admin Controller Store Equipment Action

**Files:**
- Modify: `app/Config/Routes.php:32-39`
- Modify: `app/Controllers/AdminController.php`

**Interfaces:**
- Route: `POST admin/equipment/store` -> `\App\Controllers\AdminController::storeEquipment`
- Handled in `AdminController`:
  - Enforce CSRF protection and admin session authorization.
  - Validate required fields:
    - `equipment_name`: `required|min_length[2]|max_length[100]`
    - `department_id`: `required|is_natural_no_zero`
    - `category_id`: `required|is_natural_no_zero`
    - `equipment_code`: `required|max_length[50]|is_unique[equipment.equipment_code]`
    - `brand`: `permit_empty|max_length[100]`
    - `model`: `permit_empty|max_length[100]`
    - `serial_number`: `permit_empty|max_length[100]|is_unique[equipment.serial_number]`
    - `status`: `permit_empty|in_list[AVAILABLE,RESERVED,MAINTENANCE]`
    - `condition_status`: `permit_empty|in_list[EXCELLENT,GOOD,FAIR,DAMAGED]`
    - `description`: `permit_empty|max_length[1000]`
    - `equipment_image`: `permit_empty|uploaded[equipment_image]|is_image[equipment_image]|max_size[equipment_image,5120]`
  - Process uploaded file: generate random/unique name, move directly to `FCPATH . 'uploads/equipment/'`.
  - Store relative image name in `EquipmentModel`.
  - Redirect to `admin/equipment` with flash `success` or `error` message.
  - In `AdminController::equipment()`, pass a suggested next equipment code (e.g. `EQ-CPC-009` or format matching existing sequence) for the modal input.

- [ ] **Step 1: Register POST route in `app/Config/Routes.php`**
- [ ] **Step 2: Implement `storeEquipment` method in `app/Controllers/AdminController.php`**
- [ ] **Step 3: Update `AdminController::equipment` to pass suggested tracking code and support modal error re-opening**
- [ ] **Step 4: Verify route via `php spark routes`**

---

### Task 3: Create Add Equipment Modal View (`app/Views/admin/equipment/modal_add.php`)

**Files:**
- Create: `app/Views/admin/equipment/modal_add.php`
- Modify: `app/Views/admin/equipment/index.php:256-276`

**UI Layout & Fields:**
- Modal ID: `#addNewEquipmentModal` (matches existing button `data-bs-target="#addNewEquipmentModal"`).
- Header:
  - Title: "Add New Equipment"
  - Subtitle: "Enter technical specifications and assign tracking code"
  - Close button `btn-close`
- Form:
  - `action="<?= base_url('admin/equipment/store') ?>" method="post" enctype="multipart/form-data"`
  - `<?= csrf_field() ?>`
  - Equipment Name: Full width text input with placeholder `Sony FX3 Cinema Line Camera`.
  - Department / Office * & Category (2-Column Row):
    - Department select populated from `$departmentList`, pre-selecting current `$selectedDepartmentId`.
    - Category select populated from `$categories`.
  - Brand & Model (2-Column Row):
    - Brand input (`Sony`).
    - Model input (`ILME-FX3`).
  - Serial Number & Tracking / Asset Code (2-Column Row):
    - Serial Number input (`SN-884920-X`).
    - Tracking / Asset Code input with lock icon badge (`EQ-CPC-009` or suggested code).
  - Initial Status & Condition Status (2-Column Row):
    - Initial Status dropdown: Available (`AVAILABLE`), Reserved (`RESERVED`), Maintenance (`MAINTENANCE`).
    - Condition Status dropdown: Excellent (`EXCELLENT`), Good (`GOOD`), Fair (`FAIR`), Damaged (`DAMAGED`).
  - Equipment Image Drag & Drop Container:
    - Dashed border container.
    - Camera icon inside rounded badge.
    - "Click to upload or drag and drop" & "SVG, PNG, JPG or WebP (max. 800x800px)".
    - Hidden `<input type="file" name="equipment_image" id="equipment_image" accept="image/*">`.
    - Live image preview element with remove button.
  - Detailed Description & Accessories Included:
    - Textarea with placeholder "Includes XLR Top Handle Unit, 2x NP-FZ100 Batteries, Dual Charger, SmallRig cage attachment, and hard travel case.".
- Footer:
  - "Cancel" button (`btn-outline-secondary` / modal dismiss).
  - "✓ Save Equipment" primary blue submit button (`btn btn-primary`).

- [ ] **Step 1: Build `app/Views/admin/equipment/modal_add.php` with all required elements**
- [ ] **Step 2: Replace placeholder in `app/Views/admin/equipment/index.php` with `<?= view('admin/equipment/modal_add', [...]) ?>`**

---

### Task 4: CSS Styling and JavaScript Interactivity

**Files:**
- Modify: `public/css/admin-inventory.css`
- Add JS script block in `app/Views/admin/equipment/modal_add.php` or `app/Views/admin/equipment/index.php`

**Styling Details:**
- Modal sizing: `modal-dialog-centered`, custom width ~680px-720px.
- Subtle modern inputs: `#f8faff` background, `#dbeafe` border, soft blue outline on focus.
- Asset code lock badge: positioned inside or styled as an input group with `#3b82f6` lock icon.
- Upload drop zone: dashed border `#cbd5e1`, hover highlight `#bfdbfe`, camera icon in `#e8f1ff` circle.
- Buttons: Cancel outlined pill-rounded button; Save Equipment solid blue button `#2463eb` with check icon.
- JavaScript:
  - Drag and drop handlers (`dragover`, `dragleave`, `drop`).
  - File input change handler with `FileReader` to render preview thumbnail.
  - Remove preview button that resets the file input.
  - Auto-open modal if validation errors or session flash errors occur on submission.

- [ ] **Step 1: Add modal CSS rules to `public/css/admin-inventory.css`**
- [ ] **Step 2: Implement JavaScript upload preview, drag-and-drop, and error handling**
- [ ] **Step 3: Test modal open, input styling, and responsiveness**

---

### Task 5: End-to-End Verification and Testing

**Verification Steps:**
- [ ] **Step 1: Check PHP syntax on modified and created files (`php -l`)**
- [ ] **Step 2: Test modal launch by clicking "+ Add New Equipment" button**
- [ ] **Step 3: Test validation errors (empty required fields, duplicate asset code)**
- [ ] **Step 4: Test successful equipment creation with an image upload**
- [ ] **Step 5: Verify new equipment record appears in the inventory grid and uploads directory**

