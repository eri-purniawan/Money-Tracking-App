const btn_menu = document.getElementById('btn-menu');
const nav = document.getElementById('nav');
const menu = document.getElementById('menu');
const add_uang_btn = document.getElementById('add-uang-btn');
const form_input_uang = document.getElementById('input-uang');
const btn_add = document.getElementById('btn-add');
const tambah_data = document.getElementById('tambah-data');
const close_btn_input = document.getElementById('close-btn-input');
const close_btn_input_2 = document.getElementById('close-btn-input_2');

btn_menu.addEventListener('click', () => {
  nav.classList.toggle('grid-areas');
  menu.classList.toggle('menu-show');
});

add_uang_btn.addEventListener('click', () => {
  form_input_uang.classList.add('show-window');
  tambah_data.classList.remove('tampil-form');
});

btn_add.addEventListener('click', () => {
  tambah_data.classList.add('tampil-form');
});

close_btn_input_2.addEventListener('click', () => {
  tambah_data.classList.remove('tampil-form');
});

close_btn_input.addEventListener('click', () => {
  form_input_uang.classList.remove('show-window');
});
