export default function initUserModule() {
  const TABLE = '#table-user';
  if (!$(TABLE).length) return;

  // Destroy if exists
  if ($.fn.DataTable.isDataTable(TABLE)) {
    $(TABLE).DataTable().clear().destroy();
  }

  window.allRoles = Array.isArray(window.allRoles) ? window.allRoles : [];

  const csrf = $('meta[name="csrf-token"]').attr('content');
  if (csrf) $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrf } });

  const table = $(TABLE).DataTable({
    destroy: true,
    autoWidth: false,
    ajax: { url: '/users', dataSrc: 'data' },
    columns: [
      { data: 'email' },
      { data: 'name' },
      {
        data: 'roles',
        render: r => (r || []).map(x => `<span class="badge bg-primary">${x.name}</span>`).join(' ')
      },
      {
        data: 'is_active',
        render: v => v
          ? '<span class="badge bg-success">Aktif</span>'
          : '<span class="badge bg-secondary">Tidak Aktif</span>'
      },
      {
        data: null,
        orderable: false,
        searchable: false,
        render: d => `
          <select class="form-select d-inline w-auto assign-role" data-id="${d.id}">
            ${window.allRoles.map(r =>
              `<option ${d.roles?.some(x => x.name === r.name) ? 'selected' : ''}>
                ${r.name}
              </option>`
            ).join('')}
          </select>
          <button class="btn btn-sm ${d.is_active ? 'btn-danger' : 'btn-success'} toggle-active"
                  data-id="${d.id}">
            ${d.is_active ? 'Nonaktifkan' : 'Aktifkan'}
          </button>
        `
      }
    ]
  });

  // Submit form
  $(document).off('submit.user')
    .on('submit.user', '#form-user', e => {
      e.preventDefault();
      const id = $('[name=id]').val();
      $.ajax({
        url: `/users/${id}`,
        method: 'PUT',
        data: $('#form-user').serialize(),
        success: () => {
          $('#modalUserForm').modal('hide');
          table.ajax.reload(null, false);
        }
      });
    });

  // Assign role
  $(document).off('change.user')
    .on('change.user', `${TABLE} .assign-role`, function () {
      $.post(`/users/${$(this).data('id')}/assign-role`, {
        role: $(this).val()
      }).done(() => table.ajax.reload(null, false));
    });

  // Toggle active
  $(document).off('click.user')
    .on('click.user', `${TABLE} .toggle-active`, function () {
      const row = table.row($(this).closest('tr')).data();
      const id = row.id;
      const activate = !row.is_active;

      Swal.fire({
        icon: 'warning',
        title: activate ? 'Aktifkan User?' : 'Nonaktifkan User?',
        showCancelButton: true,
        confirmButtonText: activate ? 'Ya, aktifkan' : 'Ya, nonaktifkan'
      }).then(r => {
        if (!r.isConfirmed) return;

        const req = activate
          ? $.post(`/users/${id}/activate`)
          : $.ajax({ url: `/users/${id}`, method: 'DELETE' });

        req.done(() => table.ajax.reload(null, false));
      });
    });
}
