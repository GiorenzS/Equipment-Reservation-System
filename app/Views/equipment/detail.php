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
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }
        .equipment-image-card {
            background: #0f172a;
            border-radius: 0.85rem;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 320px;
        }
        .equipment-image-container {
            width: 100%;
            height: 100%;
            aspect-ratio: 4 / 3;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
            overflow: hidden;
        }
        .equipment-showcase-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.35s ease;
        }
        .equipment-image-card:hover .equipment-showcase-img {
            transform: scale(1.03);
        }
        .image-status-pill {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 2;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .image-condition-pill {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            z-index: 2;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .info-label {
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-size: 0.975rem;
            font-weight: 600;
            color: #0f172a;
        }
        .spec-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.65rem;
            padding: 0.85rem 1rem;
            height: 100%;
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
    <div class="container py-4 py-lg-5" style="max-width: 1040px;">
        <div class="mb-3">
            <a href="<?= base_url('equipment') ?>" class="text-decoration-none small text-muted d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Back to Equipment Catalog
            </a>
        </div>

        <?php
            $status = strtoupper($item['status'] ?? 'AVAILABLE');
            $statusBadgeClass = match ($status) {
                'AVAILABLE' => 'bg-success text-white',
                'IN_USE'    => 'bg-warning text-dark',
                'RESERVED'  => 'bg-info text-dark',
                default     => 'bg-secondary text-white',
            };

            $cond = strtoupper($item['condition_status'] ?? 'GOOD');
            $condBadgeClass = match ($cond) {
                'GOOD'    => 'bg-success text-white',
                'FAIR'    => 'bg-warning text-dark',
                'DAMAGED' => 'bg-danger text-white',
                default   => 'bg-secondary text-white',
            };

            $imageUrl = equipment_image_url($item['image'] ?? null);
            $hasCustomImage = equipment_has_custom_image($item['image'] ?? null);
        ?>

        <div class="card p-4 p-md-5">
            <div class="row g-4 g-lg-5 align-items-start">
                <!-- Equipment Image Showcase Column -->
                <div class="col-lg-5">
                    <div class="equipment-image-card">
                        <!-- Floating Badges -->
                        <div class="image-status-pill">
                            <span class="badge <?= $statusBadgeClass ?> px-3 py-2 fs-6">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                <?= esc($status) ?>
                            </span>
                        </div>
                        <div class="image-condition-pill">
                            <span class="badge <?= $condBadgeClass ?> px-2.5 py-1.5 small">
                                Condition: <?= esc($cond) ?>
                            </span>
                        </div>

                        <!-- Image Container -->
                        <div class="equipment-image-container">
                            <img src="<?= esc($imageUrl) ?>"
                                 alt="<?= esc($item['equipment_name']) ?>"
                                 class="equipment-showcase-img"
                                 loading="lazy">
                        </div>
                    </div>

                    <?php if ($hasCustomImage): ?>
                        <div class="text-center mt-2">
                            <a href="<?= esc($imageUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-none small text-muted">
                                <i class="bi bi-arrows-fullscreen me-1"></i> View full resolution
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Equipment Details Column -->
                <div class="col-lg-7">
                    <div class="mb-3">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2 px-3 py-1.5">
                            <?= esc($item['category_name'] ?? 'Uncategorized') ?>
                        </span>
                        <h2 class="fw-bold mb-1 text-dark"><?= esc($item['equipment_name']) ?></h2>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <span class="font-monospace bg-light border px-2 py-0.5 rounded">
                                Code: <?= esc($item['equipment_code']) ?>
                            </span>
                            <?php if (! empty($item['serial_number'])): ?>
                                <span>&bull;</span>
                                <span class="font-monospace">SN: <?= esc($item['serial_number']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Specs Grid -->
                    <div class="row g-3 my-3">
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <div class="info-label">Brand</div>
                                <div class="info-value"><?= esc($item['brand'] ?: 'Not specified') ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <div class="info-label">Model</div>
                                <div class="info-value"><?= esc($item['model'] ?: 'Not specified') ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <div class="info-label">Serial Number</div>
                                <div class="info-value font-monospace"><?= esc($item['serial_number'] ?: 'N/A') ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="spec-item">
                                <div class="info-label">Physical Condition</div>
                                <div class="info-value"><?= esc($cond) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <div class="info-label">Description</div>
                        <div class="p-3 bg-light rounded text-secondary" style="font-size: 0.925rem; line-height: 1.6;">
                            <?= nl2br(esc($item['description'] ?: 'No additional description provided for this item.')) ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 border-top">
                        <a href="<?= base_url('equipment') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to Catalog
                        </a>
                        <?php if (strtoupper($item['status'] ?? '') === 'AVAILABLE'): ?>
                            <a href="<?= base_url('reservations/create/' . $item['equipment_id']) ?>" class="btn btn-primary px-4 py-2 fw-semibold">
                                <i class="bi bi-calendar-plus me-1"></i> Reserve This Item
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary px-4 py-2" disabled>
                                Item Currently Unavailable
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
