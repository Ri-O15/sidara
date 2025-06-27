function blockInvalidInput(event) {
    if (event.key === '-' || event.keyCode === 189 || (event.target.value.length === 0 && event.key === "0")) {
        event.preventDefault(); // Mencegah input tanda minus dan angka nol
    }
}

function fixInvalidInput(input) {
    // Hapus tanda minus jika ada
    input.value = input.value.replace('-', '');

    // Cegah angka 0 atau nilai kosong
    if (input.value.length > 1 && input.value.startsWith("0")) {
        input.value = input.value.replace(/^0+/, '');
    }
}

function resetFileInput() {
      document.getElementById("editFile").value = "";
  }

  // Contoh: Jika input file ada dalam modal
  document.getElementById("editModal").addEventListener("show.bs.modal", function() {
      resetFileInput();
  });

document.getElementById("editFile").addEventListener("change", function() {
    const file = this.files[0];
    if (file) {
      const fileType = file.type;
      if (fileType !== "application/pdf") {
        Swal.fire({
          icon: "error",
          title: "Format Tidak Diterima",
          text: "Hanya file PDF yang diperbolehkan!",
          confirmButtonColor: "#d33"
        });
        this.value = ""; // Reset input file
      }
    }
  });

document.addEventListener("DOMContentLoaded", function () {
    let formFields = document.querySelectorAll("#editForm input");

    formFields.forEach(field => {
        if (!field.value.trim()) {
            field.placeholder = "Tidak tersedia";
        }
    });
});


function printData() {
    let bulan = document.getElementById('printBulan').value;
    let tahun = document.getElementById('printTahun').value;
    
    if (!tahun) {
        Swal.fire({
            icon: 'warning',
            title: 'Tahun Belum Dipilih!',
            text: 'Silakan pilih tahun sebelum mencetak.',
            confirmButtonText: 'OK',
            timer: 3000
        });
        return;
    }
    
    let xhr = new XMLHttpRequest();
    xhr.open("GET", `print.php?bulan=${bulan}&tahun=${tahun}`, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let printFrame = document.getElementById("printFrame").contentWindow;
            printFrame.document.open();
            printFrame.document.write(xhr.responseText);
            printFrame.document.close();
            printFrame.focus();
            printFrame.print();
        }
    };
    xhr.send();
}
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get("search") || "";
        const filterParam = urlParams.get("filter") || "all";
        const bulanParam = urlParams.get("bulan") || "";
        const tahunParam = urlParams.get("tahun") || "";

        document.getElementById("searchInput").value = searchParam;
        document.getElementById("filterSelect").value = filterParam;
        document.getElementById("bulanSelect").value = bulanParam;
        document.getElementById("tahunSelect").value = tahunParam;
    });

    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        searchInput.focus();
    });

    function filterAndSearch() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let filter = document.getElementById("filterSelect").value;
        let bulan = document.getElementById("bulanSelect").value;
        let tahun = document.getElementById("tahunSelect").value;
        let rows = document.querySelectorAll("#dataBody tr");

        rows.forEach(row => {
            let columns = row.getElementsByTagName("td");
            let empty = false;
            let found = false;

            for (let i = 0; i < columns.length - 1; i++) {
                let cellText = columns[i].innerHTML.toLowerCase();

                if (cellText.includes("tidak tersedia")) {
                    empty = true;
                }
                if (cellText.includes(input)) {
                    found = true;
                }
            }

            if (filter === "all") {
                row.style.display = "";
            } else if (filter === "empty" && empty && found) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const filterSelect = document.getElementById("filterSelect");
        const bulanSelect = document.getElementById("bulanSelect");
        const tahunSelect = document.getElementById("tahunSelect");

        searchInput.addEventListener("input", applyFilter);
        filterSelect.addEventListener("change", applyFilter);
        bulanSelect.addEventListener("change", applyFilter);
        tahunSelect.addEventListener("change", function () {
            applyFilter(1);
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        applyFilter();
    });

    function applyFilter(page = 1) {
        let search = document.getElementById("searchInput").value.trim();
        let filter = document.getElementById("filterSelect").value;
        let limit = document.getElementById("limitSelect").value;
        let bulan = document.getElementById("bulanSelect").value;
        let tahun = document.getElementById("tahunSelect").value;
        
        if (tahun && !bulan) {
            bulan = ""; // Jika hanya tahun yang dipilih, tetap kosong agar data berdasarkan tahun ditampilkan
        }
        
        let url = `fetch_data.php?search=${encodeURIComponent(search)}&filter=${filter}&limit=${limit}&page=${page}&bulan=${bulan}&tahun=${tahun}`;
        
        console.log("Fetching:", url);
        
        fetch(url)
        .then(response => response.text())
        .then(data => {
            document.getElementById("dataBody").innerHTML = data;
            updatePagination(page, limit, bulan, tahun);
        })
        .catch(error => console.error("Error fetching data:", error));
    }

    function updatePagination(currentPage, limit, bulan, tahun) {
    let search = document.getElementById("searchInput").value.trim();
    let filter = document.getElementById("filterSelect").value;
    
    // Pastikan bulan bernilai kosong jika tidak dipilih agar filter tahun tetap berfungsi
    bulan = bulan || "";
    tahun = tahun || "";
    
    let url = `fetch_pagination.php?search=${encodeURIComponent(search)}&filter=${filter}&limit=${limit}&page=${currentPage}&bulan=${bulan}&tahun=${tahun}`;

    fetch(url)
    .then(response => response.text())
    .then(data => {
        let paginationElement = document.querySelector(".pagination");
        let dataBody = document.getElementById("dataBody");

        // Periksa apakah ada data yang ditampilkan
        if (dataBody.innerHTML.trim() === "") {
            paginationElement.style.display = "none";
        } else {
            paginationElement.style.display = data.trim() === "" ? "none" : "block";
            paginationElement.style.textAlign = "left";
            paginationElement.innerHTML = data;
        }
    })
    .catch(error => console.error("Error fetching pagination:", error));
}

    document.getElementById("filterSelect").addEventListener("change", function () {
        applyFilter(1);
    });



    function fetchDetail(id) {
    fetch(`fetch_detail.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let modalBody = document.getElementById("modalBody");

                modalBody.innerHTML = `
                    <tr><th width="40%">Nomor Akta Cerai</th><td>${data.NO_AC || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Tahun Akta Cerai</th><td>${data.TAHUN_AC || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Hari Akta Cerai</th><td>${data.HARI_AC || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Tanggal Akta Cerai</th><td>${data.TANGGAL_AC_M ? new Date(data.TANGGAL_AC_M).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Nomor Perkara</th><td>${data.NO_PERKARA || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Tahun Perkara</th><td>${data.TAHUN_PERKARA || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Tanggal Putus</th><td>${data.TANGGAL_PUTUS ? new Date(data.TANGGAL_PUTUS).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Nama Penggugat</th><td>${data.NAMA_P || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Umur Penggugat</th><td>${data.UMUR_P || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Binti Penggugat</th><td>${data.BINTI_P || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Pekerjaan Penggugat</th><td>${data.PEKERJAAN_P || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Alamat Penggugat</th><td>${data.ALAMAT_P || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Alamat Penggugat RT RW</th><td>${data.ALAMAT_P_RT_RW || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Alamat Penggugat Kelurahan</th><td>${data.ALAMAT_P_KELURAHAN || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Kecamatan Penggugat</th><td>${data.KECAMATAN_P || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Kabupaten Kota Penggugat</th><td>${data.KABUPATEN_KOTA_P || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Nama Tergugat</th><td>${data.NAMA_T || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Umur Tergugat</th><td>${data.UMUR_T || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Bin Tergugat</th><td>${data.BIN_T || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Pekerjaan Tergugat</th><td>${data.PEKERJAAN_T || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Alamat Tergugat</th><td>${data.ALAMAT_T || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Alamat Tergugat RT RW</th><td>${data.ALAMAT_T_RT_RW || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Alamat Tergugat Kelurahan</th><td>${data.ALAMAT_T_KELURAHAN || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Kecamatan Tergugat</th><td>${data.KECAMATAN_T || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Alamat Tergugat</th><td>${data.ALAMAT_T || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Kabupaten Kota Tergugat</th><td>${data.KABUPATEN_KOTA_T || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>KUA Kecamatan</th><td>${data.KUA_KECAMATAN || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Kabupaten Kota KUA</th><td>${data.KABUPATEN_KOTA_KUA || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Tanggal Nikah</th><td>${data.TANGGAL_NIKAH ? new Date(data.TANGGAL_NIKAH).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Nomor Akta Nikah</th><td>${data.NO_AKTANIKAH || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>Nama File</th><td>${data.FILE_NAME || '<span class="text-danger">Tidak tersedia</span>'}</td></tr>
                    <tr><th>File</th><td>${data.FILE_NAME ? `<a href='unduh_read.php?id=${data.ID}' class="btn btn-sm btn-success" target='_blank'>Download ${data.FILE_NAME}</a>` : '<span class="text-danger">Tidak ada file</span>'}</td></tr>
                `;

                // Tampilkan modal
                let modal = new bootstrap.Modal(document.getElementById("dataModal"));
                modal.show();
            } else {
                alert("Data tidak ditemukan.");
            }
        })
        .catch(error => console.error("Error fetching detail:", error));
}

function editData(id) {
        fetch(`fetch_detail.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("editId").value = data.ID;
                    document.getElementById("editNoAc").value = data.NO_AC || '';
                    document.getElementById("editTanggalAc").value = data.TANGGAL_AC_M || '';
                    document.getElementById("editNoPerkara").value = data.NO_PERKARA || '';
                    document.getElementById("editTahunPerkara").value = data.TAHUN_PERKARA || '';
                    document.getElementById("editTanggalPutus").value = data.TANGGAL_PUTUS || '';
                    document.getElementById("editNamaP").value = data.NAMA_P || '';
                    document.getElementById("editUmurP").value = data.UMUR_P || '';
                    document.getElementById("editBintiP").value = data.BINTI_P || '';
                    document.getElementById("editPekerjaanP").value = data.PEKERJAAN_P || '';
                    document.getElementById("editAlamatP").value = data.ALAMAT_P || '';
                    document.getElementById("editAlamatPRtRw").value = data.ALAMAT_P_RT_RW || '';
                    document.getElementById("editAlamatPKelurahan").value = data.ALAMAT_P_KELURAHAN || '';
                    document.getElementById("editKecamatanP").value = data.KECAMATAN_P || '';
                    document.getElementById("editKabupatenKotaP").value = data.KABUPATEN_KOTA_P || '';                   
                    document.getElementById("editNamaT").value = data.NAMA_T || '';
                    document.getElementById("editUmurT").value = data.UMUR_T || '';
                    document.getElementById("editBinT").value = data.BIN_T || '';
                    document.getElementById("editPekerjaanT").value = data.PEKERJAAN_T || '';
                    document.getElementById("editAlamatT").value = data.ALAMAT_T || '';
                    document.getElementById("editAlamatTRtRw").value = data.ALAMAT_T_RT_RW || '';
                    document.getElementById("editAlamatTKelurahan").value = data.ALAMAT_T_KELURAHAN || '';
                    document.getElementById("editKecamatanT").value = data.KECAMATAN_T || '';
                    document.getElementById("editKabupatenKotaT").value = data.KABUPATEN_KOTA_T || '';
                    document.getElementById("editKUAKecamatan").value = data.KUA_KECAMATAN || '';
                    document.getElementById("editKabupatenKotaKUA").value = data.KABUPATEN_KOTA_KUA || '';
                    document.getElementById("editTanggalNikah").value = data.TANGGAL_NIKAH || '';
                    document.getElementById("editNoAktaNikah").value = data.NO_AKTANIKAH || '';

                    new bootstrap.Modal(document.getElementById("editModal")).show();
                } else {
                    alert("Gagal mengambil data!");
                }
            })
            .catch(error => console.error("Error:", error));
    }

    function cekNoAc(noAc, tanggalAc, id, callback) {
    let formData = new FormData();
    formData.append("ID", id);
    formData.append("NO_AC", noAc);
    formData.append("TANGGAL_AC_M", tanggalAc);

    fetch("cek_up_no_ac.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        callback(data.exists, data.message);
    })
    .catch(error => {
        console.error("Error:", error);
        callback(false, "Terjadi kesalahan saat mengecek NO_AC.");
    });
}

function cekNoPerkara(noPerkara, tahunPerkara, id, callback) {
    if (!tahunPerkara) {
        callback(false, "Tahun perkara kosong, validasi dilewati.");
        return;
    }

    let formData = new FormData();
    formData.append("ID", id);
    formData.append("NO_PERKARA", noPerkara);
    formData.append("TAHUN_PERKARA", tahunPerkara);

    fetch("cek_up_no_perkara.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        callback(data.exists, data.message);
    })
    .catch(error => {
        console.error("Error:", error);
        callback(false, "Terjadi kesalahan saat mengecek NO_PERKARA.");
    });
}

function updateData(event) {
    event.preventDefault();

    let id = document.getElementById("editId").value;
    let noAc = document.getElementById("editNoAc").value;
    let tanggalAc = document.getElementById("editTanggalAc").value;
    let noPerkara = document.getElementById("editNoPerkara").value;
    let tahunPerkara = document.getElementById("editTahunPerkara").value;

    cekNoAc(noAc, tanggalAc, id, function(existsAc, messageAc) {
        if (existsAc) {
            Swal.fire({
                        title : "Gagal!",
                        text: messageAc,
                        icon: "error",
                        timer: 5000,
                        timerProgressBar: true,
                        allowOutsideClick: false
                    });
        } else {
            cekNoPerkara(noPerkara, tahunPerkara, id, function(existsPerkara, messagePerkara) {
                if (existsPerkara) {
                    Swal.fire({
                        title : "Gagal!",
                        text: messagePerkara,
                        icon: "error",
                        timer: 5000,
                        timerProgressBar: true,
                        allowOutsideClick: false
                    });
                } else {
                    let form = document.getElementById("editForm");
                    let formData = new FormData(form);

                    fetch("update_data.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Data berhasil diperbarui!",
                                icon: "success",
                                timer: 5000,
                                timerProgressBar: true,
                                allowOutsideClick: false
                            })
                            .then(() => location.reload());
                        } else {
                            Swal.fire("Gagal!", data.error || "Terjadi kesalahan.", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire("Error!", "Terjadi kesalahan saat memperbarui data.", "error");
                    });
                }
            });
        }
    });
}


function deleteData(id) {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('delete_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: "Dihapus!",
                        text: "Data telah berhasil dihapus.",
                        icon: "success",
                        timer: 1500,
                        timerProgressBar: true,
                        allowOutsideClick: false
                        })
                        .then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Gagal!", data.message, "error");
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
}

function cetakData(id) {
    let printFrame = document.createElement('iframe');
    printFrame.style.position = 'absolute';
    printFrame.style.width = '0px';
    printFrame.style.height = '0px';
    printFrame.style.border = 'none';

    printFrame.src = 'cetak.php?id=' + id;
    document.body.appendChild(printFrame);

    printFrame.onload = function () {
        printFrame.contentWindow.print();
        setTimeout(() => document.body.removeChild(printFrame), 500);
    };
}




// Pastikan tombol memanggil fungsi dengan parameter event
document.querySelector("#editForm button").addEventListener("click", updateData);

// Tutup modal saat tombol close diklik
document.querySelector(".close").addEventListener("click", function() {
    document.getElementById("dataModal").style.display = "none";
});

// Tutup modal saat klik di luar area modal
window.onclick = function(event) {
    let modal = document.getElementById("dataModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
};
