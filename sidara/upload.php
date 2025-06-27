<?php
session_start();
include 'logoTitle.php';
include 'sesi.php';

if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] !== true) {
    // Redirect ke halaman home jika akses langsung
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload AC</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/upload.css">
  </head>
  <body class="container mt-4">
  <header class="d-flex align-items-center px-3">
    <div class="col-4 d-flex align-items-center">
        <div class="bg-container"></div>
        <span class="ms-2" style="font-size: 1rem;">Pengadilan Agama Kota Malang</span>
    </div>
    <div class="col-4 text-center">
        <h2 class="m-0 fw-bold">UPLOAD AKTA CERAI</h2>
    </div>
    <div class="col-4 text-end">
        <button class="btn btn-outline-secondary" onclick="window.location.href='home.php'">Back</button>
    </div>
</header>

    <div class="form-container">
      <form
        id="uploadForm"
        action="upload_data.php"
        method="POST"
        enctype="multipart/form-data"
        onsubmit="return validateForm(event)">
        
        <div class="row mb-3">
          <label for="no_ac" class="col-md-4 col-form-label">NOMOR AKTA CERAI <span class="star">*</span></label>
          <div class="col-md-8">
              <input type="number" id="no_ac" name="NO_AC" class="form-control" min="1" onkeydown="blockInvalidInput(event)" oninput="fixInvalidInput(this)">
          </div>
        </div>
        <div class="row mb-3">
          <label for="tanggal_ac_m" class="col-md-4 col-form-label">TANGGAL AKTA CERAI</label>
          <div class="col-md-8">
            <input
            type="date"
            id="tanggal_ac_m"
            name="TANGGAL_AC_M"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="no_perkara" class="col-md-4 col-form-label">NOMOR PERKARA <span class="star">*</span></label>
          <div class="col-md-8">
            <input
            type="number"
            id="no_perkara"
            name="NO_PERKARA"
            class="form-control"
            min="1" 
            onkeydown="blockInvalidInput(event)" 
            oninput="fixInvalidInput(this)">
          </div>
        </div>
        <div class="row mb-3">
          <label for="tahun_perkara" class="col-md-4 col-form-label">TAHUN PERKARA</label>
          <div class="col-md-8">
          <input
            type="number"
            id="tahun_perkara"
            name="TAHUN_PERKARA"
            pattern="\d{4}"
            title="Masukkan tahun dalam format YYYY (contoh: 2025)"
            class="form-control" 
            min="1" 
            onkeydown="blockInvalidInput(event)" 
            oninput="fixInvalidInput(this)">
          </div>
        </div>
        <div class="row mb-3">
          <label for="tanggal_putus" class="col-md-4 col-form-label">TANGGAL PUTUS</label>
          <div class="col-md-8">
          <input
            type="date"
            id="tanggal_putus"
            name="TANGGAL_PUTUS"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="nama_p" class="col-md-4 col-form-label">NAMA PENGGUGAT <span class="star">*</span></label>
          <div class="col-md-8">
          <input type="text" id="nama_p" name="NAMA_P" class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="umur_p" class="col-md-4 col-form-label">UMUR PENGGUGAT</label>
          <div class="col-md-8">
          <input type="number" id="umur_p" name="UMUR_P" class="form-control" min="1" onkeydown="blockInvalidInput(event)" oninput="fixInvalidInput(this)">
          </div>
        </div>
        <div class="row mb-3">
          <label for="binti_p" class="col-md-4 col-form-label">BINTI PENGGUGAT</label>
          <div class="col-md-8">
          <input type="text" id="binti_p" name="BINTI_P" class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="pekerjaan_p" class="col-md-4 col-form-label">PEKERJAAN PENGGUGAT</label>
          <div class="col-md-8">
          <input
            type="text"
            id="pekerjaan_p"
            name="PEKERJAAN_P"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="alamat_p" class="col-md-4 col-form-label">ALAMAT PENGGUGAT</label>
          <div class="col-md-8">
          <input
            type="text"
            id="alamat_p"
            name="ALAMAT_P"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="alamat_p_rt_rw" class="col-md-4 col-form-label">ALAMAT PENGGUGAT RT RW</label>
          <div class="col-md-8">
          <input
            type="text"
            id="alamat_p_rt_rw"
            name="ALAMAT_P_RT_RW"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="alamat_p_kelurahan" class="col-md-4 col-form-label">ALAMAT PENGGUGAT KELURAHAN</label>
          <div class="col-md-8">
          <input
            type="text"
            id="alamat_p_kelurahan"
            name="ALAMAT_P_KELURAHAN"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="kecamatan_p" class="col-md-4 col-form-label">KECAMATAN PENGGUGAT</label>
          <div class="col-md-8">
          <input
            type="text"
            id="kecamatan_p"
            name="KECAMATAN_P"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="kabupaten_kota_p" class="col-md-4 col-form-label">KABUPATEN KOTA PENGGUGAT</label>
          <div class="col-md-8">
          <input
            type="text"
            id="kabupaten_kota_p"
            name="KABUPATEN_KOTA_P"
            class="form-control">
          </div>
        </div> 
        <div class="row mb-3">
          <label for="nama_t" class="col-md-4 col-form-label">NAMA TERGUGAT <span class="star">*</span></label>
          <div class="col-md-8">
          <input type="text" id="nama_t" name="NAMA_T" class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="umur_t" class="col-md-4 col-form-label">UMUR TERGUGAT</label>
          <div class="col-md-8">
          <input type="number" id="umur_t" name="UMUR_T" class="form-control" min="1" onkeydown="blockInvalidInput(event)" oninput="fixInvalidInput(this)">
          </div>
        </div>
        <div class="row mb-3">
          <label for="bin_t" class="col-md-4 col-form-label">BIN TERGUGAT</label>
          <div class="col-md-8">
          <input type="text" id="bin_t" name="BIN_T" class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="pekerjaan_t" class="col-md-4 col-form-label">PEKERJAAN TERGUGAT</label>
          <div class="col-md-8">
          <input
            type="text"
            id="pekerjaan_t"
            name="PEKERJAAN_T"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="alamat_t" class="col-md-4 col-form-label">ALAMAT TERGUGAT</label>
          <div class="col-md-8">
          <input
            type="text"
            id="alamat_t"
            name="ALAMAT_T"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="alamat_t_rt_rw" class="col-md-4 col-form-label">ALAMAT TERGUGAT RT RW</label>
          <div class="col-md-8">
          <input
            type="text"
            id="alamat_t_rt_rw"
            name="ALAMAT_T_RT_RW"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="alamat_t_kelurahan" class="col-md-4 col-form-label">ALAMAT TERGUGAT KELURAHAN</label>
          <div class="col-md-8">
          <input
            type="text"
            id="alamat_t_kelurahan"
            name="ALAMAT_T_KELURAHAN"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="kecamatan_t" class="col-md-4 col-form-label">KECAMATAN TERGUGAT</label>
          <div class="col-md-8">
          <input
            type="text"
            id="kecamatan_t"
            name="KECAMATAN_T"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="kabupaten_kota_t" class="col-md-4 col-form-label">KABUPATEN KOTA TERGUGAT</label>
          <div class="col-md-8">
          <input
            type="text"
            id="kabupaten_kota_t"
            name="KABUPATEN_KOTA_T"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="kua_kecamatan" class="col-md-4 col-form-label">KUA KECAMATAN</label>
          <div class="col-md-8">
          <input
            type="text"
            id="kua_kecamatan"
            name="KUA_KECAMATAN"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="kabupaten_kota_kua" class="col-md-4 col-form-label">KABUPATEN KOTA KUA</label>
          <div class="col-md-8">
          <input
            type="text"
            id="kabupaten_kota_kua"
            name="KABUPATEN_KOTA_KUA"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="tanggal_nikah" class="col-md-4 col-form-label">TANGGAL NIKAH</label>
          <div class="col-md-8">
          <input
            type="date"
            id="tanggal_nikah"
            name="TANGGAL_NIKAH"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="no_aktanikah" class="col-md-4 col-form-label">NOMOR AKTA NIKAH</label>
          <div class="col-md-8">
          <input
            type="text"
            id="no_aktanikah"
            name="NO_AKTANIKAH"
            class="form-control">
          </div>
        </div>
        <div class="row mb-3">
          <label for="file" class="col-md-4 col-form-label">UPLOAD FILE</label>
          <div class="col-md-8">
          <input type="file" class="form-control" name="FILE" id="file" accept=".pdf">
          </div>
        </div>
        <div class="btn-center">
          <button type="submit" id="submitBtn" class="btn btn-primary">
            SUBMIT DATA
          </button>
        </div>
      </form>
    </div>
<script src="js/upload.js"></script>
  </body>
</html>
