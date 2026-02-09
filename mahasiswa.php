<?php
/*************************************************
 * APLIKASI MAHASISWA - SINGLE FILE (PHP + MySQLi)
 * - CRUD mahasiswa
 *************************************************/

// ====== KONFIG KONEKSI (Soal 4.1) ======
$host = "localhost";
$user = "belajar";
$pass = "rahasia";
$db   = "belajardb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// helper escape
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ====== ROUTING SEDERHANA ======
$aksi = $_GET['aksi'] ?? 'list';     // list | tambah | edit | hapus | query
$msg  = "";

// ====== PROSES TAMBAH ======
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $nim = (int)($_POST['nim'] ?? 0);
  $nama = $conn->real_escape_string($_POST['nama'] ?? '');
  $sex = $conn->real_escape_string($_POST['sex'] ?? 'L');
  $prodi = $conn->real_escape_string($_POST['prodi'] ?? '');
  $tgl = $conn->real_escape_string($_POST['tanggal_masuk'] ?? '');

  if ($nim <= 0 || $nama === '' || $prodi === '' || $tgl === '') {
    $msg = "Gagal: semua field wajib diisi.";
  } else {
    $sql = "INSERT INTO mahasiswa (nim,nama,sex,prodi,tanggal_masuk)
            VALUES ($nim,'$nama','$sex','$prodi','$tgl')";
    if ($conn->query($sql)) {
      header("Location: ?aksi=list&msg=" . urlencode("Berhasil tambah data."));
      exit;
    } else {
      $msg = "Gagal tambah: " . $conn->error;
    }
  }
}

// ====== PROSES UPDATE ======
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $nim = (int)($_POST['nim'] ?? 0);
  $nama = $conn->real_escape_string($_POST['nama'] ?? '');
  $sex = $conn->real_escape_string($_POST['sex'] ?? 'L');
  $prodi = $conn->real_escape_string($_POST['prodi'] ?? '');
  $tgl = $conn->real_escape_string($_POST['tanggal_masuk'] ?? '');

  if ($nim <= 0 || $nama === '' || $prodi === '' || $tgl === '') {
    $msg = "Gagal: semua field wajib diisi.";
  } else {
    $sql = "UPDATE mahasiswa
            SET nama='$nama', sex='$sex', prodi='$prodi', tanggal_masuk='$tgl'
            WHERE nim=$nim";
    if ($conn->query($sql)) {
      header("Location: ?aksi=list&msg=" . urlencode("Berhasil update data."));
      exit;
    } else {
      $msg = "Gagal update: " . $conn->error;
    }
  }
}

// ====== PROSES HAPUS (Soal 3.1c) ======
if ($aksi === 'hapus') {
  $nim = (int)($_GET['nim'] ?? 0);
  if ($nim > 0) {
    $conn->query("DELETE FROM mahasiswa WHERE nim=$nim");
    header("Location: ?aksi=list&msg=" . urlencode("Data terhapus (jika ada)."));
    exit;
  } else {
    $msg = "NIM tidak valid.";
    $aksi = 'list';
  }
}

// pesan dari redirect
if (isset($_GET['msg']) && $msg === "") $msg = $_GET['msg'];

// ====== HTML ======
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Aplikasi Mahasiswa (Single File)</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #0f0f23 0%, #1a1a3f 50%, #16213e 100%);
      min-height: 100vh;
      padding: 20px;
      color: #333;
    }

    .box {
      max-width: 1100px;
      margin: 0 auto;
      background: #fafaf8;
      border-radius: 24px;
      box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3), 0 0 1px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      animation: slideUp 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
      color: #f5deb3;
      padding: 45px 35px;
      margin: 0;
      font-size: 36px;
      font-weight: 800;
      letter-spacing: 2px;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    h3 {
      color: #1a1a2e;
      margin: 28px 0 16px 0;
      font-size: 24px;
      font-weight: 700;
      padding: 0 35px;
    }

    nav {
      background: #f5f5f0;
      padding: 28px 35px;
      border-bottom: 2px solid #e8e8e0;
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      align-items: center;
    }

    nav a {
      display: inline-block;
      padding: 13px 28px;
      background: white;
      color: #1a1a2e;
      text-decoration: none;
      border-radius: 12px;
      font-weight: 700;
      border: 2px solid #d4af37;
      transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
      cursor: pointer;
      position: relative;
      overflow: hidden;
      font-size: 14px;
      letter-spacing: 0.5px;
    }

    nav a::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: #d4af37;
      z-index: -1;
      transition: left 0.35s ease;
    }

    nav a:hover {
      color: white;
      transform: translateY(-4px);
      box-shadow: 0 15px 35px rgba(212, 175, 55, 0.3);
    }

    nav a:hover::before {
      left: 0;
    }

    hr {
      border: none;
      height: 1px;
      background: linear-gradient(to right, transparent, #d4af37, transparent);
      margin: 0;
    }

    .msg, .err {
      margin: 24px 35px;
      padding: 18px 24px;
      border-radius: 14px;
      font-weight: 600;
      animation: slideDown 0.6s ease-out;
      display: flex;
      align-items: center;
      gap: 14px;
      border-left: 5px solid;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-25px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .msg {
      background: #f0fdf4;
      border-left-color: #22c55e;
      color: #166534;
    }

    .msg::before {
      content: '✓';
      font-size: 22px;
      font-weight: bold;
    }

    .err {
      background: #fef2f2;
      border-left-color: #ef4444;
      color: #7f1d1d;
    }

    .err::before {
      content: '✕';
      font-size: 22px;
      font-weight: bold;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin: 24px 0;
    }

    th {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
      color: #f5deb3;
      padding: 18px 20px;
      text-align: left;
      font-weight: 700;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    td {
      padding: 16px 20px;
      border-bottom: 1px solid #f0f0f0;
      color: #444;
      font-size: 14px;
    }

    tr {
      transition: all 0.3s ease;
    }

    tr:hover {
      background-color: #fffbf5;
      box-shadow: inset 0 0 15px rgba(212, 175, 55, 0.08);
    }

    tr:last-child td {
      border-bottom: none;
    }

    form {
      padding: 36px;
      background: #fffbf5;
      border-radius: 16px;
      margin: 24px 35px;
      border: 1px solid #f0e6d2;
    }

    .row {
      margin-bottom: 24px;
      display: flex;
      flex-direction: column;
      animation: fadeIn 0.6s ease-out forwards;
      opacity: 0;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }

    .row:nth-child(1) { animation-delay: 0.1s; }
    .row:nth-child(2) { animation-delay: 0.2s; }
    .row:nth-child(3) { animation-delay: 0.3s; }
    .row:nth-child(4) { animation-delay: 0.4s; }
    .row:nth-child(5) { animation-delay: 0.5s; }

    .row label {
      font-weight: 700;
      color: #1a1a2e;
      margin-bottom: 10px;
      font-size: 15px;
      text-transform: capitalize;
      letter-spacing: 0.3px;
    }

    input, select {
      padding: 14px 18px;
      border: 2px solid #e8e8e0;
      border-radius: 10px;
      font-size: 14px;
      transition: all 0.3s ease;
      font-family: inherit;
      background: white;
      color: #333;
    }

    input:focus, select:focus {
      outline: none;
      border-color: #d4af37;
      box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
      transform: translateY(-2px);
    }

    button {
      padding: 14px 36px;
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
      color: #f5deb3;
      border: none;
      border-radius: 10px;
      font-weight: 700;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      margin-right: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      position: relative;
      overflow: hidden;
    }

    button::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: #d4af37;
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
      z-index: 0;
    }

    button {
      position: relative;
      z-index: 1;
    }

    button:hover {
      transform: translateY(-5px);
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
    }

    button:hover::before {
      width: 300px;
      height: 300px;
    }

    button:active {
      transform: translateY(-2px);
    }

    form a {
      display: inline-block;
      padding: 14px 36px;
      background: #f5f5f0;
      color: #1a1a2e;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 700;
      transition: all 0.3s ease;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: 1px;
      border: 2px solid #e8e8e0;
    }

    form a:hover {
      background: #d4af37;
      color: white;
      border-color: #d4af37;
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
    }

    .actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .actions a {
      display: inline-block;
      padding: 9px 18px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .actions a:first-child {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
      color: #f5deb3;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .actions a:first-child:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    .actions a:last-child {
      background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
      color: white;
      box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
    }

    .actions a:last-child:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(229, 62, 62, 0.4);
    }

    code, pre {
      background: #1a1a2e;
      color: #f5deb3;
      padding: 18px 20px;
      display: block;
      border-radius: 10px;
      overflow: auto;
      margin: 18px 35px;
      font-family: 'Monaco', 'Courier New', monospace;
      font-size: 13px;
      line-height: 1.7;
      border: 1px solid #2a2a4e;
    }

    p {
      color: #666;
      line-height: 1.8;
      padding: 0 35px;
      font-size: 15px;
    }

    b {
      color: #1a1a2e;
      font-weight: 700;
    }

    @media (max-width: 768px) {
      .box {
        border-radius: 16px;
      }

      h2 {
        font-size: 28px;
        padding: 30px 25px;
      }

      nav {
        flex-direction: column;
        padding: 20px;
      }

      nav a {
        width: 100%;
        text-align: center;
      }

      table {
        font-size: 13px;
      }

      th, td {
        padding: 12px;
      }

      .actions {
        flex-direction: column;
      }

      .actions a {
        width: 100%;
        text-align: center;
      }

      form {
        padding: 24px;
        margin: 20px;
      }

      button, form a {
        width: 100%;
        margin-right: 0;
        margin-bottom: 10px;
      }

      h3 {
        padding: 0 20px;
      }

      p {
        padding: 0 20px;
      }
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Aplikasi Mahasiswa (Single File PHP)</h2>
    <nav>
      <a href="?aksi=list">Data Mahasiswa</a>
      <a href="?aksi=form_tambah">Tambah</a>
    </nav>
    <hr>

    <?php if ($msg): ?>
      <div class="<?= (stripos($msg, 'gagal') !== false || stripos($msg, 'error') !== false) ? 'err' : 'msg' ?>">
        <?= e($msg) ?>
      </div>
    <?php endif; ?>

    <?php
    // ====== VIEW: LIST ======
    if ($aksi === 'list') {
      $res = $conn->query("SELECT * FROM mahasiswa ORDER BY nim ASC");
      echo "<h3>Daftar Mahasiswa</h3>";
      echo "<table>";
      echo "<tr><th>NIM</th><th>Nama</th><th>Jenis Kelamin</th><th>Prodi</th><th>Tanggal Masuk</th><th>Aksi</th></tr>";
      while ($row = $res->fetch_assoc()) {
        $nim = (int)$row['nim'];
        echo "<tr>";
        echo "<td>".e($row['nim'])."</td>";
        echo "<td>".e($row['nama'])."</td>";
        echo "<td>".e($row['sex'])."</td>";
        echo "<td>".e($row['prodi'])."</td>";
        echo "<td>".e($row['tanggal_masuk'])."</td>";
        echo "<td class='actions'>
                <a href='?aksi=form_edit&nim=$nim'>Edit</a>
                <a href='?aksi=hapus&nim=$nim' onclick=\"return confirm('Yakin hapus NIM $nim?')\">Hapus</a>
              </td>";
        echo "</tr>";
      }
      echo "</table>";
    }

    // ====== VIEW: FORM TAMBAH ======
    elseif ($aksi === 'form_tambah') {
      ?>
      <h3>Tambah Mahasiswa</h3>
      <form method="post" action="?aksi=tambah">
        <div class="row">
          <label>NIM</label>
          <input type="number" name="nim" required>
        </div>
        <div class="row">
          <label>Nama</label>
          <input type="text" name="nama" required>
        </div>
        <div class="row">
          <label>Jenis Kelamin</label>
          <select name="sex" required>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>
        <div class="row">
          <label>Program Studi</label>
          <input type="text" name="prodi" required>
        </div>
        <div class="row">
          <label>Tanggal Masuk</label>
          <input type="date" name="tanggal_masuk" required>
        </div>
        <button type="submit">Simpan</button>
        <a href="?aksi=list">Batal</a>
      </form>
      <?php
    }

    // ====== VIEW: FORM EDIT ======
    elseif ($aksi === 'form_edit') {
      $nim = (int)($_GET['nim'] ?? 0);
      $res = $conn->query("SELECT * FROM mahasiswa WHERE nim=$nim");
      $data = $res ? $res->fetch_assoc() : null;

      if (!$data) {
        echo "<div class='err'>Data tidak ditemukan.</div>";
      } else {
        ?>
        <h3>Edit Mahasiswa (NIM: <?= e($data['nim']) ?>)</h3>
        <form method="post" action="?aksi=edit">
          <input type="hidden" name="nim" value="<?= (int)$data['nim'] ?>">
          <div class="row">
            <label>Nama</label>
            <input type="text" name="nama" value="<?= e($data['nama']) ?>" required>
          </div>
          <div class="row">
            <label>Jenis Kelamin</label>
            <select name="sex" required>
              <option value="L" <?= $data['sex']=='L'?'selected':'' ?>>Laki-laki</option>
              <option value="P" <?= $data['sex']=='P'?'selected':'' ?>>Perempuan</option>
            </select>
          </div>
          <div class="row">
            <label>Program Studi</label>
            <input type="text" name="prodi" value="<?= e($data['prodi']) ?>" required>
          </div>
          <div class="row">
            <label>Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" value="<?= e($data['tanggal_masuk']) ?>" required>
          </div>
          <button type="submit">Update</button>
          <a href="?aksi=list">Batal</a>
        </form>
        <?php
      }
    }

    // default fallback
    else {
      header("Location: ?aksi=list");
      exit;
    }
    ?>
  </div>
</body>
</html>
