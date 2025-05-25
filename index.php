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
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 20px;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .container {
      background: #fff;
      color: #333;
      border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      padding: 30px 20px;
      width: 100%;
      max-width: 500px;
      text-align: center;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      margin-bottom: 16px;
      font-size: 1.8rem;
    }

    p {
      font-size: 1rem;
      line-height: 1.5;
    }

    a {
      color: #764ba2;
      text-decoration: underline;
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

    .captcha-container {
      margin: 20px 0;
    }

    img.logo {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      margin-bottom: 16px;
    }

    @media (max-width: 500px) {
      h1 {
        font-size: 1.5rem;
      }
      .button {
        font-size: 15px;
        padding: 10px 20px;
      }
      .container {
        padding: 24px 16px;
      }
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
        <strong>FurryID</strong> is a chaotic but legit CAPTCHA.<br>
        It's Simon Says with audio, JavaScript, CSS, and PHP wizardry.<br><br>
        Made with ❤️ by Naikia/Noan.<br>
        Licensed under the <a href="https://opensource.org/license/mit" target="_blank">MIT License</a>.
      </p>
      <a href="https://github.com/naikia/captcha" class="button" target="_blank">View on GitHub</a>
    <?php endif; ?>
  </div>
</body>
</html>
