const notification = document.getElementById("notification");
const closeNotification = document.getElementById("closeNotification");

closeNotification.addEventListener("click", function () {
    notification.classList.add("hidden");
});

const hamburger = document.getElementById("hamburger");
const navLinks = document.getElementById("navLinks");

hamburger.addEventListener("click", function () {
    const isOpen = navLinks.classList.toggle("show");

    hamburger.setAttribute("aria-expanded", isOpen);
    hamburger.textContent = isOpen ? "✕" : "☰";
});

navLinks.querySelectorAll("a").forEach(function (link) {
    link.addEventListener("click", function () {
        navLinks.classList.remove("show");
        hamburger.setAttribute("aria-expanded", "false");
        hamburger.textContent = "☰";
    });
});

const themeToggle = document.getElementById("themeToggle");

themeToggle.addEventListener("click", function () {
    document.body.classList.toggle("dark");

    const darkMode = document.body.classList.contains("dark");

    themeToggle.textContent = darkMode
        ? "☀️ Light Mode"
        : "🌙 Dark Mode";
});

const faqQuestions = document.querySelectorAll(".faq-question");

faqQuestions.forEach(function (question) {
    question.addEventListener("click", function () {
        const item = question.parentElement;
        const isOpen = item.classList.toggle("open");

        question.setAttribute("aria-expanded", isOpen);

        const symbol = question.querySelector("span");
        symbol.textContent = isOpen ? "−" : "＋";
    });
});

const modal = document.getElementById("registrationModal");
const openModalButtons = document.querySelectorAll(".openModal");
const closeModal = document.getElementById("closeModal");
const fullName = document.getElementById("fullName");

function showModal() {
    modal.classList.add("show");
    modal.setAttribute("aria-hidden", "false");
    document.getElementById("formMessage").textContent = "";
    fullName.focus();
}

function hideModal() {
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
}

openModalButtons.forEach(function (button) {
    button.addEventListener("click", showModal);
});

closeModal.addEventListener("click", hideModal);

modal.addEventListener("click", function (event) {
    if (event.target === modal) {
        hideModal();
    }
});

document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && modal.classList.contains("show")) {
        hideModal();
    }
});

const registrationForm = document.getElementById("registrationForm");

registrationForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const name = fullName.value.trim();

    document.getElementById("formMessage").textContent =
        "Thank you, " + name + "! Demo registration successful.";

    registrationForm.reset();
});

const slides = document.querySelectorAll(".slide");
const dots = document.querySelectorAll(".dot");
const prevSlide = document.getElementById("prevSlide");
const nextSlide = document.getElementById("nextSlide");

let currentSlide = 0;

function showSlide(index) {
  
    currentSlide = (index + slides.length) % slides.length;

    slides.forEach(function (slide, i) {
        slide.classList.toggle("active", i === currentSlide);
    });

    dots.forEach(function (dot, i) {
        dot.classList.toggle("active", i === currentSlide);
        dot.setAttribute("aria-current",
            i === currentSlide ? "true" : "false");
    });
}

nextSlide.addEventListener("click", function () {
    showSlide(currentSlide + 1);
});

prevSlide.addEventListener("click", function () {
    showSlide(currentSlide - 1);
});

dots.forEach(function (dot, index) {
    dot.addEventListener("click", function () {
        showSlide(index);
    });
});

let sliderTimer = setInterval(function () {
    showSlide(currentSlide + 1);
}, 5000);

showSlide(0);
