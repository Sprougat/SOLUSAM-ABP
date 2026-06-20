<?= $this->extend('template/index'); ?> 
<?php ?>

<?= $this->section('content'); ?> 
<?php ?>

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> 
<!-- Judul halaman menggunakan variabel $title -->

<p class="text-muted">Kelola data klien</p> 
<!-- Deskripsi kecil halaman -->

<a href="<?= base_url('data-klien/create') ?>" class="btn btn-success btn-sm mb-3">
    <!-- Tombol menuju halaman tambah data klien -->
    <i class="ti ti-plus"></i> Tambah Data
</a>

<!-- Data Klien -->
<div class="card shadow-sm border-0">
    <!-- Card container untuk tabel klien -->
    <div class="card-body">
        <!-- Body card -->

        <h5 class="card-title mb-3"><?= $title; ?></h5>
        <!-- Judul kecil tabel data -->

        <div class="table-responsive">
            <!-- Membuat tabel responsif -->

            <table class="table table-bordered table-hover align-middle dataTable">
                <!-- Tabel utama dengan border dan hover -->

                <thead class="table-success">
                    <!-- Header tabel berwarna hijau -->
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col">No Telp</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Jenis Usaha</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    function formatRupiah($number)
                    {
                        
                        return 'Rp ' . number_format($number, 0, ',', '.');
                    }

                    $no = 1; 
                    

                    foreach ($data as $row) { 
                        
                    ?>
                        <tr>
                            <!-- Baris data tabel -->
                            <td><?= $no++; ?></td> 
                            <!-- Nomor urut -->

                            <td><?= $row['nama_lengkap']; ?></td>
                            <!-- Nama lengkap klien -->

                            <td><?= $row['no_telp']; ?></td>
                            <!-- Nomor telepon klien -->

                            <td><?= $row['alamat']; ?></td>
                            <!-- Alamat klien -->

                            <td><?= $row['jenis_usaha']; ?></td>
                            <!-- Jenis usaha klien -->

                            <td>
                                <!-- Kolom aksi (edit & hapus) -->

                                <a href="<?= base_url('data-klien/edit/' . $row['id']) ?>" class="btn btn-outline-primary btn-sm">
                                    <!-- Tombol edit -->
                                    <i class="ti ti-pencil"></i>
                                </a>

                                <button type="button"
                                    data-nama="<?= $row['nama_lengkap'] ?>"
                                    data-id="<?= $row['id'] ?>"
                                    onclick="hapus(this)"
                                    class="btn btn-outline-danger btn-sm">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                    <!-- Penutup foreach -->
                </tbody>

            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?> 
<?php ?>

<?= $this->section('js'); ?> 
<?php ?>

<script>
    function hapus(event) {
        let nama = $(event).data('nama')
        let id = $(event).data('id')

        Swal.fire({
            title: 'Apakah anda yakin?',
            html: `Ingin menghapus data <strong>${nama}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
        }).then((result) => {

            if (result.value) {
                $.ajax({
                    url: "<?= base_url('data-klien/delete'); ?>",
                    type: 'POST',
                    data: {
                        id,
                    }
                })
                .done(function(res) {
                    swal.fire({
                        title: res.title,
                        text: res.text,
                        icon: res.icon,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                })
                .fail(function() {
                    swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                });
            }

        })
    }
</script>

<?= $this->endSection(); ?> 
<?php ?>
