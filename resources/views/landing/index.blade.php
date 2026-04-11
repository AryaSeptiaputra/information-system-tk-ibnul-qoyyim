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
                    @if(is_null($facilities ?? null))
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
                    @elseif(($facilities ?? collect())->count() > 0)
                        <div class="sarpras-grid">
                            @foreach($facilities as $idx => $f)
                                @php
                                    $qty = (int)($f->quantity ?? 0);
                                    $cond = $f->condition ?? null;
                                    $badgeParts = [];
                                    if ($qty > 0) { $badgeParts[] = 'Jumlah: ' . $qty; }
                                    if ($cond) { $badgeParts[] = $cond; }
                                    $badgeText = implode(' • ', $badgeParts);

                                    $imagePath = $f->image_path ?? null;
                                    $imageUrl = null;
                                    if ($imagePath) {
                                        $imageUrl = \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://', '//'])
                                            ? $imagePath
                                            : asset($imagePath);
                                    }
                                @endphp

                                <div class="sarpras-card {{ $idx < 2 ? 'sarpras-featured' : '' }} reveal">
                                    <div class="sarpras-img" style="background: linear-gradient(135deg, #e8faf0, #c8f0dc);">
                                        @if($imageUrl)
                                            <img class="sarpras-photo" src="{{ $imageUrl }}" alt="{{ $f->name ?? 'Fasilitas' }}">
                                        @else
                                            <div class="bg-decor" style="background: radial-gradient(circle, #2ECC71 0%, transparent 70%);"></div>
                                            <span class="emoji-main">🏗️</span>
                                        @endif
                                    </div>
                                    <div class="sarpras-info">
                                        <div class="sarpras-name">{{ $f->name ?? '-' }}</div>
                                        <div class="sarpras-detail">{{ $f->description ?? '' }}</div>
                                        @if($badgeText)
                                            <span class="sarpras-badge">{{ $badgeText }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="sarpras-grid">
                            <div class="sarpras-card sarpras-featured reveal">
                                <div class="sarpras-img" style="background: linear-gradient(135deg, #e8faf0, #c8f0dc);">
                                    <div class="bg-decor" style="background: radial-gradient(circle, #2ECC71 0%, transparent 70%);"></div>
                                    <span class="emoji-main">🏗️</span>
                                </div>
                                <div class="sarpras-info">
                                    <div class="sarpras-name">Belum ada data fasilitas</div>
                                    <div class="sarpras-detail">Silakan tambahkan data Sarpras dari Dashboard agar tampil di halaman ini.</div>
                                </div>
                            </div>
                        </div>
                    @endif
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

        @php
            $facilities = $facilities ?? collect();
            $gradients = [
                'linear-gradient(135deg, #e8faf0, #c8f0dc)',
                'linear-gradient(135deg, #fff8e1, #ffe0b2)',
                'linear-gradient(135deg, #e3f2fd, #bbdefb)',
                'linear-gradient(135deg, #fce4ec, #f8bbd0)',
                'linear-gradient(135deg, #f3e5f5, #e1bee7)',
                'linear-gradient(135deg, #e8f5e9, #c8e6c9)',
                'linear-gradient(135deg, #fff3e0, #ffe0b2)',
                'linear-gradient(135deg, #e0f7fa, #b2ebf2)',
            ];
            $radials = [
                'radial-gradient(circle, #2ECC71 0%, transparent 70%)',
                'radial-gradient(circle, #FFD93D 0%, transparent 70%)',
                'radial-gradient(circle, #4ECDC4 0%, transparent 70%)',
                'radial-gradient(circle, #FF8FAB 0%, transparent 70%)',
                'radial-gradient(circle, #A78BFA 0%, transparent 70%)',
                'radial-gradient(circle, #2ECC71 0%, transparent 70%)',
                'radial-gradient(circle, #FF6B35 0%, transparent 70%)',
                'radial-gradient(circle, #4ECDC4 0%, transparent 70%)',
            ];
            $fallbackEmojis = ['🏫', '🕌', '📚', '🎪', '💻', '🍽️', '🏥', '🚗'];
        @endphp

        <div class="sarpras-grid">
            @forelse($facilities as $facility)
                @php
                    $idx = $loop->index % max(count($gradients), 1);
                    $bgGradient = $gradients[$idx] ?? $gradients[0];
                    $bgRadial = $radials[$idx] ?? $radials[0];
                    $emoji = $fallbackEmojis[$idx] ?? '🏫';

                    $rawPath = (string)($facility->image_path ?? '');
                    $hasImage = trim($rawPath) !== '';
                    $imageUrl = $hasImage
                        ? (preg_match('~^https?://~i', $rawPath) ? $rawPath : asset(ltrim($rawPath, '/')))
                        : null;
                @endphp

                <div class="sarpras-card {{ $loop->iteration <= 2 ? 'sarpras-featured' : '' }} reveal">
                    <div class="sarpras-img" style="background: {{ $bgGradient }};">
                        <div class="bg-decor" style="background: {{ $bgRadial }};"></div>

                        @if($hasImage)
                            <img class="sarpras-photo" src="{{ $imageUrl }}" alt="{{ $facility->name ?? 'Fasilitas' }}">
                        @else
                            <span class="emoji-main">{{ $emoji }}</span>
                        @endif
                    </div>
                    <div class="sarpras-info">
                        <div class="sarpras-name">{{ $facility->name ?? '-' }}</div>
                        <div class="sarpras-detail">{{ $facility->description ?? '' }}</div>
                        <span class="sarpras-badge">
                            {{ (int)($facility->quantity ?? 0) }} Unit • {{ $facility->condition ?? '-' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="sarpras-card reveal">
                    <div class="sarpras-img" style="background: linear-gradient(135deg, #e8faf0, #c8f0dc);">
                        <div class="bg-decor" style="background: radial-gradient(circle, #2ECC71 0%, transparent 70%);"></div>
                        <span class="emoji-main">🏗️</span>
                    </div>
                    <div class="sarpras-info">
                        <div class="sarpras-name">Belum ada data</div>
                        <div class="sarpras-detail">Fasilitas akan tampil di sini setelah ditambahkan dari Dashboard.</div>
                        <span class="sarpras-badge">Sarpras</span>
                    </div>
                </div>
            @endforelse
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
