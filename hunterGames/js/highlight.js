const currentUrl = window.location.href;
const navLinks = document.querySelectorAll('nav a');
navLinks.forEach(function (link) {
    if (link.href === currentUrl) {
        link.classList.add('highlighted');
    }
});