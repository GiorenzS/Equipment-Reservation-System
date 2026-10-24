<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Equipment - <?= esc($equipment['equipment_name']) ?></title>
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
        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.2);
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
    <div class="container py-4 py-lg-5" style="max-width: 720px;">
        <div class="mb-3">
            <a href="<?= base_url('equipment/' . $equipment['equipment_id']) ?>" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i> Back to Equipment Details
            </a>
        </div>

        <div class="card p-4 p-md-5">
            <!-- Equipment Summary Header -->
            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded mb-4 border">
                <div class="equipment-thumb-container shadow-sm" style="width: 60px; height: 60px; min-width: 60px; border-radius: 0.5rem; overflow: hidden; background: #0f172a; border: 1px solid #e2e8f0;">
                    <img src="<?= esc(equipment_image_url($equipment['image'] ?? null)) ?>"
                         alt="<?= esc($equipment['equipment_name']) ?>"
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><?= esc($equipment['equipment_name']) ?></h5>
                    <p class="text-muted small mb-0">
                        Code: <span class="font-monospace fw-semibold"><?= esc($equipment['equipment_code']) ?></span>
                        <?php if (! empty($equipment['brand']) || ! empty($equipment['model'])): ?>
                            &bull; <?= esc(trim(($equipment['brand'] ?? '') . ' ' . ($equipment['model'] ?? ''))) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <h4 class="fw-bold mb-1">Book Reservation</h4>
            <p class="text-muted small mb-4">Select your requested date, timeframe, and state your purpose</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php $errors = session()->getFlashdata('errors') ?? []; ?>

            <form action="<?= base_url('reservations/store') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <input type="hidden" name="equipment_id" value="<?= esc($equipment['equipment_id']) ?>">

                <!-- Reservation Date -->
                <div class="mb-3">
                    <label for="reservation_date" class="form-label small fw-semibold">Reservation Date <span class="text-danger">*</span></label>
                    <input type="date"
                           class="form-control <?= isset($errors['reservation_date']) ? 'is-invalid' : '' ?>"
                           id="reservation_date"
                           name="reservation_date"
                           min="<?= date('Y-m-d') ?>"
                           value="<?= old('reservation_date', date('Y-m-d')) ?>"
                           required>
                    <?php if (isset($errors['reservation_date'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['reservation_date']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Time Slot Range -->
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="start_time" class="form-label small fw-semibold">Start Time <span class="text-danger">*</span></label>
                        <input type="time"
                               class="form-control <?= isset($errors['start_time']) ? 'is-invalid' : '' ?>"
                               id="start_time"
                               name="start_time"
                               value="<?= old('start_time', '09:00') ?>"
                               required>
                        <?php if (isset($errors['start_time'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['start_time']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6">
                        <label for="end_time" class="form-label small fw-semibold">End Time <span class="text-danger">*</span></label>
                        <input type="time"
                               class="form-control <?= isset($errors['end_time']) ? 'is-invalid' : '' ?>"
                               id="end_time"
                               name="end_time"
                               value="<?= old('end_time', '11:00') ?>"
                               required>
                        <?php if (isset($errors['end_time'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['end_time']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Purpose -->
                <div class="mb-4">
                    <label for="purpose" class="form-label small fw-semibold">Purpose of Reservation <span class="text-danger">*</span></label>
                    <textarea class="form-control <?= isset($errors['purpose']) ? 'is-invalid' : '' ?>"
                              id="purpose"
                              name="purpose"
                              rows="3"
                              placeholder="e.g. Laboratory experiment for CS301 / Class presentation"
                              required><?= old('purpose') ?></textarea>
                    <?php if (isset($errors['purpose'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['purpose']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="<?= base_url('equipment/' . $equipment['equipment_id']) ?>" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-calendar-check me-1"></i> Submit Reservation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
