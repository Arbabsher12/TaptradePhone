document.addEventListener("DOMContentLoaded", () => {
    // Get the modal
    const modal = document.getElementById("profile-modal")

    // Get the button that opens the modal
    const btn = document.getElementById("edit-profile-btn")

    // Get the <span> element that closes the modal
    const span = document.getElementsByClassName("close")[0]

    // When the user clicks the button, open the modal
    btn.onclick = () => {
        modal.style.display = "block"
        document.body.style.overflow = "hidden" // Prevent scrolling behind modal
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = () => {
        modal.style.display = "none"
        document.body.style.overflow = "" // Restore scrolling
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = "none"
            document.body.style.overflow = "" // Restore scrolling
        }
    }

    // Preview uploaded image
    const profilePictureInput = document.getElementById("profile_picture")
    const profilePreview = document.getElementById("profile-preview")

    profilePictureInput.addEventListener("change", function() {
        const file = this.files[0]
        if (file) {
            const reader = new FileReader()

            reader.addEventListener("load", () => {
                profilePreview.src = reader.result
            })

            reader.readAsDataURL(file)
        }
    })

    // Form validation
    const profileForm = document.getElementById("profile-form")

    profileForm.addEventListener("submit", (event) => {
        const nameInput = document.getElementById("name")
        const emailInput = document.getElementById("email")
        let isValid = true

        // Validate name
        if (nameInput.value.trim() === "") {
            isValid = false
            showError(nameInput, "Name is required")
        } else {
            clearError(nameInput)
        }

        // Validate email
        if (emailInput.value.trim() === "") {
            isValid = false
            showError(emailInput, "Email is required")
        } else if (!isValidEmail(emailInput.value)) {
            isValid = false
            showError(emailInput, "Please enter a valid email address")
        } else {
            clearError(emailInput)
        }

        if (!isValid) {
            event.preventDefault()
        }
    })

    function showError(input, message) {
        const formGroup = input.parentElement
        let errorElement = formGroup.querySelector(".error-message")

        if (!errorElement) {
            errorElement = document.createElement("div")
            errorElement.className = "error-message"
            errorElement.style.color = "#b91c1c"
            errorElement.style.fontSize = "0.875rem"
            errorElement.style.marginTop = "0.25rem"
            formGroup.appendChild(errorElement)
        }

        errorElement.textContent = message
        input.style.borderColor = "#b91c1c"
    }

    function clearError(input) {
        const formGroup = input.parentElement
        const errorElement = formGroup.querySelector(".error-message")

        if (errorElement) {
            formGroup.removeChild(errorElement)
        }

        input.style.borderColor = "#ddd"
    }

    function isValidEmail(email) {
        const re =
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        return re.test(String(email).toLowerCase())
    }
})