<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Reservations</h1>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-secondary btn-sm">Log out</a>
        </div>

        <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $class): ?>
            <?php if (session()->getFlashdata($key)): ?>
                <div class="alert alert-<?= $class ?>"><?= esc(session()->getFlashdata($key)) ?></div>
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="card mb-4">
            <div class="card-header fw-semibold">Pending approvals</div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead><tr><th>User</th><th>Equipment</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No reservations found.</td></tr>
                    <?php else: foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?= esc(trim(($reservation['first_name'] ?? '') . ' ' . ($reservation['last_name'] ?? '')) ?: $reservation['id_number']) ?></td>
                            <td><?= esc($reservation['equipment_name']) ?> <small class="text-muted">(<?= esc($reservation['equipment_code']) ?>)</small></td>
                            <td><?= esc($reservation['reservation_date']) ?></td>
                            <td><?= esc($reservation['start_time']) ?>–<?= esc($reservation['end_time']) ?></td>
                            <td><?= esc($reservation['purpose']) ?></td>
                            <td><?= esc($reservation['status']) ?></td>
                            <td class="text-nowrap">
                            <?php if (strtoupper($reservation['status']) === 'PENDING'): ?>
                                <?php foreach (['APPROVED' => 'success', 'REJECTED' => 'danger'] as $status => $style): ?>
                                    <form class="d-inline" method="post" action="<?= base_url('admin/reservations/' . $reservation['reservation_id'] . '/status/' . strtolower($status)) ?>">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-<?= $style ?> btn-sm"><?= ucfirst(strtolower($status)) ?></button>
                                    </form>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header fw-semibold">Approved reservations awaiting checkout</div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead><tr><th>User</th><th>Equipment</th><th>Date</th><th>Time</th><th>Purpose</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php if (empty($approvedReservations)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No approved reservations are waiting for checkout.</td></tr>
                    <?php else: foreach ($approvedReservations as $reservation): ?>
                        <tr>
                            <td><?= esc(trim(($reservation['first_name'] ?? '') . ' ' . ($reservation['last_name'] ?? '')) ?: $reservation['id_number']) ?></td>
                            <td><?= esc($reservation['equipment_name']) ?> <small class="text-muted">(<?= esc($reservation['equipment_code']) ?>)</small></td>
                            <td><?= esc($reservation['reservation_date']) ?></td>
                            <td><?= esc($reservation['start_time']) ?>–<?= esc($reservation['end_time']) ?></td>
                            <td><?= esc($reservation['purpose']) ?></td>
                            <td>
                            <?php if (strtoupper($reservation['equipment_status']) === 'AVAILABLE'): ?>
                                <form method="post" action="<?= base_url('admin/reservations/' . $reservation['reservation_id'] . '/checkout') ?>">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-primary btn-sm">Check out</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">Equipment unavailable</span>
                            <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-semibold">Checked out equipment</div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead><tr><th>User</th><th>Equipment</th><th>Borrowed</th><th>Expected return</th><th>Return</th></tr></thead>
                    <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No active checkouts.</td></tr>
                    <?php else: foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= esc(trim(($transaction['first_name'] ?? '') . ' ' . ($transaction['last_name'] ?? '')) ?: $transaction['id_number']) ?></td>
                            <td><?= esc($transaction['equipment_name']) ?> <small class="text-muted">(<?= esc($transaction['equipment_code']) ?>)</small></td>
                            <td><?= esc($transaction['borrow_date']) ?></td>
                            <td><?= esc($transaction['expected_return_date']) ?></td>
                            <td>
                                <form class="d-flex gap-2" method="post" action="<?= base_url('admin/transactions/' . $transaction['transaction_id'] . '/return') ?>">
                                    <?= csrf_field() ?>
                                    <select class="form-select form-select-sm" name="condition_in" required>
                                        <option value="">Condition</option>
                                        <?php foreach (['GOOD', 'FAIR', 'DAMAGED'] as $condition): ?>
                                            <option value="<?= $condition ?>"><?= ucfirst(strtolower($condition)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-success btn-sm">Return</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
