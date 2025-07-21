import './bootstrap';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import html2pdf from 'html2pdf.js';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import $ from 'jquery';
import '@fortawesome/fontawesome-free/css/all.min.css';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.min.css';

// Membuat Swal tersedia secara global
window.Swal = Swal;
window.Cropper = Cropper;
// Membuat jQuery tersedia secara global
window.$ = window.jQuery = $;
// Membuat html2pdf tersedia secara global
window.html2pdf = html2pdf;
