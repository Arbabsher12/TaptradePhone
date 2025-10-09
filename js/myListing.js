document.addEventListener("DOMContentLoaded", () => {
    // Get modal elements
    const editModal = document.getElementById("editPhoneModal")
    const deleteModal = document.getElementById("deleteModal")
    const closeButtons = document.querySelectorAll(".close")
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn")

    // Close modal when clicking on X or cancel button
    closeButtons.forEach((button) => {
        button.addEventListener("click", () => {
            editModal.style.display = "none"
            deleteModal.style.display = "none"
        })
    })

    cancelDeleteBtn.addEventListener("click", () => {
        deleteModal.style.display = "none"
    })

    // Close modal when clicking outside of it
    window.addEventListener("click", (event) => {
        if (event.target === editModal) {
            editModal.style.display = "none"
        }
        if (event.target === deleteModal) {
            deleteModal.style.display = "none"
        }
    })
})

// Function to open the edit modal and load phone data
function openEditModal(phoneId) {
    const modal = document.getElementById("editPhoneModal")
    const formContainer = document.getElementById("editPhoneContainer")

    // Show loading state
    formContainer.innerHTML = '<div class="loading">Loading...</div>'
    modal.style.display = "block"

    // Fetch phone data and form
    fetch(`/updatePhoneForm?id=${phoneId}`)
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok")
            }
            return response.text()
        })
        .then((html) => {
            formContainer.innerHTML = html

            // Wait for DOM to update before accessing elements
            setTimeout(() => {
                    // Initialize form event listeners
                    const form = formContainer.querySelector("form")
                    if (form) {
                        form.addEventListener("submit", (e) => {
                            e.preventDefault()
                            updatePhone(form)
                        })
                    } else {
                        console.error("Form element not found in the loaded content")
                    }

                    // Initialize brand/model dependent dropdown
                    const brandSelect = document.getElementById("brand_id")
                    if (brandSelect) {
                        brandSelect.addEventListener("change", function() {
                            loadModels(this.value)
                        })
                    }

                    // Initialize condition range slider
                    const conditionSlider = document.getElementById("phone_condition")
                    const conditionValue = document.getElementById("condition_value")
                    if (conditionSlider && conditionValue) {
                        conditionSlider.addEventListener("input", function() {
                            conditionValue.textContent = this.value
                        })
                    }
                }, 100) // Small delay to ensure DOM is updated
        })
        .catch((error) => {
            formContainer.innerHTML = `<p class="error">Error loading form: ${error.message}</p>`
            console.error("Error loading form:", error)
        })
}

// Function to update phone data
function updatePhone(form) {
    const formData = new FormData(form)
    const submitButton = form.querySelector('button[type="submit"]')
    const statusDiv = document.createElement("div")
    statusDiv.className = "form-status"

    // Disable button and show loading
    submitButton.disabled = true
    submitButton.textContent = "Updating..."

    // Remove any existing status message
    const existingStatus = form.querySelector(".form-status")
    if (existingStatus) {
        existingStatus.remove()
    }

    fetch("/updatePhone", {
            method: "POST",
            body: formData,
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                statusDiv.className = "form-status success"
                statusDiv.textContent = data.message

                // Reload the page after a short delay
                setTimeout(() => {
                    window.location.reload()
                }, 1500)
            } else {
                statusDiv.className = "form-status error"
                statusDiv.textContent = data.message || "An error occurred"
                submitButton.disabled = false
                submitButton.textContent = "Update Phone"
            }

            form.appendChild(statusDiv)
        })
        .catch((error) => {
            statusDiv.className = "form-status error"
            statusDiv.textContent = "Network error: " + error.message
            form.appendChild(statusDiv)

            submitButton.disabled = false
            submitButton.textContent = "Update Phone"
        })
}

// Function to load models based on selected brand
function loadModels(brandId, selectedModelId = null) {
    const modelSelect = document.getElementById("model_id")

    if (!modelSelect) return

    // Clear current options except the first one
    while (modelSelect.options.length > 1) {
        modelSelect.remove(1)
    }

    if (!brandId) {
        modelSelect.disabled = true
        return
    }

    modelSelect.disabled = true
    const firstOption = modelSelect.options[0]
    firstOption.text = "Loading models..."

    fetch(`/api/models?brand_id=${brandId}`)
        .then((response) => response.json())
        .then((data) => {
            firstOption.text = "Select Model"
            modelSelect.disabled = false

            data.forEach((model) => {
                const option = document.createElement("option")
                option.value = model.id
                option.text = model.model_name

                if (selectedModelId && model.id == selectedModelId) {
                    option.selected = true
                }

                modelSelect.appendChild(option)
            })
        })
        .catch((error) => {
            firstOption.text = "Error loading models"
            console.error("Error loading models:", error)
        })
}

// Function to confirm deletion
function confirmDelete(phoneId) {
    const modal = document.getElementById("deleteModal")
    const confirmBtn = document.getElementById("confirmDeleteBtn")

    modal.style.display = "block"

    // Remove any existing event listener
    const newConfirmBtn = confirmBtn.cloneNode(true)
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn)

    // Add event listener for this specific deletion
    newConfirmBtn.addEventListener("click", () => {
        deletePhone(phoneId)
    })
}

// Function to delete phone
function deletePhone(phoneId) {
    const modal = document.getElementById("deleteModal")
    const confirmBtn = document.getElementById("confirmDeleteBtn")

    confirmBtn.disabled = true
    confirmBtn.textContent = "Deleting..."

    fetch("/deletePhone", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `id=${phoneId}`,
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Close modal and reload page
                modal.style.display = "none"
                window.location.reload()
            } else {
                alert(data.message || "An error occurred while deleting the phone.")
                confirmBtn.disabled = false
                confirmBtn.textContent = "Delete"
            }
        })
        .catch((error) => {
            alert("Network error: " + error.message)
            confirmBtn.disabled = false
            confirmBtn.textContent = "Delete"
        })
}

// Function to view phone details (redirect to phone page)
function viewPhone(phoneId) {
    window.location.href = `/phoneDetail?id=${phoneId}`
}