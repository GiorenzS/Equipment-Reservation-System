<?php
$equipmentId = (int) ($equipment['equipment_id'] ?? 0);
$modalId = 'reservationModal' . $equipmentId;
$oldEquipmentId = (int) old('equipment_id', 0);
$errors = $oldEquipmentId === $equipmentId ? (session()->getFlashdata('errors') ?? []) : [];
$errorMessage = $oldEquipmentId === $equipmentId ? session()->getFlashdata('error') : null;
$equipmentModel = trim((string) ($equipment['brand'] ?? '') . ' ' . (string) ($equipment['model'] ?? ''));
?>
<div class="modal fade reservation-create-modal" id="<?= esc($modalId) ?>" tabindex="-1" aria-labelledby="<?= esc($modalId) ?>Label" aria-hidden="true" data-auto-open="<?= $oldEquipmentId === $equipmentId && ($errors !== [] || $errorMessage) ? 'true' : 'false' ?>">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <span class="reservation-modal-icon"><i class="bi bi-calendar2-check" aria-hidden="true"></i></span>
                <div class="reservation-modal-heading">
                    <h2 class="modal-title" id="<?= esc($modalId) ?>Label">CPC Equipment Reservation: Create Reservation</h2>
                    <p>Request equipment for a specific date and purpose.</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('reservations/store') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="equipment_id" value="<?= $equipmentId ?>">
                <div class="modal-body">
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger py-2" role="alert"><?= esc($errorMessage) ?></div>
                    <?php endif; ?>
                    <div class="reservation-selected-equipment">
                        <div class="reservation-selected-thumb">
                            <img src="<?= esc(equipment_image_url($equipment['image'] ?? null)) ?>" alt="<?= esc($equipment['equipment_name']) ?>">
                        </div>
                        <div class="reservation-selected-details">
                            <span class="reservation-selected-label">Selected Equipment</span>
                            <strong><?= esc($equipment['equipment_name']) ?></strong>
                            <small><?= esc($equipment['equipment_code']) ?><?= $equipmentModel !== '' ? ' · ' . esc($equipmentModel) : '' ?></small>
                        </div>
                        <a class="reservation-equipment-change" href="<?= base_url('equipment/catalog') ?>" aria-label="Change equipment in catalog" title="Choose another item"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></a>
                    </div>

                    <div class="mb-3">
                        <label for="reservation_date_<?= $equipmentId ?>" class="form-label">Reservation Date</label>
                        <input type="date" class="form-control <?= isset($errors['reservation_date']) ? 'is-invalid' : '' ?>" id="reservation_date_<?= $equipmentId ?>" name="reservation_date" min="<?= date('Y-m-d') ?>" value="<?= esc(old('reservation_date', date('Y-m-d'))) ?>" required>
                        <?php if (isset($errors['reservation_date'])): ?><div class="invalid-feedback"><?= esc($errors['reservation_date']) ?></div><?php endif; ?>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="start_time_<?= $equipmentId ?>" class="form-label">Start Time</label>
                            <input type="time" class="form-control <?= isset($errors['start_time']) ? 'is-invalid' : '' ?>" id="start_time_<?= $equipmentId ?>" name="start_time" value="<?= esc(old('start_time', '09:00')) ?>" required>
                            <?php if (isset($errors['start_time'])): ?><div class="invalid-feedback"><?= esc($errors['start_time']) ?></div><?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label for="end_time_<?= $equipmentId ?>" class="form-label">End Time</label>
                            <input type="time" class="form-control <?= isset($errors['end_time']) ? 'is-invalid' : '' ?>" id="end_time_<?= $equipmentId ?>" name="end_time" value="<?= esc(old('end_time', '11:00')) ?>" required>
                            <?php if (isset($errors['end_time'])): ?><div class="invalid-feedback"><?= esc($errors['end_time']) ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <label for="purpose_<?= $equipmentId ?>" class="form-label">Purpose of Reservation</label>
                        <textarea class="form-control <?= isset($errors['purpose']) ? 'is-invalid' : '' ?>" id="purpose_<?= $equipmentId ?>" name="purpose" rows="3" maxlength="255" placeholder="e.g. Laboratory experiment for CS301 / Class presentation"><?= esc(old('purpose', '')) ?></textarea>
                        <?php if (isset($errors['purpose'])): ?><div class="invalid-feedback"><?= esc($errors['purpose']) ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-calendar-check me-1" aria-hidden="true"></i>Create Reservation</button>
                </div>
            </form>
        </div>
    </div>
</div>
