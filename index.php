<?php

session_start();

if (!isset($_SESSION['login'])) {
  header('Location: landPage.html');
  exit;
}

$user_id = $_SESSION['user_id'];

require "connect.php";

date_default_timezone_set('Asia/Singapore');

$query = $conn->query("SELECT user FROM users WHERE id = $user_id");
$user = $query->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("SELECT * FROM keuangan WHERE user_id = $user_id");
$row = $stmt->fetchAll(PDO::FETCH_ASSOC);

$uang_bulanan = ($row ? $uang_bulanan = $row[count($row) - 1]['uang_bln'] : 0);

function reload()
{
  header("Location: index.php ");
  exit;
}

if (isset($_POST['uang_btn'])) {
  $uang_bulanan += intval(str_replace('.', '', test_input($_POST['uang_bulanan'])));
  $tgl = str_replace('/', ' ', test_input($_POST['tgl']));

  $stmt = $conn->prepare("INSERT INTO keuangan (user_id, uang_bln, tgl) VALUES (?, ?, STR_TO_DATE('$tgl', '%d %m %Y'))");

  $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
  $stmt->bindParam(2, $uang_bulanan, PDO::PARAM_INT);

  $stmt->execute();
  reload();
}

if (isset($_POST['tambah-data'])) {
  $pengeluaran = intval(str_replace('.', '', test_input($_POST['pengeluaran'])));
  $kategori = str_replace('-', ' ', test_input($_POST['kategori']));
  $keterangan = test_input($_POST['keterangan']);
  $uang_bulanan = $uang_bulanan - $pengeluaran;
  $tgl = test_input($_POST['tgl']);

  $stmt = $conn->prepare("INSERT INTO keuangan (user_id, uang_bln, tgl, pengeluaran, kategori, ket) VALUES (?, ?, STR_TO_DATE( ? , '%d %M %Y'), ?, ?, ?)");

  $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
  $stmt->bindParam(2, $uang_bulanan, PDO::PARAM_INT);
  $stmt->bindParam(3, $tgl, PDO::PARAM_STR);
  $stmt->bindParam(4, $pengeluaran, PDO::PARAM_INT);
  $stmt->bindParam(5, $kategori, PDO::PARAM_STR);
  $stmt->bindParam(6, $keterangan, PDO::PARAM_STR);
  $stmt->execute();
  reload();
}

$t_pengeluaran = 0;
$bulan_arr = [];
foreach ($row as $v) {
  $t_pengeluaran += $v['pengeluaran'];
  $bulan = str_replace('-', ' ', $v['tgl']);

  $bulan = explode(" ", $bulan);
  array_splice($bulan, -1);
  $bulan = implode("-", $bulan);

  $bulan_arr[] = $bulan;
}

$bulan = ($row ? $bulan = $bulan : date('F Y'));
$bulan_arr = array_unique($bulan_arr);

if (count($bulan_arr) > 2) {
  array_splice($bulan_arr, 0, count($bulan_arr) - (count($bulan_arr) + 2));
}

$bulan_lalu = date('Y-m', time() - 60 * 60 * 24 * date("t", mktime(0, 0, 0, date("n") - 1)));
$q_spend = $conn->query("SELECT SUM(pengeluaran) AS pengeluaran FROM keuangan WHERE tgl LIKE '%$bulan%' AND user_id = $user_id");
$last_month_spend = $q_spend->fetchAll(PDO::FETCH_ASSOC);

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

function kategori($data, $bulan)
{
  global $user_id;
  global $conn;

  $kategori = $conn->query("SELECT DISTINCT kategori FROM keuangan WHERE pengeluaran is NOT NULL AND user_id = $user_id AND tgl LIKE '%$bulan%'");
  $kategori_row = $kategori->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < count($kategori_row); $i++) {
    $kat_name = $kategori_row[$i]['kategori'];
    $query = $conn->query("SELECT kategori, pengeluaran FROM keuangan WHERE kategori = '$kat_name' AND user_id = $user_id AND pengeluaran IS NOT NULL AND tgl LIKE '%$bulan%'");
    $query_row = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($kat_name == $data) {
      $result = 0;
      foreach ($query_row as $v) {
        $result += $v['pengeluaran'];
      }
      $count = count($query_row);
    }
  }
  if ($data == NULL) {
    return $kategori_row;
  }
  return array(
    "kategori" => $data,
    "total" => $count,
    "pengeluaran" => $result
  );
}

$q = $conn->query("SELECT uang_bln, pengeluaran FROM keuangan WHERE tgl LIKE '%$bulan_lalu%' AND user_id = $user_id ORDER BY id DESC");
$row = $q->fetchAll(PDO::FETCH_ASSOC);

$list = kategori(NULL, $bulan_lalu);
$kategori = [];
foreach ($list as $v) {
  $kategori[] = kategori($v['kategori'], $bulan_lalu);
}

$total = count($row) - 1;
$max = 0;
$p_bln_lalu = 0;

for ($i = 0; $i < $total; $i++) {
  $p_bln_lalu += $row[$i]['pengeluaran'];
}

for ($i = 0; $i < $total; $i++) {
  if ($row[$i]['pengeluaran'] > $row[$i + 1]['pengeluaran']) {
    $max = $row[$i]['pengeluaran'];
    $row[$i + 1]['pengeluaran'] = $row[$i]['pengeluaran'];
  }
}

$max_row = $conn->query("SELECT * FROM keuangan WHERE pengeluaran = $max AND user_id = $user_id");
$max_result = $max_row->fetchAll(PDO::FETCH_ASSOC);

$list_kategori = [];
$list_spend = [];

foreach ($kategori as $v) {
  $list_kategori[] = ucwords($v['kategori']);
  $list_spend[] = $v['pengeluaran'];
}

$list_tgl = [];
$x = 0;
$jumlah_tgl = 6;
while ($x >= -$jumlah_tgl) {
  $list_tgl[] = date('d F Y', time() + 60 * 60 * 24 * $x);
  $x--;
}

$daftarKategori = [
  "Konsumsi",
  "Bahan Bakar",
  "Kendaraan",
  "Holiday",
  "Tagihan",
  "Belanja",
  "Elektronik",
  "Hobi",
  "Hiburan",
  "Perawatan",
  "Kesehatan",
  "Pakaian",
  "Pendidikan",
  "Transportasi",
  "Sedekah"
]

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="description" content="Aplikasi penelusuran pengeluaran pada keuangan berbasis web">
  <meta name="keywords" content="Uang, kemana, aplikasi">
  <meta name="author" content="Eri Purniawan">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KemanaUangku?</title>
  <link rel="stylesheet" href="css/main.css?v=1">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Victor+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <?php if (in_array($bulan_lalu, $bulan_arr)) : ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <?php endif; ?>
  <link rel="icon" type="image/png" href="img/money_5776691.png" />
</head>

<body>
  <div class="container">

    <!-- Navigaiton -->
    <nav>
      <div id="nav" class="nav-container">
        <h1 class="heading"><a href="#">KemanaUangku?</a></h1>
        <div id="btn-menu" class="btn-menu">
          <i class='bx bx-menu bx-flip-horizontal'></i>
        </div>
        <ul id="menu" class="menu">
          <li><a href="#">Profile</a></li>
          <li><a href="#sum">Summary</a></li>
          <li><a href="#about">About</a></li>
        </ul>
      </div>
    </nav>

    <!-- Balance -->
    <section class="profile">
      <span> Wellcome, <?= preg_replace('/[_\d]/mi', ' ', ucwords($user[0]['user'])) ?></span>
      <a href="logout.php"> Logout</a>
    </section>

    <section class="balance" id="balance">
      <div class="bulanan">
        <h2>Sisa Uang Bulanan</h2>
        <p><?= substr(strstr(date("d F Y", strtotime($bulan)), " "), 1) ?></p>
        <p id="add-uang-btn" class="uang btn"><?= number_format($uang_bulanan, 0, '', '.') ?></p>
      </div>
      <div class="pengeluaran">
        <h2>Total Pengeluaran</h2>
        <p><?= substr(strstr(date("d F Y", strtotime($bulan)), " "), 1) ?></p>
        <p id="spend" class="uang"><?= number_format($last_month_spend[0]['pengeluaran'], 0, '', '.') ?></p>
      </div>
      <div id="btn-add" class="btn-add">
        <i class='bx bx-window-open bx-md'></i>
      </div>

      <!-- form input balance -->
      <form id="input-uang" class="form-input-uang" action="" method="post">
        <input type="hidden" name="tgl" value="<?= date('d/m/Y') ?>">
        <label for="uang-bulanan">Uang Bulanan</label>
        <input type="text" name="uang_bulanan" id="uang-bulanan" autofocus placeholder="Number Only!">
        <button id="uang_btn" type="submit" name="uang_btn"><i class='bx bx-plus bx-md'></i>Tambah Uang Bulanan</button>
        <div id="close-btn-input" class="close"><i class='bx bx-x'></i></div>
      </form>

      <!-- form input pengeluaran -->
      <form id="tambah-data" action="" method="post" class="input-data-pengeluaran">
        <div id="error"></div>
        <div class="form-list">
          <label for="tgl">Tgl</label>
          <select name="tgl" id="tgl">
            <?php foreach ($list_tgl as $v) : ?>
              <option value="<?= $v ?>"><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-list">
          <label for="pengeluaran">Pengeluaran</label>
          <input type="text" name="pengeluaran" id="pengeluaran">
        </div>
        <div class="form-list">
          <label for="kategori">Kategori</label>
          <select name="kategori" id="kategori">
            <option value="" selected disabled hidden>Pilih Kategori</option>
            <?php foreach ($daftarKategori as $v) : ?>
              <option value="<?= $v ?>"><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-list">
          <label for="keterangan">Keterangan</label>
          <textarea name="keterangan" id="keterangan" cols="30" rows="3"></textarea>
        </div>
        <div class="form-list">
          <button type="submit" name="tambah-data" id="add-btn" value="submit"><i class='bx bx-plus bx-md'></i> Tambah Pengeluaran</button>
        </div>
        <div id="close-btn-input_2" class="close"><i class='bx bx-x'></i></div>
      </form>
    </section>

    <?php if ($t_pengeluaran > 0) : ?>
      <div class="select-menu">

        <div id="reset" class="reset-btn">
          <i class='bx bx-reset bx-xs'></i>
        </div>

        <div class="select-field">
          <span class="text-field">Kategori</span>
          <i id="icon" class='bx bx-caret-down bx-xs'></i>
        </div>

        <ul class="options">
          <li class="option">
            <p class="option-text">All</p>
          </li>
          <?php foreach ($daftarKategori as $v) : ?>
            <li class="option">
              <p class="option-text"><?= ucwords($v) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="select-field">
          <span class="text-field-bln">Bulan</span>
          <i id="icon-bln" class='bx bx-caret-down bx-xs'></i>
        </div>

        <ul class="options-bln">
          <li class="option-bln">
            <p class="option-text-bln">All</p>
          </li>
          <?php foreach ($months as $month) : ?>
            <li class="option-bln">
              <p class="option-text-bln"><?= $month . ' ' . date('Y') ?></p>
            </li>
          <?php endforeach; ?>
        </ul>

        <div id="cari" class="cari">
          <i class='bx bx-search bx-xs'></i>
        </div>

      </div>
    <?php endif; ?>

    <div id="data-container" class="no-data-list"></div>

    <section id="sum" class="summary">

      <?php if (in_array($bulan_lalu, $bulan_arr)) : ?>

        <h1 id="heading-sum" class="heading">Summary on <?= substr(strstr(date("d F Y", strtotime($bulan_lalu)), " "), 1) ?></h1>

        <div class="summary-container">

          <div class="sum-content">
            <h2 class="title">Total Pengeluaran</h2>
            <p class="sum-uang red rp"><?= number_format($p_bln_lalu, 0, '', '.') ?></p>
            <h2 class="title">Sisa Uang Bulanan</h2>
            <p class="sum-uang rp"><?= number_format($row[0]['uang_bln'], 0, '', '.') ?></p>
          </div>

          <div class="wraper">
            <h2 class="title">Pengeluaran Terbanyak</h2>

            <div class="table header">
              <p class="title-list">Tanggal</p>
              <p class="title-list">kategori</p>
              <p class="title-list">Pengeluaran</p>
              <p class="title-list">Keterangan</p>
            </div>

            <div class="table value after">
              <p><?= date("d F Y", strtotime($max_result[0]['tgl'])) ?></p>
              <p><?= $max_result[0]['kategori'] ?></p>
              <p class="red rp">Rp. <?= number_format($max_result[0]['pengeluaran'], 0, '', '.') ?></p>
              <p><?= $max_result[0]['ket'] ?></p>
            </div>

          </div>

          <div class="sum-content-detail">
            <h2 class="title-detail">Detail Pengeluaran Per Kategori</h2>
            <div class="table-header">
              <p class="title-list sum-kategori">Kategori</p>
              <p class="title-list sum-kategori">Jumlah Pengeluaran</p>
              <p class="title-list sum-kategori">Total Pengeluaran</p>
            </div>

            <?php foreach ($kategori as $v) : ?>

              <div class="table-value">
                <p><?= ucwords($v['kategori']) ?></p>
                <p><?= $v['total'] ?> Kali</p>
                <p class="rp red bold"><?= number_format($v['pengeluaran'], 0, '', '.') ?></p>
              </div>

            <?php endforeach; ?>
          </div>
        </div>
        <canvas class="chart" id="myChart"></canvas>
      <?php else : ?>
        <h1 id="heading-sum" class="heading">Summary on ... </h1>
        <div class="no-data">
          <p>Data ringkasan pengeluaran bulan <?= date('F Y') ?> akan tersedia pada bulan berikutnya</p>
        </div>
      <?php endif; ?>
    </section>

    <section class="about" id="about">
      <h1>About</h1>
      <p>Aplikasi penelusuran pengeluaran pada keuangan berbasis web</p>
      <div class="email">
        <span>Email</span>
        <p>eriipurniawan@gmail.com</p>
      </div>
      <span>Made with <i class='bx bxs-heart-circle bx-sm red'></i> by EX </span>
    </section>
  </div>

  <script src="js/main.js"></script>
  <script>
    const summary = document.getElementById('heading-sum');

    if (summary.innerText !== 'Summary on ...') {
      const kategori_label = <?= json_encode($list_kategori) ?>;
      const spend_data = <?= json_encode($list_spend) ?>;
      const ctx = document.getElementById('myChart');

      Chart.defaults.font.family = "'Victor Mono', monospace";
      Chart.defaults.font.weight = 'bold';
      Chart.defaults.font.color = '#2E3440';

      function myFunction(x) {
        if (x.matches) { // If media query matches
          Chart.defaults.font.size = 12;
        } else {
          Chart.defaults.font.size = 16;
        }
      }

      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: kategori_label,
          datasets: [{
            label: 'Total Pengeluaran',
            data: spend_data,
            borderWidth: 2,
            backgroundColor: [
              '#EDAE49',
              '#D1495B',
              '#00798c',
              '#30638e',
              '#003d5b',
              '#5c425b',
              '#473335',
              '#495867',
            ],
            borderColor: '#fff',
            color: '#00070a'
          }]
        },
        options: {
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: '#00070a',
              }
            },
            title: {
              display: true,
              text: 'Chart ' + summary.innerText,
              color: '#00070a'
            }
          },
        }
      });
      let x = window.matchMedia("(max-width: 576px)")
      myFunction(x)
      x.addListener(myFunction)
    }
  </script>
</body>

</html>