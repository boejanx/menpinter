// resources/js/lazyload.js

export function initLazyImages() {
   const images = document.querySelectorAll("img[data-src]");
 
   const observer = new IntersectionObserver((entries, obs) => {
     entries.forEach((entry) => {
       if (entry.isIntersecting) {
         const img = entry.target;
         img.src = img.dataset.src;
         img.onload = () => img.classList.add("lazy-loaded");
         img.removeAttribute("data-src");
         obs.unobserve(img);
       }
     });
   }, {
     rootMargin: "100px",
     threshold: 0.1,
   });
 
   images.forEach((img) => observer.observe(img));
 }
 