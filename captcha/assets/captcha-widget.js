class FurryIDCaptcha {
  constructor(captchaId) {
    this.captchaId = captchaId
    this.gameSequence = []
    this.playerSequence = []
    this.currentRound = 0
    this.isPlayerTurn = false
    this.gameActive = false
    this.maxRounds = 6
    this.colors = ["red", "blue", "green", "yellow"]
    this.audioContext = null
    this.soundEnabled = true

    this.soundFrequencies = {
      red: 220,
      blue: 277,
      green: 330,
      yellow: 440,
    }

    this.initializeAudio()
  }

  initializeAudio() {
    document.addEventListener(
      "click",
      () => {
        if (!this.audioContext) {
          this.audioContext = new (window.AudioContext || window.webkitAudioContext)()
        }
      },
      { once: true },
    )
  }

  playSound(color, duration = 300) {
    if (!this.soundEnabled || !this.audioContext) return

    const oscillator = this.audioContext.createOscillator()
    const gainNode = this.audioContext.createGain()

    oscillator.connect(gainNode)
    gainNode.connect(this.audioContext.destination)

    oscillator.frequency.setValueAtTime(this.soundFrequencies[color], this.audioContext.currentTime)
    oscillator.type = "sine"

    gainNode.gain.setValueAtTime(0, this.audioContext.currentTime)
    gainNode.gain.linearRampToValueAtTime(0.2, this.audioContext.currentTime + 0.01)
    gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + duration / 1000)

    oscillator.start(this.audioContext.currentTime)
    oscillator.stop(this.audioContext.currentTime + duration / 1000)
  }

  startChallenge() {
    const verifyArea = document.getElementById(`verify-area-${this.captchaId}`)
    const challengeArea = document.getElementById(`challenge-${this.captchaId}`)
    const statusDot = document.getElementById(`status-${this.captchaId}`)
    const statusText = document.getElementById(`status-text-${this.captchaId}`)

    verifyArea.style.display = "none"
    challengeArea.style.display = "block"

    statusDot.style.background = "#ffc107"
    statusText.textContent = "Complete challenge"

    this.showMessage("Watch the pattern and repeat it!", "info")
  }

  startGame() {
    this.gameSequence = []
    this.playerSequence = []
    this.currentRound = 0
    this.gameActive = true
    this.isPlayerTurn = false

    const startBtn = document.getElementById(`start-${this.captchaId}`)
    startBtn.disabled = true
    startBtn.textContent = "Playing..."

    this.updateProgress()
    setTimeout(() => this.nextRound(), 1000)
  }

  nextRound() {
    if (this.currentRound >= this.maxRounds) {
      this.gameWon()
      return
    }

    this.currentRound++
    this.playerSequence = []

    const randomColor = this.colors[Math.floor(Math.random() * this.colors.length)]
    this.gameSequence.push(randomColor)

    this.updateRoundDisplay()
    this.updateProgress()

    this.showMessage(`Round ${this.currentRound}: Watch carefully!`, "info")

    setTimeout(() => this.playSequence(), 1000)
  }

  playSequence() {
    this.isPlayerTurn = false
    let i = 0

    const interval = setInterval(() => {
      if (i < this.gameSequence.length) {
        this.flashButton(this.gameSequence[i])
        i++
      } else {
        clearInterval(interval)
        setTimeout(() => {
          this.isPlayerTurn = true
          this.showMessage("Your turn! Repeat the sequence", "info")
        }, 500)
      }
    }, 700)
  }

  flashButton(color) {
    const button = document.querySelector(`#${this.captchaId} .simon-btn.${color}`)
    if (!button) return

    button.classList.add("active")
    this.playSound(color)

    setTimeout(() => {
      button.classList.remove("active")
    }, 300)
  }

  playerInput(color) {
    if (!this.isPlayerTurn || !this.gameActive) return

    this.flashButton(color)
    this.playerSequence.push(color)

    const currentIndex = this.playerSequence.length - 1
    if (this.playerSequence[currentIndex] !== this.gameSequence[currentIndex]) {
      this.gameOver()
      return
    }

    if (this.playerSequence.length === this.gameSequence.length) {
      this.isPlayerTurn = false
      this.showMessage("Correct! Next round...", "success")
      setTimeout(() => this.nextRound(), 1500)
    }
  }

  gameWon() {
    this.gameActive = false
    this.showMessage("🎉 Challenge completed!", "success")

    setTimeout(() => {
      this.markAsVerified()
    }, 1500)
  }

  gameOver() {
    this.gameActive = false
    this.isPlayerTurn = false
    this.showMessage("Wrong sequence! Try again.", "error")

    const startBtn = document.getElementById(`start-${this.captchaId}`)
    startBtn.disabled = false
    startBtn.textContent = "Try Again"

    this.currentRound = 0
    this.updateProgress()
    this.updateRoundDisplay()
  }

  markAsVerified() {
    const challengeArea = document.getElementById(`challenge-${this.captchaId}`)
    const successArea = document.getElementById(`success-${this.captchaId}`)
    const statusDot = document.getElementById(`status-${this.captchaId}`)
    const statusText = document.getElementById(`status-text-${this.captchaId}`)
    const verifiedInput = document.getElementById(`captcha-verified-${this.captchaId}`)
    const checkbox = document.getElementById(`checkbox-${this.captchaId}`)

    challengeArea.style.display = "none"
    successArea.style.display = "block"

    statusDot.classList.add("verified")
    statusText.textContent = "Verified"

    verifiedInput.value = "1"
    checkbox.classList.add("checked")

    fetch(window.location.href, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `captcha_verify=1&captcha_id=${this.captchaId}`,
    })

    const event = new CustomEvent("captchaVerified", {
      detail: { captchaId: this.captchaId },
    })
    document.dispatchEvent(event)
  }

  updateRoundDisplay() {
    const roundEl = document.getElementById(`round-${this.captchaId}`)
    if (roundEl) {
      roundEl.textContent = this.currentRound
    }
  }

  updateProgress() {
    const progress = (this.currentRound / this.maxRounds) * 100
    const progressEl = document.getElementById(`progress-${this.captchaId}`)
    if (progressEl) {
      progressEl.style.width = progress + "%"
    }
  }

  showMessage(text, type) {
    const messageEl = document.getElementById(`message-${this.captchaId}`)
    if (!messageEl) return

    messageEl.textContent = text
    messageEl.className = `game-message ${type}`

    if (type === "success" || type === "error") {
      setTimeout(() => {
        messageEl.textContent = ""
        messageEl.className = "game-message"
      }, 3000)
    }
  }
}

const captchaInstances = {}

function initializeCaptcha(captchaId) {
  captchaInstances[captchaId] = new FurryIDCaptcha(captchaId)
}

function startCaptchaChallenge(captchaId) {
  if (captchaInstances[captchaId]) {
    captchaInstances[captchaId].startChallenge()
  }
}

function startSimonGame(captchaId) {
  if (captchaInstances[captchaId]) {
    captchaInstances[captchaId].startGame()
  }
}

function playerInput(captchaId, color) {
  if (captchaInstances[captchaId]) {
    captchaInstances[captchaId].playerInput(color)
  }
}

function isCaptchaVerified(captchaId) {
  const verifiedInput = document.getElementById(`captcha-verified-${captchaId}`)
  return verifiedInput && verifiedInput.value === "1"
}
