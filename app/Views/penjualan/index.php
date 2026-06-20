<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1>
<p class="text-muted">Kelola data sampah</p>

<a href="<?= base_url('penjualan/create') ?>" class="btn btn-success btn-sm mb-3">
    <i class="ti ti-plus"></i> Tambah Data
</a>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title mb-3"><?= $title; ?></h5>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle dataTable">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Sampah</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Total</th>
                        <th>Metode Bayar</th>
                        <th>Aksi</th>
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
                        $total = $row['harga_jual'] * $row['jumlah'];
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['tanggal']; ?></td>
                            <td><?= $row['nama_sampah']; ?></td>
                            <td><?= $row['jumlah']; ?></td>
                            <td><?= formatRupiah($row['harga_jual']); ?></td>
                            <td class="text-success fw-semibold"><?= formatRupiah($total); ?></td>
                            <td><?= $row['metode_bayar']; ?></td>
                            <td>

                                <!--fitur hapus-->
                                <button type="button"
                                        data-nama="<?= $row['nama_sampah'] ?>"
                                        data-id="<?= $row['id'] ?>"
                                        onclick="hapus(this)"
                                        class="btn btn-outline-danger btn-sm"
                                        title="Hapus">
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
        let nama = $(event).data('nama');
        let id = $(event).data('id');

        //pop up konfirmasi hapus data
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
                    url: "<?= base_url('penjualan/delete'); ?>",
                    type: 'POST',
                    data: { id }
                })

                .done(function(res) {
                    Swal.fire({
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
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                });

            }
        });
    }
</script>

<?= $this->endSection(); ?>
