// This script adds a scroll effect to the navbar
// It changes the background color when the user scrolls down
window.addEventListener("scroll", function () {
  const navbar = document.querySelector(".navbar");
  if (window.scrollY > 50) {
    navbar.classList.add("scrolled");
  } else {
    navbar.classList.remove("scrolled");
  }
});

// This script for backgroun on hero section
// it changes every 6 seconds
document.addEventListener("DOMContentLoaded", function () {
  const backgrounds = document.querySelectorAll(".hero-bg");
  let current = 0;

  setInterval(() => {
    backgrounds[current].classList.remove("active");
    current = (current + 1) % backgrounds.length;
    backgrounds[current].classList.add("active");
  }, 6000); // ganti setiap 6 detik
});

// This script handles the dynamic addition and removal of product items in a form
document.getElementById("tambah-produk").addEventListener("click", function () {
  const wrapper = document.getElementById("produk-wrapper");
  const firstItem = wrapper.querySelector(".produk-item");
  const clone = firstItem.cloneNode(true);

  // Reset value
  clone.querySelector("select").value = "";
  clone.querySelector("input").value = "";
  clone.querySelector(".remove-produk").classList.remove("d-none");

  wrapper.appendChild(clone);
});

document.addEventListener("click", function (e) {
  if (
    e.target.classList.contains("remove-produk") ||
    e.target.closest(".remove-produk")
  ) {
    const item = e.target.closest(".produk-item");
    item.remove();
  }
});

// This script handles pagination for the product list
// It shows 6 products per page and allows navigation through pages
document.addEventListener("DOMContentLoaded", function () {
  const itemsPerPage = 6;
  const cards = document.querySelectorAll(".produk-card");
  const totalItems = cards.length;
  const totalPages = Math.ceil(totalItems / itemsPerPage);

  function showPage(page) {
    cards.forEach((card, index) => {
      card.style.display =
        index >= (page - 1) * itemsPerPage && index < page * itemsPerPage
          ? "block"
          : "none";
    });

    // Update tombol aktif
    const buttons = document.querySelectorAll(".pagination .page-link");
    buttons.forEach((btn, i) => {
      if (parseInt(btn.dataset.page) === page) {
        btn.classList.add("active");
      } else {
        btn.classList.remove("active");
      }
    });
  }

  // Fungsi global agar bisa dipanggil dari HTML
  window.changePage = function (page) {
    showPage(page);
  };

  // Inisialisasi tampilan awal
  showPage(1);
});
