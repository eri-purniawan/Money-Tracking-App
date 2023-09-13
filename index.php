<?php

require "connect.php";

$stmt = $conn->query("SELECT * FROM $table_name");
$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
$uang_bulanan = ($row ? $uang_bulanan = $row[count($row) - 1]['uang_bln'] : 0);

function reload()
{
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

if (isset($_POST['uang_btn'])) {
  $uang_bulanan += intval(str_replace(',', '', $_POST['uang_bulanan']));
  $tgl = date('d F Y');
  $stmt = $conn->query("INSERT INTO $table_name (uang_bln, tgl) VALUES ('$uang_bulanan', '$tgl')");
  reload();
}

function minchar($str)
{
  $patern = '/-/i';
  return (preg_match($patern, $str) ? preg_replace($patern, ' ', $str) : $str);
}

if (isset($_POST['tambah-data'])) {
  $pengeluaran = intval(str_replace(',', '', $_POST['pengeluaran']));
  $kategori = minchar($_POST['kategori']);
  $keterangan = $_POST['keterangan'];
  $uang_bulanan = $uang_bulanan - $pengeluaran;
  $tgl = date('d F Y');

  if ($uang_bulanan >= 0) {
    $stmt = $conn->query("INSERT INTO $table_name (uang_bln, tgl, pengeluaran, kategori, ket) VALUES ('$uang_bulanan', '$tgl', '$pengeluaran', '$kategori', '$keterangan')");
    reload();
  } else {
    $uang_bulanan = $uang_bulanan + $pengeluaran;
    echo "inputan anda melebihi batas sisa uang bulanan";
  }
}

$pengeluaran = 0;
$t_pengeluaran = 0;
$bulan_arr = [];
foreach ($row as $v) {
  $t_pengeluaran += $v['pengeluaran'];
  $bulan_tahun = explode(' ', $v['tgl'])[1] . ' ' . explode(' ', $v['tgl'])[2];

  $bulan_arr[] = $bulan_tahun;
}

$bulan_arr = array_unique($bulan_arr);

$bulan = ($row ? $bulan = $bulan_tahun : date('F Y'));

$bulan_lalu = date('F Y', time() - 60 * 60 * 24 * days_in_month());
$q_spend = $conn->query("SELECT pengeluaran FROM $table_name WHERE tgl LIKE '%$bulan%' ORDER BY id DESC");
$last_month_spend = $q_spend->fetchAll(PDO::FETCH_ASSOC);

foreach ($last_month_spend as $v) {
  $pengeluaran += $v['pengeluaran'];
}


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
  global $conn;
  global $table_name;

  $kategori = $conn->query("SELECT DISTINCT kategori FROM $table_name WHERE pengeluaran is NOT NULL");
  $kategori_row = $kategori->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < count($kategori_row); $i++) {
    $kat_name = $kategori_row[$i]['kategori'];
    $query = $conn->query("SELECT kategori, pengeluaran FROM $table_name WHERE kategori = '$kat_name' AND tgl LIKE '%$bulan%'");
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

function days_in_month()
{
  $month = date('m');
  $year = date('Y');
  if ($month == "02") {
    if ($year % 4 == 0) return 29;
    else return 28;
  } else if ($month == "01" || $month == "03" || $month == "05" || $month == "07" || $month == "08" || $month == "10" || $month == "12") return 31;
  else return 30;
}

$q = $conn->query("SELECT uang_bln, pengeluaran FROM $table_name WHERE tgl LIKE '%$bulan_lalu%' ORDER BY id DESC");
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

$max_row = $conn->query("SELECT * FROM $table_name WHERE pengeluaran = $max");
$max_result = $max_row->fetchAll(PDO::FETCH_ASSOC);

$list_kategori = [];
$list_spend = [];

foreach ($kategori as $v) {
  $list_kategori[] = ucwords($v['kategori']);
  $list_spend[] = $v['pengeluaran'];
}

$kategori_label = json_encode($list_kategori);
$spend_data = json_encode($list_spend);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Money Tracking</title>
  <link rel="stylesheet" href="style.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Victor+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/3c30c2ec7b.js" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          <li class="list-menu"><a href="#">Profile</a></li>
          <li class="list-menu"><a href="#sum">Summary</a></li>
          <li class="list-menu"><a href="#about">About</a></li>
        </ul>
      </div>
    </nav>

    <!-- Balance -->
    <section class="balance" id="#profile">
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
          <?php
          $kat_list = kategori(NULL, '');
          foreach ($kat_list as $v) : ?>
            <li class="option">
              <p class="option-text"><?= ucwords($v['kategori']) ?></p>
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


    <div id="data-container" class="no-data-list">
      <p class="tgl"><?= "<i class='bx bx-calendar'></i> " . 'Tgl...' ?></p>
      <section class="list">

        <div class="table-header">
          <p class="pengeluaran">Pengeluaran</p>
          <p class="kategori">Kategori</p>
          <p class="keterangan">Keterangan</p>
        </div>

        <div class="table-value">
          <p class="pengeluaran"><?= 'No data yet'  ?></p>
          <p class="kategori"><?= 'No data yet' ?></p>
          <p style="text-align: center;" class="keterangan"><?= 'No data yet' ?></p>
        </div>

      </section>
    </div>

    <section id="sum" class="summary">

      <?php if (in_array($bulan_lalu, $bulan_arr)) : ?>

        <h1 id="heading-sum" class="heading">Summary on <?= $bulan_lalu ?></h1>

        <div class="summary-container">

          <div class="sum-content">
            <h2 class="title">Total Pengeluaran</h2>
            <p class="sum-uang red rp"><?= number_format($p_bln_lalu) ?></p>
            <h2 class="title">Sisa Uang Bulanan</h2>
            <p class="sum-uang rp"><?= number_format($row[0]['uang_bln']) ?></p>
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
              <p><?= $max_result[0]['tgl'] ?></p>
              <p><?= $max_result[0]['kategori'] ?></p>
              <p class="red rp">Rp. <?= number_format($max_result[0]['pengeluaran']) ?></p>
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
                <p class="rp red bold"><?= number_format($v['pengeluaran']) ?></p>
              </div>

            <?php endforeach; ?>
          </div>

        </div>

        <canvas class="chart" id="myChart"></canvas>
      <?php else : ?>
        <h1 class="heading">Summary on ... </h1>
        <div class="no-data">
          <p>Data ringkasan pengeluaran bulan <?= $bulan ?> akan tersedia pada bulan berikutnya</p>
        </div>
      <?php endif; ?>
    </section>
  </div>

  <section class="about" id="about">
    <div class="container">
      <h1>About</h1>
      <p class="p">Aplikasi penelusuran pengeluaran pada keuangan berbasis web</p>
      <div class="email">
        <span>Email</span>
        <p>eriipurniawan@gmail.com</p>
      </div>
      <p>Made by EX for you</p>
    </div>
  </section>
  <script src="main.js"></script>
  <script>
    const kategori_label = <?= $kategori_label ?>;
    const spend_data = <?= $spend_data ?>;
    const summary = document.getElementById('heading-sum');
    const ctx = document.getElementById('myChart');

    Chart.defaults.font.family = "'Victor Mono', monospace";
    Chart.defaults.font.weight = 'bold';
    Chart.defaults.font.color = '#2E3440';

    function myFunction(x) {
      if (x.matches) { // If media query matches
        Chart.defaults.font.size = 14;
      } else {
        Chart.defaults.font.size = 20;
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
        scales: {
          // y: {
          //   beginAtZero: true
          // }
        }
      }
    });

    var x = window.matchMedia("(max-width: 576px)")
    myFunction(x) // Call listener function at run time
    x.addListener(myFunction) // Attach listener function on state changes
  </script>
</body>

</html>