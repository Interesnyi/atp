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

    <title>ELDIR | Маслосклад | Имущество  </title>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
</head>
<body>
    <? require_once('../../nav.php'); ?>
    
    <!-- Add Info Modal -->
    <div class="modal fade" id="infoAddModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить данные о имуществе</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saveForm">
                <div class="modal-body">

                    <div id="errorMessage" class="alert alert-warning d-none"></div>
                    
                    
                    <div class="mb-3">
                            <label for="maslosklad_property_id">Товар</label>
                            <select name="maslosklad_property_id" class="form-select">
                                <option selected>Выберите товар из списка</option>
                                <?php
                                    require __DIR__ . '/../../dbcon.php';
    
                                    $query = "SELECT sp.id as id, sp.name as property_name, spt.name as type_name FROM `maslosklad_property` sp 
                                        LEFT JOIN `maslosklad_property_type` spt ON sp.type = spt.id 
                                        WHERE sp.is_deleted = 0";
                                    $query_run = mysqli_query($con, $query);
    
                                    if(mysqli_num_rows($query_run) > 0)
                                    {
                                        foreach($query_run as $object)
                                        {
                                ?>
                                            <option value="<?=$object['id']?>"><?=$object['type_name'].' '.$object['property_name']?></option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="">Цена закупки</label>
                            <input type="text" name="purchase_price" class="form-control" />
                        </div>
                        
                        <div class="mb-3">
                            <label for="">Цена за нал</label>
                            <input type="text" name="cash_price" class="form-control" />
                        </div>
                        
                        <div class="mb-3">
                            <label for="">Цена за безнал</label>
                            <input type="text" name="cashless_price" class="form-control" />
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
                            <label for="maslosklad_property_id">Товар</label>
                            <select name="maslosklad_property_id" id="maslosklad_property_id" class="form-select">
                                <option selected>Выберите товар из списка</option>
                                <?php
                                    require __DIR__ . '/../../dbcon.php';
    
                                    $query = "SELECT sp.id as id, sp.name as property_name, spt.name as type_name FROM `maslosklad_property` sp 
                                        LEFT JOIN `maslosklad_property_type` spt ON sp.type = spt.id 
                                        WHERE sp.is_deleted = 0";
                                    $query_run = mysqli_query($con, $query);
    
                                    if(mysqli_num_rows($query_run) > 0)
                                    {
                                        foreach($query_run as $object)
                                        {
                                ?>
                                            <option value="<?=$object['id']?>"><?=$object['type_name'].' '.$object['property_name']?></option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="">Цена закупки</label>
                            <input type="text" name="purchase_price" id="purchase_price" class="form-control" />
                        </div>
                        
                        <div class="mb-3">
                            <label for="">Цена за нал</label>
                            <input type="text" name="cash_price" id="cash_price" class="form-control" />
                        </div>
                        
                        <div class="mb-3">
                            <label for="">Цена за безнал</label>
                            <input type="text" name="cashless_price" id="cashless_price" class="form-control" />
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
                <h1>ELDIR | Маслосклад | Прайсы / Цены </h1>
            </div>
        </div>
        <? require_once('../nav_sklad.php'); ?>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#infoAddModal">
                    Добавить цену
                </button>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="table-responsive-sm">
                    <table id="myTable" class=" table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Категория имущества</th>
                                <th>Товар</th>
                                <th>Закуп</th>
                                <th>Нал</th>
                                <th>Безнал</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            require __DIR__ . '/../../dbcon.php';

                            $query = "SELECT sp.id as id, sp.name as property_name, spt.name as type_name,  
                                        mp.purchase_price, mp.cash_price, mp.cashless_price
                                        FROM `maslosklad_property` sp 
                                        LEFT JOIN `maslosklad_property_type` spt ON sp.type = spt.id 
                                        LEFT JOIN `maslosklad_price` mp ON sp.id = mp.maslosklad_property_id 
                                        WHERE sp.is_deleted = 0";
                            $query_run = mysqli_query($con, $query);

                            if(mysqli_num_rows($query_run) > 0)
                            {
                                foreach($query_run as $property)
                                {   
                                    ?>
                                    <tr>
                                        <td><?= $property['type_name'] ?></td>
                                        <td><?= $property['property_name'] ?></td>
                                        <td><?= $property['purchase_price'] ?></td>
                                        <td><?= $property['cash_price'] ?></td>
                                        <td><?= $property['cashless_price'] ?></td>
                              
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
                url: "code.php?info_id=" + info_id + "&for=property",
                success: function (response) {

                    var res = jQuery.parseJSON(response);
                    if(res.status == 404) {

                        alert(res.message);
                    }else if(res.status == 200){

                        $('#info_id').val(res.data.id);

                        
                        $('#name_property').val(res.data.name);
                        $('#type_property').val(res.data.type)
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
       

    </script>
</body>
</html>