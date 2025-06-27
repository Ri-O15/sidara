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

document.getElementById("file").addEventListener("change", function() {
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

      function validateForm(event) {
        event.preventDefault(); // Mencegah form dikirim langsung

        var submitBtn = document.getElementById("submitBtn");
        submitBtn.disabled = true; // Nonaktifkan tombol sementara
        submitBtn.innerHTML =
          '<span class="spinner-border spinner-border-sm"></span> Mengirim...';

        var no_ac = document.getElementById("no_ac").value;
        var tanggal_ac_m = document.getElementById("tanggal_ac_m").value;
        var no_perkara = document.getElementById("no_perkara").value;
        var tahun_perkara = document.getElementById("tahun_perkara").value;
        // var tanggal_putus = document.getElementById("tanggal_putus").value;
        var nama_p = document.getElementById("nama_p").value;
        var nama_t = document.getElementById("nama_t").value;
        // var file = document.getElementById("file").value;
        var missingFields = [];
        if (tahun_perkara === "") {
            tahun_perkara = null; // Tetapkan nilai null jika kosong
        }

        if (no_ac === "") missingFields.push("NOMOR AKTA CERAI");
        // if (tanggal_ac_m === "") missingFields.push("TANGGAL_AC_M");
        if (no_perkara === "") missingFields.push("NOMOR PERKARA");

        // if (tahun_perkara === "") {
        //   missingFields.push("TAHUN_PERKARA");
        // } else if (!/^\d{4}$/.test(tahun_perkara)) {
        //   Swal.fire({
        //     title: "Format Salah!",
        //     text: "Tahun Perkara harus terdiri dari 4 angka (contoh: 2025).",
        //     icon: "error",
        //     confirmButtonText: "Coba lagi",
        //   });
        //   resetButton(); // Aktifkan kembali tombol submit jika ada error
        //   return;
        // }
        // if (tanggal_putus === "") missingFields.push("TANGGAL_PUTUS");
        if (nama_p === "") missingFields.push("NAMA PENGGUGAT");
        if (nama_t === "") missingFields.push("NAMA TERGUGAT");
        // if (file === "") missingFields.push("FILE");

        if (missingFields.length > 0) {
          Swal.fire({
            title: "Oops...",
            text: "Harap lengkapi field: " + missingFields.join(", "),
            icon: "error",
            timer: 5000,
            timerProgressBar: true,
            allowOutsideClick: false,
            confirmButtonText: "Coba lagi",
          });
          resetButton(); // Aktifkan kembali tombol submit jika ada error
          return;
        }

        var xhr1 = new XMLHttpRequest();
        xhr1.open("POST", "cek_no_ac.php", true);
        xhr1.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr1.onreadystatechange = function () {
            if (xhr1.readyState === 4) {
                if (xhr1.status === 200) {
                    try {
                        var response1 = JSON.parse(xhr1.responseText);
                        console.log(response1);

                        if (response1.exists) {
                            Swal.fire({
                                title: "No AC Sudah Ada!",
                                text: response1.message,
                                icon: "error",
                                timer: 5000,
                                timerProgressBar: true,
                                allowOutsideClick: false
                            });
                            resetButton();
                        } else {
                            var xhr2 = new XMLHttpRequest();
                            xhr2.open("POST", "cek_no_perkara.php", true);
                            xhr2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhr2.onreadystatechange = function () {
                                if (xhr2.readyState === 4) {
                                    if (xhr2.status === 200) {
                                        var response2 = JSON.parse(xhr2.responseText);
                                        if (response2.exists) {
                                            Swal.fire({
                                                title: "No Perkara Sudah Ada di Tahun yang Sama!",
                                                text: response2.message,
                                                icon: "error",
                                                timer: 5000,
                                                timerProgressBar: true,
                                                allowOutsideClick: false
                                            });
                                            resetButton();
                                        } else {
                                            Swal.fire({
                                                title: "Sukses!",
                                                text: "Data berhasil dikirim!",
                                                icon: "success",
                                                timer: 1500,                                              
                                                timerProgressBar: true,
                                                allowOutsideClick: false
                                            }).then(() => {
                                                document.getElementById("uploadForm").submit();
                                            });
                                        }
                                    }
                                }
                            };
                            xhr2.send("NO_PERKARA=" + encodeURIComponent(no_perkara) + "&TAHUN_PERKARA=" + (tahun_perkara !== null ? encodeURIComponent(tahun_perkara) : ""));
                        }
                    } catch (e) {
                        console.error("Respon server tidak valid", e);
                        Swal.fire({
                            title: "Error!",
                            text: "Respon server tidak valid.",
                            icon: "error",
                            confirmButtonText: "Coba lagi",
                        });
                        resetButton();
                    }
                } else {
                    console.error("Server Error: " + xhr1.status);
                }
            }
        };
          xhr1.send("NO_AC=" + encodeURIComponent(no_ac) + "&TANGGAL_AC_M=" + encodeURIComponent(tanggal_ac_m));
    }


      // Fungsi untuk mengembalikan tombol ke kondisi semula jika terjadi error
      function resetButton() {
        var submitBtn = document.getElementById("submitBtn");
        submitBtn.disabled = false;
        submitBtn.innerHTML = "SUBMIT DATA";
      }