const passwordInput = document.getElementById('password');
const togglePasswordButton = document.getElementById('toggle-password');
const loginForm = document.getElementById('admin-login-form');
const loginButton = document.getElementById('login-button');

if (passwordInput && togglePasswordButton) {
togglePasswordButton.addEventListener('click', () => {
const isPasswordHidden = passwordInput.type === 'password';

    passwordInput.type = isPasswordHidden ? 'text' : 'password';
    togglePasswordButton.textContent = isPasswordHidden ? 'Hide' : 'Show';

    togglePasswordButton.setAttribute(
        'aria-label',
        isPasswordHidden ? 'Hide password' : 'Show password'
    );

    togglePasswordButton.setAttribute(
        'aria-pressed',
        String(isPasswordHidden)
    );
});

}

if (loginForm && loginButton) {
loginForm.addEventListener('submit', (event) => {
if (!loginForm.checkValidity()) {
    event.preventDefault();
    loginForm.reportValidity();
    return;
}

    loginButton.disabled = true;
    loginButton.textContent = 'Signing in...';
});

}