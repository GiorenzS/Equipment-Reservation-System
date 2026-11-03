<?php
/**
 * @var array $equipmentList
 * @var array $categories
 * @var array $departmentList
 * @var int|null $selectedDepartmentId
 * @var int $totalAssets
 * @var string $userName
 * @var string $userRole
 * @var string $search
 * @var int|null $categoryId
 * @var string $statusFilter
 * @var string $conditionFilter
 * @var \CodeIgniter\Pager\Pager $pager
 * @var array $paginationDetails
 * @var string|null $suggestedAssetCode
 */

$selectedDepartmentName = 'Select Department';
foreach ($departmentList as $department) {
    if ((int) $department['department_id'] === (int) $selectedDepartmentId) {
        $selectedDepartmentName = $department['department_name'];
        break;
    }
}

$total = (int) ($paginationDetails['total'] ?? 0);
$currentPage = (int) ($paginationDetails['currentPage'] ?? 1);
$perPage = (int) ($paginationDetails['perPage'] ?? 8);
$from = $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
$to = min($currentPage * $perPage, $total);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Inventory - CPC Equipment Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="<?= base_url('css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/catalog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/admin-dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/admin-inventory.css') ?>" rel="stylesheet">
</head>
<body class="dashboard-page admin-dashboard-page admin-inventory-page">
    <aside class="dashboard-sidebar">
        <a href="<?= base_url('admin') ?>" class="dashboard-brand">
            <span class="dashboard-brand-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
            <span><strong>CPC</strong><small>Equipment Reservation</small></span>
        </a>
        <nav class="dashboard-nav" aria-label="Admin navigation">
            <a href="<?= base_url('admin') ?>" class="dashboard-nav-link"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span>Dashboard</span></a>
            <a href="<?= base_url('admin/equipment') ?>" class="dashboard-nav-link active" aria-current="page"><i class="bi bi-archive" aria-hidden="true"></i><span>Equipment Inventory</span></a>
            <a href="<?= base_url('admin/transactions') ?>" class="dashboard-nav-link"><i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Transactions</span></a>
        </nav>
        <a href="<?= base_url('logout') ?>" class="dashboard-logout"><i class="bi bi-box-arrow-left" aria-hidden="true"></i><span>Logout</span></a>
    </aside>

    <main class="dashboard-main admin-dashboard-main">
        <header class="dashboard-topbar">
            <form class="dashboard-global-search" method="get" action="<?= base_url('admin/equipment') ?>">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" name="search" value="<?= esc($search) ?>" placeholder="Search equipment by name, code, brand or category..." aria-label="Search equipment by name, code, brand or category">
                <?php if ($selectedDepartmentId !== null): ?><input type="hidden" name="department_id" value="<?= (int) $selectedDepartmentId ?>"><?php endif; ?>
                <?php if ($categoryId !== null): ?><input type="hidden" name="category_id" value="<?= (int) $categoryId ?>"><?php endif; ?>
                <?php if ($statusFilter !== ''): ?><input type="hidden" name="status" value="<?= esc($statusFilter) ?>"><?php endif; ?>
                <?php if ($conditionFilter !== ''): ?><input type="hidden" name="condition" value="<?= esc($conditionFilter) ?>"><?php endif; ?>
            </form>

            <div class="dropdown dashboard-department-dropdown">
                <button class="dashboard-profile dashboard-department-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Choose department">
                    <span class="dashboard-department-name"><?= esc($selectedDepartmentName) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dashboard-profile-menu dashboard-department-menu">
                    <?php if (empty($departmentList)): ?>
                        <li><span class="dropdown-item-text">No departments available</span></li>
                    <?php else: foreach ($departmentList as $department): ?>
                        <?php
                            $deptQuery = ['department_id' => (int) $department['department_id']];
                            if ($search !== '') $deptQuery['search'] = $search;
                            if ($categoryId !== null) $deptQuery['category_id'] = $categoryId;
                            if ($statusFilter !== '') $deptQuery['status'] = $statusFilter;
                            if ($conditionFilter !== '') $deptQuery['condition'] = $conditionFilter;
                        ?>
                        <li><a class="dropdown-item <?= (int) $selectedDepartmentId === (int) $department['department_id'] ? 'active' : '' ?>" href="<?= base_url('admin/equipment?' . http_build_query($deptQuery)) ?>"><?= esc($department['department_name']) ?></a></li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>

            <div class="dropdown">
                <button class="catalog-profile dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="catalog-avatar"><?= esc(strtoupper(substr($userName ?: 'A', 0, 1))) ?></span>
                    <span class="catalog-profile-copy"><strong class="catalog-profile-name"><?= esc($userName ?: 'Admin') ?></strong><small><?= esc($userRole) ?></small></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end catalog-profile-menu">
                    <li><span class="dropdown-item-text"><?= esc($userRole) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Logout</a></li>
                </ul>
            </div>
        </header>

        <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $class): ?>
            <?php if (session()->getFlashdata($key)): ?>
                <div class="alert alert-<?= $class ?> alert-dismissible fade show small" role="alert">
                    <?= esc(session()->getFlashdata($key)) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <section class="admin-inventory-banner" aria-labelledby="inventory-title">
            <div class="admin-inventory-banner-content">
                <span class="admin-inventory-banner-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                <div>
                    <div class="admin-inventory-title-row">
                        <h1 id="inventory-title">Equipment Management</h1>
                        <span class="admin-assets-badge"><?= esc((string) $totalAssets) ?> Total Assets</span>
                    </div>
                    <p>Manage assets, update condition grading, and edit catalog equipment</p>
                </div>
            </div>
            <a href="#" class="btn-add-equipment" data-bs-toggle="modal" data-bs-target="#addNewEquipmentModal" role="button">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                <span>Add New Equipment</span>
            </a>
        </section>

        <section class="admin-inventory-card" aria-labelledby="inventory-section-title">
            <div class="admin-inventory-toolbar">
                <h2 id="inventory-section-title" class="admin-inventory-heading">
                    <span>Equipment Inventory</span>
                </h2>

                <form class="admin-inventory-filters" method="get" action="<?= base_url('admin/equipment') ?>">
                    <?php if ($selectedDepartmentId !== null): ?>
                        <input type="hidden" name="department_id" value="<?= (int) $selectedDepartmentId ?>">
                    <?php endif; ?>

                    <div class="admin-search-field">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <input type="search" name="search" value="<?= esc($search) ?>" placeholder="Search equipment..." aria-label="Search equipment">
                    </div>

                    <select class="form-select admin-filter-select select-category" name="category_id" onchange="this.form.submit()" aria-label="Filter by category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['category_id'] ?>" <?= $categoryId === (int) $category['category_id'] ? 'selected' : '' ?>><?= esc($category['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select class="form-select admin-filter-select select-status" name="status" onchange="this.form.submit()" aria-label="Filter by status">
                        <option value="">All Status</option>
                        <option value="AVAILABLE" <?= $statusFilter === 'AVAILABLE' ? 'selected' : '' ?>>Available</option>
                        <option value="RESERVED" <?= $statusFilter === 'RESERVED' ? 'selected' : '' ?>>Reserved</option>
                        <option value="IN_USE" <?= $statusFilter === 'IN_USE' ? 'selected' : '' ?>>In Use</option>
                    </select>

                    <select class="form-select admin-filter-select select-condition" name="condition" onchange="this.form.submit()" aria-label="Filter by condition">
                        <option value="">All Conditions</option>
                        <option value="GOOD" <?= $conditionFilter === 'GOOD' ? 'selected' : '' ?>>Good</option>
                        <option value="EXCELLENT" <?= $conditionFilter === 'EXCELLENT' ? 'selected' : '' ?>>Excellent</option>
                        <option value="FAIR" <?= $conditionFilter === 'FAIR' ? 'selected' : '' ?>>Fair</option>
                        <option value="DAMAGED" <?= $conditionFilter === 'DAMAGED' ? 'selected' : '' ?>>Damaged</option>
                    </select>

                    <button type="submit" class="admin-filter-btn" title="Apply filters" aria-label="Apply filters">
                        <i class="bi bi-sliders" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            <?php if (empty($equipmentList)): ?>
                <div class="catalog-no-results">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                    <h2>No equipment found</h2>
                    <p>Try changing your search keywords or filter criteria.</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3 catalog-grid admin-inventory-grid">
                    <?php foreach ($equipmentList as $item): ?>
                        <?php
                            $status = strtoupper((string) ($item['status'] ?? 'AVAILABLE'));
                            $condition = strtoupper((string) ($item['condition_status'] ?? 'GOOD'));
                            $categoryName = (string) ($item['category_name'] ?? 'Uncategorized');
                            $catLower = strtolower($categoryName);

                            $catClass = match (true) {
                                str_contains($catLower, 'computer') || str_contains($catLower, 'laptop') => 'cat-computers',
                                str_contains($catLower, 'av') || str_contains($catLower, 'projector') => 'cat-av',
                                str_contains($catLower, 'photo') || str_contains($catLower, 'camera') => 'cat-photo',
                                str_contains($catLower, 'audio') || str_contains($catLower, 'sound') || str_contains($catLower, 'mic') => 'cat-audio',
                                str_contains($catLower, 'tool') || str_contains($catLower, 'drill') => 'cat-tools',
                                str_contains($catLower, 'office') || str_contains($catLower, 'print') => 'cat-office',
                                default => 'cat-default',
                            };

                            $statusClass = match ($status) {
                                'AVAILABLE' => 'state-available',
                                'RESERVED'  => 'state-reserved',
                                'IN_USE'    => 'state-in-use',
                                default     => 'state-other',
                            };

                            $conditionClass = match ($condition) {
                                'EXCELLENT' => 'state-excellent',
                                'GOOD'      => 'state-good',
                                'FAIR'      => 'state-fair',
                                'DAMAGED'   => 'state-damaged',
                                default     => 'state-good',
                            };

                            $modelName = trim((string) ($item['brand'] ?? '') . ' ' . (string) ($item['model'] ?? ''));
                        ?>
                        <div class="col">
                            <article class="catalog-item-card">
                                <button type="button"
                                    class="catalog-item-image btn-open-manage"
                                    data-bs-toggle="modal"
                                    data-bs-target="#manageEquipmentModal"
                                    data-id="<?= (int) $item['equipment_id'] ?>"
                                    data-code="<?= esc($item['equipment_code']) ?>"
                                    data-name="<?= esc($item['equipment_name']) ?>"
                                    data-department-id="<?= (int) ($item['department_id'] ?? 0) ?>"
                                    data-category="<?= esc($categoryName) ?>"
                                    data-brand="<?= esc($item['brand'] ?? '') ?>"
                                    data-model="<?= esc($item['model'] ?? '') ?>"
                                    data-serial="<?= esc($item['serial_number'] ?? '') ?>"
                                    data-status="<?= esc($item['status'] ?? 'AVAILABLE') ?>"
                                    data-condition="<?= esc($item['condition_status'] ?? 'GOOD') ?>"
                                    data-description="<?= esc($item['description'] ?? '') ?>"
                                    data-image="<?= esc(equipment_image_url($item['image'] ?? null)) ?>"
                                    aria-label="Edit <?= esc($item['equipment_name']) ?>"
                                    style="border: 0; padding: 0; width: 100%; background: transparent;">

                                    <img src="<?= esc(equipment_image_url($item['image'] ?? null)) ?>"
                                        alt="<?= esc($item['equipment_name']) ?>"
                                        loading="lazy">
                                </button>
                                <div class="catalog-item-body">
                                    <span class="catalog-asset-tag"><?= esc($item['equipment_code']) ?></span>
                                    <h2><?= esc($item['equipment_name']) ?></h2>
                                    <p class="catalog-item-model"><?= esc($modelName !== '' ? $modelName : 'Model not specified') ?></p>
                                    <span class="cat-badge <?= esc($catClass) ?>"><?= esc($categoryName) ?></span>
                                    <div class="admin-card-footer">
                                        <div class="admin-card-state">
                                            <span class="admin-card-indicator <?= esc($statusClass) ?>">
                                                <i></i><?= esc(ucwords(strtolower(str_replace('_', ' ', $status)))) ?>
                                            </span>
                                            <span class="admin-card-indicator <?= esc($conditionClass) ?>">
                                                <i></i><?= esc(ucwords(strtolower($condition))) ?>
                                            </span>
                                        </div>
                                        <button type="button" 
                                            class="admin-manage-btn btn-open-manage" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#manageEquipmentModal"
                                            data-id="<?= (int) $item['equipment_id'] ?>"
                                            data-code="<?= esc($item['equipment_code']) ?>"
                                            data-name="<?= esc($item['equipment_name']) ?>"
                                            data-department-id="<?= (int) ($item['department_id'] ?? 0) ?>"
                                            data-category="<?= esc($categoryName) ?>"
                                            data-brand="<?= esc($item['brand'] ?? '') ?>"
                                            data-model="<?= esc($item['model'] ?? '') ?>"
                                            data-serial="<?= esc($item['serial_number'] ?? '') ?>"
                                            data-status="<?= esc($item['status'] ?? 'AVAILABLE') ?>"
                                            data-condition="<?= esc($item['condition_status'] ?? 'GOOD') ?>"
                                            data-description="<?= esc($item['description'] ?? '') ?>"
                                            data-image="<?= esc(equipment_image_url($item['image'] ?? null)) ?>"
                                            title="Manage <?= esc($item['equipment_name']) ?>">
                                            <i class="bi bi-wrench" aria-hidden="true"></i>
                                            <span>Manage</span>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <footer class="catalog-pagination-footer">
                <p>Showing <?= $from ?>–<?= $to ?> of <?= $total ?> equipment</p>
                <?= $pager->links('inventory', 'catalog_full') ?>
            </footer>
        </section>
    </main>

    <?= view('admin/equipment/modal_add', [
        'departmentList'       => $departmentList,
        'categories'           => $categories,
        'selectedDepartmentId' => $selectedDepartmentId,
        'suggestedAssetCode'   => $suggestedAssetCode ?? null,
    ]) ?>

    <?= view('admin/equipment/modal_edit', [
        'departmentList' => $departmentList,
        'categories'     => $categories,
    ]) ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-open Add modal if validation errors exist
            const addModalEl = document.getElementById('addNewEquipmentModal');
            if (addModalEl && addModalEl.dataset.autoOpen === 'true') {
                const addModal = new bootstrap.Modal(addModalEl);
                addModal.show();
            }

            // Drag and drop image upload for Add Modal
            const dropzone = document.getElementById('equipmentDropzone');
            const fileInput = document.getElementById('equipmentImageInput');
            const promptEl = document.getElementById('dropzonePrompt');
            const previewEl = document.getElementById('dropzonePreview');
            const previewImg = document.getElementById('previewImg');
            const fileNameEl = document.getElementById('previewFileName');
            const fileSizeEl = document.getElementById('previewFileSize');
            const removeBtn = document.getElementById('btnRemoveImage');

            if (dropzone && fileInput) {
                dropzone.addEventListener('click', (e) => {
                    if (e.target.closest('#btnRemoveImage')) return;
                    fileInput.click();
                });

                dropzone.addEventListener('keydown', (e) => {
                    if ((e.key === 'Enter' || e.key === ' ') && !e.target.closest('#btnRemoveImage')) {
                        e.preventDefault();
                        fileInput.click();
                    }
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.add('dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.remove('dragover');
                    });
                });

                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    if (dt && dt.files && dt.files.length > 0) {
                        fileInput.files = dt.files;
                        handleFileSelection(dt.files[0]);
                    }
                });

                fileInput.addEventListener('change', () => {
                    if (fileInput.files && fileInput.files.length > 0) {
                        handleFileSelection(fileInput.files[0]);
                    }
                });

                if (removeBtn) {
                    removeBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        fileInput.value = '';
                        previewImg.src = '';
                        fileNameEl.textContent = '';
                        fileSizeEl.textContent = '';
                        previewEl.classList.add('d-none');
                        promptEl.classList.remove('d-none');
                    });
                }

                function handleFileSelection(file) {
                    if (!file) return;

                    const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPEG, PNG, WebP, or SVG).');
                        fileInput.value = '';
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size exceeds 5MB limit.');
                        fileInput.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImg.src = e.target.result;
                        fileNameEl.textContent = file.name;
                        fileSizeEl.textContent = formatBytes(file.size);
                        promptEl.classList.add('d-none');
                        previewEl.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            }

            // =====================================================================
            // Manage Equipment Modal Dynamic Population & Handling
            // =====================================================================
            const manageModalEl = document.getElementById('manageEquipmentModal');
            const manageForm = document.getElementById('manageEquipmentForm');
            const manageDeleteForm = document.getElementById('manageDeleteForm');
            const btnManageDelete = document.getElementById('btnManageDelete');

            const currentImg = document.getElementById('manageCurrentImg');
            const manageDropzone = document.getElementById('manageDropzone');
            const manageFileInput = document.getElementById('manageEquipmentImageInput');
            const managePrompt = document.getElementById('manageDropzonePrompt');
            const managePreview = document.getElementById('manageDropzonePreview');
            const managePreviewImg = document.getElementById('managePreviewImg');
            const manageFileName = document.getElementById('managePreviewFileName');
            const manageFileSize = document.getElementById('managePreviewFileSize');
            const manageRemoveBtn = document.getElementById('btnRemoveManageImage');

            // Manage buttons click
            document.querySelectorAll('.btn-open-manage').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const code = btn.dataset.code || '';
                    const name = btn.dataset.name || '';
                    const departmentId = btn.dataset.departmentId || '';
                    const category = btn.dataset.category || '';
                    const brand = btn.dataset.brand || '';
                    const model = btn.dataset.model || '';
                    const serial = btn.dataset.serial || '';
                    const status = btn.dataset.status || 'AVAILABLE';
                    const condition = btn.dataset.condition || 'GOOD';
                    const description = btn.dataset.description || '';
                    const image = btn.dataset.image || '';

                    // Set form actions
                    manageForm.action = '<?= base_url('admin/equipment') ?>/' + id + '/update';
                    manageDeleteForm.action = '<?= base_url('admin/equipment') ?>/' + id + '/delete';

                    // Populate fields
                    document.getElementById('manage_equipment_id').value = id;
                    document.getElementById('manage_eq_name').value = name;
                    document.getElementById('manage_eq_code').value = code;
                    document.getElementById('manage_eq_serial').value = serial;
                    document.getElementById('manage_eq_department').value = departmentId;
                    document.getElementById('manage_eq_category').value = category;
                    document.getElementById('manage_eq_brand').value = brand;
                    document.getElementById('manage_eq_model').value = model;
                    document.getElementById('manage_eq_status').value = status;
                    document.getElementById('manage_eq_condition').value = condition;
                    document.getElementById('manage_eq_description').value = description;

                    // Set current thumbnail
                    if (currentImg) {
                        currentImg.src = image;
                    }

                    // Reset replacement preview
                    resetManageDropzone();
                });
            });

            // Replacement dropzone event listeners
            if (manageDropzone && manageFileInput) {
                manageDropzone.addEventListener('click', (e) => {
                    if (e.target.closest('#btnRemoveManageImage')) return;
                    manageFileInput.click();
                });

                manageDropzone.addEventListener('keydown', (e) => {
                    if ((e.key === 'Enter' || e.key === ' ') && !e.target.closest('#btnRemoveManageImage')) {
                        e.preventDefault();
                        manageFileInput.click();
                    }
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    manageDropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        manageDropzone.classList.add('dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    manageDropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        manageDropzone.classList.remove('dragover');
                    });
                });

                manageDropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    if (dt && dt.files && dt.files.length > 0) {
                        manageFileInput.files = dt.files;
                        handleManageFileSelection(dt.files[0]);
                    }
                });

                manageFileInput.addEventListener('change', () => {
                    if (manageFileInput.files && manageFileInput.files.length > 0) {
                        handleManageFileSelection(manageFileInput.files[0]);
                    }
                });

                if (manageRemoveBtn) {
                    manageRemoveBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        resetManageDropzone();
                    });
                }

                function handleManageFileSelection(file) {
                    if (!file) return;

                    const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPEG, PNG, WebP, or SVG).');
                        manageFileInput.value = '';
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size exceeds 5MB limit.');
                        manageFileInput.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        managePreviewImg.src = e.target.result;
                        manageFileName.textContent = file.name;
                        manageFileSize.textContent = formatBytes(file.size);
                        managePrompt.classList.add('d-none');
                        managePreview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }

                function resetManageDropzone() {
                    manageFileInput.value = '';
                    managePreviewImg.src = '';
                    manageFileName.textContent = '';
                    manageFileSize.textContent = '';
                    managePreview.classList.add('d-none');
                    managePrompt.classList.remove('d-none');
                }
            }

            // Delete / Decommission Asset handler
            if (btnManageDelete) {
                btnManageDelete.addEventListener('click', () => {
                    const eqName = document.getElementById('manage_eq_name').value || 'this asset';
                    const confirmed = confirm('Are you sure you want to decommission/delete ' + eqName + '? This action cannot be undone.');
                    if (confirmed && manageDeleteForm) {
                        manageDeleteForm.submit();
                    }
                });
            }

            function formatBytes(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }
        });
    </script>
</body>
</html>

