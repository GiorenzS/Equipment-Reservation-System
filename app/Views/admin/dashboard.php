<?php
/**
 * @var array $departmentList
 * @var int|null $selectedDepartmentId
 * @var string $userName
 * @var string $userRole
 * @var string $search
 * @var string $filter
 * @var array $equipmentStats
 * @var array $pendingReservations
 * @var array $pendingCounts
 * @var \CodeIgniter\Pager\Pager $pager
 * @var array $paginationDetails
 */
$selectedDepartmentName = 'Select Department';
foreach ($departmentList as $department) {
    if ((int) $department['department_id'] === (int) $selectedDepartmentId) {
        $selectedDepartmentName = $department['department_name'];
        break;
    }
}
$totalPending = (int) ($paginationDetails['total'] ?? 0);
$currentPage = (int) ($paginationDetails['currentPage'] ?? 1);
$perPage = (int) ($paginationDetails['perPage'] ?? 5);
$from = $totalPending > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
$to = min($currentPage * $perPage, $totalPending);
$queueUrl = static function (string $selectedFilter) use ($selectedDepartmentId, $search): string {
    $query = ['filter' => $selectedFilter];
    if ($selectedDepartmentId !== null) {
        $query['department_id'] = $selectedDepartmentId;
    }
    if ($search !== '') {
        $query['search'] = $search;
    }
    return base_url('admin') . '?' . http_build_query($query);
};
$growth = $equipmentStats['growth'];
$growthLabel = $growth === null ? 'No baseline' : sprintf('%+d%% since last month', $growth);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CPC Equipment Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/catalog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/admin-dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/admin-transactions.css') ?>" rel="stylesheet">
</head>
<body class="dashboard-page admin-dashboard-page">
    <aside class="dashboard-sidebar">
        <a href="<?= base_url('admin') ?>" class="dashboard-brand">
            <span class="dashboard-brand-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
            <span><strong>CPC</strong><small>Equipment Reservation</small></span>
        </a>
        <nav class="dashboard-nav" aria-label="Admin navigation">
            <a href="<?= base_url('admin') ?>" class="dashboard-nav-link active" aria-current="page"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span>Dashboard</span></a>
            <a href="<?= base_url('admin/equipment') ?>" class="dashboard-nav-link"><i class="bi bi-archive" aria-hidden="true"></i><span>Equipment Inventory</span></a>
            <a href="<?= base_url('admin/transactions') ?>" class="dashboard-nav-link"><i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Transactions</span></a>
        </nav>
        <a href="<?= base_url('logout') ?>" class="dashboard-logout"><i class="bi bi-box-arrow-left" aria-hidden="true"></i><span>Logout</span></a>
    </aside>

    <main class="dashboard-main admin-dashboard-main">
        <header class="dashboard-topbar">
            <form class="dashboard-global-search" method="get" action="<?= base_url('admin') ?>">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" name="search" value="<?= esc($search) ?>" placeholder="Search equipment, borrowers, or..." aria-label="Search equipment, borrowers, or reservations">
                <?php if ($selectedDepartmentId !== null): ?><input type="hidden" name="department_id" value="<?= (int) $selectedDepartmentId ?>"><?php endif; ?>
                <?php if ($filter !== 'all'): ?><input type="hidden" name="filter" value="<?= esc($filter) ?>"><?php endif; ?>
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
                            $departmentQuery = ['department_id' => (int) $department['department_id'], 'filter' => $filter];
                            if ($search !== '') {
                                $departmentQuery['search'] = $search;
                            }
                        ?>
                        <li><a class="dropdown-item <?= (int) $selectedDepartmentId === (int) $department['department_id'] ? 'active' : '' ?>" href="<?= base_url('admin?' . http_build_query($departmentQuery)) ?>"><?= esc($department['department_name']) ?></a></li>
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

        <section class="catalog-stats admin-dashboard-stats" aria-label="System summary">
            <a class="catalog-stat catalog-stat-link" href="<?= base_url('admin/equipment') . ($selectedDepartmentId !== null ? '?department_id=' . (int) $selectedDepartmentId : '') ?>" aria-label="View equipment inventory"><span class="catalog-stat-icon stat-blue"><i class="bi bi-box-seam" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Total Equipment</span><strong><?= esc((string) $equipmentStats['total']) ?></strong></div></a>
            <a class="catalog-stat catalog-stat-link" href="<?= base_url('admin/transactions?queue=pending') ?>" aria-label="View active reservations"><span class="catalog-stat-icon stat-green"><i class="bi bi-calendar-check" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Active Reservations</span><strong><?= esc((string) $equipmentStats['activeReservations']) ?></strong></div></a>
            <a class="catalog-stat catalog-stat-link" href="<?= base_url('admin/transactions?queue=checkout') ?>" aria-label="View ongoing borrowings"><span class="catalog-stat-icon stat-amber"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Ongoing Borrowings</span><strong><?= esc((string) $equipmentStats['ongoingBorrowings']) ?></strong></div></a>
            <a class="catalog-stat catalog-stat-link" href="<?= base_url('admin/transactions?queue=overdue') ?>" aria-label="View pending returns"><span class="catalog-stat-icon stat-red"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Pending Returns</span><strong><?= esc((string) $equipmentStats['pendingReturns']) ?></strong></div></a>
        </section>

        <section class="admin-tx-card" id="pending-section" aria-labelledby="approval-queue-title">
            <div class="admin-tx-card-header">
                <div>
                    <h2 class="admin-tx-card-title title-pending" id="approval-queue-title">
                        <span>Pending Approval Queue</span>
                    </h2>
                    <p class="admin-tx-card-meta">Review requests submitted by students and faculty awaiting approval.</p>
                </div>
                <nav class="admin-tx-nav-tabs admin-queue-tabs" aria-label="Filter pending requests">
                    <a class="admin-tx-tab-btn <?= $filter === 'all' ? 'active' : '' ?>" href="<?= esc($queueUrl('all')) ?>">
                        <span>All Pending</span>
                        <span class="tab-count"><?= esc((string) $pendingCounts['all']) ?></span>
                    </a>
                    <a class="admin-tx-tab-btn <?= $filter === 'faculty' ? 'active' : '' ?>" href="<?= esc($queueUrl('faculty')) ?>">
                        <span>Faculty</span>
                        <span class="tab-count"><?= esc((string) $pendingCounts['faculty']) ?></span>
                    </a>
                    <a class="admin-tx-tab-btn <?= $filter === 'students' ? 'active' : '' ?>" href="<?= esc($queueUrl('students')) ?>">
                        <span>Students</span>
                        <span class="tab-count"><?= esc((string) $pendingCounts['students']) ?></span>
                    </a>
                </nav>
            </div>

            <div class="table-responsive">
                <table class="table admin-tx-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Requester</th>
                            <th scope="col">Equipment</th>
                            <th scope="col">Reservation Date</th>
                            <th scope="col">Schedule</th>
                            <th scope="col">Purpose</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingReservations)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="admin-tx-empty">
                                        <i class="bi bi-inbox" aria-hidden="true"></i>
                                        <h5>No Pending Reservations</h5>
                                        <p>All reservation requests have been processed.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: foreach ($pendingReservations as $res): ?>
                            <?php
                                $requesterName = trim((string) ($res['first_name'] ?? '') . ' ' . (string) ($res['last_name'] ?? '')) ?: (string) $res['id_number'];
                                $requesterRole = strtoupper((string) ($res['requester_role'] ?? '')) === 'STAFF' ? 'Faculty / Staff' : 'Student';
                                $initial = strtoupper(substr($res['first_name'] ?? $res['id_number'] ?? 'U', 0, 1));
                            ?>
                            <tr>
                                <td>
                                    <div class="admin-user-cell">
                                        <span class="admin-user-avatar"><?= esc($initial) ?></span>
                                        <div class="admin-user-info">
                                            <strong><?= esc($requesterName) ?></strong>
                                            <small><?= esc($requesterRole) ?> · ID <?= esc($res['id_number']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="admin-equipment-info">
                                        <strong><?= esc($res['equipment_name']) ?></strong>
                                        <small><?= esc($res['equipment_code']) ?></small>
                                    </div>
                                </td>
                                <td><?= esc($res['reservation_date']) ?></td>
                                <td>
                                    <div class="admin-schedule-info">
                                        <span><?= esc($res['start_time']) ?> – <?= esc($res['end_time']) ?></span>
                                    </div>
                                </td>
                                <td><?= esc($res['purpose']) ?></td>
                                <td>
                                    <span class="admin-badge-status badge-pending">
                                        <i></i><?= esc(ucfirst(strtolower($res['status']))) ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <form class="d-inline" method="post" action="<?= base_url('admin/reservations/' . (int) $res['reservation_id'] . '/status/approved') ?>">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-success btn-sm me-1"><i class="bi bi-check-lg me-1"></i>Approve</button>
                                    </form>
                                    <form class="d-inline" method="post" action="<?= base_url('admin/reservations/' . (int) $res['reservation_id'] . '/status/rejected') ?>">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg me-1"></i>Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
            <footer class="admin-approval-footer">
                <p>Showing <?= $from ?> to <?= $to ?> of <?= $totalPending ?> pending requests</p>
                <?= $pager->links('pending_approvals', 'catalog_full') ?>
            </footer>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
