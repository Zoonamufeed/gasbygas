function togglePassword(icon) {
    const passwordInput = icon.previousElementSibling; // Get the input field
    if (passwordInput.type === "password") {
        passwordInput.type = "text"; // Show the password
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye"); // Change icon to 'eye'
    } else {
        passwordInput.type = "password"; // Hide the password
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash"); // Change icon to 'eye-slash'
    }
}
