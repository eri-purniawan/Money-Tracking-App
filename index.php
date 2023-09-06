<?php

require "connect.php";

$stmt = $conn->query("SELECT uang_bln FROM $table_name");
$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
$uang_bulanan = ($row ? $uang_bulanan = $row[count($row) - 1]['uang_bln'] : 0);

if (isset($_POST['uang_btn'])) {
  $uang_bulanan += intval(str_replace(',', '', $_POST['uang_bulanan']));
  $tgl = date('d F Y');

  $stmt = $conn->query("INSERT INTO $table_name (uang_bln, tgl) VALUES ('$uang_bulanan', '$tgl')");

  // $stmt->execute();
  // sleep('1');
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

function minchar($str)
{
  $patern = '/-/i';
  if (preg_match($patern, $str)) {
    return preg_replace($patern, ' ', $str);
  } else {
    return $str;
  }
}

if (isset($_POST['tambah-data'])) {
  $pengeluaran = intval(str_replace(',', '', $_POST['pengeluaran']));
  $kategori = minchar($_POST['kategori']);
  $keterangan = $_POST['keterangan'];
  $uang_bulanan = $uang_bulanan - $pengeluaran;
  $tgl = date('d F Y');

  if ($uang_bulanan >= 0) {

    $stmt = $conn->query("INSERT INTO $table_name (uang_bln, tgl, pengeluaran, kategori, ket) VALUES ('$uang_bulanan', '$tgl', '$pengeluaran', '$kategori', '$keterangan')");

    // $stmt->execute();
    // sleep('1');
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  } else {
    $uang_bulanan = $uang_bulanan + $pengeluaran;
    echo "inputan anda melebihi batas sisa uang bulanan";
  }
}

$hasil = $conn->query("SELECT * FROM $table_name");
$baris = $hasil->fetchAll(PDO::FETCH_ASSOC);

$pengeluaran = ($baris ? $pengeluaran = $baris[0]['pengeluaran'] : 0);

//mengembalikan total nilai pengeluaran
foreach ($baris as $v) {
  $pengeluaran += $v['pengeluaran'];
  $bulan = $v['tgl'];
}

$array_tgl = explode(' ', $bulan)[0];
$array_bulan = explode(' ', $bulan);

$bulan = array_diff($array_bulan, array($array_tgl));
$bulan = $bulan[1] . " " . $bulan[2];

$months = array(
  'January',
  'February',
  'March',
  'April',
  'May',
  'June',
  'July ',
  'August',
  'September',
  'October',
  'November',
  'December',
);


// if ($bulan != date('F Y')) {
//   echo "total pengeluaran {$bulan} : " . number_format($pengeluaran);
// }
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
          <p><?= $bulan ?></p>
          <p id="add-uang-btn" class="uang btn"><?= number_format($uang_bulanan) ?></p>
        </div>
        <div class="pengeluaran">
          <h2>Total Pengeluaran</h2>
          <p><?= $bulan ?></p>
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

      <div class="select-menu">

        <div id="reset" class="reset-btn">
          <i class='bx bx-reset bx-sm'></i>
        </div>

        <div class="select-field">
          <span class="text-field">Kategori</span>
          <i id="icon" class='bx bx-caret-down bx-sm'></i>
        </div>

        <ul class="options">
          <li class="option">
            <p class="option-text">Makanan</p>
          </li>
          <li class="option">
            <p class="option-text">Bensin</p>
          </li>
          <li class="option">
            <p class="option-text">Service Motor</p>
          </li>
          <li class="option">
            <p class="option-text">Hobi</p>
          </li>
          <li class="option">
            <p class="option-text">Holiday</p>
          </li>
          <li class="option">
            <p class="option-text">Tagihan</p>
          </li>
        </ul>

        <div class="select-field">
          <span class="text-field-bln">Bulan</span>
          <i id="icon-bln" class='bx bx-caret-down bx-sm'></i>
        </div>

        <ul class="options-bln">
          <?php foreach ($months as $month) : ?>
            <li class="option-bln">
              <p class="option-text-bln"><?= $month ?></p>
            </li>
          <?php endforeach; ?>
        </ul>

        <div id="cari" class="cari">
          <i class='bx bx-search bx-sm'></i>
        </div>

      </div>

      <div id="data-container"></div>
    </main>

  </div>
  <script src="main.js"></script>
</body>

</html>