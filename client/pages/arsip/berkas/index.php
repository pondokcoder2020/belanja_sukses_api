<div class="container-fluid page__heading-container">
	<div class="page__heading d-flex align-items-center">
		<div class="flex">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo __HOSTNAME__; ?>/">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page">Berkas</li>
				</ol>
			</nav>
		</div>
		<button class="btn btn-sm btn-info" id="tambah-berkas">
			<i class="fa fa-plus"></i> Tambah Berkas Arsip
		</button>
	</div>
</div>


<div class="container-fluid page__container">
	<div class="row card-group-row">
		<div class="col-lg-12 col-md-12 card-group-row__col">
			<div class="card card-group-row__card card-body card-body-x-lg flex-row align-items-center">
				<table class="table table-bordered largeDataType" id="table-berkas">
					<thead class="thead-dark text-center">
						<tr>
							<th class="wrap_content">No</th>
							<th>Tanggal</th>
                            <th>Nama</th>
                            <th>File</th>
                            <th>Keterangan</th>
							<th>Lokasi Simpan</th>
							<th class="wrap_content">Aksi</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>