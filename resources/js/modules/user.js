$(document).ready(function () {
  const table = $('#table-user').DataTable({
    ajax: '/api/users',
    columns: [
      { data: 'email' },
      { data: 'name' },
      {
        data: 'roles',
        render: roles => roles.map(r => `<span class="badge bg-primary">${r.name}</span>`).join(' ')
      },
      {
        data: null,
        render: function (data) {
          return `
            <button class="btn btn-sm btn-info edit-user" data-id="${data.id}">Edit</button>
            <select class="form-select d-inline w-auto assign-role" data-id="${data.id}">
              ${window.allRoles.map(role =>
                `<option value="${role.name}" ${data.roles.some(r => r.name === role.name) ? 'selected' : ''}>${role.name}</option>`
              ).join('')}
            </select>
            <button class="btn btn-sm btn-danger delete-user" data-id="${data.id}">Nonaktifkan</button>
          `
        }
      }
    ]
  })

  // Load all roles from hidden meta or inline JS (optional)
  window.allRoles = window.allRoles || []

  // Handle edit
  $('#table-user').on('click', '.edit-user', function () {
    const id = $(this).data('id')
    $.get(`/api/users/${id}`, res => {
      $('#form-user [name="id"]').val(res.id)
      $('#form-user [name="email"]').val(res.email)
      $('#form-user [name="name"]').val(res.name)
      $('#modalUserForm').modal('show')
    })
  })

  // Submit form
  $('#form-user').submit(function (e) {
    e.preventDefault()
    const id = $('[name="id"]').val()
    const data = $(this).serialize()
    $.ajax({
      url: `/api/users/${id}`,
      method: 'PUT',
      data,
      success: () => {
        $('#modalUserForm').modal('hide')
        table.ajax.reload()
      }
    })
  })

  // Role change
  $('#table-user').on('change', '.assign-role', function () {
    const id = $(this).data('id')
    const role = $(this).val()
    $.post(`/api/users/${id}/assign-role`, { role }, () => {
      table.ajax.reload(null, false)
    })
  })

  // Delete
  $('#table-user').on('click', '.delete-user', function () {
    if (!confirm('Nonaktifkan user ini?')) return
    const id = $(this).data('id')
    $.ajax({
      url: `/api/users/${id}`,
      method: 'DELETE',
      success: () => table.ajax.reload()
    })
  })
})
