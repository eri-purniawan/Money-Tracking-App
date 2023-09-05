<?php

require "../connect.php";

session_start();

$keyword = $_GET['keyword'];

$date_row = $conn->query("SELECT DISTINCT tgl FROM $table_name WHERE pengeluaran IS NOT NULL AND kategori LIKE '%$keyword%' ORDER BY tgl DESC");
$dates = $date_row->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- tampil data pengeluaran -->
<div class="data">
  <?php foreach ($dates as $date) : ?>
    <p class="tgl"><?= "<i class='bx bx-calendar'></i> " . $date = $date['tgl'] ?></p>
    <section class="list">

      <div class="table-header">
        <span class="pengeluaran">Pengeluaran</span>
        <span class="kategori">Kategori</span>
        <span class="keterangan">Keterangan</span>
      </div>

      <?php $values = $conn->query("SELECT * FROM $table_name WHERE tgl = '$date' AND pengeluaran IS NOT NULL AND kategori LIKE '%$keyword%'");
      $row_values = $values->fetchAll(PDO::FETCH_ASSOC);
      ?>
      <?php foreach ($row_values as $value) : ?>
        <div class="table-value">
          <span class="pengeluaran"><?= 'Rp.' . number_format($value['pengeluaran']) ?></span>
          <p class="kategori"><?= ucwords($value['kategori']) ?></p>
          <p class="keterangan"><?= $value['ket'] ?></p>
        </div>
      <?php endforeach; ?>
    </section>

    <p class="tgl total-pengeluaran">
      <?php
      $total_pengeluaran = 0;
      foreach ($row_values as $value) {
        $total_pengeluaran += $value['pengeluaran'];
      }
      echo 'Total Pengeluaran: Rp.' . number_format($total_pengeluaran)
      ?>
    </p>
  <?php endforeach; ?>
</div>