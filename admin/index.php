<?php

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');
include(__DIR__ . '/include/html_functions.php');

requireAdminLogin();

?>

<?php
headerContainer();
?>
</head>


<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">


        <?php navbarContainer(); ?>

        <?php sidebarContainer(); ?>

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0"><?php echo SITE_TITLE; ?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="/palermo/admin">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo SITE_TITLE; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>


            <?php infoBoxContainer(); ?>


            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                       
                    </div>
                </div>
            </div>
        </main>

        <?php footerContainer(); ?>

</body>

</html>