<?php
/*************************************************
 * APLIKASI MAHASISWA - SINGLE FILE (PHP + MySQLi)
 * - CRUD mahasiswa
 * - Query soal (d-k)
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
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      min-height: 100vh;
      padding: 20px;
      animation: gradientShift 15s ease infinite;
      background-size: 200% 200%;
    }

    @keyframes gradientShift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    .box {
      max-width: 1100px;
      margin: 0 auto;
      background: white;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25), 0 0 40px rgba(102, 126, 234, 0.15);
      overflow: hidden;
      animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 35px 25px;
      margin: 0;
      font-size: 32px;
      font-weight: 700;
      letter-spacing: 1px;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h3 {
      color: #2c3e50;
      margin: 25px 0 15px 0;
      font-size: 22px;
      font-weight: 700;
      padding: 0 25px;
    }

    nav {
      background: linear-gradient(to right, #f8f9fa 0%, #f0f1f7 100%);
      padding: 20px 25px;
      border-bottom: 1px solid #e0e0f0;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      align-items: center;
    }

    nav a {
      display: inline-block;
      padding: 11px 22px;
      background: white;
      color: #667eea;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 700;
      border: 2px solid #667eea;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }

    nav a::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: #667eea;
      z-index: -1;
      transition: left 0.4s ease;
    }

    nav a:hover {
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    nav a:hover::before {
      left: 0;
    }

    hr {
      border: none;
      height: 2px;
      background: linear-gradient(to right, transparent, #667eea, transparent);
      margin: 0;
    }

    .msg, .err {
      margin: 20px 25px;
      padding: 18px 22px;
      border-radius: 12px;
      font-weight: 600;
      animation: slideDown 0.5s ease-out;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .msg {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      border: 2px solid #28a745;
      color: #155724;
    }

    .msg::before {
      content: '✓';
      font-size: 20px;
      font-weight: bold;
    }

    .err {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      border: 2px solid #dc3545;
      color: #721c24;
    }

    .err::before {
      content: '✕';
      font-size: 20px;
      font-weight: bold;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin: 20px 0;
    }

    th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 18px 15px;
      text-align: left;
      font-weight: 700;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    td {
      padding: 14px 15px;
      border-bottom: 1px solid #f0f0f0;
      color: #333;
      font-size: 14px;
    }

    tr {
      transition: all 0.3s ease;
    }

    tr:hover {
      background-color: #f8f9ff;
      box-shadow: inset 0 0 10px rgba(102, 126, 234, 0.1);
    }

    tr:last-child td {
      border-bottom: none;
    }

    form {
      padding: 30px;
      background: linear-gradient(135deg, #fafbff 0%, #f5f7ff 100%);
      border-radius: 15px;
      margin: 20px 25px;
      border: 1px solid #e0e5ff;
    }

    .row {
      margin-bottom: 22px;
      display: flex;
      flex-direction: column;
      animation: fadeIn 0.5s ease-out forwards;
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
      color: #2c3e50;
      margin-bottom: 10px;
      font-size: 15px;
      text-transform: capitalize;
    }

    input, select {
      padding: 13px 16px;
      border: 2px solid #e0e5ff;
      border-radius: 10px;
      font-size: 14px;
      transition: all 0.3s ease;
      font-family: inherit;
      background: white;
    }

    input:focus, select:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
      transform: translateY(-1px);
    }

    button {
      padding: 14px 35px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: 700;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
      margin-right: 10px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
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
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    button:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
    }

    button:hover::before {
      width: 300px;
      height: 300px;
    }

    button:active {
      transform: translateY(-1px);
    }

    form a {
      display: inline-block;
      padding: 14px 35px;
      background: #e8eef7;
      color: #667eea;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 700;
      transition: all 0.3s ease;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: 2px solid transparent;
    }

    form a:hover {
      background: #667eea;
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .actions a {
      display: inline-block;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    .actions a:first-child {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .actions a:first-child:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .actions a:last-child {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white;
      box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .actions a:last-child:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
    }

    code, pre {
      background: #2d2d2d;
      color: #f8f8f2;
      padding: 16px 18px;
      display: block;
      border-radius: 10px;
      overflow: auto;
      margin: 15px 25px;
      font-family: 'Courier New', monospace;
      font-size: 13px;
      line-height: 1.6;
      border: 1px solid #404040;
    }

    p {
      color: #555;
      line-height: 1.8;
      padding: 0 25px;
      font-size: 15px;
    }

    b {
      color: #2c3e50;
      font-weight: 700;
    }

    @media (max-width: 768px) {
      .box {
        border-radius: 15px;
      }

      h2 {
        font-size: 24px;
        padding: 25px;
      }

      nav {
        flex-direction: column;
      }

      nav a {
        width: 100%;
        text-align: center;
      }

      table {
        font-size: 13px;
      }

      th, td {
        padding: 10px;
      }

      .actions {
        flex-direction: column;
      }

      .actions a {
        width: 100%;
        text-align: center;
      }

      form {
        padding: 20px;
      }

      button, form a {
        width: 100%;
        margin-right: 0;
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
      <a href="?aksi=query">Query Soal</a>
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

    // ====== VIEW: QUERY SOAL (d-k) ======
    elseif ($aksi === 'query') {

      // fungsi tampil tabel dari query
      $show = function($judul, $sql) use ($conn) {
        echo "<h3>".e($judul)."</h3>";
        echo "<pre>".e($sql)."</pre>";
        $res = $conn->query($sql);
        if (!$res) {
          echo "<div class='err'>Query error: ".e($conn->error)."</div>";
          return;
        }
        if ($res->num_rows === 0) {
          echo "<p>(Tidak ada data)</p>";
          return;
        }
        echo "<table><tr>";
        foreach ($res->fetch_fields() as $f) echo "<th>".e($f->name)."</th>";
        echo "</tr>";
        while ($row = $res->fetch_assoc()) {
          echo "<tr>";
          foreach ($row as $v) echo "<td>".e($v)."</td>";
          echo "</tr>";
        }
        echo "</table>";
      };

      echo "<h3>Query Soal</h3>";
      echo "<p>Berikut hasil query sesuai poin d–k pada soal.</p>";

      // (d) pilih mahasiswa sex='P'
      $show("d) Mahasiswa dengan sex = 'P'",
        "SELECT * FROM mahasiswa WHERE sex='P'");

      // (e) ubah jurusan Siti menjadi Sastra (ditampilkan sebagai perintah)
      echo "<h3>e) SQL ubah Prodi Siti menjadi Sastra</h3>";
      echo "<pre>UPDATE mahasiswa SET prodi='Sastra' WHERE nama='Siti';</pre>";

      // (f) prodi unik
      $show("f) Daftar Prodi (unik / tampil sekali)",
        "SELECT DISTINCT prodi FROM mahasiswa ORDER BY prodi ASC");

      // (g) mahasiswa yang sudah kuliah >= 5 tahun
      $show("g) Mahasiswa yang sudah kuliah >= 5 tahun",
        "SELECT nim, nama, prodi, tanggal_masuk
         FROM mahasiswa
         WHERE tanggal_masuk <= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)");

      // (h) jumlah masuk tahun 2002
      $show("h) Jumlah mahasiswa yang masuk tahun 2002",
        "SELECT COUNT(*) AS jumlah
         FROM mahasiswa
         WHERE YEAR(tanggal_masuk)=2002");

      // (i) NIM, Nama, Prodi urut berdasarkan Prodi
      $show("i) NIM, Nama, Prodi diurutkan berdasarkan Prodi",
        "SELECT nim, nama, prodi
         FROM mahasiswa
         ORDER BY prodi ASC, nama ASC");

      // (j) urut tanggal masuk paling lama sampai terbaru
      $show("j) Mahasiswa diurutkan dari tanggal masuk paling lama -> terbaru",
        "SELECT * FROM mahasiswa ORDER BY tanggal_masuk ASC");

      // (k) hitung jumlah mahasiswa jurusan Sipil
      $show("k) Jumlah mahasiswa pada jurusan Sipil",
        "SELECT COUNT(*) AS jumlah_sipil
         FROM mahasiswa
         WHERE prodi='Sipil'");

      echo "<hr>";
      echo "<p><b>Catatan:</b> Hapus data Soni (poin c) dilakukan dari menu <i>Data Mahasiswa</i> lewat tombol <i>Hapus</i>.</p>";
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
