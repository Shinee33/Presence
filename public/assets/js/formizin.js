document.addEventListener("DOMContentLoaded", function () {
    // Initialize Materialize components
    M.AutoInit();

    // Initialize datepicker with Indonesian localization
    const datepickerOptions = {
        format: "yyyy-mm-dd",
        autoClose: true,
        setDefaultDate: false,
        minDate: new Date(),
        i18n: {
            months: [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember",
            ],
            monthsShort: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "Mei",
                "Jun",
                "Jul",
                "Agu",
                "Sep",
                "Okt",
                "Nov",
                "Des",
            ],
            weekdays: [
                "Minggu",
                "Senin",
                "Selasa",
                "Rabu",
                "Kamis",
                "Jumat",
                "Sabtu",
            ],
            weekdaysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
            weekdaysAbbrev: ["M", "S", "S", "R", "K", "J", "S"],
        },
    };
    M.Datepicker.init(
        document.querySelectorAll(".datepicker"),
        datepickerOptions
    );

    // Form submission handler
    document
        .getElementById("frmizin")
        .addEventListener("submit", async function (e) {
            e.preventDefault();

            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const formData = new FormData(form);

            // Frontend validation
            if (
                !formData.get("tgl_izin") ||
                !formData.get("status") ||
                !formData.get("keterangan")
            ) {
                showErrorToast("Semua field wajib diisi!");
                return;
            }

            if (formData.get("keterangan").length < 10) {
                showErrorToast("Keterangan minimal 10 karakter!");
                return;
            }

            // Show loading state
            btn.disabled = true;
            btn.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            try {
                // Submit form
                const response = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || "Terjadi kesalahan server");
                }

                // Success handling
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: data.message,
                    confirmButtonColor: "#667eea",
                }).then(() => {
                    window.location.href = "/presensi/izin";
                });
            } catch (error) {
                console.error("Error:", error);
                showErrorToast(
                    error.message || "Terjadi kesalahan. Silakan coba lagi."
                );
            } finally {
                btn.disabled = false;
                btn.innerHTML =
                    '<i class="fas fa-paper-plane"></i> Kirim Permohonan';
            }
        });

    // Show error toast notification
    function showErrorToast(message) {
        M.toast({
            html: `<span>${message}</span>`,
            classes: "red lighten-1",
            displayLength: 4000,
        });
    }
});
