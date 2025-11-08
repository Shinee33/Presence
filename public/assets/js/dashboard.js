// Dashboard JavaScript
$(document).ready(function () {
    // Initialize dashboard
    initializeDashboard();

    // Tab switching functionality
    $(".custom-tab").on("click", function (e) {
        e.preventDefault();
        switchTab($(this));
    });

    // Menu item click effects
    $(".menu-item").on("click", function (e) {
        addClickEffect($(this));
    });

    // Initialize tooltips if needed
    initializeTooltips();

    // Auto-refresh functionality
    setupAutoRefresh();

    // Handle responsive layout changes
    handleResponsiveChanges();

    // Initialize loading states
    initializeLoadingStates();
});

/**
 * Initialize dashboard components
 */
function initializeDashboard() {
    console.log("Dashboard initialized successfully");

    // Add fade-in animation to cards
    $(".presence-card, .stat-card").each(function (index) {
        $(this)
            .css({
                opacity: "0",
                transform: "translateY(20px)",
            })
            .delay(index * 100)
            .animate(
                {
                    opacity: "1",
                },
                500
            )
            .css("transform", "translateY(0)");
    });

    // Update current time display
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
}

/**
 * Switch between tabs
 * @param {jQuery} clickedTab - The clicked tab element
 */
function switchTab(clickedTab) {
    // Check if tab is already active
    if (clickedTab.hasClass("active")) {
        return;
    }

    // Add loading state
    addLoadingState();

    // Remove active class from all tabs and content
    $(".custom-tab").removeClass("active");
    $(".tab-content").removeClass("active");

    // Add active class to clicked tab
    clickedTab.addClass("active");

    // Show corresponding content with animation
    const tabId = clickedTab.data("tab");
    const targetContent = $("#" + tabId);

    setTimeout(() => {
        targetContent.addClass("active");
        removeLoadingState();
    }, 150);

    // Log tab switch for analytics
    console.log("Tab switched to:", tabId);
}

/**
 * Add click effect to menu items
 * @param {jQuery} element - The clicked element
 */
function addClickEffect(element) {
    element.addClass("clicked");

    // Add ripple effect
    const ripple = $('<span class="ripple"></span>');
    element.append(ripple);

    setTimeout(() => {
        element.removeClass("clicked");
        ripple.remove();
    }, 300);
}

/**
 * Initialize tooltips for better UX
 */
function initializeTooltips() {
    // Add tooltips to stat cards
    $(".stat-card").each(function () {
        const label = $(this).find(".stat-label").text();
        const number = $(this).find(".stat-number").text();
        $(this).attr("title", `${label}: ${number}`);
    });

    // Add tooltips to presence cards
    $(".presence-card").each(function () {
        const title = $(this).find(".presence-title").text();
        const time = $(this).find(".presence-time").text();
        $(this).attr("title", `${title}: ${time}`);
    });
}

/**
 * Setup auto-refresh functionality
 */
function setupAutoRefresh() {
    // Auto-refresh presence data every 5 minutes
    setInterval(function () {
        refreshPresenceData();
    }, 300000); // 5 minutes

    // Auto-refresh stats every 10 minutes
    setInterval(function () {
        refreshStatsData();
    }, 600000); // 10 minutes
}

/**
 * Refresh presence data via AJAX
 */
function refreshPresenceData() {
    console.log("Refreshing presence data...");

    // Add loading indicator
    $(".presence-section").addClass("loading");

    // Example AJAX call (uncomment and modify as needed)
    /*
    $.ajax({
        url: '/api/presence/today',
        method: 'GET',
        success: function(response) {
            updatePresenceCards(response);
            $('.presence-section').removeClass('loading');
            showNotification('Data berhasil diperbarui', 'success');
        },
        error: function(xhr, status, error) {
            $('.presence-section').removeClass('loading');
            showNotification('Gagal memperbarui data', 'error');
            console.error('Error refreshing presence data:', error);
        }
    });
    */

    // Remove loading state after simulation
    setTimeout(() => {
        $(".presence-section").removeClass("loading");
    }, 1000);
}

/**
 * Refresh statistics data via AJAX
 */
function refreshStatsData() {
    console.log("Refreshing stats data...");

    // Add loading indicator
    $(".stats-grid").addClass("loading");

    // Example AJAX call (uncomment and modify as needed)
    /*
    $.ajax({
        url: '/api/stats/monthly',
        method: 'GET',
        success: function(response) {
            updateStatsCards(response);
            $('.stats-grid').removeClass('loading');
        },
        error: function(xhr, status, error) {
            $('.stats-grid').removeClass('loading');
            console.error('Error refreshing stats data:', error);
        }
    });
    */

    // Remove loading state after simulation
    setTimeout(() => {
        $(".stats-grid").removeClass("loading");
    }, 1000);
}

/**
 * Update presence cards with new data
 * @param {Object} data - The presence data
 */
function updatePresenceCards(data) {
    if (data.check_in) {
        $(".presence-card:first .presence-time").text(data.check_in.time);
        if (data.check_in.photo) {
            $(".presence-card:first .no-presence").replaceWith(
                `<img src="${data.check_in.photo}" alt="Check In" class="presence-photo checkin">`
            );
        }
    }

    if (data.check_out) {
        $(".presence-card:last .presence-time").text(data.check_out.time);
        if (data.check_out.photo) {
            $(".presence-card:last .no-presence").replaceWith(
                `<img src="${data.check_out.photo}" alt="Check Out" class="presence-photo checkout">`
            );
        }
    }
}

/**
 * Update statistics cards with new data
 * @param {Object} data - The statistics data
 */
function updateStatsCards(data) {
    if (data.attendance)
        $(".stat-card:eq(0) .stat-number").text(data.attendance);
    if (data.permission)
        $(".stat-card:eq(1) .stat-number").text(data.permission);
    if (data.sick) $(".stat-card:eq(2) .stat-number").text(data.sick);
    if (data.absent) $(".stat-card:eq(3) .stat-number").text(data.absent);
}

/**
 * Handle responsive layout changes
 */
function handleResponsiveChanges() {
    let resizeTimer;

    $(window).on("resize", function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            adjustLayoutForScreenSize();
        }, 250);
    });

    // Initial adjustment
    adjustLayoutForScreenSize();
}

/**
 * Adjust layout based on screen size
 */
function adjustLayoutForScreenSize() {
    const windowWidth = $(window).width();

    // Adjust stats grid for mobile
    if (windowWidth <= 768) {
        $(".stats-grid").removeClass("stats-grid-4").addClass("stats-grid-2");
    } else {
        $(".stats-grid").removeClass("stats-grid-2").addClass("stats-grid-4");
    }

    // Adjust floating button position on very small screens
    if (windowWidth <= 480) {
        $(".floating-button").css({
            bottom: "20px",
            right: "20px",
            width: "50px",
            height: "50px",
        });
    }
}

/**
 * Initialize loading states
 */
function initializeLoadingStates() {
    // Add skeleton loading for initial load
    if ($(".history-list .history-item").length === 0) {
        addSkeletonLoading(".history-list");
    }
}

/**
 * Add loading state to dashboard
 */
function addLoadingState() {
    $(".tab-content.active").addClass("loading");
}

/**
 * Remove loading state from dashboard
 */
function removeLoadingState() {
    $(".tab-content").removeClass("loading");
}

/**
 * Add skeleton loading animation
 * @param {string} selector - The target selector
 */
function addSkeletonLoading(selector) {
    const skeletonHTML = `
        <div class="skeleton-item">
            <div class="skeleton skeleton-avatar"></div>
            <div class="skeleton-content">
                <div class="skeleton skeleton-line"></div>
                <div class="skeleton skeleton-line-short"></div>
            </div>
        </div>
    `;

    $(selector).html(Array(3).fill(skeletonHTML).join(""));
}

/**
 * Update current time display
 */
function updateCurrentTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });

    // Update time in header if element exists
    if ($("#current-time").length) {
        $("#current-time").text(timeString);
    }
}

/**
 * Show notification to user
 * @param {string} message - The notification message
 * @param {string} type - The notification type (success, error, info)
 */
function showNotification(message, type = "info") {
    // Create notification element
    const notification = $(`
        <div class="notification notification-${type}">
            <span class="notification-message">${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `);

    // Add to page
    $("body").append(notification);

    // Show with animation
    setTimeout(() => {
        notification.addClass("show");
    }, 100);

    // Auto-hide after 3 seconds
    setTimeout(() => {
        hideNotification(notification);
    }, 3000);

    // Handle close button
    notification.find(".notification-close").on("click", () => {
        hideNotification(notification);
    });
}

/**
 * Hide notification
 * @param {jQuery} notification - The notification element
 */
function hideNotification(notification) {
    notification.removeClass("show");
    setTimeout(() => {
        notification.remove();
    }, 300);
}

/**
 * Handle page visibility changes for performance
 */
$(document).on("visibilitychange", function () {
    if (document.hidden) {
        // Page is hidden, pause unnecessary operations
        console.log("Dashboard paused");
    } else {
        // Page is visible, resume operations
        console.log("Dashboard resumed");
        // Optionally refresh data when user returns
        refreshPresenceData();
    }
});

/**
 * Handle online/offline status
 */
$(window).on("online", function () {
    showNotification("Koneksi internet tersambung kembali", "success");
    // Refresh data when back online
    refreshPresenceData();
    refreshStatsData();
});

$(window).on("offline", function () {
    showNotification("Koneksi internet terputus", "error");
});

/**
 * Handle errors gracefully
 */
$(document).ajaxError(function (event, xhr, settings, thrownError) {
    console.error("AJAX Error:", {
        url: settings.url,
        status: xhr.status,
        error: thrownError,
    });

    showNotification("Terjadi kesalahan saat memuat data", "error");
});

/**
 * Utility function to format time
 * @param {string} timeString - Time string to format
 * @returns {string} Formatted time
 */
function formatTime(timeString) {
    if (!timeString) return "Belum Presensi";

    try {
        const time = new Date(`2000-01-01 ${timeString}`);
        return time.toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit",
        });
    } catch (error) {
        return timeString;
    }
}

/**
 * Utility function to format date
 * @param {string} dateString - Date string to format
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    if (!dateString) return "";

    try {
        const date = new Date(dateString);
        return date.toLocaleDateString("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        });
    } catch (error) {
        return dateString;
    }
}

// Export functions for testing (if needed)
if (typeof module !== "undefined" && module.exports) {
    module.exports = {
        switchTab,
        addClickEffect,
        refreshPresenceData,
        refreshStatsData,
        showNotification,
        formatTime,
        formatDate,
    };
}
