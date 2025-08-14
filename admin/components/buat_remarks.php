<?php
// Simulasi ID Order (bisa diambil dari parameter GET atau POST)
$order_id = $_GET['id_order'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Buat Remarks</title>
    <script>
        function tambahField() {
            const container = document.getElementById("remarks-container");
            const field = document.createElement("div");
            field.className = "flex items-center gap-2 mb-2";

            field.innerHTML = `
        <input type="text" name="remarks[]" class="border p-2 rounded w-full" placeholder="Isi remark">
        <button type="button" onclick="hapusField(this)" class="text-red-500 font-bold">Hapus</button>
      `;
            container.appendChild(field);
        }

        function hapusField(button) {
            button.parentElement.remove();
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-xl">
        <h2 class="text-2xl font-bold mb-4">Buat Remarks untuk Order #<?= htmlspecialchars($order_id) ?></h2>

        <form action="../../backend/admin/proses_remarks.php" method="post">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">

            <div id="remarks-container">
                <div class="flex items-center gap-2 mb-2">
                    <input type="text" name="remarks[]" class="border p-2 rounded w-full" placeholder="Isi remark">
                </div>
            </div>

            <button type="button" onclick="tambahField()" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">+ Tambah Field</button>

            <div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan Remarks</button>
            </div>
        </form>
    </div>
</body>

</html>