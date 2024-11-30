<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function logout() {
        Swal.fire({
            title: 'Ganti Sesi?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            confirmButtonColor: '#FF0000',
            customClass: {
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/lokapustaka/request_handler.php?action=logout';
            }
        });
    }

    function changePassword() {
        Swal.fire({
            title: 'Ganti Password',
            html: `
        <input type="password" id="currentPassword" class="swal2-input" placeholder="Password Lama">
        <input type="password" id="newPassword" class="swal2-input" placeholder="Password Baru">
        <input type="password" id="confirmPassword" class="swal2-input" placeholder="Konfirmasi Password Baru">
        `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Ubah',
            cancelButtonText: 'Batal',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            },
            preConfirm: () => {
                const currentPassword = document.getElementById('currentPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if (!currentPassword || !newPassword || !confirmPassword) {
                    Swal.showValidationMessage('Semua kolom harus diisi');
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    Swal.showValidationMessage('Password baru tidak cocok');
                    return false;
                }

                // Proceed with updating the password
                return fetch('/lokapustaka/request_handler.php?action=change_password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ currentPassword, newPassword })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil', 'Password berhasil diubah', 'success');
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal', 'Tidak dapat mengubah password.', 'error');
                    });
            }
        });
    }

    function resetPasswordStaff(id) {
        Swal.fire({
            title: 'Reset Password ' + id + ' ke Default?',
            text: 'Password default adalah <?= DEFAULT_PASS ?>', // PHP variable
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Reset',
            cancelButtonText: 'Kembali',
            color: '#262626',
            confirmButtonColor: '#FF0000',
            customClass: {
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/lokapustaka/request_handler.php?action=reset_password', {
                    method: 'POST',
                    body: JSON.stringify({ id }),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Password berhasil direset!', '', 'success')
                                .then(() => {
                                    history.back();
                                });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal', 'Terjadi kesalahan saat menghubungi server', 'error');
                    });
            }
        });
    }

    function deleteStaff(id) {
        Swal.fire({
            title: 'Hapus ' + id + '?',
            html: `
            <input type="password" id="password" class="swal2-input" placeholder="Masukkan password anda untuk konfirmasi">
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            color: '#262626',
            confirmButtonColor: '#FF0000',
            customClass: {
                cancelButton: 'no-color-btn'
            },
            preConfirm: () => {
                const password = document.getElementById('password').value;

                if (!password) {
                    Swal.showValidationMessage('Password harus diisi!');
                    return false;
                }

                return { password };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/lokapustaka/request_handler.php?action=delete_staff', {
                    method: 'POST',
                    body: JSON.stringify({ password: result.value.password, id: id }),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);  // Add this to debug
                        if (data.success) {
                            Swal.fire('Staff berhasil dihapus!', '', 'success')
                                .then(() => {
                                    window.location.href = '/lokapustaka/pages/staff.php';
                                });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal', 'Tidak dapat menghapus staff.', 'error');
                    });
            }
        });
    }

    function confirmAddStaff() {
        const form = document.getElementById('addStaffForm');

        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Tidak Valid!',
                text: 'Harap lengkapi semua data sebelum melanjutkan.',
                confirmButtonText: 'OK',
                color: '#262626',
                customClass: {
                    confirmButton: 'pri-color-btn'
                }
            });
            return; // Stop further execution
        }

        Swal.fire({
            title: 'Tambah Staff?',
            text: "Apakah Anda yakin ingin menambahkan staff baru?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Get form data
                const formData = new FormData(form);

                // Convert form data to a plain object
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                // Send the form data as JSON via fetch
                fetch('/lokapustaka/request_handler.php?action=add_staff', {
                    method: 'POST',
                    body: JSON.stringify(data), // Send data as JSON
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(result => {
                        // Handle success or error from the server
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Staff baru berhasil ditambahkan.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            }).then(() => {
                                window.location.href = `/lokapustaka/pages/staff.php?id=${result.id}`;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message || 'Terjadi kesalahan saat menambahkan staff.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        // Handle fetch errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghubungi server.',
                            confirmButtonText: 'OK',
                            color: '#262626',
                            customClass: {
                                confirmButton: 'pri-color-btn'
                            }
                        });
                    });
            }
        });
    }

    function confirmEditStaff() {
        const form = document.getElementById('editStaffForm');

        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Tidak Valid!',
                text: 'Harap lengkapi semua data sebelum melanjutkan.',
                confirmButtonText: 'OK',
                color: '#262626',
                customClass: {
                    confirmButton: 'pri-color-btn'
                }
            });
            return; // Stop further execution
        }

        Swal.fire({
            title: 'Edit Staff?',
            text: 'Apakah anda yakin untuk mengedit staff ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(form);

                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                fetch('/lokapustaka/request_handler.php?action=edit_staff', {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Staff berhasil diedit',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            }).then(() => {
                                window.location.href = '/lokapustaka/pages/staff.php?id=' + result.id;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message || 'Terjadi kesalahan saat mengedit staff.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        // Handle fetch errors
                        console.log(error);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghubungi server.',
                            confirmButtonText: 'OK',
                            color: '#262626',
                            customClass: {
                                confirmButton: 'pri-color-btn'
                            }
                        });
                    });
            }
        });
    }

    function confirmAddMember() {
        const form = document.getElementById('addMemberForm');

        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Tidak Valid!',
                text: 'Harap lengkapi semua data sebelum melanjutkan.',
                confirmButtonText: 'OK',
                color: '#262626',
                customClass: {
                    confirmButton: 'pri-color-btn'
                }
            });
            return;
        }

        Swal.fire({
            title: 'Tambah Anggota?',
            text: "Apakah Anda yakin ingin menambahkan anggota baru?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Get form data
                const formData = new FormData(form);

                // Convert form data to a plain object
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                // Send the form data as JSON via fetch
                fetch('/lokapustaka/request_handler.php?action=add_member', {
                    method: 'POST',
                    body: JSON.stringify(data), // Send data as JSON
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(result => {
                        // Handle success or error from the server
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Anggota baru berhasil ditambahkan.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            }).then(() => {
                                window.location.href = `/lokapustaka/pages/members.php?id=${result.id}`;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message || 'Terjadi kesalahan saat menambahkan anggota.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        // Handle fetch errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghubungi server.',
                            confirmButtonText: 'OK',
                            color: '#262626',
                            customClass: {
                                confirmButton: 'pri-color-btn'
                            }
                        });
                    });
            }
        });
    }

    function confirmEditMember() {
        const form = document.getElementById('editMemberForm');

        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Tidak Valid!',
                text: 'Harap lengkapi semua data sebelum melanjutkan.',
                confirmButtonText: 'OK',
                color: '#262626',
                customClass: {
                    confirmButton: 'pri-color-btn'
                }
            });
            return; // Stop further execution
        }

        Swal.fire({
            title: 'Edit Anggota?',
            text: 'Apakah anda yakin untuk mengedit anggota ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(form);

                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                fetch('/lokapustaka/request_handler.php?action=edit_member', {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Anggota berhasil diedit',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            }).then(() => {
                                window.location.href = '/lokapustaka/pages/members.php?id=' + result.id;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message || 'Terjadi kesalahan saat mengedit anggota.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        // Handle fetch errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghubungi server.',
                            confirmButtonText: 'OK',
                            color: '#262626',
                            customClass: {
                                confirmButton: 'pri-color-btn'
                            }
                        });
                    });
            }
        });
    }

    function deleteMember(id) {
        Swal.fire({
            title: 'Hapus ' + id + '?',
            html: `
            <input type="password" id="password" class="swal2-input" placeholder="Masukkan password anda untuk konfirmasi">
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            color: '#262626',
            confirmButtonColor: '#FF0000',
            customClass: {
                cancelButton: 'no-color-btn'
            },
            preConfirm: () => {
                const password = document.getElementById('password').value;

                if (!password) {
                    Swal.showValidationMessage('Password harus diisi!');
                    return false;
                }

                return { password };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/lokapustaka/request_handler.php?action=delete_member', {
                    method: 'POST',
                    body: JSON.stringify({ password: result.value.password, id: id }),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Anggota berhasil dihapus!', '', 'success')
                                .then(() => {
                                    window.location.href = '/lokapustaka/pages/members.php';
                                });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal', 'Tidak dapat menghapus anggota.', 'error');
                    });
            }
        });
    }

    function extendMember(id) {
        Swal.fire({
            title: 'Perpanjang Keanggotan ' + id + '?',
            text: 'Keanggotaan akan diperpanjang selama ' + '<?= EXPIRED_DATE_TEXT ?>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/lokapustaka/request_handler.php?action=extend_member', {
                    method: 'POST',
                    body: JSON.stringify({ id }),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Keanggotaan ' + data.id + ' berhasil diperpanjang!', 'Masa aktif sampai ' + data.expired_date, 'success')
                                .then(() => {
                                    window.location.href = '/lokapustaka/pages/members.php?id=' + data.id;
                                });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal', 'Tidak dapat memperpanjang anggota.', 'error');
                    });
            }
        });
    }

    function confirmAddBook() {
        const form = document.getElementById('addBookForm');

        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Tidak Valid!',
                text: 'Harap lengkapi semua data sebelum melanjutkan.',
                confirmButtonText: 'OK',
                color: '#262626',
                customClass: {
                    confirmButton: 'pri-color-btn'
                }
            });
            return; // Stop further execution
        }

        Swal.fire({
            title: 'Tambah Buku?',
            text: "Apakah anda yakin ingin menambahkan buku baru?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Prevent default form submission
                const formData = new FormData(form);

                // Submit the form data via fetch
                fetch('/lokapustaka/request_handler.php?action=add_book', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(result => {
                        // Handle success or error from the server
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Buku baru berhasil ditambahkan.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            }).then(() => {
                                window.location.href = `/lokapustaka/pages/books.php?id=${result.id}`;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message || 'Terjadi kesalahan saat menambahkan buku.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghubungi server.',
                            confirmButtonText: 'OK',
                            color: '#262626',
                            customClass: {
                                confirmButton: 'pri-color-btn'
                            }
                        });
                    });
            }
        });
    }

    function confirmEditBook() {
        const form = document.getElementById('editBookForm');

        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Tidak Valid!',
                text: 'Harap lengkapi semua data sebelum melanjutkan.',
                confirmButtonText: 'OK',
                color: '#262626',
                customClass: {
                    confirmButton: 'pri-color-btn'
                }
            });
            return; // Stop further execution
        }

        Swal.fire({
            title: 'Edit Buku?',
            text: "Apakah anda yakin ingin mengedit buku ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Prevent default form submission
                const formData = new FormData(form);

                // Submit the form data via fetch
                fetch('/lokapustaka/request_handler.php?action=edit_book', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(result => {
                        // Handle success or error from the server
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Buku berhasil diedit.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            }).then(() => {
                                window.location.href = `/lokapustaka/pages/books.php?id=${result.id}`;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: result.message || 'Terjadi kesalahan saat mengedit buku.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghubungi server.',
                            confirmButtonText: 'OK',
                            color: '#262626',
                            customClass: {
                                confirmButton: 'pri-color-btn'
                            }
                        });
                    });
            }
        });
    }

    function deleteBook(id) {
        Swal.fire({
            title: 'Hapus ' + id + '?',
            html: `
            <input type="password" id="password" class="swal2-input" placeholder="Masukkan password anda untuk konfirmasi">
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            color: '#262626',
            confirmButtonColor: '#FF0000',
            customClass: {
                cancelButton: 'no-color-btn'
            },
            preConfirm: () => {
                const password = document.getElementById('password').value;

                if (!password) {
                    Swal.showValidationMessage('Password harus diisi!');
                    return false;
                }

                return { password };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/lokapustaka/request_handler.php?action=delete_book', {
                    method: 'POST',
                    body: JSON.stringify({ password: result.value.password, id: id }),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Anggota berhasil dihapus!', '', 'success')
                                .then(() => {
                                    window.location.href = '/lokapustaka/pages/books.php';
                                });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal', 'Tidak dapat menghapus Buku.', 'error');
                    });
            }
        });
    }

    function confirmAddLoan() {
        const form = document.getElementById('addLoanForm');

        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Tidak Valid!',
                text: 'Harap lengkapi semua data sebelum melanjutkan.',
                confirmButtonText: 'OK',
                color: '#262626',
                customClass: {
                    confirmButton: 'pri-color-btn'
                }
            });
            return; // Stop further execution
        }

        Swal.fire({
            title: 'Tambah Peminjaman?',
            text: "Apakah Anda yakin ingin menambahkan peminjaman baru?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Get form data
                const formData = new FormData(form);

                // Convert form data to a plain object
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                // Send the form data as JSON via fetch
                fetch('/lokapustaka/request_handler.php?action=add_loan', {
                    method: 'POST',
                    body: JSON.stringify(data), // Send data as JSON
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Handle success or error from the server
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Peminjaman!',
                                text: 'Dengan tenggat waktu sampai ' + data.expected_return_date,
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            }).then(() => {
                                window.location.href = `/lokapustaka/pages/loans.php?id=${data.id}`;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan saat menambahkan peminjaman.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        // Handle fetch errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghubungi server.',
                            confirmButtonText: 'OK',
                            color: '#262626',
                            customClass: {
                                confirmButton: 'pri-color-btn'
                            }
                        });
                    });
            }
        });
    }

    function deleteLoan(id) {
        Swal.fire({
            title: 'Hapus ' + id + '?',
            html: `
            <input type="password" id="password" class="swal2-input" placeholder="Masukkan password anda untuk konfirmasi">
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            color: '#262626',
            confirmButtonColor: '#FF0000',
            customClass: {
                cancelButton: 'no-color-btn'
            },
            preConfirm: () => {
                const password = document.getElementById('password').value;

                if (!password) {
                    Swal.showValidationMessage('Password harus diisi!');
                    return false;
                }

                return { password };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/lokapustaka/request_handler.php?action=delete_loan', {
                    method: 'POST',
                    body: JSON.stringify({ password: result.value.password, id: id }),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Peminjaman berhasil dihapus!', '', 'success')
                                .then(() => {
                                    window.location.href = '/lokapustaka/pages/loans.php';
                                });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal', 'Tidak dapat menghapus Peminjaman.', 'error');
                    });
            }
        });
    }

    function extendLoan(id) {
        Swal.fire({
            title: 'Perpanjang Peminjaman ' + id + '?',
            text: 'Peminjaman akan diperpanjang selama ' + '<?= EXPECTED_RETURN_DATE_TEXT ?>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/lokapustaka/request_handler.php?action=extend_loan', {
                    method: 'POST',
                    body: JSON.stringify({ id }),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Peminjaman ' + data.id + ' berhasil diperpanjang!', 'Tenggat Waktu sampai ' + data.expected_return_date, 'success')
                                .then(() => {
                                    window.location.reload();
                                });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error')
                                .then(() => {
                                    window.location.reload();
                                });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal', 'Kesalahan saat menghubungkan dengan server.', 'error');
                    });
            }
        });
    }

    function returnLoan(id) {
        Swal.fire({
            title: 'Peminjaman ' + id + ' Dikembalikan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/lokapustaka/request_handler.php?action=return_loan', {
                    method: 'POST',
                    body: JSON.stringify({ id }), // Send data as JSON
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Handle success or error from the server
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Peminjaman ' + data.id + ' selesai.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            }).then(() => {
                                window.location.reload();
                            });
                        } else if (data.fines_set) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peminjaman telat ' + data.day_late + ' hari!',
                                text: 'Harap bayar denda sebesar Rp. ' + data.fines + '.',
                                confirmButtonText: 'Dibayar',
                                cancelButtonText: 'Nanti',
                                showCancelButton: true,
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn',
                                    cancelButton: 'no-color-btn'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    fetch('/lokapustaka/request_handler.php?action=fines_paid', {
                                        method: 'POST',
                                        body: JSON.stringify({ id }),
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Denda Dibayar!',
                                                    text: 'Denda berhasil dibayar. Peminjaman ' + data.id + ' selesai!',
                                                    confirmButtonText: 'OK',
                                                    color: '#262626',
                                                    customClass: {
                                                        confirmButton: 'pri-color-btn'
                                                    }
                                                }).then(() => {
                                                    window.location.reload();
                                                });
                                            } else {
                                                Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            Swal.fire('Gagal', 'Kesalahan saat menghubungkan dengan server.', 'error');
                                        });
                                } else {
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Denda Belum Dibayar',
                                        text: 'Silakan selesaikan pembayaran denda nanti.',
                                        confirmButtonText: 'OK',
                                        color: '#262626',
                                        customClass: {
                                            confirmButton: 'pri-color-btn'
                                        }
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan saat menyelesaikan peminjaman.',
                                confirmButtonText: 'OK',
                                color: '#262626',
                                customClass: {
                                    confirmButton: 'pri-color-btn'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        // Handle fetch errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghubungi server.',
                            confirmButtonText: 'OK',
                            color: '#262626',
                            customClass: {
                                confirmButton: 'pri-color-btn'
                            }
                        });
                    });
            }
        });
    }

    function searchLoan() {
        Swal.fire({
            title: 'Pengembalian / Perpanjang Peminjaman',
            html: `
            <input type="text" id="id" class="swal2-input" placeholder="ID Peminjaman">
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Cari ID',
            cancelButtonText: 'Batal',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            },
            preConfirm: () => {
                const id = document.getElementById('id').value;

                if (!id) {
                    Swal.showValidationMessage('ID Peminjaman harus diisi!');
                    return false;
                }

                return { id };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/lokapustaka/pages/loans.php?id=${result.value.id}`
            }
        });
    }

    function searchBook() {
        Swal.fire({
            title: 'Cari Buku',
            html: `
            <input type="text" id="search" class="swal2-input" placeholder="ID, Judul atau ISBN Buku">
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Cari Buku',
            cancelButtonText: 'Batal',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            },
            preConfirm: () => {
                const search = document.getElementById('search').value;

                if (!search) {
                    Swal.showValidationMessage('Harus diisi!');
                    return false;
                }

                return { search };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/lokapustaka/pages/books.php?search=${result.value.search}`
            }
        });
    }

    function searchMember() {
        Swal.fire({
            title: 'Cari Member',
            html: `
            <input type="text" id="search" class="swal2-input" placeholder="ID atau Nama Anggota">
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Cari Anggota',
            cancelButtonText: 'Batal',
            color: '#262626',
            customClass: {
                confirmButton: 'pri-color-btn',
                cancelButton: 'no-color-btn'
            },
            preConfirm: () => {
                const search = document.getElementById('search').value;

                if (!search) {
                    Swal.showValidationMessage('Harus diisi!');
                    return false;
                }

                return { search };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/lokapustaka/pages/members.php?search=${result.value.search}`
            }
        });
    }
</script>