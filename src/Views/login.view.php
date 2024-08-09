<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шалгалтын систем - Нэврэх</title>
    <link rel="stylesheet" type="text/css" href="/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/css/login.css">
</head>

<body>
    <div class="login-cont">
        <h1>Нэврэх хэсэг</h1>
        <?php if (isset($error)) : ?>
            <p class="error">Нэврэх нэр эсвэл нууц үг буруу байна!</p>
        <?php endif; ?>

        <form method="post">
            <div class="input-field">
                <input type="text" name="username" required placeholder="  ">
                <label>Утасны дугаар эсвэл и-мэйл</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" required placeholder="  ">
                <label>Нууц үг</label>
            </div>
            <div class="footer">
                <a href="/register">Бүртгүүлэх</a>
                <input type="submit" name="login" value="Нэвтрэх">
            </div>

        </form>
    </div>
</body>

</html>