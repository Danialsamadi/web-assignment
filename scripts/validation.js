document.getElementById('registerForm').addEventListener('submit', function(event) {
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    let errorMessage = '';

    if (username.length < 3) {
        errorMessage += 'Username must be at least 3 characters long.\n';
    }

    const emailPattern = /^[^@]+@[^@]+\.[^@]+$/;
    if (!emailPattern.test(email)) {
        errorMessage += 'Invalid email format.\n';
    }

    if (password.length < 6) {
        errorMessage += 'Password must be at least 6 characters long.\n';
    }

    if (errorMessage !== '') {
        alert(errorMessage);
        event.preventDefault();
    }
});
