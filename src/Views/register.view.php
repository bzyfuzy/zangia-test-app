<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шалгалтын систем - Нэврэх</title>
    <link rel="stylesheet" type="text/css" href="/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/css/login.css">
    <script>
        function validateForm() {
            const email = document.getElementById("reg-email").value;
            const phone = document.getElementById("reg-phone").value;
            const password = document.getElementById("r_password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const phonePattern = /^[0-9]{8}$/;

            if (!emailPattern.test(email)) {
                alert("И-мэйл хаяг буруу байна.");
                return false;
            }

            if (!phonePattern.test(phone)) {
                alert("Утасны дугаар буруу байна.");
                return false;
            }

            if (password !== confirmPassword) {
                alert("Нууц үг болон баталгаажуулах нууц үг таарахгүй байна.");
                return false;
            }

            return true;
        }
    </script>
</head>

<body>
    <div class="login-cont">
        <h1>Бүртгүүлэх хэсэг</h1>
        <?php if (isset($error)) : ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post" onsubmit="return validateForm();">
            <div class="input-field">
                <input type="text" name="lastName" required placeholder="  ">
                <label>Овог</label>
            </div>
            <div class="input-field">
                <input type="text" name="firstName" required placeholder="  ">
                <label>Нэр үг</label>
            </div>
            <div class="input-field">
                <input type="email" name="email" required placeholder="  " id="reg-email">
                <label>И-мэйл</label>
            </div>
            <div class="input-field">
                <input type="text" name="phoneNumber" required placeholder="  " id="reg-phone">
                <label>Утасны дугаар</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" required placeholder="  " id="r_password">
                <label>Нууц үг</label>
            </div>
            <div class="input-field">
                <input type="password" name="confirm_password" required placeholder="  " id="confirm_password">
                <label>Баталгаажуулах Нууц үг</label>
            </div>

            <div class="footer">
                <a href="/login">Нэвтрэх</a>
                <input type="submit" name="register" value="Бүртгүүлэх">
            </div>
        </form>
    </div>
</body>

</html>