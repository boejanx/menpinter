<x-app-layout>
  <div class="col-12">
    <h4 class="fw-bold">Manajemen User</h4>

    <div class="card py-4">
      <div class="table-responsive text-nowrap">
        <table class="table" id="table-user">
          <thead>
            <tr>
              <th scope="col">Email</th>
              <th scope="col">Nama</th>
              <th scope="col">Role</th>
              <th scope="col">Status</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
  

  <!-- Modal -->
  <div class="modal fade" id="modalUserForm" tabindex="-1" aria-labelledby="modalUserTitle" aria-hidden="true" role="dialog">
    <div class="modal-dialog">
      <form id="form-user" class="modal-content" method="post">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalUserTitle">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" />
          <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" autocomplete="email" required />
          </div>
          <div class="mb-3">
            <label for="name">Nama</label>
            <input type="text" name="name" id="name" class="form-control" autocomplete="name" required />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
<script>
    window.allRoles = @json($roles ?? []);
  </script>
</x-app-layout>
