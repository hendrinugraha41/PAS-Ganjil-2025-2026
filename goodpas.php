<?php 
// index.php - versi PHP dari portal HTML
// Simpan file ini di: C:\xampp\htdocs\portal_penilaian\index.php

// --- Konfigurasi default yang bisa kamu ubah di sini ---
$defaultConfig = [
    "school_name"    => "SMP Negeri 18 Samarinda",
    "portal_title"   => "Portal Penilaian Akhir Semester",
    "semester_info"  => "Ujian Semester Ganjil Tahun Ajaran 2005/2006",
    "grade_7_button" => "Kelas 7",
    "grade_8_button" => "Kelas 8",
    "grade_9_button" => "Kelas 9",
    "admin_button_text" => "Admin",
    "primary_color"  => "#3b82f6",
    "secondary_color"=> "#ffffff",
    "text_color"     => "#1e293b",
    "accent_color"   => "#8b5cf6",
    "button_color"   => "#10b981",
    "font_family"    => "Segoe UI",
    "font_size"      => 16
];

$defaultSubjects = [
    // grade 7
    ["grade"=>"7","subject_name"=>"Matematika","form_link"=>"","order"=>1,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"Bahasa Indonesia","form_link"=>"","order"=>2,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"Bahasa Inggris","form_link"=>"","order"=>3,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"IPA","form_link"=>"","order"=>4,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"IPS","form_link"=>"","order"=>5,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"Pendidikan Agama","form_link"=>"","order"=>6,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"PKn","form_link"=>"","order"=>7,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"Seni Budaya","form_link"=>"","order"=>8,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"Penjasorkes","form_link"=>"","order"=>9,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"Prakarya","form_link"=>"","order"=>10,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"Bahasa Daerah","form_link"=>"","order"=>11,"enabled"=>true],
    ["grade"=>"7","subject_name"=>"TIK","form_link"=>"","order"=>12,"enabled"=>true],
    // grade 8
    ["grade"=>"8","subject_name"=>"Matematika","form_link"=>"","order"=>1,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"Bahasa Indonesia","form_link"=>"","order"=>2,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"Bahasa Inggris","form_link"=>"","order"=>3,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"IPA","form_link"=>"","order"=>4,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"IPS","form_link"=>"","order"=>5,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"Pendidikan Agama","form_link"=>"","order"=>6,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"PKn","form_link"=>"","order"=>7,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"Seni Budaya","form_link"=>"","order"=>8,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"Penjasorkes","form_link"=>"","order"=>9,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"Prakarya","form_link"=>"","order"=>10,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"Bahasa Daerah","form_link"=>"","order"=>11,"enabled"=>true],
    ["grade"=>"8","subject_name"=>"TIK","form_link"=>"","order"=>12,"enabled"=>true],
    // grade 9
    ["grade"=>"9","subject_name"=>"Matematika","form_link"=>"","order"=>1,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"Bahasa Indonesia","form_link"=>"","order"=>2,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"Bahasa Inggris","form_link"=>"","order"=>3,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"IPA","form_link"=>"","order"=>4,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"IPS","form_link"=>"","order"=>5,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"Pendidikan Agama","form_link"=>"","order"=>6,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"PKn","form_link"=>"","order"=>7,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"Seni Budaya","form_link"=>"","order"=>8,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"Penjasorkes","form_link"=>"","order"=>9,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"Prakarya","form_link"=>"","order"=>10,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"Bahasa Daerah","form_link"=>"","order"=>11,"enabled"=>true],
    ["grade"=>"9","subject_name"=>"TIK","form_link"=>"","order"=>12,"enabled"=>true]
];

// --- End of PHP config ---
// --- Persistence handler: baca/tulis subjects ke file JSON ---
$__DATA_FILE = __DIR__ . '/subjects_data.json';

// Jika ada permintaan POST untuk menyimpan data subjects (application/json)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true);
    $action = null;
    if (isset($body['action'])) $action = $body['action'];
    if (!$action && isset($_POST['action'])) $action = $_POST['action'];

    if ($action === 'save_subjects') {
        $subjects = null;
        if (isset($body['subjects']) && is_array($body['subjects'])) {
            $subjects = $body['subjects'];
        } elseif (isset($_POST['subjects'])) {
            $subjects = $_POST['subjects'];
            if (is_string($subjects)) {
                $decoded = json_decode($subjects, true);
                if (is_array($decoded)) $subjects = $decoded;
            }
        }
        if (!is_array($subjects)) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success'=>false,'message'=>'Invalid subjects payload']);
            exit;
        }
        // Pastikan setiap subject memiliki enabled (default true) dan __backendId bila perlu
        foreach ($subjects as &$s) {
            if (!isset($s['enabled'])) $s['enabled'] = true;
            if (!isset($s['__backendId'])) {
                $s['__backendId'] = (isset($s['id']) ? $s['id'] : 'sub-'.uniqid());
            }
        }
        $ok = @file_put_contents($__DATA_FILE, json_encode($subjects, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        if ($ok === false) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['success'=>false,'message'=>'Gagal menulis file pada server. Pastikan PHP memiliki izin menulis pada direktori.']);
            exit;
        }
        header('Content-Type: application/json', true, 200);
        echo json_encode(['success'=>true,'message'=>'Subjects tersimpan.']);
        exit;
    }
}

// Saat memuat halaman, jika file JSON ada, gunakan sebagai sumber kebenaran
if (file_exists($__DATA_FILE)) {
    $json = @file_get_contents($__DATA_FILE);
    $decoded = json_decode($json, true);
    if (is_array($decoded) && count($decoded)>0) {
        // pastikan struktur lengkap (enabled dan __backendId)
        foreach ($decoded as &$s) {
            if (!isset($s['enabled'])) $s['enabled'] = true;
            if (!isset($s['__backendId'])) $s['__backendId'] = (isset($s['id']) ? $s['id'] : 'sub-'.uniqid());
        }
        $defaultSubjects = $decoded;
    }
}

// --- End persistence handler ---

?><!doctype html>
<html lang="id">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($defaultConfig['portal_title']); ?></title>
  <script src="/_sdk/data_sdk.js"></script>
  <script src="/_sdk/element_sdk.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { box-sizing: border-box; margin:0; padding:0; height:100%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    html { height:100%; }
    .subject-btn { transition: all 0.3s ease; }
    .subject-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .subject-btn:active { transform: translateY(0); }
    .subject-btn.clicked { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); }
    .grade-card { transition: all 0.3s ease; }
    .grade-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
    .badge-nonaktif { background: #fde68a; color: #92400e; padding: 2px 8px; border-radius: 999px; font-size: 0.8em; margin-left:8px; }
  </style>
  <style>@view-transition { navigation: auto; }</style>
 </head>
 <body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-full">
  <div id="app" class="min-h-full p-6"></div>

  <script>
    // Konfigurasi di-inject dari PHP (ubah di atas, bukan di sini)
    const defaultConfig = <?php echo json_encode($defaultConfig, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>;
    const defaultSubjects = <?php echo json_encode($defaultSubjects, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>;

    // Variabel runtime
    let allSubjects = [];
    let currentView = 'home';
    let selectedGrade = null;
    let isLoggedIn = false;
    let adminUsername = 'admin';
    let adminPassword = 'jurkunsis';

    // Utility: kirim perubahan ke server (global)
    async function sendSaveRequest(subjects) {
      try {
        const res = await fetch(location.href, {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({action: 'save_subjects', subjects})
        });
        const data = await res.json();
        if (res.ok && data.success) {
          return {ok:true, message: data.message || 'Tersimpan'};
        } else {
          return {ok:false, message: data.message || 'Gagal menyimpan ke server'};
        }
      } catch (err) {
        console.error('save error', err);
        return {ok:false, message: err.message || err};
      }
    }

    const dataHandler = {
      onDataChanged(data) {
        allSubjects = data;
        render();
      }
    };

    function render() {
      const app = document.getElementById('app');
      const config = window.elementSdk?.config || defaultConfig;
      const customFont = config.font_family || defaultConfig.font_family;
      const baseSize = config.font_size || defaultConfig.font_size;
      const baseFontStack = 'Arial, sans-serif';

      if (currentView === 'home') {
        app.innerHTML = `
          <div class="max-w-6xl mx-auto">
            <div class="text-center mb-8">
              <div class="inline-block p-4 rounded-lg mb-4" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h1 id="school-name" class="font-bold mb-2" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 2}px;">
                  ${config.school_name || defaultConfig.school_name}
                </h1>
                <h2 id="portal-title" class="font-semibold mb-2" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.5}px;">
                  ${config.portal_title || defaultConfig.portal_title}
                </h2>
                <p id="semester-info" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                  ${config.semester_info || defaultConfig.semester_info}
                </p>
              </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mb-8">
              <div class="grade-card p-6 rounded-xl cursor-pointer" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" onclick="showGrade('7')">
                <div class="text-center">
                  <div class="text-5xl mb-4">📚</div>
                  <h3 class="font-bold mb-2" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.5}px;">
                    ${config.grade_7_button || defaultConfig.grade_7_button}
                  </h3>
                  <p style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
                    12 Mata Pelajaran
                  </p>
                </div>
              </div>

              <div class="grade-card p-6 rounded-xl cursor-pointer" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" onclick="showGrade('8')">
                <div class="text-center">
                  <div class="text-5xl mb-4">📖</div>
                  <h3 class="font-bold mb-2" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.5}px;">
                    ${config.grade_8_button || defaultConfig.grade_8_button}
                  </h3>
                  <p style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
                    12 Mata Pelajaran
                  </p>
                </div>
              </div>

              <div class="grade-card p-6 rounded-xl cursor-pointer" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" onclick="showGrade('9')">
                <div class="text-center">
                  <div class="text-5xl mb-4">🎓</div>
                  <h3 class="font-bold mb-2" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.5}px;">
                    ${config.grade_9_button || defaultConfig.grade_9_button}
                  </h3>
                  <p style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
                    12 Mata Pelajaran
                  </p>
                </div>
              </div>
            </div>

            <div class="text-center">
              <button onclick="showLogin()" class="px-6 py-3 rounded-lg font-semibold transition-all hover:opacity-90" style="background-color: ${config.accent_color || defaultConfig.accent_color}; color: ${config.secondary_color || defaultConfig.secondary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                ⚙️ ${config.admin_button_text || defaultConfig.admin_button_text}
              </button>
            </div>
          </div>
        `;
      } else if (currentView === 'grade') {
        const gradeSubjects = allSubjects.filter(s => s.grade === selectedGrade).sort((a, b) => a.order - b.order);
        
        if (gradeSubjects.length === 0) {
          const defaultGradeSubjects = defaultSubjects.filter(s => s.grade === selectedGrade);
          app.innerHTML = `
            <div class="max-w-4xl mx-auto">
              <button onclick="backToHome()" class="mb-6 px-4 py-2 rounded-lg font-semibold" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
                ← Kembali
              </button>

              <div class="p-6 rounded-xl mb-6" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h2 class="font-bold text-center mb-2" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.8}px;">
                  Kelas ${selectedGrade}
                </h2>
                <p class="text-center" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                  Pilih mata pelajaran untuk memulai ujian
                </p>
              </div>

              <div class="grid md:grid-cols-2 gap-4">
                ${defaultGradeSubjects.map(subject => `
                  <div class="p-4 rounded-lg text-center" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; color: ${config.text_color || defaultConfig.text_color}; opacity: 0.6; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                    📝 ${subject.subject_name}
                    <div style="font-size: ${baseSize * 0.8}px; margin-top: 8px;">Link belum tersedia</div>
                  </div>
                `).join('')}
              </div>
            </div>
          `;
        } else {
          app.innerHTML = `
            <div class="max-w-4xl mx-auto">
              <button onclick="backToHome()" class="mb-6 px-4 py-2 rounded-lg font-semibold" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
                ← Kembali
              </button>

              <div class="p-6 rounded-xl mb-6" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h2 class="font-bold text-center mb-2" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.8}px;">
                  Kelas ${selectedGrade}
                </h2>
                <p class="text-center" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                  Pilih mata pelajaran untuk memulai ujian
                </p>
              </div>

              <div class="grid md:grid-cols-2 gap-4">
                ${gradeSubjects.map(subject => {
                  const isEnabled = (typeof subject.enabled === 'undefined') ? true : subject.enabled;
                  const disabledAttr = (!subject.form_link || !isEnabled) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '';
                  const badge = (!isEnabled) ? '<span class="badge-nonaktif">Non-aktif</span>' : '';
                  return `
                  <button 
                    class="subject-btn p-4 rounded-lg font-semibold text-left"
                    style="background-color: ${config.button_color || defaultConfig.button_color}; color: ${config.secondary_color || defaultConfig.secondary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                    onclick="openSubject('${subject.__backendId || ''}', '${subject.form_link || ''}')"
                    ${disabledAttr}
                  >
                    📝 ${subject.subject_name} ${badge}
                    ${!subject.form_link ? '<div style="font-size: ' + (baseSize * 0.8) + 'px; margin-top: 8px;">Link belum tersedia</div>' : ''}
                  </button>
                `}).join('')}
              </div>
            </div>
          `;
        }
      } else if (currentView === 'login') {
        app.innerHTML = `
          <div class="max-w-md mx-auto">
            <button onclick="backToHome()" class="mb-6 px-4 py-2 rounded-lg font-semibold" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
              ← Kembali
            </button>

            <div class="p-8 rounded-xl" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <h2 class="font-bold text-center mb-6" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.8}px;">
                Login Admin
              </h2>

              <form onsubmit="handleLogin(event)">
                <div class="mb-4">
                  <label for="username" class="block mb-2 font-semibold" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                    Username
                  </label>
                  <input 
                    type="text" 
                    id="username" 
                    class="w-full p-3 rounded-lg border-2"
                    style="border-color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                    required
                  >
                </div>

                <div class="mb-6">
                  <label for="password" class="block mb-2 font-semibold" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                    Password
                  </label>
                  <input 
                    type="password" 
                    id="password" 
                    class="w-full p-3 rounded-lg border-2"
                    style="border-color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                    required
                  >
                </div>

                <div id="login-error" class="mb-4 p-3 rounded-lg hidden" style="background-color: #fee2e2; color: #991b1b; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;"></div>

                <button 
                  type="submit" 
                  class="w-full py-3 rounded-lg font-semibold"
                  style="background-color: ${config.button_color || defaultConfig.button_color}; color: ${config.secondary_color || defaultConfig.secondary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                >
                  Masuk
                </button>
              </form>

              <div class="mt-6 p-4 rounded-lg" style="background-color: #dbeafe; color: #1e40af; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.85}px;">
                <p class="font-semibold mb-1">Default Login:</p>
                <p>Username: admin</p>
                <p>Password: admin123</p>
              </div>
            </div>
          </div>
        `;
      } else if (currentView === 'admin') {
        app.innerHTML = `
          <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
              <button onclick="logout()" class="px-4 py-2 rounded-lg font-semibold" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
                ← Logout
              </button>
              <button onclick="showSettings()" class="px-4 py-2 rounded-lg font-semibold" style="background-color: ${config.accent_color || defaultConfig.accent_color}; color: ${config.secondary_color || defaultConfig.secondary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
                ⚙️ Pengaturan Login
              </button>
            </div>

            <div class="p-6 rounded-xl mb-6" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <h2 class="font-bold text-center" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.8}px;">
                Panel Admin
              </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
              ${['7', '8', '9'].map(grade => {
                const gradeSubjects = allSubjects.filter(s => s.grade === grade).sort((a, b) => a.order - b.order);
                const displaySubjects = gradeSubjects.length > 0 ? gradeSubjects : defaultSubjects.filter(s => s.grade === grade);
                
                return `
                  <div class="p-6 rounded-xl" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 class="font-bold mb-4 text-center" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.3}px;">
                      Kelas ${grade}
                    </h3>
                    <div class="space-y-3">
                      ${displaySubjects.map(subject => `
                        <div class="p-3 rounded-lg" style="background-color: #f1f5f9;">
                          <div class="flex justify-between items-center mb-2">
                            <div class="font-semibold" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
                              ${subject.subject_name}
                            </div>
                            <div>
                              ${((typeof subject.enabled === 'undefined') || subject.enabled) ? '<small style="color:#065f46;">Aktif</small>' : '<small style="color:#92400e;">Non-aktif</small>'}
                            </div>
                          </div>
                          <div class="flex gap-2">
                            <button 
                              onclick="toggleEnabled('${subject.__backendId || ''}')"
                              class="flex-1 py-2 rounded-lg font-semibold text-sm"
                              style="background-color: ${config.accent_color || defaultConfig.accent_color}; color: ${config.secondary_color || defaultConfig.secondary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.85}px;"
                            >
                              ⚡ Toggle Aktif
                            </button>
                            <button 
                              onclick="editSubject('${subject.__backendId || ''}', '${grade}', '${subject.subject_name.replace(/'/g, "\\'")}', '${subject.form_link || ''}', ${subject.order}, ${subject.enabled ? 'true' : 'false'})"
                              class="py-2 px-3 rounded-lg font-semibold text-sm"
                              style="background-color: ${config.button_color || defaultConfig.button_color}; color: ${config.secondary_color || defaultConfig.secondary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.85}px;"
                            >
                              ✏️ Edit
                            </button>
                          </div>
                        </div>
                      `).join('')}
                    </div>
                  </div>
                `;
              }).join('')}
            </div>
          </div>
        `;
      } else if (currentView === 'edit') {
        const subject = allSubjects.find(s => s.__backendId === window.editingSubjectId);
        const displaySubject = subject || window.editingSubjectData;
        const isEnabled = (typeof displaySubject.enabled === 'undefined') ? true : displaySubject.enabled;
        
        app.innerHTML = `
          <div class="max-w-2xl mx-auto">
            <button onclick="backToAdmin()" class="mb-6 px-4 py-2 rounded-lg font-semibold" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
              ← Kembali
            </button>

            <div class="p-8 rounded-xl" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <h2 class="font-bold text-center mb-6" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.8}px;">
                Edit Mata Pelajaran
              </h2>

              <form onsubmit="handleSaveSubject(event)">
                <div class="mb-4">
                  <label for="subject-name" class="block mb-2 font-semibold" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                    Nama Mata Pelajaran
                  </label>
                  <input 
                    type="text" 
                    id="subject-name" 
                    class="w-full p-3 rounded-lg border-2"
                    style="border-color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                    value="${(displaySubject.subject_name||'').replace(/"/g, '&quot;')}"
                    required
                  >
                </div>

                <div class="mb-6">
                  <label for="form-link" class="block mb-2 font-semibold" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                    Link Google Form
                  </label>
                  <input 
                    type="url" 
                    id="form-link" 
                    class="w-full p-3 rounded-lg border-2"
                    style="border-color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                    value="${displaySubject.form_link || ''}"
                    placeholder="https://forms.gle/..."
                  >
                  <p class="mt-2" style="color: ${config.text_color || defaultConfig.text_color}; opacity: 0.7; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.85}px;">
                    Masukkan link lengkap Google Form
                  </p>
                </div>

                <div class="mb-6">
                  <label class="flex items-center gap-3">
                    <input type="checkbox" id="enabled-checkbox" ${isEnabled ? 'checked' : ''} />
                    <span class="font-medium" style="font-size:${baseSize}px;">Aktifkan link (aktif/non-aktif)</span>
                  </label>
                </div>

                <div id="save-message" class="mb-4 p-3 rounded-lg hidden"></div>

                <button 
                  type="submit" 
                  id="save-btn"
                  class="w-full py-3 rounded-lg font-semibold"
                  style="background-color: ${config.button_color || defaultConfig.button_color}; color: ${config.secondary_color || defaultConfig.secondary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                >
                  💾 Simpan Perubahan
                </button>
              </form>
            </div>
          </div>
        `;
      } else if (currentView === 'settings') {
        app.innerHTML = `
          <div class="max-w-2xl mx-auto">
            <button onclick="backToAdmin()" class="mb-6 px-4 py-2 rounded-lg font-semibold" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 0.9}px;">
              ← Kembali
            </button>

            <div class="p-8 rounded-xl" style="background-color: ${config.secondary_color || defaultConfig.secondary_color}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <h2 class="font-bold text-center mb-6" style="color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize * 1.8}px;">
                Pengaturan Login Admin
              </h2>

              <form onsubmit="handleUpdateCredentials(event)">
                <div class="mb-4">
                  <label for="new-username" class="block mb-2 font-semibold" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                    Username Baru
                  </label>
                  <input 
                    type="text" 
                    id="new-username" 
                    class="w-full p-3 rounded-lg border-2"
                    style="border-color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                    value="${adminUsername}"
                    required
                  >
                </div>

                <div class="mb-6">
                  <label for="new-password" class="block mb-2 font-semibold" style="color: ${config.text_color || defaultConfig.text_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;">
                    Password Baru
                  </label>
                  <input 
                    type="password" 
                    id="new-password" 
                    class="w-full p-3 rounded-lg border-2"
                    style="border-color: ${config.primary_color || defaultConfig.primary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                    value="${adminPassword}"
                    required
                  >
                </div>

                <div id="settings-message" class="mb-4 p-3 rounded-lg hidden"></div>

                <button 
                  type="submit" 
                  class="w-full py-3 rounded-lg font-semibold"
                  style="background-color: ${config.button_color || defaultConfig.button_color}; color: ${config.secondary_color || defaultConfig.secondary_color}; font-family: ${customFont}, ${baseFontStack}; font-size: ${baseSize}px;"
                >
                  💾 Simpan Pengaturan
                </button>
              </form>
            </div>
          </div>
        `;
      }
    }

    function showGrade(grade) { currentView = 'grade'; selectedGrade = grade; render(); }
    function backToHome() { currentView = 'home'; selectedGrade = null; render(); }
    function showLogin() { currentView = 'login'; render(); }

    function handleLogin(event) {
      event.preventDefault();
      const username = document.getElementById('username').value;
      const password = document.getElementById('password').value;
      const errorDiv = document.getElementById('login-error');

      if (username === adminUsername && password === adminPassword) {
        isLoggedIn = true;
        currentView = 'admin';
        render();
      } else {
        errorDiv.textContent = 'Username atau password salah!';
        errorDiv.classList.remove('hidden');
      }
    }

    function logout() { isLoggedIn = false; currentView = 'home'; render(); }
    function backToAdmin() { currentView = 'admin'; render(); }

    function editSubject(id, grade, name, link, order, enabled = true) {
      window.editingSubjectId = id;
      window.editingSubjectData = { grade, subject_name: name, form_link: link, order, enabled };
      currentView = 'edit';
      render();
    }

    async function handleSaveSubject(event) {
      event.preventDefault();
      const saveBtn = document.getElementById('save-btn');
      const messageDiv = document.getElementById('save-message');
      
      if (!saveBtn) return;
      saveBtn.disabled = true;
      saveBtn.textContent = 'Menyimpan...';
      
      const newName = document.getElementById('subject-name').value;
      const newLink = document.getElementById('form-link').value;
      const newEnabled = document.getElementById('enabled-checkbox').checked;

      const existingSubject = allSubjects.find(s => s.__backendId === window.editingSubjectId);

      if (existingSubject) {
        existingSubject.subject_name = newName;
        existingSubject.form_link = newLink;
        existingSubject.enabled = newEnabled;
        const res = await sendSaveRequest(allSubjects);
        if (res.ok) {
          messageDiv.textContent = 'Perubahan berhasil disimpan (server).';
          messageDiv.style.backgroundColor = '#d1fae5';
          messageDiv.style.color = '#065f46';
          messageDiv.classList.remove('hidden');
          setTimeout(()=>{ currentView='admin'; render(); }, 700);
        } else {
          messageDiv.textContent = 'Gagal menyimpan: ' + res.message;
          messageDiv.style.backgroundColor = '#fee2e2';
          messageDiv.style.color = '#b91c1c';
          messageDiv.classList.remove('hidden');
          saveBtn.disabled = false;
          saveBtn.textContent = '💾 Simpan Perubahan';
        }
      } else {
        // create new subject in client memory
        const newSub = {
          __backendId: 'local-'+Date.now(),
          id: `${window.editingSubjectData.grade}-${Date.now()}`,
          grade: window.editingSubjectData.grade,
          subject_name: newName,
          form_link: newLink,
          order: window.editingSubjectData.order,
          enabled: newEnabled
        };
        allSubjects.push(newSub);
        const res = await sendSaveRequest(allSubjects);
        if (res.ok) {
          messageDiv.textContent = 'Data berhasil disimpan (server).';
          messageDiv.style.backgroundColor = '#d1fae5';
          messageDiv.style.color = '#065f46';
          messageDiv.classList.remove('hidden');
          setTimeout(()=>{ currentView='admin'; render(); }, 700);
        } else {
          messageDiv.textContent = 'Gagal menyimpan: ' + res.message;
          messageDiv.style.backgroundColor = '#fee2e2';
          messageDiv.style.color = '#b91c1c';
          messageDiv.classList.remove('hidden');
          saveBtn.disabled = false;
          saveBtn.textContent = '💾 Simpan Perubahan';
        }
      }
    }

    function openSubject(id, link) {
      if (!link) return;
      const button = event.target;
      if (button && button.classList) button.classList.add('clicked');
      setTimeout(() => window.open(link, '_blank', 'noopener,noreferrer'), 300);
    }

    function showSettings() { currentView = 'settings'; render(); }

    function handleUpdateCredentials(event) {
      event.preventDefault();
      const newUsername = document.getElementById('new-username').value;
      const newPassword = document.getElementById('new-password').value;
      const messageDiv = document.getElementById('settings-message');

      adminUsername = newUsername;
      adminPassword = newPassword;

      messageDiv.textContent = 'Pengaturan login berhasil diperbarui (di memori)!';
      messageDiv.style.backgroundColor = '#d1fae5';
      messageDiv.style.color = '#065f46';
      messageDiv.classList.remove('hidden');

      setTimeout(() => { currentView = 'admin'; render(); }, 1000);
    }

    // Toggle aktif/non-aktif cepat dari panel admin
    async function toggleEnabled(backendId) {
      const subj = allSubjects.find(s => s.__backendId === backendId);
      if (!subj) return alert('Subject tidak ditemukan');
      subj.enabled = !(subj.enabled === undefined ? true : subj.enabled);
      // kirim perubahan
      const res = await sendSaveRequest(allSubjects);
      if (!res.ok) {
        alert('Gagal menyimpan perubahan: ' + res.message);
      }
      render();
    }

    async function onConfigChange(config) {
      const customFont = config.font_family || defaultConfig.font_family;
      const baseSize = config.font_size || defaultConfig.font_size;
      const baseFontStack = 'Arial, sans-serif';

      const schoolName = document.getElementById('school-name');
      if (schoolName) {
        schoolName.textContent = config.school_name || defaultConfig.school_name;
        schoolName.style.color = config.primary_color || defaultConfig.primary_color;
        schoolName.style.fontFamily = `${customFont}, ${baseFontStack}`;
        schoolName.style.fontSize = `${baseSize * 2}px`;
      }

      const portalTitle = document.getElementById('portal-title');
      if (portalTitle) {
        portalTitle.textContent = config.portal_title || defaultConfig.portal_title;
        portalTitle.style.color = config.text_color || defaultConfig.text_color;
        portalTitle.style.fontFamily = `${customFont}, ${baseFontStack}`;
        portalTitle.style.fontSize = `${baseSize * 1.5}px`;
      }

      const semesterInfo = document.getElementById('semester-info');
      if (semesterInfo) {
        semesterInfo.textContent = config.semester_info || defaultConfig.semester_info;
        semesterInfo.style.color = config.text_color || defaultConfig.text_color;
        semesterInfo.style.fontFamily = `${customFont}, ${baseFontStack}`;
        semesterInfo.style.fontSize = `${baseSize}px`;
      }

      render();
    }

    async function init() {
      // Jika ada SDK data di environment, init; jika tidak, gunakan defaultSubjects
      if (window.dataSdk && typeof window.dataSdk.init === 'function') {
        const initResult = await window.dataSdk.init(dataHandler);
        if (!initResult.isOk) console.error("Failed to initialize data SDK");
      } else {
        // gunakan defaultSubjects (dari PHP)
        // pastikan __backendId dan enabled ada
        allSubjects = defaultSubjects.map(s => ({
          ...s,
          __backendId: s.__backendId || (s.id || ('def-'+s.grade+'-'+s.order)),
          enabled: (typeof s.enabled === 'undefined') ? true : s.enabled
        }));
      }

      if (window.elementSdk) {
        window.elementSdk.init({
          defaultConfig,
          onConfigChange,
          mapToCapabilities: (config) => ({ /* ... */ }),
          mapToEditPanelValues: (config) => new Map([
            ["school_name", config.school_name || defaultConfig.school_name],
            ["portal_title", config.portal_title || defaultConfig.portal_title],
            ["semester_info", config.semester_info || defaultConfig.semester_info],
            ["grade_7_button", config.grade_7_button || defaultConfig.grade_7_button],
            ["grade_8_button", config.grade_8_button || defaultConfig.grade_8_button],
            ["grade_9_button", config.grade_9_button || defaultConfig.grade_9_button],
            ["admin_button_text", config.admin_button_text || defaultConfig.admin_button_text]
          ])
        });
      }

      render();
    }

    init();
  </script>

  <script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'997144e394a8d515',t:'MTc2MTg5Mzk2MC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script>
</body>
</html>