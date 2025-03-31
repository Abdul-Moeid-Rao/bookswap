// Mobile menu toggle - Improved Version
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    const nav = document.querySelector('nav');
    
    // Create mobile menu toggle button
    const mobileMenuToggle = document.createElement('button');
    mobileMenuToggle.className = 'mobile-menu-toggle';
    mobileMenuToggle.setAttribute('aria-label', 'Toggle navigation menu');
    mobileMenuToggle.innerHTML = '<span class="toggle-bar"></span><span class="toggle-bar"></span><span class="toggle-bar"></span>';
    header.appendChild(mobileMenuToggle);
    
    // Toggle menu function
    function toggleMenu() {
        const isOpen = nav.classList.contains('active');
        
        // Toggle menu state
        nav.classList.toggle('active');
        mobileMenuToggle.classList.toggle('active');
        document.body.classList.toggle('menu-open', !isOpen);
        
        // Accessibility
        mobileMenuToggle.setAttribute('aria-expanded', !isOpen);
    }
    
    // Event listeners
    mobileMenuToggle.addEventListener('click', toggleMenu);
    
    // Close menu when clicking on links (for mobile)
    document.querySelectorAll('nav ul li a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) { // Match your mobile breakpoint
                toggleMenu();
            }
        });
    });
    
    // Responsive behavior
    function handleResize() {
        if (window.innerWidth > 768) { // Desktop
            nav.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
            document.body.classList.remove('menu-open');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
        }
    }
    
    window.addEventListener('resize', handleResize);
    
    // Initialize
    handleResize();

    // Image preview for file uploads
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const previewId = e.target.id + '-preview';
            let preview = document.getElementById(previewId);
            
            if (!preview) {
                preview = document.createElement('div');
                preview.id = previewId;
                preview.className = 'image-preview';
                e.target.parentNode.appendChild(preview);
            }
            
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.innerHTML = '<img src="' + event.target.result + '" alt="Preview">';
            };
            reader.readAsDataURL(file);
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
    
    // Geolocation for profile page
    if (document.getElementById('latitude') && document.getElementById('longitude')) {
        if (!navigator.geolocation) {
            console.log("Geolocation is not supported by this browser.");
        }
    }
});