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
                <h1>ELDIR | Маслосклад | КАНИСТРЫ</h1>
            </div>
        </div>
        <?  require_once('nav_sklad.php'); 
        ?>
            
            <div class="col-md-12 mt-2">
                <h2>Остатки</h2>
                <p>Зелёным выделены позиции в наличии 2шт+, жёлтым остальные.</p>
                <p>Посмотреть приём / выдачу в разделе <b><a href="canisters.php">ELDIR | Маслосклад | КАНИСТРЫ ".</a></b></p>
            </div>
            <div class="col-md-12">
                <div class="table-responsive-sm">
                    <table id="myTableDay" class=" table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Категория</th>
                                <th>Наименование</th>
                                <th>Остаток</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            require __DIR__ . '/../dbcon.php';

                            $query = "SELECT 
                                mpt.name as type_name, 
                                mp.name as product_name, 
                                mp.volume,
								mp.id as product_id,
                                SUM(
                                    CASE 
                                        WHEN m.sign_of_calculation = 'priemka' THEN  m.count
                                        WHEN m.sign_of_calculation = 'vydacha' THEN -m.count
                                        ELSE 0
                                    END
                                ) as total_volume_by_product
                            FROM maslosklad m 
                            LEFT JOIN maslosklad_property mp ON m.property_id = mp.id 
                            LEFT JOIN maslosklad_property_type mpt ON mp.type = mpt.id 
                            WHERE m.is_deleted = 0 
                                AND mp.volume = 0 
                                AND mpt.id IN (1,2,3,4,6,11)
                            GROUP BY mp.id, mpt.name, mp.name, mp.volume
                            ORDER BY mpt.name, mp.name;";
                            
                            $query_run = mysqli_query($con, $query);

                            if(mysqli_num_rows($query_run) > 0)
                            {
                               
                                
                                foreach($query_run as $data)
                                {
                                        ?>
                                        <tr class="<?= $data['total_volume_by_product'] > 1 ? 'table-success' : 'table-warning' ?>">
                                            <td><?= $data['type_name'] ?></td>
                                            <td><a href="/maslosklad/canisters.php?product_id=<?= $data['product_id'] ?>"><?= $data['product_name'] ?></a></td>
                                            <td><?= $data['total_volume_by_product'] ?></td>
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
				"pageLength": 50,
                "order": [[0, 'desc']]
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