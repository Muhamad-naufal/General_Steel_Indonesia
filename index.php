<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PT GENERAL STEEL INDONESIA</title>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- optional -->
    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="assets/logo.png" type="image/png">
</head>

<body>

    <?php include 'components/navbar.php'; ?>

    <?php include 'components/hero.php'; ?>

    <?php include 'components/about.php'; ?>

    <?php include 'components/layanan.php'; ?>

    <?php include 'components/produk.php'; ?>

    <?php include 'components/cta.php'; ?>

    <?php include 'components/footer.php'; ?>


    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true, // animasi hanya terjadi sekali
            duration: 800, // durasi animasi (ms)
        });
    </script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>

</html>