document.addEventListener('DOMContentLoaded', function() {
    const legalModal = document.getElementById('legalModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const acceptBtn = document.getElementById('acceptBtn');
    const denyBtn = document.getElementById('denyBtn');
    
    // Check if policies were already accepted
    if (!localStorage.getItem('policiesAccepted')) {
        // Show modal after slight delay
        setTimeout(() => {
            legalModal.classList.add('active');
        }, 1000);
    }
    
    // Tab switching functionality
    const tabs = document.querySelectorAll('.legal-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.legal-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.legal-tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            const tabId = tab.getAttribute('data-tab');
            document.getElementById(`${tabId}-content`).classList.add('active');
        });
    });
    
    // Close button
    closeModalBtn.addEventListener('click', () => {
        legalModal.classList.remove('active');
        // Optionally redirect or show message
        // window.location.href = 'about.php';
    });
    
    // Accept button
    acceptBtn.addEventListener('click', () => {
        localStorage.setItem('policiesAccepted', 'true');
        legalModal.classList.remove('active');
    });
    
    // Deny button
    denyBtn.addEventListener('click', () => {
        // Redirect to about page or show message
        window.location.href = 'about.php';
    });
    
    // Close when clicking outside modal
    legalModal.addEventListener('click', (e) => {
        if (e.target === legalModal) {
            legalModal.classList.remove('active');
        }
    });
    
    // Close with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && legalModal.classList.contains('active')) {
            legalModal.classList.remove('active');
        }
    });
});