if (window.innerWidth > 1024) {

const slides = document.querySelectorAll(".service-body");
const flipBox = document.querySelector(".flip-box");
const scrollHint = document.querySelector(".scroll-hint");

let current = 0;
let isFlipping = false;

slides[current].classList.add("active");

function flipTo(next, direction = "down") {
  if (isFlipping || next === current) return;
  isFlipping = true;

  const currentSlide = slides[current];
  const nextSlide = slides[next];

  // Smooth direction-aware rotation
  currentSlide.style.transformOrigin = direction === "down" ? "bottom center" : "top center";
  nextSlide.style.transformOrigin = direction === "down" ? "top center" : "bottom center";

  currentSlide.classList.add("exiting");
  nextSlide.classList.add("active");

  setTimeout(() => {
    currentSlide.classList.remove("active", "exiting");
    current = next;
    isFlipping = false;
  }, 500); // faster flip timing
}

// Scroll flip
flipBox.addEventListener("wheel", (e) => {
  if (isFlipping) return;
  e.preventDefault();
  scrollHint.classList.add("hidden");

  if (e.deltaY > 0) flipTo((current + 1) % slides.length, "down");
  else flipTo((current - 1 + slides.length) % slides.length, "up");
}, { passive: false });

// Mobile touch support
let startY = 0;
flipBox.addEventListener("touchstart", (e) => (startY = e.touches[0].clientY));
flipBox.addEventListener("touchend", (e) => {
  const diff = startY - e.changedTouches[0].clientY;
  if (Math.abs(diff) > 50 && !isFlipping) {
    scrollHint.classList.add("hidden");
    if (diff > 0) flipTo((current + 1) % slides.length, "down");
    else flipTo((current - 1 + slides.length) % slides.length, "up");
  }
});

} else {
document.addEventListener("DOMContentLoaded", function () {
  const slides = document.querySelectorAll(".service-body");
  let current = 0;
  const total = slides.length;

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle("active", i === index);
    });
  }

  showSlide(current);

  setInterval(() => {
    slides[current].classList.remove("active");
    current = (current + 1) % total;
    slides[current].classList.add("active");
  }, 5000);
});
}
