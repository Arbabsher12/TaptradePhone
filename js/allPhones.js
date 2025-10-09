document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let currentPage = 1;
    let currentFilters = {
        search: '',
        sort: 'newest',
        price_range: [],
        brands: [],
        conditions: [],
        storage: []
    };
    let isLoading = false;
    let hasMorePhones = true;

    // Initialize page
    initializePage();

    function initializePage() {
        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        currentFilters.search = urlParams.get('search') || '';
        currentFilters.sort = urlParams.get('sort') || 'newest';

        // Parse array parameters
        if (urlParams.get('price_range')) {
            currentFilters.price_range = urlParams.get('price_range').split(',');
        }
        if (urlParams.get('brands')) {
            currentFilters.brands = urlParams.get('brands').split(',');
        }
        if (urlParams.get('conditions')) {
            currentFilters.conditions = urlParams.get('conditions').split(',');
        }
        if (urlParams.get('storage')) {
            currentFilters.storage = urlParams.get('storage').split(',');
        }

        // Set initial UI state
        updateSortText();
        updateActiveFilters();
        loadPhones();

        // Set up event listeners
        setupEventListeners();
    }

    function setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const searchTimeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            setTimeout(() => {
                currentFilters.search = this.value;
                resetAndLoadPhones();
            }, 500);
        });

        // Filter button
        document.getElementById('filterButton').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('filterModal'));
            modal.show();
        });

        // Sort dropdown
        document.querySelectorAll('[data-sort]').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                currentFilters.sort = this.dataset.sort;
                updateSortText();
                resetAndLoadPhones();
            });
        });

        // Filter modal buttons
        document.getElementById('applyFilters').addEventListener('click', applyFilters);
        document.getElementById('clearFilters').addEventListener('click', clearFilters);
        document.getElementById('clearAllFilters').addEventListener('click', clearAllFilters);

        // Load more button
        document.getElementById('loadMoreBtn').addEventListener('click', loadMorePhones);

        // View mode toggle
        document.querySelectorAll('input[name="viewMode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                toggleViewMode(this.id);
            });
        });
    }

    function loadPhones(reset = false) {
        if (isLoading) return;

        isLoading = true;

        if (reset) {
            currentPage = 1;
            hasMorePhones = true;
        }

        // Build query parameters
        const params = new URLSearchParams();
        params.append('search', currentFilters.search);
        params.append('sort', currentFilters.sort);
        params.append('page', currentPage);
        params.append('limit', 12);

        if (currentFilters.price_range.length > 0) {
            params.append('price_range', currentFilters.price_range.join(','));
        }
        if (currentFilters.brands.length > 0) {
            params.append('brands', currentFilters.brands.join(','));
        }
        if (currentFilters.conditions.length > 0) {
            params.append('conditions', currentFilters.conditions.join(','));
        }
        if (currentFilters.storage.length > 0) {
            params.append('storage', currentFilters.storage.join(','));
        }

        // Show loading state
        if (reset) {
            showLoadingState();
        }

        // Fetch phones
        fetch(`../php/fetchDataPhone.php?${params.toString()}`)
            .then(response => response.json())
            .then(response => {
                console.log('Raw response:', response);

                // Handle both old and new format
                const data = response.phones || response;
                const hasMore = response.hasMore !== undefined ? response.hasMore : (data.length >= 12);

                console.log(`Loaded ${data.length} phones for page ${currentPage}, hasMore: ${hasMore}`);

                if (reset) {
                    // Clear any existing content first
                    const container = document.getElementById('phone-container');
                    container.innerHTML = '';

                    displayPhones(data);
                    console.log('Displayed phones, clearing loading state');
                } else {
                    appendPhones(data);
                }

                // Update hasMorePhones based on server response
                hasMorePhones = hasMore;

                updateResultsCount(data.length);
                updateLoadMoreButton(data.length);

                // Update URL without page reload
                updateURL();

                // Ensure loading state is cleared
                clearLoadingState();
            })
            .catch(error => {
                console.error('Error loading phones:', error);
                showErrorState();
            })
            .finally(() => {
                isLoading = false;
                console.log(`Finished loading. hasMorePhones: ${hasMorePhones}`);
            });
    }

    function resetAndLoadPhones() {
        loadPhones(true);
    }

    function loadMorePhones() {
        currentPage++;
        loadPhones(false);
    }

    function displayPhones(phones) {
        const container = document.getElementById('phone-container');

        console.log('displayPhones called with', phones.length, 'phones');
        console.log('Container before:', container.innerHTML.substring(0, 100));

        if (phones.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No phones found</h4>
                    <p class="text-muted">Try adjusting your search criteria or filters</p>
                </div>
            `;
            return;
        }

        const phoneCards = phones.map(phone => createPhoneCard(phone)).join('');
        console.log('Generated phone cards:', phoneCards.substring(0, 200));

        container.innerHTML = phoneCards;

        console.log('Container after:', container.innerHTML.substring(0, 100));
    }

    function appendPhones(phones) {
        const container = document.getElementById('phone-container');

        if (phones.length === 0) {
            hasMorePhones = false;
            // Hide load more button when no more phones
            document.getElementById('loadMoreContainer').style.display = 'none';
            return;
        }

        const newCards = phones.map(phone => createPhoneCard(phone)).join('');
        container.insertAdjacentHTML('beforeend', newCards);
    }

    function createPhoneCard(phone) {
        console.log('Creating card for phone:', phone.name, 'Image:', phone.image);

        const conditionText = getConditionText(phone.condition);
        const formattedPrice = formatPrice(phone.price);

        // Handle different image path formats
        let imagePath = '../uploads/none.jpg'; // Default for no image

        if (phone.image && phone.image.trim() !== '' &&
            phone.image !== '/uploads/none.jpg' &&
            phone.image !== 'uploads/none.jpg' &&
            phone.image !== 'none.jpg') {

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

        console.log('Final image path:', imagePath);

        return `
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card phone-card h-100">
                    <div class="card-img-container">
                        <img src="${imagePath}" class="card-img-top" alt="${phone.name}" onerror="this.src='../uploads/none.jpg'">
                        <div class="card-overlay">
                            <a href="phoneDetail?id=${phone.id}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${phone.name}</h5>
                        <p class="card-text text-muted small">${phone.details ? phone.details.substring(0, 100) + '...' : 'No description available'}</p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-secondary">${conditionText}</span>
                                <small class="text-muted">${phone.views} views</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="text-primary mb-0">${formattedPrice}</h6>
                                <small class="text-muted">${formatDate(phone.created_at)}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function applyFilters() {
        // Get selected filters
        currentFilters.price_range = [];
        currentFilters.brands = [];
        currentFilters.conditions = [];
        currentFilters.storage = [];

        // Price range filters
        document.querySelectorAll('#filterModal input[type="checkbox"]:checked').forEach(checkbox => {
            if (checkbox.id.startsWith('priceRange')) {
                currentFilters.price_range.push(checkbox.value);
            } else if (checkbox.id.startsWith('brand')) {
                currentFilters.brands.push(checkbox.value);
            } else if (checkbox.id.startsWith('condition')) {
                currentFilters.conditions.push(checkbox.value);
            } else if (checkbox.id.startsWith('storage')) {
                currentFilters.storage.push(checkbox.value);
            }
        });

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
        modal.hide();

        // Update UI and reload
        updateActiveFilters();
        resetAndLoadPhones();
    }

    function clearFilters() {
        // Clear all checkboxes in modal
        document.querySelectorAll('#filterModal input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    function clearAllFilters() {
        // Clear all filters
        currentFilters.search = '';
        currentFilters.price_range = [];
        currentFilters.brands = [];
        currentFilters.conditions = [];
        currentFilters.storage = [];
        currentFilters.sort = 'newest';

        // Clear UI
        document.getElementById('searchInput').value = '';
        clearFilters();
        updateSortText();
        updateActiveFilters();

        // Reload phones
        resetAndLoadPhones();
    }

    function updateSortText() {
        const sortText = document.getElementById('sortText');
        const sortMap = {
            'newest': 'Newest',
            'oldest': 'Oldest',
            'price_low': 'Price: Low to High',
            'price_high': 'Price: High to Low',
            'popularity': 'Popularity'
        };
        sortText.textContent = sortMap[currentFilters.sort] || 'Newest';
    }

    function updateActiveFilters() {
        const activeFiltersDiv = document.getElementById('activeFilters');
        const filterTagsDiv = document.getElementById('filterTags');

        const activeFilters = [];

        if (currentFilters.search) {
            activeFilters.push(`<span class="badge bg-info">Search: "${currentFilters.search}"</span>`);
        }

        if (currentFilters.price_range.length > 0) {
            activeFilters.push(`<span class="badge bg-success">Price: ${currentFilters.price_range.join(', ')}</span>`);
        }

        if (currentFilters.brands.length > 0) {
            activeFilters.push(`<span class="badge bg-warning">Brands: ${currentFilters.brands.join(', ')}</span>`);
        }

        if (currentFilters.conditions.length > 0) {
            activeFilters.push(`<span class="badge bg-secondary">Conditions: ${currentFilters.conditions.join(', ')}</span>`);
        }

        if (currentFilters.storage.length > 0) {
            activeFilters.push(`<span class="badge bg-dark">Storage: ${currentFilters.storage.join(', ')}</span>`);
        }

        if (activeFilters.length > 0) {
            filterTagsDiv.innerHTML = activeFilters.join(' ');
            activeFiltersDiv.style.display = 'block';
        } else {
            activeFiltersDiv.style.display = 'none';
        }
    }

    function updateResultsCount(count) {
        const resultsCount = document.getElementById('resultsCount');
        const resultsTitle = document.getElementById('resultsTitle');

        if (currentPage === 1) {
            resultsCount.textContent = `${count} phones found`;
            if (count === 0) {
                resultsTitle.textContent = 'No Results';
            } else {
                resultsTitle.textContent = 'Search Results';
            }
        } else {
            resultsCount.textContent = `Showing ${count} more phones`;
        }
    }

    function updateLoadMoreButton(count) {
        const loadMoreContainer = document.getElementById('loadMoreContainer');

        // Hide load more button if no more phones available
        if (!hasMorePhones) {
            loadMoreContainer.style.display = 'none';
            return;
        }

        // Show load more button if we still have more phones to load
        loadMoreContainer.style.display = 'block';
    }

    function updateURL() {
        const params = new URLSearchParams();

        if (currentFilters.search) params.append('search', currentFilters.search);
        if (currentFilters.sort !== 'newest') params.append('sort', currentFilters.sort);
        if (currentFilters.price_range.length > 0) params.append('price_range', currentFilters.price_range.join(','));
        if (currentFilters.brands.length > 0) params.append('brands', currentFilters.brands.join(','));
        if (currentFilters.conditions.length > 0) params.append('conditions', currentFilters.conditions.join(','));
        if (currentFilters.storage.length > 0) params.append('storage', currentFilters.storage.join(','));

        const newURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.replaceState({}, '', newURL);
    }

    function showLoadingState() {
        const container = document.getElementById('phone-container');
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading phones...</p>
            </div>
        `;
    }

    function clearLoadingState() {
        // Remove any remaining loading spinners
        const loadingSpinners = document.querySelectorAll('.spinner-border');
        loadingSpinners.forEach(spinner => {
            const container = spinner.closest('.col-12');
            if (container && container.textContent.includes('Loading phones')) {
                container.remove();
            }
        });

        // Also check for any loading text
        const loadingTexts = document.querySelectorAll('p');
        loadingTexts.forEach(p => {
            if (p.textContent.includes('Loading phones')) {
                p.remove();
            }
        });
    }

    function showErrorState() {
        const container = document.getElementById('phone-container');
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h4 class="text-danger">Error Loading Phones</h4>
                <p class="text-muted">Please try again later</p>
                <button class="btn btn-primary" onclick="location.reload()">Retry</button>
            </div>
        `;
    }

    function toggleViewMode(mode) {
        const container = document.getElementById('phone-container');
        const cards = container.querySelectorAll('.col-lg-3');

        cards.forEach(card => {
            if (mode === 'listView') {
                card.className = 'col-12 mb-3';
            } else {
                card.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';
            }
        });
    }

    // Utility functions
    function getConditionText(condition) {
        const conditions = {
            10: 'New',
            9: 'Like New',
            8: 'Excellent',
            7: 'Excellent',
            6: 'Good',
            5: 'Good',
            4: 'Fair',
            3: 'Fair',
            2: 'Poor',
            1: 'Poor'
        };
        return conditions[condition] || 'Unknown';
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(price);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }
});