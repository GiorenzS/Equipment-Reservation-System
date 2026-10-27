<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - CPC Equipment Tracker System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body class="signup-page">
    <main class="signup-shell container-fluid px-3 py-4">
        <section class="signup-card row g-0" aria-label="Create your account">
            <div class="signup-brand col-lg-5">
                <div class="signup-brand-content">
                    <h1 class="signup-brand-title">CPC Equipment<br>Reservation System</h1>
                </div>
            </div>

            <div class="signup-form-panel col-lg-7">
                <header class="signup-heading">
                    <span class="signup-eyebrow">Join CPC Equipment Reservation</span>
                    <h2>Create Account</h2>
                    <p>Please fill in the details to create your new account.</p>
                </header>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <?php $errors = session()->getFlashdata('errors') ?? [];
$fieldLabels = [
    'first_name' => 'First Name',
    'last_name' => 'Last Name',
    'id_number' => 'Student Number/Faculty ID',
    'role' => 'Account Type',
    'email' => 'Email Address',
    'contact_number' => 'Contact Number',
    'department_id' => 'Department / College',
    'password_confirm' => 'Confirm Password',
    'password' => 'Password',
]; ?>

                <form action="<?= base_url('register') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                                   id="first_name" name="first_name" placeholder="First name"
                                   value="<?= old('first_name') ?>" maxlength="50" autocomplete="given-name" required>
                            <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['first_name'])) ?></div><?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                                   id="last_name" name="last_name" placeholder="Last name"
                                   value="<?= old('last_name') ?>" maxlength="50" autocomplete="family-name" required>
                            <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['last_name'])) ?></div><?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <label for="id_number" class="form-label">Student Number/Faculty ID </label>
                            <input type="text" class="form-control <?= isset($errors['id_number']) ? 'is-invalid' : '' ?>"
                                   id="id_number" name="id_number" placeholder="e.g. 202400123"
                                   value="<?= old('id_number') ?>" maxlength="50" autocomplete="username" required>
                            <?php if (isset($errors['id_number'])): ?><div class="invalid-feedback"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['id_number'])) ?></div><?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label for="role" class="form-label">Account Type</label>
                            <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" id="role" name="role">
                                <option value="STUDENT" <?= old('role', 'STUDENT') === 'STUDENT' ? 'selected' : '' ?>>Student</option>
                                <option value="STAFF" <?= old('role') === 'STAFF' ? 'selected' : '' ?>>Faculty / Staff</option>
                            </select>
                            <?php if (isset($errors['role'])): ?><div class="invalid-feedback"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['role'])) ?></div><?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                   id="email" name="email" placeholder="name@gmail.com"
                                   value="<?= old('email') ?>" maxlength="255" autocomplete="email" required>
                            <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['email'])) ?></div><?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control <?= isset($errors['contact_number']) ? 'is-invalid' : '' ?>"
                                   id="contact_number" name="contact_number" placeholder="0912 345 6789"
                                   value="<?= old('contact_number') ?>" maxlength="20" autocomplete="tel">
                            <?php if (isset($errors['contact_number'])): ?><div class="invalid-feedback"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['contact_number'])) ?></div><?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label for="department_id" class="form-label">Department / College </label>
                            <select class="form-select <?= isset($errors['department_id']) ? 'is-invalid' : '' ?>"
                                    id="department_id" name="department_id" required>
                                <option value="">Select your department or college</option>
                                <?php foreach (($departments ?? []) as $department): ?>
                                    <option value="<?= esc($department['department_id']) ?>"
                                        <?= (string) old('department_id') === (string) $department['department_id'] ? 'selected' : '' ?>>
                                        <?= esc($department['department_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['department_id'])): ?><div class="invalid-feedback"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['department_id'])) ?></div><?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-field">
                                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                       id="password" name="password" placeholder="At least 8 characters"
                                       minlength="8" autocomplete="new-password" required>
                                <button class="password-toggle" type="button" data-password-toggle="password"
                                        aria-label="Show password" aria-pressed="false">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <?php if (isset($errors['password'])): ?><div class="invalid-feedback d-block"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['password'])) ?></div><?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <div class="password-field">
                                <input type="password" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                                       id="password_confirm" name="password_confirm" placeholder="Re-type password"
                                       autocomplete="new-password" required>
                                <button class="password-toggle" type="button" data-password-toggle="password_confirm"
                                        aria-label="Show confirm password" aria-pressed="false">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <?php if (isset($errors['password_confirm'])): ?><div class="invalid-feedback d-block"><?= esc(str_replace(array_keys($fieldLabels), array_values($fieldLabels), $errors['password_confirm'])) ?></div><?php endif; ?>
                        </div>
                    </div>

                    <div class="signup-actions">
                        <button type="submit" class="btn btn-create-account">
                            Create Account <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>

                <p class="signup-signin">Already have an account? <a href="<?= base_url('login') ?>">Sign In</a></p>
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
                button.setAttribute('aria-label', (isVisible ? 'Show ' : 'Hide ') + (input.id === 'password' ? 'password' : 'confirm password'));
                button.innerHTML = '<i class="bi ' + (isVisible ? 'bi-eye' : 'bi-eye-slash') + '" aria-hidden="true"></i>';
            });
        });
    </script>
</body>
</html>
