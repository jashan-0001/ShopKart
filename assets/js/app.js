document.addEventListener('DOMContentLoaded', () => {
  // Small progressive enhancement: prevent accidental double-submit on order placement.
  document.querySelectorAll('form[action="checkout.php"]').forEach((form) => {
    form.addEventListener('submit', () => {
      const button = form.querySelector('button[type="submit"]');
      if (button) {
        button.disabled = true;
        button.textContent = 'Placing order…';
      }
    });
  });

  // Fade flash notices after a few seconds.
  const notice = document.querySelector('.notice');
  if (notice) {
    window.setTimeout(() => {
      notice.classList.add('notice-hide');
    }, 4500);
  }
});
