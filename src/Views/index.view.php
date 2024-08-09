<!DOCTYPE html>
<html>

<head>
    <title>Шалгалтын систем</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <script src="/scripts/exams.js"></script>
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="search-cont flex-row-center">

            </div>
            <?php if ($is_admin) : ?>
                <a href="/admin">Admin</a>
            <?php endif; ?>
            <a href="/logout">
                Системээс гарах
            </a>
        </div>
        <div class="exam-list">
            <div class="exam-container loading">
                <div class="exam-loading">
                    Уншиж байна...
                </div>
            </div>

        </div>
    </div>
</body>

</html>