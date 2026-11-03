# Manage Equipment Modal Dialog Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create the "Manage Equipment" modal dialog matching `designs/manage-equipment-modal-design.png`, with dynamic data population, image replacement with live preview, update handling, delete/decommission handling, and robust server-side validation.

**Architecture:** CodeIgniter 4 MVC architecture. A dedicated partial view `app/Views/admin/equipment/modal_edit.php` is included in `app/Views/admin/equipment/index.php`. Clicking the "Manage" wrench button on any inventory card triggers dynamic population via data attributes / AJAX endpoint `GET admin/equipment/get/(:num)`. Updates submit via `POST admin/equipment/(:num)/update` to `AdminController::updateEquipment($id)`, and decommission/delete requests submit via `POST admin/equipment/(:num)/delete` to `AdminController::deleteEquipment($id)`. Custom styling extends `public/css/admin-inventory.css`.

**Tech Stack:** CodeIgniter 4 (PHP 8+), MySQL, Bootstrap 5.3, Bootstrap Icons 1.11, JavaScript (Vanilla / Fetch API), Custom CSS.

**Spec:** `designs/manage-equipment-modal-design.png`

## Global Constraints

- Tech Stack: CodeIgniter 4, Bootstrap 5, JavaScript, and custom CSS.
- Preserve existing working modules and route conventions without unnecessary rewrites.
- Include CSRF protection (`<?= csrf_field() ?>`) and multipart support (`enctype="multipart/form-data"`).
- Exact visual alignment with `designs/manage-equipment-modal-design.png`:
  - Split image section: thumbnail preview of current image on the left, dotted replacement dropzone with cloud upload icon on the right.
  - Section 1: "Basic Asset Information" (Equipment Name, Asset Code with lock icon badge, Serial Number, Department, Category, Brand, Model).
  - Section 2: "Status & Condition Calibration" styled container card with Operational Status ("Available", "Reserved", "Maintenance", "Checked Out") and Condition Assessment ("Good", "Fair", "Damaged").
  - Full-width textarea for "Condition Remarks / Equipment Notes".
  - Footer with outlined red "Delete / Decommission Asset" on left, and "Cancel" + "✓ Update Equipment" on right.
- Delete protection: do not allow deletion of equipment that is currently in use or has active reservations.

## Review Focus

1. **Dynamic Data Population:** Clicking "Manage" on any equipment item correctly populates all form fields (Name, Code, Serial, Dept, Category, Brand, Model, Status, Condition, Description) and loads the current thumbnail in the left preview box.
2. **Image Replacement & File Cleanup:** When a new replacement image is uploaded, it previews in real-time. On update, the old local image file is unlinked (if one existed in `public/uploads/equipment/`) and the new image is saved.
3. **Status Mapping:** Map "Checked Out" to `IN_USE`, "Available" to `AVAILABLE`, "Reserved" to `RESERVED`, and "Maintenance" to `MAINTENANCE`.
4. **Delete / Decommission Safety:** Clicking "Delete / Decommission Asset" triggers a confirmation dialog. Active checkouts or approved reservations block deletion with a clear flash warning.
5. **Form Validation & Unique Constraints:** Asset Code is readonly or validated ignoring the current equipment ID; Serial Number uniqueness validation ignores the current equipment ID.

---

### Task 1: Routes and AdminController Endpoints for Get, Update, and Delete

**Files:**
- Modify: `app/Config/Routes.php`
- Modify: `app/Controllers/AdminController.php`

**Interfaces:**
- `GET admin/equipment/get/(:num)` -> `AdminController::getEquipment($id)`: returns JSON payload of equipment data for AJAX population.
- `POST admin/equipment/(:num)/update` -> `AdminController::updateEquipment($id)`:
  - Validates inputs (`equipment_name`, `department_id`, `category`, `serial_number`, `status`, `condition_status`, `description`, `equipment_image`).
  - Resolves category (matching existing category or creating new if needed).
  - Handles image upload replacement: saves new file in `public/uploads/equipment/` and deletes old file if exists.
  - Updates `EquipmentModel` record.
  - Redirects back with success flash message.
- `POST admin/equipment/(:num)/delete` -> `AdminController::deleteEquipment($id)`:
  - Checks if item has active borrowings (`borrow_transactions` / `borrow_items`) or reservations (`reservations`).
  - If safe, deletes the equipment and removes image if local.
  - Redirects back with success flash message.

- [ ] **Step 1: Register routes in `app/Config/Routes.php`**
- [ ] **Step 2: Implement `AdminController::getEquipment($id)` returning JSON**
- [ ] **Step 3: Implement `AdminController::updateEquipment($id)` with validation and image replacement**
- [ ] **Step 4: Implement `AdminController::deleteEquipment($id)` with safety checks**
- [ ] **Step 5: Verify routes via `php spark routes` and test syntax with `php -l`**

---

### Task 2: Build Manage Equipment Modal View (`app/Views/admin/equipment/modal_edit.php`)

**Files:**
- Create: `app/Views/admin/equipment/modal_edit.php`
- Modify: `app/Views/admin/equipment/index.php`

**UI Layout & Components:**
- Modal ID: `#manageEquipmentModal`
- Header:
  - Title: "Manage Equipment"
  - Subtitle: "Edit asset tracking info, re-assign availability status, or update condition report."
  - Close button `btn-close`
- Image Preview & Replacement Section:
  - Container with split 2-column layout:
    - Left: Current image preview wrapper (`#manageCurrentImgContainer`) with `<img>` tag and placeholder fallback.
    - Right: Dotted dropzone (`#manageDropzone`) with cloud upload icon (`bi bi-cloud-arrow-up`), "Click to upload or drag and drop replacement", and supporting formats note. Hidden input `<input type="file" name="equipment_image" id="manageImageInput">`.
- Section 1: "Basic Asset Information"
  - Equipment Name: Full width text input (`#manage_eq_name`).
  - Row (2 cols): Asset Code with lock icon badge (`#manage_eq_code`, readonly / tracked) & Serial Number (`#manage_eq_serial`).
  - Row (2 cols): Department dropdown (`#manage_eq_department`) & Category input/dropdown (`#manage_eq_category`).
  - Row (2 cols): Brand (`#manage_eq_brand`) & Model (`#manage_eq_model`).
- Section 2: "Status & Condition Calibration"
  - Contained in card `.admin-calibration-card`:
    - Title: "Status & Condition Calibration"
    - Row (2 cols): Operational Status (`#manage_eq_status`) with options Available, Reserved, Maintenance, Checked Out; and Condition Assessment (`#manage_eq_condition`) with options Good, Fair, Damaged.
- Condition Remarks / Equipment Notes:
  - Full width textarea (`#manage_eq_description`).
- Modal Footer:
  - Left: `<button type="button" class="btn btn-outline-danger admin-btn-delete" id="btnTriggerDelete"><i class="bi bi-trash3 me-1"></i>Delete / Decommission Asset</button>`.
  - Right: `<button type="button" class="btn admin-btn-cancel" data-bs-dismiss="modal">Cancel</button>` and `<button type="submit" class="btn admin-btn-save"><i class="bi bi-check-lg me-1"></i>Update Equipment</button>`.
- Hidden Delete Form: `<form id="deleteEquipmentForm" method="post" action="">...` with CSRF protection for decommissioning the asset safely.

- [ ] **Step 1: Create `app/Views/admin/equipment/modal_edit.php`**
- [ ] **Step 2: Update "Manage" button in `app/Views/admin/equipment/index.php` with `data-bs-toggle="modal"` and equipment data attributes**
- [ ] **Step 3: Include `modal_edit.php` in `app/Views/admin/equipment/index.php`**

---

### Task 3: CSS Styling for Manage Modal

**Files:**
- Modify: `public/css/admin-inventory.css`

**Styling Details:**
- Image split container `.admin-manage-image-split`:
  - Left thumbnail box: fixed width ~160px, height ~100px, border-radius 14px, border `1px solid #e2e8f0`, dark subtle background, `object-fit: cover`.
  - Right dropzone box: flex-1, border `1.5px dashed #cbd5e1`, border-radius 14px, background `#f8faff`, cloud icon badge.
- Calibration card `.admin-calibration-card`:
  - Border `1px solid #e2e8f0`, background `#f8faff`, border-radius 16px, padding 18px 20px.
- Section titles:
  - Font size ~1.05rem, font-weight 700, color `#101828`.
- Delete button:
  - Outlined red `#ef4444`, border-radius 11px, padding 9px 18px, font-weight 600, smooth hover transition.

- [ ] **Step 1: Add modal image split, calibration card, and footer styles to `public/css/admin-inventory.css`**

---

### Task 4: JavaScript Dynamic Population, Live Preview, and Delete Confirmation

**Files:**
- Modify: `app/Views/admin/equipment/index.php` (or dedicated script section)

**Functionality:**
- Manage button click listener:
  - Reads data attributes from clicked button (or performs fetch to `/admin/equipment/get/$id`).
  - Sets form action to `admin/equipment/${id}/update`.
  - Sets delete form action to `admin/equipment/${id}/delete`.
  - Populates all fields: name, code, serial, department, category, brand, model, status, condition, notes.
  - Updates current thumbnail preview with `data-image` URL.
  - Resets replacement file input and dropzone preview state.
- Replacement dropzone event listeners:
  - Handles drag-and-drop and file input selection.
  - Renders live preview in replacement dropzone and/or updates left thumbnail.
- Delete button listener:
  - Displays confirmation dialog ("Are you sure you want to decommission/delete this equipment asset? This action cannot be undone.").
  - Submits the delete form upon confirmation.

- [ ] **Step 1: Implement modal population and image preview scripts**
- [ ] **Step 2: Implement delete confirmation flow**

---

### Task 5: Testing and End-to-End Verification

**Verification Steps:**
- [ ] **Step 1: Check syntax (`php -l`) on all touched files**
- [ ] **Step 2: Verify modal opens with exact values when clicking "Manage" on equipment cards**
- [ ] **Step 3: Test editing technical details, changing status to "Maintenance" or "Checked Out", and changing condition**
- [ ] **Step 4: Test replacing equipment image and verify old image file cleanup**
- [ ] **Step 5: Test Delete / Decommission confirmation and safety guard against active borrowings**

