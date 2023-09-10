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
});

const btn_add = document.getElementById('btn-add');
const tambah_data = document.getElementById('tambah-data');
btn_add.addEventListener('click', () => {
  tambah_data.classList.add('tampil-form');
});

const close_btn_input_2 = document.getElementById('close-btn-input_2');
close_btn_input_2.addEventListener('click', () => {
  tambah_data.classList.remove('tampil-form');
});

const close_btn_input = document.getElementById('close-btn-input');
close_btn_input.addEventListener('click', () => {
  form_input_uang.classList.remove('show-window');
});

let uang_bln = document.getElementById('uang-bulanan');
uang_bln.addEventListener(
  'keyup',
  (event) => {
    number_only(event);
    number_format(uang_bln);
  },
  false
);

let pengeluaran = document.getElementById('pengeluaran');
pengeluaran.addEventListener(
  'keyup',
  (event) => {
    number_only(event);
    number_format(pengeluaran);
  },
  false
);

// function to change input value to curency format
function number_format(value) {
  let n = parseInt(value.value.replace(/\D/g, ''), 10);
  value.value = n.toLocaleString();
  if (value.value === 'NaN') {
    value.value = '';
  }
}

// function number only input
function number_only(event) {
  if (event.which < 48 || event.which > 57) {
    event.preventDefault();
  }
}

penampung = document.getElementById('data-container');
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

//filter with ajax
filter('.text-field', '.options', '.option', 'icon', 'reset', '.option-text', 'Kategori');
filter('.text-field-bln', '.options-bln', '.option-bln', 'icon-bln', 'reset', '.option-text-bln', 'Bulan');

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
cari.addEventListener('click', () => {
  let val1 = document.querySelector('.text-field').innerText;
  let val2 = document.querySelector('.text-field-bln').innerText;

  ajaxHandler(val1, val2);
});

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
