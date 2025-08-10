
document.addEventListener('DOMContentLoaded', () => {
    // --- FORM & PAGE SELECTORS ---
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const interestsContainer = document.getElementById('interests-container');
    const dashboardBody = document.querySelector('body.dashboard-light, body.dashboard-dark');
    const forgotPasswordForm = document.getElementById('forgot-password-form');
    const resetPasswordForm = document.getElementById('reset-password-form');

    // --- 1. LOGIN LOGIC ---
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const messageDiv = loginForm.querySelector('#message');
            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());
            const response = await fetch('api/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                sessionStorage.setItem('username', result.username);
                if (result.interests_set) {
                    window.location.href = './dashboard.html';
                } else {
                    window.location.href = './interests.html';
                }
            } else {
                messageDiv.textContent = result.message;
            }
        });
    }

    // --- 2. REGISTRATION LOGIC ---
    if (registerForm) {
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const usernameIcon = document.getElementById('username-icon');
        const passwordIcon = document.getElementById('password-icon');
        const usernameMsg = document.getElementById('username-validation');
        const passwordMsg = document.getElementById('password-validation');
        const registerButton = document.getElementById('register-button');
        let isUsernameValid = false;
        let isPasswordValid = false;
        let debounceTimer;

        const checkFormValidity = () => {
            if (isUsernameValid && isPasswordValid) {
                registerButton.disabled = false;
                registerButton.classList.remove('bg-indigo-400', 'cursor-not-allowed');
                registerButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            } else {
                registerButton.disabled = true;
                registerButton.classList.add('bg-indigo-400', 'cursor-not-allowed');
                registerButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            }
        };

        passwordInput.addEventListener('input', () => {
            if (passwordInput.value.length >= 8) {
                passwordInput.classList.add('input-valid');
                passwordInput.classList.remove('input-invalid');
                passwordIcon.textContent = '✅';
                passwordIcon.classList.add('visible');
                passwordMsg.classList.remove('visible');
                isPasswordValid = true;
            } else {
                passwordInput.classList.add('input-invalid');
                passwordInput.classList.remove('input-valid');
                passwordIcon.textContent = '❌';
                passwordIcon.classList.add('visible');
                passwordMsg.classList.add('visible', 'text-error');
                isPasswordValid = false;
            }
            checkFormValidity();
        });

        usernameInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const username = usernameInput.value;
            usernameIcon.classList.remove('visible');
            usernameMsg.classList.remove('visible');
            usernameInput.classList.remove('input-valid', 'input-invalid');
            isUsernameValid = false;
            if (username.length < 3) {
                checkFormValidity();
                return;
            }
            debounceTimer = setTimeout(async () => {
                const response = await fetch('api/check_username.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: username })
                });
                const result = await response.json();
                if (result.available) {
                    usernameInput.classList.add('input-valid');
                    usernameIcon.textContent = '✅';
                    usernameMsg.textContent = 'Username is available!';
                    usernameMsg.className = 'validation-message visible text-success';
                    isUsernameValid = true;
                } else {
                    usernameInput.classList.add('input-invalid');
                    usernameIcon.textContent = '❌';
                    usernameMsg.textContent = 'Username is already taken.';
                    usernameMsg.className = 'validation-message visible text-error';
                    isUsernameValid = false;
                }
                usernameIcon.classList.add('visible');
                checkFormValidity();
            }, 500);
        });

        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!isUsernameValid || !isPasswordValid) {
                document.getElementById('message').textContent = 'Please fix the errors before submitting.';
                return;
            }
            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries());
            const response = await fetch('api/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                window.location.href = `./token_display.html?token=${result.token}`;
            } else {
                document.getElementById('message').textContent = result.message;
            }
        });
    }

    // --- 3. INTERESTS PAGE LOGIC ---
    if (interestsContainer) {
        const interestsButtons = interestsContainer.querySelectorAll('.interest-btn');
        const submitButton = document.getElementById('submit-interests');
        const messageDiv = document.getElementById('message');
        let selectedInterests = [];

        interestsButtons.forEach(button => {
            button.addEventListener('click', () => {
                const interest = button.textContent;
                button.classList.toggle('bg-indigo-600');
                button.classList.toggle('text-white');
                if (selectedInterests.includes(interest)) {
                    selectedInterests = selectedInterests.filter(i => i !== interest);
                } else {
                    selectedInterests.push(interest);
                }
                if (selectedInterests.length === 3) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('bg-indigo-400', 'cursor-not-allowed');
                    submitButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    messageDiv.textContent = '';
                } else {
                    submitButton.disabled = true;
                    submitButton.classList.add('bg-indigo-400', 'cursor-not-allowed');
                    submitButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                    messageDiv.textContent = selectedInterests.length > 3 ? 'You can only select 3 interests.' : `Select ${3 - selectedInterests.length} more.`;
                }
            });
        });

        submitButton.addEventListener('click', async () => {
            if (selectedInterests.length !== 3) return;
            const response = await fetch('api/save_interests.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ interests: selectedInterests })
            });
            const result = await response.json();
            if (result.success) {
                window.location.href = './dashboard.html';
            } else {
                messageDiv.textContent = result.message || 'An error occurred.';
            }
        });
    }

    // --- 4. DASHBOARD LOGIC ---
    if (dashboardBody) {
        const usernameDisplay = document.getElementById('username-display');
        const interestsDisplayContainer = document.getElementById('interests-display-container');
        const interestsEditContainer = document.getElementById('interests-edit-container');
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const editBtn = document.getElementById('edit-interests-btn');
        const saveBtn = document.getElementById('save-interests-btn');
        let currentUserInterests = [];
        const allPossibleInterests = ["CODING", "GAMING", "DANCE", "MUSIC", "BEAUTY", "SPORTS"];

        async function fetchUserData() {
            const username = sessionStorage.getItem('username') || 'USER';
            usernameDisplay.textContent = username.toUpperCase();
            // In a real app, you would fetch interests from a get_user_data.php endpoint
            // We'll simulate for now
            currentUserInterests = ["CODING", "GAMING", "MUSIC"];
            renderInterests('display');
        }

        function renderInterests(mode) {
            if (mode === 'display') {
                interestsDisplayContainer.innerHTML = '';
                currentUserInterests.forEach(interest => {
                    const pill = document.createElement('span');
                    pill.className = 'interest-pill';
                    pill.textContent = interest;
                    interestsDisplayContainer.appendChild(pill);
                });
            } else if (mode === 'edit') {
                interestsEditContainer.innerHTML = '';
                allPossibleInterests.forEach(interest => {
                    const btn = document.createElement('button');
                    btn.className = 'interest-btn-edit';
                    btn.textContent = interest;
                    if (currentUserInterests.includes(interest)) {
                        btn.classList.add('selected');
                    }
                    btn.addEventListener('click', () => {
                        if (btn.classList.contains('selected')) {
                            btn.classList.remove('selected');
                            currentUserInterests = currentUserInterests.filter(i => i !== interest);
                        } else {
                            if (currentUserInterests.length < 3) {
                                btn.classList.add('selected');
                                currentUserInterests.push(interest);
                            } else {
                                alert('You can only select up to 3 interests.');
                            }
                        }
                    });
                    interestsEditContainer.appendChild(btn);
                });
            }
        }

        editBtn.addEventListener('click', () => {
            renderInterests('edit');
            interestsDisplayContainer.classList.add('hidden');
            interestsEditContainer.classList.remove('hidden');
            editBtn.classList.add('hidden');
            saveBtn.classList.remove('hidden');
        });

        saveBtn.addEventListener('click', async () => {
            if (currentUserInterests.length !== 3) {
                alert('Please select exactly 3 interests.');
                return;
            }
            // In a real app, you would call fetch('api/save_interests.php', ...)
            console.log('Saving interests:', currentUserInterests);
            renderInterests('display');
            interestsDisplayContainer.classList.remove('hidden');
            interestsEditContainer.classList.add('hidden');
            editBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
        });

        darkModeToggle.addEventListener('change', () => {
            document.body.classList.toggle('dashboard-dark');
            document.body.classList.toggle('dashboard-light');
        });

        fetchUserData();
    }

    // --- 5. FORGOT PASSWORD LOGIC ---
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const messageDiv = forgotPasswordForm.querySelector('#message');
            const formData = new FormData(forgotPasswordForm);
            const data = Object.fromEntries(formData.entries());
            const response = await fetch('api/validate_token.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                window.location.href = `./reset_password.html?token=${data.token}`;
            } else {
                messageDiv.textContent = result.message;
            }
        });
    }

    // --- 6. RESET PASSWORD LOGIC ---
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const messageDiv = document.querySelector('#reset-password-form ~ #message');
            const formData = new FormData(resetPasswordForm);
            const data = Object.fromEntries(formData.entries());
            if (data.password.length < 8) {
                messageDiv.textContent = 'Password must be at least 8 characters.';
                messageDiv.className = 'text-center text-red-500';
                return;
            }
            if (data.password !== data.confirm_password) {
                messageDiv.textContent = 'Passwords do not match.';
                messageDiv.className = 'text-center text-red-500';
                return;
            }
            const response = await fetch('api/reset_password.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                messageDiv.textContent = result.message;
                messageDiv.className = 'text-center text-green-500';
                setTimeout(() => {
                    window.location.href = './login.html';
                }, 3000);
            } else {
                messageDiv.textContent = result.message;
                messageDiv.className = 'text-center text-red-500';
            }
        });
    }
});
