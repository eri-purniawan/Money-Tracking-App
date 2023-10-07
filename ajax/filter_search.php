<?php

session_start();

$user_id = $_SESSION['user_id'];

require "../connect.php";

$keyword = check($_GET['keyword']);
$keyword_bln = check(date('Y-m', strtotime($_GET['keyword_bln'])));

function check($data)
{
  return $data == "All" || $data == "1970-01" ? $data = "" : $data;
}

$date_row = $conn->query("SELECT DISTINCT tgl FROM keuangan WHERE pengeluaran IS NOT NULL AND user_id = $user_id AND kategori LIKE '%$keyword%' AND tgl LIKE '%$keyword_bln%' ORDER BY tgl DESC");
$dates = $date_row->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if ($date_row->rowCount() === 0) : ?>
  <div class="zero-data-container">
    <a href="https://storyset.com/data"><img src="img/nodata.png" class="no-data-img" alt="nodata"></a>
    <p class="zero-data">Data Tidak Ditemukan</p>
  </div>
<?php endif; ?>

<?php foreach ($dates as $date) : ?>
  <section class="list">
    <p class="tgl"><?= "<i class='bx bx-calendar'></i> " . date("d F Y", strtotime($date['tgl'])) ?></p>
    <div class="table-header">
      <span class="pengeluaran">Pengeluaran</span>
      <span class="kategori">Kategori</span>
      <span class="keterangan">Keterangan</span>
    </div>

    <?php $date = $date['tgl'] ?>
    <?php $values = $conn->query("SELECT * FROM keuangan WHERE tgl = '$date' AND pengeluaran IS NOT NULL AND user_id = $user_id AND kategori LIKE '%$keyword%'");
    $row_values = $values->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <?php foreach ($row_values as $value) : ?>
      <div class="table-value">
        <span class="pengeluaran"><?= 'Rp.' . number_format($value['pengeluaran']) ?></span>
        <p class="kategori"><?= ucwords($value['kategori']) ?></p>
        <p class="keterangan"><?= $value['ket'] ?></p>
      </div>
    <?php endforeach; ?>
    <p class="total-pengeluaran">
      <?php
      $total_pengeluaran = 0;
      foreach ($row_values as $value) {
        $total_pengeluaran += $value['pengeluaran'];
      }
      echo 'Total Pengeluaran: Rp.' . number_format($total_pengeluaran)
      ?>
    </p>
  </section>
<?php endforeach; ?>