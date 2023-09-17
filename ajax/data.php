<?php

session_start();

$user_id = $_SESSION['user_id'];


require "../connect.php";

$page_row = $conn->query("SELECT DISTINCT tgl FROM keuangan WHERE pengeluaran IS NOT NULL AND user_id = $user_id ORDER BY tgl DESC");
$pages = $page_row->fetchAll(PDO::FETCH_ASSOC);

$jum_data = 1;
$tot_data = count($pages);
$jum_hal = (count($pages) > 10 ? 10 : $jum_hal = ceil($tot_data / $jum_data));
$hal_aktif = (isset($_POST['page']) ? $_POST['page'] : 1);
$awal_data = ($jum_data * $hal_aktif) - $jum_data;

$date_row = $conn->query("SELECT DISTINCT tgl FROM keuangan WHERE pengeluaran IS NOT NULL AND user_id = $user_id ORDER BY id DESC LIMIT $awal_data, $jum_data");
$dates = $date_row->fetchAll(PDO::FETCH_ASSOC);

$jum_link = 1;
$startNumber = ($hal_aktif > $jum_link ? $hal_aktif - $jum_link : 1);
$endNumber = ($hal_aktif < $jum_hal - $jum_link ? $hal_aktif + $jum_link : $jum_hal);

?>

<!-- tampil data pengeluaran -->
<div class="data">
  <?php foreach ($dates as $date) : ?>
    <p class="tgl"><?= "<i class='bx bx-calendar'></i> " . $date = $date['tgl'] ?></p>
    <section class="list">

      <div class="table-header">
        <p class="pengeluaran">Pengeluaran</p>
        <p class="kategori">Kategori</p>
        <p class="keterangan">Keterangan</p>
      </div>

      <?php $values = $conn->query("SELECT * FROM keuangan WHERE tgl = '$date' AND pengeluaran IS NOT NULL AND user_id = $user_id");
      $row_values = $values->fetchAll(PDO::FETCH_ASSOC);
      ?>
      <?php foreach ($row_values as $value) : ?>
        <div class="table-value">
          <p class="pengeluaran"><?= 'Rp. ' . number_format($value['pengeluaran'], 0, '', '.') ?></p>
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
      echo 'Total Pengeluaran: Rp. ' . number_format($total_pengeluaran, 0, '', '.')
      ?>
    </p>
  <?php endforeach; ?>
</div>

<section class="halaman">
  <?php if ($hal_aktif > 1) : ?>

    <li class="arrow list-halaman" id="<?= $hal_aktif - 1 ?>"><i class='bx bx-chevron-left bx-md'></i></li>

  <?php endif; ?>

  <?php for ($i = $startNumber; $i <= $endNumber; $i++) : ?>
    <?php if ($i == $hal_aktif) : ?>
      <li class="halaman-aktif list-halaman" id="<?= $i ?>"><?= $i ?></li>
    <?php else : ?>
      <li class="list-halaman" id="<?= $i ?>"><?= $i  ?></li>
    <?php endif; ?>
  <?php endfor; ?>

  <?php if ($hal_aktif < $jum_hal) : ?>

    <li class="arrow list-halaman" id="<?= $hal_aktif + 1 ?>"><i class='bx bx-chevron-right bx-md'></i></li>

  <?php endif; ?>
</section>