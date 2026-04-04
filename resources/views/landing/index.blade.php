@extends('layouts.landing')

@section('content')
    @include('components.hero')

    <!-- PROFIL -->
    <section id="profil" class="section section-white">
        <div class="section-header reveal">
            <div class="section-tag">📋 Tentang Kami</div>
            <h2 class="section-title">Profil <span class="accent">Sekolah</span></h2>
            <p class="section-desc">Mengenal lebih dekat TK Ibnul Qoyyim Sulawesi, sekolah Islam terpadu yang berdedikasi membentuk generasi emas.</p>
        </div>
        
        <div class="profil-grid">
            <div class="profil-visual reveal">
                <div class="profil-main-img">
                    <span class="profil-emoji">🏫</span>
                </div>
                <div class="profil-visi-box">
                    <div class="visi-title">✦ Motto Sekolah</div>
                    "Cerdas, Berkarakter, Qurani"
                </div>
            </div>
            
            <div class="profil-content reveal">
                <h3>TK Ibnul Qoyyim Sulawesi</h3>
                <p>Didirikan sejak tahun 2012, TK Ibnul Qoyyim Sulawesi hadir sebagai lembaga pendidikan anak usia dini berbasis Islam yang memadukan kurikulum nasional dengan nilai-nilai Islami. Berlokasi di Sulawesi, kami berkomitmen memberikan pendidikan terbaik untuk generasi penerus bangsa.</p>
                
                <div class="profil-info-grid">
                    <div class="profil-info-item">
                        <div class="label">📍 Lokasi</div>
                        <div class="value">Sulawesi, Indonesia</div>
                    </div>
                    <div class="profil-info-item">
                        <div class="label">📅 Berdiri</div>
                        <div class="value">Tahun 2012</div>
                    </div>
                    <div class="profil-info-item">
                        <div class="label">🏛️ Akreditasi</div>
                        <div class="value">A (Unggul)</div>
                    </div>
                    <div class="profil-info-item">
                        <div class="label">🕐 Jam Belajar</div>
                        <div class="value">07.30 – 12.00 WIT</div>
                    </div>
                    <div class="profil-info-item">
                        <div class="label">👨‍👩‍👧 Kelompok</div>
                        <div class="value">A (4–5 th) & B (5–6 th)</div>
                    </div>
                    <div class="profil-info-item">
                        <div class="label">📞 Kontak</div>
                        <div class="value">0812-3456-7890</div>
                    </div>
                </div>
                
                <div class="visi-misi-tabs">
                    <div class="tab-buttons">
                        <button type="button" class="tab-btn active" data-tab="visi">🌟 Visi</button>
                        <button type="button" class="tab-btn" data-tab="misi">🎯 Misi</button>
                        <button type="button" class="tab-btn" data-tab="nilai">💎 Nilai</button>
                    </div>
                    
                    <div id="visi" class="tab-content active">
                        Menjadi lembaga pendidikan anak usia dini Islam terbaik yang melahirkan generasi Qurani, cerdas, berakhlak mulia, dan siap menghadapi tantangan zaman dengan keimanan yang kuat.
                    </div>
                    
                    <div id="misi" class="tab-content">
                        <ul>
                            <li>Menyelenggarakan pembelajaran Islami yang menyenangkan dan bermakna</li>
                            <li>Menanamkan nilai-nilai Al-Quran dan Sunnah sejak dini</li>
                            <li>Mengembangkan potensi anak secara holistik (kognitif, motorik, sosial, spiritual)</li>
                            <li>Membangun kerjasama yang erat antara sekolah, orang tua, dan masyarakat</li>
                            <li>Menyediakan lingkungan belajar yang aman, nyaman, dan islami</li>
                        </ul>
                    </div>
                    
                    <div id="nilai" class="tab-content">
                        <ul>
                            <li>🕌 <strong>Taqwa</strong> — Menanamkan ketakwaan kepada Allah SWT</li>
                            <li>🧠 <strong>Ilmu</strong> — Memupuk kecintaan pada ilmu pengetahuan</li>
                            <li>💪 <strong>Amanah</strong> — Membentuk karakter jujur dan bertanggung jawab</li>
                            <li>🤝 <strong>Ukhuwah</strong> — Menumbuhkan jiwa persaudaraan dan kerjasama</li>
                            <li>✨ <strong>Kreativitas</strong> — Mengembangkan bakat dan kreativitas anak</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PROGRAM -->
    <section id="program" class="section section-light">
        <div class="program-bg"></div>
        <div class="section-header reveal">
            <div class="section-tag">🎓 Kurikulum</div>
            <h2 class="section-title">Program <span class="accent">Unggulan</span></h2>
            <p class="section-desc">Berbagai program pembelajaran yang dirancang khusus untuk mengoptimalkan tumbuh kembang si kecil.</p>
        </div>
        
        <div class="program-grid">
            <div class="program-card green reveal">
                <div class="prog-icon">📖</div>
                <div class="prog-title">Tahfidz Quran</div>
                <p class="prog-desc">Program hafalan Al-Quran dengan metode talaqqi yang menyenangkan. Target hafalan Juz 30 untuk kelas B.</p>
            </div>
            <div class="program-card blue reveal">
                <div class="prog-icon">🌍</div>
                <div class="prog-title">Bahasa Arab & Inggris</div>
                <p class="prog-desc">Pengenalan bahasa Arab sebagai bahasa Al-Quran dan bahasa Inggris sebagai bahasa internasional sejak dini.</p>
            </div>
            <div class="program-card yellow reveal">
                <div class="prog-icon">🎨</div>
                <div class="prog-title">Seni & Kreativitas</div>
                <p class="prog-desc">Mengembangkan kreativitas melalui mewarnai, menggambar, kerajinan tangan, dan seni islami.</p>
            </div>
            <div class="program-card orange reveal">
                <div class="prog-icon">🏃</div>
                <div class="prog-title">Motorik & Olahraga</div>
                <p class="prog-desc">Stimulasi motorik kasar dan halus melalui senam, permainan edukatif, dan olahraga yang menyenangkan.</p>
            </div>
            <div class="program-card pink reveal">
                <div class="prog-icon">🔢</div>
                <div class="prog-title">Calistung Islami</div>
                <p class="prog-desc">Belajar membaca, menulis, dan berhitung dengan pendekatan Islami yang membuat anak bersemangat belajar.</p>
            </div>
            <div class="program-card purple reveal">
                <div class="prog-icon">🤝</div>
                <div class="prog-title">Karakter & Adab</div>
                <p class="prog-desc">Pembentukan akhlak mulia, adab Islami, kepemimpinan, dan kecerdasan emosional anak usia dini.</p>
            </div>
        </div>
    </section>

    <!-- SARANA PRASARANA -->
    <section id="sarpras" class="section">
        <div class="section-header reveal">
            <div class="section-tag">🏗️ Fasilitas</div>
            <h2 class="section-title">Sarana & <span class="accent">Prasarana</span></h2>
            <p class="section-desc">Fasilitas lengkap dan modern yang mendukung proses belajar mengajar yang optimal dan menyenangkan.</p>
        </div>
        
        <div class="sarpras-grid">
            <div class="sarpras-card sarpras-featured reveal">
                <div class="sarpras-img" style="background: linear-gradient(135deg, #e8faf0, #c8f0dc);">
                    <div class="bg-decor" style="background: radial-gradient(circle, #2ECC71 0%, transparent 70%);"></div>
                    <span class="emoji-main">🏫</span>
                </div>
                <div class="sarpras-info">
                    <div class="sarpras-name">Ruang Kelas Modern</div>
                    <div class="sarpras-detail">6 ruang kelas ber-AC, full media interaktif, dan dekorasi edukatif yang colorful. Kapasitas 20 siswa per kelas dengan pencahayaan optimal.</div>
                    <span class="sarpras-badge">6 Ruangan</span>
                </div>
            </div>
            <div class="sarpras-card sarpras-featured reveal">
                <div class="sarpras-img" style="background: linear-gradient(135deg, #fff8e1, #ffe0b2);">
                    <div class="bg-decor" style="background: radial-gradient(circle, #FFD93D 0%, transparent 70%);"></div>
                    <span class="emoji-main">🕌</span>
                </div>
                <div class="sarpras-info">
                    <div class="sarpras-name">Musholla Al-Ikhlas</div>
                    <div class="sarpras-detail">Musholla khusus anak yang nyaman untuk shalat berjamaah, mengaji, dan kegiatan keislaman sehari-hari. Dilengkapi fasilitas wudhu anak.</div>
                    <span class="sarpras-badge">Kapasitas 80 Orang</span>
                </div>
            </div>
            <div class="sarpras-card reveal">
                <div class="sarpras-img" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb);">
                    <div class="bg-decor" style="background: radial-gradient(circle, #4ECDC4 0%, transparent 70%);"></div>
                    <span class="emoji-main">📚</span>
                </div>
                <div class="sarpras-info">
                    <div class="sarpras-name">Perpustakaan</div>
                    <div class="sarpras-detail">Koleksi 500+ buku cerita Islami dan edukatif.</div>
                    <span class="sarpras-badge">500+ Koleksi</span>
                </div>
            </div>
            <div class="sarpras-card reveal">
                <div class="sarpras-img" style="background: linear-gradient(135deg, #fce4ec, #f8bbd0);">
                    <div class="bg-decor" style="background: radial-gradient(circle, #FF8FAB 0%, transparent 70%);"></div>
                    <span class="emoji-main">🎪</span>
                </div>
                <div class="sarpras-info">
                    <div class="sarpras-name">Area Bermain</div>
                    <div class="sarpras-detail">Area bermain outdoor & indoor berstandar keamanan anak.</div>
                    <span class="sarpras-badge">Indoor & Outdoor</span>
                </div>
            </div>
            <div class="sarpras-card reveal">
                <div class="sarpras-img" style="background: linear-gradient(135deg, #f3e5f5, #e1bee7);">
                    <div class="bg-decor" style="background: radial-gradient(circle, #A78BFA 0%, transparent 70%);"></div>
                    <span class="emoji-main">💻</span>
                </div>
                <div class="sarpras-info">
                    <div class="sarpras-name">Lab Komputer</div>
                    <div class="sarpras-detail">10 unit komputer khusus anak dengan software edukatif.</div>
                    <span class="sarpras-badge">10 Unit</span>
                </div>
            </div>
            <div class="sarpras-card reveal">
                <div class="sarpras-img" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9);">
                    <div class="bg-decor" style="background: radial-gradient(circle, #2ECC71 0%, transparent 70%);"></div>
                    <span class="emoji-main">🍽️</span>
                </div>
                <div class="sarpras-info">
                    <div class="sarpras-name">Kantin Sehat</div>
                    <div class="sarpras-detail">Kantin dengan menu bergizi dan halal, dikelola langsung oleh sekolah.</div>
                    <span class="sarpras-badge">Halal Certified</span>
                </div>
            </div>
            <div class="sarpras-card reveal">
                <div class="sarpras-img" style="background: linear-gradient(135deg, #fff3e0, #ffe0b2);">
                    <div class="bg-decor" style="background: radial-gradient(circle, #FF6B35 0%, transparent 70%);"></div>
                    <span class="emoji-main">🏥</span>
                </div>
                <div class="sarpras-info">
                    <div class="sarpras-name">UKS</div>
                    <div class="sarpras-detail">Unit Kesehatan Sekolah lengkap dengan tenaga kesehatan terlatih.</div>
                    <span class="sarpras-badge">Tenaga Medis</span>
                </div>
            </div>
            <div class="sarpras-card reveal">
                <div class="sarpras-img" style="background: linear-gradient(135deg, #e0f7fa, #b2ebf2);">
                    <div class="bg-decor" style="background: radial-gradient(circle, #4ECDC4 0%, transparent 70%);"></div>
                    <span class="emoji-main">🚗</span>
                </div>
                <div class="sarpras-info">
                    <div class="sarpras-name">Antar Jemput</div>
                    <div class="sarpras-detail">Layanan antar jemput siswa dengan armada bus sekolah yang aman.</div>
                    <span class="sarpras-badge">3 Armada</span>
                </div>
            </div>
        </div>
    </section>

    <!-- GURU -->
    <section id="guru" class="section section-white">
        <div class="section-header reveal">
            <div class="section-tag">👩‍🏫 Tim Pengajar</div>
            <h2 class="section-title">Guru <span class="accent">Kami</span></h2>
            <p class="section-desc">Tim pengajar berpengalaman dan bersertifikat yang berdedikasi untuk perkembangan optimal setiap anak.</p>
        </div>
        
        <div class="guru-grid">
            <div class="guru-card reveal">
                <div class="guru-avatar" style="background: linear-gradient(135deg, #e8faf0, #c8f0dc);">👩‍💼</div>
                <div class="guru-name">Ustadzah Fatimah</div>
                <div class="guru-jabatan">Kepala Sekolah</div>
                <div class="guru-pendidikan">S2 Pendidikan Islam<br>15 Tahun Pengalaman</div>
            </div>
            <div class="guru-card reveal">
                <div class="guru-avatar" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb);">👩‍🏫</div>
                <div class="guru-name">Ustadzah Aisyah</div>
                <div class="guru-jabatan">Guru Kelas A</div>
                <div class="guru-pendidikan">S1 PAUD<br>8 Tahun Pengalaman</div>
            </div>
            <div class="guru-card reveal">
                <div class="guru-avatar" style="background: linear-gradient(135deg, #fff8e1, #ffe0b2);">👩‍🏫</div>
                <div class="guru-name">Ustadzah Khadijah</div>
                <div class="guru-jabatan">Guru Kelas B</div>
                <div class="guru-pendidikan">S1 PAUD<br>6 Tahun Pengalaman</div>
            </div>
            <div class="guru-card reveal">
                <div class="guru-avatar" style="background: linear-gradient(135deg, #fce4ec, #f8bbd0);">👨‍🏫</div>
                <div class="guru-name">Ustadz Yusuf</div>
                <div class="guru-jabatan">Guru Tahfidz</div>
                <div class="guru-pendidikan">Hafidz 30 Juz<br>10 Tahun Pengalaman</div>
            </div>
        </div>
    </section>

    <!-- GALERI -->
    <section id="galeri" class="section section-light">
        <div class="section-header reveal">
            <div class="section-tag">📸 Dokumentasi</div>
            <h2 class="section-title">Galeri <span class="accent">Kegiatan</span></h2>
            <p class="section-desc">Momen-momen berharga kegiatan belajar dan bermain siswa TK Ibnul Qoyyim Sulawesi.</p>
        </div>
        
        <div class="galeri-scroll reveal">
            <div class="galeri-item" style="background: linear-gradient(135deg, #e8faf0, #c8f0dc);">
                <span class="galeri-emoji">🎨</span>
                <div class="galeri-label">Seni & Kreasi</div>
            </div>
            <div class="galeri-item" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb);">
                <span class="galeri-emoji">📖</span>
                <div class="galeri-label">Belajar Quran</div>
            </div>
            <div class="galeri-item" style="background: linear-gradient(135deg, #fff8e1, #ffe0b2);">
                <span class="galeri-emoji">🏃</span>
                <div class="galeri-label">Olahraga Pagi</div>
            </div>
            <div class="galeri-item" style="background: linear-gradient(135deg, #fce4ec, #f8bbd0);">
                <span class="galeri-emoji">🎭</span>
                <div class="galeri-label">Pentas Seni</div>
            </div>
            <div class="galeri-item" style="background: linear-gradient(135deg, #f3e5f5, #e1bee7);">
                <span class="galeri-emoji">🎪</span>
                <div class="galeri-label">Wisuda TK</div>
            </div>
            <div class="galeri-item" style="background: linear-gradient(135deg, #e0f7fa, #b2ebf2);">
                <span class="galeri-emoji">🌱</span>
                <div class="galeri-label">Berkebun</div>
            </div>
            <div class="galeri-item" style="background: linear-gradient(135deg, #fff3e0, #ffe0b2);">
                <span class="galeri-emoji">🕌</span>
                <div class="galeri-label">Shalat Berjamaah</div>
            </div>
            <div class="galeri-item" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9);">
                <span class="galeri-emoji">🏆</span>
                <div class="galeri-label">Lomba & Prestasi</div>
            </div>
        </div>
    </section>
@endsection
