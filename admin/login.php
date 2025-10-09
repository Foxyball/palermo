<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');


if (isset($_SESSION['admin_logged_in'])) {
  header('location: index');
  exit;
}

if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = md5($_POST['password']);
  $ip = $_SERVER['REMOTE_ADDR'];

  try {
    $stmt = $pdo->prepare("SELECT admin_id, admin_name, admin_email, admin_password, created_at FROM admins WHERE admin_email = ? AND admin_password = ? AND active = '1' LIMIT 1");
    $stmt->execute([$email, $password]);

    if ($stmt->rowCount() == 1) {
      $admin = $stmt->fetch(PDO::FETCH_ASSOC);

      $stmt1 = $pdo->prepare("UPDATE admins SET last_log_ip = ?, last_log_date = NOW() WHERE admin_email = ? AND admin_password = ? LIMIT 1");
      $stmt1->execute([$ip, $email, $password]);

      // Set session variables
      $_SESSION['admin_id'] = $admin['admin_id'];
      $_SESSION['created_at'] = $admin['created_at'];
      $_SESSION['admin_logged_in'] = true;

      header('location: index');
    } else {
      $_SESSION['error'] = 'Wrong email or password.';
      header('location: login');
    }
      exit;
  } catch (PDOException $e) {
    $_SESSION['error'] = 'Something went wrong.';
    header('location: login');
    exit;
  }
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Admin Panel Login | <?php echo SITE_TITLE; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="color-scheme" content="light dark" />
  <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
  <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
  <meta name="title" content="Admin Panel Login | <?php echo SITE_TITLE; ?>" />
  <meta
    name="description"
    content="Admin Panel Palermo" />
  <meta
    name="keywords"
    content="admin panel, palermo" />
  <meta name="supported-color-schemes" content="light dark" />
  <link rel="preload" href="./css/adminlte.css" as="style" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
    integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
    crossorigin="anonymous"
    media="print"
    onload="this.media='all'" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
    crossorigin="anonymous" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    crossorigin="anonymous" />
  <link rel="stylesheet" href="./css/adminlte.css" />

  <script src="./js/loadingAnimation.js"></script>
</head>

<body class="login-page bg-body-secondary">
  <div class="login-box">
    <div class="login-logo">
      <b>Admin Panel </b>Login
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form action="#" method="post" onsubmit="showLoading()">

          <div class="input-group mb-3">
              <label>
                  <input type="email" name="email" class="form-control" placeholder="Email" required />
              </label>
              <div class="input-group-text"><span class="bi bi-envelope"></span></div>
          </div>
          <div class="input-group mb-3">
              <label>
                  <input type="password" name="password" class="form-control" placeholder="Password" required />
              </label>
              <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
          </div>
          <div class="row">
            <div class="col-4">
              <div class="d-grid gap-2">
                <button type="submit" name="login" class="btn btn-primary">Sign In</button>
              </div>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
  <script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
    crossorigin="anonymous"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    crossorigin="anonymous"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    crossorigin="anonymous"></script>
  <script src="../js/adminlte.js"></script>
  <script>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
      scrollbarTheme: 'os-theme-light',
      scrollbarAutoHide: 'leave',
      scrollbarClickScroll: true,
    };
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
      if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: {
            theme: Default.scrollbarTheme,
            autoHide: Default.scrollbarAutoHide,
            clickScroll: Default.scrollbarClickScroll,
          },
        });
      }
    });
  </script>
</body>

</html>