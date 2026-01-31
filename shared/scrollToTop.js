const scrollToTopButton = document.querySelector('.scroll-top-btn');

window.addEventListener('scroll', () => {
   if (window.scrollY > 20) {
      scrollToTopButton.style.display = 'flex';
   } else {
      scrollToTopButton.style.display = 'none';
   }
});

// Scroll to the top when the user clicks the button
scrollToTopButton.addEventListener('click', () => {
   document.body.scrollTop = 0; // For Safari
   document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE, and Opera
});
