<?php
/**
 * @var array $departmentList
 * @var array $categories
 */
?>

<div class="modal fade admin-equipment-modal" id="manageEquipmentModal" tabindex="-1" aria-labelledby="manageEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content admin-modal-card">
            <div class="modal-header admin-modal-header">
                <div>
                    <h2 class="modal-title admin-modal-title" id="manageEquipmentModalLabel">Manage Equipment</h2>
                    <p class="admin-modal-subtitle">Edit asset tracking info, re-assign availability status, or update condition report.</p>
                </div>
                <button type="button" class="btn-close admin-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" method="post" enctype="multipart/form-data" id="manageEquipmentForm" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="equipment_id" id="manage_equipment_id" value="">
                
                <div class="modal-body admin-modal-body">
                    <!-- Image Preview & Replacement Section (Split Container) -->
                    <div class="admin-image-split-container mb-4">
                        <div class="admin-current-thumb-box">
                            <img src="" alt="Current equipment preview" id="manageCurrentImg" class="admin-current-thumb-img">
                            <span class="admin-current-thumb-label">Current Photo</span>
                        </div>
                        
                        <div class="admin-replace-dropzone" id="manageDropzone" role="button" tabindex="0" aria-label="Upload replacement equipment image">
                            <input type="file" name="equipment_image" id="manageEquipmentImageInput" class="d-none" accept="image/png, image/jpeg, image/jpg, image/webp">
                            
                            <div class="admin-replace-prompt" id="manageDropzonePrompt">
                                <div class="admin-replace-icon">
                                    <i class="bi bi-cloud-arrow-up" aria-hidden="true"></i>
                                </div>
                                <p class="admin-replace-title">
                                    <span class="text-primary fw-semibold">Click to upload</span> or drag and drop replacement
                                </p>
                                <p class="admin-replace-sub">PNG, JPG, or WEBP up to 5MB (1600x1200 recommended)</p>
                            </div>

                            <div class="admin-replace-preview d-none" id="manageDropzonePreview">
                                <div class="admin-preview-wrapper">
                                    <img src="" alt="Replacement preview" id="managePreviewImg" class="admin-preview-img">
                                    <div class="admin-preview-meta">
                                        <span class="admin-preview-name" id="managePreviewFileName"></span>
                                        <span class="admin-preview-size text-muted" id="managePreviewFileSize"></span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger admin-preview-remove" id="btnRemoveManageImage" title="Clear replacement">
                                        <i class="bi bi-trash3" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Section 1: Basic Asset Information -->
                    <div class="admin-form-section mb-4">
                        <h3 class="admin-section-heading">Basic Asset Information</h3>

                        <!-- Equipment Name (Full Width) -->
                        <div class="mb-3">
                            <label for="manage_eq_name" class="form-label admin-form-label">Equipment Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control admin-form-control" id="manage_eq_name" name="equipment_name" placeholder="Dell Latitude 5520 Laptop" required>
                        </div>

                        <!-- Asset Code & Serial Number (2-Column Row) -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="manage_eq_code" class="form-label admin-form-label">Asset Code / Tracking ID</label>
                                <div class="admin-asset-code-wrapper">
                                    <input type="text" class="form-control admin-form-control admin-asset-code-input" id="manage_eq_code" name="equipment_code" readonly>
                                    <span class="admin-asset-code-badge" title="Asset tracking identifier is locked"><i class="bi bi-lock-fill" aria-hidden="true"></i></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="manage_eq_serial" class="form-label admin-form-label">Serial Number</label>
                                <input type="text" class="form-control admin-form-control" id="manage_eq_serial" name="serial_number" placeholder="SN-DELL-883921-X">
                            </div>
                        </div>

                        <!-- Department & Category (2-Column Row) -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="manage_eq_department" class="form-label admin-form-label">Department / Custodian Office <span class="text-danger">*</span></label>
                                <select class="form-select admin-form-select" id="manage_eq_department" name="department_id" required>
                                    <option value="">Select Department / Office</option>
                                    <?php foreach ($departmentList as $dept): ?>
                                        <option value="<?= (int) $dept['department_id'] ?>">
                                            <?= esc($dept['department_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="manage_eq_category" class="form-label admin-form-label">Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control admin-form-control" id="manage_eq_category" name="category" placeholder="Computers / Laptops" list="manageCategoryOptions" required>
                                <datalist id="manageCategoryOptions">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= esc($cat['category_name']) ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <!-- Brand & Model (2-Column Row) -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="manage_eq_brand" class="form-label admin-form-label">Brand / Manufacturer</label>
                                <input type="text" class="form-control admin-form-control" id="manage_eq_brand" name="brand" placeholder="Dell">
                            </div>

                            <div class="col-md-6">
                                <label for="manage_eq_model" class="form-label admin-form-label">Model</label>
                                <input type="text" class="form-control admin-form-control" id="manage_eq_model" name="model" placeholder="Latitude 5520">
                            </div>
                        </div>
                    </div>

                    <!-- Form Section 2: Status & Condition Calibration Card -->
                    <div class="admin-calibration-card mb-3">
                        <h3 class="admin-section-heading">Status &amp; Condition Calibration</h3>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="manage_eq_status" class="form-label admin-form-label">Operational Status</label>
                                <select class="form-select admin-form-select" id="manage_eq_status" name="status">
                                    <option value="AVAILABLE">Available</option>
                                    <option value="RESERVED">Reserved</option>
                                    <option value="MAINTENANCE">Maintenance</option>
                                    <option value="IN_USE">Checked Out</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="manage_eq_condition" class="form-label admin-form-label">Condition Assessment</label>
                                <select class="form-select admin-form-select" id="manage_eq_condition" name="condition_status">
                                    <option value="GOOD">Good</option>
                                    <option value="FAIR">Fair</option>
                                    <option value="DAMAGED">Damaged</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Condition Remarks / Equipment Notes (Full Width Textarea) -->
                    <div class="mb-2">
                        <label for="manage_eq_description" class="form-label admin-form-label">Condition Remarks / Equipment Notes</label>
                        <textarea class="form-control admin-form-control" id="manage_eq_description" name="description" rows="3" placeholder="Condition remarks, accessories, or equipment notes..."></textarea>
                    </div>
                </div>

                <div class="modal-footer admin-modal-footer justify-content-between">
                    <div>
                        <button type="button" class="btn btn-outline-danger admin-btn-delete" id="btnManageDelete">
                            <i class="bi bi-trash3" aria-hidden="true"></i>
                            <span>Delete / Decommission Asset</span>
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn admin-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn admin-btn-save">
                            <i class="bi bi-check-lg" aria-hidden="true"></i>
                            <span>Update Equipment</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Hidden Delete Form -->
            <form id="manageDeleteForm" method="post" action="" class="d-none">
                <?= csrf_field() ?>
            </form>
        </div>
    </div>
</div>

