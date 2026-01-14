export function formatTanggalIndonesia(tanggal) {
    if (!tanggal) return '-'; // Jika tanggal kosong, kembalikan strip

    const bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    const dateObj = new Date(tanggal);

    if (isNaN(dateObj)) return '-'; // Handle jika tanggal invalid

    const tgl = dateObj.getDate();
    const bln = bulan[dateObj.getMonth()];
    const thn = dateObj.getFullYear();

    return `${tgl} ${bln} ${thn}`;
}
