 // Flip effect on scroll inside container
  const flipContainer = document.querySelector('.flip-container');
  const flipInner = document.querySelector('.flip-card-inner');

  flipContainer.addEventListener('scroll', () => {
    const scrollTop = flipContainer.scrollTop;
    const maxScroll = flipContainer.scrollHeight - flipContainer.clientHeight;
    const rotateY = Math.min((scrollTop / maxScroll) * 180, 180);
    flipInner.style.transform = `rotateY(${rotateY}deg)`;
  });