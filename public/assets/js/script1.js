document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("sidebar-control");
    const overlay = document.getElementById("overlay");
    const mainContent = document.getElementById("main-content");

    if (!sidebar || !toggleBtn) return;

    // Desktop pinned state
    let isPinned = window.innerWidth >= 1024;

    // ===== ICON CONTROL =====
    function showMenuIcon() {
        toggleBtn.classList.remove("ri-close-line");
        toggleBtn.classList.add("ri-menu-line");
    }

    function showCloseIcon() {
        toggleBtn.classList.remove("ri-menu-line");
        toggleBtn.classList.add("ri-close-line");
    }

    // ===== DESKTOP FUNCTIONS =====
    function expandSidebar() {
        sidebar.classList.remove("lg:w-[80px]");
        sidebar.classList.add("lg:w-[300px]");

        mainContent.classList.remove("lg:ml-[80px]");
        mainContent.classList.add("lg:ml-[300px]");

        document.querySelectorAll(".menu-text").forEach(el => {
            el.classList.remove("hidden");
        });

        // Keep dropdown lists visible when expanded
        document.querySelectorAll(".dropdown-content").forEach(el => {
            el.classList.add("d-block");
            el.classList.remove("d-none");
        });
    }

    function collapseSidebar() {
        sidebar.classList.remove("lg:w-[300px]");
        sidebar.classList.add("lg:w-[80px]");

        mainContent.classList.remove("lg:ml-[300px]");
        mainContent.classList.add("lg:ml-[80px]");

        document.querySelectorAll(".menu-text").forEach(el => {
            el.classList.add("hidden");
        });

        // Hide dropdown lists when collapsed
        document.querySelectorAll(".dropdown-content").forEach(el => {
            el.classList.add("d-none");
            el.classList.remove("d-block");
        });
    }

    // ===== MOBILE OPEN/CLOSE =====
    function openMobileSidebar() {
        sidebar.classList.remove("-translate-x-full");
        overlay?.classList.remove("hidden");
        showCloseIcon();
    }

    function closeMobileSidebar() {
        sidebar.classList.add("-translate-x-full");
        overlay?.classList.add("hidden");
        showMenuIcon();
    }

    // ===== TOGGLE CLICK =====
    toggleBtn.addEventListener("click", function () {

        // Mobile
        if (window.innerWidth < 1024) {
            if (sidebar.classList.contains("-translate-x-full")) {
                openMobileSidebar();
            } else {
                closeMobileSidebar();
            }
            return;
        }

        // Desktop
        if (isPinned) {
            collapseSidebar();
            isPinned = false;
            showMenuIcon();
        } else {
            expandSidebar();
            isPinned = true;
            showCloseIcon();
        }
    });

    // ===== Overlay Click (Mobile) =====
    overlay?.addEventListener("click", closeMobileSidebar);

    // ===== Desktop Hover (when collapsed) =====
    sidebar.addEventListener("mouseenter", () => {
        if (window.innerWidth >= 1024 && !isPinned) {
            expandSidebar();
        }
    });

    sidebar.addEventListener("mouseleave", () => {
        if (window.innerWidth >= 1024 && !isPinned) {
            collapseSidebar();
        }
    });

    // ===== Dropdown =====
    document.querySelectorAll(".dropdown-btn").forEach(btn => {
        btn.addEventListener("click", function () {

            const content = btn.nextElementSibling;
            const arrow = btn.querySelector(".dropdown-arrow");

            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                content.style.opacity = 0;
                arrow.classList.remove("rotate-90");
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                content.style.opacity = 1;
                arrow.classList.add("rotate-90");
            }
        });
    });

    // ===== Window Resize Fix =====
    window.addEventListener("resize", () => {
        if (window.innerWidth >= 1024) {
            overlay?.classList.add("hidden");
            sidebar.classList.remove("-translate-x-full");
        } else {
            sidebar.classList.add("-translate-x-full");
            showMenuIcon();
        }
    });

});
