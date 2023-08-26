<?php

$db = new PDO("sqlite:money_tracking.db");
$host = gethostname();
date_default_timezone_set('Asia/Singapore');

$split = str_split($host);
foreach ($split as $str) {
  if ($str != '-' && $str != ' ') {
    $new_name[] = $str;
  }
}
$table_name = 'keuangan_' . implode('', $new_name);
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

$index = count($row);
$uang_bulanan = ($row ? $uang_bulanan = $row[$index - 1]['uang_bln'] : 0);

if (isset($_POST['uang_btn'])) {
  $uang_bulanan += (int)$_POST['uang_bulanan'];
  $tgl = date('d F Y');

  $stmt = $db->prepare("INSERT INTO $table_name (uang_bln, tgl) VALUES ('$uang_bulanan', '$tgl')");

  $stmt->execute();

  sleep('3');
  header($_SERVER['PHP_SELF']);
}

if (isset($_POST['tambah-data'])) {
  $pengeluaran = (int)$_POST['pengeluaran'];
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

$page_row = $db->query("SELECT DISTINCT tgl FROM $table_name WHERE pengeluaran IS NOT NULL ORDER BY tgl DESC");
$pages = $page_row->fetchAll(PDO::FETCH_ASSOC);

//konfigurasi paguination
$jum_data = 1;
$tot_data = count($pages);
$jum_hal = ceil($tot_data / $jum_data);
$hal_aktif = (isset($_GET['halaman']) ? $_GET['halaman'] : 1);
$awal_data = ($jum_data * $hal_aktif) - $jum_data;

//konfiurasi link
$jum_link = 1;
$startNumber = ($hal_aktif > $jum_link ? $hal_aktif - $jum_link : 1);
$endNumber = ($hal_aktif < $jum_hal - $jum_link ? $hal_aktif + $jum_link : $jum_hal);

$date_row = $db->query("SELECT DISTINCT tgl FROM $table_name WHERE pengeluaran IS NOT NULL ORDER BY tgl DESC LIMIT $awal_data, $jum_data");
$dates = $date_row->fetchAll(PDO::FETCH_ASSOC);

// konfigurasi nama link agar seusai tgl
$i = 1;
$date_pages = [];
while ($i <= count($pages)) {
  $c_date = $pages[count($pages) - $i]['tgl'];
  $c_date = explode(' ', $c_date);
  $c_date = array_diff($c_date, array(date('Y')));
  $date_pages[] = $c_date[0] . ' ' . $c_date[1];
  $i++;
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
</head>

<body>
  <div class="container">
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

      <form id="input-uang" class="form-input-uang" action="" method="post">
        <label for="uang-bulanan">Uang Bulanan</label>
        <input type="number" name="uang_bulanan" id="uang-bulanan" autofocus placeholder="Number Only!">
        <button type="submit" name="uang_btn">Submit</button>
        <div id="close-btn-input" class="close"><i class='bx bx-x'></i></div>
      </form>

      <form id="tambah-data" action="" method="post" class="input-data-pengeluaran">
        <div class="form-list">
          <label for="pengeluaran">Pengeluaran</label>
          <input type="number" name="pengeluaran" id="pengeluaran">
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
          <button type="submit" name="tambah-data">Tambah Pengeluaran</button>
        </div>

        <div id="close-btn-input_2" class="close"><i class='bx bx-x'></i></div>
      </form>

    </section>

    <?php foreach ($dates as $date) : ?>
      <p class="tgl"><?= $date = $date['tgl'] ?></p>
      <section class="list">

        <div class="table-header">
          <span class="pengeluaran">Pengeluaran</span>
          <span class="kategori">Kategori</span>
          <span class="keterangan">Keterangan</span>
        </div>

        <?php $values = $db->query("SELECT * FROM $table_name WHERE tgl = '$date' AND pengeluaran IS NOT NULL"); ?>
        <?php foreach ($values as $value) : ?>
          <div class="table-value">
            <span class="pengeluaran"><?= 'Rp.' . number_format($value['pengeluaran']) ?></span>
            <p class="kategori"><?= ucwords($value['kategori']) ?></p>
            <p class="keterangan"><?= $value['ket'] ?></p>
          </div>
        <?php endforeach; ?>
      </section>
    <?php endforeach; ?>

    <section class="halaman">
      <?php if ($hal_aktif > 1) : ?>

        <a class="arrow" href="?halaman=<?= $hal_aktif - 1 ?>"><i class='bx bx-chevron-left bx-lg'></i></a>

      <?php endif; ?>

      <?php for ($i = $startNumber; $i <= $endNumber; $i++) : ?>
        <?php if ($i == $hal_aktif) : ?>
          <a class="halaman-aktif" href="?halaman=<?= $i ?>""><?= $date_pages[count($pages) - $i] ?></a>
        <?php else : ?>
          <a href=" ?halaman=<?= $i ?>"><?= $date_pages[count($pages) - $i] ?></a>
        <?php endif; ?>
      <?php endfor; ?>

      <?php if ($hal_aktif < $jum_hal) : ?>

        <a class="arrow" href="?halaman=<?= $hal_aktif + 1 ?>"><i class='bx bx-chevron-right bx-lg'></i></a>

      <?php endif; ?>
    </section>
  </div>
  <script src="main.js"></script>
</body>

</html>