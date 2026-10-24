<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Equipment Reservation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
            color: #1e293b;
        }
        .auth-card {
            background: #ffffff;
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 440px;
        }
        .brand-icon {
            width: 56px;
            height: 56px;
            background: #e0e7ff;
            color: #4338ca;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.2);
        }
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
    </style>
</head>
<body class="p-3">
    <div class="auth-card p-4 p-sm-5">
        <div class="text-center mb-4">
            <div class="brand-icon mb-2">
                <i class="bi bi-box-seam"></i>
            </div>
            <h4 class="fw-bold mb-1">Equipment Reservation</h4>
            <p class="text-muted small">Sign in with your ID number to continue</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <form action="<?= base_url('login') ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="id_number" class="form-label small fw-semibold">ID Number</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person-badge"></i></span>
                    <input type="text"
                           class="form-control <?= isset($errors['id_number']) ? 'is-invalid' : '' ?>"
                           id="id_number"
                           name="id_number"
                           placeholder="e.g. 2024-00123"
                           value="<?= old('id_number') ?>"
                           required
                           autofocus>
                    <?php if (isset($errors['id_number'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['id_number']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password"
                           class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                           id="password"
                           name="password"
                           placeholder="Enter your password"
                           required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                Sign In
            </button>
        </form>

        <div class="text-center mt-4">
            <span class="text-muted small">Don't have an account?</span>
            <a href="<?= base_url('register') ?>" class="text-decoration-none small fw-semibold ms-1">Create Account</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
