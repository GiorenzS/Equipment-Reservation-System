<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Reservation - CPC Equipment Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/reservations.css') ?>" rel="stylesheet">
</head>
<body class="reservation-create-page">
    <?= view('reservations/modal_create', ['equipment' => $equipment]) ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const reservationModal = document.getElementById('reservationModal<?= (int) $equipment['equipment_id'] ?>');
        bootstrap.Modal.getOrCreateInstance(reservationModal).show();
    </script>
</body>
</html>
