// This script needs to be at the end of the form to ensure elements exist
document.addEventListener('DOMContentLoaded', function() {
    // Initialize brand/model dependent dropdown
    const brandSelect = document.getElementById('brand_id');
    //const currentModelId = <?= $phone['model_id'] ?: 'null' ?>;
    
    if (brandSelect) {
        brandSelect.addEventListener('change', function() {
            loadModels(this.value);
        });
    }
    
    // Initialize condition range slider
    const conditionSlider = document.getElementById('phone_condition');
    const conditionValue = document.getElementById('condition_value');
    
    if (conditionSlider && conditionValue) {
        conditionSlider.addEventListener('input', function() {
            conditionValue.textContent = this.value;
        });
    }
});

// Array to store removed existing images
let removedImages = [];

// Function to remove existing image
function removeExistingImage(button) {
    const imagePreview = button.parentElement;
    const imagePath = imagePreview.getAttribute('data-path');
    
    // Add to removed images array
    removedImages.push(imagePath);
    document.getElementById('removed_images').value = JSON.stringify(removedImages);
    
    // Remove from DOM
    imagePreview.remove();
}

// Function to preview new images
function previewNewImages(input) {
    const previewContainer = document.getElementById('new-images-preview');
    previewContainer.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'image-preview';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="New Image ${i+1}">
                    <button type="button" class="remove-image" data-index="${i}">×</button>
                `;
                previewContainer.appendChild(preview);
                
                // Add event listener to remove button
                preview.querySelector('.remove-image').addEventListener('click', function() {
                    preview.remove();
                    
                    // Create a new FileList without the removed file
                    if (this.hasAttribute('data-index')) {
                        const index = parseInt(this.getAttribute('data-index'));
                        const dt = new DataTransfer();
                        
                        for (let j = 0; j < input.files.length; j++) {
                            if (j !== index) {
                                dt.items.add(input.files[j]);
                            }
                        }
                        
                        input.files = dt.files;
                    }
                });
            }
            
            reader.readAsDataURL(file);
        }
    }
}