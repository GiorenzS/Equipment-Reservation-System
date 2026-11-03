<?php
/**
 * @var array $departmentList
 * @var array $categories
 * @var int|null $selectedDepartmentId
 * @var string|null $suggestedAssetCode
 */

$formErrors = session()->getFlashdata('errors') ?? [];
$hasErrors = ! empty($formErrors);
$selectedDept = old('department_id', $selectedDepartmentId ?? '');
$selectedCategory = old('category_id', '');
$selectedStatus = old('status', 'AVAILABLE');
$selectedCondition = old('condition_status', 'GOOD');
$codeValue = old('equipment_code', $suggestedAssetCode ?? 'EQ-CPC-001');
?>

<div class="modal fade admin-equipment-modal" id="addNewEquipmentModal" tabindex="-1" aria-labelledby="addNewEquipmentModalLabel" aria-hidden="true" data-auto-open="<?= session()->getFlashdata('openAddModal') ? 'true' : 'false' ?>">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content admin-modal-card">
            <div class="modal-header admin-modal-header">
                <div>
                    <h2 class="modal-title admin-modal-title" id="addNewEquipmentModalLabel">Add New Equipment</h2>
                    <p class="admin-modal-subtitle">Enter technical specifications and assign tracking code</p>
                </div>
                <button type="button" class="btn-close admin-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('admin/equipment/store') ?>" method="post" enctype="multipart/form-data" id="addEquipmentForm" novalidate>
                <?= csrf_field() ?>
                
                <div class="modal-body admin-modal-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show small mb-4 py-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
                            <?= esc(session()->getFlashdata('error')) ?>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Row 1: Equipment Name (Full Width) -->
                    <div class="mb-3">
                        <label for="eq_name" class="form-label admin-form-label">Equipment Name <span class="text-danger"></span></label>
                        <input type="text" class="form-control admin-form-control <?= isset($formErrors['equipment_name']) ? 'is-invalid' : '' ?>" id="eq_name" name="equipment_name" value="<?= esc(old('equipment_name', '')) ?>" placeholder="Sony FX3 Cinema Line Camera" required>
                        <?php if (isset($formErrors['equipment_name'])): ?>
                            <div class="invalid-feedback"><?= esc($formErrors['equipment_name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Row 2: Department / Office & Category (2-Column Row) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="eq_department" class="form-label admin-form-label">Department / Office <span class="text-danger"></span></label>
                            <select class="form-select admin-form-select <?= isset($formErrors['department_id']) ? 'is-invalid' : '' ?>" id="eq_department" name="department_id" required>
                                <option value="">Select Department / Office</option>
                                <?php foreach ($departmentList as $dept): ?>
                                    <option value="<?= (int) $dept['department_id'] ?>" <?= (int) $selectedDept === (int) $dept['department_id'] ? 'selected' : '' ?>>
                                        <?= esc($dept['department_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['department_id'])): ?>
                                <div class="invalid-feedback"><?= esc($formErrors['department_id']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="eq_category" class="form-label admin-form-label">Category <span class="text-danger"></span></label>
                            <select class="form-select admin-form-select <?= isset($formErrors['category_id']) ? 'is-invalid' : '' ?>" id="eq_category" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int) $cat['category_id'] ?>" <?= (int) $selectedCategory === (int) $cat['category_id'] ? 'selected' : '' ?>>
                                        <?= esc($cat['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['category_id'])): ?>
                                <div class="invalid-feedback"><?= esc($formErrors['category_id']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Row 3: Brand & Model (2-Column Row) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="eq_brand" class="form-label admin-form-label">Brand</label>
                            <input type="text" class="form-control admin-form-control <?= isset($formErrors['brand']) ? 'is-invalid' : '' ?>" id="eq_brand" name="brand" value="<?= esc(old('brand', '')) ?>" placeholder="Sony">
                            <?php if (isset($formErrors['brand'])): ?>
                                <div class="invalid-feedback"><?= esc($formErrors['brand']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="eq_model" class="form-label admin-form-label">Model</label>
                            <input type="text" class="form-control admin-form-control <?= isset($formErrors['model']) ? 'is-invalid' : '' ?>" id="eq_model" name="model" value="<?= esc(old('model', '')) ?>" placeholder="ILME-FX3">
                            <?php if (isset($formErrors['model'])): ?>
                                <div class="invalid-feedback"><?= esc($formErrors['model']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Row 4: Serial Number & Tracking / Asset Code (2-Column Row) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="eq_serial" class="form-label admin-form-label">Serial Number</label>
                            <input type="text" class="form-control admin-form-control <?= isset($formErrors['serial_number']) ? 'is-invalid' : '' ?>" id="eq_serial" name="serial_number" value="<?= esc(old('serial_number', '')) ?>" placeholder="SN-884920-X">
                            <?php if (isset($formErrors['serial_number'])): ?>
                                <div class="invalid-feedback"><?= esc($formErrors['serial_number']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="eq_code" class="form-label admin-form-label">Tracking / Asset Code <span class="text-danger"></span></label>
                            <div class="admin-asset-code-wrapper">
                                <input type="text" class="form-control admin-form-control admin-asset-code-input <?= isset($formErrors['equipment_code']) ? 'is-invalid' : '' ?>" id="eq_code" name="equipment_code" value="<?= esc($codeValue) ?>" placeholder="EQ-CPC-009" required>
                                <span class="admin-asset-code-badge" title="Asset tracking identifier"><i class="bi bi-lock-fill" aria-hidden="true"></i></span>
                            </div>
                            <?php if (isset($formErrors['equipment_code'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($formErrors['equipment_code']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Row 5: Initial Status & Condition Status (2-Column Row) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="eq_status" class="form-label admin-form-label">Initial Status</label>
                            <select class="form-select admin-form-select <?= isset($formErrors['status']) ? 'is-invalid' : '' ?>" id="eq_status" name="status">
                                <option value="AVAILABLE" <?= $selectedStatus === 'AVAILABLE' ? 'selected' : '' ?>>Available</option>
                                <option value="RESERVED" <?= $selectedStatus === 'RESERVED' ? 'selected' : '' ?>>Reserved</option>
                                <option value="MAINTENANCE" <?= $selectedStatus === 'MAINTENANCE' ? 'selected' : '' ?>>Maintenance</option>
                            </select>
                            <?php if (isset($formErrors['status'])): ?>
                                <div class="invalid-feedback"><?= esc($formErrors['status']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="eq_condition" class="form-label admin-form-label">Condition Status</label>
                            <select class="form-select admin-form-select <?= isset($formErrors['condition_status']) ? 'is-invalid' : '' ?>" id="eq_condition" name="condition_status">
                                <option value="GOOD" <?= $selectedCondition === 'GOOD' ? 'selected' : '' ?>>Good</option>
                                <option value="FAIR" <?= $selectedCondition === 'FAIR' ? 'selected' : '' ?>>Fair</option>
                                <option value="DAMAGED" <?= $selectedCondition === 'DAMAGED' ? 'selected' : '' ?>>Damaged</option>
                            </select>
                            <?php if (isset($formErrors['condition_status'])): ?>
                                <div class="invalid-feedback"><?= esc($formErrors['condition_status']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Row 6: Equipment Image Drag & Drop Container -->
                    <div class="mb-3">
                        <label class="form-label admin-form-label">Equipment Image <span class="text-muted fw-normal">(PNG, JPG up to 5MB)</span></label>
                        
                        <div class="admin-dropzone" id="equipmentDropzone" role="button" tabindex="0" aria-label="Upload equipment image">
                            <input type="file" name="equipment_image" id="equipmentImageInput" class="d-none" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml">
                            
                            <div class="admin-dropzone-content" id="dropzonePrompt">
                                <div class="admin-dropzone-icon">
                                    <i class="bi bi-camera" aria-hidden="true"></i>
                                </div>
                                <p class="admin-dropzone-title">
                                    <span class="text-primary fw-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="admin-dropzone-sub">SVG, PNG, JPG or WebP (max. 800x800px)</p>
                            </div>

                            <div class="admin-dropzone-preview d-none" id="dropzonePreview">
                                <div class="admin-preview-wrapper">
                                    <img src="" alt="Equipment preview" id="previewImg" class="admin-preview-img">
                                    <div class="admin-preview-meta">
                                        <span class="admin-preview-name" id="previewFileName"></span>
                                        <span class="admin-preview-size text-muted" id="previewFileSize"></span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger admin-preview-remove" id="btnRemoveImage" title="Remove image">
                                        <i class="bi bi-trash3" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($formErrors['equipment_image'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($formErrors['equipment_image']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Row 7: Detailed Description & Accessories Included (Full Width Textarea) -->
                    <div class="mb-2">
                        <label for="eq_description" class="form-label admin-form-label">Detailed Description &amp; Accessories Included</label>
                        <textarea class="form-control admin-form-control <?= isset($formErrors['description']) ? 'is-invalid' : '' ?>" id="eq_description" name="description" rows="3" placeholder="Includes XLR Top Handle Unit, 2x NP-FZ100 Batteries, Dual Charger, SmallRig cage attachment, and hard travel case."><?= esc(old('description', '')) ?></textarea>
                        <?php if (isset($formErrors['description'])): ?>
                            <div class="invalid-feedback"><?= esc($formErrors['description']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal-footer admin-modal-footer">
                    <button type="button" class="btn admin-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn admin-btn-save">
                        <i class="bi bi-check-lg" aria-hidden="true"></i>
                        <span>Save Equipment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

