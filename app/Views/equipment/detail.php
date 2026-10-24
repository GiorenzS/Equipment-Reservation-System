<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($item['equipment_name']) ?> - Equipment Details</title>
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
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= base_url('/') ?>">
                <i class="bi bi-box-seam me-2"></i> Equipment Tracker
            </a>
            <div class="d-flex align-items-center gap-3 text-light ms-auto">
                <span class="small">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= esc(session()->get('full_name') ?: session()->get('id_number')) ?>
                </span>
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4 py-lg-5" style="max-width: 860px;">
        <div class="mb-3">
            <a href="<?= base_url('equipment') ?>" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Equipment Catalog
            </a>
        </div>

        <div class="card p-4 p-md-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4 pb-3 border-bottom">
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2">
                        <?= esc($item['category_name'] ?? 'Uncategorized') ?>
                    </span>
                    <h3 class="fw-bold mb-1"><?= esc($item['equipment_name']) ?></h3>
                    <p class="text-muted small mb-0 font-monospace">Code: <?= esc($item['equipment_code']) ?></p>
                </div>
                <div>
                    <?php
                        $status = strtoupper($item['status'] ?? 'AVAILABLE');
                        $badgeClass = match ($status) {
                            'AVAILABLE' => 'bg-success text-white',
                            'IN_USE'    => 'bg-warning text-dark',
                            'RESERVED'  => 'bg-info text-dark',
                            default     => 'bg-secondary text-white',
                        };
                    ?>
                    <span class="badge <?= $badgeClass ?> px-3 py-2 fs-6">
                        <?= esc($status) ?>
                    </span>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-sm-6">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Brand</div>
                    <div class="fw-semibold text-dark"><?= esc($item['brand'] ?: 'Not specified') ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Model</div>
                    <div class="fw-semibold text-dark"><?= esc($item['model'] ?: 'Not specified') ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Serial Number</div>
                    <div class="font-monospace text-dark"><?= esc($item['serial_number'] ?: 'N/A') ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Physical Condition</div>
                    <div>
                        <?php
                            $cond = strtoupper($item['condition_status'] ?? 'GOOD');
                            $condBadge = match ($cond) {
                                'GOOD'    => 'bg-success-subtle text-success border border-success-subtle',
                                'FAIR'    => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                'DAMAGED' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                default   => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                            };
                        ?>
                        <span class="badge <?= $condBadge ?>"><?= esc($cond) ?></span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Description</div>
                <p class="text-secondary mb-0 bg-light p-3 rounded">
                    <?= nl2br(esc($item['description'] ?: 'No additional description provided for this item.')) ?>
                </p>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <a href="<?= base_url('equipment') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <?php if (strtoupper($item['status'] ?? '') === 'AVAILABLE'): ?>
                    <a href="<?= base_url('reservations/create/' . $item['equipment_id']) ?>" class="btn btn-primary">
                        <i class="bi bi-calendar-plus me-1"></i> Reserve This Item
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>
                        Item Currently Unavailable
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
