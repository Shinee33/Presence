document.addEventListener("DOMContentLoaded", function () {
    const uploadArea = document.getElementById("uploadArea");
    const photoInput = document.getElementById("photoInput");
    const previewImage = document.getElementById("previewImage");
    const previewArea = document.getElementById("previewArea");
    const uploadBtn = document.getElementById("uploadBtn");
    const form = document.getElementById("uploadPhotoForm");
    let selectedFile = null;

    // Upload area click
    uploadArea.addEventListener("click", () => {
        photoInput.click();
    });

    // File input change
    photoInput.addEventListener("change", function (e) {
        if (e.target.files && e.target.files[0]) {
            handleFile(e.target.files[0]);
        }
    });

    // Drag and drop events
    uploadArea.addEventListener("dragover", function (e) {
        e.preventDefault();
        uploadArea.classList.add("dragover");
    });

    uploadArea.addEventListener("dragleave", function (e) {
        e.preventDefault();
        uploadArea.classList.remove("dragover");
    });

    uploadArea.addEventListener("drop", function (e) {
        e.preventDefault();
        uploadArea.classList.remove("dragover");
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    function handleFile(file) {
        console.log("File selected:", file.name, file.size, file.type);

        // Validate file type
        const allowedTypes = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/gif",
        ];
        if (!allowedTypes.includes(file.type)) {
            showError(
                "Format file tidak didukung. Gunakan JPG, PNG, JPEG, atau GIF."
            );
            return;
        }

        // Validate file size (2MB)
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            showError("Ukuran file terlalu besar. Maksimal 2MB.");
            return;
        }

        // Store selected file
        selectedFile = file;

        // Show preview
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            uploadArea.style.display = "none";
            previewArea.style.display = "block";
            uploadBtn.disabled = false;
        };
        reader.onerror = function () {
            showError("Gagal membaca file. Silakan coba lagi.");
        };
        reader.readAsDataURL(file);
    }

    // Form submission
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!selectedFile) {
            showError("Silakan pilih file terlebih dahulu.");
            return;
        }

        console.log("Form submitted");

        const formData = new FormData();
        formData.append("photo", selectedFile);

        // Add CSRF token
        const csrfToken =
            document.querySelector('meta[name="csrf-token"]') ||
            document.querySelector('input[name="_token"]');
        if (csrfToken) {
            const token = csrfToken.getAttribute
                ? csrfToken.getAttribute("content")
                : csrfToken.value;
            formData.append("_token", token);
        }

        // Show loading state
        uploadBtn.disabled = true;
        const originalText = uploadBtn.innerHTML;
        uploadBtn.innerHTML =
            '<ion-icon name="sync-outline" class="spinner"></ion-icon> Uploading...';

        // Submit form
        fetch(form.action, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        })
            .then(async (response) => {
                console.log("Response status:", response.status);

                const contentType = response.headers.get("content-type");
                if (
                    contentType &&
                    contentType.indexOf("application/json") !== -1
                ) {
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message ||
                                `HTTP error! status: ${response.status}`
                        );
                    }

                    return data;
                } else {
                    const text = await response.text();
                    console.error("Non-JSON response:", text);
                    throw new Error("Server returned invalid response");
                }
            })
            .then((data) => {
                console.log("Success response:", data);

                if (data.success) {
                    // Update profile photo
                    updateProfilePhoto(data.photo_url);
                    closePhotoModal();
                    showSuccess("Foto profil berhasil diperbarui!");
                } else {
                    throw new Error(data.message || "Upload failed");
                }
            })
            .catch((error) => {
                console.error("Upload error:", error);
                showError(
                    "Terjadi kesalahan saat mengupload foto: " + error.message
                );
            })
            .finally(() => {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = originalText;
            });
    });

    // Update profile photo function
    function updateProfilePhoto(photoUrl) {
        const profileContainer = document.getElementById(
            "profilePhotoContainer"
        );
        if (!profileContainer) return;

        const existingImg = profileContainer.querySelector("img");
        const existingIcon = profileContainer.querySelector("ion-icon");

        if (existingImg) {
            // Update existing image with cache busting
            existingImg.src = photoUrl + "?t=" + Date.now();
        } else {
            // Remove icon and add new image
            if (existingIcon) {
                existingIcon.remove();
            }

            const newImg = document.createElement("img");
            newImg.src = photoUrl;
            newImg.alt = "Profile Photo";
            newImg.className = "profile-photo-img";
            newImg.id = "profileImage";
            profileContainer.appendChild(newImg);
        }
    }

    // Error handling functions
    function showError(message) {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Error",
                text: message,
                icon: "error",
                confirmButtonText: "OK",
            });
        } else {
            alert(message);
        }
    }

    function showSuccess(message) {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Berhasil",
                text: message,
                icon: "success",
                timer: 3000,
                showConfirmButton: false,
            });
        } else {
            alert(message);
        }
    }
});

// Modal functions
function openPhotoModal() {
    const modal = document.getElementById("photoModal");
    if (modal) {
        modal.style.display = "block";
    }
}

function closePhotoModal() {
    const modal = document.getElementById("photoModal");
    if (modal) {
        modal.style.display = "none";
    }

    // Reset form
    const photoInput = document.getElementById("photoInput");
    const previewArea = document.getElementById("previewArea");
    const uploadArea = document.getElementById("uploadArea");
    const uploadBtn = document.getElementById("uploadBtn");

    if (photoInput) photoInput.value = "";
    if (previewArea) previewArea.style.display = "none";
    if (uploadArea) uploadArea.style.display = "block";
    if (uploadBtn) uploadBtn.disabled = true;
}

// Close modal when clicking outside
window.addEventListener("click", function (e) {
    const modal = document.getElementById("photoModal");
    if (e.target === modal) {
        closePhotoModal();
    }
});

// ESC key to close modal
document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        closePhotoModal();
    }
});
