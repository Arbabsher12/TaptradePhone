document.addEventListener('DOMContentLoaded', function() {
    // Fetch latest 5 phones from the database
    fetchPhones();

    // Search functionality - redirect to all phones page
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchInput');

    if (searchButton && searchInput) {
        searchButton.addEventListener('click', function() {
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                // Redirect to all phones page with search term
                window.location.href = `allPhones?search=${encodeURIComponent(searchTerm)}`;
            } else {
                // Just go to all phones page
                window.location.href = 'allPhones';
            }
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    // Redirect to all phones page with search term
                    window.location.href = `allPhones?search=${encodeURIComponent(searchTerm)}`;
                } else {
                    // Just go to all phones page
                    window.location.href = 'allPhones';
                }
            }
        });
    }
});

function fetchPhones() {
    // Show loading state
    const phoneContainer = document.getElementById('phone-container');
    phoneContainer.innerHTML = `
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading latest phones...</p>
        </div>
    `;

    // Fetch latest 5 phones from the server
    fetch('../php/fetchDataPhone.php?limit=5&sort=newest')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            // Handle both old and new format
            const phones = response.phones || response;
            displayPhones(phones);
        })
        .catch(error => {
            console.error('Error fetching phones:', error);
            phoneContainer.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error loading phones. Please try again later.
                    </div>
                </div>
            `;
        });
}

function displayPhones(phones) {
    const phoneContainer = document.getElementById('phone-container');

    // Clear previous content
    phoneContainer.innerHTML = '';

    if (phones.length === 0) {
        phoneContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    No phones found matching your criteria.
                </div>
            </div>
        `;
        return;
    }

    // Display each phone
    phones.forEach(phone => {
                // Get condition text based on condition value
                let conditionText = 'Unknown';
                let conditionClass = 'bg-secondary';

                if (phone.condition) {
                    const condition = parseInt(phone.condition);
                    if (condition >= 9) {
                        conditionText = 'Like New';
                        conditionClass = 'bg-success';
                    } else if (condition >= 7) {
                        conditionText = 'Excellent';
                        conditionClass = 'bg-info';
                    } else if (condition >= 5) {
                        conditionText = 'Good';
                        conditionClass = 'bg-primary';
                    } else if (condition >= 3) {
                        conditionText = 'Fair';
                        conditionClass = 'bg-warning';
                    } else {
                        conditionText = 'Poor';
                        conditionClass = 'bg-danger';
                    }
                }


                // Handle different image path formats
                let imagePath = '../Components/noDp.png'; // Default
                if (phone.image && phone.image.trim() !== '' && phone.image !== '/uploads/none.jpg' && phone.image !== 'uploads/none.jpg') {
                    if (phone.image.startsWith('../uploads/')) {
                        imagePath = phone.image;
                    } else if (phone.image.startsWith('uploads/')) {
                        imagePath = '../' + phone.image;
                    } else if (phone.image.startsWith('/uploads/')) {
                        imagePath = '..' + phone.image;
                    } else {
                        imagePath = '../uploads/' + phone.image;
                    }
                }



                // Create phone card
                const phoneCard = document.createElement('div');
                phoneCard.className = 'col-sm-6 col-md-4 col-lg-3 mb-4';
                phoneCard.innerHTML = `
            <div class="phone-card">
                <div class="phone-image-container">
                    <img src="${imagePath}" alt="${phone.name}" class="phone-image" onerror="this.onerror=null;this.src='../Components/noDp.png';">
                    ${phone.condition ? `<span class="phone-condition ${conditionClass}">${conditionText}</span>` : ''}
                </div>
                <div class="phone-details">
                    <h3 class="phone-name">${phone.name}</h3>
                    <div class="phone-price">$${parseFloat(phone.price).toFixed(2)}</div>
                    <div class="phone-meta">
                        <span><i class="far fa-clock me-1"></i>${formatDate(phone.created_at || new Date())}</span>
                        <span><i class="far fa-eye me-1"></i>${phone.views || 0} views</span>
                    </div>
                </div>
            </div>
        `;
        
        // Add click event to redirect to phone detail page
        phoneCard.addEventListener('click', function() {
            window.location.href = `/phoneDetail?id=${phone.id}`;
        });
        
        phoneContainer.appendChild(phoneCard);
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) {
        return 'Today';
    } else if (diffDays === 1) {
        return 'Yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else if (diffDays < 30) {
        const weeks = Math.floor(diffDays / 7);
        return `${weeks} ${weeks === 1 ? 'week' : 'weeks'} ago`;
    } else {
        return date.toLocaleDateString();
    }
}