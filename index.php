<?php
require_once './captcha/verify.php';
$verified = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $captchaId = $_POST['captcha_id'] ?? '';
    if (verifyCaptcha($captchaId)) {
        $verified = true;
        unset($_SESSION['captcha_' . $captchaId]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FurryID Verification</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      overflow: hidden;
    }

    .container {
      background: #fff;
      color: #333;
      border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      padding: 40px;
      max-width: 500px;
      width: 100%;
      text-align: center;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      margin-bottom: 20px;
    }

    .button {
      background: linear-gradient(45deg, #667eea, #764ba2);
      color: #fff;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      margin-top: 20px;
      display: inline-block;
      text-decoration: none;
    }

    .button:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    form {
      animation: fadeIn 0.8s ease;
    }

    .captcha-container {
      margin: 20px 0;
    }

    img.logo {
      width: 64px;
      border-radius: 50%;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <?php if (!$verified): ?>
      <img class="logo" src="https://gravatar.com/userimage/263388927/a29d9fe8ade6630da27125300e648a27.jpeg?size=256&cache=1748089911022" alt="FurryID Logo"/>
      <h1>Welcome to FurryID</h1>
      <p>Please prove you're not a robot</p>
      <form method="POST">
        <div class="captcha-container">
          <?php include './captcha/captcha.php'; ?>
        </div>
        <button type="submit" class="button">Verify Me</button>
      </form>
    <?php else: ?>
      <h1>You're Verified!</h1>
      <p>
        FurryID is a chaotic but legit CAPTCHA solution. It's Simon Says with audio, JS, CSS, and PHP magic.<br><br>
        Made with ❤️ by Naikia/Noan under the <a href="https://opensource.org/license/mit" target="_blank">MIT License</a>.
      </p>
      <a href="https://github.com/naikia/captcha" class="button" target="_blank">View on GitHub</a>
    <?php endif; ?>
  </div>
</body>
</html>
