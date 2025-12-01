<?php
 include __DIR__ . '/../php/sellPhone.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Your Phone | Phone Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/sellPhone.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="container main-content ">
        <div class="sell-phone-container">
            <h1 class="text-center mb-4 mt-auto">Sell Your Phone</h1>
            <p class="text-center text-muted mb-5">Get the best price for your used phone in just a few simple steps</p>
            
            <!-- Progress Steps -->
            <div class="progress-container mb-8">
                <div class="progress-step">
                    <div class="progress-line">
                        <div class="progress-line-fill" id="progressLine"></div>
                    </div>
                    <div class="step active" id="step1">1
                        <div class="step-label">Phone Details</div>
                    </div>
                    <div class="step" id="step2">2
                        <div class="step-label">Upload Photos</div>
                    </div>
                    <div class="step" id="step3">3
                        <div class="step-label">Contact Info</div>
                    </div>
                    <div class="step" id="step4">4
                        <div class="step-label">Review </div>
                    </div>
                </div>
            </div>
            <form id="sellPhoneForm" action="/sellPhone" method="POST" enctype="multipart/form-data">
                <!-- Step 1: Phone Details -->
                <div class="form-section mt-10" id="phoneDetailsSection">
                    <h3 class="section-title">Phone Details</h3>
                       <div class="mb-3">
                        <label for="brand_id" class="form-label required-field">Brand</label>
                        <select class="form-select" id="brand_id" name="brand_id" required>
                         <option value="" selected disabled>Select Brand</option>
                          <?php foreach ($brands as $brand): ?>
                         <option value="<?php echo $brand['id']; ?>" data-logo="">
                         <?php echo $brand['name']; ?>
                         </option>
                         <?php endforeach; ?>
                         <option value="other">My brand is not listed</option>
                        </select>

                    </div>
                    
                    <div class="mb-3">
                        <label for="model_id" class="form-label required-field">Phone Model</label>
                        <select class="form-select" id="model_id" name="model_id" disabled>
                            <option value="" selected disabled>Select Brand First</option>
                        </select>
                        
                        <div class="custom-model-input" id="customModelInput" style="display: none;">
                            <label class="custom-model-label">
                                <i class="fas fa-mobile-alt me-2"></i>
                                <span>Enter your custom model name</span>
                            </label>
                            <input type="text" class="form-control custom-model-field" id="custom_model" name="custom_model" placeholder="e.g. iPhone 15 Pro Max, Samsung Galaxy S24">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phoneStorage" class="form-label required-field">Storage Capacity</label>
                            <select class="form-select" id="phoneStorage" name="phoneStorage" required>
                                <option value="" selected disabled>Select Storage</option>
                                <option value="16GB">16GB</option>
                                <option value="32GB">32GB</option>
                                <option value="64GB">64GB</option>
                                <option value="128GB">128GB</option>
                                <option value="256GB">256GB</option>
                                <option value="512GB">512GB</option>
                                <option value="1TB">1TB</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phoneColor" class="form-label required-field">Color</label>
                            <select class="form-select color-dropdown" id="phoneColor" name="phoneColor" required>
                                <option value="" selected disabled>Select Color</option>
                                <option value="Black" data-color="#000000" class="color-option-black">Black</option>
                                <option value="White" data-color="#ffffff" class="color-option-white">White</option>
                                <option value="Silver" data-color="#c0c0c0" class="color-option-silver">Silver</option>
                                <option value="Gold" data-color="#ffd700" class="color-option-gold">Gold</option>
                                <option value="Rose Gold" data-color="#e8b4b8" class="color-option-rosegold">Rose Gold</option>
                                <option value="Red" data-color="#dc3545" class="color-option-red">Red</option>
                                <option value="Blue" data-color="#007bff" class="color-option-blue">Blue</option>
                                <option value="Green" data-color="#28a745" class="color-option-green">Green</option>
                                <option value="Purple" data-color="#6f42c1" class="color-option-purple">Purple</option>
                                <option value="Pink" data-color="#e83e8c" class="color-option-pink">Pink</option>
                                <option value="Orange" data-color="#fd7e14" class="color-option-orange">Orange</option>
                                <option value="Yellow" data-color="#ffc107" class="color-option-yellow">Yellow</option>
                                <option value="Grey" data-color="#6c757d" class="color-option-grey">Grey</option>
                                <option value="Other" data-color="#999999" class="color-option-other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phonePrice" class="form-label required-field">Price ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="phonePrice" name="phonePrice" placeholder="Enter your asking price" min="1" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phoneCondition" class="form-label required-field">Condition: <span id="conditionValue">5</span>/10</label>
                        <input type="range" class="form-range" id="phoneCondition" name="phoneCondition" min="1" max="10" step="1" value="5" required>
                        <div class="condition-labels">
                            <span>Poor</span>
                            <span>Fair</span>
                            <span>Good</span>
                            <span>Excellent</span>
                            <span>Like New</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phoneDetails" class="form-label">Additional Details</label>
                        <textarea class="form-control" id="phoneDetails" name="phoneDetails" rows="4" placeholder="Describe your phone's condition, included accessories, reason for selling, etc."></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" id="nextToPhotos">Next: Upload Photos</button>
                    </div>
                </div>
                
                <!-- Step 2: Upload Photos -->
                <div class="form-section" id="uploadPhotosSection" style="display: none;">
                    <h3 class="section-title">Upload Photos</h3>
                    
                    <div class="mb-4">
                        <p class="text-muted">Upload clear photos of your phone from different angles. Include photos of any damage or wear. Maximum 7 photos.</p>
                        
                        <div class="upload-container" id="uploadContainer">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <h5>Drag & Drop Photos Here</h5>
                            <p class="text-muted">or</p>
                            <button type="button" class="btn btn-outline-primary" id="browseButton">Browse Files</button>
                            <input type="file" id="phoneImages" name="phoneImages[]" multiple accept="image/*">
                        </div>
                        
                        <div id="imagePreview" class="mt-4"></div>
                        
                        <div class="text-muted mt-2">
                            <small><i class="fas fa-info-circle me-1"></i> Tip: Include photos of the front, back, sides, and any accessories.</small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="backToDetails">Back</button>
                        <button type="button" class="btn btn-primary" id="nextToContact">Next: Contact Info</button>
                    </div>
                </div>
                
                <!-- Step 3: Contact Information -->
                <div class="form-section" id="contactInfoSection" style="display: none;">
                    <h3 class="section-title">Contact Information</h3>
                    
                    <div class="mb-3">
                        <label for="sellerName" class="form-label required-field">Your Name</label>
                        <input type="text" class="form-control" id="sellerName" name="sellerName" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sellerEmail" class="form-label required-field">Email Address</label>
                        <input type="email" class="form-control" id="sellerEmail" name="sellerEmail" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sellerPhone" class="form-label required-field">Phone Number</label>
                        <input type="tel" class="form-control" id="sellerPhone" name="sellerPhone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sellerLocation" class="form-label required-field">Location</label>
                        <input type="text" class="form-control" id="sellerLocation" name="sellerLocation" placeholder="City, State" required>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="backToPhotos">Back</button>
                        <button type="button" class="btn btn-primary" id="nextToReview">Next: Review</button>
                    </div>
                </div>
                
                <!-- Step 4: Review & Submit -->
                <div class="form-section" id="reviewSection" style="display: none;">
                    <h3 class="section-title">Review Your Listing</h3>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Phone Details</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Brand:</strong></td>
                                            <td id="reviewBrand"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Model:</strong></td>
                                            <td id="reviewModel"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Storage:</strong></td>
                                            <td id="reviewStorage"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Color:</strong></td>
                                            <td id="reviewColor"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price:</strong></td>
                                            <td id="reviewPrice"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Condition:</strong></td>
                                            <td id="reviewCondition"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Contact Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td id="reviewName"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td id="reviewEmail"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td id="reviewPhone"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Location:</strong></td>
                                            <td id="reviewLocation"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Photos</h5>
                        <div id="reviewPhotos" class="d-flex flex-wrap gap-2"></div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Additional Details</h5>
                        <p id="reviewDetails" class="p-3 bg-light rounded"></p>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="termsCheck" required>
                        <label class="form-check-label" for="termsCheck">
                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> and confirm that all information provided is accurate.
                        </label>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="backToContact">Back</button>
                        <button type="submit" class="btn btn-success" id="submitListing">Submit Listing</button>
                    </div>
                </div>
            </form>
            
            <!-- Success Message (initially hidden) -->
            <div class="alert alert-success text-center p-4" id="successMessage" style="display: none;">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h4>Your listing has been submitted successfully!</h4>
                <p>We'll review your listing and it will be live on our marketplace soon.</p>
                <div class="mt-4">
                    <a href="/" class="btn btn-primary me-2">Go to Homepage</a>
                    <a href="/sellYourPhone" class="btn btn-outline-primary">Sell Another Phone</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">  
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Listing Policies</h6>
                    <p>By submitting a listing, you confirm that:</p>
                    <ul>
                        <li>You are the rightful owner of the device or authorized to sell it</li>
                        <li>The information provided is accurate and complete</li>
                        <li>The photos uploaded are of the actual device being sold</li>
                        <li>You will respond to inquiries from potential buyers in a timely manner</li>
                    </ul>
                    
                    <h6>2. Prohibited Items</h6>
                    <p>The following items are prohibited:</p>
                    <ul>
                        <li>Stolen devices</li>
                        <li>Counterfeit or replica devices</li>
                        <li>Devices with illegal modifications</li>
                        <li>Devices with iCloud/Google account locks</li>
                    </ul>
                    
                    <h6>3. Fees and Payments</h6>
                    <p>Our platform charges a 5% fee on successful sales. Payment processing is handled securely through our payment partners.</p>
                    
                    <h6>4. Privacy Policy</h6>
                    <p>Your contact information will be shared with potential buyers. We do not sell your personal data to third parties.</p>
                    
                    <h6>5. Listing Removal</h6>
                    <p>We reserve the right to remove listings that violate our policies or receive multiple complaints from users.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="../js/sellPhone.js"></script>
</body>
</html>

