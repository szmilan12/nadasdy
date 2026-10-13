document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkboxes = form.querySelectorAll('input[name="majors[]"]:checked');
            
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Kérlek, válassz ki legalább egy szakmát, ami érdekel!');
            }
        });
    }
});