# Dokumentasi Komponen Blade

Berikut adalah daftar komponen yang telah dibuat beserta dokumentasinya:

1.  [Form Input (`form-input.blade.php`)](#1-form-input)
2.  [Form Textarea (`form-textarea.blade.php`)](#2-form-textarea)
3.  [Form Select (`form-select.blade.php`)](#3-form-select)
4.  [Form Checkbox (`form-checkbox.blade.php`)](#4-form-checkbox)
5.  [Form Radio (`form-radio.blade.php`)](#5-form-radio)
6.  [Form File (`form-file.blade.php`)](#6-form-file)
7.  [Button (`button.blade.php`)](#7-button)
8.  [Card (`card.blade.php`)](#8-card)
9.  [Modal (`modal.blade.php`)](#9-modal)

---

### 1. Form Input (`form-input.blade.php`)

Komponen untuk membuat elemen input form standar (text, email, password, number, dll.) dengan dukungan untuk label, placeholder, validasi error, teks bantuan, dan layout horizontal.

**Properti:**

*   `type` (string, default: `'text'`): Jenis input (misalnya, `text`, `email`, `password`, `number`, `date`).
*   `name` (string, **wajib**): Atribut `name` untuk input. Digunakan untuk data form, validasi, dan `old()` helper.
*   `label` (string, default: `''`): Teks untuk elemen `<label>`. Jika kosong, label tidak ditampilkan.
*   `value` (string, default: `''`): Nilai awal untuk input.
*   `placeholder` (string, default: `''`): Teks placeholder untuk input.
*   `required` (boolean, default: `false`): Jika `true`, menambahkan atribut `required`.
*   `disabled` (boolean, default: `false`): Jika `true`, menambahkan atribut `disabled`.
*   `readonly` (boolean, default: `false`): Jika `true`, menambahkan atribut `readonly`.
*   `id` (string, default: `null`): Atribut `id` untuk input. Jika `null`, akan menggunakan nilai dari `name`.
*   `class` (string, default: `'form-control'`): Kelas CSS tambahan untuk elemen input.
*   `helpText` (string, default: `''`): Teks bantuan kecil di bawah input.
*   `horizontal` (boolean, default: `false`): Jika `true`, menggunakan layout horizontal.
*   `labelColClass` (string, default: `'col-md-2'`): Kelas kolom Bootstrap untuk label pada layout horizontal.
*   `inputColClass` (string, default: `'col-md-10'`): Kelas kolom Bootstrap untuk input pada layout horizontal.
*   `noMargin` (boolean, default: `false`): Jika `true`, menghilangkan margin bawah (`mb-3`) dari div pembungkus.
*   `$attributes`: Atribut HTML tambahan akan diteruskan ke elemen `<input>`.

**Contoh Penggunaan:**

```blade
{{-- Input Teks Biasa --}}
<x-form-input
    type="text"
    name="nama_pengguna"
    label="Nama Pengguna"
    placeholder="Masukkan nama pengguna"
    required
/>

{{-- Input Email Horizontal --}}
<x-form-input
    type="email"
    name="email_user"
    label="Alamat Email"
    horizontal
    labelColClass="col-sm-3"
    inputColClass="col-sm-9"
    helpText="Email Anda aman bersama kami."
/>
```

---

### 2. Form Textarea (`form-textarea.blade.php`)

Komponen untuk membuat elemen `<textarea>` dengan dukungan serupa `form-input`.

**Properti:**

*   `name` (string, **wajib**): Atribut `name`.
*   `label` (string, default: `''`): Teks label.
*   `value` (string, default: `''`): Nilai awal.
*   `placeholder` (string, default: `''`): Teks placeholder.
*   `required` (boolean, default: `false`): Atribut `required`.
*   `disabled` (boolean, default: `false`): Atribut `disabled`.
*   `readonly` (boolean, default: `false`): Atribut `readonly`.
*   `id` (string, default: `null`): Atribut `id`. Jika `null`, dari `name`.
*   `class` (string, default: `'form-control'`): Kelas CSS tambahan.
*   `rows` (integer, default: `3`): Atribut `rows` untuk textarea.
*   `helpText` (string, default: `''`): Teks bantuan.
*   `horizontal` (boolean, default: `false`): Layout horizontal.
*   `labelColClass` (string, default: `'col-md-2'`): Kelas kolom label horizontal.
*   `inputColClass` (string, default: `'col-md-10'`): Kelas kolom input horizontal.
*   `noMargin` (boolean, default: `false`): Hilangkan margin bawah.
*   `$attributes`: Atribut HTML tambahan.

**Contoh Penggunaan:**

```blade
<x-form-textarea
    name="alamat_lengkap"
    label="Alamat Lengkap"
    rows="4"
    placeholder="Masukkan alamat lengkap Anda"
/>
```

---

### 3. Form Select (`form-select.blade.php`)

Komponen untuk membuat elemen `<select>` (dropdown) dengan opsi dinamis, dukungan multiple select, dan layout horizontal.

**Properti:**

*   `name` (string, **wajib**): Atribut `name`. Untuk multiple select, nama akan otomatis diubah menjadi `nama[]`.
*   `label` (string, default: `''`): Teks label.
*   `options` (array, default: `[]`): Array untuk opsi. Bisa berupa array asosiatif `[value => text]` atau array objek `[{value: 'val', text: 'label'}]`.
*   `selected` (string|array, default: `null`): Nilai atau array nilai yang terpilih. Untuk multiple select, berikan array.
*   `placeholder` (string, default: `null`): Teks untuk opsi default yang tidak bisa dipilih (misalnya, "-- Pilih --"). Jika `null`, tidak ada placeholder.
*   `required` (boolean, default: `false`): Atribut `required`.
*   `disabled` (boolean, default: `false`): Atribut `disabled`.
*   `id` (string, default: `null`): Atribut `id`. Jika `null`, dari `name`.
*   `class` (string, default: `'form-select'`): Kelas CSS tambahan.
*   `helpText` (string, default: `''`): Teks bantuan.
*   `multiple` (boolean, default: `false`): Jika `true`, mengaktifkan multiple select.
*   `horizontal` (boolean, default: `false`): Layout horizontal.
*   `labelColClass` (string, default: `'col-md-2'`): Kelas kolom label horizontal.
*   `inputColClass` (string, default: `'col-md-10'`): Kelas kolom input horizontal.
*   `noMargin` (boolean, default: `false`): Hilangkan margin bawah.
*   `$attributes`: Atribut HTML tambahan.

**Contoh Penggunaan:**

```blade
@php
$provinsi = [
    'jabar' => 'Jawa Barat',
    'jateng' => 'Jawa Tengah',
    'jatim' => 'Jawa Timur'
];
@endphp
<x-form-select
    name="provinsi_id"
    label="Pilih Provinsi"
    :options="$provinsi"
    placeholder="-- Pilih Provinsi --"
    selected="jateng"
/>

<x-form-select
    name="hobi[]" {{-- Nama dengan [] untuk multiple --}}
    label="Pilih Hobi (Multiple)"
    :options="['membaca' => 'Membaca', 'olahraga' => 'Olahraga', 'musik' => 'Musik']"
    :selected="['membaca', 'musik']"
    multiple
    horizontal
/>
```

---

### 4. Form Checkbox (`form-checkbox.blade.php`)

Komponen untuk membuat elemen input checkbox tunggal atau sebagai bagian dari grup.

**Properti:**

*   `name` (string, **wajib**): Atribut `name`.
*   `label` (string, default: `''`): Teks label untuk checkbox.
*   `value` (string, default: `'1'`): Nilai yang dikirim saat checkbox dicentang.
*   `checked` (boolean, default: `false`): Status centang awal.
*   `disabled` (boolean, default: `false`): Atribut `disabled`.
*   `id` (string, default: `null`): Atribut `id`. Jika `null`, akan digenerate otomatis.
*   `class` (string, default: `'form-check-input'`): Kelas CSS untuk input.
*   `labelClass` (string, default: `'form-check-label'`): Kelas CSS untuk label.
*   `helpText` (string, default: `''`): Teks bantuan.
*   `inline` (boolean, default: `false`): Jika `true`, checkbox ditampilkan inline.
*   `switch` (boolean, default: `false`): Jika `true`, checkbox ditampilkan sebagai switch (Bootstrap 5 style).
*   `horizontal` (boolean, default: `false`): Untuk layout horizontal (biasanya untuk grup).
*   `labelColClass` (string, default: `'col-md-2'`): Kelas kolom label (jika `horizontal` dan bukan `isGroup`).
*   `inputColClass` (string, default: `'col-md-10'`): Kelas kolom input (jika `horizontal` dan bukan `isGroup`).
*   `noMargin` (boolean, default: `false`): Hilangkan margin bawah.
*   `isGroup` (boolean, default: `false`): Menandakan jika checkbox ini adalah bagian dari grup (mempengaruhi margin dan layout horizontal).
*   `$attributes`: Atribut HTML tambahan.

**Contoh Penggunaan:**

```blade
<x-form-checkbox
    name="setuju_sk"
    label="Saya menyetujui Syarat & Ketentuan"
    required
/>

<x-form-checkbox
    name="mode_gelap"
    label="Mode Gelap"
    switch
    checked
/>

{{-- Grup Checkbox Inline --}}
<div class="mb-3">
    <label class="form-label">Pilih Warna Favorit:</label>
    <div>
        <x-form-checkbox name="warna[]" value="merah" label="Merah" inline isGroup/>
        <x-form-checkbox name="warna[]" value="biru" label="Biru" inline isGroup/>
        <x-form-checkbox name="warna[]" value="hijau" label="Hijau" inline isGroup/>
    </div>
</div>
```

---

### 5. Form Radio (`form-radio.blade.php`)

Komponen untuk membuat elemen input radio, biasanya digunakan dalam grup.

**Properti:**

*   `name` (string, **wajib**): Atribut `name`. Semua radio dalam satu grup harus memiliki `name` yang sama.
*   `label` (string, default: `''`): Teks label untuk radio button.
*   `value` (string, **wajib**): Nilai unik untuk radio button ini.
*   `checkedValue` (string, default: `null`): Nilai dari grup radio yang seharusnya terpilih. Bandingkan dengan `value` radio ini.
*   `disabled` (boolean, default: `false`): Atribut `disabled`.
*   `id` (string, default: `null`): Atribut `id`. Jika `null`, akan digenerate otomatis.
*   `class` (string, default: `'form-check-input'`): Kelas CSS untuk input.
*   `labelClass` (string, default: `'form-check-label'`): Kelas CSS untuk label.
*   `helpText` (string, default: `''`): Teks bantuan (biasanya di level grup).
*   `inline` (boolean, default: `false`): Jika `true`, radio ditampilkan inline.
*   `horizontal` (boolean, default: `false`): Menandakan jika ini bagian dari grup radio horizontal.
*   `noMargin` (boolean, default: `false`): Hilangkan margin bawah (jika bukan bagian dari grup).
*   `isGroup` (boolean, default: `false`): Menandakan jika radio ini adalah bagian dari grup (mempengaruhi margin dan penanganan error/help text).
*   `$attributes`: Atribut HTML tambahan.

**Contoh Penggunaan:**

```blade
<div class="mb-3">
    <label class="form-label">Pilih Metode Pengiriman:</label>
    <x-form-radio name="shipping_method" value="jne" label="JNE" :checkedValue="old('shipping_method', 'jne')" isGroup/>
    <x-form-radio name="shipping_method" value="tiki" label="TIKI" :checkedValue="old('shipping_method')" isGroup/>
    <x-form-radio name="shipping_method" value="pos" label="POS Indonesia" :checkedValue="old('shipping_method')" inline isGroup/>
    @error('shipping_method') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>
```

---

### 6. Form File (`form-file.blade.php`)

Komponen untuk membuat elemen input file dengan opsi preview gambar.

**Properti:**

*   `name` (string, **wajib**): Atribut `name`.
*   `label` (string, default: `''`): Teks label.
*   `required` (boolean, default: `false`): Atribut `required`.
*   `disabled` (boolean, default: `false`): Atribut `disabled`.
*   `id` (string, default: `null`): Atribut `id`. Jika `null`, dari `name`.
*   `class` (string, default: `'form-control'`): Kelas CSS tambahan.
*   `helpText` (string, default: `''`): Teks bantuan.
*   `preview` (boolean, default: `false`): Jika `true`, mengaktifkan preview gambar sederhana.
*   `previewTarget` (string, default: `null`): ID elemen `<img>` untuk target preview. Jika `null` dan `preview` true, ID akan digenerate.
*   `horizontal` (boolean, default: `false`): Layout horizontal.
*   `labelColClass` (string, default: `'col-md-2'`): Kelas kolom label horizontal.
*   `inputColClass` (string, default: `'col-md-10'`): Kelas kolom input horizontal.
*   `noMargin` (boolean, default: `false`): Hilangkan margin bawah.
*   `$attributes`: Atribut HTML tambahan (misalnya `accept="image/*"`).

**Contoh Penggunaan:**

```blade
<x-form-file
    name="foto_profil"
    label="Upload Foto Profil"
    accept="image/png, image/jpeg"
    preview
/>
```

---

### 7. Button (`button.blade.php`)

Komponen serbaguna untuk membuat tombol atau link bergaya tombol dengan berbagai opsi kustomisasi.

**Properti:**

*   `type` (string, default: `'button'`): Atribut `type` untuk elemen `<button>` (`submit`, `reset`, `button`). Diabaikan jika `href` diisi.
*   `text` (string, default: `''`): Teks di dalam tombol. Bisa juga menggunakan slot default.
*   `variant` (string, default: `'primary'`): Varian warna tombol (misalnya, `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark`, `link`).
*   `size` (string, default: `''`): Ukuran tombol (`lg`, `sm`).
*   `outline` (boolean, default: `false`): Jika `true`, menggunakan gaya outline.
*   `icon` (string, default: `''`): Kelas CSS untuk ikon (misalnya, `'bx bx-user'` dari Boxicons).
*   `iconPosition` (string, default: `'before'`): Posisi ikon (`before` atau `after` teks).
*   `disabled` (boolean, default: `false`): Atribut `disabled`.
*   `loading` (boolean, default: `false`): Jika `true`, menampilkan spinner dan menonaktifkan tombol.
*   `loadingText` (string, default: `'Memuat...'`): Teks yang ditampilkan saat `loading` true.
*   `id` (string, default: `null`): Atribut `id`.
*   `class` (string, default: `''`): Kelas CSS tambahan.
*   `href` (string, default: `null`): Jika diisi, komponen akan dirender sebagai `<a>` (link) bukan `<button>`.
*   `target` (string, default: `null`): Atribut `target` untuk link (misalnya, `_blank`).
*   `$attributes`: Atribut HTML tambahan.

**Contoh Penggunaan:**

```blade
<x-button type="submit" text="Kirim Data" />

<x-button variant="warning" icon="bx bx-edit" text="Edit" />

<x-button href="/dashboard" variant="info" outline>
    Ke Dashboard <i class="bx bx-right-arrow-alt ms-1"></i>
</x-button>

<x-button variant="danger" loading text="Hapus" />
```

---

### 8. Card (`card.blade.php`)

Komponen untuk membuat elemen card Bootstrap dengan header, body, footer, dan gambar opsional.

**Properti:**

*   `title` (string, default: `null`): Teks judul untuk card header. Akan diabaikan jika slot `header` digunakan.
*   `header` (slot, default: `null`): Slot untuk konten header kustom.
*   `footer` (slot, default: `null`): Slot untuk konten footer kustom.
*   `imgSrc` (string, default: `null`): URL sumber gambar untuk card.
*   `imgAlt` (string, default: `'Card image'`): Teks `alt` untuk gambar.
*   `imgPosition` (string, default: `'top'`): Posisi gambar (`top` atau `bottom`).
*   `variant` (string, default: `null`): Varian warna card (misalnya, `primary`, `success`). Bisa dikombinasikan dengan `outline`.
*   `outline` (boolean, default: `false`): Jika `true` dan `variant` diisi, menggunakan gaya border berwarna.
*   `class` (string, default: `''`): Kelas CSS tambahan untuk elemen `.card`.
*   `headerClass` (string, default: `''`): Kelas CSS tambahan untuk `.card-header`.
*   `bodyClass` (string, default: `''`): Kelas CSS tambahan untuk `.card-body`.
*   `footerClass` (string, default: `''`): Kelas CSS tambahan untuk `.card-footer`.
*   `titleClass` (string, default: `'card-title'`): Kelas CSS untuk judul di header.
*   `noBody` (boolean, default: `false`): Jika `true`, konten slot default akan langsung di dalam `.card` tanpa pembungkus `.card-body`.
*   `$attributes`: Atribut HTML tambahan untuk elemen `.card`.

**Contoh Penggunaan:**

```blade
<x-card title="Informasi Pengguna">
    <p>Ini adalah detail pengguna.</p>
    <x-button text="Lihat Profil" variant="primary" size="sm" />
</x-card>

<x-card imgSrc="/images/artikel.jpg" imgAlt="Gambar Artikel">
    <x-slot name="header">
        <h5 class="card-title mb-0">Artikel Terbaru</h5>
    </x-slot>
    <p class="card-text">Ringkasan singkat mengenai artikel terbaru ada di sini.</p>
    <x-slot name="footer">
        <small class="text-muted">Dipublikasikan 2 jam lalu</small>
    </x-slot>
</x-card>
```

---

### 9. Modal (`modal.blade.php`)

Komponen untuk membuat dialog modal Bootstrap dengan header, body, footer, dan opsi form.

**Properti:**

*   `id` (string, **wajib**): ID unik untuk modal, digunakan untuk mentrigger tampilan modal.
*   `title` (string, default: `'Modal Title'`): Judul modal.
*   `size` (string, default: `''`): Ukuran modal (`sm`, `lg`, `xl`, `fullscreen`).
*   `formAction` (string, default: `null`): URL action untuk form di dalam modal. Jika `null`, tidak ada tag `<form>`.
*   `formMethod` (string, default: `'POST'`): Metode HTTP untuk form (`POST`, `GET`, `PUT`, `DELETE`).
*   `submitButtonText` (string, default: `'Simpan Perubahan'`): Teks untuk tombol submit di footer.
*   `cancelButtonText` (string, default: `'Batal'`): Teks untuk tombol cancel/close di footer.
*   `hideFooter` (boolean, default: `false`): Jika `true`, footer modal tidak ditampilkan.
*   `staticBackdrop` (boolean, default: `false`): Jika `true`, modal tidak akan tertutup saat klik di luar area modal (`data-bs-backdrop="static"`).
*   `header` (slot): Slot untuk konten header kustom, menggantikan `title` default.
*   `footer` (slot): Slot untuk konten footer kustom, menggantikan tombol default.
*   `$attributes`: Atribut HTML tambahan untuk elemen `.modal`.

**Contoh Penggunaan:**

```blade
{{-- Tombol untuk mentrigger modal --}}
<x-button type="button" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
    Tambah User Baru
</x-button>

<x-modal id="modalTambahUser" title="Form Tambah User Baru" formAction="/users" formMethod="POST" submitButtonText="Tambah User">
    {{-- Slot default untuk body modal --}}
    <x-form-input name="nama" label="Nama Lengkap" required />
    <x-form-input type="email" name="email" label="Alamat Email" required />
    <x-form-input type="password" name="password" label="Kata Sandi" required />

    {{-- Contoh slot footer kustom jika diperlukan --}}
    {{-- <x-slot name="footer">
        <x-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup Saja</x-button>
        <x-button type="submit" class="btn btn-success">Simpan Spesial</x-button>
    </x-slot> --}}
</x-modal>
```