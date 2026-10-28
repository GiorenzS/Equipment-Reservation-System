<?php
/**
 * @var array{total:int, available:int, reserved:int, maintenance:int} $equipmentStats
 * @var int $reservationCount
 * @var array $recentHistory
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
    <link href="<?= base_url('css/catalog.css') ?>" rel="stylesheet">
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

        <section class="catalog-stats" aria-label="Equipment summary">
            <article class="catalog-stat">
                <span class="catalog-stat-icon stat-blue"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                <div><span class="catalog-stat-label">Total Equipment</span><strong><?= esc((string) $equipmentStats['total']) ?></strong><small>in catalog</small></div>
            </article>
            <article class="catalog-stat">
                <span class="catalog-stat-icon stat-green"><i class="bi bi-check-circle-fill" aria-hidden="true"></i></span>
                <div><span class="catalog-stat-label">Available</span><strong><?= esc((string) $equipmentStats['available']) ?></strong><small>ready to use</small></div>
            </article>
            <article class="catalog-stat">
                <span class="catalog-stat-icon stat-amber"><i class="bi bi-clock-fill" aria-hidden="true"></i></span>
                <div><span class="catalog-stat-label">Reserved</span><strong><?= esc((string) $equipmentStats['reserved']) ?></strong><small>currently reserved</small></div>
            </article>
            <article class="catalog-stat">
                <span class="catalog-stat-icon stat-red"><i class="bi bi-wrench-adjustable" aria-hidden="true"></i></span>
                <div><span class="catalog-stat-label">In Maintenance</span><strong><?= esc((string) $equipmentStats['maintenance']) ?></strong><small>damaged / under repair</small></div>
            </article>
        </section>

        <section class="borrowing-history" aria-labelledby="borrowing-history-title">
            <div class="borrowing-history-header">
                <div>
                    <h2 id="borrowing-history-title">Personal Borrowing History</h2>
                </div>
            </div>

            <?php if (empty($recentHistory)): ?>
                <div class="borrowing-history-empty">
                    <span class="borrowing-history-empty-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                    <h3>No previous borrowings found</h3>
                    <p>Browse the Equipment Catalog to make your first reservation.</p>
                    <a href="<?= base_url('equipment/catalog') ?>" class="btn btn-primary btn-sm">Browse Equipment</a>
                </div>
            <?php else: ?>
                <div class="table-responsive borrowing-history-table-wrap">
                    <table class="table borrowing-history-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Equipment</th>
                                <th scope="col">Category</th>
                                <th scope="col">Borrowed / Return Date</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentHistory as $item): ?>
                                <?php
                                    $transactionStatus = strtoupper((string) ($item['transaction_status'] ?? ''));
                                    $borrowedTimestamp = ! empty($item['borrow_date']) ? strtotime($item['borrow_date']) : false;
                                    $returnDate = $item['actual_return_date'] ?: $item['expected_return_date'];
                                    $returnTimestamp = ! empty($returnDate) ? strtotime($returnDate) : false;
                                    $isReturned = $transactionStatus === 'RETURNED' || ! empty($item['actual_return_date']);
                                    $isOverdue = ! $isReturned && $transactionStatus === 'CHECKED_OUT'
                                        && $returnTimestamp !== false && $returnTimestamp < time();

                                    if ($isReturned) {
                                        $historyStatus = 'Returned';
                                        $historyStatusClass = 'history-status-returned';
                                    } elseif ($isOverdue) {
                                        $historyStatus = 'Overdue';
                                        $historyStatusClass = 'history-status-overdue';
                                    } elseif ($transactionStatus === 'CHECKED_OUT') {
                                        $historyStatus = 'Currently Borrowed';
                                        $historyStatusClass = 'history-status-active';
                                    } else {
                                        $historyStatus = ucwords(strtolower(str_replace('_', ' ', $transactionStatus ?: 'Unknown')));
                                        $historyStatusClass = 'history-status-other';
                                    }

                                    $equipmentModelName = trim((string) ($item['brand'] ?? '') . ' ' . (string) ($item['model'] ?? ''));
                                ?>
                                <tr>
                                    <td>
                                        <div class="history-equipment-name"><?= esc($item['equipment_name']) ?></div>
                                        <?php if ($equipmentModelName !== ''): ?><small class="history-equipment-model"><?= esc($equipmentModelName) ?></small><?php endif; ?>
                                        <span class="history-asset-tag"><?= esc($item['equipment_code']) ?></span>
                                    </td>
                                    <td><span class="history-category"><?= esc($item['category_name'] ?? 'Uncategorized') ?></span></td>
                                    <td>
                                        <div class="history-date-range">
                                            <span><?= $borrowedTimestamp !== false ? esc(date('M j, Y', $borrowedTimestamp)) : '�' ?></span>
                                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                            <span><?= $returnTimestamp !== false ? esc(date('M j, Y', $returnTimestamp)) : '�' ?></span>
                                        </div>
                                        <small class="history-date-caption"><?= $isReturned ? 'Borrowed - Returned' : 'Borrowed - Due back' ?></small>
                                    </td>
                                    <td><span class="history-status <?= $historyStatusClass ?>"><?= esc($historyStatus) ?></span></td>
                                    <td class="text-end">
                                        <?php if (strtoupper((string) ($item['equipment_status'] ?? '')) === 'AVAILABLE'): ?>
                                            <a href="<?= base_url('reservations/create/' . (int) $item['equipment_id']) ?>" class="btn btn-primary btn-sm history-book-again"><i class="bi bi-calendar2-plus me-1" aria-hidden="true"></i>Book Again</a>
                                        <?php else: ?>
                                            <a href="<?= base_url('equipment/' . (int) $item['equipment_id']) ?>" class="btn btn-outline-secondary btn-sm history-book-again">View Item</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>