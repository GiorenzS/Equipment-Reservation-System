<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Equipment Reservation System</title>
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
            max-width: 640px;
        }
        .brand-icon {
            width: 52px;
            height: 52px;
            background: #e0e7ff;
            color: #4338ca;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
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
<body class="p-3 py-4">
    <div class="auth-card p-4 p-sm-5">
        <div class="text-center mb-4">
            <div class="brand-icon mb-2">
                <i class="bi bi-person-plus"></i>
            </div>
            <h4 class="fw-bold mb-1">Create an Account</h4>
            <p class="text-muted small">Register to start reserving laboratory and school equipment</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <form action="<?= base_url('register') ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <!-- Credentials Section -->
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label for="id_number" class="form-label small fw-semibold">ID Number <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control <?= isset($errors['id_number']) ? 'is-invalid' : '' ?>"
                           id="id_number"
                           name="id_number"
                           placeholder="e.g. 2024-00123"
                           value="<?= old('id_number') ?>"
                           required>
                    <?php if (isset($errors['id_number'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['id_number']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-sm-6">
                    <label for="role" class="form-label small fw-semibold">Role</label>
                    <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" id="role" name="role">
                        <option value="STUDENT" <?= old('role', 'STUDENT') === 'STUDENT' ? 'selected' : '' ?>>Student</option>
                        <option value="STAFF" <?= old('role') === 'STAFF' ? 'selected' : '' ?>>Faculty / Staff</option>
                    </select>
                    <?php if (isset($errors['role'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['role']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Info Section -->
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label for="first_name" class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                           id="first_name"
                           name="first_name"
                           placeholder="First name"
                           value="<?= old('first_name') ?>"
                           required>
                    <?php if (isset($errors['first_name'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['first_name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-sm-6">
                    <label for="last_name" class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                           id="last_name"
                           name="last_name"
                           placeholder="Last name"
                           value="<?= old('last_name') ?>"
                           required>
                    <?php if (isset($errors['last_name'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['last_name']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label for="email" class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email"
                           class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                           id="email"
                           name="email"
                           placeholder="name@school.edu.ph"
                           value="<?= old('email') ?>"
                           required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label for="contact_number" class="form-label small fw-semibold">Contact Number</label>
                    <input type="text"
                           class="form-control <?= isset($errors['contact_number']) ? 'is-invalid' : '' ?>"
                           id="contact_number"
                           name="contact_number"
                           placeholder="0912 345 6789"
                           value="<?= old('contact_number') ?>">
                    <?php if (isset($errors['contact_number'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['contact_number']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-sm-6">
                    <label for="department" class="form-label small fw-semibold">Department / College</label>
                    <input type="text"
                           class="form-control <?= isset($errors['department']) ? 'is-invalid' : '' ?>"
                           id="department"
                           name="department"
                           placeholder="e.g. CCS / Engineering"
                           value="<?= old('department') ?>">
                    <?php if (isset($errors['department'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['department']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Passwords -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <label for="password" class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password"
                           class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                           id="password"
                           name="password"
                           placeholder="At least 8 characters"
                           required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-sm-6">
                    <label for="password_confirm" class="form-label small fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password"
                           class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                           id="password_confirm"
                           name="password_confirm"
                           placeholder="Re-type password"
                           required>
                    <?php if (isset($errors['password_confirm'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['password_confirm']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                Create Account
            </button>
        </form>

        <div class="text-center mt-4">
            <span class="text-muted small">Already have an account?</span>
            <a href="<?= base_url('login') ?>" class="text-decoration-none small fw-semibold ms-1">Sign In</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
