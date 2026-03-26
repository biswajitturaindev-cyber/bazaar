const sidebarControl = document.getElementById("sidebar-control");
const sidebar = document.getElementById("sidebar");
const mainContent = document.getElementById("main-content");
const overlay = document.getElementById("overlay");

// Sidebar state: open/pinned by default on desktop
let isPinned = window.innerWidth >= 1024;

// ===== ICON UTILITIES =====

function setMenuIcon() {
    sidebarControl.classList.remove("ri-close-line");
    sidebarControl.classList.add("ri-menu-line");
}

function setCloseIcon() {
    sidebarControl.classList.remove("ri-menu-line");
    sidebarControl.classList.add("ri-close-line");
}

// Set initial icon on page load
function setInitialIcon() {
    if (window.innerWidth < 1024) {
        setMenuIcon(); // Mobile: Closed by default -> Menu Icon
    } else {
        setMenuIcon(); // Desktop: Open/Pinned by default -> Menu Icon (Reverse)
    }
}
setInitialIcon();

// ===== MAIN CLICK EVENT =====
sidebarControl.onclick = () => {
    const isMobile = window.innerWidth < 1024;

    if (isMobile) {
        // --- MOBILE LOGIC ---
        const isHidden = sidebar.classList.contains("-translate-x-full");

        if (isHidden) {
            sidebar.classList.remove("-translate-x-full");
            overlay?.classList.remove("hidden");
            setCloseIcon(); // Open -> Close Icon
        } else {
            sidebar.classList.add("-translate-x-full");
            overlay?.classList.add("hidden");
            setMenuIcon(); // Closed -> Menu Icon
        }
    } else {
        // --- DESKTOP LOGIC (REVERSE) ---
        if (isPinned) {
            collapseSidebar();
            isPinned = false;
            setCloseIcon(); // Desktop Closed/Unpinned -> Close Icon
        } else {
            expandSidebar();
            isPinned = true;
            setMenuIcon(); // Desktop Open/Pinned -> Menu Icon
        }
    }
};

// ===== MOBILE OVERLAY CLICK =====
overlay?.addEventListener("click", () => {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
    setMenuIcon(); // Mobile Closed -> Menu Icon
});

// ===== DESKTOP HOVER BEHAVIOR =====
sidebar.addEventListener("mouseenter", () => {
    if (window.innerWidth >= 1024 && !isPinned) {
        expandSidebar();
        // Keep the Close icon while hovering if unpinned
    }
});

sidebar.addEventListener("mouseleave", () => {
    if (window.innerWidth >= 1024 && !isPinned) {
        collapseSidebar();
    }
});

// ===== CORE UI FUNCTIONS =====

function expandSidebar() {
    sidebar.classList.remove("lg:w-[80px]");
    sidebar.classList.add("lg:w-[280px]");
    mainContent.classList.remove("lg:ml-[80px]");
    mainContent.classList.add("lg:ml-[280px]");

    document.querySelectorAll(".menu-text").forEach(el => {
        el.classList.remove("hidden");
    });

    // Ensure dropdown lists are visible in expanded state
    document.querySelectorAll(".dropdown-content").forEach(el => {
        el.classList.add("block");
        el.classList.remove("hidden");
    });
}

function collapseSidebar() {
    sidebar.classList.remove("lg:w-[280px]");
    sidebar.classList.add("lg:w-[80px]");
    mainContent.classList.remove("lg:ml-[280px]");
    mainContent.classList.add("lg:ml-[80px]");

    document.querySelectorAll(".menu-text").forEach(el => {
        el.classList.add("hidden");
    });

    // Hide dropdown lists when the sidebar collapses
    document.querySelectorAll(".dropdown-content").forEach(el => {
        el.classList.add("hidden");
        el.classList.remove("block");
    });
}



// ===== DROPDOWN LOGIC =====
document.querySelectorAll(".dropdown-btn").forEach((button) => {
    button.addEventListener("click", function (e) {
        e.preventDefault();
        const dropdownContent = button.nextElementSibling;
        const dropdownArrow = button.querySelector(".dropdown-arrow");

        if (dropdownContent.style.maxHeight) {
            dropdownContent.style.maxHeight = null;
            dropdownContent.style.opacity = 0;
        } else {
            dropdownContent.style.maxHeight = dropdownContent.scrollHeight + "px";
            dropdownContent.style.opacity = 1;
        }
        dropdownArrow.classList.toggle("rotate-90");
    });
});
