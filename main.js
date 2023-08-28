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

const page_btn = document.getElementById('pages-btn');
const pages_list = document.getElementById('pages-list');
page_btn.addEventListener('click', () => {
  pages_list.classList.toggle('pages-list-show');
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
