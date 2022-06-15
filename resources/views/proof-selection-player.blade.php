<!doctype html>
<html lang="en">

<head>
    <title>Bukti Pendaftaran</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        table {
            font-size: 12px;
        }

        ul li:last-child {
            border-bottom: none
        }

        ol,
        ul {
            columns: 1;
            -webkit-column-break-inside: avoid;
        }

        li {
            border-bottom: 1px solid silver;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }

        .logo img {
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
        }

        .title {
            margin-bottom: 3rem;
        }

        .profile-image {
            width: 250px;
            height: 250px;
            text-align: center;
            background-size: cover;
            margin: auto;
            margin-bottom: 6rem;
        }

        .profile-image img {
            max-width: 30%;
            margin-bottom: 6rem;
        }

    </style>
</head>

<body>
    <div class="container-fluid relative">
        <div class="title text-center">
            <h4 class="card-title font-weight-bold mb-1">BUKTI PENDAFTARAN</h4>
            <h5 class="card-title font-weight-bold mb-1">CALON PESERTA GOALKEEPER BATTLE</h5>
            <h5 class="card-title font-weight-bold">BINTANG TIMUR SURABAYA</h5>
            <h5 class="card-title">30-31 Oktober 2021</h5>
        </div>
        <div class="logo">
            <img src="https://api.semanggi.app/web/public/attachment_company/logo/20210123074428.png" />
        </div>
        <div class="profile-image" style="background-image: url({{$user["profileImage"]}})">
            {{-- <img src={{ $user["profileImage"] }} /> --}}
        </div>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td class="w-50">Nama Calon Peserta</td>
                    <td>{{ $user["fullName"] }}</td>
                </tr>
                <tr>
                    <td class="w-50">Tempat, Tanggal Lahir</td>
                    <td>{{ $user["tempatLahir"] }}, {{$user["tglLahir"]}}</td>
                </tr>
                <tr>
                    <td class="w-50">Nomor Telepon</td>
                    <td>{{ $user["phone"] }}</td>
                </tr>
                <tr>
                    <td class="w-50">Berat Badan</td>
                    <td>{{ $user["beratBadan"] }}</td>
                </tr>
                <tr>
                    <td class="w-50">Tinggi Badan</td>
                    <td>{{ $user["tinggiBadan"] }}</td>
                </tr>
                <tr>
                    <td class="w-50">Posisi</td>
                    <td>{{ $user["posisi"] }}</td>
                </tr>
                <tr>
                    <td class="w-50">Kaki Dominan</td>
                    <td>{{ $user["kakiDominan"] }}</td>
                </tr>
                <tr>
                    <td class="w-50">Sekolah/Bekerja</td>
                    <td>{{ $user["kesibukan"] }}</td>
                </tr>
                <tr>
                    <td class="w-50">Tempat Sekolah/Bekerja</td>
                    <td>{{ $user["tempatKesibukan"] }}</td>
                </tr>
                <tr>
                    <td class="w-50">Waktu pulang Sekolah/Bekerja</td>
                    <td>{{ $user["waktuKesibukan"] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
