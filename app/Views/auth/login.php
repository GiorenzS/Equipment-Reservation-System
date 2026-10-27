<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - CPC Equipment Tracker System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="login-page">
    <main class="login-shell container-fluid px-3 py-4">
        <section class="login-card row g-0" aria-label="Log in to your account">
            <div class="login-brand col-lg-6">
                <div class="login-brand-content">
                    <h1 class="login-brand-title">CPC Equipment<br>Reservation System</h1>
                </div>
            </div>

            <div class="login-form-panel col-lg-6">
                <header class="login-heading">
                    <span class="login-eyebrow">CPC Equipment Reservation System</span>
                    <h2>Welcome Back</h2>
                </header>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                        <?= esc(session()->getFlashdata('success')) ?>
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php
                $errors = session()->getFlashdata('errors') ?? [];
                $fieldLabels = [
                    'id_number' => 'ID Number',
                    'password' => 'Password',
                ];
                ?>

                <form action="<?= base_url('login') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="id_number" class="form-label">Student Number/Faculty ID</label>
                        <input type="text"
                               class="form-control <?= isset($errors['id_number']) ? 'is-invalid' : '' ?>"
                               id="id_number"
                               name="id_number"
                               placeholder="Enter your ID number"
                               value="<?= old('id_number') ?>"
                               autocomplete="username"
                               autofocus
                               required>
                        <?php if (isset($errors['id_number'])): ?>
                            <div class="invalid-feedback"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['id_number'])) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-1">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-field">
                            <input type="password"
                                   class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                   id="password"
                                   name="password"
                                   placeholder="Enter your password"
                                   autocomplete="current-password"
                                   required>
                            <button class="password-toggle" type="button" data-password-toggle="password"
                                    aria-label="Show password" aria-pressed="false">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback d-block"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['password'])) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="login-actions">
                        <a href="<?= base_url('signup') ?>" class="btn btn-signup-label">Sign Up</a>
                        <button type="submit" class="btn btn-create-account">
                            Log In <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                const isVisible = input.type === 'text';
                input.type = isVisible ? 'password' : 'text';
                button.setAttribute('aria-pressed', String(!isVisible));
                button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
                button.innerHTML = '<i class="bi ' + (isVisible ? 'bi-eye' : 'bi-eye-slash') + '" aria-hidden="true"></i>';
            });
        });
    </script>
</body>
</html>
