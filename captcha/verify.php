<?php
session_start();

function verifyCaptcha($captchaId) {
    if (!isset($_SESSION['captcha_' . $captchaId])) {
        return false;
    }
    
    $captchaData = $_SESSION['captcha_' . $captchaId];
    
    if (!$captchaData['verified']) {
        return false;
    }
    
    $timeLimit = 300;
    if (time() - $captchaData['timestamp'] > $timeLimit) {
        unset($_SESSION['captcha_' . $captchaId]);
        return false;
    }
    
    return true;
}

function getCaptchaStatus($captchaId) {
    if (!isset($_SESSION['captcha_' . $captchaId])) {
        return 'not_found';
    }
    
    $captchaData = $_SESSION['captcha_' . $captchaId];
    
    if ($captchaData['verified']) {
        return 'verified';
    }
    
    return 'pending';
}

function cleanupExpiredCaptchas() {
    $timeLimit = 300;
    $currentTime = time();
    
    foreach ($_SESSION as $key => $value) {
        if (strpos($key, 'captcha_') === 0) {
            if (is_array($value) && isset($value['timestamp'])) {
                if ($currentTime - $value['timestamp'] > $timeLimit) {
                    unset($_SESSION[$key]);
                }
            }
        }
    }
}

cleanupExpiredCaptchas();
?>
