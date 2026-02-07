document
  .getElementById("togglePayments")
  .addEventListener("click", function (event) {
    event.preventDefault();
    const dropdown = document.getElementById("paymentsDropdown");
    dropdown.style.display =
      dropdown.style.display === "block" ? "none" : "block";
  });

const invoiceLinks = document.querySelectorAll(".invoice-link");
invoiceLinks.forEach(function (link) {
  link.addEventListener("click", function (event) {
    event.preventDefault();

    const invoiceId = link.getAttribute("data-id");
    const invoiceDate = link.getAttribute("data-date");

    document.getElementById("date").value = invoiceDate;

    document.getElementById("invoiceForm").submit();
  });
});

function togglePopup(popupId) {
  const popup = document.getElementById(popupId);
  const displayState = popup.style.display === "flex" ? "none" : "flex";
  popup.style.display = displayState;
}

document.addEventListener("DOMContentLoaded", function () {
  const openButtons = document.querySelectorAll("[data-popup-target]");
  const closeButtons = document.querySelectorAll(".popup-close");

  // Open popup on button click
  openButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const popupId = this.getAttribute("data-popup-target");
      togglePopup(popupId);
    });
  });

  // Close popup on close button click
  closeButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const popup = this.closest(".popup-container");
      popup.style.display = "none";
    });
  });

  // Close popup if clicking outside the popup content
  window.addEventListener("click", function (event) {
    if (event.target.classList.contains("popup-container")) {
      event.target.style.display = "none";
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  var popup = document.getElementById("response-popup");
  if (popup) {
    // Show the popup
    popup.classList.add("show");

    // Hide the popup after 30 seconds
    setTimeout(function () {
      popup.classList.remove("show");
    }, 10000); // 30000ms = 30 seconds
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const toggleButton = document.getElementById("toggleMenu");
  const sidebar = document.getElementById("sidebar");
  const dashboard = document.querySelector(".dashboard");

  toggleButton.addEventListener("click", function () {
    if (window.innerWidth <= 768) {
      // MOBILE: Toggle the sidebar
      sidebar.classList.toggle("show-mobile");

      // Update button appearance
      if (sidebar.classList.contains("show-mobile")) {
        toggleButton.innerHTML = "X";
        toggleButton.style.fontWeight = "bold";
        toggleButton.style.color = "black";
      } else {
        toggleButton.innerHTML = "☰";
        toggleButton.style.fontWeight = "normal";
        toggleButton.style.color = "#1E5BC6";
      }
    } else {
      // DESKTOP: Toggle with margin adjustment
      if (sidebar.style.display === "none" || sidebar.style.display === "") {
        sidebar.style.display = "block";
        if (dashboard) dashboard.style.marginLeft = "260px";
        toggleButton.innerHTML = "X";
        toggleButton.style.fontWeight = "bold";
        toggleButton.style.color = "black";
      } else {
        sidebar.style.display = "none";
        if (dashboard) dashboard.style.marginLeft = "0";
        toggleButton.innerHTML = "☰";
        toggleButton.style.fontWeight = "normal";
        toggleButton.style.color = "#1E5BC6";
      }
    }
  });

  // Handle window resize
  window.addEventListener("resize", function () {
    if (window.innerWidth > 768) {
      // On desktop, ensure sidebar is visible
      sidebar.style.display = "block";
      sidebar.classList.remove("show-mobile");
      if (dashboard) dashboard.style.marginLeft = "260px";
    } else {
      // On mobile, ensure sidebar is hidden by default
      sidebar.style.display = ""; // Reset inline display
      sidebar.classList.remove("show-mobile");
      if (dashboard) dashboard.style.marginLeft = "0";
      toggleButton.innerHTML = "☰";
      toggleButton.style.fontWeight = "normal";
      toggleButton.style.color = "#1E5BC6";
    }
  });
});

// Add event listener for the More button
document.querySelectorAll(".moreButton").forEach((button) => {
  button.addEventListener("click", function () {
    const blogSingle = this.closest(".blogSingle");
    const blogId = blogSingle.getAttribute("data-id"); // Get the blog ID

    // Redirect to another page with the blog ID
    window.location.href = "blogs.php?id=" + blogId;
  });
});

document
  .getElementById("toggleMessagesReceivedMessages")
  .addEventListener("click", function () {
    document.getElementById("messagePopupReceivedMessages").style.display =
      "flex";
  });

document
  .getElementById("closePopupReceivedMessages")
  .addEventListener("click", function () {
    document.getElementById("messagePopupReceivedMessages").style.display =
      "none";
  });

function showFullMessageReceivedMessages(message) {
  document.getElementById("fullMessageTextReceivedMessages").textContent =
    message;
  document.getElementById("fullMessagePopupReceivedMessages").style.display =
    "flex";
}

document
  .getElementById("closeFullMessageReceivedMessages")
  .addEventListener("click", function () {
    document.getElementById("fullMessagePopupReceivedMessages").style.display =
      "none";
  });

document.addEventListener("DOMContentLoaded", function () {
  // Initialize Quill editor
  var quill = new Quill("#quillEditor", {
    theme: "snow",
  });

  // Handle the form submission
  document.getElementById("eventForm").onsubmit = function (event) {
    event.preventDefault();
    var eventDescriptionInput = document.getElementById("eventDescription");
    eventDescriptionInput.value = quill.root.innerHTML;

    // Now submit the form
    this.submit();
  };
});

var myModal = document.getElementById("myModal");

var openPostEventModal = document.getElementById("openPostEventModal");

var closeBtn = document.getElementsByClassName("close")[0];

openPostEventModal.onclick = function (event) {
  event.preventDefault();
  myModal.style.display = "block";
};

closeBtn.onclick = function () {
  myModal.style.display = "none";
};

window.onclick = function (event) {
  if (event.target == modal) {
    myModal.style.display = "none";
  }
};

// Initialize the Quill editor for Event Details
var pastEventQuill = new Quill("#pastEventDetailsEditor", {
  theme: "snow",
  placeholder: "Enter event details...",
  modules: {
    toolbar: [
      [
        {
          header: "1",
        },
        {
          header: "2",
        },
      ],
      [
        {
          list: "ordered",
        },
        {
          list: "bullet",
        },
      ],
      ["bold", "italic", "underline"],
      ["link", "image"],
    ],
  },
});

// Attach an event listener for form submission
document
  .getElementById("pastEventForm")
  .addEventListener("submit", function (event) {
    // Prevent the form from submitting immediately
    event.preventDefault();

    // Set the value of the hidden input to the Quill editor content
    document.getElementById("pastEventDetails").value =
      pastEventQuill.root.innerHTML;

    // Now submit the form
    this.submit();
  });

var modal = document.getElementById("pastEventModal");
var btn = document.getElementById("openPastEventModal");
var span = document.getElementsByClassName("close-past-event")[0];
btn.onclick = function () {
  modal.style.display = "block";
};
span.onclick = function () {
  modal.style.display = "none";
};
window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};

function showMessagePopup() {
  document.getElementById("messagePopupsend").style.display = "flex";
}

function hideMessagePopup() {
  document.getElementById("messagePopupsend").style.display = "none";
}

document
  .getElementById("openMessagePopupSend")
  .addEventListener("click", function (event) {
    event.preventDefault();
    showMessagePopup();
  });

document
  .getElementById("messageClosePopupBtn")
  .addEventListener("click", function (event) {
    event.preventDefault();
    hideMessagePopup();
  });

// Initialize the Quill editor
var quill = new Quill("#editor", {
  theme: "snow",
  placeholder: "Write your blog content...",
  modules: {
    toolbar: [
      [
        {
          header: "1",
        },
        {
          header: "2",
        },
      ],
      [
        {
          list: "ordered",
        },
        {
          list: "bullet",
        },
      ],
      ["bold", "italic", "underline"],
    ],
  },
});

// Attach an event listener for form submission
document
  .getElementById("blogPostForm")
  .addEventListener("submit", function (event) {
    // Prevent the form from submitting immediately
    event.preventDefault();

    // Set the value of the hidden input to the Quill editor content
    document.getElementById("blogContent").value = quill.root.innerHTML;

    // Now submit the form
    this.submit();
  });

const blogPostModal = document.getElementById("blogPostModal");
const openBlogPostModal = document.getElementById("openBlogPostModal");
const closeBlogPost = document.querySelector(".close-blog-post");
openBlogPostModal.addEventListener("click", function (event) {
  event.preventDefault();
  blogPostModal.style.display = "block";
});
closeBlogPost.addEventListener("click", function () {
  blogPostModal.style.display = "none";
});
window.addEventListener("click", function (event) {
  if (event.target == blogPostModal) {
    blogPostModal.style.display = "none";
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // Attach click event listeners to all buttons with class 'PastEventsmoreButton'
  document.querySelectorAll(".PastEventsmoreButton").forEach((button) => {
    button.addEventListener("click", function () {
      // Get the event ID from the button's data-id attribute
      var eventId = this.getAttribute("data-id");
      // Redirect to event.php with the event ID as a query parameter
      window.location.href = "Event.php?id=" + eventId;
    });
  });
});
