/* ===== Auto Slide Transition Script ===== */
const slides = document.querySelectorAll(".slide");
let current = 0;

function showNextSlide() {
  const total = slides.length;
  const currentSlide = slides[current];
  const nextSlide = slides[(current + 1) % total];

  currentSlide.classList.remove("active");
  currentSlide.classList.add("exit");

  nextSlide.classList.remove("exit");
  nextSlide.classList.add("active");

  setTimeout(() => currentSlide.classList.remove("exit"), 1500);

  current = (current + 1) % total;
}

setInterval(showNextSlide, 5000);