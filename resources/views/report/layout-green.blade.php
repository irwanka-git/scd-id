<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <style type="text/css">
        body{
            font-size: 1.1em !important;
            font-family: 'Arial Narrow', sans-serif;   
        }
        
        .fa-check:before {
            content: url(/images/check.png);
        }
        .jf{
          text-align: justify !important;
        }
        .tc{
          text-align: center; !important;
        }
        tr > td{
            vertical-align: middle; !important;
           
        }
        td.tpad40{
           padding-left: 40px !important;
        }

        td.tpad25{
           padding-left: 25px !important;
        }

        td.tpad10{
           padding-left: 10px !important;
        }
        td.tc{
          text-align: center !important;
        }
        td.jf{
           text-align: justify !important;
        }
        
        table.table-x {
          font-family: 'Arial Narrow', sans-serif;
          border: 1px solid #000000;
          width: 100%;
          border-collapse: collapse;
        }

        table.table-x td, table.table-x th {
          border: 1px solid #000000;
          padding: 3px 4px;
        }
         
        table.table-x thead {
          background: #ededed;
          border-bottom: 2px solid #000000;
        }

        table.table-x thead th {
          font-weight: bold;
          color: #000000;
        }
        table.table-x tfoot {
          font-weight: bold;
          color: #000000;
          border-top: 3px solid #000000;
        }
        table.table-x tfoot td {
          font-size: 14px;
        }

        td.tr{
          text-align:  right !important;
        }
         td.tc{
          text-align:  center !important;
        }
        big{
          font-size: 1.1em !important;
        }
        big2{
          font-size: 1.05em !important;
        }
        .sm{
          font-size: 0.9em;
        }

        table tbody, .table td, .table tfoot, .table th, .table thead, .table tr {
            border: 1px solid #1f2d19 !important;
        }
        .table-primary {
            background: #d7f6bf !important;
        }

        thead { display: table-row-group }
        tfoot { display: table-row-group }
        tr { page-break-inside: avoid !important }
        
        table { overflow: visible !important; }
        tfoot { display: table-row-group }
        tr { page-break-inside: avoid }
        thead.repeat { display: table-header-group !important }
        .new-page {
          page-break-before: always !important;
        }

        @media print {
          .new-page {
            page-break-before: always;
          }
        }
    </style>
</head>
<body>
@section("content")
@show
</body>
</html>