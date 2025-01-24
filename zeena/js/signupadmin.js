//displaying outlet locations
const headofficeRadio = document.getElementById('headoffice-admin');
const outletRadio = document.getElementById('outlet-admin');
const outletLocation = document.getElementById('outlet-location');

headofficeRadio.addEventListener('change', () => {
    outletLocation.style.display = 'none';
});

outletRadio.addEventListener('change', () => {
    outletLocation.style.display = 'block';
});

// code to toggle the eye icon
function togglePassword(icon) {
    const passwordInput = icon.previousElementSibling; 
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye"); 
    } else {
        passwordInput.type = "password"; 
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    }
}
//form submission
const form = document.querySelector('.signin-form');
form.addEventListener('submit', function (e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match.');
    }
});
