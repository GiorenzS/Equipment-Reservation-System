
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPC Equipment Tracker | Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        :root {
            --navy: #14243a;
            --blue: #2563eb;
            --background: #f4f7fb;
        }

        body {
            margin: 0;
            background: var(--background);
            color: #1e293b;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 245px;
            background: var(--navy);
            color: white;
            padding: 28px 18px;
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.4;
            padding: 0 10px 30px;
        }

        .brand small {
            display: block;
            margin-top: 6px;
            color: #a8bad0;
            font-size: 12px;
            font-weight: 400;
        }

        .nav-link-custom {
            display: block;
            color: #d5deea;
            text-decoration: none;
            padding: 13px 14px;
            border-radius: 9px;
            margin-bottom: 8px;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            background: #29415f;
            color: white;
        }

        .main {
            margin-left: 245px;
            padding: 35px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 30px;
        }

        .welcome-card {
            background: linear-gradient(120deg, #14243a, #28548a);
            color: white;
            border-radius: 18px;
            padding: 38px;
            margin-bottom: 28px;
            box-shadow: 0 12px 30px rgba(20, 36, 58, .12);
        }

        .welcome-card p {
            color: #d9e5f4;
            max-width: 560px;
        }

        .btn-equipment {
            display: inline-block;
            background: white;
            color: #14243a;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 9px;
            font-weight: 700;
            margin-top: 12px;
        }

        .btn-equipment:hover {
            background: #eaf1fa;
            color: #14243a;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .feature-card {
            height: 100%;
            background: white;
            border: 1px solid #e6ebf2;
            border-radius: 14px;
            padding: 24px;
            transition: transform .2s, box-shadow .2s;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(20, 36, 58, .07);
        }

        .feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #eaf1ff;
            font-size: 24px;
            margin-bottom: 17px;
        }

        .feature-card h3 {
            font-size: 17px;
            font-weight: 700;
        }

        .feature-card p {
            color: #64748b;
            font-size: 14px;
            min-height: 42px;
        }

        .feature-link {
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
        }

        .footer {
            margin-top: 35px;
            color: #718096;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
                padding: 18px;
            }

            .brand {
                padding-bottom: 15px;
            }

            .main {
                margin-left: 0;
                padding: 22px 16px;
            }

            .welcome-card {
                padding: 26px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <div class="brand">
        CPC EQUIPMENT TRACKER
        <small>Equipment Management System</small>
    </div>

    <nav>
        <a class="nav-link-custom active"
           href="<?= site_url('/') ?>">⌂ &nbsp; Dashboard</a>

        <a class="nav-link-custom"
           href="<?= site_url('equipment') ?>">▣ &nbsp; Equipment List</a>

        <a class="nav-link-custom"
           href="<?= site_url('equipment/create') ?>">＋ &nbsp; Add Equipment</a>
    </nav>
</aside>

<main class="main">

    <div class="topbar">
        <div>
            <h1 class="h3 fw-bold mb-1">Dashboard</h1>
            <div class="text-secondary">
                Welcome to the CPC Equipment Tracker System.
            </div>
        </div>

        <a href="<?= site_url('equipment') ?>"
           class="btn btn-primary px-4 py-2">
            View Equipment
        </a>
    </div>

    <section class="welcome-card">
        <div class="text-uppercase small fw-semibold mb-2">
            Equipment Management
        </div>

        <h2 class="display-6 fw-bold">
            Manage Your Equipment
        </h2>

        <p class="mt-3 mb-2">
            Access your department's equipment records, review equipment
            information, and keep your inventory organized in one place.
        </p>

        <a href="<?= site_url('equipment') ?>" class="btn-equipment">
            Browse Equipment &rarr;
        </a>
    </section>

    <h2 class="section-title">Quick Access</h2>

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <div class="feature-card">
                <div class="feature-icon">▣</div>
                <h3>Equipment Inventory</h3>
                <p>View and review equipment records available to you.</p>
                <a class="feature-link"
                   href="<?= site_url('equipment') ?>">
                    Open Equipment List &rarr;
                </a>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="feature-card">
                <div class="feature-icon">＋</div>
                <h3>Add Equipment</h3>
                <p>Register a new equipment item in your department.</p>
                <a class="feature-link"
                   href="<?= site_url('equipment/create') ?>">
                    Add New Equipment &rarr;
                </a>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="feature-card">
                <div class="feature-icon">↗</div>
                <h3>Equipment Details</h3>
                <p>Open the inventory to select an item and inspect its details.</p>
                <a class="feature-link"
                   href="<?= site_url('equipment') ?>">
                    View Item Details &rarr;
                </a>
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; <?= date('Y') ?> CPC Equipment Tracker System
    </div>

</main>

</body>
</html>
