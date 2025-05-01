function openMessageModal(contactId) {
    // Fetch message details via AJAX
    fetch('get_contact.php?id=' + contactId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('messageSubject').textContent = data.subject;
            document.getElementById('messageSender').textContent = data.user_name || data.name;
            document.getElementById('messageEmail').textContent = data.email;
            document.getElementById('messageDate').textContent = new Date(data.created_at).toLocaleString();
            document.getElementById('messageContent').textContent = data.message;
            document.getElementById('modalContactId').value = contactId;
            document.getElementById('messageModal').style.display = 'block';
        });
}

// Close modal when clicking X
document.querySelector('.modal .close').addEventListener('click', function() {
    document.getElementById('messageModal').style.display = 'none';
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('messageModal')) {
        document.getElementById('messageModal').style.display = 'none';
    }
});