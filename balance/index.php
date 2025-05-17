<?
    
session_start();

if ( !isset($_SESSION["id"]) || $_SESSION["id"] > 4) {
    header("location: /");
    exit;
}

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>ELDIR electro 2023!!!</title>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    
     <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
    
<!--    <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>  -->
    <script src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script>
    
</head>
<body>
    <? require_once('../nav.php'); ?>
   
     <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1>ELDIR | Касса</h1>
            </div>
        </div>
        <?  require_once('nav_balance.php'); 
         
        ?>
        
        <div class="row mt-3">
            <div class="col-md-12">

                <div class="table-responsive-sm">
                    <table id="myTable" class=" table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Расход (затраты)</th>
                                <th>Приход (доход)</th>
                                <th>Сальдо</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            require __DIR__ . '/../dbcon.php';

                            $query = "SELECT YEAR(date_operations) as year, MONTH(date_operations) AS month, SUM(summa) AS total_amount, `sign_of_calculation` FROM `balance_income_expenses` WHERE `history` = 0 and is_deleted = 0 GROUP BY YEAR(date_operations), MONTH(date_operations), `sign_of_calculation` ORDER BY YEAR(date_operations), MONTH(date_operations)";
                            $query_run = mysqli_query($con, $query);

                            if(mysqli_num_rows($query_run) > 0)
                            {   
                            
                                $tbl = [];
                                
                                $graf = '[';
                                
                                foreach($query_run as $payment)
                                {
                                    
                                    $monthyear = $payment['year'].'/'.$payment['month'];
                                    
                                    if($payment['sign_of_calculation'] == 'income')
                                    {
                                        $tbl[$monthyear]['income'] = $payment['total_amount']; 
                                    }
                                    
                                    if($payment['sign_of_calculation'] == 'expenses')
                                    {
                                        $tbl[$monthyear]['expenses'] = $payment['total_amount'];
                                    }
                                    
                                }
                                
                                $saldo_mnth = $saldo;
                                
                                foreach($tbl as $key=>$tr)
                                {  
                                    $expenses_mnth = isset($tr['expenses']) ? $tr['expenses'] : 0;
                                    $income_mnth = isset($tr['income']) ? $tr['income'] : 0;
                                    $itogo_mnth = $income_mnth-$expenses_mnth;
                                    $saldo_mnth = $saldo_mnth+$itogo_mnth;
                                    
                                    ?>
                                    <tr>
                                        <td><?= $key ?></td>
                                        <td class="table-danger"><?= isset($tr['expenses']) ? $tr['expenses'] : 0 ?></td>
                                        <td class="table-success"><?= isset($tr['income']) ? $tr['income'] : 0 ?></td>
                                        <td class="table-primary"><?= $saldo_mnth ?></td>
                                    </tr>
                                    <?php 
                                    $graf .= '["'.$key.'",'.$expenses_mnth.']';
                                }
                                
                                $graf .= ']';
                                
                            }
                            ?>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        
            <div class="col-md-12">
                <h2>По дням</h2>
            </div>
            <div class="col-md-12">
                <div class="table-responsive-sm">
                    <table id="myTableDay" class=" table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Расход</th>
                                <th>Приход</th>
                                <th>Дневной баланс</th>
                                <th>Сальдо</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            require __DIR__ . '/../dbcon.php';

                            $query = "SELECT SUM(summa) AS total_amount, CASE WHEN sign_of_calculation = 'income' THEN 'Доход' WHEN sign_of_calculation = 'expenses' THEN 'Расход' END AS sign_of_calculation_word, sign_of_calculation, DATE_FORMAT(date_operations,'%d/%m/%Y') as date, date_operations  FROM `balance_income_expenses` WHERE `history` = 0 AND is_deleted = 0 GROUP BY date_operations, `sign_of_calculation` ORDER BY date_operations;";
                            $query_run = mysqli_query($con, $query);

                            if(mysqli_num_rows($query_run) > 0)
                            {
                                $tbl_day = [];
                                
                                foreach($query_run as $payment)
                                {
                                    
                                    if($payment['sign_of_calculation'] == 'income')
                                    {
                                        $tbl_day[$payment['date_operations']]['income'] = $payment['total_amount']; 
                                    }
                                    /*
                                    else
                                    {
                                        $tbl_day[$payment['date_operations']]['income'] = 0;
                                    }
                                    */
                                    
                                    if($payment['sign_of_calculation'] == 'expenses')
                                    {
                                        $tbl_day[$payment['date_operations']]['expenses'] = $payment['total_amount'];
                                    }
                                    /*
                                    else
                                    {
                                        $tbl_day[$payment['date_operations']]['expenses'] = 0;
                                    }
                                    */
                                    
                                }
                                
                                $saldo_day = $saldo;
                                
                                foreach($tbl_day as $key=>$tr)
                                {  
                                    $expenses = isset($tr['expenses']) ? $tr['expenses'] : 0;
                                    $income = isset($tr['income']) ? $tr['income'] : 0;
                                    $itogo = $income-$expenses;
                                    $saldo_day = $saldo_day+$itogo;
                                    
                                    ?>
                                    <tr>
                                        <td><?= $key ?></td>
                                        <td><?= $expenses ?></td>
                                        <td><?= $income ?></td>
                                        <td class="table-<?= $itogo >=0 ? 'success' : 'danger' ?>"><?= $itogo ?></td>
                                        <td><?= $saldo_day ?></td>
                                    </tr>
                                   
                                    <?php 
                                   
                                }
                            }
                            ?>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function () {
            
           // DataTable.datetime('MM/YYYY');
           
         //   DataTable.datetime('DD.MM.YYYY');
            
            $('#myTableDay').DataTable({
                "language": {
                    "lengthMenu": "Показать _MENU_ записей на странице",
                    "zeroRecords": "Записи не найдено",
                    "info": "Показана страница _PAGE_ из _PAGES_",
                    "infoEmpty": "Нет записей",
                    "infoFiltered": "(отфильтровано из _MAX_ всего записей)",
                    "loadingRecords": "Загрузка...",
                    "processing":     "",
                    "search":         "Поиск:",
                    "paginate": {
                        "first":      "Первый",
                        "last":       "Последний",
                        "next":       "Следующий",
                        "previous":   "Предыдущий"
                    },
                    "aria": {
                        "sortAscending":  ": Сортировать по возврастанию",
                        "sortDescending": ": Сортировать по убыванию"
                    }
                },
                "order": [[0, 'desc']],
                "columnDefs": [
                    {
                        targets: 0,
                    //    render: DataTable.render.date('d.m.Y')
                        render: DataTable.render.date()
                    }
                ]
            });
            
             
            $('#myTable').DataTable({
                "language": {
                    "lengthMenu": "Показать _MENU_ записей на странице",
                    "zeroRecords": "Записи не найдено",
                    "info": "Показана страница _PAGE_ из _PAGES_",
                    "infoEmpty": "Нет записей",
                    "infoFiltered": "(отфильтровано из _MAX_ всего записей)",
                    "loadingRecords": "Загрузка...",
                    "processing":     "",
                    "search":         "Поиск:",
                    "paginate": {
                        "first":      "Первый",
                        "last":       "Последний",
                        "next":       "Следующий",
                        "previous":   "Предыдущий"
                    },
                    "aria": {
                        "sortAscending":  ": Сортировать по возврастанию",
                        "sortDescending": ": Сортировать по убыванию"
                    }
                }/*,,
                "order": [[0, 'desc']] 
              
                "columnDefs": [
                    {
                        targets: 0,
                    //    render: DataTable.render.date('m.Y')
                        render: DataTable.render.date()
                    }
                ]
                */
            });
        });
    
        $(document).on('submit', '#saveForm', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("save_info", true);

            $.ajax({
                type: "POST",
                url: "code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    
                    var res = jQuery.parseJSON(response);
                    if(res.status == 422) {
                        $('#errorMessage').removeClass('d-none');
                        $('#errorMessage').text(res.message);

                    }else if(res.status == 200){

                        $('#errorMessage').addClass('d-none');
                        $('#infoAddModal').modal('hide');
                        $('#saveForm')[0].reset();

                        alertify.set('notifier','position', 'top-right');
                        alertify.success(res.message);

                        $('#myTable').load(location.href + " #myTable");
                        
                        location.reload();
                        
                    }else if(res.status == 500) {
                        alert(res.message);
                    }
                }
            });

        });

        $(document).on('click', '.editInfoBtn', function () {

            var info_id = $(this).val();
          
            $.ajax({
                type: "GET",
                url: "code.php?info_id=" + info_id,
                success: function (response) {

                    var res = jQuery.parseJSON(response);
                    if(res.status == 404) {

                        alert(res.message);
                    }else if(res.status == 200){

                        $('#info_id').val(res.data.id);

                        $('#number_pass').val(res.data.number_pass);
                        $('#tenant_id').val(res.data.tenant_id);
                        $('#date_of_issue').val(res.data.date_of_issue);
                        $('#fio_recipient').val(res.data.fio_recipient);
                        $('#car_brand').val(res.data.car_brand);
                        $('#type_of_car').val(res.data.type_of_car);
                        $('#type_of_pass').val(res.data.type_of_pass);
                        $('#state_number').val(res.data.state_number);
                      
                        $('#infoEditModal').modal('show');
                        
                        location.reload();
                    }

                }
            });

        });

        $(document).on('submit', '#updateInfo', function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("update_info", true);

            $.ajax({
                type: "POST",
                url: "code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    
                    var res = jQuery.parseJSON(response);
                    if(res.status == 422) {
                        $('#errorMessageUpdate').removeClass('d-none');
                        $('#errorMessageUpdate').text(res.message);

                    }else if(res.status == 200){

                        $('#errorMessageUpdate').addClass('d-none');

                        alertify.set('notifier','position', 'top-right');
                        alertify.success(res.message);
                        
                        $('#infoEditModal').modal('hide');
                        $('#updateInfo')[0].reset();

                        $('#myTable').load(location.href + " #myTable");
                        
                        location.reload();

                    }else if(res.status == 500) {
                        alert(res.message);
                    }
                }
            });

        });

        $(document).on('click', '.deleteInfoBtn', function (e) {
            e.preventDefault();

            if(confirm('Вы действительно желаете удалить эти данные?'))
            {
                var info_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "code.php",
                    data: {
                        'delete_info': true,
                        'info_id': info_id
                    },
                    success: function (response) {

                        var res = jQuery.parseJSON(response);
                        if(res.status == 500) {

                            alert(res.message);
                        }else{
                            alertify.set('notifier','position', 'top-right');
                            alertify.success(res.message);

                            $('#myTable').load(location.href + " #myTable");
                            
                            location.reload();
                        }
                    }
                });
            }
        });

    </script>
</body>
</html>