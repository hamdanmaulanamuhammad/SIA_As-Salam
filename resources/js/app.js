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
import convert from 'heic-convert/browser';
import heic2any from 'heic2any';

// Membuat tersedia secara global
window.Swal = Swal;
window.Cropper = Cropper;
window.$ = window.jQuery = $;
window.html2pdf = html2pdf;
window.heicConvert = convert;
window.heic2any = heic2any;
