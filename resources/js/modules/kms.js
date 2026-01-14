let timer = null;

document.getElementById('search-kms').addEventListener('keyup', function () {
    clearTimeout(timer);

    const keyword = this.value;

    timer = setTimeout(() => {
        fetch(`/kms/search?q=${encodeURIComponent(keyword)}`)
            .then(res => res.json())
            .then(data => {
                let html = '';

                if (data.length === 0) {
                    html = `<tr><td colspan="2" class="text-center">Data tidak ditemukan</td></tr>`;
                } else {
                    data.forEach(item => {
                        html += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${item.thumbnail 
                                        ? '/storage/kms/' + item.thumbnail 
                                        : '/assets/img/default.png'}"
                                         class="me-3" width="32" height="32">
                                    <div>
                                        <h6 class="mb-0">${item.judul}</h6>
                                        <small>${item.user?.name ?? '-'}</small>
                                    </div>
                                </div>
                            </td>
                            <td>${item.category?.cat_name ?? '-'}</td>
                        </tr>`;
                    });
                }

                document.getElementById('kms-body').innerHTML = html;
            });
    }, 300); // debounce 300ms
});

$(document).on('click', '.kms-category', function () {
    let categoryId = $(this).data('id');

    $('.kms-category').removeClass('active');
    $(this).addClass('active');

    $.ajax({
        url: "{{ route('kms') }}",
        data: { category: categoryId },
        beforeSend() {
            $('#kms-body').html(`
                <tr>
                    <td colspan="2" class="text-center py-4">
                        <div class="spinner-border"></div>
                    </td>
                </tr>
            `);
        },
        success(res) {
            let html = '';
            $(res.data).each(function (_, c) {
                html += `
                <tr>
                    <td>
                        <h6 class="mb-0">${c.judul}</h6>
                        <span class="badge bg-label-primary">${c.category.cat_name}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn rounded-pill btn-dark">
                            <i class="fas fa-list"></i> Detail
                        </button>
                    </td>
                </tr>`;
            });

            $('#kms-body').html(html);
        }
    });
});
