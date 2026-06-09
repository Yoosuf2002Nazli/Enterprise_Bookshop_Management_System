/**
 * UI Enhancement Layer — ui.js
 * Stagger animations for stat cards, book covers, stock bars.
 * DO NOT merge into shared.js.
 */
document.addEventListener('DOMContentLoaded', function () {

  // Stagger fade-in for .stat-card
  const statCards = document.querySelectorAll('.stat-card');
  statCards.forEach(function(card, index) {
    card.style.opacity = '0';
    card.style.transform = 'translateY(12px)';
    setTimeout(function() {
      card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, index * 80);
  });

  // Fade-in for .book-card-cover
  const bookCovers = document.querySelectorAll('.book-card-cover');
  bookCovers.forEach(function(cover, index) {
    cover.style.opacity = '0';
    setTimeout(function() {
      cover.style.transition = 'opacity 0.5s ease';
      cover.style.opacity = '1';
    }, 50 + index * 60);
  });

  // Animate stock bars from 0 to target width
  const stockFills = document.querySelectorAll('.stock-bar-fill');
  stockFills.forEach(function(bar) {
    const targetWidth = bar.style.width;
    bar.style.width = '0%';
    setTimeout(function() {
      bar.style.width = targetWidth;
    }, 200);
  });

});
