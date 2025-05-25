<?php
session_start();

$captcha_id = uniqid('furryid_');
$_SESSION['captcha_' . $captcha_id] = [
    'verified' => false,
    'timestamp' => time()
];

$captcha_config = [
    'theme' => 'default',
    'difficulty' => 'medium',
    'sound_enabled' => true,
    'width' => '100%',
    'height' => '400px'
];

if (isset($_POST['captcha_verify']) && isset($_POST['captcha_id'])) {
    $captcha_id = $_POST['captcha_id'];
    if (isset($_SESSION['captcha_' . $captcha_id])) {
        $_SESSION['captcha_' . $captcha_id]['verified'] = true;
        echo json_encode(['success' => true, 'captcha_id' => $captcha_id]);
        exit;
    }
}
?>

<div class="furryid-captcha-container" id="<?php echo $captcha_id; ?>" data-captcha-id="<?php echo $captcha_id; ?>">
    <link rel="stylesheet" href="./captcha/assets/captcha-widget.css">
    
    <div class="captcha-widget">
        <div class="widget-header">
            <div class="logo-mini">
                <img src="https://gravatar.com/userimage/263388927/a29d9fe8ade6630da27125300e648a27.jpeg?size=64" 
                     alt="FurryID" class="logo-img">
                <span class="logo-text">FurryID</span>
            </div>
            <div class="status-indicator">
                <div class="status-dot" id="status-<?php echo $captcha_id; ?>"></div>
                <span class="status-text" id="status-text-<?php echo $captcha_id; ?>">Click to verify</span>
            </div>
        </div>
        
        <div class="verification-area" id="verify-area-<?php echo $captcha_id; ?>">
            <button class="verify-button" onclick="startCaptchaChallenge('<?php echo $captcha_id; ?>')">
                <span class="verify-icon">🤖</span>
                <span class="verify-text">I'm not a robot</span>
                <div class="verify-checkbox" id="checkbox-<?php echo $captcha_id; ?>">
                    <div class="checkmark">✓</div>
                </div>
            </button>
        </div>
        
        <div class="challenge-area" id="challenge-<?php echo $captcha_id; ?>" style="display: none;">
            <div class="challenge-header">
                <h3>🎮 Complete the Simon Says Challenge</h3>
                <p>Watch the pattern and repeat it!</p>
            </div>
            
            <div class="game-info">
                <div class="round-display">
                    Round <span id="round-<?php echo $captcha_id; ?>">1</span> of 6
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-<?php echo $captcha_id; ?>"></div>
                </div>
            </div>
            
            <div class="simon-board">
                <button class="simon-btn red" data-color="red" onclick="playerInput('<?php echo $captcha_id; ?>', 'red')">
                    <span>RED</span>
                </button>
                <button class="simon-btn blue" data-color="blue" onclick="playerInput('<?php echo $captcha_id; ?>', 'blue')">
                    <span>BLUE</span>
                </button>
                <button class="simon-btn green" data-color="green" onclick="playerInput('<?php echo $captcha_id; ?>', 'green')">
                    <span>GREEN</span>
                </button>
                <button class="simon-btn yellow" data-color="yellow" onclick="playerInput('<?php echo $captcha_id; ?>', 'yellow')">
                    <span>YELLOW</span>
                </button>
            </div>
            
            <div class="game-controls">
                <button class="start-game-btn" id="start-<?php echo $captcha_id; ?>" onclick="startSimonGame('<?php echo $captcha_id; ?>')">
                    Start Challenge
                </button>
                <div class="game-message" id="message-<?php echo $captcha_id; ?>"></div>
            </div>
        </div>
        
        <div class="success-area" id="success-<?php echo $captcha_id; ?>" style="display: none;">
            <div class="success-content">
                <div class="success-icon">🎉</div>
                <h3>Verification Complete!</h3>
                <p>You've successfully proven you're human! 🐾</p>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="captcha_verified" id="captcha-verified-<?php echo $captcha_id; ?>" value="0">
    <input type="hidden" name="captcha_id" value="<?php echo $captcha_id; ?>">
    
    <script src="./captcha/assets/captcha-widget.js"></script>
    <script>
        initializeCaptcha('<?php echo $captcha_id; ?>');
    </script>
</div>
