document.addEventListener("DOMContentLoaded", function () {
  /* =========================
     VARIABLES & ELEMENTS
  ========================== */
  const toggleMenu = document.getElementById("toggleMenu");
  const sidebar = document.getElementById("sidebar");
  const body = document.body;
  
  /* =========================
     SIDEBAR TOGGLE (Fixed)
  ========================== */
  if (toggleMenu && sidebar) {
    toggleMenu.addEventListener("click", function (e) {
      e.stopPropagation();
      
      if (window.innerWidth <= 768) {
        // Mobile toggle
        sidebar.classList.toggle("show-mobile");
        body.style.overflow = sidebar.classList.contains("show-mobile") ? "hidden" : "";
        toggleMenu.textContent = sidebar.classList.contains("show-mobile") ? "✕" : "☰";
      } else {
        // Desktop toggle - toggle collapsed class
        body.classList.toggle("sidebar-collapsed");
        toggleMenu.textContent = body.classList.contains("sidebar-collapsed") ? "☰" : "✕";
      }
    });
    
    // Close mobile sidebar when clicking outside
    document.addEventListener("click", function(e) {
      if (window.innerWidth <= 768 && 
          sidebar.classList.contains("show-mobile") &&
          !sidebar.contains(e.target) && 
          !toggleMenu.contains(e.target)) {
        sidebar.classList.remove("show-mobile");
        toggleMenu.textContent = "☰";
        body.style.overflow = "";
      }
    });
  }
  
  /* =========================
     PAYMENTS DROPDOWN (Fixed)
  ========================== */
  const togglePayments = document.getElementById("togglePayments");
  if (togglePayments) {
    togglePayments.addEventListener("click", function(e) {
      e.preventDefault();
      e.stopPropagation();
      const dropdown = document.getElementById("paymentsDropdown");
      if (dropdown) {
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        this.classList.toggle("active");
      }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener("click", function(e) {
      if (!e.target.closest("#togglePayments") && !e.target.closest("#paymentsDropdown")) {
        const dropdown = document.getElementById("paymentsDropdown");
        if (dropdown) dropdown.style.display = "none";
      }
    });
  }
  
  /* =========================
     INVOICE FORM (Fixed)
  ========================== */
  document.querySelectorAll(".invoice-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const invoiceId = this.dataset.id;
      const invoiceDate = this.dataset.date;
      
      // Create hidden inputs and submit form
      const form = document.getElementById("invoiceForm");
      if (form) {
        // Add invoice_id if not exists
        if (!document.getElementById("invoice_id")) {
          const input = document.createElement("input");
          input.type = "hidden";
          input.name = "invoice_id";
          input.id = "invoice_id";
          form.appendChild(input);
        }
        
        document.getElementById("invoice_id").value = invoiceId;
        document.getElementById("date").value = invoiceDate;
        form.submit();
      }
    });
  });
  
  /* =========================
     BLOG REDIRECT (Fixed)
  ========================== */
  document.querySelectorAll(".blogSingle .moreButton").forEach((button) => {
    button.addEventListener("click", function() {
      const blogDiv = this.closest(".blogSingle");
      const blogId = blogDiv.dataset.id;
      const blogTitle = encodeURIComponent(blogDiv.dataset.title || '');
      window.location.href = `blogs.php?id=${blogId}&title=${blogTitle}`;
    });
  });
  
  /* =========================
     POPUP MANAGEMENT (Improved)
  ========================== */
  // Open popups
  document.querySelectorAll("[data-popup-target]").forEach((btn) => {
    if (btn.id !== "togglePayments") { // Skip payment toggle
      btn.addEventListener("click", function() {
        const popupId = this.dataset.popupTarget;
        const popup = document.getElementById(popupId);
        if (popup) {
          popup.style.display = "flex";
          body.style.overflow = "hidden";
        }
      });
    }
  });
  
  // Close popups
  document.querySelectorAll(".popup-close, .close, .close-btn, .closeBtn").forEach((btn) => {
    btn.addEventListener("click", function() {
      const popup = this.closest(".popup-container, .modal, .popup-form, .message-popup");
      if (popup) {
        popup.style.display = "none";
        body.style.overflow = "";
      }
    });
  });
  
  // Close popup when clicking outside
  document.querySelectorAll(".popup-container, .modal, .popup-form").forEach((popup) => {
    popup.addEventListener("click", function(e) {
      if (e.target === this) {
        this.style.display = "none";
        body.style.overflow = "";
      }
    });
  });
  
  /* =========================
     RESPONSE POPUP (Fixed)
  ========================== */
  const responsePopup = document.getElementById("response-popup");
  if (responsePopup) {
    responsePopup.style.display = "block";
    setTimeout(() => {
      responsePopup.style.opacity = "0";
      setTimeout(() => {
        responsePopup.style.display = "none";
      }, 500);
    }, 5000);
  }
  
  /* =========================
     QUILL EDITORS (Safe Initialization)
  ========================== */
  // Initialize only if elements exist and user has permission
  if (document.getElementById("quillEditor")) {
    try {
      const eventQuill = new Quill("#quillEditor", { theme: "snow" });
      const eventForm = document.getElementById("eventForm");
      if (eventForm) {
        eventForm.addEventListener("submit", function(e) {
          document.getElementById("eventDescription").value = 
            eventQuill.root.innerHTML;
        });
      }
    } catch (e) {
      console.warn("Could not initialize event Quill editor:", e);
    }
  }
  
  /* =========================
     MESSAGE POPUPS
  ========================== */
  const messageToggle = document.getElementById("toggleMessagesReceivedMessages");
  const messagePopup = document.getElementById("messagePopupReceivedMessages");
  
  if (messageToggle && messagePopup) {
    messageToggle.addEventListener("click", function(e) {
      e.preventDefault();
      messagePopup.style.display = "block";
    });
  }
  
  /* =========================
     RESIZE HANDLER (Fixed)
  ========================== */
  let resizeTimer;
  window.addEventListener("resize", function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      if (window.innerWidth > 768) {
        // Reset mobile styles
        if (sidebar) {
          sidebar.classList.remove("show-mobile");
          sidebar.style.display = "block";
        }
        if (toggleMenu) toggleMenu.textContent = "☰";
        body.style.overflow = "";
      } else {
        // Mobile view
        if (sidebar) {
          sidebar.style.display = "none";
        }
      }
    }, 250);
  });
  
  /* =========================
     PAYMENT BUTTON VALIDATION
  ========================== */
  const mpesaBtn = document.getElementById("mpesa-btn");
  if (mpesaBtn && mpesaBtn.disabled) {
    mpesaBtn.title = "Payment already processed";
  }
  
  /* =========================
     FORM VALIDATION HELPERS
  ========================== */
  // Add input validation
  document.querySelectorAll("input[required], select[required]").forEach((input) => {
    input.addEventListener("blur", function() {
      if (!this.value.trim()) {
        this.style.borderColor = "#ff6b6b";
      } else {
        this.style.borderColor = "#4caf50";
      }
    });
  });
  
  /* =========================
     FILE UPLOAD PREVIEW
  ========================== */
  document.querySelectorAll('input[type="file"][accept*="image"]').forEach((input) => {
    input.addEventListener("change", function(e) {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          // You could show preview here
          console.log("Image selected:", file.name);
        };
        reader.readAsDataURL(file);
      }
    });
  });
});

// Global function for message display
if (typeof showFullMessageReceivedMessages !== 'function') {
  window.showFullMessageReceivedMessages = function(message) {
    const popup = document.getElementById("fullMessagePopupReceivedMessages");
    const text = document.getElementById("fullMessageTextReceivedMessages");
    if (popup && text) {
      text.textContent = message;
      popup.style.display = "flex";
      document.body.style.overflow = "hidden";
    }
  };
}