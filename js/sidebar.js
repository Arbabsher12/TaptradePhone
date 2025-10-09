document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar")
    const showSidebarBtn = document.getElementById("showSidebar")
    const hideSidebarBtn = document.getElementById("hideSidebar")

    if (showSidebarBtn && sidebar && hideSidebarBtn) {
        // Show Sidebar
        showSidebarBtn.addEventListener("click", () => {
            sidebar.style.transform = "translateX(0)"
            showSidebarBtn.style.display = "none" // Hide button in navbar
        })

        // Hide Sidebar
        hideSidebarBtn.addEventListener("click", () => {
            sidebar.style.transform = "translateX(-100%)"
            setTimeout(() => {
                showSidebarBtn.style.display = "block" // Show button in navbar
            }, 300)
        })
    }
})