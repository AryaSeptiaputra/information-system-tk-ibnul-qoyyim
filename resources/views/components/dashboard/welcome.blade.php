<style>
    .dashboard-welcome {
        background: linear-gradient(135deg, #e8faf0 0%, #d0f5e8 100%);
        border-radius: 20px;
        padding: 32px;
        border: 2px solid var(--green-light);
        margin-bottom: 32px;
    }

    .dashboard-welcome h2 {
        font-family: 'Fredoka One', cursive;
        font-size: 28px;
        color: var(--dark);
        margin-bottom: 8px;
    }

    .dashboard-welcome .greeting {
        font-size: 16px;
        font-weight: 700;
        color: var(--green-dark);
        margin-bottom: 4px;
    }

    .dashboard-welcome p {
        color: var(--gray);
        font-size: 15px;
        line-height: 1.6;
        font-weight: 600;
    }

    @media (max-width: 600px) {
        .dashboard-welcome {
            padding: 20px;
        }

        .dashboard-welcome h2 {
            font-size: 22px;
        }
    }
</style>

<div class="dashboard-welcome">
    <div class="greeting">{{ $greeting ?? 'Selamat Datang' }} 👋</div>
    <h2>{{ $user->name ?? 'User' }}</h2>
    <p>
        @if($role === 'guest')
            Ini adalah halaman dashboard Anda. Di sini Anda dapat memantau status pendaftaran anak, 
            melihat informasi siswa, dan mengakses fitur lainnya.
        @else
            Selamat datang di dashboard TK Ibnul Qoyyim Sulawesi. Kelola sekolah dari sini.
        @endif
    </p>
</div>
