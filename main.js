const btn_menu = document.getElementById('btn-menu');
const nav = document.getElementById('nav');
const menu = document.getElementById('menu');
btn_menu.addEventListener('click', () => {
  nav.classList.toggle('grid-areas');
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

function ajaxHandler(value) {
  let xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      penampung.innerHTML = xhr.responseText;
    }
  };

  xhr.open('GET', 'ajax/filter_search.php?keyword=' + value, true);
  xhr.send();
}

//search filter with ajax
const text_field = document.querySelector('.text-field'),
  options = document.querySelector('.options'),
  option = document.querySelectorAll('.option'),
  select_field = document.querySelector('.select-field'),
  icon = document.getElementById('icon'),
  reset = document.getElementById('reset');

text_field.addEventListener('click', () => {
  options.classList.toggle('active');
  icon.classList.toggle('rotate');
});

option.forEach((list) => {
  list.addEventListener('click', () => {
    let selected = list.querySelector('.option-text').innerText;
    options.classList.remove('active');
    icon.classList.remove('rotate');
    reset.classList.add('show');

    let value = (text_field.innerText = selected);

    ajaxHandler(value);
  });
});

reset.addEventListener('click', () => {
  text_field.innerText = 'Pilih Kategori';
  reset.classList.remove('show');
  loadDatas('#data-container', 'data.php');
});

//pagination ajax with jquery
loadDatas('#data-container', 'data.php');
// loadDatas('#page', 'pageData.php');

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
