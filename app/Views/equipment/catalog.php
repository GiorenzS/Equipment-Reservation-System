<?php
/**
 * @var array $equipmentList
 * @var array $categories
 * @var array{total:int, available:int, reserved:int, maintenance:int} $equipmentStats
 * @var array $departmentList
 * @var int|null $selectedDepartmentId
 * @var string $userName
 * @var string $userRole
 * @var string $search
 * @var int|null $categoryId
 * @var string $statusFilter
 * @var string $conditionFilter
 * @var \CodeIgniter\Pager\Pager $pager
 * @var array $paginationDetails
 */
$selectedDepartmentName = 'All Departments';
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
    <title>Equipment Catalog - CPC Equipment Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/catalog.css') ?>" rel="stylesheet">
</head>
<body class="dashboard-page catalog-page">
    <aside class="dashboard-sidebar">
        <a href="<?= base_url('dashboard') ?>" class="dashboard-brand">
            <span class="dashboard-brand-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
            <span><strong>CPC</strong><small>Equipment Tracker</small></span>
        </a>
        <nav class="dashboard-nav" aria-label="Main navigation">
            <a href="<?= base_url('dashboard') ?>" class="dashboard-nav-link">
                <i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span>Dashboard</span>
            </a>
            <a href="<?= base_url('equipment') ?>" class="dashboard-nav-link active" aria-current="page">
                <i class="bi bi-archive" aria-hidden="true"></i><span>Equipment Catalog</span>
            </a>
            <a href="<?= base_url('reservations') ?>" class="dashboard-nav-link">
                <i class="bi bi-calendar3" aria-hidden="true"></i><span>My Reservations</span>
            </a>
        </nav>
        <a href="<?= base_url('logout') ?>" class="dashboard-logout">
            <i class="bi bi-box-arrow-left" aria-hidden="true"></i><span>Logout</span>
        </a>
    </aside>

    <main class="catalog-main">
        <header class="catalog-topbar">
            <form class="catalog-global-search" method="get" action="<?= base_url('equipment') ?>">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" name="search" value="<?= esc($search) ?>" placeholder="Search equipment by name, code, brand or category..." aria-label="Search equipment by name, code, brand or category">
                <?php if ($selectedDepartmentId !== null): ?><input type="hidden" name="department_id" value="<?= (int) $selectedDepartmentId ?>"><?php endif; ?>
            </form>
            <div class="dropdown catalog-department-dropdown">
                <button class="catalog-profile catalog-department-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Choose department">
                    <i class="bi bi-box-seam" aria-hidden="true"></i><span class="catalog-department-name"><?= esc($selectedDepartmentName) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end catalog-profile-menu">
                    <?php foreach ($departmentList as $department): ?>
                        <li><a class="dropdown-item <?= (int) $selectedDepartmentId === (int) $department['department_id'] ? 'active' : '' ?>" href="<?= base_url('equipment?department_id=' . (int) $department['department_id']) ?>"><?= esc($department['department_name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="dropdown">
                <button class="catalog-profile dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="catalog-avatar"><?= esc(strtoupper(substr($userName ?: 'U', 0, 1))) ?></span>
                    <span class="catalog-profile-name"><?= esc($userName ?: 'User') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end catalog-profile-menu">
                    <li><span class="dropdown-item-text"><?= esc($userRole) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Logout</a></li>
                </ul>
            </div>
        </header>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert"><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show small" role="alert"><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        <?php endif; ?>

        <section class="catalog-stats" aria-label="Equipment summary">
            <article class="catalog-stat"><span class="catalog-stat-icon stat-blue"><i class="bi bi-box-seam" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Total Equipment</span><strong><?= esc((string) $equipmentStats['total']) ?></strong><small>in catalog</small></div></article>
            <article class="catalog-stat"><span class="catalog-stat-icon stat-green"><i class="bi bi-check-circle-fill" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Available</span><strong><?= esc((string) $equipmentStats['available']) ?></strong><small>ready to use</small></div></article>
            <article class="catalog-stat"><span class="catalog-stat-icon stat-amber"><i class="bi bi-clock-fill" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Reserved</span><strong><?= esc((string) $equipmentStats['reserved']) ?></strong><small>currently reserved</small></div></article>
            <article class="catalog-stat"><span class="catalog-stat-icon stat-red"><i class="bi bi-wrench-adjustable" aria-hidden="true"></i></span><div><span class="catalog-stat-label">In Maintenance</span><strong><?= esc((string) $equipmentStats['maintenance']) ?></strong><small>damaged / under repair</small></div></article>
        </section>

        <section class="catalog-content" aria-labelledby="catalog-title">
            <div class="catalog-heading-row">
                <h1 id="catalog-title"><span><i class="bi bi-box-seam" aria-hidden="true"></i></span>Equipment Catalog</h1>
                <span class="catalog-department-context"><?= esc($selectedDepartmentName) ?></span>
            </div>
            <form class="catalog-filter-form" method="get" action="<?= base_url('equipment') ?>">
                <?php if ($selectedDepartmentId !== null): ?><input type="hidden" name="department_id" value="<?= (int) $selectedDepartmentId ?>"><?php endif; ?>
                <label class="catalog-search-field"><i class="bi bi-search" aria-hidden="true"></i><input type="search" name="search" value="<?= esc($search) ?>" placeholder="Search equipment..." aria-label="Search equipment"></label>
                <select class="form-select" name="category_id" aria-label="Filter by category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['category_id'] ?>" <?= $categoryId === (int) $category['category_id'] ? 'selected' : '' ?>><?= esc($category['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-select" name="status" aria-label="Filter by status">
                    <option value="">All Status</option><option value="AVAILABLE" <?= $statusFilter === 'AVAILABLE' ? 'selected' : '' ?>>Available</option><option value="RESERVED" <?= $statusFilter === 'RESERVED' ? 'selected' : '' ?>>Reserved</option><option value="IN_USE" <?= $statusFilter === 'IN_USE' ? 'selected' : '' ?>>In Use</option>
                </select>
                <select class="form-select" name="condition_status" aria-label="Filter by condition">
                    <option value="">All Conditions</option><option value="GOOD" <?= $conditionFilter === 'GOOD' ? 'selected' : '' ?>>Good</option><option value="FAIR" <?= $conditionFilter === 'FAIR' ? 'selected' : '' ?>>Fair</option><option value="DAMAGED" <?= $conditionFilter === 'DAMAGED' ? 'selected' : '' ?>>Damaged</option>
                </select>
                <button class="btn btn-primary catalog-filter-submit" type="submit">Apply</button>
            </form>

            <?php if (empty($equipmentList)): ?>
                <div class="catalog-no-results"><i class="bi bi-inbox" aria-hidden="true"></i><h2>No equipment found</h2><p>Try changing your search or filters.</p></div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 catalog-grid">
                    <?php foreach ($equipmentList as $item): ?>
                        <?php
                            $status = strtoupper((string) ($item['status'] ?? 'AVAILABLE'));
                            $condition = strtoupper((string) ($item['condition_status'] ?? 'GOOD'));
                            $categoryName = (string) ($item['category_name'] ?? 'Uncategorized');
                            $categoryLower = strtolower($categoryName);
                            $categoryIcon = match (true) {
                                str_contains($categoryLower, 'computer') => 'bi-pc-display',
                                str_contains($categoryLower, 'av') => 'bi-projector',
                                str_contains($categoryLower, 'photo') => 'bi-camera-fill',
                                str_contains($categoryLower, 'audio') => 'bi-mic-fill',
                                str_contains($categoryLower, 'tool') => 'bi-tools',
                                default => 'bi-box-seam',
                            };
                            $statusClass = match ($status) {
                                'AVAILABLE' => 'item-status-available', 'RESERVED' => 'item-status-reserved', 'IN_USE' => 'item-status-in-use', default => 'item-status-other',
                            };
                            $conditionClass = match ($condition) {
                                'GOOD' => 'item-condition-good', 'FAIR' => 'item-condition-fair', 'DAMAGED' => 'item-condition-damaged', default => 'item-condition-other',
                            };
                            $modelName = trim((string) ($item['brand'] ?? '') . ' ' . (string) ($item['model'] ?? ''));
                        ?>
                        <div class="col">
                            <article class="catalog-item-card">
                                <a href="<?= base_url('equipment/' . (int) $item['equipment_id']) ?>" class="catalog-item-image" aria-label="View <?= esc($item['equipment_name']) ?> details"><img src="<?= esc(equipment_image_url($item['image'] ?? null)) ?>" alt="<?= esc($item['equipment_name']) ?>" loading="lazy"></a>
                                <div class="catalog-item-body">
                                    <span class="catalog-asset-tag"><?= esc($item['equipment_code']) ?></span>
                                    <h2><?= esc($item['equipment_name']) ?></h2>
                                    <p class="catalog-item-model"><?= esc($modelName !== '' ? $modelName : 'Model not specified') ?></p>
                                    <span class="catalog-category"><i class="bi <?= esc($categoryIcon) ?>" aria-hidden="true"></i><?= esc($categoryName) ?></span>
                                    <div class="catalog-item-footer">
                                        <div class="catalog-item-state"><span class="catalog-item-status <?= $statusClass ?>"><i></i><?= esc(ucwords(strtolower(str_replace('_', ' ', $status)))) ?></span><span class="catalog-item-condition <?= $conditionClass ?>"><i></i><?= esc(ucwords(strtolower($condition))) ?></span></div>
                                        <?php if ($status === 'AVAILABLE'): ?>
                                            <a href="<?= base_url('reservations/create/' . (int) $item['equipment_id']) ?>" class="btn btn-primary catalog-reserve-button"><i class="bi bi-calendar2-plus" aria-hidden="true"></i><span>Reserve</span></a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-primary catalog-reserve-button" disabled><i class="bi bi-calendar2-plus" aria-hidden="true"></i><span>Reserve</span></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <footer class="catalog-pagination-footer">
                <p>Showing <?= $from ?>–<?= $to ?> of <?= $total ?> equipment</p>
                <?= $pager->links('catalog', 'catalog_full') ?>
            </footer>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>