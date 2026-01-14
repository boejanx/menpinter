<x-app-layout>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold">Manajemen User</h4>

    <div class="card">
      <div class="table-responsive text-nowrap">
        <table class="table" id="table-user">
          <thead>
            <tr>
              <th>Email</th>
              <th>Nama</th>
              <th>Role</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalUserForm" tabindex="-1">
    <div class="modal-dialog">
      <form id="form-user" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" />
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" />
          </div>
          <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
