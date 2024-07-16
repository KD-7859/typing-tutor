document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');

    loginBtn.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display = 'flex';
        signupForm.style.display = 'none';
        loginBtn.classList.add('active');
        signupBtn.classList.remove('active');
    });

    signupBtn.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display = 'none';
        signupForm.style.display = 'flex';
        loginBtn.classList.remove('active');
        signupBtn.classList.add('active');
    });
});