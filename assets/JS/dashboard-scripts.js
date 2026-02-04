// ===========================
// DOM Ready Function
// ===========================
document.addEventListener('DOMContentLoaded', function() {
    initializeSidebar();
    initializePopups();
    initializeModals();
    initializePaymentDropdown();
    initializeInvoiceLinks();
    initializeBlogButtons();
    initializeEventRegistration();
    initializeResponsePopup();
    initializeMessageSystem();
});

// ===========================
// SIDEBAR FUNCTIONALITY
// ===========================
function initializeSidebar() {
    const toggleButton = document.getElementById('toggleMenu');
    const sidebar = document.getElementById('sidebar');

    if (toggleButton && sidebar) {
        toggleButton.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                // Mobile: toggle active class
                sidebar.classList.toggle('active');
                if (sidebar.classList.contains('active')) {
                    toggleButton.innerHTML = 'X';
                    toggleButton.style.fontWeight = 'bold';
                    toggleButton.style.color = 'black';
                } else {
                    toggleButton.innerHTML = '☰';
                }
            } else {
                // Desktop: toggle display
                if (sidebar.style.display === 'block') {
                    sidebar.style.display = 'none';
                    toggleButton.innerHTML = '☰';
                } else {
                    sidebar.style.display = 'block';
                    toggleButton.innerHTML = 'X';
                    toggleButton.style.fontWeight = 'bold';
                    toggleButton.style.color = 'black';
                }
            }
        });
    }
}

// ===========================
// PAYMENT POPUPS
// ===========================
function initializePopups() {
    // Toggle popup function
    window.togglePopup = function(popupId) {
        const popup = document.getElementById(popupId);
        const displayState = popup.style.display === 'flex' ? 'none' : 'flex';
        popup.style.display = displayState;
    };

    // Open popup on button click
    const openButtons = document.querySelectorAll('[data-popup-target]');
    const closeButtons = document.querySelectorAll('.popup-close');

    openButtons.forEach(button => {
        button.addEventListener('click', function() {
            const popupId = this.getAttribute('data-popup-target');
            togglePopup(popupId);
        });
    });

    // Close popup on close button click
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const popup = this.closest('.popup-container');
            if (popup) {
                popup.style.display = 'none';
            }
        });
    });

    // Close popup if clicking outside the popup content
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('popup-container')) {
            event.target.style.display = 'none';
        }
    });
}

// ===========================
// MODALS FUNCTIONALITY
// ===========================
function initializeModals() {
    // Planned Event Modal
    const plannedEventModal = document.getElementById("myModal");
    const openPostEventModal = document.getElementById("openPostEventModal");
    const closePlannedEventBtn = document.querySelector(".close");

    if (openPostEventModal && plannedEventModal) {
        openPostEventModal.onclick = function(event) {
            event.preventDefault();
            plannedEventModal.style.display = "block";
        };
    }

    if (closePlannedEventBtn && plannedEventModal) {
        closePlannedEventBtn.onclick = function() {
            plannedEventModal.style.display = "none";
        };
    }

    // Past Event Modal
    const pastEventModal = document.getElementById("pastEventModal");
    const openPastEventModal = document.getElementById("openPastEventModal");
    const closePastEventBtn = document.querySelector(".close-past-event");

    if (openPastEventModal && pastEventModal) {
        openPastEventModal.onclick = function(event) {
            event.preventDefault();
            pastEventModal.style.display = "block";
        };
    }

    if (closePastEventBtn && pastEventModal) {
        closePastEventBtn.onclick = function() {
            pastEventModal.style.display = "none";
        };
    }

    // Blog Post Modal
    const blogPostModal = document.getElementById("blogPostModal");
    const openBlogPostModal = document.getElementById("openBlogPostModal");
    const closeBlogPostBtn = document.querySelector(".close-blog-post");

    if (openBlogPostModal && blogPostModal) {
        openBlogPostModal.addEventListener("click", function(event) {
            event.preventDefault();
            blogPostModal.style.display = "block";
        });
    }

    if (closeBlogPostBtn && blogPostModal) {
        closeBlogPostBtn.addEventListener("click", function() {
            blogPostModal.style.display = "none";
        });
    }

    // Message Send Modal
    const messagePopupSend = document.getElementById("messagePopupsend");
    const openMessagePopupSend = document.getElementById("openMessagePopupSend");
    const closeMessagePopupBtn = document.getElementById("messageClosePopupBtn");

    if (openMessagePopupSend && messagePopupSend) {
        openMessagePopupSend.addEventListener('click', function(event) {
            event.preventDefault();
            messagePopupSend.style.display = 'flex';
        });
    }

    if (closeMessagePopupBtn && messagePopupSend) {
        closeMessagePopupBtn.addEventListener('click', function(event) {
            event.preventDefault();
            messagePopupSend.style.display = 'none';
        });
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target == plannedEventModal) {
            plannedEventModal.style.display = "none";
        }
        if (event.target == pastEventModal) {
            pastEventModal.style.display = "none";
        }
        if (event.target == blogPostModal) {
            blogPostModal.style.display = "none";
        }
        if (event.target == messagePopupSend) {
            messagePopupSend.style.display = 'none';
        }
    };
}

// ===========================
// PAYMENT DROPDOWN
// ===========================
function initializePaymentDropdown() {
    const togglePayments = document.getElementById('togglePayments');
    if (togglePayments) {
        togglePayments.addEventListener('click', function(event) {
            event.preventDefault();
            const dropdown = document.getElementById('paymentsDropdown');
            if (dropdown) {
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                this.classList.toggle('active');
            }
        });
    }
}

// ===========================
// INVOICE LINKS
// ===========================
function initializeInvoiceLinks() {
    const invoiceLinks = document.querySelectorAll('.invoice-link');
    invoiceLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();

            const invoiceId = link.getAttribute('data-id');
            const invoiceDate = link.getAttribute('data-date');

            document.getElementById('date').value = invoiceDate;

            const invoiceForm = document.getElementById('invoiceForm');
            if (invoiceForm) {
                invoiceForm.submit();
            }
        });
    });
}

// ===========================
// BLOG BUTTONS
// ===========================
function initializeBlogButtons() {
    // Add event listener for the More button
    document.querySelectorAll('.moreButton').forEach(button => {
        button.addEventListener('click', function() {
            const blogSingle = this.closest('.blogSingle');
            const blogId = blogSingle.getAttribute('data-id');
            window.location.href = 'blogs.php?id=' + blogId;
        });
    });
}

// ===========================
// EVENT REGISTRATION
// ===========================
function initializeEventRegistration() {
    // This will be populated by PHP-generated JavaScript for each event
    // The PHP code in the main file will generate specific event handlers
}

// ===========================
// RESPONSE POPUP
// ===========================
function initializeResponsePopup() {
    var popup = document.getElementById('response-popup');
    if (popup) {
        // Show the popup
        popup.classList.add('show');

        // Hide the popup after 10 seconds
        setTimeout(function() {
            popup.classList.remove('show');
        }, 10000);
    }
}

// ===========================
// MESSAGE SYSTEM
// ===========================
function initializeMessageSystem() {
    // Received Messages
    const toggleMessagesBtn = document.getElementById('toggleMessagesReceivedMessages');
    const messagePopup = document.getElementById('messagePopupReceivedMessages');
    const closeMessagePopup = document.getElementById('closePopupReceivedMessages');
    const closeFullMessage = document.getElementById('closeFullMessageReceivedMessages');
    const fullMessagePopup = document.getElementById('fullMessagePopupReceivedMessages');

    if (toggleMessagesBtn && messagePopup) {
        toggleMessagesBtn.addEventListener('click', function(event) {
            event.preventDefault();
            messagePopup.style.display = 'flex';
        });
    }

    if (closeMessagePopup && messagePopup) {
        closeMessagePopup.addEventListener('click', function() {
            messagePopup.style.display = 'none';
        });
    }

    // Function to show full message
    window.showFullMessageReceivedMessages = function(message) {
        const fullMessageText = document.getElementById('fullMessageTextReceivedMessages');
        if (fullMessageText && fullMessagePopup) {
            fullMessageText.textContent = message;
            fullMessagePopup.style.display = 'flex';
        }
    };

    if (closeFullMessage && fullMessagePopup) {
        closeFullMessage.addEventListener('click', function() {
            fullMessagePopup.style.display = 'none';
        });
    }

    // Close popups when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target == messagePopup) {
            messagePopup.style.display = 'none';
        }
        if (event.target == fullMessagePopup) {
            fullMessagePopup.style.display = 'none';
        }
    });
}

// ===========================
// QUILL EDITOR INITIALIZATION
// ===========================
function initializeQuillEditors() {
    // Planned Event Editor
    if (document.getElementById('quillEditor')) {
        var quill = new Quill('#quillEditor', {
            theme: 'snow'
        });

        // Handle the form submission
        document.getElementById('eventForm').onsubmit = function(event) {
            event.preventDefault();
            var eventDescriptionInput = document.getElementById('eventDescription');
            eventDescriptionInput.value = quill.root.innerHTML;
            this.submit();
        };
    }

    // Past Event Editor
    if (document.getElementById('pastEventDetailsEditor')) {
        var pastEventQuill = new Quill('#pastEventDetailsEditor', {
            theme: 'snow',
            placeholder: 'Enter event details...',
            modules: {
                toolbar: [
                    [{ 'header': '1' }, { 'header': '2' }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['bold', 'italic', 'underline'],
                    ['link', 'image']
                ]
            }
        });

        document.getElementById('pastEventForm').addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById('pastEventDetails').value = pastEventQuill.root.innerHTML;
            this.submit();
        });
    }

    // Blog Editor
    if (document.getElementById('editor')) {
        var blogQuill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Write your blog content...',
            modules: {
                toolbar: [
                    [{ 'header': '1' }, { 'header': '2' }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['bold', 'italic', 'underline'],
                ]
            }
        });

        document.getElementById('blogPostForm').addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById('blogContent').value = blogQuill.root.innerHTML;
            this.submit();
        });
    }
}

// Initialize Quill editors when Quill is loaded
if (typeof Quill !== 'undefined') {
    initializeQuillEditors();
} else {
    // Wait for Quill to load
    document.addEventListener('quill-loaded', initializeQuillEditors);
}