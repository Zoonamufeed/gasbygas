//eye toggle
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
// submission
const form = document.querySelector('.signin-form');
form.addEventListener('submit', function (e) {
    // Getting form input values
    console.log("Form submission triggered")
    const email = document.querySelector('input[type="email"]').value;
    const password = document.querySelector('input[type="password"]').value;

    // Checking if email and password are filled
    if (email.trim() === "" || password.trim() === "") {
        e.preventDefault(); // Preventing form submission
        alert('Please fill in both email and password.');
        console.log("Validation failed: Empty fields");
    }
});
