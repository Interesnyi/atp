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

    <title>Маслосклад 2024</title>

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
                <h1>ELDIR | Маслосклад | БОЧКИ</h1>
            </div>
        </div>
        <?  require_once('nav_sklad.php'); 
        ?>
            
            <div class="col-md-12 mt-2">
                <h2>Приём / выдача</h2>
                <p>Зелёным выделены выдача масла, синим приём. В столбце "Остаток" отображается на Дату указанную строке.</p>
                <p>Посмотреть остатки на текущий момент можно в разделе <b><a href="barrel_remains.php">ELDIR | Маслосклад | БОЧКИ -> "РОЗЛИВ(остатки)".</a></b></p>
            </div>
            <div class="col-md-12">
                <div class="table-responsive-sm">
                    <table id="myTableDay" class=" table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Тип операции</th>
                                <th>Позиция</th>
                                <th>Количество</th>
                                <th>Остаток</th>
                                <th>Комментарий</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            require __DIR__ . '/../dbcon.php';
							
							// Сначала установим переменные
							mysqli_query($con, "SET @running_total = 0");
							mysqli_query($con, "SET @current_product = NULL");
							
                            $query = "SELECT 
								date,
								type_operation,
								type_name,
								product_name,
								operation_volume,
								@running_total := IF(
									@current_product = product_id,
									@running_total + operation_volume,
									operation_volume
								) AS current_total_volume,
								@current_product := product_id,
								comment
							FROM (
								SELECT 
									m.date_operations as date,
									CASE
										WHEN m.sign_of_calculation = 'priemka' THEN 'Приём'
										WHEN m.sign_of_calculation = 'vydacha' THEN 'Выдача'
									END as type_operation,
									mpt.name as type_name,
									mp.name as product_name,
									mp.id as product_id,
									CASE 
										WHEN m.sign_of_calculation = 'priemka' THEN m.count
										WHEN m.sign_of_calculation = 'vydacha' THEN -m.count
										ELSE 0
									END as operation_volume,
									m.comment
								FROM maslosklad m
								LEFT JOIN maslosklad_property mp ON m.property_id = mp.id
								LEFT JOIN maslosklad_property_type mpt ON mp.type = mpt.id
								WHERE m.is_deleted = 0 
									AND mp.volume = 0
									AND mpt.id IN (1,2,3,6) ".($_REQUEST['product_id'] ?  "AND mp.id = ".$_REQUEST['product_id']."" : '')."
								ORDER BY mp.id, m.date_operations
							) as derived_table;";
                            
                            $query_run = mysqli_query($con, $query);

                            if(mysqli_num_rows($query_run) > 0)
                            {
                               
                                
                                foreach($query_run as $data)
                                {
                                        ?>
                                        <tr class="<?= $data['type_operation'] == 'Приём' ? 'table-primary' : 'table-success' ?>">
                                            <td><?= $data['date'] ?></td>
                                            <td><?= $data['type_operation'] ?></td>
                                            <td><?= $data['product_name'] ?></td>
                                            <td><?= $data['operation_volume'] ?></td>
                                            <td><?= $data['current_total_volume'] ?></td>
                                            <td><?= $data['comment'] ?></td>
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