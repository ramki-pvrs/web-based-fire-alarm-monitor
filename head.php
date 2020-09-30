<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="static/public/DataTables/datatables.min.css">
<link rel="stylesheet" href="static/public/FontAwesome/fontawesome-free-5.1.0-web/css/all.css">
<link href="static/appspecific/css/login_style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="static/appspecific/css/chooseFile.css">
<link rel="stylesheet" href="static/public/Bootstrap/css/bootstrap.min.css">
<!-- <link rel="stylesheet" href="static/appspecific/css/bootstrap-multiselect.css"> -->
<script type="text/javascript" src="static/public/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="static/public/js/popper.min.js"></script>
<script type="text/javascript" src="static/public/js/tooltip.js"></script>
<script type="text/javascript" src="static/public/js/moment-with-locales.js"></script>
<script type="text/javascript" src="static/public/Bootstrap/js/bootstrap.min.js"></script>
<!-- <script type="text/javascript" src="static/public/js/bootstrap-multiselect.js"></script> -->
<!-- Note that datatable should be before the sorting and moment scripts are included -->
<script type="text/javascript" src="static/public/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="static/public/DataTables/datetime-moment.js"></script>
<!-- Chrome does not show x mark in Datatable search box, we need this style fix for it -->
<style type="text/css">
    input[type="search"] {
        -webkit-appearance: searchfield;
    }

    input[type="search"]::-webkit-search-cancel-button {
        -webkit-appearance: searchfield-cancel-button;
    }
    .dataTables_filter input {
       background-color: yellow;
       margin-bottom: 10px;
    }

    .navLinkItags {
        aria-hidden:  true;
        font-size: 24px;
        color:  black;
    }
    .hideNavLinks {
        display:  none;
    }
    .showNavLinks {
        display:  block;
    }
    .fa-disabled {
          opacity: 0.6;
          cursor: not-allowed;
    }
</style>