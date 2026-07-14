<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengguna LMS Kampus Malang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #1E40AF;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #111;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .role-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .role-admin { background-color: #E0E7FF; color: #4338CA; }
        .role-dosen { background-color: #FEF3C7; color: #92400E; }
        .role-mahasiswa { background-color: #D1FAE5; color: #065F46; }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
            }
        }
        .print-btn {
            background-color: #2563EB;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .print-btn:hover {
            background-color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="print-btn" onclick="window.print()"><i class="fas fa-print"></i> Cetak / Simpan sebagai PDF</button>
    </div>

    <div class="header">
        <h1>UNIVERSITAS KAMPUS MALANG</h1>
        <p>Laporan Data Pengguna Sistem Pembelajaran Online (LMS)</p>
        <div style="font-size: 10px; margin-top: 8px; color: #888;">Dicetak pada: {{ now()->translatedFormat('l, d F Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Nomor Induk</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 25%;">Email</th>
                <th style="width: 10%;">Role</th>
                <th style="width: 12%;">Program Studi</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $u)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $u->nomor_induk }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        <span class="role-badge role-{{ $u->role }}">
                            {{ $u->role }}
                        </span>
                    </td>
                    <td>{{ $u->prodi ? $u->prodi->nama : ($u->jurusan ?: '-') }}</td>
                    <td style="color: {{ $u->is_active ? 'green' : 'red' }}; font-weight: bold;">
                        {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Malang, ____________________</p>
        <p style="margin-top: 60px; font-weight: bold;">LMS Administrator</p>
    </div>

    <script>
        // Auto trigger print window on load
        window.addEventListener('DOMContentLoaded', () => {
            // Uncomment to auto-print when opened
            // window.print();
        });
    </script>
</body>
</html>
