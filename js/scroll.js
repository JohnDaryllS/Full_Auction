// Add this JavaScript to handle the scroll behavior
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    const logo = document.querySelector('.navbar-left');
    
    // Only apply this behavior on mobile devices
    if (window.innerWidth <= 768) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
});