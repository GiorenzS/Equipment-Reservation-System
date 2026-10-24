<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment List - Equipment Reservation System</title>
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
        .equipment-thumbnail {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            object-fit: cover;
            background: #0f172a;
            border: 1px solid #e2e8f0;
            display: block;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .equipment-thumb-link {
            display: inline-block;
            text-decoration: none;
        }
        .equipment-thumb-link:hover .equipment-thumbnail {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= base_url('/') ?>">
                <i class="bi bi-box-seam me-2 text-indigo"></i> Equipment Tracker
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('equipment') ?>">
                            <i class="bi bi-tools me-1"></i> Equipment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('reservations') ?>">
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
                <h3 class="fw-bold mb-1">Equipment Catalog</h3>
                <p class="text-muted small mb-0">Browse and reserve available laboratory and campus equipment</p>
            </div>
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
                            <th scope="col" style="width: 64px;">Photo</th>
                            <th scope="col">Code</th>
                            <th scope="col">Equipment Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Brand / Model</th>
                            <th scope="col">Status</th>
                            <th scope="col">Condition</th>
                            <th scope="col" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipment)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    No equipment records found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($equipment as $item): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('equipment/' . $item['equipment_id']) ?>" class="equipment-thumb-link" title="View details">
                                            <img src="<?= esc(equipment_image_url($item['image'] ?? null)) ?>"
                                                 alt="<?= esc($item['equipment_name']) ?>"
                                                 class="equipment-thumbnail shadow-sm">
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">
                                            <?= esc($item['equipment_code']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($item['equipment_name']) ?></div>
                                        <?php if (! empty($item['serial_number'])): ?>
                                            <small class="text-muted">SN: <?= esc($item['serial_number']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                            <?= esc($item['category_name'] ?? 'Uncategorized') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= esc(trim(($item['brand'] ?? '') . ' ' . ($item['model'] ?? '')) ?: '—') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                            $status = strtoupper($item['status'] ?? 'AVAILABLE');
                                            $badgeClass = match ($status) {
                                                'AVAILABLE' => 'bg-success-subtle text-success border border-success-subtle',
                                                'IN_USE'    => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                'RESERVED'  => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                                default     => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                            };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= esc($status) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                            $cond = strtoupper($item['condition_status'] ?? 'GOOD');
                                            $condClass = match ($cond) {
                                                'GOOD'    => 'text-success',
                                                'FAIR'    => 'text-warning-emphasis',
                                                'DAMAGED' => 'text-danger',
                                                default   => 'text-muted',
                                            };
                                        ?>
                                        <small class="fw-semibold <?= $condClass ?>">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                            <?= esc($cond) ?>
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= base_url('equipment/' . $item['equipment_id']) ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i> View
                                        </a>
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
