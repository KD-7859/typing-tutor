<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Bar</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
:root {
    --primary-color: #6c63ff;
    --secondary-color: #f5f5f5;
    --text-color: #333;
    --error-color: #ff6b6b;
    --success-color: #51cf66;
    --gradient: linear-gradient(135deg, #6c63ff, #4834d4);
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--secondary-color);
    color: var(--text-color);
    background-image: 
        radial-gradient(circle at top left, rgba(108, 99, 255, 0.1) 0%, transparent 30%),
        radial-gradient(circle at bottom right, rgba(72, 52, 212, 0.1) 0%, transparent 30%);
    background-size: 100% 100%;
    background-repeat: no-repeat;
    min-height: 100vh;
}

/* Loader Styles */
#loader-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--gradient);
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.loader-container {
    position: relative;
    width: 100px;
    height: 100px;
}

.pulse-loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background-color: #fff;
    border-radius: 50%;
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(0.5);
        opacity: 0;
    }
    50% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.5);
        opacity: 0;
    }
}

.loader-text {
    color: #fff;
    font-size: 24px;
    margin-top: 20px;
    opacity: 0;
    animation: fadeIn 1s ease-out forwards 0.5s;
}

@keyframes fadeIn {
    to {
        opacity: 1;
    }
}

/* Existing Styles */
.navbar {
    background: var(--gradient);
    color: #fff;
    padding: 1rem;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
}

.navbar-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.navbar-brand {
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
    text-decoration: none;
}

.navbar-right {
    display: flex;
    align-items: center;
}

.navbar-right .username,
.navbar-right .logout-button {
    display: inline-block;
    margin-right: 1rem;
}

.logout-button {
    background-color: transparent;
    color: white;
    border: 2px solid white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.logout-button:hover {
    background-color: white;
    color: var(--primary-color);
}

.container {
    max-width: 800px;
    margin: 80px auto 20px;
    background-color: #fff;
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    padding: 2rem;
}

.controls {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-bottom: 2rem;
}

.timer-card {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 1rem 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    width: 100%;
    max-width: 200px;
}

select, #timer-input, button {
    padding: 0.7rem 1.2rem;
    border: 1px solid var(--primary-color);
    border-radius: 50px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

select, #timer-input {
    background: #fff;
    color: var(--text-color);
}

#timer-input {
    width: 100%;
    text-align: center;
}

button {
    background: var(--gradient);
    color: #fff;
    cursor: pointer;
}

button:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

#timer-display {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
    font-family: 'Roboto Mono', monospace;
}

#text-display {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    line-height: 1.8;
    text-align: left;
    background-color: #f9f9f9;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
    white-space: pre-wrap;
    position: relative;
    overflow: hidden;
    height: auto;
    display: inline-block;
    width: 100%;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* New cursor styles */
#text-display span.cursor {
    position: relative;
}

#text-display span.cursor::after {
    content: '';
    position: absolute;
    top: 0;
    right: -2px;
    width: 2px;
    height: 100%;
    background-color: #000;
    animation: blink 0.7s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

.progress-bar-container {
    margin: 1rem 0;
    height: 10px;
    background-color: #ddd;
    border-radius: 5px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: var(--gradient);
    width: 0%;
    transition: width 0.3s;
}

#stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    background-color: var(--secondary-color);
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 1.5rem;
}

.stat {
    text-align: center;
    padding: 0.5rem;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat i {
    margin-right: 0.5rem;
}

.correct { color: var(--success-color); }
.incorrect { 
    color: var(--error-color); 
    text-decoration: underline; 
    background-color: rgba(231, 76, 60, 0.1);
}

#history {
    margin-top: 2rem;
    background-color: var(--secondary-color);
    padding: 1.5rem;
    border-radius: 12px;
}

#history h2 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

#history-list {
    list-style-type: none;
    padding: 0;
}

#history-list li {
    background-color: #fff;
    padding: 0.8rem;
    margin-bottom: 0.8rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

#history-list li:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.result-popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    z-index: 1000;
    text-align: center;
    max-width: 400px;
    width: 90%;
}

.result-popup h2 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
}

.result-details {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.result-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: var(--secondary-color);
    padding: 1rem;
    border-radius: 8px;
}

.result-item i {
    font-size: 1.5rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.result-item span {
    font-size: 0.9rem;
    margin-bottom: 0.3rem;
}

.result-item strong {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.close-popup {
    background: var(--gradient);
    color: white;
    border: none;
    padding: 0.7rem 1.2rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

.close-popup:hover {
    opacity: 0.9;
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

#backspace-toggle.disabled {
    background: var(--error-color);
}

.hamburger-menu {
    display: none;
    flex-direction: column;
    justify-content: space-around;
    width: 2rem;
    height: 2rem;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    z-index: 10;
}

.hamburger-menu span {
    width: 2rem;
    height: 0.25rem;
    background: white;
    border-radius: 10px;
    transition: all 0.3s linear;
    position: relative;
    transform-origin: 1px;
}

.menu-items {
    display: none;
    flex-direction: column;
    justify-content: flex-start;
    background: var(--gradient);
    height: 100vh;
    width: 100%;
    text-align: left;
    padding: 2rem;
    position: fixed;
    top: 0;
    right: 0;
    transition: transform 0.3s ease-in-out;
    transform: translateX(100%);
}

.menu-items.open {
    display: flex;
    transform: translateX(0);
}

.menu-items .username {
    color: white;
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.menu-items .logout-button {
    width: 100%;
    margin-top: 1rem;
    opacity: 1;
}

.hamburger-menu.open span:first-child {
    transform: rotate(45deg);
}

.hamburger-menu.open span:nth-child(2) {
    opacity: 0;
}

.hamburger-menu.open span:nth-child(3) {
    transform: rotate(-45deg);
}

@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    .controls {
        flex-direction: column;
        align-items: stretch;
    }
    .timer-card {
        margin: 1rem auto;
    }
    select, #timer-input, button {
        width: 100%;
        margin-bottom: 10px;
    }
    .hamburger-menu {
        display: flex;
    }
    .navbar-right .username,
    .navbar-right .logout-button {
        display: none;
    }
    .menu-items {
        width: 100%;
    }
    .logout-button {
        margin-top: 1rem;
    }
}

@media (max-width: 600px) {
    #stats {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 400px) {
    #stats {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>
<div id="loader-wrapper">
    <div class="loader-container">
        <div class="pulse-loader"></div>
    </div>
    <h2 class="loader-text">Welcome to Typing Bar</h2>
</div>

<nav class="navbar">
    <div class="navbar-content">
        <a href="#" class="navbar-brand">Typing Bar</a>
        <div class="navbar-right">
            <span class="username">Welcome, <?php echo $username; ?></span>
            <button id="logout-button" class="logout-button">Logout</button>
            <div class="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <div class="menu-items">
        <span class="username">Welcome, <?php echo $username; ?></span>
        <button id="mobile-logout-button" class="logout-button">Logout</button>
    </div>
</nav>
<div class="container">
    <div class="controls">
        <select id="difficulty-select">
            <option value="easy">Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
        </select>
        <div class="timer-container">
            <input type="number" id="timer-input" min="10" max="60000" value="60" placeholder="Timer (seconds)">
        </div>
        <button id="new-test-button">New Test</button>
        <button id="start-test-button">Start Test</button>
        <button id="backspace-toggle">Backspace Allow: On</button>
    </div>
        <div id="text-display"></div>
        <div class="progress-bar-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <div class="timer-card">
            <div id="timer-display">0:00</div>
        </div>
        <div id="stats">
            <div class="stat">
                <i class="fas fa-tachometer-alt"></i>WPM:<span id="wpm">0</span>
            </div>
            <div class="stat">
                <i class="fas fa-bullseye"></i>Accuracy:<span id="accuracy">100%</span>
            </div>
            <div class="stat">
                <i class="fas fa-exclamation-triangle"></i>Errors:<span id="errors">0</span>
            </div>
        </div>
        <div id="history">
            <h2>History</h2>
            <ul id="history-list"></ul>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>
    <div class="result-popup" id="result-popup">
    <h2>Test Results</h2>
    <div class="result-details">
        <div class="result-item">
            <i class="fas fa-tachometer-alt"></i>
            <span>WPM</span>
            <strong id="result-wpm"></strong>
        </div>
        <div class="result-item">
            <i class="fas fa-bullseye"></i>
            <span>Accuracy</span>
            <strong id="result-accuracy"></strong>
        </div>
        <div class="result-item">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Errors</span>
            <strong id="result-errors"></strong>
        </div>
    </div>
    <button class="close-popup" id="close-popup">Close</button>
</div>

<script>
const textDisplay = document.getElementById('text-display');
const wpmDisplay = document.getElementById('wpm');
const accuracyDisplay = document.getElementById('accuracy');
const errorsDisplay = document.getElementById('errors');
const newTestButton = document.getElementById('new-test-button');
const progressBar = document.getElementById('progress-bar');
const difficultySelect = document.getElementById('difficulty-select');
const historyList = document.getElementById("history-list");
const backspaceToggle = document.getElementById('backspace-toggle');
const timerInput = document.getElementById('timer-input');
const timerDisplay = document.getElementById('timer-display');
const startTestButton = document.getElementById('start-test-button');

let currentText = '';
let typedText = '';
let isTestActive = false;
let isTestReady = false;
let hasStartedTyping = false;
let startTime;
let totalErrors = 0;
let totalCharacters = 0;
let isBackspaceAllowed = true;
let timerInterval;
let remainingTime;

const words = [
    "the", "be", "of", "and", "a", "to", "in", "he", "have", "it", "that", "for", "they", "with", "as", "not",
    "on", "she", "at", "by", "this", "we", "you", "do", "but", "from", "or", "which", "one", "would", "all",
    "will", "there", "say", "who", "make", "when", "can", "more", "if", "no", "man", "out", "other", "so",
    "what", "time", "up", "go", "about", "than", "into", "could", "state", "only", "new", "year", "some",
    "take", "come", "these", "know", "see", "use", "get", "like", "then", "first", "any", "work", "now",
    "may", "such", "give", "over", "think", "most", "even", "find", "day", "also", "after", "way", "many",
    "must", "look", "before", "great", "back", "through", "long", "where", "much", "should", "well", "people",
    "down", "own", "just", "because", "good", "each", "those", "feel", "seem", "how", "high", "too", "place",
    "little", "world", "very", "still", "nation", "hand", "old", "life", "tell", "write", "become", "here",
    "show", "house", "both", "between", "need", "mean", "call", "develop", "under", "last", "right", "move",
    "thing", "general", "school", "never", "same", "another", "begin", "while", "number", "part", "turn",
    "real", "leave", "might", "want", "point", "form", "off", "child", "few", "small", "since", "against",
    "ask", "late", "home", "interest", "large", "person", "end", "open", "public", "follow", "during",
    "present", "without", "again", "hold", "govern", "around", "possible", "head", "consider", "word",
    "program", "problem", "however", "lead", "system", "set", "order", "eye", "plan", "run", "keep", "face",
    "fact", "group", "play", "stand", "increase", "early", "course", "change", "help", "line"
];

function generateRandomParagraph(difficulty) {
    const wordCount = difficulty === 'easy' ? 30 : difficulty === 'medium' ? 50 : 70;
    let paragraph = [];

    for (let i = 0; i < wordCount; i++) {
        const randomWord = words[Math.floor(Math.random() * words.length)];
        paragraph.push(i === 0 ? randomWord.charAt(0).toUpperCase() + randomWord.slice(1) : randomWord);
    }

    for (let i = 1; i < paragraph.length; i++) {
        if (Math.random() < 0.1) {
            paragraph[i-1] += '.';
            paragraph[i] = paragraph[i].charAt(0).toUpperCase() + paragraph[i].slice(1);
        }
    }
    paragraph[paragraph.length - 1] += '.';

    return paragraph.join(' ');
}

function setText() {
    const difficulty = difficultySelect.value;
    currentText = generateRandomParagraph(difficulty);
    textDisplay.innerHTML = currentText.split('').map(char => `<span>${char}</span>`).join('');
    typedText = '';
    updateProgressBar();
    updateTextDisplay();
}

function updateProgressBar() {
    if (isTestActive) {
        const textLength = currentText.length;
        const charsTyped = typedText.length;
        const percentage = (charsTyped / textLength) * 100;
        progressBar.style.width = `${Math.min(percentage, 100)}%`;
    }
}

function resetTest() {
    typedText = '';
    wpmDisplay.textContent = '0';
    startTime = null;
    accuracyDisplay.textContent = '100%';
    errorsDisplay.textContent = '0';
    difficultySelect.disabled = false;
    timerInput.disabled = false;
    isTestActive = false;
    isTestReady = false;
    hasStartedTyping = false;
    progressBar.style.width = '0%';
    totalErrors = 0;
    totalCharacters = 0;
    isBackspaceAllowed = true;
    backspaceToggle.textContent = 'Backspace Allow: On';
    backspaceToggle.classList.remove('disabled');
    startTestButton.disabled = false;
    resetTimer();
    setText();
    updateStats();
}

function prepareTest() {
    isTestReady = true;
    difficultySelect.disabled = true;
    timerInput.disabled = true;
    startTestButton.disabled = true;
    setText();
}

function startTest() {
    if (!isTestReady) return;
    isTestActive = true;
    startTime = new Date().getTime();
    startTimer();
}

function startTimer() {
    remainingTime = parseInt(timerInput.value, 10);
    if (isNaN(remainingTime) || remainingTime < 10) {
        remainingTime = 60; // Default to 60 seconds if invalid input
    }
    updateTimerDisplay();
    timerInterval = setInterval(() => {
        remainingTime--;
        updateTimerDisplay();
        if (remainingTime <= 0) {
            clearInterval(timerInterval);
            endTest();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(remainingTime / 60);
    const seconds = remainingTime % 60;
    timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
}

function resetTimer() {
    clearInterval(timerInterval);
    timerDisplay.textContent = '0:00';
}

function addToHistory(wpm, accuracy, errors) {
    const entry = {
        wpm,
        accuracy,
        errors,
        date: new Date().toLocaleString()
    };

    fetch('save_history.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            wpm: entry.wpm,
            accuracy: entry.accuracy,
            errors: entry.errors
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            fetchHistory();
        } else {
            console.error('Error saving history:', data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function fetchHistory() {
    fetch('get_history.php')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            historyList.innerHTML = '';
            data.history.forEach(entry => {
                const li = document.createElement("li");
                li.textContent = `WPM: ${entry.wpm} | Accuracy: ${entry.accuracy}% | Errors: ${entry.errors} | Date: ${new Date(entry.date).toLocaleString()}`;
                historyList.appendChild(li);
            });
        } else {
            console.error('Error fetching history:', data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function calculateErrors(original, typed) {
    let newErrors = 0;
    for (let i = totalCharacters; i < typed.length; i++) {
        if (typed[i] !== original[i]) {
            newErrors++;
        }
    }
    return newErrors;
}

function endTest() {
    isTestActive = false;
    clearInterval(timerInterval);

    const endTime = new Date().getTime();
    const timeElapsed = (endTime - startTime) / 1000 / 60;

    const wordsTyped = typedText.trim().split(/\s+/).length;
    const wpm = Math.round(wordsTyped / timeElapsed);

    const accuracy = Math.max(0, Math.round(((totalCharacters - totalErrors) / totalCharacters) * 100));

    accuracyDisplay.textContent = `${accuracy}%`;

    wpmDisplay.textContent = wpm;
    errorsDisplay.textContent = totalErrors;

    addToHistory(wpm, accuracy, totalErrors);

    showResultPopup(wpm, accuracy, totalErrors);
}

function updateTextDisplay() {
    const spanElements = textDisplay.querySelectorAll('span');
    spanElements.forEach((span, index) => {
        const char = span.textContent;
        span.classList.remove('cursor');
        if (index < typedText.length) {
            if (typedText[index] === char) {
                span.className = 'correct';
            } else {
                span.className = 'incorrect';
            }
        } else {
            span.className = '';
        }
        
        if (index === typedText.length) {
            span.classList.add('cursor');
        }
    });
}

function updateStats() {
    if (isTestActive) {
        const currentTime = new Date().getTime();
        const timeElapsed = (currentTime - startTime) / 1000 / 60;

        const wordsTyped = typedText.trim().split(/\s+/).length;
        const wpm = Math.round(wordsTyped / timeElapsed);

        const newErrors = calculateErrors(currentText, typedText);
        totalErrors += newErrors;
        const newCharacters = typedText.length - totalCharacters;
        totalCharacters += newCharacters;

        const accuracy = totalCharacters > 0 ? Math.max(0, Math.round(((totalCharacters - totalErrors) / totalCharacters) * 100)) : 100;

        accuracyDisplay.textContent = `${accuracy}%`;

        wpmDisplay.textContent = isNaN(wpm) || !isFinite(wpm) ? '0' : wpm;
        errorsDisplay.textContent = totalErrors;
    }
}

function showResultPopup(wpm, accuracy, errors) {
    document.getElementById('result-wpm').textContent = wpm;
    document.getElementById('result-accuracy').textContent = accuracy + '%';
    document.getElementById('result-errors').textContent = errors;
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('result-popup').style.display = 'block';
}

function hideResultPopup() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('result-popup').style.display = 'none';
}

document.addEventListener('keydown', (e) => {
    if (!isTestReady) return;
    
    if (!isTestActive && !hasStartedTyping && e.key.length === 1) {
        console.log("First keypress, starting test");
        startTest();
        hasStartedTyping = true;
    }

    if (!isTestActive || (isTestActive && !hasStartedTyping)) return;

    if (e.key === 'Backspace') {
        if (isBackspaceAllowed && typedText.length > 0) {
            typedText = typedText.slice(0, -1);
            updateTextDisplay();
            updateStats();
        }
    } else if (e.key === ' ') {
        e.preventDefault();
        if (typedText.length < currentText.length) {
            typedText += ' ';
            updateTextDisplay();
            updateStats();
        }
    } else if (e.key.length === 1) {
        if (typedText.length < currentText.length) {
            typedText += e.key;
            updateTextDisplay();
            updateStats();
        }
    }

    updateProgressBar();

    if (typedText.length >= currentText.length) {
        endTest();
    }
});

newTestButton.addEventListener('click', () => {
    resetTest();
});

startTestButton.addEventListener('click', prepareTest);

backspaceToggle.addEventListener('click', () => {
    isBackspaceAllowed = !isBackspaceAllowed;
    backspaceToggle.textContent = `Backspace Allow: ${isBackspaceAllowed ? 'On' : 'Off'}`;
    backspaceToggle.classList.toggle('disabled', !isBackspaceAllowed);
});

document.getElementById('close-popup').addEventListener('click', hideResultPopup);

document.getElementById('overlay').addEventListener('click', hideResultPopup);

document.addEventListener('DOMContentLoaded', () => {
    const logoutButton = document.getElementById('logout-button');
    const mobileLogoutButton = document.getElementById('mobile-logout-button');
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const menuItems = document.querySelector('.menu-items');
    
    function logout() {
        fetch('logout.php', {
            method: 'POST',
            credentials: 'same-origin'
        }).then(() => {
            window.location.href = 'login.php';
        }).catch(error => {
            console.error('Error:', error);
            alert('An error occurred during logout. Please try again.');
        });
    }

    logoutButton.addEventListener('click', logout);
    mobileLogoutButton.addEventListener('click', logout);

    hamburgerMenu.addEventListener('click', () => {
        hamburgerMenu.classList.toggle('open');
        menuItems.classList.toggle('open');
    });

    // Close menu when clicking outside
    document.addEventListener('click', (event) => {
        if (!hamburgerMenu.contains(event.target) && !menuItems.contains(event.target)) {
            hamburgerMenu.classList.remove('open');
            menuItems.classList.remove('open');
        }
    });

    // Loader functionality
    const loaderWrapper = document.getElementById('loader-wrapper');
    setTimeout(() => {
        loaderWrapper.style.opacity = '0';
        setTimeout(() => {
            loaderWrapper.style.display = 'none';
        }, 500);
    }, 2000); // Adjust this value to control how long the loader is displayed
});

function initialize() {
    resetTest();
    fetchHistory();
}

initialize();
</script>
</body>
</html>