<?php

require "connect.php";

date_default_timezone_set('Asia/Singapore');
$stmt = $db->prepare("CREATE TABLE IF NOT EXISTS $table_name (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        uang_bln INTEGER(20),
                        tgl VARCHAR(20),
                        pengeluaran INTEGER(20),
                        kategori VARCHAR(20),
                        ket VARCHAR(300)
                        )");
$stmt->execute();

$stmt = $db->query("SELECT uang_bln FROM $table_name");
$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
$uang_bulanan = ($row ? $uang_bulanan = $row[count($row) - 1]['uang_bln'] : 0);

if (isset($_POST['uang_btn'])) {
  $uang_bulanan += intval(str_replace(',', '', $_POST['uang_bulanan']));
  $tgl = date('d F Y');

  $stmt = $db->prepare("INSERT INTO $table_name (uang_bln, tgl) VALUES ('$uang_bulanan', '$tgl')");

  $stmt->execute();

  sleep('3');
  header($_SERVER['PHP_SELF']);
}

if (isset($_POST['tambah-data'])) {
  $pengeluaran = intval(str_replace(',', '', $_POST['pengeluaran']));
  $kategori = $_POST['kategori'];
  $keterangan = $_POST['keterangan'];
  $uang_bulanan = $uang_bulanan - $pengeluaran;
  $tgl = date('d F Y');

  if ($uang_bulanan >= 0) {

    $stmt = $db->prepare("INSERT INTO $table_name (uang_bln, tgl, pengeluaran, kategori, ket) VALUES ('$uang_bulanan', '$tgl', '$pengeluaran', '$kategori', '$keterangan')");

    $stmt->execute();
    sleep('3');
    header($_SERVER['PHP_SELF']);
  } else {
    $uang_bulanan = $uang_bulanan + $pengeluaran;
    echo "inputan anda melebihi batas sisa uang bulanan";
  }
}

$hasil = $db->query("SELECT * FROM $table_name");
$baris = $hasil->fetchAll(PDO::FETCH_ASSOC);

$pengeluaran = ($baris ? $pengeluaran = $baris[0]['pengeluaran'] : 0);

//mengembalikan total nilai pengeluaran
foreach ($baris as $v) {
  $pengeluaran += $v['pengeluaran'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Money Tracking</title>
  <link rel="stylesheet" href="style.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://kit.fontawesome.com/3c30c2ec7b.js" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
  <div class="container">

    <!-- Navigaiton -->
    <nav id="nav">
      <h1 class="heading">Money Tracking</h1>
      <div id="btn-menu" class="btn-menu">
        <i class='bx bx-menu bx-flip-horizontal'></i>
      </div>
      <ul id="menu" class="menu">
        <li class="list-menu">Balance</li>
        <li class="list-menu">Statistic</li>
        <li class="list-menu">About</li>
      </ul>
    </nav>

    <!-- Balance -->
    <header>
      <section class="balance">
        <div class="bulanan">
          <h2>Sisa Uang Bulanan</h2>
          <p><?= date('F Y') ?></p>
          <p id="add-uang-btn" class="uang btn"><?= number_format($uang_bulanan) ?></p>
        </div>
        <div class="pengeluaran">
          <h2>Total Pengeluaran</h2>
          <p><?= date('F Y') ?></p>
          <p class="uang"><?= number_format($pengeluaran) ?></p>
        </div>
        <div id="btn-add" class="btn-add">
          <i class='bx bx-plus'></i>
        </div>

        <!-- form input balance -->
        <form id="input-uang" class="form-input-uang" action="" method="post">
          <label for="uang-bulanan">Uang Bulanan</label>
          <input type="text" name="uang_bulanan" id="uang-bulanan" autofocus placeholder="Number Only!">
          <button type="submit" name="uang_btn">Submit</button>
          <div id="close-btn-input" class="close"><i class='bx bx-x'></i></div>
        </form>

        <!-- form input pengeluaran -->
        <form id="tambah-data" action="" method="post" class="input-data-pengeluaran">
          <div class="form-list">
            <label for="pengeluaran">Pengeluaran</label>
            <input type="text" name="pengeluaran" id="pengeluaran">
          </div>
          <div class="form-list">
            <label for="kategori">Kategori</label>
            <select name="kategori" id="kategori">
              <option value="" selected disabled hidden>Pilih Kategori</option>
              <option value="makanan">Makanan</option>
              <option value="bensin">Bensin</option>
              <option value="service-motor">Service Motor</option>
              <option value="hobi">Hobi</option>
              <option value="holiday">Holiday</option>
              <option value="tagihan">Tagihan</option>
            </select>
          </div>
          <div class="form-list">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" cols="30" rows="3"></textarea>
          </div>
          <div class="form-list">
            <button type="submit" name="tambah-data"><i class='bx bx-plus bx-md'></i></button>
          </div>
          <div id="close-btn-input_2" class="close"><i class='bx bx-x'></i></div>
        </form>
      </section>
    </header>

    <main id="main">
      <div class="search">
        <input id="search" class="search-field" type="search" autofocus autocomplete="off">
        <i class='bx bx-search bx-md'></i>
      </div>


      <div id="data-container"></div>

    </main>

  </div>
  <script src="main.js"></script>
</body>

</html>