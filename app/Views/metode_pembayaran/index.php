<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1>
<p class="text-muted">Kelola data metode pembayaran</p>

<a href="<?= base_url('metode-bayar/create') ?>" class="btn btn-success btn-sm mb-3">
    <i class="ti ti-plus"></i> Tambah Data
</a>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title mb-3"><?= $title; ?></h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle dataTable">
                <thead class="table-success">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Metode</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $no = 1; 
                    foreach ($data as $row) { 
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nama']; ?></td>
                            <td>
                                <a href="<?= base_url('metode-bayar/edit/' . $row['id']) ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="ti ti-pencil"></i>
                                </a>
                                <button type="button" data-nama="<?= $row['nama'] ?>" data-id="<?= $row['id'] ?>"
                                    onclick="hapus(this)" class="btn btn-outline-danger btn-sm">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('js'); ?>
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
                        url: "<?= base_url('metode-bayar/delete'); ?>",
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
