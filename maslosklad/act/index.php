<?
    
session_start();

if ( !isset($_SESSION["id"])) {
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

    <title>ELDIR | Масклосклад | Счета получения Имущества</title>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
</head>
<body>
    <? require_once('../../nav.php'); ?>
    
    <!-- Add Info Modal -->
    <div class="modal fade" id="infoAddModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Приёмка имущества</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saveForm">
                <div class="modal-body">

                    <div id="errorMessage" class="alert alert-warning d-none"></div>
                    
                    <div class="mb-3">
                        <label for="">Дата приёмки</label>
                        <input type="date" name="date_operations" class="form-control" />
                    </div>
                    
                     <div class="mb-3">
                        <label for="type_id">Наименование поставщика</label>
                        <select name="suppliers_id" class="form-select">
                            <option selected>Выберите поставщика из списка</option>
                            <?php
                                require __DIR__ . '/../../dbcon.php';

                                $query = "SELECT ms.id, ms.name 
                                FROM maslosklad_suppliers ms
                                WHERE ms.is_deleted = 0";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $object)
                                    {
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_id">Наименование имущества</label>
                        <select name="property_id" class="form-select">
                            <option selected>Выберите наименование имущества из списка</option>
                            <?php
                                require __DIR__ . '/../../dbcon.php';

                                $query = "SELECT sp.id, spt.name as type, sp.name, sp.article 
                                FROM maslosklad_property sp
                                LEFT JOIN maslosklad_property_type spt ON sp.type = spt.id
                                WHERE sp.is_deleted = 0";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $object)
                                    {
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['article'].' ' ?><?=$object['type'].' - '.$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Количество</label>
                        <input type="text" name="count" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_id">Место хранения имущества</label>
                        <select name="place_id" class="form-select">
                            <option selected>Выберите из списка</option>
                            <?php
                                require __DIR__ . '/../../dbcon.php';

                                $query = "SELECT id, name 
                                FROM maslosklad_property_place";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $object)
                                    {
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Комментарий</label>
                        <input type="text" name="comment" class="form-control" />
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Edit Info Modal -->
    <div class="modal fade" id="infoEditModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Изменить данные о имуществе</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateInfo">
                <div class="modal-body">

                    <div id="errorMessageUpdate" class="alert alert-warning d-none"></div>

                    <input type="hidden" name="info_id" id="info_id" >
                    
                    <div class="mb-3">
                        <label for="">Дата приёмки</label>
                        <input type="date" name="date_operations" id="date_operations" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_id">Наименование поставщика</label>
                        <select name="suppliers_id" id="suppliers_id" class="form-select">
                            <option selected>Выберите поставщика из списка</option>
                            <?php
                                require __DIR__ . '/../../dbcon.php';

                                $query = "SELECT ms.id, ms.name 
                                FROM maslosklad_suppliers ms
                                WHERE ms.is_deleted = 0";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $object)
                                    {
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_id">Наименование имущества</label>
                        <select name="name_property" id="name_property" class="form-select">
                            <option selected>Выберите наименование имущества из списка</option>
                            <?php
                                require __DIR__ . '/../../dbcon.php';

                                $query = "SELECT sp.id, spt.name as type, sp.name , sp.article
                                FROM maslosklad_property sp
                                LEFT JOIN maslosklad_property_type spt ON sp.type = spt.id
                                WHERE sp.is_deleted = 0";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $object)
                                    {
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['article'].' ' ?><?=$object['type'].' - '.$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Количество</label>
                        <input type="text" name="count" id="count" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_id">Место хранения имущества</label>
                        <select name="place_id" id="place_id" class="form-select">
                            <option selected>Выберите из списка</option>
                            <?php
                                require __DIR__ . '/../../dbcon.php';

                                $query = "SELECT id, name 
                                FROM maslosklad_property_place";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $object)
                                    {
                            ?>
                                        <option value="<?=$object['id']?>"><?=$object['name']?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Комментарий</label>
                        <input type="text" name="comment" id="comment" class="form-control" />
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
            </div>
        </div>
    </div>
  
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1>ELDIR | Маслосклад | Счета получения Имущества </h1>
            </div>
        </div>
        <? require_once('../nav_sklad.php'); ?>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#infoAddModal">
                    Добавить счёт получения Имущества
                </button>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="table-responsive-sm">
                    <table id="myTable" class=" table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Поставщик</th>
                                <th>Наименование</th>
                                <th>Сумма</th>
                                <th>Комментарий</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            require __DIR__ . '/../../dbcon.php';

                            $query = "SELECT s.id as id, sp.article, sp.name as property_name, spt.name as type_name, s.count, s.date_operations, spp.name as place, s.comment, ms.name as name_suppliers 
                                        FROM `maslosklad` s 
                                        LEFT JOIN `maslosklad_property` sp ON s.property_id = sp.id 
                                        LEFT JOIN `maslosklad_property_type` spt ON sp.type = spt.id
                                        LEFT JOIN `maslosklad_property_place` spp ON s.place_id = spp.id
                                        LEFT JOIN `maslosklad_suppliers` ms ON s.suppliers_id = ms.id 
                                        WHERE sp.is_deleted = 0 AND s.sign_of_calculation = 'priemka' ";
                            $query_run = mysqli_query($con, $query);

                            if(mysqli_num_rows($query_run) > 0)
                            {
                                foreach($query_run as $property)
                                {   
                                    ?>
                                    <tr>
                                        <td><?= $property['date_operations'] ?></td>
                                        <td><?= $property['name_suppliers'] ?></td>
                                        <td><?= $property['property_name'] ?></td>
                                        <td><?= $property['summa'] ?></td>
                                        <td><?= $property['comment'] ?></td>
                                        <td>
                                            <button type="button" value="<?=$property['id'];?>" class="editInfoBtn btn btn-success btn-sm">Изменить</button>
                                            <button type="button" value="<?=$property['id'];?>" class="deleteInfoBtn btn btn-danger btn-sm">Удалить</button>
                                        </td>
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
   

    <script>
        $(document).ready(function () {
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
                },
                "order": [[0, 'desc']]
            });
      
        
        /* Добавление Имущества */
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
        
        /* Редактирование Имущества */
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
                        
                        console.log(res.data);
                        
                        $('#info_id').val(res.data.id);
                        $('#date_operations').val(res.data.date_operations);
                        $('#name_property').val(res.data.property_id);
                        $('#suppliers_id').val(res.data.suppliers_id);
                        $('#count').val(res.data.count);
                        $('#place_id').val(res.data.place_id);
                        $('#comment').val(res.data.comment);
                        
                        $('#infoEditModal').modal('show');
                        
                    }

                }
            });

        });
        
        /* Обновление информации о Имуществе */
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
        
    });
    </script>
</body>
</html>
        