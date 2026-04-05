<style>
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border-left: 5px solid var(--green);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .stat-icon {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .stat-number {
        font-family: 'Fredoka One', cursive;
        font-size: 24px;
        color: var(--dark);
        line-height: 1;
    }

    .stat-label {
        font-size: 12px;
        color: var(--gray);
        font-weight: 700;
        margin-top: 4px;
    }

    @media (max-width: 900px) {
        .dashboard-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .dashboard-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-stats">
    @foreach($stats ?? [] as $stat)
        <div class="stat-card">
            <div class="stat-icon">{{ $stat['icon'] }}</div>
            <div class="stat-number">{{ $stat['value'] }}</div>
            <div class="stat-label">{{ $stat['label'] }}</div>
        </div>
    @endforeach
</div>
