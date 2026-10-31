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
    <title>Admin Dashboard - CPC Equipment Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/catalog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/admin-dashboard.css') ?>" rel="stylesheet">
</head>
<body class="dashboard-page admin-dashboard-page">
    <aside class="dashboard-sidebar">
        <a href="<?= base_url('admin') ?>" class="dashboard-brand">
            <span class="dashboard-brand-icon"><i class="bi bi-building-gear" aria-hidden="true"></i></span>
            <span><strong>CPC</strong><small>Equipment Tracker</small></span>
        </a>
        <nav class="dashboard-nav" aria-label="Admin navigation">
            <a href="<?= base_url('admin') ?>" class="dashboard-nav-link active" aria-current="page"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span>Dashboard</span></a>
            <a href="<?= base_url('equipment') ?>" class="dashboard-nav-link"><i class="bi bi-archive" aria-hidden="true"></i><span>Equipment Inventory</span></a>
            <a href="<?= base_url('admin/reservations#transactions') ?>" class="dashboard-nav-link"><i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Transactions</span></a>
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
            <a class="catalog-stat catalog-stat-link" href="<?= base_url('equipment') . ($selectedDepartmentId !== null ? '?department_id=' . (int) $selectedDepartmentId : '') ?>" aria-label="View equipment inventory"><span class="catalog-stat-icon stat-blue"><i class="bi bi-box-seam" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Total Equipment</span><strong><?= esc((string) $equipmentStats['total']) ?></strong><small><?= esc($growthLabel) ?></small></div></a>
            <a class="catalog-stat catalog-stat-link" href="<?= base_url('admin/reservations') ?>" aria-label="View active reservations"><span class="catalog-stat-icon stat-green"><i class="bi bi-calendar-check" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Active Reservations</span><strong><?= esc((string) $equipmentStats['activeReservations']) ?></strong><small>pending or approved</small></div></a>
            <a class="catalog-stat catalog-stat-link" href="<?= base_url('admin/reservations#transactions') ?>" aria-label="View ongoing borrowings"><span class="catalog-stat-icon stat-amber"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Ongoing Borrowings</span><strong><?= esc((string) $equipmentStats['ongoingBorrowings']) ?></strong><small><?= esc((string) $equipmentStats['dueToday']) ?> due today</small></div></a>
            <a class="catalog-stat catalog-stat-link" href="<?= base_url('admin/reservations#transactions') ?>" aria-label="View pending returns"><span class="catalog-stat-icon stat-red"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span><div><span class="catalog-stat-label">Pending Returns</span><strong><?= esc((string) $equipmentStats['pendingReturns']) ?></strong><small class="admin-overdue-note"><?= esc((string) $equipmentStats['overdueReturns']) ?> overdue</small></div></a>
        </section>

        <section class="admin-approval-card" aria-labelledby="approval-queue-title">
            <div class="admin-approval-header">
                <div><h1 id="approval-queue-title">Pending Approval Queue</h1><p>Review and process reservation requests.</p></div>
                <nav class="admin-queue-tabs" aria-label="Filter pending requests">
                    <a class="<?= $filter === 'all' ? 'active' : '' ?>" href="<?= esc($queueUrl('all')) ?>">All Pending <span><?= esc((string) $pendingCounts['all']) ?></span></a>
                    <a class="<?= $filter === 'faculty' ? 'active' : '' ?>" href="<?= esc($queueUrl('faculty')) ?>">Faculty <span><?= esc((string) $pendingCounts['faculty']) ?></span></a>
                    <a class="<?= $filter === 'students' ? 'active' : '' ?>" href="<?= esc($queueUrl('students')) ?>">Students <span><?= esc((string) $pendingCounts['students']) ?></span></a>
                </nav>
            </div>

            <div class="table-responsive">
                <table class="table table-striped admin-approval-table align-middle mb-0">
                    <thead><tr><th scope="col">User</th><th scope="col">Equipment</th><th scope="col">Date</th><th scope="col">Time</th><th scope="col">Purpose</th><th scope="col">Status</th><th scope="col">Actions</th></tr></thead>
                    <tbody>
                        <?php if (empty($pendingReservations)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No reservations found.</td></tr>
                        <?php else: foreach ($pendingReservations as $reservation): ?>
                            <?php
                                $requesterName = trim((string) ($reservation['first_name'] ?? '') . ' ' . (string) ($reservation['last_name'] ?? '')) ?: (string) $reservation['id_number'];
                                $requesterRole = strtoupper((string) ($reservation['requester_role'] ?? '')) === 'STAFF' ? 'Faculty / Staff' : 'Student';
                            ?>
                            <tr>
                                <td><strong class="admin-requester-name"><?= esc($requesterName) ?></strong><small class="admin-requester-meta"><?= esc($requesterRole) ?> · ID <?= esc($reservation['id_number']) ?></small></td>
                                <td><strong class="admin-equipment-name"><?= esc($reservation['equipment_name']) ?></strong><small class="admin-equipment-code"><?= esc($reservation['equipment_code']) ?></small></td>
                                <td><?= esc($reservation['reservation_date']) ?></td>
                                <td><?= esc($reservation['start_time']) ?>–<?= esc($reservation['end_time']) ?></td>
                                <td><?= esc($reservation['purpose']) ?></td>
                                <td><?= esc($reservation['status']) ?></td>
                                <td class="text-end text-nowrap">
                                    <?php foreach (['APPROVED' => 'success', 'REJECTED' => 'danger'] as $status => $style): ?>
                                        <form class="d-inline" method="post" action="<?= base_url('admin/reservations/' . (int) $reservation['reservation_id'] . '/status/' . strtolower($status)) ?>">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-<?= $style ?> btn-sm"><?= ucfirst(strtolower($status)) ?></button>
                                        </form>
                                    <?php endforeach; ?>
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
