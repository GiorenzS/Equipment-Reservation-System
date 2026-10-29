<?php
/**
 * @var array $reservations
 * @var array $departmentList
 * @var int|null $selectedDepartmentId
 * @var string $userName
 * @var string $userRole
 * @var string $search
 * @var string $statusFilter
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
$total = (int) ($paginationDetails['total'] ?? 0);
$currentPage = (int) ($paginationDetails['currentPage'] ?? 1);
$perPage = (int) ($paginationDetails['perPage'] ?? 8);
$from = $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
$to = min($currentPage * $perPage, $total);
$baseQuery = $selectedDepartmentId !== null ? ['department_id' => $selectedDepartmentId] : [];
if ($search !== '') {
    $baseQuery['search'] = $search;
}
if ($statusFilter !== '') {
    $baseQuery['status'] = $statusFilter;
}
$reservationUrl = base_url('reservations') . ($baseQuery === [] ? '' : '?' . http_build_query($baseQuery));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - CPC Equipment Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/catalog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/reservations.css') ?>" rel="stylesheet">
</head>
<body class="dashboard-page reservations-page">
    <aside class="dashboard-sidebar">
        <a href="<?= base_url('dashboard') ?>" class="dashboard-brand">
            <span class="dashboard-brand-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
            <span><strong>CPC</strong><small>Equipment Reservation</small></span>
        </a>
        <nav class="dashboard-nav" aria-label="Main navigation">
            <a href="<?= base_url('dashboard') ?>" class="dashboard-nav-link"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span>Dashboard</span></a>
            <a href="<?= base_url('equipment') ?>" class="dashboard-nav-link"><i class="bi bi-archive" aria-hidden="true"></i><span>Equipment Catalog</span></a>
            <a href="<?= base_url('reservations') ?>" class="dashboard-nav-link active" aria-current="page"><i class="bi bi-calendar3" aria-hidden="true"></i><span>My Reservations</span></a>
        </nav>
        <a href="<?= base_url('logout') ?>" class="dashboard-logout"><i class="bi bi-box-arrow-left" aria-hidden="true"></i><span>Logout</span></a>
    </aside>

    <main class="reservations-main">
        <header class="dashboard-topbar">
            <form class="dashboard-global-search" method="get" action="<?= base_url('reservations') ?>">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" name="search" value="<?= esc($search) ?>" placeholder="Search equipment by name, code, brand or category..." aria-label="Search reservations">
                <?php if ($selectedDepartmentId !== null): ?><input type="hidden" name="department_id" value="<?= (int) $selectedDepartmentId ?>"><?php endif; ?>
                <?php if ($statusFilter !== ''): ?><input type="hidden" name="status" value="<?= esc($statusFilter) ?>"><?php endif; ?>
            </form>

            <div class="dropdown dashboard-department-dropdown">
                <button class="dashboard-profile dashboard-department-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Choose department">
                    <span class="dashboard-department-name"><?= esc($selectedDepartmentName) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dashboard-profile-menu dashboard-department-menu">
                    <?php if (empty($departmentList)): ?>
                        <li><span class="dropdown-item-text">No departments available</span></li>
                    <?php else: ?>
                        <?php foreach ($departmentList as $department): ?>
                            <?php
                                $departmentQuery = ['department_id' => (int) $department['department_id']];
                                if ($search !== '') {
                                    $departmentQuery['search'] = $search;
                                }
                                if ($statusFilter !== '') {
                                    $departmentQuery['status'] = $statusFilter;
                                }
                            ?>
                            <li><a class="dropdown-item <?= (int) $selectedDepartmentId === (int) $department['department_id'] ? 'active' : '' ?>" href="<?= base_url('reservations?' . http_build_query($departmentQuery)) ?>"><?= esc($department['department_name']) ?></a></li>
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

        <section class="reservation-banner" aria-labelledby="reservations-title">
            <div>
                <span class="reservation-banner-icon"><i class="bi bi-calendar2-check" aria-hidden="true"></i></span>
                <div>
                    <h1 id="reservations-title">My Reservations</h1>
                    <p>View and manage your equipment reservation requests.</p>
                </div>
            </div>
            <a href="<?= base_url('equipment/catalog') ?>" class="btn btn-primary reservation-new-button"><i class="bi bi-plus-lg" aria-hidden="true"></i>New Reservation</a>
        </section>

        <section class="reservation-history" aria-labelledby="reservation-history-title">
            <div class="reservation-history-heading">
                <h2 id="reservation-history-title">Reservation History</h2>
                <form class="reservation-filter-form" method="get" action="<?= base_url('reservations') ?>">
                    <?php if ($selectedDepartmentId !== null): ?><input type="hidden" name="department_id" value="<?= (int) $selectedDepartmentId ?>"><?php endif; ?>
                    <label class="reservation-search-field"><i class="bi bi-search" aria-hidden="true"></i><input type="search" name="search" value="<?= esc($search) ?>" placeholder="Search reservations..." aria-label="Search reservations"></label>
                    <select class="form-select" name="status" aria-label="Filter by status" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <?php foreach (['PENDING' => 'Pending', 'APPROVED' => 'Approved', 'CHECKED_OUT' => 'Checked Out', 'RETURNED' => 'Returned', 'REJECTED' => 'Rejected', 'CANCELLED' => 'Cancelled'] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $statusFilter === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn reservation-filter-button" aria-label="Apply reservation filters"><i class="bi bi-sliders2" aria-hidden="true"></i></button>
                </form>
            </div>

            <div class="reservation-table-card">
                <div class="table-responsive">
                    <table class="table reservation-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Equipment</th>
                                <th scope="col">Reservation Date</th>
                                <th scope="col">Time</th>
                                <th scope="col">Purpose</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reservations)): ?>
                                <tr><td colspan="6" class="reservation-empty"><i class="bi bi-calendar-x" aria-hidden="true"></i><strong>No reservations found</strong><span>Browse the Equipment Catalog to make your first reservation.</span><a href="<?= base_url('equipment/catalog') ?>" class="btn btn-primary btn-sm">Browse Equipment</a></td></tr>
                            <?php else: ?>
                                <?php foreach ($reservations as $res): ?>
                                    <?php
                                        $status = strtoupper((string) ($res['status'] ?? 'PENDING'));
                                        $statusClass = match ($status) {
                                            'APPROVED' => 'reservation-status-approved',
                                            'CHECKED_OUT' => 'reservation-status-checked-out',
                                            'RETURNED' => 'reservation-status-returned',
                                            'REJECTED' => 'reservation-status-rejected',
                                            'CANCELLED' => 'reservation-status-cancelled',
                                            default => 'reservation-status-pending',
                                        };
                                        $equipmentLabel = trim((string) ($res['equipment_name'] ?? 'Equipment') . ' ' . (string) ($res['brand'] ?? '') . ' ' . (string) ($res['model'] ?? ''));
                                    ?>
                                    <tr>
                                        <td><strong class="reservation-equipment-name"><?= esc($equipmentLabel) ?></strong><span class="reservation-equipment-category"><?= esc($res['category_name'] ?? 'Uncategorized') ?></span></td>
                                        <td><?= esc(date('M j, Y', strtotime((string) $res['reservation_date']))) ?></td>
                                        <td><?= esc(date('g:i A', strtotime((string) $res['start_time']))) ?> <span class="reservation-time-separator"> - </span> <?= esc(date('g:i A', strtotime((string) $res['end_time']))) ?></td>
                                        <td><span class="reservation-purpose" title="<?= esc($res['purpose']) ?>"><?= esc($res['purpose']) ?></span></td>
                                        <td><span class="reservation-status <?= $statusClass ?>"><?= esc(ucfirst(strtolower($status))) ?></span></td>
                                        <td class="text-end"><a href="<?= base_url('reservations/' . (int) $res['reservation_id']) ?>" class="btn reservation-view-button" aria-label="View reservation <?= (int) $res['reservation_id'] ?>"><i class="bi bi-eye" aria-hidden="true"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <footer class="catalog-pagination-footer">
                <p>Showing <?= $from ?>-<?= $to ?> of <?= $total ?> reservations</p>
                <?= $pager->links('reservations', 'catalog_full') ?>
            </footer>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
