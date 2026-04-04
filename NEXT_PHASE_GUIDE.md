# NEXT PHASE: Blade Template Building

## Status: ✅ CSS Foundation Complete → Ready for HTML/Blade Integration

---

## Step 1: Update Vite Configuration (IMMEDIATE)

### File: `vite.config.js`

**Current Entry Point**:
```javascript
resources/css/landing.css  // OLD - 4224 lines monolithic
```

**New Entry Point**:
```javascript
resources/css/new-structure/00-manifest.css  // NEW - modular with imports
```

**Action**: Update vite.config.js line with CSS entry point

---

## Step 2: Create Master Layout

### File: `resources/views/layouts/app.blade.php`

**Structure**:
```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TK Ibnul Qoyyim')</title>
    @vite(['resources/css/new-structure/00-manifest.css', 'resources/js/app.js'])
</head>
<body>
    @include('components.navbar')
    <main class="main-content">
        @yield('content')
    </main>
    @include('components.footer')
    @vite(['resources/js/bootstrap.js'])
</body>
</html>
```

**Components Needed**:
- `components/navbar.blade.php` - Navigation header
- `components/footer.blade.php` - Footer

---

## Step 3: Build Landing Page

### File: `resources/views/landing/index.blade.php`

**Source**: Extract HTML sections from `tk_ibnul_qoyyim.html` (keep CSS classes, remove inline styles)

**Sections** (in order from HTML):
1. **Hero** (#home section)
   - Use: .hero-container, .hero-title, .hero-buttons, .hero-visual, .school-illustration
   - Classes: .animate-fadeInUp for animation

2. **Profile** (#profil section)
   - Use: .grid-2 layout, .section-header
   - Classes: .reveal for scroll animation

3. **Programs** (#program section)
   - Use: .grid-3 or .grid-4 for program cards
   - Classes: .program-card with color variants (.green, .blue, etc.)

4. **Facilities** (#sarpras section)
   - Use: .grid-4 for facility cards (responsive)
   - Classes: .sarpras-card with featured cards

5. **Teachers** (#guru section)
   - Use: .grid-4 for teacher cards
   - Classes: .guru-card with avatars and badges

6. **Gallery** (#galeri section)
   - Use: .galeri-scroll container
   - Classes: .galeri-item for each item

7. **Registration** (#pendaftaran section)
   - Use: .form-tabs for tab navigation
   - Classes: .form-group, .form-input, form-select
   - Use modals for confirmations

8. **Payment** (within registration or separate)
   - Use: .payment-grid for methods
   - Classes: .payment-option for each method
   - Use: .biaya-section for cost breakdown

**Template Structure**:
```blade
@extends('layouts.app')

@section('title', 'TK Ibnul Qoyyim - Home')

@section('content')
    <!-- Hero Section -->
    <section id="home" class="hero-container">
        {{-- content from tk_ibnul_qoyyim.html line 150-250 --}}
    </section>

    <!-- Profile Section -->
    <section id="profil" class="reveal">
        {{-- content from tk_ibnul_qoyyim.html --}}
    </section>

    {{-- Continue for each section --}}
@endsection
```

---

## Next: CSS Class Mapping Guide

**Common Usage Patterns**

**Sections**:
```html
<section id="section-name">
    <div class="section-header">
        <div class="section-tag">LABEL</div>
        <h2 class="section-title">Title <span class="accent">Highlight</span></h2>
    </div>
    <div class="grid-3"><!-- items --></div>
</section>
```

**Cards**:
```html
<!-- Program Card -->
<div class="program-card green">
    <div class="prog-icon">📖</div>
    <div class="prog-title">Title</div>
    <p class="prog-desc">Description</p>
</div>
```

**Forms**:
```html
<div class="form-group">
    <label class="form-label">Name <span>*</span></label>
    <input type="text" class="form-input" required>
</div>
```

**Buttons**:
```html
<button class="btn-primary">Primary</button>
<button class="btn-secondary">Secondary</button>
<a href="#" class="btn-ghost">Ghost</a>
```

---

## Ready for Template Building

✅ CSS foundation complete with 13 modular files
✅ Design reference (tk_ibnul_qoyyim.html) analyzed  
✅ Component classes documented
✅ Responsive design built-in
✅ Color palette and animations ready

**Next**: Update vite.config.js → Create layouts → Build landing page
