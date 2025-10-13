<?php



function headerContainer(): void
{

?>

    <!doctype html>
    <html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Admin Panel | <?php echo SITE_TITLE; ?></title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
        <meta name="color-scheme" content="light dark" />
        <meta name="theme-color" content="#003b79ff" media="(prefers-color-scheme: light)" />
        <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
        <link rel="icon" type="image/x-icon" href="../favicon.ico">


        <meta name="title" content="Admin Panel | <?php echo SITE_TITLE; ?>" />
        <meta
            name="description"
            content="Admin panel Palermo" />
        <meta
            name="keywords"
            content="Admin, Dashboard, Palermo" />

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
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
            integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
            crossorigin="anonymous" />


        <link rel="stylesheet" href="../node_modules/toastr/build/toastr.min.css" />
        <script src="./js/jquery.js"></script>
        <script src="../node_modules/toastr/build/toastr.min.js"></script>
        <script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
        <script src="./js/session-renew.js"></script>
    <?php
}


function navbarContainer(): void
{

    global $pdo;
    $stmt = $pdo->prepare("SELECT admin_id, admin_name, admin_email FROM admins WHERE admin_id = ? AND active = '1' LIMIT 1");
    $stmt->execute([$_SESSION['admin_id']]);
    $loggedInAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

    ?>


        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">


                    <!-- T0D0: Notifications system - use AJAX to fetch new unread notifications and show them here -->

                    <li class="nav-item dropdown">
                        <a class="nav-link" data-bs-toggle="dropdown" href="#">
                            <i class="bi bi-bell-fill"></i>
                            <span class="navbar-badge badge text-bg-warning">1</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <span class="dropdown-item dropdown-header">1 Notification</span>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-envelope me-2"></i> 4 new messages
                                <span class="float-end text-secondary fs-7">3 mins</span>
                            </a>

                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item dropdown-footer"> See All Notifications </a>
                        </div>
                    </li>

                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="user-image rounded-circle shadow border border-2 border-primary" style="width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;font-size:1.25rem;background:#f8f9fa;color:#003b79;">
                                <?php echo strtoupper($loggedInAdmin['admin_name'][0]); ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-footer">
                                <a href="/palermo/admin/account" class="btn btn-default btn-flat">Profile</a>
                                <a href="/palermo/admin/logout?logout" class="btn btn-default btn-flat float-end">Sign out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

    <?php }


function sidebarContainer(): void
{
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
    
    $adminPages = [
        'admin_list', 
        'admin_add', 
        'admin_edit', 
        'user_list', 
        'user_add', 
        'user_edit'
    ];
    $articlePages = [
        'category_list',
        'category_add',
        'category_edit'
    ];

    $galleryPages = [
            'gallery_list',
        'gallery_add',
        'gallery_edit',
        'gallery_images'
    ];

    $blogPages = [
            'blog_list',
        'blog_add',
        'blog_edit'
    ];

    $blogCategoriesPages = [
            'blog_category_list',
        'blog_category_add',
        'blog_category_edit'
    ];

    $dashboardPages = ['index'];
    
    $isAdminSection = in_array($currentPage, $adminPages);
    $isArticleSection = in_array($currentPage, $articlePages);
    $isGallerySection = in_array($currentPage, $galleryPages);
    $isBlogSection = in_array($currentPage, $blogPages);
    $isBlogCategorySection = in_array($currentPage, $blogCategoriesPages);
    $isDashboard = in_array($currentPage, $dashboardPages) || $currentPage === 'index';

    ?>


        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark" style="position: relative;">
            <div class="sidebar-brand">
                <a href="./index.html" class="brand-link">
                    <span class="brand-text fw-light">Admin Panel | <?php echo SITE_TITLE; ?></span>
                </a>
            </div>
            <div class="sidebar-wrapper" style="padding-bottom: 80px; height: calc(100vh - 57px); overflow-y: auto;">
                <nav class="mt-2">
                    <ul
                        class="nav sidebar-menu flex-column"
                        data-lte-toggle="treeview"
                        role="listbox"
                        aria-label="Main navigation"
                        data-accordion="false"
                        id="navigation">
                        <li class="nav-item">
                            <a href="/palermo/admin" class="nav-link <?php echo $isDashboard ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>


                        <li class="nav-item <?php echo $isAdminSection ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link <?php echo $isAdminSection ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-person-gear"></i>
                                <p>
                                    Admin
                                    <i class="bi bi-chevron-down right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="admin_list" class="nav-link <?php echo in_array($currentPage, ['admin_list', 'admin_add', 'admin_edit']) ? 'active' : ''; ?>">
                                        <i class="bi bi-person-lines-fill nav-icon"></i>
                                        <p>Admins</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/palermo/admin/user_list" class="nav-link <?php echo in_array($currentPage, ['user_list', 'user_add', 'user_edit']) ? 'active' : ''; ?>">
                                        <i class="bi bi-people nav-icon"></i>
                                        <p>Customers</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                          <li class="nav-item <?php echo $isArticleSection ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link <?php echo $isArticleSection ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-file-earmark-text"></i>
                                <p>
                                    Articles
                                    <i class="bi bi-chevron-down right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="category_list" class="nav-link <?php echo in_array($currentPage, ['category_list', 'category_add', 'category_edit']) ? 'active' : ''; ?>">
                                        <i class="bi bi-tag-fill nav-icon"></i>
                                        <p>Categories</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item <?php echo $isGallerySection ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link <?php echo $isGallerySection ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-file-earmark-image"></i>
                                <p>
                                    Galleries
                                    <i class="bi bi-chevron-down right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="gallery_list" class="nav-link <?php echo in_array($currentPage, ['gallery_list', 'gallery_add', 'gallery_edit', 'gallery_images']) ? 'active' : ''; ?>">
                                        <i class="bi bi-file-image-fill nav-icon"></i>
                                        <p>Galleries</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item <?php echo $isBlogSection ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link <?php echo $isBlogSection ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-file-earmark-text"></i>
                                <p>
                                    Blog
                                    <i class="bi bi-chevron-down right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="blog_list" class="nav-link <?php echo in_array($currentPage, ['blog_list', 'blog_add', 'blog_edit']) ? 'active' : ''; ?>">
                                        <i class="bi bi-newspaper nav-icon"></i>
                                        <p>Blog Post</p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="blog_category_list" class="nav-link <?php echo in_array($currentPage, ['blog_category_list', 'blog_category_add', 'blog_category_edit']) ? 'active' : ''; ?>">
                                        <i class="bi bi-tag-fill nav-icon"></i>
                                        <p>Blog Category</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                    </ul>
                </nav>
            </div>
        </aside>

    <?php }


function infoBoxContainer(): void
{
    // Dynamic counts
    global $pdo;
    $totalAdmins = 0;
    $totalProducts = 0;
    $totalOrders = 0;
    $totalUsers = 0;
    try { $totalAdmins = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn(); } catch (Throwable $e) { $totalAdmins = 0; }
    try { $totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(); } catch (Throwable $e) { $totalProducts = 0; }
    try { $totalOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(); } catch (Throwable $e) { $totalOrders = 0; }
    try { $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(); } catch (Throwable $e) { $totalUsers = 0; }

    ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3><?php echo $totalAdmins; ?></h3>
                            <p>Administrators</p>
                        </div>
                        <svg
                            class="small-box-icon"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z"></path>
                        </svg>
                        <a
                            href="admin_list"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Manage <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3><?php echo $totalProducts; ?></h3>
                            <p>Products</p>
                        </div>
                        <svg
                            class="small-box-icon"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"></path>
                        </svg>
                        <a
                            href="#"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3><?php echo $totalOrders; ?></h3>
                            <p>Orders</p>
                        </div>
                        <svg
                            class="small-box-icon"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z"></path>
                        </svg>
                        <a
                            href="#"
                            class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3><?php echo $totalUsers; ?></h3>
                            <p>Customers</p>
                        </div>
                        <svg
                            class="small-box-icon"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z"></path>
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z"></path>
                        </svg>
                        <a
                            href="#"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
        </div>

    <?php }


function footerContainer(): void
{

    ?>

        <footer class="app-footer">
            <strong>
                Copyright &copy; <?php echo date('Y'); ?>&nbsp;
                <span class="text-decoration-none">Palermo</span>
            </strong>
            All rights reserved.
        </footer>
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
        <script src="./js/adminlte.js"></script>

        <script
            src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
            integrity="sha256-Er6+oxBM7ZLw8uYgrS0d6zPFq9B8NbhJvJ20g4/iU1Q="
            crossorigin="anonymous"></script>

        <script>
            // eslint-disable-next-line no-unused-vars
            const Select2 = {
                bs5Theme: () => {
                    const $element = $(this);
                    const hasMultiple = !!$element.attr('multiple');
                    const hasInputGroup = $element.closest('.input-group').length;

                    let theme = 'bootstrap-5';

                    if (hasMultiple) {
                        theme += ' select2-bootstrap-5-theme-multiple';
                    }

                    if (hasInputGroup) {
                        theme += ' select2-bootstrap-5-theme-input-group';
                    }

                    return theme;
                },
                _init: () => {
                    // page specific demo, per page to load specific select2 demo
                },
            };
        </script>


    <?php
    if (!empty($_SESSION['success']) || !empty($_SESSION['error'])) { ?>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                <?php if (!empty($_SESSION['success'])): ?>
                toastr.success('<?php echo addslashes($_SESSION['success']); ?>');
                <?php endif; ?>
                <?php if (!empty($_SESSION['error'])): ?>
                toastr.error('<?php echo addslashes($_SESSION['error']); ?>');
                <?php endif; ?>
            });
        </script>
    <?php
    unset($_SESSION['success'], $_SESSION['error']);
    };
    ?>

    <?php }
