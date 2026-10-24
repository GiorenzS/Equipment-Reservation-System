<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - Equipment Reservation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: system-ui, -apple-system, sans-serif;
            color: #1e293b;
        }
        .navbar-custom {
            background: #0f172a;
        }
        .card {
            border: 0;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= base_url('/') ?>">
                <i class="bi bi-box-seam me-2"></i> Equipment Tracker
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('equipment') ?>">
                            <i class="bi bi-tools me-1"></i> Equipment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('reservations') ?>">
                            <i class="bi bi-calendar-check me-1"></i> My Reservations
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3 text-light">
                    <span class="small">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= esc(session()->get('full_name') ?: session()->get('id_number')) ?>
                        <span class="badge bg-secondary ms-1"><?= esc(session()->get('role')) ?></span>
                    </span>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4 py-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">My Reservations</h3>
                <p class="text-muted small mb-0">Track and manage your equipment reservation requests</p>
            </div>
            <a href="<?= base_url('equipment') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> New Reservation
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Equipment</th>
                            <th scope="col">Date</th>
                            <th scope="col">Time Window</th>
                            <th scope="col">Purpose</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Requested On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservations)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-2 d-block mb-2 text-secondary"></i>
                                    You have no active or past reservations yet.
                                    <div class="mt-2">
                                        <a href="<?= base_url('equipment') ?>" class="btn btn-outline-primary btn-sm">
                                            Browse Available Equipment
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $res): ?>
                                <tr>
                                    <td class="text-muted small">#<?= esc($res['reservation_id']) ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($res['equipment_name'] ?? 'Equipment #' . $res['equipment_id']) ?></div>
                                        <small class="text-muted font-monospace"><?= esc($res['equipment_code'] ?? '') ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            <i class="bi bi-calendar3 me-1 text-secondary"></i>
                                            <?= esc(date('M d, Y', strtotime($res['reservation_date']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-clock me-1 text-secondary"></i>
                                            <?= esc(date('h:i A', strtotime($res['start_time']))) ?> - <?= esc(date('h:i A', strtotime($res['end_time']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-secondary" title="<?= esc($res['purpose']) ?>">
                                            <?= esc(strlen($res['purpose'] ?? '') > 40 ? substr($res['purpose'], 0, 40) . '...' : ($res['purpose'] ?? '')) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                            $st = strtoupper($res['status'] ?? 'PENDING');
                                            $badgeClass = match ($st) {
                                                'APPROVED'  => 'bg-success-subtle text-success border border-success-subtle',
                                                'PENDING'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                'REJECTED'  => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                'CANCELLED' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                                default     => 'bg-light text-dark border',
                                            };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= esc($st) ?></span>
                                    </td>
                                    <td class="text-end text-muted small">
                                        <?= esc(date('M d, Y', strtotime($res['created_at'] ?? 'now'))) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
