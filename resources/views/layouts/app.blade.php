<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BÚHO') — Panel Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* ─── Variables ─────────────────────────────────────────── */
        :root {
            --primary:        #DC1E2E;
            --primary-dark:   #B01825;
            --primary-light:  #E94855;
            --primary-lighter:#FFE8EA;
            --green:          #10B981;
            --green-dark:     #059669;
            --green-light:    #D1FAE5;
            --blue:           #3B82F6;
            --blue-dark:      #1D4ED8;
            --blue-light:     #DBEAFE;
            --purple:         #8B5CF6;
            --purple-dark:    #6D28D9;
            --purple-light:   #EDE9FE;
            --amber:          #F59E0B;
            --amber-dark:     #D97706;
            --amber-light:    #FEF3C7;
            --sidebar-bg:     #1A1D29;
            --sidebar-bg2:    #252832;
            --text-dark:      #0F172A;
            --text-medium:    #334155;
            --text-light:     #64748B;
            --text-lighter:   #94A3B8;
            --border:         #E2E8F0;
            --bg:             #F1F5FB;
            --sidebar-w:      280px;
            --radius-sm:      8px;
            --radius-md:      12px;
            --radius-lg:      16px;
            --radius-xl:      20px;
            --shadow-sm:      0 2px 8px rgba(0,0,0,.08);
            --shadow-md:      0 4px 16px rgba(0,0,0,.10);
            --shadow-lg:      0 8px 32px rgba(0,0,0,.12);
            --shadow-red:     0 4px 16px rgba(220,30,46,.25);
        }

        /* ─── Reset ─────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            background-image:
                radial-gradient(at 20% 20%, rgba(220,30,46,.04) 0%, transparent 50%),
                radial-gradient(at 80% 80%, rgba(59,130,246,.04) 0%, transparent 50%),
                radial-gradient(at 50% 0%, rgba(139,92,246,.03) 0%, transparent 50%);
            background-attachment: fixed;
            color: var(--text-medium);
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
        }

        /* ─── Sidebar ────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--sidebar-bg2) 100%);
            border-right: 1px solid rgba(220,30,46,.15);
            box-shadow: 4px 0 24px rgba(0,0,0,.15);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            z-index: 1000;
        }

        /* Logo area */
        .sidebar-brand {
            padding: 28px 24px 24px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .sidebar-logo {
            width: 46px; height: 46px;
            border-radius: 12px;
            flex-shrink: 0;
            overflow: hidden;
        }
        .sidebar-logo img { width: 46px; height: 46px; object-fit: contain; border-radius: 12px; }
        .sidebar-brand-text h2 {
            font-size: 18px; font-weight: 800;
            color: #fff; letter-spacing: -.3px;
            line-height: 1.1;
        }
        .sidebar-brand-text span {
            font-size: 10px; font-weight: 500;
            color: rgba(255,255,255,.4);
            text-transform: uppercase; letter-spacing: 1px;
        }

        /* User badge */
        .sidebar-user {
            margin: 16px 16px 0;
            padding: 12px 14px;
            background: rgba(220,30,46,.08);
            border: 1px solid rgba(220,30,46,.2);
            border-radius: var(--radius-md);
        }
        .sidebar-user-name {
            font-size: 13px; font-weight: 600; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user-role {
            font-size: 11px; font-weight: 500;
            color: var(--primary-light);
            text-transform: uppercase; letter-spacing: .8px;
        }

        /* Nav */
        .sidebar-nav { padding: 16px 16px 24px; flex: 1; }
        .nav-section-label {
            font-size: 10px; font-weight: 700;
            color: rgba(255,255,255,.35);
            text-transform: uppercase; letter-spacing: 1.4px;
            padding: 20px 12px 8px;
            display: flex; align-items: center; gap: 7px;
        }
        .nav-section-label::before {
            content: '';
            display: inline-block;
            width: 16px; height: 1.5px;
            background: rgba(220,30,46,.5);
            border-radius: 1px;
            flex-shrink: 0;
        }
        .nav-section-label:first-child { padding-top: 8px; }
        .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 12px;
            color: rgba(255,255,255,.65);
            font-size: 13.5px; font-weight: 500;
            border-radius: 10px;
            text-decoration: none;
            transition: all .18s ease;
            margin-bottom: 2px;
        }
        .nav-link i { font-size: 16px; width: 18px; text-align: center; flex-shrink: 0; }
        .nav-link:hover {
            background: rgba(220,30,46,.1);
            color: var(--primary-light);
        }
        .nav-link.active {
            background: rgba(220,30,46,.15);
            color: var(--primary-light);
            font-weight: 600;
            box-shadow: 0 0 0 1px rgba(220,30,46,.25);
        }

        /* Logout at bottom */
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .btn-logout {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px;
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.25);
            border-radius: var(--radius-md);
            color: #F87171;
            font-size: 13px; font-weight: 600;
            cursor: pointer;
            transition: all .18s ease;
            text-decoration: none;
        }
        .btn-logout:hover {
            background: rgba(239,68,68,.22);
            color: #FCA5A5;
        }

        /* ─── Main Content ───────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            border-top: 3px solid var(--primary);
            padding: 14px 36px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
        }
        .topbar-title {
            font-size: 18px; font-weight: 700; color: var(--text-dark);
            letter-spacing: -.3px;
        }
        .topbar-title small {
            display: block; font-size: 12px; font-weight: 400;
            color: var(--text-light); margin-top: 1px;
        }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-badge {
            padding: 5px 12px;
            background: var(--primary-lighter);
            color: var(--primary-dark);
            border: 1px solid rgba(220,30,46,.2);
            border-radius: 50px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .8px;
        }

        /* Page content */
        .page-content { padding: 28px 36px; flex: 1; }

        /* ─── Alerts ─────────────────────────────────────────────── */
        .alert {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 13.5px; font-weight: 500;
            animation: slideDown .3s ease;
        }
        .alert i { font-size: 17px; margin-top: 1px; flex-shrink: 0; }
        .alert-success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .alert-danger   { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        .alert-warning  { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
        .alert-dismiss {
            margin-left: auto; background: none; border: none;
            cursor: pointer; color: inherit; opacity: .6; font-size: 18px;
            line-height: 1; padding: 0 2px;
        }
        .alert-dismiss:hover { opacity: 1; }
        .alert ul { margin: 4px 0 0 16px; }

        /* ─── Cards ──────────────────────────────────────────────── */
        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: box-shadow .2s ease, border-color .2s ease;
        }
        .card:hover { box-shadow: var(--shadow-sm); }
        .card-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            border-left: 3px solid var(--primary);
            background: linear-gradient(to right, #FFFAFA, #fff);
            font-size: 14px; font-weight: 700; color: var(--text-dark);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-header i { color: var(--primary); margin-right: 7px; }
        .card-body { padding: 24px; }
        .card-footer {
            padding: 14px 24px;
            background: var(--bg);
            border-top: 1px solid var(--border);
        }
        /* Color variants for card headers */
        .card-header.ch-green  { border-left-color: var(--green);  background: linear-gradient(to right, #F0FDF9, #fff); }
        .card-header.ch-green i { color: var(--green); }
        .card-header.ch-blue   { border-left-color: var(--blue);   background: linear-gradient(to right, #EFF6FF, #fff); }
        .card-header.ch-blue i  { color: var(--blue); }
        .card-header.ch-purple { border-left-color: var(--purple); background: linear-gradient(to right, #F5F3FF, #fff); }
        .card-header.ch-purple i { color: var(--purple); }
        .card-header.ch-amber  { border-left-color: var(--amber);  background: linear-gradient(to right, #FFFBEB, #fff); }
        .card-header.ch-amber i  { color: var(--amber); }

        /* Stat card */
        .stat-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            display: flex; align-items: center; gap: 18px;
            transition: all .25s cubic-bezier(.4,0,.2,1);
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            opacity: 0;
            transition: opacity .25s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(220,30,46,.2);
        }
        .stat-card:hover::before { opacity: 1; }
        .stat-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 23px;
            flex-shrink: 0;
        }
        .stat-icon.red    { background: linear-gradient(135deg, #FFE8EA, #FECDD3); color: var(--primary); box-shadow: 0 4px 12px rgba(220,30,46,.18); }
        .stat-icon.green  { background: linear-gradient(135deg, #D1FAE5, #6EE7B7); color: #059669; box-shadow: 0 4px 12px rgba(16,185,129,.18); }
        .stat-icon.blue   { background: linear-gradient(135deg, #DBEAFE, #93C5FD); color: #2563EB; box-shadow: 0 4px 12px rgba(59,130,246,.18); }
        .stat-icon.amber  { background: linear-gradient(135deg, #FEF3C7, #FCD34D); color: #D97706; box-shadow: 0 4px 12px rgba(245,158,11,.18); }
        .stat-icon.dark   { background: linear-gradient(135deg, #F1F5F9, #E2E8F0); color: var(--text-dark); }
        .stat-icon.purple { background: linear-gradient(135deg, #EDE9FE, #C4B5FD); color: #7C3AED; box-shadow: 0 4px 12px rgba(139,92,246,.18); }
        .stat-icon.teal   { background: linear-gradient(135deg, #CCFBF1, #5EEAD4); color: #0F766E; box-shadow: 0 4px 12px rgba(20,184,166,.18); }
        .stat-value { font-size: 28px; font-weight: 800; color: var(--text-dark); line-height: 1; }
        .stat-label { font-size: 12.5px; font-weight: 600; color: var(--text-light); margin-top: 4px; text-transform: uppercase; letter-spacing: .4px; }

        /* ─── Table ──────────────────────────────────────────────── */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead { border-top: 2px solid var(--primary); }
        thead th {
            background: linear-gradient(180deg, #F8FAFC, #F1F5F9);
            padding: 12px 20px;
            font-size: 11.5px; font-weight: 700;
            color: var(--text-light);
            text-transform: uppercase; letter-spacing: .5px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        tbody td {
            padding: 14px 20px;
            border-bottom: 1px solid #F1F5F9;
            color: var(--text-medium);
            font-size: 13.5px; font-weight: 500;
            vertical-align: middle;
            transition: background .12s ease;
        }
        tbody tr:nth-child(even) td { background: #FAFBFF; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td {
            background: linear-gradient(to right, var(--primary-lighter), #FFF5F5, transparent) !important;
            color: var(--text-dark);
        }
        .td-end { text-align: right; }

        /* ─── Badges ─────────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 11px;
            border-radius: 50px;
            font-size: 11.5px; font-weight: 700;
            white-space: nowrap;
            letter-spacing: .2px;
        }
        .badge-success { background: linear-gradient(135deg, #D1FAE5, #A7F3D0); color: #065F46; box-shadow: inset 0 0 0 1px rgba(16,185,129,.2); }
        .badge-danger  { background: linear-gradient(135deg, #FEE2E2, #FECACA); color: #991B1B; box-shadow: inset 0 0 0 1px rgba(239,68,68,.2); }
        .badge-warning { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: #92400E; box-shadow: inset 0 0 0 1px rgba(245,158,11,.2); }
        .badge-info    { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: #1E40AF; box-shadow: inset 0 0 0 1px rgba(59,130,246,.2); }
        .badge-gray    { background: #F1F5F9; color: #475569; border: 1px solid var(--border); }
        .badge-purple  { background: linear-gradient(135deg, #EDE9FE, #DDD6FE); color: #5B21B6; box-shadow: inset 0 0 0 1px rgba(139,92,246,.2); }
        .badge-primary { background: linear-gradient(135deg, var(--primary-lighter), #FECDD3); color: var(--primary-dark); box-shadow: inset 0 0 0 1px rgba(220,30,46,.2); }
        .badge-teal    { background: linear-gradient(135deg, #CCFBF1, #99F6E4); color: #0F766E; box-shadow: inset 0 0 0 1px rgba(20,184,166,.2); }

        /* ─── Buttons ────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; border: none;
            text-decoration: none;
            transition: all .18s ease;
            white-space: nowrap;
            line-height: 1.2;
        }
        .btn i { font-size: 15px; }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #fff;
            box-shadow: var(--shadow-red);
        }
        .btn-primary:hover { background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(220,30,46,.35); color: #fff; }
        .btn-secondary {
            background: #F1F5F9; color: var(--text-light);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { background: var(--border); color: var(--text-medium); }
        .btn-danger {
            background: #EF4444; color: #fff;
            box-shadow: 0 2px 8px rgba(239,68,68,.2);
        }
        .btn-danger:hover { background: #DC2626; transform: translateY(-1px); color: #fff; }
        .btn-warning {
            background: #F59E0B; color: #fff;
            box-shadow: 0 2px 8px rgba(245,158,11,.2);
        }
        .btn-warning:hover { background: #D97706; transform: translateY(-1px); color: #fff; }
        .btn-success {
            background: #10B981; color: #fff;
            box-shadow: 0 2px 8px rgba(16,185,129,.2);
        }
        .btn-success:hover { background: #059669; transform: translateY(-1px); color: #fff; }
        .btn-outline {
            background: transparent; color: var(--primary);
            border: 1.5px solid var(--primary);
        }
        .btn-outline:hover { background: var(--primary-lighter); }
        .btn-sm { padding: 7px 14px; font-size: 12px; }
        .btn-sm i { font-size: 13px; }
        .btn-xs { padding: 4px 10px; font-size: 11.5px; }
        .btn-icon { padding: 8px 10px; }

        /* ─── Forms ──────────────────────────────────────────────── */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; margin-bottom: 6px;
            font-size: 13px; font-weight: 600; color: var(--text-dark);
        }
        .form-label .req { color: var(--primary); margin-left: 2px; }
        .form-control, .form-select {
            width: 100%;
            padding: 10px 14px;
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-dark);
            font-size: 13.5px; font-weight: 500;
            font-family: inherit;
            transition: border-color .15s, box-shadow .15s;
            appearance: none;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(220,30,46,.1);
        }
        .form-control::placeholder { color: #CBD5E1; }
        .form-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%2364748B' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 38px;
        }
        .form-control.is-invalid { border-color: var(--primary); }
        .invalid-feedback { color: var(--primary); font-size: 12px; margin-top: 4px; font-weight: 500; }
        .form-check { display: flex; align-items: center; gap: 8px; }
        .form-check-input {
            width: 16px; height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .form-switch .form-check-input { width: 36px; height: 20px; }
        .form-check-label { font-size: 13.5px; font-weight: 500; color: var(--text-medium); cursor: pointer; }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .form-hint { font-size: 11.5px; color: var(--text-lighter); margin-top: 4px; }

        /* ─── Grid ───────────────────────────────────────────────── */
        .grid { display: grid; }
        .cols-2 { grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .cols-3 { grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .cols-4 { grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 28px; }
        .flex { display: flex; } .flex-center { align-items: center; } .flex-between { justify-content: space-between; }
        .gap-8 { gap: 8px; } .gap-12 { gap: 12px; } .gap-16 { gap: 16px; }
        .mb-8 { margin-bottom: 8px; } .mb-16 { margin-bottom: 16px; } .mb-24 { margin-bottom: 24px; } .mb-28 { margin-bottom: 28px; }
        .mt-4 { margin-top: 4px; } .mt-8 { margin-top: 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-muted { color: var(--text-light); }
        .text-success { color: #059669; }
        .text-danger  { color: var(--primary); }
        .text-dark    { color: var(--text-dark); }
        .fw-600 { font-weight: 600; } .fw-700 { font-weight: 700; } .fw-800 { font-weight: 800; }
        .fs-12 { font-size: 12px; } .fs-13 { font-size: 13px; }
        .w-100 { width: 100%; }

        /* ─── Required asterisk ─────────────────────────────────── */
        .req { color: var(--primary); font-weight: 700; }

        /* ─── Form hint (small note below input) ────────────────── */
        .form-hint { font-size: 11.5px; color: var(--text-lighter); margin-top: 4px; }

        /* ─── Icon-only button ──────────────────────────────────── */
        .btn-icon { padding: 6px 10px !important; }

        /* ─── Outline button ────────────────────────────────────── */
        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text-medium);
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 500;
            cursor: pointer;
            display: inline-flex; align-items: center; gap: 5px;
            text-decoration: none;
            transition: all .15s;
        }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }

        /* ─── Table td-end (right-aligned actions) ──────────────── */
        .td-end { text-align: right; }

        /* ─── Alert boxes ───────────────────────────────────────── */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 18px; border-radius: var(--radius-md);
            font-size: 13px; font-weight: 500;
            margin-bottom: 16px;
        }
        .alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .alert-danger  { background: #FEF2F2; color: var(--primary-dark); border: 1px solid #FECACA; }
        .alert-warning { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
        .alert-info    { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
        .alert i { font-size: 18px; flex-shrink: 0; }

        /* ─── Invalid feedback (form errors) ────────────────────── */
        .invalid-feedback { display: block; font-size: 12px; color: var(--primary); margin-top: 4px; font-weight: 500; }
        .is-invalid { border-color: var(--primary) !important; }

        /* ─── Card footer ───────────────────────────────────────── */
        .card-footer {
            padding: 14px 24px;
            background: var(--bg);
            border-top: 1px solid var(--border);
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        }

        /* ─── Form check (checkbox) ─────────────────────────────── */
        .form-check { display: flex; align-items: center; gap: 8px; }
        .form-check-input { accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer; }
        .form-check-label { font-size: 13px; font-weight: 500; color: var(--text-medium); cursor: pointer; }

        /* ─── Page header bar ────────────────────────────────────── */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
            position: relative;
        }
        .page-header::after {
            content: '';
            position: absolute; bottom: -1px; left: 0;
            width: 64px; height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border-radius: 1px;
        }
        .page-header-left { display: flex; align-items: center; gap: 12px; }
        .page-title { font-size: 20px; font-weight: 800; color: var(--text-dark); }
        .back-btn {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            background: #fff; border: 1.5px solid var(--border);
            border-radius: var(--radius-sm); color: var(--text-light);
            font-size: 16px; text-decoration: none;
            transition: all .15s;
        }
        .back-btn:hover { border-color: var(--primary); color: var(--primary); }

        /* ─── Filter bar ─────────────────────────────────────────── */
        .filter-bar {
            background: linear-gradient(to right, #fff, #FAFCFF);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 16px 20px;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
            box-shadow: 0 1px 6px rgba(0,0,0,.04);
        }
        .filter-bar .form-control,
        .filter-bar .form-select { width: auto; min-width: 180px; }

        /* ─── Modal ──────────────────────────────────────────────── */
        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(15,23,42,.7);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: none;
            align-items: center; justify-content: center;
        }
        .modal-backdrop.open { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: var(--radius-xl);
            width: 90%; max-width: 580px;
            max-height: 90vh; overflow-y: auto;
            box-shadow: 0 25px 60px rgba(0,0,0,.25);
            animation: slideUp .3s ease;
        }
        .modal-header {
            background: linear-gradient(135deg, var(--sidebar-bg) 0%, var(--sidebar-bg2) 100%);
            padding: 24px 28px;
            display: flex; align-items: center; justify-content: space-between;
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
        }
        .modal-header h5 { font-size: 16px; font-weight: 700; color: #fff; }
        .modal-close {
            width: 30px; height: 30px;
            background: rgba(255,255,255,.1); border: none;
            border-radius: 8px; color: rgba(255,255,255,.7);
            font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all .15s;
        }
        .modal-close:hover { background: rgba(255,255,255,.2); color: #fff; }
        .modal-body { padding: 28px; }
        .modal-footer {
            padding: 18px 28px;
            background: var(--bg); border-top: 1px solid var(--border);
            display: flex; gap: 10px; justify-content: flex-end;
            border-radius: 0 0 var(--radius-xl) var(--radius-xl);
        }

        /* ─── Pagination ─────────────────────────────────────────── */
        .pagination { display: flex; gap: 4px; list-style: none; padding: 0; }
        .page-item .page-link {
            padding: 7px 13px; border-radius: var(--radius-sm);
            border: 1.5px solid var(--border); background: #fff;
            color: var(--text-medium); font-size: 13px; font-weight: 500;
            text-decoration: none; display: block;
            transition: all .15s;
        }
        .page-item.active .page-link { background: var(--primary); border-color: var(--primary); color: #fff; }
        .page-item .page-link:hover { border-color: var(--primary); color: var(--primary); }
        .page-item.disabled .page-link { opacity: .4; pointer-events: none; }

        /* ─── Animations ─────────────────────────────────────────── */
        @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        @keyframes slideUp   { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeIn    { from { opacity:0; } to { opacity:1; } }

        /* ─── Responsive ─────────────────────────────────────────── */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); transition: transform .3s ease; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .topbar { padding: 14px 20px; }
            .page-content { padding: 20px; }
        }
        @media (max-width: 768px) {
            .cols-2, .cols-3, .cols-4 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-bar .form-control,
            .filter-bar .form-select { width: 100%; min-width: unset; }
        }

        /* ─── Empty state ────────────────────────────────────────── */
        .empty-state {
            padding: 64px 24px; text-align: center; color: var(--text-light);
        }
        .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; opacity: .35; }
        .empty-state p { font-size: 14px; font-weight: 500; }

        /* ─── Section dividers ───────────────────────────────────── */
        .section-title {
            font-size: 11px; font-weight: 700;
            color: var(--text-lighter);
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 12px; padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }

        /* ─── Custom scrollbar ───────────────────────────────────── */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 2px; }

        /* ─── Mobile toggle button ───────────────────────────────── */
        .btn-menu-toggle { display: none; }
        @media (max-width: 1024px) {
            .btn-menu-toggle { display: inline-flex; }
        }

        /* ─── Sidebar overlay (mobile) ───────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 999;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.open { display: block; }

        /* ─── Progress bar ───────────────────────────────────────── */
        .progress-bar-wrap { background: #F1F5F9; border-radius: 50px; height: 8px; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 50px; background: linear-gradient(90deg, var(--primary), var(--primary-light)); }

        /* ─── Code ───────────────────────────────────────────────── */
        code {
            background: #F1F5F9; color: var(--primary-dark);
            padding: 2px 7px; border-radius: 5px;
            font-size: 12.5px; font-family: 'SFMono-Regular', Consolas, monospace;
        }

        /* ─── Bootstrap-replacement Grid ────────────────────────── */
        .row { display: flex; flex-wrap: wrap; margin: -10px; }
        .row > [class*="col-"] { padding: 10px; box-sizing: border-box; }
        .row.g-3 { margin: -10px; }
        .row.g-3 > [class*="col-"] { padding: 10px; }
        .row.g-2 { margin: -6px; }
        .row.g-2 > [class*="col-"] { padding: 6px; }
        .col-12  { width: 100%; }
        .col-6   { width: 50%; }
        .col-auto { width: auto; }
        .col-md-2 { width: 16.66%; } .col-md-3 { width: 25%; }
        .col-md-4 { width: 33.33%; } .col-md-5 { width: 41.66%; }
        .col-md-6 { width: 50%; } .col-md-7 { width: 58.33%; }
        .col-md-8 { width: 66.66%; }
        .col-sm-6 { width: 50%; }
        .col-lg-4 { width: 33.33%; } .col-lg-6 { width: 50%; } .col-lg-7 { width: 58.33%; } .col-lg-8 { width: 66.66%; }
        @media (max-width: 1024px) {
            .col-lg-4, .col-lg-6, .col-lg-7, .col-lg-8 { width: 100%; }
        }
        @media (max-width: 768px) {
            .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-sm-6 { width: 100%; }
        }

        /* ─── Detail Grid (show pages) ──────────────────────────── */
        .detail-grid {
            display: grid; gap: 0;
        }
        .detail-row {
            display: flex; align-items: baseline;
            padding: 10px 0;
            border-bottom: 1px solid #F1F5F9;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label {
            width: 140px; flex-shrink: 0;
            font-size: 12px; font-weight: 600;
            color: var(--text-lighter);
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .detail-value {
            font-size: 13.5px; font-weight: 500;
            color: var(--text-dark);
        }

        /* ─── Chip (toggle filter) ──────────────────────────────── */
        .chip {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 12px; font-weight: 600;
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--text-medium);
            text-decoration: none;
            cursor: pointer;
            transition: all .15s ease;
            white-space: nowrap;
        }
        .chip:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-lighter); }
        .chip.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #fff; border-color: var(--primary);
            box-shadow: var(--shadow-red);
        }
        .chip i { font-size: 12px; }
        .chip-group { display: flex; flex-wrap: wrap; gap: 6px; }

        /* ─── Info Card (compact stat) ──────────────────────────── */
        .info-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px 24px;
            text-align: center;
            transition: all .25s cubic-bezier(.4,0,.2,1);
            position: relative; overflow: hidden;
        }
        .info-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light), transparent);
            opacity: 0;
            transition: opacity .25s ease;
        }
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: rgba(220,30,46,.15);
        }
        .info-card:hover::after { opacity: 1; }
        .info-card-value {
            font-size: 30px; font-weight: 800;
            line-height: 1;
        }
        .info-card-label {
            font-size: 11.5px; font-weight: 600;
            color: var(--text-light);
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: .4px;
            display: flex; align-items: center; justify-content: center; gap: 5px;
        }

        /* ─── Form card (create/edit pages) ─────────────────────── */
        .form-card { max-width: 800px; margin: 0 auto; }
        .form-card .card { margin-bottom: 20px; }
        .form-card .card-header {
            background: linear-gradient(135deg, var(--sidebar-bg) 0%, var(--sidebar-bg2) 100%);
            color: #fff;
            border-bottom: none;
            border-left: none;
        }
        .form-card .card-header i { color: var(--primary-light); margin-right: 8px; }

        /* ─── Action bar (form bottom) ──────────────────────────── */
        .action-bar {
            display: flex; gap: 10px; justify-content: flex-end;
            padding-top: 8px;
        }

        /* ─── Hover card effect ─────────────────────────────────── */
        .hover-lift { transition: all .25s cubic-bezier(.4,0,.2,1); }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

        /* ─── Responsive table scroll ───────────────────────────── */
        .table-responsive { overflow-x: auto; }

        /* ─── Link reset ────────────────────────────────────────── */
        a.card-link { text-decoration: none; color: inherit; display: block; }
        a.card-link:hover .card { border-color: var(--primary-lighter); }

        /* ─── Inline badge with dot ─────────────────────────────── */
        .badge i.dot { font-size: 7px; }

        /* ─── Switch toggle styled ──────────────────────────────── */
        .toggle-switch {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 10px 18px;
            background: #F8FAFC;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all .15s;
        }
        .toggle-switch:has(input:checked) {
            background: var(--primary-lighter);
            border-color: rgba(220,30,46,.3);
        }
        .toggle-switch input { accent-color: var(--primary); width: 18px; height: 18px; cursor: pointer; }
        .toggle-switch span { font-size: 13.5px; font-weight: 500; color: var(--text-medium); }

        /* ─── Permission chips ──────────────────────────────────── */
        .perm-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        .perm-chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px;
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all .15s;
            font-size: 13px; font-weight: 500;
        }
        .perm-chip:has(input:checked) {
            background: var(--primary-lighter);
            border-color: var(--primary);
            color: var(--primary-dark);
        }
        .perm-chip input { accent-color: var(--primary); width: 15px; height: 15px; cursor: pointer; }

        /* ─── Card with accent border ───────────────────────────── */
        .card-accent { border-left: 3px solid var(--primary) !important; }

        /* ─── Report card ───────────────────────────────────────── */
        .report-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 36px 24px;
            text-align: center;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            text-decoration: none; display: block; color: inherit;
            position: relative; overflow: hidden;
        }
        .report-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s ease;
        }
        .report-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(220,30,46,.2);
        }
        .report-card:hover::before { transform: scaleX(1); }
        .report-card i {
            font-size: 40px; display: block; margin-bottom: 14px;
            transition: transform .3s ease;
        }
        .report-card:hover i { transform: scale(1.1); }
        .report-card h6 { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .report-card p { font-size: 12.5px; color: var(--text-light); margin: 0; }

        /* ─── Warehouse card ────────────────────────────────────── */
        .warehouse-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all .25s cubic-bezier(.4,0,.2,1);
        }
        .warehouse-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .warehouse-card.is-primary { border-color: var(--primary); border-width: 2px; }
        .warehouse-card .wh-body { padding: 20px 22px; }
        .warehouse-card .wh-footer {
            padding: 12px 22px;
            background: var(--bg);
            border-top: 1px solid var(--border);
            display: flex; gap: 8px;
        }

        /* ─── User avatar ───────────────────────────────────────── */
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 13px; font-weight: 700; color: #fff;
        }

        /* ─── Stagger animation ─────────────────────────────────── */
        .stagger > * {
            animation: slideDown .3s ease both;
        }
        .stagger > *:nth-child(1) { animation-delay: 0s; }
        .stagger > *:nth-child(2) { animation-delay: .05s; }
        .stagger > *:nth-child(3) { animation-delay: .1s; }
        .stagger > *:nth-child(4) { animation-delay: .15s; }

        /* ─── Colored accent cards (dashboard / module highlights) ── */
        .accent-card {
            border-radius: var(--radius-lg);
            padding: 22px 24px;
            color: #fff;
            position: relative; overflow: hidden;
        }
        .accent-card::after {
            content: '';
            position: absolute; right: -20px; bottom: -20px;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
        }
        .accent-card.red    { background: linear-gradient(135deg, var(--primary), #FF6B7A); box-shadow: 0 6px 20px rgba(220,30,46,.3); }
        .accent-card.green  { background: linear-gradient(135deg, #059669, #34D399); box-shadow: 0 6px 20px rgba(16,185,129,.3); }
        .accent-card.blue   { background: linear-gradient(135deg, #1D4ED8, #60A5FA); box-shadow: 0 6px 20px rgba(59,130,246,.3); }
        .accent-card.purple { background: linear-gradient(135deg, #6D28D9, #A78BFA); box-shadow: 0 6px 20px rgba(109,40,217,.3); }
        .accent-card.amber  { background: linear-gradient(135deg, #D97706, #FCD34D); box-shadow: 0 6px 20px rgba(217,119,6,.3); }

        /* ─── Section color header ──────────────────────────────── */
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            font-weight: 700; font-size: 13.5px;
        }
        .section-header.sh-red    { background: var(--primary-lighter); color: var(--primary-dark); border: 1px solid rgba(220,30,46,.15); }
        .section-header.sh-green  { background: var(--green-light); color: var(--green-dark); border: 1px solid rgba(16,185,129,.15); }
        .section-header.sh-blue   { background: var(--blue-light); color: var(--blue-dark); border: 1px solid rgba(59,130,246,.15); }
        .section-header.sh-purple { background: var(--purple-light); color: var(--purple-dark); border: 1px solid rgba(139,92,246,.15); }

        /* ─── Empty state enhanced ──────────────────────────────── */
        .empty-state i {
            background: linear-gradient(135deg, var(--primary-lighter), var(--bg));
            width: 72px; height: 72px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 30px !important; opacity: 1 !important;
            color: var(--text-lighter);
            margin-bottom: 16px;
        }

        /* ─── Progress bar enhanced ─────────────────────────────── */
        .progress-bar-fill { background: linear-gradient(90deg, var(--primary), var(--primary-light), #FF9AA2); }

        /* ─── Pulse glow on active nav item ─────────────────────── */
        @keyframes navPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(220,30,46,.2); } 50% { box-shadow: 0 0 0 4px rgba(220,30,46,.08); } }
        .nav-link.active { animation: navPulse 3s ease infinite; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ─── Sidebar overlay (mobile) ───────────────────────────── --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- ─── Sidebar ─────────────────────────────────────────────── --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo"><img src="{{ asset('images/logo.png') }}" alt="Logo"></div>
        <div class="sidebar-brand-text">
            <h2>BÚHO</h2>
            <span>Publicidad con Calle</span>
        </div>
    </div>

    @php $user = auth()->user(); @endphp

    <div class="sidebar-user mx-2" style="margin: 14px 16px 0;">
        <div class="sidebar-user-name">{{ $user->nombre_completo }}</div>
        <div class="sidebar-user-role">{{ $user->rol }}</div>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-section-label">General</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>Dashboard
        </a>

        @if($user->esAdmin() || $user->tienePermiso('empresas'))
        <div class="nav-section-label">Clientes</div>
        <a href="{{ route('empresas.index') }}" class="nav-link {{ request()->routeIs('empresas.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>Empresas
        </a>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('cobranzas'))
        <a href="{{ route('cobranzas.index') }}" class="nav-link {{ request()->routeIs('cobranzas.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i>Cobranzas
        </a>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('ingresos') || $user->tienePermiso('egresos') || $user->esAdmin())
        <div class="nav-section-label">Finanzas</div>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('ingresos'))
        <a href="{{ route('ingresos.index') }}" class="nav-link {{ request()->routeIs('ingresos.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-down-circle"></i>Ingresos
        </a>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('egresos'))
        <a href="{{ route('egresos.index') }}" class="nav-link {{ request()->routeIs('egresos.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-up-circle"></i>Egresos
        </a>
        @endif

        @if($user->esAdmin())
        <a href="{{ route('deudas.index') }}" class="nav-link {{ request()->routeIs('deudas.*') ? 'active' : '' }}">
            <i class="bi bi-exclamation-triangle"></i>Deudas
        </a>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('paneles_digitales') || $user->tienePermiso('paneles_tradicionales') || $user->tienePermiso('control_publicitario'))
        <div class="nav-section-label">Paneles</div>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('paneles_digitales'))
        <a href="{{ route('paneles-digitales.index') }}" class="nav-link {{ request()->routeIs('paneles-digitales.*') ? 'active' : '' }}">
            <i class="bi bi-display"></i>Paneles Digitales
        </a>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('paneles_tradicionales'))
        <a href="{{ route('paneles-tradicionales.index') }}" class="nav-link {{ request()->routeIs('paneles-tradicionales.*') ? 'active' : '' }}">
            <i class="bi bi-signpost-2"></i>Paneles Tradicionales
        </a>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('control_publicitario'))
        <a href="{{ route('control-publicitario.index') }}" class="nav-link {{ request()->routeIs('control-publicitario.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-check"></i>Control Publicitario
        </a>
        <a href="{{ route('parrilla.hoy') }}" class="nav-link {{ request()->routeIs('parrilla.*') ? 'active' : '' }}">
            <i class="bi bi-broadcast"></i>Parrilla hoy
        </a>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('contratos') || $user->tienePermiso('cotizaciones') || $user->tienePermiso('tramites'))
        <div class="nav-section-label">Contratos</div>
        @if($user->esAdmin() || $user->tienePermiso('cotizaciones'))
        <a href="{{ route('cotizaciones.index') }}" class="nav-link {{ request()->routeIs('cotizaciones.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-plus"></i>Cotizaciones
        </a>
        @endif
        @if($user->esAdmin() || $user->tienePermiso('contratos'))
        <a href="{{ route('contratos.index') }}" class="nav-link {{ request()->routeIs('contratos.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>Contratos
        </a>
        @endif
        @if($user->esAdmin() || $user->tienePermiso('tramites'))
        <a href="{{ route('tramites.index') }}" class="nav-link {{ request()->routeIs('tramites.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-check"></i>Trámites
        </a>
        @endif
        @endif

        @if($user->esAdmin())
        <div class="nav-section-label">Inventario</div>
        <a href="{{ route('almacenes.index') }}" class="nav-link {{ request()->routeIs('almacenes.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>Almacenes
        </a>
        @endif

        @if($user->esAdmin() || $user->tienePermiso('reportes'))
        <div class="nav-section-label">Análisis</div>
        <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i>Reportes
        </a>
        @endif

        @if($user->esAdmin())
        <div class="nav-section-label">Sistema</div>
        <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>Usuarios
        </a>
        <a href="{{ route('servicios.index') }}" class="nav-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>Servicios
        </a>
        <a href="{{ route('auditoria.index') }}" class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i>Auditoría
        </a>
        @endif

    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>Cerrar sesión
            </button>
        </form>
    </div>
</aside>

{{-- ─── Main ────────────────────────────────────────────────── --}}
<div class="main-wrapper">
    <header class="topbar">
        <div>
            <div class="topbar-title">
                @yield('title', 'Dashboard')
                @hasSection('subtitle')
                <small>@yield('subtitle')</small>
                @endif
            </div>
        </div>
        <div class="topbar-right">
            @if($user->esAdmin())
            <span class="topbar-badge"><i class="bi bi-shield-fill" style="margin-right:5px"></i>Admin</span>
            @elseif($user->esGerencia())
            <span class="topbar-badge" style="background:var(--purple-light);color:var(--purple-dark);border-color:rgba(139,92,246,.2)">
                <i class="bi bi-person-check" style="margin-right:5px"></i>Gerencia
            </span>
            @else
            <span class="topbar-badge" style="background:#DBEAFE;color:#1E40AF;border-color:rgba(37,99,235,.2)">
                <i class="bi bi-building" style="margin-right:5px"></i>{{ $user->empresa->nombre ?? 'Empresa' }}
            </span>
            @endif
            <button class="btn btn-sm btn-secondary btn-menu-toggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </header>

    <main class="page-content">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
            <button class="alert-dismiss" onclick="this.parentElement.remove()">×</button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
            <button class="alert-dismiss" onclick="this.parentElement.remove()">×</button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul style="margin:0;padding-left:16px">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
            <button class="alert-dismiss" onclick="this.parentElement.remove()">×</button>
        </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
    }
    // Cerrar sidebar al tocar un link en móvil
    document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024) closeSidebar();
        });
    });
</script>
</body>
</html>
