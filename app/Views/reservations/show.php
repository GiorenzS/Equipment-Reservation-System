<?php
/** @var array $reservation */
$status = strtoupper((string) ($reservation['status'] ?? 'PENDING'));
$statusClass = match ($status) {
    'APPROVED' => 'reservation-status-approved',
    'REJECTED' => 'reservation-status-rejected',
    'CANCELLED' => 'reservation-status-cancelled',
    default => 'reservation-status-pending',
};
$equipmentName = trim((string) ($reservation['equipment_name'] ?? 'Equipment') . ' ' . (string) ($reservation['brand'] ?? '') . ' ' . (string) ($reservation['model'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details - CPC Equipment Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/reservations.css') ?>" rel="stylesheet">
</head>
<body class="dashboard-page">
    <main class="container">
        <section class="reservation-detail-card">
            <a href="<?= base_url('reservations') ?>" class="btn btn-outline-secondary btn-sm mb-4"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Back to Reservations</a>
            <h1>Reservation Details</h1>
            <dl class="reservation-detail-grid">
                <div><dt>Equipment</dt><dd><?= esc($equipmentName) ?></dd></div>
                <div><dt>Asset Tag</dt><dd><?= esc($reservation['equipment_code'] ?? '—') ?></dd></div>
                <div><dt>Category</dt><dd><?= esc($reservation['category_name'] ?? 'Uncategorized') ?></dd></div>
                <div><dt>Status</dt><dd><span class="reservation-status <?= $statusClass ?>"><?= esc(ucfirst(strtolower($status))) ?></span></dd></div>
                <div><dt>Reservation Date</dt><dd><?= esc(date('M j, Y', strtotime((string) $reservation['reservation_date']))) ?></dd></div>
                <div><dt>Time</dt><dd><?= esc(date('g:i A', strtotime((string) $reservation['start_time']))) ?> – <?= esc(date('g:i A', strtotime((string) $reservation['end_time']))) ?></dd></div>
                <div class="grid-column-span"><dt>Purpose</dt><dd><?= esc($reservation['purpose']) ?></dd></div>
            </dl>
        </section>
    </main>
</body>
</html>