<?php
/** @var array<string, mixed> $reservation Reservation joined with its equipment and category data. */
$reservation = $reservation ?? [];
$reservationId = (int) ($reservation['reservation_id'] ?? 0);
$status = strtoupper((string) ($reservation['status'] ?? 'PENDING'));
$statusClass = match ($status) {
    'APPROVED' => 'reservation-status-approved',
    'CHECKED_OUT' => 'reservation-status-checked-out',
    'RETURNED' => 'reservation-status-returned',
    'REJECTED' => 'reservation-status-rejected',
    'CANCELLED' => 'reservation-status-cancelled',
    default => 'reservation-status-pending',
};
$statusLabel = ucwords(strtolower(str_replace('_', ' ', $status)));
$equipmentName = trim((string) ($reservation['equipment_name'] ?? 'Equipment'));
$equipmentModel = trim((string) ($reservation['brand'] ?? '') . ' ' . (string) ($reservation['model'] ?? ''));
$reservationDate = (string) ($reservation['reservation_date'] ?? '');
$startTime = (string) ($reservation['start_time'] ?? '');
$endTime = (string) ($reservation['end_time'] ?? '');
?>
<div class="modal fade reservation-detail-modal" id="reservationDetails<?= $reservationId ?>" tabindex="-1" aria-labelledby="reservationDetails<?= $reservationId ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <span class="reservation-modal-icon"><i class="bi bi-calendar2-check" aria-hidden="true"></i></span>
                <div class="reservation-modal-heading">
                    <h2 class="modal-title" id="reservationDetails<?= $reservationId ?>Label">Reservation Details</h2>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <section class="reservation-detail-equipment" aria-label="Reserved equipment">
                    <span class="reservation-detail-equipment-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                    <div class="reservation-detail-equipment-copy">
                        <span class="reservation-selected-label">Equipment</span>
                        <strong><?= esc($equipmentName) ?></strong>
                        <small><?= esc($equipmentModel !== '' ? $equipmentModel : ($reservation['category_name'] ?? 'Uncategorized')) ?></small>
                    </div>
                    <span class="reservation-status <?= esc($statusClass) ?>"><?= esc($statusLabel) ?></span>
                </section>
                <dl class="reservation-detail-grid reservation-detail-modal-grid">
                    <div><dt>Asset Tag</dt><dd><?= esc($reservation['equipment_code'] ?? '—') ?></dd></div>
                    <div><dt>Category</dt><dd><?= esc($reservation['category_name'] ?? 'Uncategorized') ?></dd></div>
                    <div><dt>Reservation Date</dt><dd><?= $reservationDate !== '' ? esc(date('M j, Y', strtotime($reservationDate))) : '—' ?></dd></div>
                    <div><dt>Time</dt><dd><?= $startTime !== '' && $endTime !== '' ? esc(date('g:i A', strtotime($startTime)) . ' – ' . date('g:i A', strtotime($endTime))) : '—' ?></dd></div>
                    <div class="reservation-detail-purpose"><dt>Purpose</dt><dd><?= nl2br(esc($reservation['purpose'] ?? '—')) ?></dd></div>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
