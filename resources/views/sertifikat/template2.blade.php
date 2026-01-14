<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 30px 60px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            background-image: url("{{ public_path('assets/img/background/sertifikat_bg.png') }}");
            background-size: cover;
            background-position: center;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 90px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .subtitle {
            font-size: 14px;
            margin-top: 5px;
        }

        .content {
            margin-top: 10px;
            
        }

        .data-table {
            margin: 10px auto;
            width: 80%;
        }

        .data-table td {
            padding: 0;
            vertical-align: top;
            font-size: 14px;
        }

        .data-table td:first-child {
            width: 25%;
        }

        .data-table td:nth-child(2) {
            width: 3%;
        }

        .footer {
            margin-top: 10px;
            width: 100%;
        }

        .signature {
            float: right;
            text-align: center;
            width: 40%;
        }

        .signature .space {
            height: 80px;
        }

        .signature .space img {
            width: 90px;
        }

        .qr {
            position: absolute;
            bottom: 40px;
            left: 40px;
        }

        .qr img {
            width: 90px;
        }

        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <img src="{{ public_path('assets/img/logo/logo_kab.png') }}">
        <div class="title">SERTIFIKAT</div>
        <div class="subtitle">
            Nomor : {{ $bangkom->nomor_sertifikat ?? '800.2.4/_____/2026' }}
        </div>
    </div>

    {{-- ISI --}}
    <div class="content">
        <p style="text-align: justify;">
            Badan Kepegawaian Pengembangan Sumber Daya Manusia Kabupaten Pekalongan
            sesuai Undang-undang Nomor 20 Tahun 2023 tentang Aparatur Sipil Negara,
            serta ketentuan pelaksanaannya menyatakan bahwa:
        </p>

        <table class="data-table">
            <tr>
                <td>Nama</td><td>:</td>
                <td><strong>{{ $asn['nama'] }}</strong></td>
            </tr>
            <tr>
                <td>NIP</td><td>:</td>
                <td>{{ $asn['nip'] }}</td>
            </tr>
            <tr>
                <td>Jabatan</td><td>:</td>
                <td>{{ $asn['jabatan'] }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td><td>:</td>
                <td>{{ $asn['unit_kerja'] }}</td>
            </tr>
            <tr>
                <td>Instansi</td><td>:</td>
                <td>{{ $asn['instansi'] }}</td>
            </tr>
        </table>

        <p style="text-align: justify;">
            Telah melaksanakan kegiatan <em>'Coaching / Mentoring'</em>
            di Lingkungan Pemerintah Kabupaten Pekalongan dengan tema
            <strong>{{ $bangkom->namakegiatan }}</strong>,
            yang diselenggarakan pada tanggal
            {{ \Carbon\Carbon::parse($bangkom->tanggal_mulai)->translatedFormat('d F Y') }}
            di Kabupaten Pekalongan selama
            2 (dua) Jam Pelajaran.
        </p>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="signature">
            <p>
                Kajen, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                a.n. BUPATI PEKALONGAN<br>
                {{$ttd->jabatan}} KEPALA BADAN KEPEGAWAIAN DAN<br>
                PENGEMBANGAN SUMBER DAYA MANUSIA
            </p>

            <div class="space">
                <img src="{{ $qr }}">
            </div>

            <p>
                <strong>{{ $ttd->nama ?? '__________________' }}</strong><br>
                {{ $ttd->pangkat ?? '__________________' }}<br>
                NIP. {{ $ttd->nip ?? '__________________' }}
            </p>
        </div>

        <div class="clear"></div>
    </div>  

</body>
</html>
