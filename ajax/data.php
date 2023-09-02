<?php
require "../connect.php";

date_default_timezone_set('Asia/Singapore');

$page_row = $db->query("SELECT DISTINCT tgl FROM $table_name WHERE pengeluaran IS NOT NULL ORDER BY tgl DESC");
$pages = $page_row->fetchAll(PDO::FETCH_ASSOC);

$jum_data = 1;
$tot_data = count($pages);
$jum_hal = ceil($tot_data / $jum_data);
$hal_aktif = (isset($_POST['page']) ? $_POST['page'] : 1);
$awal_data = ($jum_data * $hal_aktif) - $jum_data;

//konfiurasi link
$jum_link = 1;
$startNumber = ($hal_aktif > $jum_link ? $hal_aktif - $jum_link : 1);
$endNumber = ($hal_aktif < $jum_hal - $jum_link ? $hal_aktif + $jum_link : $jum_hal);

$date_row = $db->query("SELECT DISTINCT tgl FROM $table_name WHERE pengeluaran IS NOT NULL ORDER BY tgl DESC LIMIT $awal_data, $jum_data");
$dates = $date_row->fetchAll(PDO::FETCH_ASSOC);

// konfigurasi nama link agar seusai tgl
function datePages($index)
{
  global $pages;
  $i = 1;
  $date_pages = [];
  while ($i <= count($pages)) {
    $c_date = $pages[count($pages) - $i]['tgl'];
    $c_date = explode(' ', $c_date);
    $date_pages[] = $c_date[$index];
    $i++;
  }
  return $date_pages;
}

?>

<!-- tampil data pengeluaran -->
<?php foreach ($dates as $date) : ?>
  <p class="tgl"><?= "<i class='bx bx-calendar'></i> " . $date = $date['tgl'] ?></p>
  <section class="list">

    <div class="table-header">
      <span class="pengeluaran">Pengeluaran</span>
      <span class="kategori">Kategori</span>
      <span class="keterangan">Keterangan</span>
    </div>

    <?php $values = $db->query("SELECT * FROM $table_name WHERE tgl = '$date' AND pengeluaran IS NOT NULL");
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


<!-- pagination -->
<section class="halaman">
  <?php if ($hal_aktif > 1) : ?>

    <li class="arrow list-halaman" id="<?= $hal_aktif - 1 ?>"><i class='bx bx-chevron-left bx-md'></i></li>

  <?php endif; ?>

  <?php for ($i = $startNumber; $i <= $endNumber; $i++) : ?>
    <?php if ($i == $hal_aktif) : ?>
      <li class="halaman-aktif list-halaman" id="<?= $i ?>"><?= datePages(0)[count($pages) - $i] . ' ' . datePages(1)[0] ?></li>
    <?php else : ?>
      <li class="list-halaman" id="<?= $i ?>"><?= datePages(0)[count($pages) - $i] ?></li>
    <?php endif; ?>
  <?php endfor; ?>

  <?php if ($hal_aktif < $jum_hal) : ?>

    <li class="arrow list-halaman" id="<?= $hal_aktif + 1 ?>"><i class='bx bx-chevron-right bx-md'></i></li>

  <?php endif; ?>
</section>