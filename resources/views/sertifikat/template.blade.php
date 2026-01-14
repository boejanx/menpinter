<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0cm;
        }

        body {
            padding: 1cm 4cm 3cm 4cm;
            font-family: Arial, sans-serif;
            background-image: url("{{ public_path('assets/img/backgrounds/sertifikat.jpg') }}");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .mb-1 {
            margin-bottom: 5px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mb-4 {
            margin-bottom: 20px;
        }

        .header-logo {
            width: 100px;
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            position: center;
        }

        .table-info {
            width: 70%;
            margin: 10px 0;
        }

        .table-info td {
            padding: 1px;
            vertical-align: top;
        }

        .qr-signature {
            margin-top: 30px;
            text-align: center;
        }

        .qr-signature img {
            width: 100px;
            height: 100px;
        }

        .footer-note {
            position: absolute;
            bottom: 20px;
            left: 40px;
            right: 40px;
            font-size: 10px;
            text-align: center;
        }

        .title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }

        .sertifikat-number {
            font-size: 10pt;
            text-align: center;
            margin-bottom: 10px;
        }

        .container {
            width: 100%;
            max-width: 100%;
            padding: 0 20px;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="text-center">
            <img class="header-logo" src="{{ public_path('assets/img/logo/logo_kab.png') }}">
            <div class="title">BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA</div>
            <div class="title">KABUPATEN PEKALONGAN</div>
            <div class="sertifikat-number">SERTIFIKAT </div>
            <div class="sertifikat-number">Nomor: 800/{{ $bangkom->event_certificate}}</div>
        </div>

        <h1 class="text-center">{{$user->name}}</h1>

        <p class="mb-4">
            Atas partisipasinya sebagai <strong>Peserta</strong> pada kegiatan <strong>{{ $bangkom->event_tema }}</strong> yang diselenggarakan oleh <strong>Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Kabupaten Pekalongan</strong> pada tanggal
            {{ \Carbon\Carbon::parse($bangkom->event_mulai)->translatedFormat('d F Y') }}
            meliputi {{ $bangkom->event_jp }} Jam Pelajaran.
        </p>

        <div class="text-center">
            Kajen, {{ \Carbon\Carbon::parse($bangkom->event_selesai)->translatedFormat('d F Y') }}<br>
            Kepala BKPSDM Kabupaten Pekalongan
        </div>

        <div class="qr-signature">
            <a href="{{ $urlValidasi }}" target="_blank">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($qr)) }}" style="width:100px;">
            </a>
            <br>
            <strong>Suprayitno, S.Sos., M.Si</strong><br>
            NIP. 196602191994011001
        </div>

        <p class="footer-note">
            * Dokumen ini dikeluarkan oleh BKPSDM Kabupaten Pekalongan melalui Bangkompas. Scan Qr untuk validasi keaslian sertifikat.
        </p>
    </div>
</body>

</html>
