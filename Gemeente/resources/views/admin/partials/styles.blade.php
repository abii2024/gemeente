<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .stat-label {
        color: #6B7280;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .filter-card, .map-card, .table-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #1F2937;
    }
    .leaflet-popup-content {
        margin: 0;
    }
    .custom-marker {
        background: transparent;
        border: none;
    }
    input, select {
        padding: 0.5rem;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    input:focus, select:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .btn-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: transform 0.2s;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
    }
    .btn-secondary {
        background: white;
        color: #0ea5e9;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        border: 2px solid #0ea5e9;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-secondary:hover {
        background: #0ea5e9;
        color: white;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    thead {
        background: #F9FAFB;
    }
    th {
        padding: 0.75rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
    }
    td {
        padding: 1rem 1.5rem;
        border-top: 1px solid #E5E7EB;
    }
    tr:hover {
        background: #F9FAFB;
    }
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
