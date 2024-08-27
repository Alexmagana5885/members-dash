
// Function to open a modal
function openModal(modalId) {
    document.getElementById(modalId).style.display = "block";
}

// Function to close a modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Add event listeners for modals
document
    .querySelector(".close")
    .addEventListener("click", () => closeModal("myModal"));
document
    .querySelector(".close-past-event")
    .addEventListener("click", () => closeModal("pastEventModal"));
document
    .querySelector(".message-close-btn")
    .addEventListener("click", () => closeModal("messagePopup"));
document
    .querySelector(".close-blog-post")
    .addEventListener("click", () => closeModal("blogPostModal"));

// Quill Editor Setup
const quill = new Quill("#pastEventDetails", {
    theme: "snow",
});

const messageQuill = new Quill("#messageContent", {
    theme: "snow",
});

const blogQuill = new Quill("#blogContentEditor", {
    theme: "snow",
});

// Handling form submission
document
    .getElementById("messageForm")
    .addEventListener("submit", function () {
        document.getElementById("messageContentHidden").value =
            messageQuill.root.innerHTML;
    });

document
    .getElementById("blogPostForm")
    .addEventListener("submit", function () {
        document.getElementById("blogContent").value =
            blogQuill.root.innerHTML;
    });

document
    .getElementById("openPostEventModal")
    .addEventListener("click", () => openModal("myModal"));
document
    .getElementById("openPastEventModal")
    .addEventListener("click", () => openModal("pastEventModal"));
document
    .getElementById("openMessagePopup")
    .addEventListener("click", () => openModal("messagePopup"));
document
    .getElementById("openBlogPostModal")
    .addEventListener("click", () => openModal("blogPostModal"));
