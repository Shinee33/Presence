document.addEventListener("DOMContentLoaded", function () {
    // Hide loader
    setTimeout(() => {
        document.getElementById("loader").classList.add("hidden");
    }, 800);

    const logo = document.getElementById("loginLogo");
    if (logo) {
        setTimeout(() => {
            logo.style.transform = "scale(1) translateY(-50px)";
            logo.style.transition = "all 0.8s ease";
        }, 500);
    }

    const form = document.getElementById("loginForm");
    const loginBtn = document.getElementById("loginBtn");
    const btnText = document.getElementById("btnText");
    const btnSpinner = document.getElementById("btnSpinner");

    form.addEventListener("submit", function (e) {
        const username = document.getElementById("nama_lengkap").value.trim();
        const password = document.getElementById("password1").value.trim();

        if (!username || !password) {
            e.preventDefault();
            alert("Silahkan isi username dan password!");
            return;
        }

        loginBtn.disabled = true;
        btnText.style.display = "none";
        btnSpinner.style.display = "inline-block";
    });

    // Clear input button
    const inputs = document.querySelectorAll(".form-control");
    inputs.forEach((input) => {
        const clearBtn = input.parentNode.querySelector(".clear-input");
        input.addEventListener("input", () => {
            if (input.value.length > 0) {
                clearBtn.style.opacity = "1";
            } else {
                clearBtn.style.opacity = "0";
            }
        });
    });

    // ESC key clears inputs
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            document.querySelectorAll(".form-control").forEach((input) => {
                input.value = "";
            });
        }
    });
});

function clearInput(inputId) {
    const input = document.getElementById(inputId);
    input.value = "";
    input.focus();
}
