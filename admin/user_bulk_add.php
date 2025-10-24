<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
require_once(__DIR__ . '/../include/smtp_class.php');
require_once(__DIR__ . '/../include/EmailTemplateGenerator.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

$errors = [];
$successMessage = '';
$processedCount = 0;
$emailGenerator = new EmailTemplateGenerator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emails = trim($_POST['emails'] ?? '');
    $isTestAccount = isset($_POST['create_as_test']);

    if ($emails === '') {
        $errors[] = 'Please provide at least one email address';
    } else {
        // Split emails by comma or newline
        $emailList = preg_split('/[\r\n,]+/', $emails);
        $emailList = array_filter(array_map('trim', $emailList));

        if (empty($emailList)) {
            $errors[] = 'No valid email addresses found';
        } else {
            $validEmails = [];
            $invalidEmails = [];
            $existingEmails = [];

            foreach ($emailList as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Check if email already exists
                    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                    $stmt->execute([$email]);
                    if ($stmt->fetch()) {
                        $existingEmails[] = $email;
                    } else {
                        $validEmails[] = $email;
                    }
                } else {
                    $invalidEmails[] = $email;
                }
            }

            if (!empty($invalidEmails)) {
                $errors[] = 'Invalid email addresses: ' . implode(', ', $invalidEmails);
            }
            if (!empty($existingEmails)) {
                $errors[] = 'Email addresses already in use: ' . implode(', ', $existingEmails);
            }

            if (!empty($validEmails) && empty($errors)) {
                try {
                    $pdo->beginTransaction();

                    foreach ($validEmails as $email) {
                        if ($isTestAccount) {
                            $password = '12345678';
                            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
                            $stmt->execute(['New', 'User', $email, $passwordHash]);

                            $emailSubject = 'Welcome to ' . SITE_TITLE . ' - Your Test Account';
                            $emailBody = $emailGenerator->generateTestAccountEmail($email, $password);
                            sendEmail($email, 'New User', $emailSubject, $emailBody);
                        } else {
                            $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
                            $stmt->execute(['New', 'User', $email, '']);

                            $token = bin2hex(random_bytes(32));
                            $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
                            $tokenStmt = $pdo->prepare('INSERT INTO password_reset_tokens (email, token, expires_at) VALUES (?, ?, ?)');
                            $tokenStmt->execute([$email, $token, $expiresAt]);

                            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . BASE_URL . "reset_password?token=" . $token;
                            $emailSubject = 'Welcome to ' . SITE_TITLE . ' - Set Your Password';
                            $emailBody = $emailGenerator->generateWelcomeEmail($email, $resetLink);

                            sendEmail($email, 'New User', $emailSubject, $emailBody);
                        }
                        $processedCount++;
                    }

                    $pdo->commit();
                    if ($isTestAccount) {
                        $successMessage = "Successfully created {$processedCount} test user accounts. Welcome emails with login credentials have been sent to all users.";
                    } else {
                        $successMessage = "Successfully created {$processedCount} user accounts. Welcome emails with password setup links have been sent to all users.";
                    }
                    $_POST = [];
                } catch (PDOException $e) {
                    $pdo->rollback();
                    $errors[] = 'Database error occurred while creating users. Please try again.';
                }
            }
        }
    }
}

headerContainer();
?>
<title>Bulk Add Users | <?php echo SITE_TITLE; ?></title>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php navbarContainer(); ?>
        <?php sidebarContainer(); ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Bulk Add Users</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item"><a href="user_list">User List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Bulk Add Users</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="bi bi-people-fill me-2"></i>
                                        Bulk Add Users
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($errors)) { ?>
                                        <div class="alert alert-danger">
                                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Errors occurred:</h6>
                                            <ul class="mb-0">
                                                <?php foreach ($errors as $err) { ?>
                                                    <li><?php echo htmlspecialchars($err); ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } ?>

                                    <?php if ($successMessage) { ?>
                                        <div class="alert alert-success">
                                            <h6 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Success!</h6>
                                            <?php echo htmlspecialchars($successMessage); ?>



                                            <div class="mt-2">
                                                <a href="user_list" class="btn btn-sm btn-outline-success">View User List</a>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <form method="post" novalidate>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">
                                                <i class="bi bi-envelope-fill me-1"></i>
                                                Email Addresses *
                                            </label>
                                            <textarea
                                                name="emails"
                                                class="form-control"
                                                rows="8"
                                                placeholder="Enter email addresses separated by comma or new line:&#10;&#10;user1@example.com&#10;user2@example.com, user3@example.com&#10;user4@example.com"
                                                required><?php echo htmlspecialchars($_POST['emails'] ?? ''); ?></textarea>
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle"></i>
                                                You can paste multiple email addresses separated by commas or new lines
                                            </small>
                                        </div>

                                        <div class="mb-4">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="create_as_test"
                                                    id="create_as_test"
                                                    <?php echo isset($_POST['create_as_test']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label fw-bold" for="create_as_test">
                                                    <i class="bi bi-shield-check me-1"></i>
                                                    Create as test accounts
                                                </label>
                                            </div>
                                            <small class="text-muted ms-4">
                                                <i class="bi bi-question-circle me-1"></i>
                                                When checked, accounts will be created with a default password ('12345678') and users will receive an email with their credentials.
                                                Otherwise, users will receive an email with a secure link to set their own password.
                                            </small>
                                        </div>

                                        <div class="mb-4">
                                            <div class="alert alert-info">
                                                <h6 class="alert-heading">
                                                    <i class="bi bi-clock-fill"></i>
                                                    Accounts being processed: <span id="processing-count"><?php echo $processedCount; ?></span>
                                                </h6>
                                                <small class="text-muted">This counter shows how many accounts have been successfully created in the current session.</small>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="user_list" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left me-1"></i>
                                                Back to User List
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-people-fill me-1"></i>
                                                Create Users
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php footerContainer(); ?>
    </div>

    <style>
        .alert-heading {
            margin-bottom: 0.5rem;
        }

        .card-title i {
            color: #0d6efd;
        }

        .form-check-label {
            cursor: pointer;
        }

        .form-check-input {
            cursor: pointer;
        }

        textarea.form-control {
            font-family: 'Courier New', monospace;
            resize: vertical;
        }

        .alert-info .alert-heading {
            color: #0c5460;
        }

        #processing-count {
            font-weight: bold;
            color: #0d6efd;
        }
    </style>

    <script>
        $(function() {
            <?php if ($processedCount > 0) { ?>
                $('#processing-count').text(<?php echo $processedCount; ?>);
            <?php } ?>

            $('form').on('submit', function() {
                const emails = $('textarea[name="emails"]').val().trim();
                if (!emails) {
                    alert('Please enter at least one email address.');
                    return false;
                }

                const $btn = $(this).find('button[type="submit"]');
                $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> Processing...');
            });
        });
    </script>
</body>

</html>