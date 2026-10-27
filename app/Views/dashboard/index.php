<?php
/**
 * @var array $equipmentList
 * @var array $categories
 * @var array{total:int, available:int, reserved:int, maintenance:int} $equipmentStats
 * @var int $reservationCount
 * @var array $departmentList
 * @var int|null $selectedDepartmentId
 * @var string $userName
 * @var string $userRole
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CPC Equipment Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/dashboard.css') ?>" rel="stylesheet">
</head>
<body class="dashboard-page">
    <aside class="dashboard-sidebar">
        <a href="<?= base_url('dashboard') ?>" class="dashboard-brand">
            <span class="dashboard-brand-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
            <span><strong>CPC</strong><small>Equipment Tracker</small></span>
        </a>

        <nav class="dashboard-nav" aria-label="Main navigation">
            <a href="<?= base_url('dashboard') ?>" class="dashboard-nav-link active" aria-current="page">
                <i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span>Dashboard</span>
            </a>
            <a href="<?= base_url('equipment') ?>" class="dashboard-nav-link">
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

    <main class="dashboard-main">
        <header class="dashboard-topbar">
            <label class="dashboard-global-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" placeholder="Search equipment, reservations, or activities..." aria-label="Search dashboard" data-global-search>
            </label>

            <div class="dropdown dashboard-department-dropdown">
                <button class="dashboard-profile dashboard-department-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Choose department">
                    <i class="bi bi-building" aria-hidden="true"></i>
                    <span class="dashboard-department-name">
                        <?php
                            $selectedDepartmentName = 'Select Department';
                            foreach ($departmentList as $department) {
                                if ((int) $department['department_id'] === (int) $selectedDepartmentId) {
                                    $selectedDepartmentName = $department['department_name'];
                                    break;
                                }
                            }
                        ?>
                        <?= esc($selectedDepartmentName) ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dashboard-profile-menu dashboard-department-menu">
                    <?php if (empty($departmentList)): ?>
                        <li><span class="dropdown-item-text">No departments available</span></li>
                    <?php else: ?>
                        <?php foreach ($departmentList as $department): ?>
                            <li>
                                <a class="dropdown-item <?= (int) $selectedDepartmentId === (int) $department['department_id'] ? 'active' : '' ?>"
                                   href="<?= base_url('dashboard?department_id=' . (int) $department['department_id']) ?>">
                                    <?= esc($department['department_name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="dropdown">
                <button class="dashboard-profile dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="dashboard-avatar"><?= esc(strtoupper(substr($userName ?: 'U', 0, 1))) ?></span>
                    <span class="dashboard-profile-name"><?= esc($userName ?: 'User') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dashboard-profile-menu">
                    <li><span class="dropdown-item-text"><?= esc($userRole) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
                </ul>
            </div>
        </header>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <section class="dashboard-quick-actions" aria-label="Quick actions">
            <article class="quick-action-card">
                <div class="quick-action-top">
                    <div><span class="quick-action-kicker">Quick Action</span><h2>New Reservation</h2></div>
                    <span class="quick-action-icon"><i class="bi bi-calendar2-check" aria-hidden="true"></i></span>
                </div>
                <p>Reserve equipment for your upcoming classes and projects.</p>
                <div class="quick-action-footer"><span>Choose an available item</span><a href="#equipment-catalog">Book Now <i class="bi bi-arrow-right"></i></a></div>
            </article>
            <article class="quick-action-card">
                <div class="quick-action-top">
                    <div><span class="quick-action-kicker">Inventory</span><h2>View Equipment</h2></div>
                    <span class="quick-action-icon"><i class="bi bi-clipboard2-check" aria-hidden="true"></i></span>
                </div>
                <p>Browse equipment available in your selected department.</p>
                <div class="quick-action-footer"><span><?= esc((string) $equipmentStats['total']) ?> items in catalog</span><a href="<?= base_url('equipment') ?>">Open Catalog <i class="bi bi-arrow-right"></i></a></div>
            </article>
            <article class="quick-action-card quick-action-card-green">
                <div class="quick-action-top">
                    <div><span class="quick-action-kicker">My Activity</span><h2>My Reservations</h2></div>
                    <span class="quick-action-icon"><i class="bi bi-calendar-check" aria-hidden="true"></i></span>
                </div>
                <p>Track the status of your reservation requests.</p>
                <div class="quick-action-footer"><span><?= esc((string) $reservationCount) ?> reservations</span><a href="<?= base_url('reservations') ?>">Review Status <i class="bi bi-arrow-right"></i></a></div>
            </article>
        </section>

        <section class="dashboard-stats" aria-label="Equipment summary">
            <article class="stat-card">
                <div><span>Total Equipment</span><strong><?= esc((string) $equipmentStats['total']) ?></strong><small>in catalog</small></div>
                <span class="stat-icon stat-icon-blue"><i class="bi bi-archive" aria-hidden="true"></i></span>
            </article>
            <article class="stat-card">
                <div><span>Available</span><strong><?= esc((string) $equipmentStats['available']) ?></strong><small class="text-success">ready to use</small></div>
                <span class="stat-icon stat-icon-green"><i class="bi bi-check-circle" aria-hidden="true"></i></span>
            </article>
            <article class="stat-card">
                <div><span>Reserved</span><strong><?= esc((string) $equipmentStats['reserved']) ?></strong><small>currently reserved</small></div>
                <span class="stat-icon stat-icon-blue"><i class="bi bi-clock" aria-hidden="true"></i></span>
            </article>
            <article class="stat-card">
                <div><span>In Maintenance</span><strong><?= esc((string) $equipmentStats['maintenance']) ?></strong><small class="text-danger">damaged condition</small></div>
                <span class="stat-icon stat-icon-red"><i class="bi bi-wrench-adjustable" aria-hidden="true"></i></span>
            </article>
        </section>

        <section class="catalog-section" id="equipment-catalog">
            <div class="catalog-heading-row">
                <h2><span class="catalog-heading-icon"><i class="bi bi-archive" aria-hidden="true"></i></span>Equipment Catalog</h2>
                <div class="catalog-filters">
                    <label class="catalog-search">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <input type="search" placeholder="Search equipment..." aria-label="Search equipment" data-catalog-search>
                    </label>
                    <select class="form-select" aria-label="Filter by category" data-category-filter>
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= esc(strtolower($category['category_name'])) ?>"><?= esc($category['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select" aria-label="Filter by status" data-status-filter>
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="reserved">Reserved</option>
                        <option value="in_use">In Use</option>
                    </select>
                    <select class="form-select" aria-label="Filter by condition" data-condition-filter>
                        <option value="">All Conditions</option>
                        <option value="good">Good</option>
                        <option value="fair">Fair</option>
                        <option value="damaged">Damaged</option>
                    </select>
                </div>
            </div>

            <div class="row g-3" data-equipment-grid>
                <?php foreach ($equipmentList as $item): ?>
                    <?php
                        $status = strtolower((string) ($item['status'] ?? 'available'));
                        $condition = strtolower((string) ($item['condition_status'] ?? 'good'));
                        $category = strtolower((string) ($item['category_name'] ?? 'uncategorized'));
                        $searchText = strtolower(trim(($item['equipment_code'] ?? '') . ' ' . ($item['equipment_name'] ?? '') . ' ' . ($item['brand'] ?? '') . ' ' . ($item['model'] ?? '') . ' ' . $category));
                    ?>
                    <div class="col-md-6 col-xl-4" data-equipment-card
                         data-search="<?= esc($searchText) ?>"
                         data-category="<?= esc($category) ?>"
                         data-status="<?= esc($status) ?>"
                         data-condition="<?= esc($condition) ?>">
                        <article class="equipment-card">
                            <a href="<?= base_url('equipment/' . $item['equipment_id']) ?>" class="equipment-card-image">
                                <img src="<?= esc(equipment_image_url($item['image'] ?? null)) ?>"
                                     alt="<?= esc($item['equipment_name']) ?>" loading="lazy">
                            </a>
                            <div class="equipment-card-body">
                                <span class="equipment-code"><?= esc($item['equipment_code']) ?></span>
                                <h3><?= esc($item['equipment_name']) ?></h3>
                                <p class="equipment-model"><?= esc(trim(($item['brand'] ?? '') . ' ' . ($item['model'] ?? '')) ?: 'Model not specified') ?></p>
                                <span class="equipment-category"><?= esc($item['category_name'] ?? 'Uncategorized') ?></span>
                                <div class="equipment-card-footer">
                                    <div class="equipment-statuses">
                                        <span class="equipment-status <?= $status === 'available' ? 'status-available' : 'status-other' ?>"><i></i><?= esc(ucwords(str_replace('_', ' ', $status))) ?></span>
                                        <span class="equipment-status <?= $condition === 'good' ? 'status-available' : 'status-condition' ?>"><i></i><?= esc(ucwords($condition)) ?></span>
                                    </div>
                                    <?php if ($status === 'available'): ?>
                                        <a href="<?= base_url('reservations/create/' . $item['equipment_id']) ?>" class="btn btn-reserve"><i class="bi bi-calendar2-plus me-1" aria-hidden="true"></i>Reserve</a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-reserve" disabled><i class="bi bi-calendar2-plus me-1" aria-hidden="true"></i>Reserve</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="catalog-empty" data-empty-state <?= $equipmentList === [] ? '' : 'hidden' ?>>
                <?= $equipmentList === [] ? 'No equipment is listed for this department yet.' : 'No equipment matches your filters.' ?>
            </p>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const cards = Array.from(document.querySelectorAll('[data-equipment-card]'));
            const globalSearch = document.querySelector('[data-global-search]');
            const catalogSearch = document.querySelector('[data-catalog-search]');
            const categoryFilter = document.querySelector('[data-category-filter]');
            const statusFilter = document.querySelector('[data-status-filter]');
            const conditionFilter = document.querySelector('[data-condition-filter]');
            const emptyState = document.querySelector('[data-empty-state]');

            function filterEquipment() {
                const query = (catalogSearch?.value || globalSearch?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                cards.forEach((card) => {
                    const matches = (!query || card.dataset.search.includes(query))
                        && (!categoryFilter.value || card.dataset.category === categoryFilter.value)
                        && (!statusFilter.value || card.dataset.status === statusFilter.value)
                        && (!conditionFilter.value || card.dataset.condition === conditionFilter.value);
                    card.hidden = !matches;
                    if (matches) visibleCount++;
                });

                if (emptyState) emptyState.hidden = visibleCount > 0;
            }

            [catalogSearch, categoryFilter, statusFilter, conditionFilter].forEach((control) => {
                control?.addEventListener('input', filterEquipment);
                control?.addEventListener('change', filterEquipment);
            });

            globalSearch?.addEventListener('input', () => {
                if (catalogSearch) catalogSearch.value = globalSearch.value;
                filterEquipment();
            });
            catalogSearch?.addEventListener('input', () => {
                if (globalSearch) globalSearch.value = catalogSearch.value;
                filterEquipment();
            });
        })();
    </script>
</body>
</html>
