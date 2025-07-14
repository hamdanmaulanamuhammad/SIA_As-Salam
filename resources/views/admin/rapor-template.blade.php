<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>E-Rapor Santri</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    body {
      font-family: 'Times New Roman', Times, serif;
      margin: 0;
      padding: 0;
    }
    .a4-container {
      width: 210mm; /* A4 width */
      min-height: 297mm; /* A4 height, set as min-height to allow content to fit */
      margin: 0 auto;
      background: white;
      box-sizing: border-box;
      padding: 10mm; /* Reduced padding to ensure content fits */
    }
    /* Ensure consistent rendering for print and PDF */
    @media print {
      body {
        margin: 0;
      }
      .a4-container {
        margin: 0;
        padding: 10mm;
        width: 210mm;
        height: 297mm;
        overflow: hidden; /* Prevent content from spilling over */
      }
    }
    /* Adjust table to prevent overflow */
    table {
      table-layout: fixed;
      width: 100%;
      font-size: 10px; /* Smaller font size to fit content */
    }
    th, td {
      word-wrap: break-word; /* Ensure text wraps to prevent overflow */
      padding: 2px; /* Reduce padding for better fit */
    }
    /* Reduce spacing in specific sections */
    .mt-6, .mb-3, .mt-3 {
      margin-top: 0.5rem !important;
      margin-bottom: 0.5rem !important;
    }
    .text-xs {
      font-size: 9px; /* Smaller font for better fit */
    }
    .h-36 {
      height: 80px; /* Reduce signature section height */
    }
  </style>
</head>
<body class="bg-white text-black font-serif">
  <div class="a4-container" id="rapor-content">
    <div class="flex items-center justify-center gap-x-6">
      <!-- Logo -->
      <div class="flex-shrink-0">
        <img src="https://placehold.co/70x30" alt="Logo" class="h-[70px]" />
      </div>

      <!-- Teks Judul -->
      <div class="text-center">
        <h1 class="text-xl font-bold uppercase">Taman Pendidikan Al-Quran</h1>
        <h2 class="text-xl font-bold uppercase">As-Salam Yogyakarta</h2>
      </div>
    </div>

    <!-- Info Kontak -->
    <div class="text-center text-xs mt-3">
      <p>Ngemplong Lor, Sariharjo, Ngaglik, Sleman, Daerah Istimewa Yogyakarta 55581</p>
      <p>
        Telp. +628517983482 |
        Email:
        <a href="mailto:info.tpaassalam@gmail.com" class="text-blue-600 underline">
          info.tpaassalam@gmail.com
        </a>
      </p>
    </div>

    <!-- Garis bawah -->
    <div class="border-t-4 border-black mt-2 relative">
      <div class="absolute top-1 w-full border-t border-black"></div>
    </div>

    <div class="mt-6 font-[Times New Roman] text-sm leading-relaxed">
      <!-- Info Santri -->
      <div class="mb-3">
        <p><span class="font-bold">Nama Santri:</span> <span id="nama-santri"></span></p>
        <p><span class="font-bold">NIS:</span> <span id="nis-santri"></span></p>
        <p><span class="font-bold">Mustawa:</span> <span id="mustawa-santri"></span></p>
      </div>

      <!-- Tabel Nilai -->
      <table class="w-full border border-black text-center table-fixed mb-6 text-xs">
        <thead class="bg-gray-200 font-bold">
          <tr>
            <th class="border border-black px-2 py-1 w-10">No</th>
            <th class="border border-black px-2 py-1">Bahan Ajar</th>
            <th class="border border-black px-2 py-1 w-16">Angka</th>
            <th class="border border-black px-2 py-1">Terbilang</th>
          </tr>
        </thead>
        <tbody id="nilai-table-body"></tbody>
      </table>

      <div class="w-full">
        <!-- Predikat -->
        <div class="mb-3">
          <p class="font-bold mb-1">Predikat :</p>
          <div class="border border-black p-1 text-center font-semibold" id="predikat"></div>
        </div>

        <!-- Keputusan -->
        <div class="mb-3">
          <div class="flex border-black pt-1">
            <div class="w-3/4 pr-3">
              <p><span class="font-bold">Keputusan:</span><br>
              Dengan memperhatikan hasil capaian kompetensi santri di atas, maka santri yang bersangkutan ditempatkan di:</p>
            </div>
            <div class="w-1/4 flex items-center justify-center border border-black font-bold text-center" id="mustawa-keputusan">
              MUSTAWA
            </div>
          </div>
        </div>

        <!-- Catatan -->
        <div class="mt-3">
          <p class="font-bold">Catatan:</p>
          <!-- Nama ustadzah -->
          <div class="border border-black p-1 italic mb-1" id="catatan-dari"></div>
          <!-- Pesan untuk Ananda -->
          <div class="border border-black p-1" id="catatan-pesan"></div>
        </div>
      </div>

      <!-- Tanda Tangan -->
      <div class="mt-6 grid grid-cols-3 gap-3 text-center text-xs">
        <!-- Wali Santri -->
        <div class="flex flex-col justify-between h-36">
          <div class="space-y-1">
            <p> </p> <!-- Dummy baris agar sejajar -->
            <p>Wali Santri,</p>
          </div>
          <p class="font-bold underline">............................</p>
        </div>

        <!-- Musyrifah Mustawa -->
        <div class="flex flex-col justify-between h-36">
          <div class="space-y-1">
            <p> </p> <!-- Dummy baris agar sejajar -->
            <p>Musyrifah Mustawa <span id="mustawa-ttd"></span>,</p>
          </div>
          <p class="font-bold underline" id="nama-wali-kelas"></p>
        </div>

        <!-- Mudir TPA -->
        <div class="flex flex-col justify-between h-36">
          <div class="space-y-1">
            <p>Mengetahui,</p>
            <p>Mudir TPA As-Salam,</p>
          </div>
          <p class="font-bold underline" id="nama-mudir"></p>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Ambil data dari localStorage
    const kelasList = JSON.parse(localStorage.getItem('kelasList')) || [];
    const kelasId = 1; // Hardcoded for demo; replace with dynamic value if needed
    const kelas = kelasList.find(k => k.id == kelasId);
    const santriList = JSON.parse(localStorage.getItem('santriList')) || [];
    const pathSegments = window.location.pathname.split('/');
    const santriId = parseInt(pathSegments[pathSegments.indexOf('santri') + 1]);
    const santri = santriList.find(s => s.id === santriId);

    // Fungsi untuk konversi angka ke terbilang
    function angkaKeTerbilang(angka) {
      const satuan = ['Nol', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
      const belasan = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
      const puluhan = [' ', ' ', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
      const ratusan = [' ', 'Seratus', 'Dua Ratus', 'Tiga Ratus', 'Empat Ratus', 'Lima Ratus', 'Enam Ratus', 'Tujuh Ratus', 'Delapan Ratus', 'Sembilan Ratus'];

      if (angka === 0) return satuan[0];
      if (angka < 10) return satuan[angka];
      if (angka < 20) return belasan[angka - 10];
      if (angka < 100) {
        const puluh = Math.floor(angka / 10);
        const sisa = angka % 10;
        return `${puluhan[puluh]}${sisa ? ' ' + satuan[sisa] : ''}`.trim();
      }
      if (angka < 1000) {
        const ratus = Math.floor(angka / 100);
        const sisa = angka % 100;
        return `${ratusan[ratus]}${sisa ? ' ' + angkaKeTerbilang(sisa) : ''}`.trim();
      }
      return angka.toString();
    }

    // Fungsi untuk menghitung predikat
    function hitungPredikat(rataRata) {
      if (rataRata >= 90) return 'MUMTAZ';
      if (rataRata >= 80) return 'JAYYID JIDDAN';
      if (rataRata >= 70) return 'JAYYID';
      return 'MAQBUL';
    }

    // Fungsi untuk merender tabel nilai
    function renderNilaiTable() {
      const tableBody = document.getElementById('nilai-table-body');
      tableBody.innerHTML = '';
      if (!kelas || !kelas.mata_pelajaran) {
        tableBody.innerHTML = '<tr><td colspan="4" class="px-2 py-1 text-center">Mata pelajaran belum diatur untuk kelas ini.</td></tr>';
        return;
      }

      const grouped = kelas.mata_pelajaran.reduce((acc, mp) => {
        if (!acc[mp.kategori]) acc[mp.kategori] = [];
        acc[mp.kategori].push(mp);
        return acc;
      }, {});
      let nomor = 1;
      let totalNilai = 0;
      let jumlahMataPelajaran = 0;

      for (const [kategori, mataPelajaran] of Object.entries(grouped)) {
        tableBody.innerHTML += `
          <tr class="bg-gray-100 font-semibold text-left">
            <td class="border border-black px-2 py-1 text-center" colspan="4">${kategori}</td>
          </tr>
        `;
        mataPelajaran.forEach(mp => {
          const nilai = santri.nilai[mp.nama] || 0;
          totalNilai += parseInt(nilai);
          if (nilai) jumlahMataPelajaran++;
          const row = `
            <tr>
              <td class="border border-black px-2 py-1">${nomor++}</td>
              <td class="border border-black px-2 py-1">${mp.nama}</td>
              <td class="border border-black px-2 py-1">${nilai || '-'}</td>
              <td class="border border-black px-2 py-1">${nilai ? angkaKeTerbilang(parseInt(nilai)) : '-'}</td>
            </tr>
          `;
          tableBody.innerHTML += row;
        });
      }

      const rataRata = jumlahMataPelajaran > 0 ? Math.round(totalNilai / jumlahMataPelajaran) : 0;
      tableBody.innerHTML += `
        <tr class="font-semibold">
          <td class="border border-black px-2 py-1 text-center" colspan="2">Jumlah Nilai</td>
          <td class="border border-black px-2 py-1">${totalNilai}</td>
          <td class="border border-black px-2 py-1">${angkaKeTerbilang(totalNilai)}</td>
        </tr>
        <tr class="font-semibold">
          <td class="border border-black px-2 py-1 text-center" colspan="2">Rata-rata</td>
          <td class="border border-black px-2 py-1">${rataRata}</td>
          <td class="border border-black px-2 py-1">${angkaKeTerbilang(rataRata)}</td>
        </tr>
      `;
      document.getElementById('predikat').textContent = hitungPredikat(rataRata);
    }

    // Fungsi untuk merender catatan
    function renderCatatan() {
      const catatanDari = document.getElementById('catatan-dari');
      const catatanPesan = document.getElementById('catatan-pesan');
      if (santri.catatan && santri.catatan_dari) {
        catatanDari.textContent = `Dari ${santri.catatan_dari}`;
        catatanPesan.textContent = santri.catatan;
      } else {
        catatanDari.textContent = 'Tidak ada catatan.';
        catatanPesan.textContent = '';
      }
    }

    // Render data saat halaman dimuat
    if (santri && kelas) {
      document.getElementById('nama-santri').textContent = santri.nama_santri;
      document.getElementById('nis-santri').textContent = santri.nis;
      document.getElementById('mustawa-santri').textContent = kelas.mustawa;
      document.getElementById('mustawa-keputusan').textContent = `MUSTAWA ${kelas.mustawa}`;
      document.getElementById('mustawa-ttd').textContent = kelas.mustawa;
      renderNilaiTable();
      renderCatatan();
      document.getElementById('nama-wali-kelas').textContent = kelas.wali_kelas || 'Belum diatur';
      document.getElementById('nama-mudir').textContent = kelas.mudir || 'Belum diatur';

      // Otomatis generate PDF untuk preview
      window.onload = function() {
        const element = document.getElementById('rapor-content');
        const opt = {
          margin: [10, 10, 10, 10], // 10mm margin on all sides
          filename: `Rapor_${santri.nama_santri}.pdf`,
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2, useCORS: true }, // Higher scale for better quality
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
          pagebreak: { mode: ['avoid-all', 'css', 'legacy'] } // Prevent content from splitting
        };
        html2pdf().from(element).set(opt).toPdf().get('pdf').then(function (pdf) {
          window.location.href = pdf.output('bloburl');
        });
      };
    } else {
      document.body.innerHTML = '<p class="text-center text-red-600">Santri atau kelas tidak ditemukan.</p>';
    }
  </script>
</body>
</html>
