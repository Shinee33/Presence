document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formIzin");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch("{{ route('presensi.storeizin') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]')
                    .value,
            },
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "success") {
                    window.location.href = "/dashboard";
                } else {
                    alert(data.message || "Gagal mengirim izin.");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Terjadi kesalahan. Silakan coba lagi.");
            });
    });
});
