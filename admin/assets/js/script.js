document.addEventListener("DOMContentLoaded", () => {
  // Placeholder untuk animasi tambahan atau interaksi
});

// Function to handle image preview on tambah produk page
function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function () {
    const preview = document.getElementById("previewGambar");
    preview.src = reader.result;
  };
  reader.readAsDataURL(event.target.files[0]);
}

// Function to handle image preview on edit produk page
function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function () {
    const preview = document.getElementById("previewGambar");
    preview.src = reader.result;
  };
  reader.readAsDataURL(event.target.files[0]);
}

// Function to handle confirmation modal for deleting products
function konfirmasiHapus(id) {
  const modal = document.getElementById("modalKonfirmasi");
  const btnHapus = document.getElementById("btnHapus");
  btnHapus.href = "hapus_produk.php?id=" + id;
  modal.classList.add("active");
}

function tutupModal() {
  const modal = document.getElementById("modalKonfirmasi");
  modal.classList.remove("active");
}
