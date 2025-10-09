document.addEventListener('DOMContentLoaded', function() {
    // Variables
    let uploadedFiles = [];
    const maxFiles = 7;

    // Elements
    const brandSelect = document.getElementById('brand_id');
    const modelSelect = document.getElementById('model_id');
    const customModelCheck = document.getElementById('customModelCheck');
    const customModelInput = document.getElementById('customModelInput');
    const conditionSlider = document.getElementById('phoneCondition');
    const conditionValue = document.getElementById('conditionValue');
    const uploadContainer = document.getElementById('uploadContainer');
    const phoneImagesInput = document.getElementById('phoneImages');
    const browseButton = document.getElementById('browseButton');
    const imagePreview = document.getElementById('imagePreview');
    const CheckboxDiv = document.getElementById('customModel');

    // Navigation buttons
    const nextToPhotosBtn = document.getElementById('nextToPhotos');
    const backToDetailsBtn = document.getElementById('backToDetails');
    const nextToContactBtn = document.getElementById('nextToContact');
    const backToPhotosBtn = document.getElementById('backToPhotos');
    const nextToReviewBtn = document.getElementById('nextToReview');
    const backToContactBtn = document.getElementById('backToContact');
    const submitListingBtn = document.getElementById('submitListing');

    // Sections
    const phoneDetailsSection = document.getElementById('phoneDetailsSection');
    const uploadPhotosSection = document.getElementById('uploadPhotosSection');
    const contactInfoSection = document.getElementById('contactInfoSection');
    const reviewSection = document.getElementById('reviewSection');
    const successMessage = document.getElementById('successMessage');

    // Progress steps
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const step4 = document.getElementById('step4');
    const progressLine = document.getElementById('progressLine');

    // Update condition value display
    conditionSlider.addEventListener('input', function() {
        conditionValue.textContent = this.value;
    });

    // Brand selection changes
    brandSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            modelSelect.disabled = true;
            customModelCheck.checked = true;
            customModelInput.style.display = 'block';
            document.getElementById('custom_model').required = true;
            modelSelect.required = false;
        } else {
            // Fetch models for the selected brand
            fetchModels(this.value);
            modelSelect.disabled = false;
            customModelInput.style.display = 'none';
            customModelCheck.checked = false;

            if (!customModelCheck.checked) {
                modelSelect.required = true;
                document.getElementById('custom_model').required = false;
            }
        }
    });

    // Custom model checkbox
    customModelCheck.addEventListener('change', function() {
        if (this.checked) {
            customModelInput.style.display = 'block';
            document.getElementById('custom_model').required = true;
            modelSelect.required = false;
        } else {
            customModelInput.style.display = 'none';
            document.getElementById('custom_model').required = false;
            if (brandSelect.value !== 'other') {
                modelSelect.required = true;
            }
        }
    });

    // Fetch models for a brand
    function fetchModels(brandId) {
        modelSelect.innerHTML = '<option value="" selected disabled>Loading models...</option>';

        fetch(`/api/models?brand_id=${brandId}`)
            .then(response => response.json())
            .then(data => {
                modelSelect.innerHTML = '<option value="" selected disabled>Select Model</option>';

                data.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.id;
                    option.textContent = model.model_name;
                    modelSelect.appendChild(option);
                });

                if (data.length === 0) {
                    modelSelect.innerHTML = '<option value="" selected disabled>No models found</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching models:', error);
                modelSelect.innerHTML = '<option value="" selected disabled>Error loading models</option>';
            });
    }

    modelSelect.addEventListener("change", function() {
        if (modelSelect.value) {
            customModel.style.display = "none";
        } else {
            // Show checkbox again if no brand selected
            customModel.style.display = "block";
        }

    });

    // Image upload handling
    browseButton.addEventListener('click', function() {
        phoneImagesInput.click();
    });

    // Drag and drop functionality
    uploadContainer.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-primary');
    });

    uploadContainer.addEventListener('dragleave', function() {
        this.classList.remove('border-primary');
    });

    uploadContainer.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary');

        if (e.dataTransfer.files.length > 0) {
            handleFiles(e.dataTransfer.files);
        }
    });

    phoneImagesInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        const newFiles = Array.from(files);

        // Check if adding these files would exceed the limit
        if (uploadedFiles.length + newFiles.length > maxFiles) {
            alert(`You can upload a maximum of ${maxFiles} images. You've selected ${uploadedFiles.length + newFiles.length} images.`);
            return;
        }

        newFiles.forEach(file => {
            // Check if file is an image
            if (!file.type.match('image.*')) {
                alert('Please upload only image files.');
                return;
            }

            // Check if file is already in the list
            const isDuplicate = uploadedFiles.some(f =>
                f.name === file.name &&
                f.size === file.size &&
                f.lastModified === file.lastModified
            );

            if (!isDuplicate) {
                uploadedFiles.push(file);
                displayImage(file);
            }
        });

        updateFileInput();
    }

    function displayImage(file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'image-preview-item';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = file.name;

            const removeBtn = document.createElement('span');
            removeBtn.className = 'remove-image';
            removeBtn.innerHTML = '&times;';
            removeBtn.addEventListener('click', function() {
                uploadedFiles = uploadedFiles.filter(f => f !== file);
                previewItem.remove();
                updateFileInput();
            });

            previewItem.appendChild(img);
            previewItem.appendChild(removeBtn);
            imagePreview.appendChild(previewItem);
        };

        reader.readAsDataURL(file);
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        uploadedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        phoneImagesInput.files = dataTransfer.files;
    }

    // Navigation between steps
    nextToPhotosBtn.addEventListener('click', function() {
        if (validatePhoneDetails()) {
            phoneDetailsSection.style.display = 'none';
            uploadPhotosSection.style.display = 'block';
            updateProgress(2);
        }
    });

    backToDetailsBtn.addEventListener('click', function() {
        uploadPhotosSection.style.display = 'none';
        phoneDetailsSection.style.display = 'block';
        updateProgress(1);
    });

    nextToContactBtn.addEventListener('click', function() {
        if (validatePhotos()) {
            uploadPhotosSection.style.display = 'none';
            contactInfoSection.style.display = 'block';
            updateProgress(3);
        }
    });

    backToPhotosBtn.addEventListener('click', function() {
        contactInfoSection.style.display = 'none';
        uploadPhotosSection.style.display = 'block';
        updateProgress(2);
    });

    nextToReviewBtn.addEventListener('click', function() {
        if (validateContactInfo()) {
            contactInfoSection.style.display = 'none';
            populateReviewSection();
            reviewSection.style.display = 'block';
            updateProgress(4);
        }
    });

    backToContactBtn.addEventListener('click', function() {
        reviewSection.style.display = 'none';
        contactInfoSection.style.display = 'block';
        updateProgress(3);
    });
    // Validation functions
    function validatePhoneDetails() {
        const form = document.getElementById('sellPhoneForm');

        // Check brand
        if (!brandSelect.value) {
            alert('Please select a brand.');
            brandSelect.focus();
            return false;
        }

        // Check model or custom model
        if (customModelCheck.checked) {
            if (!document.getElementById('custom_model').value.trim()) {
                alert('Please enter your phone model.');
                document.getElementById('custom_model').focus();
                return false;
            }
        } else if (brandSelect.value !== 'other' && !modelSelect.value) {
            alert('Please select a phone model.');
            modelSelect.focus();
            return false;
        }

        // Check storage
        if (!document.getElementById('phoneStorage').value) {
            alert('Please select storage capacity.');
            document.getElementById('phoneStorage').focus();
            return false;
        }

        // Check color
        if (!document.getElementById('phoneColor').value.trim()) {
            alert('Please enter the phone color.');
            document.getElementById('phoneColor').focus();
            return false;
        }

        // Check price
        if (!document.getElementById('phonePrice').value || document.getElementById('phonePrice').value <= 0) {
            alert('Please enter a valid price.');
            document.getElementById('phonePrice').focus();
            return false;
        }

        return true;
    }

    function validatePhotos() {
        if (uploadedFiles.length === 0) {
            alert('Please upload at least one photo of your phone.');
            return false;
        }

        return true;
    }

    function validateContactInfo() {
        // Check name
        if (!document.getElementById('sellerName').value.trim()) {
            alert('Please enter your name.');
            document.getElementById('sellerName').focus();
            return false;
        }

        // Check email
        const email = document.getElementById('sellerEmail').value.trim();
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
            alert('Please enter a valid email address.');
            document.getElementById('sellerEmail').focus();
            return false;
        }

        // Check phone
        if (!document.getElementById('sellerPhone').value.trim()) {
            alert('Please enter your phone number.');
            document.getElementById('sellerPhone').focus();
            return false;
        }

        // Check location
        if (!document.getElementById('sellerLocation').value.trim()) {
            alert('Please enter your location.');
            document.getElementById('sellerLocation').focus();
            return false;
        }

        return true;
    }

    // Update progress indicator
    function updateProgress(step) {
        // Reset all steps
        [step1, step2, step3, step4].forEach(s => {
            s.classList.remove('active', 'completed');
        });

        // Set active step
        if (step >= 1) step1.classList.add(step > 1 ? 'completed' : 'active');
        if (step >= 2) step2.classList.add(step > 2 ? 'completed' : 'active');
        if (step >= 3) step3.classList.add(step > 3 ? 'completed' : 'active');
        if (step >= 4) step4.classList.add('active');

        // Update progress line
        const progressPercentage = (step - 1) * 33.33;
        progressLine.style.width = `${progressPercentage}%`;
    }

    // Populate review section
    function populateReviewSection() {
        // Get brand name
        const brandText = brandSelect.options[brandSelect.selectedIndex].text;
        document.getElementById('reviewBrand').textContent = brandText;

        // Get model name
        let modelText = '';
        if (customModelCheck.checked) {
            modelText = document.getElementById('custom_model').value;
        } else if (modelSelect.selectedIndex > 0) {
            modelText = modelSelect.options[modelSelect.selectedIndex].text;
        }
        document.getElementById('reviewModel').textContent = modelText;

        // Other details
        document.getElementById('reviewStorage').textContent = document.getElementById('phoneStorage').value;
        document.getElementById('reviewColor').textContent = document.getElementById('phoneColor').value;
        document.getElementById('reviewPrice').textContent = '$' + parseFloat(document.getElementById('phonePrice').value).toFixed(2);

        const conditionVal = document.getElementById('phoneCondition').value;
        let conditionText = '';
        if (conditionVal >= 9) conditionText = 'Like New (10/10)';
        else if (conditionVal >= 7) conditionText = 'Excellent (' + conditionVal + '/10)';
        else if (conditionVal >= 5) conditionText = 'Good (' + conditionVal + '/10)';
        else if (conditionVal >= 3) conditionText = 'Fair (' + conditionVal + '/10)';
        else conditionText = 'Poor (' + conditionVal + '/10)';

        document.getElementById('reviewCondition').textContent = conditionText;

        // Contact info
        document.getElementById('reviewName').textContent = document.getElementById('sellerName').value;
        document.getElementById('reviewEmail').textContent = document.getElementById('sellerEmail').value;
        document.getElementById('reviewPhone').textContent = document.getElementById('sellerPhone').value;
        document.getElementById('reviewLocation').textContent = document.getElementById('sellerLocation').value;

        // Details
        const details = document.getElementById('phoneDetails').value.trim();
        document.getElementById('reviewDetails').textContent = details || 'No additional details provided.';

        // Photos
        const reviewPhotos = document.getElementById('reviewPhotos');
        reviewPhotos.innerHTML = '';

        if (uploadedFiles.length > 0) {
            uploadedFiles.forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Phone photo';
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '4px';
                    reviewPhotos.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        } else {
            reviewPhotos.innerHTML = '<p class="text-muted">No photos uploaded.</p>';
        }
    }

    // Form submission
    document.getElementById('sellPhoneForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!document.getElementById('termsCheck').checked) {
            alert('Please agree to the Terms and Conditions to proceed.');
            return;
        }


        const formData = new FormData(this);

        // Show loading state
        submitListingBtn.disabled = true;
        submitListingBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...';

        fetch('/sellPhone', {
                method: 'POST',
                body: formData

            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    reviewSection.style.display = 'none';
                    successMessage.style.display = 'block';
                } else {
                    alert('Error: ' + data.message);
                    submitListingBtn.disabled = false;
                    submitListingBtn.innerHTML = 'Submit Listing';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting your listing. Please try again.');
                submitListingBtn.disabled = false;
                submitListingBtn.innerHTML = 'Submit Listing';
            });
    });

});