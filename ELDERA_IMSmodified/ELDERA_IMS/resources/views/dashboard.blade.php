<x-sidebar>
  <x-header>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ELDERA - Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5faff;
            font-size: clamp(14px, 2vw, 16px);
        }
        .main {
            margin-left: clamp(0px, 25vw, 250px);
            padding: clamp(50px, 8vh, 70px) 0 0 0;
            overflow: hidden;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            overflow-y: auto;
            padding: clamp(10px, 3vw, 20px);
            flex: 1;
        }
        
        .stats {
            display: flex;
            gap: clamp(10px, 2vw, 20px);
            margin-top: clamp(10px, 2vh, 20px);
            flex-wrap: wrap;
        }
        .stat {
            background: white;
            flex: 1;
            min-width: clamp(180px, 25vw, 250px);
            padding: clamp(12px, 2vw, 20px);
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        .stat.green { background: #00CC63; color: white; }
        .stat.yellow { background: #F7B720; color: white; } 
        .stat.red { background: #F72020; color: white; }
        .stat.blue { background: #208FF7; color: white; }

        .stat-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .stat-left i {
            font-size: clamp(18px, 3vw, 24px);
            margin-bottom: 4px;
        }

        .stat-left span {
            font-size: clamp(12px, 1.5vw, 14px);
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-divider {
            width: clamp(3px, 0.5vw, 4px);
            height: clamp(50px, 8vw, 60px);
            background: linear-gradient(90deg, #252424 0%, #252424 50%, #edeaea 50%, #d3d3d3 100%);
            margin: 0 clamp(10px, 2vw, 15px);
            border-radius: 20px;
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-right {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
        }

        .stat-right strong {
            font-size: clamp(20px, 4vw, 28px);
            font-weight: bold;
        }

        .stats {
            background: white;
            margin-top: 10px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .section {
            background: white;
            margin-top: clamp(15px, 3vh, 20px);
            padding: clamp(15px, 3vw, 20px);
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .section h3 {
            margin-top: 0;
            font-size: clamp(16px, 2.5vw, 20px);
        }
        .charts {
            display: flex;
            gap: clamp(15px, 3vw, 20px);
            flex-wrap: wrap;
        }
        .chart, .events {
            flex: 1;
            min-width: clamp(250px, 35vw, 280px);
            padding: clamp(8px, 1.5vw, 10px);
        }
       .events-panel {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px #0001;
        padding: clamp(12px, 2.5vw, 18px);
        min-width: clamp(280px, 40vw, 320px);
        flex: 1.2;
        display: flex;
        flex-direction: column;
    }
    .events-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .events-table th, .events-table td {
        padding: clamp(4px, 1vw, 8px);
        text-align: left;
        font-size: clamp(12px, 1.5vw, 14px);
    }
    .events-table th {
        color: #222;
        font-weight: bold;
        border-bottom: 2px solid #e0e0e0;
    }
    .event-color {
        width: clamp(14px, 2vw, 18px);
        height: clamp(14px, 2vw, 18px);
        border-radius: 4px;
        display: inline-block;
        margin-right: clamp(6px, 1vw, 8px);
    }
    .event-general { background: #19e36c; }
    .event-health { background: #e33c3c; }
    .event-pension { background: #3c8be3; }
    .events-link {
        color: #3c8be3;
        font-weight: bold;
        text-align: right;
        text-decoration: none;
        margin-top: 4px;
        font-size: 14px;
        align-self: flex-end;
    }
        .btn {
            padding: clamp(8px, 1.5vw, 10px) clamp(15px, 3vw, 20px);
            margin: 5px;
            border: none;
            color: white;
            background: #277da1;
            cursor: pointer;
            border-radius: 5px;
            font-size: clamp(12px, 1.5vw, 14px);
    }
    

    .main-footer {
            background: #fff;
            padding: clamp(15px, 3vw, 20px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -1px 4px #0001;
            position: fixed;
            bottom: 0;
            left: clamp(0px, 25vw, 250px);
            right: 0;
            height: clamp(15px, 3vh, 20px);
            z-index: 1;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .footer-logo {
            height: clamp(40px, 6vh, 50px);
            display: block;
            margin: 0 auto;
        }

        /* Pie Charts */
        .chart-container {
          width: clamp(80px, 15vw, 120px);
          height: clamp(80px, 15vw, 120px);
          margin: 0 auto;
          max-width: 100%;
        }
        .card {
          background: #fff;
          border-radius: 10px;
          box-shadow: 0 2px 8px #0001;
          padding: 0;
          margin-bottom: clamp(10px, 2vh, 15px);
          display: flex;
          flex-direction: column;
          min-width: clamp(220px, 30vw, 260px);
          max-width: 100%;
        }
        .card-header {
          background: #555555;
          color: #fff;
          font-weight: bold;
          text-transform: uppercase;
          font-size: clamp(11px, 1.5vw, 13px);
          padding: clamp(6px, 1.5vw, 8px) clamp(12px, 2.5vw, 16px);
          border-radius: 10px 10px 0 0;
          letter-spacing: 1px;
        }
        .card-content {
          padding: clamp(8px, 2vw, 12px) clamp(12px, 2.5vw, 16px) clamp(4px, 1vw, 6px) clamp(12px, 2.5vw, 16px);
          display: flex;
          flex-direction: column;
          align-items: center;
        }
        .card-footer {
          display: flex;
          justify-content: flex-end;
          align-items: center;
          padding: 0 clamp(12px, 2.5vw, 16px) clamp(8px, 2vh, 12px) clamp(12px, 2.5vw, 16px);
        }
        .legend {
          display: flex;
          gap: clamp(8px, 2vw, 12px);
          margin-top: clamp(6px, 1.5vh, 8px);
          font-size: clamp(8px, 1.2vw, 10px);
          justify-content: center;
          flex-wrap: wrap;
        }
        .legend-item {
          display: flex;
          align-items: center;
          gap: clamp(3px, 0.5vw, 4px);
        }
        .legend-color {
          width: clamp(8px, 1.5vw, 10px);
          height: clamp(8px, 1.5vw, 10px);
          border-radius: 3px;
          display: inline-block;
        }
        .legend-male { background: #208FF7; }
        .legend-female { background: #F72020; }
        .legend-pension { background: #e31575 }
        .legend-nopension { background: #e0e0e0; }

        .event-general { background: #19e36c; }
        .event-pension { background: #3c8be3; }
        .event-health { background: #e33c3c }
        .event-id_claiming { background: #ffd500; }

        .events-card {
          background: #fff;
          border-radius: 10px;
          box-shadow: 0 2px 8px #0001;
          padding: 0;
          display: flex;
          flex-direction: column;
          min-width: 320px;
          max-width: 100%;
          border: 1px solid #e0e0e0;
          overflow: hidden;
        }
        .events-header {
          background: #555555;
          color: #fff;
          font-weight: bold;
          text-transform: uppercase;
          font-size: 13px;
          padding: 8px 16px;
          border-radius: 5px 5px 0 0;
          letter-spacing: 1px;
        }
        .events-table {
          width: 100%;
          border-collapse: collapse;
          margin-bottom: 0;
          background: white;
        }
        .events-table th, .events-table td {
          padding: 8px 12px;
          text-align: left;
          font-size: 14px;
          border-bottom: 1px solid #e0e0e0;
        }
        .events-table th {
          background: #f8f9fa;
          color: #333;
          font-weight: bold;
          font-size: 13px;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }
        .events-table-header {
          background: #f8f9fa;
          border-bottom: 2px solid #dee2e6;
          position: sticky;
          top: 0;
          z-index: 10;
        }
        .events-table-header th {
          background: #f8f9fa;
          position: static;
          z-index: auto;
          font-weight: 600;
          color: #495057;
        }
        .events-table tbody tr:hover {
          background: #f8f9fa;
        }
        .events-table tbody tr:hover td a {
          color: #1a5f7a !important;
        }
        .events-table tbody tr:last-child td {
          border-bottom: none;
        }
        .event-color {
          width: 12px;
          height: 12px;
          border-radius: 3px;
          display: inline-block;
        }
        .events-table-body::-webkit-scrollbar {
          width: 8px;
        }
        .events-table-body::-webkit-scrollbar-track {
          background: #f1f1f1;
          border-radius: 4px;
        }
        .events-table-body::-webkit-scrollbar-thumb {
          background: #c1c1c1;
          border-radius: 4px;
        }
        .events-table-body::-webkit-scrollbar-thumb:hover {
          background: #a8a8a8;
        }
        .event-general { background: #19e36c; }
        .event-health { background: #e33c3c; }
        .event-pension { background: #3c8be3; }
        .event-id_claiming { background: #ffd500; }
        .events-link {
          color: #277da1;
          font-weight: bold;
          text-align: right;
          text-decoration: none;
          margin-top: 4px;
          font-size: 14px;
          align-self: flex-end;
        }


        .charts {
          display: flex;
          gap: 20px;
        }

        @media (max-width: 900px) {
          .charts {
            flex-direction: column;
          }
        }

        /* Filter Dropdown Styling */
        .filter-group {
            position: relative;
        }

        .filter-btn {
            background: #f8f9fa;
            border: 2px solid #ddd;
            border-radius: 6px;
            padding: clamp(6px, 1.5vw, 8px) clamp(10px, 2vw, 12px);
            font-size: clamp(12px, 1.5vw, 14px);
            font-weight: 500;
            color: #333;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: clamp(4px, 1vw, 6px);
            transition: all 0.3s ease;
            white-space: nowrap;
            min-width: clamp(150px, 25vw, 200px);
        }

        .filter-btn:hover {
            background: #e9ecef;
            border-color: #CC0052;
            color: #CC0052;
        }

        .filter-btn.active {
            background: #CC0052;
            border-color: #CC0052;
            color: #fff;
        }

        .filter-btn i.fas.fa-chevron-down {
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        .filter-btn.active i.fas.fa-chevron-down {
            transform: rotate(180deg);
        }

        .filter-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            display: none;
            min-width: clamp(150px, 25vw, 200px);
            margin-top: clamp(3px, 1vh, 5px);
        }

        .filter-dropdown.show {
            display: block;
        }

        .filter-dropdown-content {
            padding: clamp(8px, 2vw, 10px);
            max-height: clamp(200px, 40vh, 300px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: clamp(6px, 1.5vw, 8px);
            padding: clamp(4px, 1vw, 5px);
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s ease;
            font-size: clamp(12px, 1.5vw, 14px);
        }

        .filter-option:hover {
            background: #f8f9fa;
        }

        .filter-option input[type="radio"] {
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .main {
                margin-left: 200px;
            }
            
            .header {
                left: 200px;
            }
            
            .main-footer {
                left: 200px;
            }
        }

        @media (max-width: 992px) {
            .main {
                margin-left: 0;
                padding-top: 60px;
            }
            
            .header {
                left: 0;
            }
            
            .main-footer {
                left: 0;
            }
            
            .stats {
                gap: 15px;
            }
            
            .stat {
                min-width: 180px;
                padding: 15px;
            }
            
            .charts {
                gap: 15px;
            }
            
            .card {
                min-width: 240px;
            }
            
            .events-card {
                min-width: 280px;
            }
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding-top: 60px;
            }
            
            .header {
                left: 0;
            }
            
            .main-footer {
                left: 0;
            }
            
            .stats {
                flex-direction: column;
                gap: 10px;
            }
            
            .stat {
                min-width: unset;
                width: 100%;
            }
            
            .charts {
                flex-direction: column;
                gap: 15px;
            }
            
            .card, .events-card {
                min-width: unset;
                width: 100%;
            }
            
            .chart-container {
                width: 100px;
                height: 100px;
            }
            
            .events-table th, .events-table td {
                padding: 6px 4px;
                font-size: 12px;
            }
            
            .legend {
                flex-wrap: wrap;
                gap: 8px;
            }
        }

        @media (max-width: 576px) {
            .main {
                margin-left: 0;
                padding-top: 60px;
            }
            
            .header {
                left: 0;
            }
            
            .main-footer {
                left: 0;
            }
            
            .main-content {
                padding: 10px;
            }
            
            .stat {
                padding: 12px;
                flex-direction: column;
                text-align: center;
            }
            
            .stat-divider {
                width: 60px;
                height: 4px;
                margin: 10px 0;
            }
            
            .stat-left span {
                font-size: 12px;
            }
            
            .stat-right strong {
                font-size: 24px;
            }
            
            .chart-container {
                width: 80px;
                height: 80px;
            }
            
            .events-table {
                font-size: 11px;
            }
            
            .events-table th, .events-table td {
                padding: 4px 2px;
            }
            
            .card-header, .events-header {
                font-size: 11px;
                padding: 6px 12px;
            }
        }

        /* Extra small screens - for very narrow windows */
        @media (max-width: 400px) {
            .main {
                margin-left: 0;
                padding-top: 50px;
            }
            
            .main-content {
                padding: 5px;
            }
            
            .stat {
                padding: 8px;
                min-height: auto;
            }
            
            .stat-left i {
                font-size: 18px;
            }
            
            .stat-left span {
                font-size: 10px;
            }
            
            .stat-right strong {
                font-size: 20px;
            }
            
            .chart-container {
                width: 60px;
                height: 60px;
            }
            
            .card-header, .events-header {
                font-size: 10px;
                padding: 4px 8px;
            }
            
            .events-table th, .events-table td {
                padding: 2px 1px;
                font-size: 9px;
            }
            
            .legend {
                font-size: 8px;
                gap: 4px;
            }
            
            .legend-color {
                width: 8px;
                height: 8px;
            }
            
            .filter-btn {
                min-width: 150px;
                font-size: 12px;
                padding: 6px 8px;
            }
        }
    </style>
</head>
<body>
    
   
    
    <div class="main">
        <div class="main-content">
                 <div class="stats">
            <div class="stat green">
                <div class="stat-left">
                    <i class="fas fa-home"></i>
                    <span>BARANGAY</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-right">
                    <strong>{{ $stats['barangays']['total'] ?? 0 }}</strong>
                </div>
            </div>

            <div class="stat yellow">
                <div class="stat-left">
                    <i class="fas fa-users"></i>
                    <span>SENIORS</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-right">
                    <strong>{{ number_format($stats['seniors']['total'] ?? 0) }}</strong>
                </div>
            </div>

            <div class="stat red">
                <div class="stat-left">
                    <i class="fas fa-female"></i>
                    <span>FEMALE</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-right">
                    <strong>{{ number_format($stats['seniors']['female'] ?? 0) }}</strong>
                </div>
            </div>
            <div class="stat blue">
                <div class="stat-left">
                    <i class="fas fa-male"></i>
                    <span>MALE</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-right">
                    <strong>{{ number_format($stats['seniors']['male'] ?? 0) }}</strong>
                </div>
            </div>
        </div>

      {{-- Dropdown Button --}}
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <div class="filter-group">
                    <button class="filter-btn" id="barangay-btn" onclick="toggleBarangayDropdown()">
                        <span id="barangay-text">ALL BARANGAY</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="filter-dropdown" id="barangay-dropdown">
                        <div class="filter-dropdown-content">
                            <div class="filter-options">
                                <label class="filter-option" onclick="selectBarangay('')">
                                    <input type="radio" name="barangay" value="" checked>
                                    <span>All Barangay</span>
                                </label>
                                @foreach($stats['barangays']['barangays'] ?? [] as $barangay)
                                <label class="filter-option" onclick="selectBarangay('{{ $barangay['name'] }}')">
                                    <input type="radio" name="barangay" value="{{ $barangay['name'] }}">
                                    <span>{{ $barangay['name'] }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
            
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-start;">
                {{-- Left Column: Pie Charts and Bar Chart --}}
                <div style="display: flex; flex-direction: column; gap: 15px; flex: 1; min-width: 520px;">
                    {{-- Pie Charts Row --}}
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        {{-- With Pension Chart --}}
                        <div class="card" style="flex: 1; min-width: 250px; max-width: 300px; height: 300px; display: flex; flex-direction: column;">
                            <div class="card-header">With Pension</div>
                            <div class="card-content" style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1;">
                                <div class="legend" style="flex-wrap: nowrap; margin-bottom: 8px; padding-top: 8px; font-size: 7px; gap: 6px;">
                                    <div class="legend-item" style="gap: 2px;"><span class="legend-color legend-pension" style="width: 7px; height: 7px;"></span>WITH PENSION</div>
                                    <div class="legend-item" style="gap: 2px;"><span class="legend-color legend-nopension" style="width: 7px; height: 7px;"></span>WITHOUT PENSION</div>
                                </div>
                                <div style="width: 220px; height: 180px; margin: 10px auto; max-width: 100%; display: flex; align-items: center; justify-content: center;">
                                    <canvas id="pensionPieChart" width="180" height="180"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Total Events Chart --}}
                        <div class="card" style="flex: 1; min-width: 250px; max-width: 300px; height: 300px; display: flex; flex-direction: column;">
                            <div class="card-header">TOTAL EVENTS</div>
                            <div class="card-content" style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1;">
                                <div class="legend" style="flex-wrap: nowrap; margin-bottom: 8px; padding-top: 8px; font-size: 7px; gap: 6px;">
                                    <div class="legend-item" style="gap: 2px;"><span class="legend-color event-general" style="width: 7px; height: 7px;"></span>GENERAL</div>
                                    <div class="legend-item" style="gap: 2px;"><span class="legend-color event-pension" style="width: 7px; height: 7px;"></span>PENSION</div>
                                    <div class="legend-item" style="gap: 2px;"><span class="legend-color event-health" style="width: 7px; height: 7px;"></span>HEALTH</div>
                                    <div class="legend-item" style="gap: 2px;"><span class="legend-color event-id_claiming" style="width: 7px; height: 7px;"></span>ID CLAIMING</div>
                                </div>
                                <div style="width: 220px; height: 180px; margin: 10px auto; max-width: 100%; display: flex; align-items: center; justify-content: center;">
                                    <canvas id="eventsPieChart" width="180" height="180"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Age Bar Chart Below Pie Charts --}}
                    <div class="card" style="width: 100%;">
                        <div class="card-header">Senior Citizen by Age</div>
                        <div class="card-content">
                            <div style="width:100%; height:200px; display:flex; align-items:center; justify-content:center;">
                                <canvas id="ageBarChart" width="400" height="180"></canvas>
                            </div>
                            <div class="legend">
                                <div class="legend-item"><span class="legend-color legend-male"></span>MALE</div>
                                <div class="legend-item"><span class="legend-color legend-female"></span>FEMALE</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Right Column: Events Card --}}
                <div class="card" style="flex: 1.5; min-width: 280px; height: 575px; display: flex; flex-direction: column;">
                    <div class="card-header">EVENTS</div>
                    <div class="events-table-container" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">
                        <div class="events-table-header">
                            <table class="events-table" style="margin-bottom: 0; table-layout: fixed;">
                                <colgroup>
                                    <col style="width: 45%;">
                                    <col style="width: 20%;">
                                    <col style="width: 20%;">
                                    <col style="width: 15%;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th style="text-align: left; padding: 12px 12px;">EVENTS</th>
                                        <th style="text-align: center; padding: 12px 8px;">DATE</th>
                                        <th style="text-align: center; padding: 12px 8px;">TIME</th>
                                        <th style="text-align: left; padding: 12px 12px;">PLACE</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="events-table-body" style="flex: 1; overflow-y: auto;">
                            <table class="events-table" style="table-layout: fixed;">
                                <colgroup>
                                    <col style="width: 45%;">
                                    <col style="width: 20%;">
                                    <col style="width: 20%;">
                                    <col style="width: 15%;">
                                </colgroup>
                                <tbody>
                                    @forelse($events as $event)
                                    @php
                                        $eventColors = [
                                            'general' => '#19e36c',
                                            'pension' => '#3c8be3',
                                            'health' => '#e33c3c',
                                            'id_claiming' => '#ffd500'
                                        ];
                                        $eventColor = $eventColors[$event->event_type] ?? '#277da1';
                                    @endphp
                                    <tr style="transition: background-color 0.2s ease;">
                                        <td style="padding: 12px 12px; vertical-align: middle;">
                                            <a href="{{ route('events.show', $event->id) }}" style="color: {{ $eventColor }}; text-decoration: none; font-weight: 600; font-size: 14px; transition: color 0.2s ease; display: inline-block;">{{ $event->title }}</a>
                                        </td>
                                        <td style="padding: 12px 8px; text-align: center; vertical-align: middle; color: #555; font-size: 13px; font-weight: 500;">
                                            {{ $event->event_date->format('d/m/y') }}
                                        </td>
                                        <td style="padding: 12px 8px; text-align: center; vertical-align: middle; color: #555; font-size: 13px; font-weight: 500;">
                                            {{ $event->start_time->format('g:i A') }}
                                        </td>
                                        <td style="padding: 12px 12px; vertical-align: middle; color: #666; font-size: 13px;">
                                            {{ $event->location }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 40px 20px; color: #999; font-style: italic; font-size: 14px;">
                                            No upcoming events
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    {{-- <footer class="main-footer">
        <img src="{{ asset('images/Bagong_Pilipinas.png') }}" alt="Bagong Pilipinas" class="footer-logo">
    </footer> --}}

   
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  if (typeof Chart === 'undefined') {
    console.error('Chart.js not loaded');
    return;
  }

  // Pension Pie Chart
  const pensionCanvas = document.getElementById('pensionPieChart');
  if (pensionCanvas) {
    const pensionCtx = pensionCanvas.getContext('2d');
    const pensionData = [{{ $stats['seniors']['with_pension'] ?? 0 }}, {{ $stats['seniors']['without_pension'] ?? 0 }}]; // With Pension, Without Pension
    const pensionWithCount = {{ $stats['seniors']['with_pension'] ?? 0 }}; // Number of seniors with pension

    window.pensionPieChart = new Chart(pensionCtx, {
      type: 'doughnut',
      data: {
        datasets: [{
          data: pensionData,
          backgroundColor: ['#e31575', '#e0e0e0'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          animateRotate: true,
          animateScale: false,
          duration: 1000,
          easing: 'easeOutQuart'
        },
        layout: {
          padding: {
            top: 30,
            right: 30,
            bottom: 30,
            left: 30
          }
        },
        plugins: {
          legend: { 
            display: false 
          },
          tooltip: {
            enabled: false
          }
        },
        elements: {
          arc: {
            borderWidth: 0
          }
        }
      },
      plugins: [{
        id: 'centerText',
        beforeDraw: function(chart) {
          const width = chart.width;
          const height = chart.height;
          const ctx = chart.ctx;
          
          ctx.restore();
          ctx.font = 'bold 18px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
          ctx.fillStyle = '#333';
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          
          const centerX = width / 2;
          const centerY = height / 2;
          
          // Display number of seniors with pension (first value in the data array)
          const withPensionCount = chart.data.datasets[0].data[0];
          ctx.fillText(withPensionCount.toLocaleString(), centerX, centerY);
          
          ctx.save();
        }
      }, {
        id: 'percentageLabels',
        afterDraw: function(chart) {
          const ctx = chart.ctx;
          const chartArea = chart.chartArea;
          const meta = chart.getDatasetMeta(0);
          const centerX = (chartArea.left + chartArea.right) / 2;
          const centerY = (chartArea.top + chartArea.bottom) / 2;
          const radius = Math.min((chartArea.right - chartArea.left) / 2, (chartArea.bottom - chartArea.top) / 2) * 0.85;
          
          // Speech bubble dimensions
          const bubbleWidth = 45;
          const bubbleHeight = 22;
          const borderRadius = 5;
          const pointerSize = 8;
          const gap = 30; // Spacing between donut and label (increased)
          
          // Calculate outer radius with gap and bubble height
          const outerRadius = radius + gap + (bubbleHeight / 2);
          
          const data = chart.data.datasets[0].data;
          const total = data.reduce((a, b) => a + b, 0);
          const colors = chart.data.datasets[0].backgroundColor;
          
          let currentAngle = -Math.PI / 2; // Start from top
          
          data.forEach((value, index) => {
            if (value === 0) return;
            
            const percentage = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
            const sliceAngle = (value / total) * 2 * Math.PI;
            const midAngle = currentAngle + sliceAngle / 2;
            
            // Calculate position for label outside the donut
            const labelX = centerX + Math.cos(midAngle) * outerRadius;
            const labelY = centerY + Math.sin(midAngle) * outerRadius;
            
            // Calculate pointer position on the donut edge
            const pointerX = centerX + Math.cos(midAngle) * radius;
            const pointerY = centerY + Math.sin(midAngle) * radius;
            
            // Add spacing gap - arrow should not reach the pie chart
            const arrowGap = 8; // Gap between arrow tip and pie chart
            const arrowEndX = centerX + Math.cos(midAngle) * (radius - arrowGap);
            const arrowEndY = centerY + Math.sin(midAngle) * (radius - arrowGap);
            
            ctx.save();
            
            // Calculate bubble position (centered on label position)
            const bubbleX = labelX - bubbleWidth / 2;
            const bubbleY = labelY - bubbleHeight / 2;
            
            // Calculate angle from label center to donut segment
            const angleToSegment = Math.atan2(pointerY - labelY, pointerX - labelX);
            
            // Determine which edge the pointer should connect to based on angle
            const angleDeg = (angleToSegment * 180) / Math.PI;
            const normalizedAngle = ((angleDeg + 360) % 360);
            
            // Find the closest edge point on the bubble
            let pointerEdgeX, pointerEdgeY;
            
            if (normalizedAngle >= 315 || normalizedAngle < 45) {
              // Right edge
              pointerEdgeX = bubbleX + bubbleWidth;
              pointerEdgeY = labelY;
            } else if (normalizedAngle >= 45 && normalizedAngle < 135) {
              // Bottom edge
              pointerEdgeX = labelX;
              pointerEdgeY = bubbleY + bubbleHeight;
            } else if (normalizedAngle >= 135 && normalizedAngle < 225) {
              // Left edge
              pointerEdgeX = bubbleX;
              pointerEdgeY = labelY;
            } else {
              // Top edge
              pointerEdgeX = labelX;
              pointerEdgeY = bubbleY;
            }
            
            // Draw speech bubble (simple rounded rectangle without pointer)
            ctx.fillStyle = colors[index];
            ctx.beginPath();
            ctx.moveTo(bubbleX + borderRadius, bubbleY);
            ctx.lineTo(bubbleX + bubbleWidth - borderRadius, bubbleY);
            ctx.arc(bubbleX + bubbleWidth - borderRadius, bubbleY + borderRadius, borderRadius, -Math.PI / 2, 0);
            ctx.lineTo(bubbleX + bubbleWidth, bubbleY + bubbleHeight - borderRadius);
            ctx.arc(bubbleX + bubbleWidth - borderRadius, bubbleY + bubbleHeight - borderRadius, borderRadius, 0, Math.PI / 2);
            ctx.lineTo(bubbleX + borderRadius, bubbleY + bubbleHeight);
            ctx.arc(bubbleX + borderRadius, bubbleY + bubbleHeight - borderRadius, borderRadius, Math.PI / 2, Math.PI);
            ctx.lineTo(bubbleX, bubbleY + borderRadius);
            ctx.arc(bubbleX + borderRadius, bubbleY + borderRadius, borderRadius, Math.PI, -Math.PI / 2);
            ctx.closePath();
            ctx.fill();
            
            // Draw separate pointer line from bubble to pie segment (with gap, no arrowhead)
            ctx.strokeStyle = colors[index];
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(pointerEdgeX, pointerEdgeY);
            ctx.lineTo(arrowEndX, arrowEndY);
            ctx.stroke();
            
            // Draw percentage text
            ctx.save();
            ctx.fillStyle = '#000';
            ctx.font = 'bold 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(percentage + '%', labelX, labelY);
            ctx.restore();
            
            ctx.restore();
            
            currentAngle += sliceAngle;
          });
        }
      }]
    });
  }

  // Events Pie Chart
  const eventsCanvas = document.getElementById('eventsPieChart');
  if (eventsCanvas) {
    const eventsCtx = eventsCanvas.getContext('2d');
    const eventsData = [
      {{ $eventsByType['general'] ?? 0 }}, 
      {{ $eventsByType['pension'] ?? 0 }}, 
      {{ $eventsByType['health'] ?? 0 }}, 
      {{ $eventsByType['id_claiming'] ?? 0 }}
    ];
    const totalEvents = eventsData.reduce((a, b) => a + b, 0);

    window.eventsPieChart = new Chart(eventsCtx, {
      type: 'doughnut',
      data: {
        datasets: [{
          data: eventsData,
          backgroundColor: ['#19e36c', '#3c8be3', '#e33c3c', '#ffd500'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          animateRotate: true,
          animateScale: false,
          duration: 1000,
          easing: 'easeOutQuart'
        },
        layout: {
          padding: {
            top: 30,
            right: 30,
            bottom: 30,
            left: 30
          }
        },
        plugins: {
          legend: { 
            display: false 
          },
          tooltip: {
            enabled: false
          }
        },
        elements: {
          arc: {
            borderWidth: 0
          }
        }
      },
      plugins: [{
        id: 'centerText',
        beforeDraw: function(chart) {
          const width = chart.width;
          const height = chart.height;
          const ctx = chart.ctx;
          
          ctx.restore();
          ctx.font = 'bold 18px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
          ctx.fillStyle = '#333';
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          
          const centerX = width / 2;
          const centerY = height / 2;
          
          // Display total events count
          const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
          ctx.fillText(total.toLocaleString(), centerX, centerY);
          
          ctx.save();
        }
      }, {
        id: 'percentageLabels',
        afterDraw: function(chart) {
          const ctx = chart.ctx;
          const chartArea = chart.chartArea;
          const meta = chart.getDatasetMeta(0);
          const centerX = (chartArea.left + chartArea.right) / 2;
          const centerY = (chartArea.top + chartArea.bottom) / 2;
          const radius = Math.min((chartArea.right - chartArea.left) / 2, (chartArea.bottom - chartArea.top) / 2) * 0.85;
          
          // Speech bubble dimensions
          const bubbleWidth = 45;
          const bubbleHeight = 22;
          const borderRadius = 5;
          const pointerSize = 8;
          const gap = 30; // Spacing between donut and label (increased)
          
          // Calculate outer radius with gap and bubble height
          const outerRadius = radius + gap + (bubbleHeight / 2);
          
          const data = chart.data.datasets[0].data;
          const total = data.reduce((a, b) => a + b, 0);
          const colors = chart.data.datasets[0].backgroundColor;
          
          let currentAngle = -Math.PI / 2; // Start from top
          
          data.forEach((value, index) => {
            if (value === 0) return;
            
            const percentage = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
            const sliceAngle = (value / total) * 2 * Math.PI;
            const midAngle = currentAngle + sliceAngle / 2;
            
            // Calculate position for label outside the donut
            const labelX = centerX + Math.cos(midAngle) * outerRadius;
            const labelY = centerY + Math.sin(midAngle) * outerRadius;
            
            // Calculate pointer position on the donut edge
            const pointerX = centerX + Math.cos(midAngle) * radius;
            const pointerY = centerY + Math.sin(midAngle) * radius;
            
            // Add spacing gap - arrow should not reach the pie chart
            const arrowGap = 8; // Gap between arrow tip and pie chart
            const arrowEndX = centerX + Math.cos(midAngle) * (radius - arrowGap);
            const arrowEndY = centerY + Math.sin(midAngle) * (radius - arrowGap);
            
            ctx.save();
            
            // Calculate bubble position (centered on label position)
            const bubbleX = labelX - bubbleWidth / 2;
            const bubbleY = labelY - bubbleHeight / 2;
            
            // Calculate angle from label center to donut segment
            const angleToSegment = Math.atan2(pointerY - labelY, pointerX - labelX);
            
            // Determine which edge the pointer should connect to based on angle
            const angleDeg = (angleToSegment * 180) / Math.PI;
            const normalizedAngle = ((angleDeg + 360) % 360);
            
            // Find the closest edge point on the bubble
            let pointerEdgeX, pointerEdgeY;
            
            if (normalizedAngle >= 315 || normalizedAngle < 45) {
              // Right edge
              pointerEdgeX = bubbleX + bubbleWidth;
              pointerEdgeY = labelY;
            } else if (normalizedAngle >= 45 && normalizedAngle < 135) {
              // Bottom edge
              pointerEdgeX = labelX;
              pointerEdgeY = bubbleY + bubbleHeight;
            } else if (normalizedAngle >= 135 && normalizedAngle < 225) {
              // Left edge
              pointerEdgeX = bubbleX;
              pointerEdgeY = labelY;
            } else {
              // Top edge
              pointerEdgeX = labelX;
              pointerEdgeY = bubbleY;
            }
            
            // Draw speech bubble (simple rounded rectangle without pointer)
            ctx.fillStyle = colors[index];
            ctx.beginPath();
            ctx.moveTo(bubbleX + borderRadius, bubbleY);
            ctx.lineTo(bubbleX + bubbleWidth - borderRadius, bubbleY);
            ctx.arc(bubbleX + bubbleWidth - borderRadius, bubbleY + borderRadius, borderRadius, -Math.PI / 2, 0);
            ctx.lineTo(bubbleX + bubbleWidth, bubbleY + bubbleHeight - borderRadius);
            ctx.arc(bubbleX + bubbleWidth - borderRadius, bubbleY + bubbleHeight - borderRadius, borderRadius, 0, Math.PI / 2);
            ctx.lineTo(bubbleX + borderRadius, bubbleY + bubbleHeight);
            ctx.arc(bubbleX + borderRadius, bubbleY + bubbleHeight - borderRadius, borderRadius, Math.PI / 2, Math.PI);
            ctx.lineTo(bubbleX, bubbleY + borderRadius);
            ctx.arc(bubbleX + borderRadius, bubbleY + borderRadius, borderRadius, Math.PI, -Math.PI / 2);
            ctx.closePath();
            ctx.fill();
            
            // Draw separate pointer line from bubble to pie segment (with gap, no arrowhead)
            ctx.strokeStyle = colors[index];
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(pointerEdgeX, pointerEdgeY);
            ctx.lineTo(arrowEndX, arrowEndY);
            ctx.stroke();
            
            // Draw percentage text
            ctx.save();
            ctx.fillStyle = '#000';
            ctx.font = 'bold 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(percentage + '%', labelX, labelY);
            ctx.restore();
            
            ctx.restore();
            
            currentAngle += sliceAngle;
          });
        }
      }]
    });
  }

  // Age Bar Chart
  const ageBarCanvas = document.getElementById('ageBarChart');
  if (ageBarCanvas) {
    const ageBarCtx = ageBarCanvas.getContext('2d');
    const ageDistribution = @json($stats['age_distribution'] ?? []);
    
    window.ageBarChart = new Chart(ageBarCtx, {
      type: 'bar',
      data: {
        labels: ['60-65', '66-70', '71-75', '76-80', '81-85', '86-90', '90+'],
        datasets: [
          {
            label: 'Male',
            data: [
              ageDistribution['60-65']?.male || 0,
              ageDistribution['66-70']?.male || 0,
              ageDistribution['71-75']?.male || 0,
              ageDistribution['76-80']?.male || 0,
              ageDistribution['81-85']?.male || 0,
              ageDistribution['86-90']?.male || 0,
              ageDistribution['90+']?.male || 0
            ],
            backgroundColor: '#208FF7',
            borderRadius: 6,
            barPercentage: 0.5,
            categoryPercentage: 0.6
          },
          {
            label: 'Female',
            data: [
              ageDistribution['60-65']?.female || 0,
              ageDistribution['66-70']?.female || 0,
              ageDistribution['71-75']?.female || 0,
              ageDistribution['76-80']?.female || 0,
              ageDistribution['81-85']?.female || 0,
              ageDistribution['86-90']?.female || 0,
              ageDistribution['90+']?.female || 0
            ],
            backgroundColor: '#F72020',
            borderRadius: 6,
            barPercentage: 0.5,
            categoryPercentage: 0.6
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              font: { size: 12 },
              color: '#444'
            }
          },
          y: {
            beginAtZero: true,
            grid: { color: '#8882', borderDash: [4,4] },
            ticks: {
              font: { size: 12 },
              color: '#444',
              stepSize: 20
            }
          }
        }
      }
    });
  }
});

// Barangay filtering function
function filterByBarangay(selectedBarangay = null) {
    if (selectedBarangay === null) {
        selectedBarangay = document.getElementById('barangay')?.value || '';
    }
    
    if (selectedBarangay && selectedBarangay !== 'ALL BARANGAY' && selectedBarangay !== '') {
        // Show loading state
        showLoadingState();
        
        // Fetch barangay-specific stats
        fetch(`/api/barangay-stats/${encodeURIComponent(selectedBarangay)}?t=${Date.now()}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                console.log('Seniors total:', data.seniors?.total);
                console.log('Seniors male:', data.seniors?.male);
                console.log('Seniors female:', data.seniors?.female);
                updateDashboardStats(data);
                hideLoadingState();
            })
            .catch(error => {
                console.error('Detailed error:', error);
                console.error('Error message:', error.message);
                hideLoadingState();
                alert(`Error loading barangay statistics: ${error.message}. Please check the console for details.`);
            });
    } else {
        // Reset to all barangays
        location.reload();
    }
}

function showLoadingState() {
    // Add loading overlay or spinner
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loading-overlay';
    loadingDiv.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    `;
    loadingDiv.innerHTML = '<div style="color: white; font-size: 18px;">Loading statistics...</div>';
    document.body.appendChild(loadingDiv);
}

function hideLoadingState() {
    const loadingDiv = document.getElementById('loading-overlay');
    if (loadingDiv) {
        loadingDiv.remove();
    }
}

// New dropdown functions
function toggleBarangayDropdown() {
    const dropdown = document.getElementById('barangay-dropdown');
    const btn = document.getElementById('barangay-btn');
    
    if (dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
        btn.classList.remove('active');
    } else {
        // Close any other open dropdowns
        document.querySelectorAll('.filter-dropdown.show').forEach(d => d.classList.remove('show'));
        document.querySelectorAll('.filter-btn.active').forEach(b => b.classList.remove('active'));
        
        dropdown.classList.add('show');
        btn.classList.add('active');
    }
}

function selectBarangay(barangayName) {
    const btn = document.getElementById('barangay-btn');
    const text = document.getElementById('barangay-text');
    const dropdown = document.getElementById('barangay-dropdown');
    
    // Update button text
    text.textContent = barangayName || 'ALL BARANGAY';
    
    // Close dropdown
    dropdown.classList.remove('show');
    btn.classList.remove('active');
    
    // Update radio button selection
    document.querySelectorAll('input[name="barangay"]').forEach(radio => {
        radio.checked = radio.value === barangayName;
    });
    
    // Call the existing filter function
    filterByBarangay(barangayName);
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.filter-group')) {
        document.querySelectorAll('.filter-dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
        document.querySelectorAll('.filter-btn.active').forEach(btn => {
            btn.classList.remove('active');
        });
    }
});

function updateDashboardStats(data) {
    console.log('Updating dashboard with data:', data);
    
    // Update stats cards
    const statCards = document.querySelectorAll('.stat-right strong');
    console.log('Found stat cards:', statCards.length);
    
    if (statCards.length >= 4) {
        statCards[0].textContent = data.barangays?.selected || 'N/A';
        statCards[1].textContent = (data.seniors?.total || 0).toLocaleString();
        statCards[2].textContent = (data.seniors?.female || 0).toLocaleString();
        statCards[3].textContent = (data.seniors?.male || 0).toLocaleString();
        console.log('Stats cards updated successfully');
    } else {
        console.error('Not enough stat cards found. Expected 4, found:', statCards.length);
    }
    
    // Update pension pie chart
    if (window.pensionPieChart) {
        window.pensionPieChart.data.datasets[0].data = [parseInt(data.seniors?.with_pension) || 0, parseInt(data.seniors?.without_pension) || 0];
        window.pensionPieChart.update();
        console.log('Pension pie chart updated');
    } else {
        console.warn('Pension pie chart not found');
    }
    
    // Update age distribution bar chart
    if (window.ageBarChart) {
        const ageDistribution = data.age_distribution || {};
        window.ageBarChart.data.datasets[0].data = [
            ageDistribution['60-65']?.male || 0,
            ageDistribution['66-70']?.male || 0,
            ageDistribution['71-75']?.male || 0,
            ageDistribution['76-80']?.male || 0,
            ageDistribution['81-85']?.male || 0,
            ageDistribution['86-90']?.male || 0,
            ageDistribution['90+']?.male || 0
        ];
        window.ageBarChart.data.datasets[1].data = [
            ageDistribution['60-65']?.female || 0,
            ageDistribution['66-70']?.female || 0,
            ageDistribution['71-75']?.female || 0,
            ageDistribution['76-80']?.female || 0,
            ageDistribution['81-85']?.female || 0,
            ageDistribution['86-90']?.female || 0,
            ageDistribution['90+']?.female || 0
        ];
        window.ageBarChart.update();
        console.log('Age bar chart updated');
    } else {
        console.warn('Age bar chart not found');
    }
}

</script>

</body>
</html>
    </x-head>
</x-sidebar>