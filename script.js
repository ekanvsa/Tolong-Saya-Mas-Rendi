document.getElementById('toggle-link').addEventListener('click', function (e) {
    e.preventDefault();
    var formTitle = document.getElementById('form-title');
    var toggleMessage = document.getElementById('toggle-message');
    var submitButton = document.getElementById('submit-button');
    var confirmPasswordGroup = document.getElementById('confirm-password-group');

    if (formTitle.textContent === 'Login') {
        formTitle.textContent = 'Register';
        toggleMessage.innerHTML = 'Sudah punya akun? <a href="#" id="toggle-link">Login</a>';
        submitButton.textContent = 'Register';
        confirmPasswordGroup.style.display = 'block';
        document.getElementById('form').action = 'register.php';
    } else {
        formTitle.textContent = 'Login';
        toggleMessage.innerHTML = 'Belum punya akun? <a href="#" id="toggle-link">Register</a>';
        submitButton.textContent = 'Login';
        confirmPasswordGroup.style.display = 'none';
        document.getElementById('form').action = 'login.php';
    }
});
