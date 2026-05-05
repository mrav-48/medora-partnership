// Hero crossfade on mobile
(function() {
  var imgs = document.querySelectorAll('.hero-fade-img');
  if (!imgs.length) return;
  var current = 0;
  setInterval(function() {
    imgs[current].classList.remove('active');
    current = (current + 1) % imgs.length;
    imgs[current].classList.add('active');
  }, 4000);
})();

// Show nav CTA after scrolling past hero on mobile
(function() {
  var cta = document.querySelector('.nav-mobile-cta');
  if (!cta) return;
  var hero = document.querySelector('.hero');
  function onScroll() {
    if (!hero) return;
    var heroBottom = hero.getBoundingClientRect().bottom;
    if (heroBottom < 0) {
      cta.classList.add('visible');
    } else {
      cta.classList.remove('visible');
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
})();

// Venue tabs on mobile
(function() {
  var tabs = document.querySelectorAll('.venue-tab');
  var slides = document.querySelectorAll('.venue-slide');
  if (!tabs.length) return;

  tabs.forEach(function(tab) {
    tab.addEventListener('click', function() {
      var idx = parseInt(this.getAttribute('data-index'));
      tabs.forEach(function(t) { t.classList.remove('active'); });
      slides.forEach(function(s) { s.classList.remove('active'); });
      tabs[idx].classList.add('active');
      slides[idx].classList.add('active');
    });
  });

  // Swipe support
  var grid = document.getElementById('propertiesGrid');
  if (!grid) return;
  var startX = 0;
  grid.addEventListener('touchstart', function(e) {
    startX = e.touches[0].clientX;
  }, { passive: true });
  grid.addEventListener('touchend', function(e) {
    var diff = startX - e.changedTouches[0].clientX;
    if (Math.abs(diff) < 50) return;
    var current = 0;
    slides.forEach(function(s, i) { if (s.classList.contains('active')) current = i; });
    var next = diff > 0 ? Math.min(current + 1, slides.length - 1) : Math.max(current - 1, 0);
    if (next !== current) {
      tabs.forEach(function(t) { t.classList.remove('active'); });
      slides.forEach(function(s) { s.classList.remove('active'); });
      tabs[next].classList.add('active');
      slides[next].classList.add('active');
    }
  }, { passive: true });
})();
