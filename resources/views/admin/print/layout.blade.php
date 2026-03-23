<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Print')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 1in;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        @media screen {
            body {
                background: #f5f5f5;
                padding: 30px 20px 60px;
            }
            
            .print-preview {
                width: 100%;
                max-width: 210mm;
                margin: 0 auto 40px;
                background: #fff;
                padding: 18mm 20mm;
                border-radius: 8px;
                box-shadow: 0 2px 12px rgba(0,0,0,0.12);
                min-height: auto;
            }
            
            .print-btn {
                display: block;
                position: fixed;
                top: 30px;
                right: 30px;
                padding: 10px 20px;
                background: #2563eb;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                box-shadow: 0 4px 12px rgba(37,99,235,0.3);
            }
            
            .print-btn:hover {
                background: #1d4ed8;
            }

            .table-wrapper {
                width: 100%;
                overflow-x: auto;
                margin-bottom: 12px;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <button class="print-btn no-print" type="button" onclick="window.print()">Print</button>
    @yield('content')
</body>
</html>
