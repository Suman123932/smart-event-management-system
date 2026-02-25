// --- DOM Elements ---
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const toRegister = document.getElementById('toRegister');
const toLogin = document.getElementById('toLogin');
const registerSection = document.getElementById('registerSection');
const loginError = document.getElementById('loginError');
const regError = document.getElementById('regError');
const dashboard = document.getElementById('dashboard');

// --- Toggle Forms ---
toRegister.addEventListener('click', (e) => {
    e.preventDefault();
    registerSection.classList.remove('hidden');
    document.querySelector('.login').classList.add('hidden');
    clearErrors();
});

toLogin.addEventListener('click', (e) => {
    e.preventDefault();
    registerSection.classList.add('hidden');
    document.querySelector('.login').classList.remove('hidden');
    clearErrors();
});

function clearErrors() {
    loginError.innerText = "";
    regError.innerText = "";
}

// --- Registration Logic ---
registerForm.addEventListener('submit', (e) => {
    e.preventDefault();

    // Get values
    const id = document.getElementById('regId').value;
    const name = document.getElementById('regName').value;
    const email = document.getElementById('regEmail').value;
    const role = document.getElementById('regRole').value;
    const password = document.getElementById('regPassword').value;

    // Get existing users from LocalStorage
    const users = JSON.parse(localStorage.getItem('users')) || [];

    // Check if email already exists
    const userExists = users.find(user => user.email === email);

    if (userExists) {
        regError.innerText = "Email already registered!";
        return;
    }

    // Create new user object
    const newUser = { id, name, email, role, password };
    
    // Save to LocalStorage
    users.push(newUser);
    localStorage.setItem('users', JSON.stringify(users));

    alert("Registration Successful! Please Login.");
    registerForm.reset();
    
    // Switch back to login
    toLogin.click();
});

// --- Login Logic ---
loginForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    // Retrieve users
    const users = JSON.parse(localStorage.getItem('users')) || [];

    // Find user
    const user = users.find(u => u.email === email && u.password === password);

    if (user) {
        // Login Successful
        loginError.innerText = "";
        
        // Show Dashboard
        document.querySelector('.container').classList.add('hidden');
        dashboard.classList.remove('hidden');
        
        // Set Dashboard Info
        document.getElementById('userNameDisplay').innerText = user.name;
        document.getElementById('userRoleDisplay').innerText = user.role;
        
        // Store current session (optional)
        sessionStorage.setItem('currentUser', JSON.stringify(user));
        
    } else {
        loginError.innerText = "Invalid Email or Password!";
    }
});

// --- Logout Logic ---
function logout() {
    sessionStorage.removeItem('currentUser');
    dashboard.classList.add('hidden');
    document.querySelector('.container').classList.remove('hidden');
    loginForm.reset();
}