const btn_menu = document.getElementById('btn-menu');
const nav = document.getElementById('nav');
const menu = document.getElementById('menu');
btn_menu.addEventListener('click', () => {
  nav.classList.toggle('height');
  menu.classList.toggle('menu-show');
});

const add_uang_btn = document.getElementById('add-uang-btn');
const form_input_uang = document.getElementById('input-uang');
add_uang_btn.addEventListener('click', () => {
  form_input_uang.classList.add('show-window');
  tambah_data.classList.remove('tampil-form');
  balance.classList.remove('height-balance');
});

const btn_add = document.getElementById('btn-add');
const tambah_data = document.getElementById('tambah-data');
const balance = document.getElementById('balance');
btn_add.addEventListener('click', () => {
  tambah_data.classList.add('tampil-form');
  balance.classList.add('height-balance');
});

const close_btn_input_2 = document.getElementById('close-btn-input_2');
close_btn_input_2.addEventListener('click', () => {
  tambah_data.classList.remove('tampil-form');
  balance.classList.remove('height-balance');
});

const close_btn_input = document.getElementById('close-btn-input');
close_btn_input.addEventListener('click', () => {
  form_input_uang.classList.remove('show-window');
});

document.getElementById('tgl').valueAsDate = new Date();

let uang_bln = document.getElementById('uang-bulanan');
let uang_btn = document.getElementById('uang_btn');

uang_btn.addEventListener('click', () => {
  if (uang_bln.value === '') {
    uang_btn.disabled = true;
    inputDisable(uang_bln);
  }
});

uang_bln.addEventListener('keyup', () => {
  uang_bln.value = number_format(uang_bln.value);

  if (uang_bln.value === '') {
    inputDisable(uang_bln);
  } else {
    uang_btn.disabled = false;
    inputEnable(uang_bln);
  }
});

const pengeluaran = document.getElementById('pengeluaran');
const kategori = document.getElementById('kategori');
const ket = document.getElementById('keterangan');
const add_btn = document.getElementById('add-btn');
const error = document.getElementById('error');
const spend = document.getElementById('spend');
const tglVal = document.getElementById('tgl');
let sisa_value = add_uang_btn.innerText;
let patern = /[^,\d]/gi;
let num_bul = parseInt(sisa_value.replace(patern, ''));

function getVal(val) {
  let spend_val = val.value;
  return (num_spend = parseInt(spend_val.replace(patern, '')));
}

pengeluaran.addEventListener('keyup', () => {
  getVal(pengeluaran);
  pengeluaran.value = number_format(pengeluaran.value);

  if (pengeluaran.value === '') {
    error.innerText = '';
    inputDisable(pengeluaran);
  } else if (num_bul < num_spend) {
    inputDisable(pengeluaran);
    error.innerHTML = '<div class="error">inputan anda melebihi batas sisa uang bulanan !!</div>';
    add_btn.disabled = true;
  } else {
    inputEnable(pengeluaran);
    error.innerText = '';
    add_btn.disabled = false;
  }
});

add_btn.addEventListener('click', () => {
  getVal(pengeluaran);

  if (pengeluaran.value === '') {
    inputDisable(pengeluaran);
    add_btn.disabled = true;
  }

  if (kategori.value === '') {
    inputDisable(kategori);
    add_btn.disabled = true;
  }

  if (ket.value === '') {
    inputDisable(ket);
    add_btn.disabled = true;
  }

  if (tglVal.innerText === '') {
    inputDisable(tglVal);
    add_btn.disabled = true;
  }

  if (num_bul < num_spend) {
    add_btn.disabled = true;
  }
});

kategori.addEventListener('click', () => {
  if (kategori.value === '') {
    inputDisable(kategori);
  } else {
    add_btn.disabled = false;
    inputEnable(kategori);
  }
});

tglVal.addEventListener('click', () => {
  if (tglVal.innerText === '') {
    inputDisable(tglVal);
  } else {
    add_btn.disabled = false;
    inputEnable(tglVal);
  }
});

ket.addEventListener('keyup', () => {
  if (ket.value == '') {
    inputDisable(ket);
  } else {
    add_btn.disabled = false;
    inputEnable(ket);
  }
});

function inputDisable(element) {
  element.style.outline = '2px solid var(--red)';
}

function inputEnable(element) {
  element.style.outline = '2px solid var(--blue)';
}

// function to change input value to curency format
function number_format(angka) {
  let number_string = angka.replace(/[^,\d]/g, '').toString(),
    split = number_string.split(','),
    sisa = split[0].length % 3,
    rupiah = split[0].substr(0, sisa),
    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

  // tambahkan titik jika yang di input sudah menjadi angka ribuan
  if (ribuan) {
    separator = sisa ? '.' : '';
    rupiah += separator + ribuan.join('.');
  }

  return (rupiah = split[1] != undefined ? rupiah + '.' + split[1] : rupiah);
}

let penampung = document.getElementById('data-container');
function ajaxHandler(data1, data2) {
  let xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      penampung.innerHTML = xhr.responseText;
    }
  };

  xhr.open('GET', 'ajax/filter_search.php?keyword=' + data1 + '&keyword_bln=' + data2, true);
  xhr.send();
}

function removeClass(element1, element2, className1, className2) {
  element1.classList.remove(className1);
  element2.classList.remove(className2);
}

function filter(text_field_, options_, option_, icon_, reset_, option_text_, reload) {
  const text_field = document.querySelector(text_field_),
    options = document.querySelector(options_),
    option = document.querySelectorAll(option_),
    icon = document.getElementById(icon_),
    reset = document.getElementById(reset_);

  text_field.addEventListener('click', () => {
    options.classList.toggle('active');
    icon.classList.toggle('rotate');
  });

  option.forEach((list) => {
    list.addEventListener('click', () => {
      let selected = list.querySelector(option_text_).innerText;
      removeClass(options, icon, 'active', 'rotate');
      text_field.innerText = selected;
    });
  });

  reset.addEventListener('click', () => {
    text_field.innerText = reload;
    removeClass(options, icon, 'active', 'rotate');
    loadDatas('#data-container', 'data.php');
  });
}

const cari = document.getElementById('cari');

if (parseInt(spend.innerText.replace(patern, '')) > 0) {
  filter('.text-field', '.options', '.option', 'icon', 'reset', '.option-text', 'Kategori');
  filter('.text-field-bln', '.options-bln', '.option-bln', 'icon-bln', 'reset', '.option-text-bln', 'Bulan');

  cari.addEventListener('click', () => {
    let val1 = document.querySelector('.text-field').innerText;
    let val2 = document.querySelector('.text-field-bln').innerText;

    ajaxHandler(val1, val2);
  });
}

//pagination with ajax jQueary
loadDatas('#data-container', 'data.php');
function loadDatas(element, url) {
  load_data();
  function load_data(page) {
    $.ajax({
      url: 'ajax/' + url,
      method: 'POST',
      data: { page: page },
      success: function (data) {
        $(element).html(data);
      },
    });
  }
  $(document).on('click', '.list-halaman', function () {
    var page = $(this).attr('id');
    load_data(page);
  });
}
