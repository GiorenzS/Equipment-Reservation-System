<?php
/**
 * @var array $departmentList
 * @var int|null $selectedDepartmentId
 * @var string $userName
 * @var string $userRole
 * @var string $search
 * @var array $reservations
 * @var array $approvedReservations
 * @var array $transactions
 * @var array $returnedHistory
 * @var array $overdueTransactions
 * @var array $counts
 */

$selectedDepartmentName = 'Select Department';
foreach ($departmentList as $department) {
    if ((int) $department['department_id'] === (int) $selectedDepartmentId) {
        $selectedDepartmentName = $department['department_name'];
        break;
    }
}

$now = date('Y-m-d H:i:s');

$allowedQueues = ['all', 'pending', 'checkout', 'active', 'overdue', 'returned'];

$selectedQueue = service('request')->getGet('queue') ?? 'all';

if (!in_array($selectedQueue, $allowedQueues, true)) {
    $selectedQueue = 'all';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions &amp; Reservations - CPC Equipment Reservation </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/catalog.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/admin-dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/admin-transactions.css') ?>" rel="stylesheet">
</head>
<body class="dashboard-page admin-dashboard-page admin-tx-page">
    <aside class="dashboard-sidebar">
        <a href="<?= base_url('admin') ?>" class="dashboard-brand">
            <span class="dashboard-brand-icon"><img src="<?= base_url('assets/cpc-logo.jpg') ?>" alt="CPC Logo"></span>
            <span><strong>CPC</strong><small>Equipment Reservation</small></span>
        </a>
        <nav class="dashboard-nav" aria-label="Admin navigation">
            <a href="<?= base_url('admin') ?>" class="dashboard-nav-link"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i><span>Dashboard</span></a>
            <a href="<?= base_url('admin/equipment') ?>" class="dashboard-nav-link"><i class="bi bi-archive" aria-hidden="true"></i><span>Equipment Inventory</span></a>
            <a href="<?= base_url('admin/transactions') ?>" class="dashboard-nav-link active" aria-current="page"><i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Transactions</span></a>
        </nav>
        <a href="<?= base_url('logout') ?>" class="dashboard-logout"><i class="bi bi-box-arrow-left" aria-hidden="true"></i><span>Logout</span></a>
    </aside>

    <main class="dashboard-main admin-dashboard-main">
        <header class="dashboard-topbar">
            <form class="dashboard-global-search" method="get" action="<?= base_url('admin/transactions') ?>">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" name="search" value="<?= esc($search) ?>" placeholder="Search transactions, reservations, equipment, or borrowers..." aria-label="Search transactions">
                <?php if ($selectedDepartmentId !== null): ?><input type="hidden" name="department_id" value="<?= (int) $selectedDepartmentId ?>"><?php endif; ?>
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
                            $departmentQuery = ['department_id' => (int) $department['department_id']];
                            if ($search !== '') {
                                $departmentQuery['search'] = $search;
                            }
                        ?>
                        <li><a class="dropdown-item <?= (int) $selectedDepartmentId === (int) $department['department_id'] ? 'active' : '' ?>" href="<?= base_url('admin/transactions?' . http_build_query($departmentQuery)) ?>"><?= esc($department['department_name']) ?></a></li>
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

        <!-- Banner Card -->
        <section class="admin-tx-banner" aria-labelledby="tx-page-title">
            <div class="admin-tx-banner-content">
                <span class="admin-tx-banner-icon"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></span>
                <div>
                    <h1 id="tx-page-title">Transactions &amp; Reservations</h1>
                    <p>Review pending approvals, process checkouts, and record equipment returns.</p>
                </div>
            </div>
            <div class="admin-tx-summary-pills">
                <a href="#pending-section" class="admin-tx-pill" data-tab="pending">
                    <span>Pending Approvals</span>
                </a>
                <a href="#checkout-section" class="admin-tx-pill" data-tab="checkout">
                    <span>Awaiting Checkout</span>
                </a>
                <a href="#active-section" class="admin-tx-pill" data-tab="active">
                    <span>Active Checkouts</span>
                </a>
                <a href="#overdue-section" class="admin-tx-pill" data-tab="overdue">
                    <span>Pending Returns</span>
                </a>
            </div>
        </section>

            <!-- Queue Tabs -->
            <div class="admin-tx-tabs-row">
                <nav class="admin-tx-nav-tabs" aria-label="Transaction queues navigation">

                    <button type="button"
                            class="admin-tx-tab-btn <?= $selectedQueue === 'all' ? 'active' : '' ?>"
                            data-filter-queue="all">
                        <span>All Queues</span>
                    </button>

                    <button type="button"
                            class="admin-tx-tab-btn <?= $selectedQueue === 'pending' ? 'active' : '' ?>"
                            data-filter-queue="pending">
                        <span>Pending Approvals</span>
                        <span class="tab-count"><?= (int) ($counts['pending'] ?? 0) ?></span>
                    </button>

                    <button type="button"
                            class="admin-tx-tab-btn <?= $selectedQueue === 'checkout' ? 'active' : '' ?>"
                            data-filter-queue="checkout">
                        <span>Awaiting Checkout</span>
                        <span class="tab-count"><?= (int) ($counts['checkout'] ?? 0) ?></span>
                    </button>

                    <button type="button"
                            class="admin-tx-tab-btn <?= $selectedQueue === 'active' ? 'active' : '' ?>"
                            data-filter-queue="active">
                        <span>Active Borrowings</span>
                        <span class="tab-count"><?= (int) ($counts['active'] ?? 0) ?></span>
                    </button>

                    <button type="button"
                            class="admin-tx-tab-btn <?= $selectedQueue === 'overdue' ? 'active' : '' ?>"
                            data-filter-queue="overdue">
                        <span>Pending Returns</span>
                        <span class="tab-count"><?= (int) ($counts['overdue'] ?? 0) ?></span>
                    </button>

                    <button type="button"
                            class="admin-tx-tab-btn <?= $selectedQueue === 'returned' ? 'active' : '' ?>"
                            data-filter-queue="returned">
                        <span>Return History</span>
                        <span class="tab-count"><?= (int) ($counts['returned'] ?? 0) ?></span>
                    </button>

                </nav>
            </div>
        <!-- 1. Pending Approvals Queue -->
        <section class="admin-tx-card" id="pending-section" data-queue="pending">
            <div class="admin-tx-card-header">
                <div>
                    <h2 class="admin-tx-card-title title-pending">
                        <span>Pending Approval Queue</span>
                    </h2>
                    <p class="admin-tx-card-meta">Review requests submitted by students and faculty awaiting approval.</p>
                </div>
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
                        <?php if (empty($reservations)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="admin-tx-empty">
                                        <i class="bi bi-inbox" aria-hidden="true"></i>
                                        <h5>No Pending Reservations</h5>
                                        <p>All reservation requests have been processed.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: foreach ($reservations as $res): ?>
                            <?php
                                $requesterName = trim((string) ($res['first_name'] ?? '') . ' ' . (string) ($res['last_name'] ?? '')) ?: (string) $res['id_number'];
                            ?>
                            <tr>
                                <td>
                                    <div class="admin-user-cell">
                                        <div class="admin-user-info">
                                            <strong><?= esc($requesterName) ?></strong>
                                            <small><?= esc($res['id_number']) ?></small>
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
        </section>

        <!-- 2. Approved Reservations Awaiting Checkout -->
        <section class="admin-tx-card" id="checkout-section" data-queue="checkout">
            <div class="admin-tx-card-header">
                <div>
                    <h2 class="admin-tx-card-title title-checkout">
                        <span>Approved Reservations Awaiting Checkout</span>
                    </h2>
                    <p class="admin-tx-card-meta">Borrowers with approved reservations ready for asset release and checkout.</p>
                </div>
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
                            <th scope="col">Item Status</th>
                            <th scope="col" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($approvedReservations)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="admin-tx-empty">
                                        <i class="bi bi-calendar-check" aria-hidden="true"></i>
                                        <h5>No Approved Checkouts Pending</h5>
                                        <p>There are no approved reservations waiting for checkout at this moment.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: foreach ($approvedReservations as $res): ?>
                            <?php
                                $requesterName = trim((string) ($res['first_name'] ?? '') . ' ' . (string) ($res['last_name'] ?? '')) ?: (string) $res['id_number'];
                                $isAvailable = strtoupper((string) ($res['equipment_status'] ?? '')) === 'AVAILABLE';
                            ?>
                            <tr>
                                <td>
                                    <div class="admin-user-cell">
                                        <div class="admin-user-info">
                                            <strong><?= esc($requesterName) ?></strong>
                                            <small><?= esc($res['id_number']) ?></small>
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
                                    <?php if ($isAvailable): ?>
                                        <span class="admin-badge-status badge-returned"><i class="bg-success"></i>Available</span>
                                    <?php else: ?>
                                        <span class="admin-badge-status badge-overdue"><i class="bg-danger"></i>In Use</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <?php if ($isAvailable): ?>
                                        <form method="post" action="<?= base_url('admin/reservations/' . (int) $res['reservation_id'] . '/checkout') ?>">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-primary btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Check Out</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled title="Equipment is currently checked out by another borrower">Unavailable</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 3. Active Checkouts & Returns -->
        <section class="admin-tx-card" id="active-section" data-queue="active">
            <div class="admin-tx-card-header">
                <div>
                    <h2 class="admin-tx-card-title title-active">
                        <span>Active Checkouts &amp; Returns</span>
                    </h2>
                    <p class="admin-tx-card-meta">Equipment currently checked out and in custody of borrowers.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table admin-tx-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Borrower</th>
                            <th scope="col">Equipment</th>
                            <th scope="col">Condition Out</th>
                            <th scope="col">Borrowed Time</th>
                            <th scope="col">Expected Return</th>
                            <th scope="col" class="text-end">Record Return</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="admin-tx-empty">
                                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                                        <h5>No Active Checkouts</h5>
                                        <p>All borrowed equipment has been returned to inventory.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: foreach ($transactions as $tx): ?>
                            <?php
                                $borrowerName = trim((string) ($tx['first_name'] ?? '') . ' ' . (string) ($tx['last_name'] ?? '')) ?: (string) $tx['id_number'];
                                $isOverdue = ! empty($tx['expected_return_date']) && strtotime($tx['expected_return_date']) < strtotime($now);
                            ?>
                            <tr>
                                <td>
                                    <div class="admin-user-cell">
                                        <div class="admin-user-info">
                                            <strong><?= esc($borrowerName) ?></strong>
                                            <small><?= esc($tx['id_number']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="admin-equipment-info">
                                        <strong><?= esc($tx['equipment_name']) ?></strong>
                                        <small><?= esc($tx['equipment_code']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= esc($tx['condition_out'] ?? 'GOOD') ?></span>
                                </td>
                                <td><?= esc($tx['borrow_date']) ?></td>
                                <td>
                                    <span><?= esc($tx['expected_return_date']) ?></span>
                                    <?php if ($isOverdue): ?>
                                        <span class="admin-badge-status badge-overdue ms-1"><i class="bg-danger"></i>Overdue</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form class="admin-return-form justify-content-end" method="post" action="<?= base_url('admin/transactions/' . (int) $tx['transaction_id'] . '/return') ?>">
                                        <?= csrf_field() ?>
                                        <select class="form-select admin-return-select" name="condition_in" required aria-label="Return condition">
                                            <option value="">Condition...</option>
                                            <option value="GOOD">Good</option>
                                            <option value="FAIR">Fair</option>
                                            <option value="DAMAGED">Damaged</option>
                                        </select>
                                        <button class="btn btn-success admin-return-btn">
                                            <i class="bi bi-box-arrow-in-left" aria-hidden="true"></i>Return
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 4. Pending Returns — Overdue -->
        <section class="admin-tx-card" id="overdue-section" data-queue="overdue">
            <div class="admin-tx-card-header">
                <div>
                    <h2 class="admin-tx-card-title title-overdue">
                        <span>Pending Returns &ndash; Overdue</span>
                    </h2>
                    <p class="admin-tx-card-meta">Items past their expected return date still in borrower custody.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table admin-tx-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Borrower</th>
                            <th scope="col">Equipment</th>
                            <th scope="col">Condition Out</th>
                            <th scope="col">Borrowed Time</th>
                            <th scope="col">Expected Return</th>
                            <th scope="col" class="text-end">Record Return</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($overdueTransactions)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="admin-tx-empty">
                                        <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                        <h5>No Overdue Items</h5>
                                        <p>All borrowed equipment has been returned on time.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: foreach ($overdueTransactions as $tx): ?>
                            <?php
                                $borrowerName = trim((string) ($tx['first_name'] ?? '') . ' ' . (string) ($tx['last_name'] ?? '')) ?: (string) $tx['id_number'];
                            ?>
                            <tr class="table-danger-row">
                                <td>
                                    <div class="admin-user-cell">
                                        <div class="admin-user-info">
                                            <strong><?= esc($borrowerName) ?></strong>
                                            <small><?= esc($tx['id_number']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="admin-equipment-info">
                                        <strong><?= esc($tx['equipment_name']) ?></strong>
                                        <small><?= esc($tx['equipment_code']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= esc($tx['condition_out'] ?? 'GOOD') ?></span>
                                </td>
                                <td><?= esc($tx['borrow_date']) ?></td>
                                <td>
                                    <span class="text-danger fw-semibold"><?= esc($tx['expected_return_date']) ?></span>
                                    <span class="admin-badge-status badge-overdue ms-1"><i class="bg-danger"></i>Overdue</span>
                                </td>
                                <td class="text-end">
                                    <form class="admin-return-form justify-content-end" method="post" action="<?= base_url('admin/transactions/' . (int) $tx['transaction_id'] . '/return') ?>">
                                        <?= csrf_field() ?>
                                        <select class="form-select admin-return-select" name="condition_in" required aria-label="Return condition">
                                            <option value="">Condition...</option>
                                            <option value="GOOD">Good</option>
                                            <option value="FAIR">Fair</option>
                                            <option value="DAMAGED">Damaged</option>
                                        </select>
                                        <button class="btn btn-danger admin-return-btn">
                                            <i class="bi bi-box-arrow-in-left" aria-hidden="true"></i>Return
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 5. Recent Return History -->
        <section class="admin-tx-card" id="history-section" data-queue="returned">
            <div class="admin-tx-card-header">
                <div>
                    <h2 class="admin-tx-card-title title-returned">
                        <span>Recent Return History</span>
                    </h2>
                    <p class="admin-tx-card-meta">Recently returned equipment logged back into inventory.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table admin-tx-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Borrower</th>
                            <th scope="col">Equipment</th>
                            <th scope="col">Borrowed Date</th>
                            <th scope="col">Returned Date</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($returnedHistory)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="admin-tx-empty">
                                        <i class="bi bi-archive" aria-hidden="true"></i>
                                        <h5>No Return History Found</h5>
                                        <p>Completed equipment returns will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: foreach ($returnedHistory as $tx): ?>
                            <?php
                                $borrowerName = trim((string) ($tx['first_name'] ?? '') . ' ' . (string) ($tx['last_name'] ?? '')) ?: (string) $tx['id_number'];
                            ?>
                            <tr>
                                <td>
                                    <div class="admin-user-cell">
                                        <div class="admin-user-info">
                                            <strong><?= esc($borrowerName) ?></strong>
                                            <small><?= esc($tx['id_number']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="admin-equipment-info">
                                        <strong><?= esc($tx['equipment_name']) ?></strong>
                                        <small><?= esc($tx['equipment_code']) ?></small>
                                    </div>
                                </td>
                                <td><?= esc($tx['borrow_date']) ?></td>
                                <td><?= esc($tx['actual_return_date']) ?></td>
                                <td>
                                    <span class="admin-badge-status badge-returned"><i class="bg-success"></i>Returned</span>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Queue selected from the URL
        const initialQueue = <?= json_encode($selectedQueue) ?>;

        // Apply queue filter
        function applyQueueFilter(filter) {

            // Update active tab
            document.querySelectorAll('.admin-tx-tab-btn').forEach(btn => {
                btn.classList.toggle(
                    'active',
                    btn.getAttribute('data-filter-queue') === filter
                );
            });

            // Show only matching transaction card
            document.querySelectorAll('.admin-tx-card').forEach(card => {
                const cardQueue = card.getAttribute('data-queue');

                if (filter === 'all' || cardQueue === filter) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Queue tab click events
        document.querySelectorAll('.admin-tx-tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.getAttribute('data-filter-queue');
                applyQueueFilter(filter);
            });
        });

        // Banner pill click events
        document.querySelectorAll('.admin-tx-pill[data-tab]').forEach(pill => {
            pill.addEventListener('click', (e) => {
                e.preventDefault();

                const targetQueue = pill.getAttribute('data-tab');
                applyQueueFilter(targetQueue);

                // Scroll to the selected transaction section
                const targetSection = document.querySelector(
                    `.admin-tx-card[data-queue="${targetQueue}"]`
                );

                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // IMPORTANT: Apply the URL-selected queue when the page first loads
        applyQueueFilter(initialQueue);
    </script>
</body>
</html>

