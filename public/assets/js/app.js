$(document).ready(function() {
    /* Авторизация */
    $(document).on('submit', '#loginForm', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append("sendLoginForm", true);

        $.ajax({
            type: "POST",
            url: "/login",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var res = jQuery.parseJSON(response);
                if(res.status == 422) {
                    $('#errorMessageLogin').removeClass('d-none');
                    $('#errorMessageLogin').text(res.message);
                } else if(res.status == 200) {
                    $('#errorMessageLogin').addClass('d-none');
                    $('#loginModal').modal('hide');
                    $('#loginForm')[0].reset();

                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);
                    
                    setTimeout(function(){
                        window.location.reload();
                    }, 2000);
                } else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });
    });

    /* Регистрация */
    $(document).on('submit', '#registerForm', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append("sendRegisterForm", true);

        $.ajax({
            type: "POST",
            url: "/register",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var res = jQuery.parseJSON(response);
                if(res.status == 422) {
                    $('#errorMessageRegister').removeClass('d-none');
                    $('#errorMessageRegister').text(res.message);
                } else if(res.status == 200) {
                    $('#errorMessageRegister').addClass('d-none');
                    $('#registerModal').modal('hide');
                    $('#registerForm')[0].reset();

                    alertify.set('notifier','position', 'top-right');
                    alertify.success(res.message);
                } else if(res.status == 500) {
                    alert(res.message);
                }
            }
        });
    });

    // --- Динамическая подгрузка имущества по категории в форме закупки ---
    $(document).on('change', '#category_id', function() {
        var categoryId = $(this).val();
        var $itemSelect = $('#item_id');
        $itemSelect.html('<option value="">Загрузка...</option>');
        if (!categoryId) {
            $itemSelect.html('<option value="">Сначала выберите категорию</option>');
            return;
        }
        $.ajax({
            url: '/api/items/by-category/' + categoryId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var items = response.items || [];
                var options = '<option value="">Выберите имущество...</option>';
                if (items.length) {
                    $.each(items, function(i, item) {
                        options += '<option value="' + item.id + '">' + item.name + (item.article ? ' (' + item.article + ')' : '') + '</option>';
                    });
                } else {
                    options += '<option value="">Нет имущества в категории</option>';
                }
                $itemSelect.html(options);
            },
            error: function() {
                $itemSelect.html('<option value="">Ошибка загрузки</option>');
            }
        });
    });

    // --- Добавление позиции в таблицу закупки ---
    let purchasePositions = [];
    $(document).on('click', '#addPositionBtn', function() {
        const categoryId = $('#category_id').val();
        const categoryText = $('#category_id option:selected').text();
        const itemId = $('#item_id').val();
        const itemText = $('#item_id option:selected').text();
        const quantity = $('#quantity').val();

        if (!categoryId || !itemId || !quantity || quantity <= 0) {
            alert('Заполните все поля корректно!');
            return;
        }

        // Проверка на дубли
        const exists = purchasePositions.some(pos => pos.categoryId === categoryId && pos.itemId === itemId);
        if (exists) {
            alert('Такая позиция уже добавлена!');
            return;
        }

        // Добавляем в массив
        purchasePositions.push({ categoryId, categoryText, itemId, itemText, quantity });

        // Добавляем строку в таблицу
        const rowHtml = `<tr data-category-id="${categoryId}" data-item-id="${itemId}">
            <td>${categoryText}</td>
            <td>${itemText}</td>
            <td>${quantity}</td>
            <td><button type="button" class="btn btn-sm btn-danger remove-position-btn">Удалить</button></td>
        </tr>`;
        $('#positionsTable tbody').append(rowHtml);

        // Очищаем поля
        $('#item_id').val('');
        $('#quantity').val(1);
    });

    // Удаление позиции из таблицы и массива
    $(document).on('click', '.remove-position-btn', function() {
        const $row = $(this).closest('tr');
        const categoryId = $row.data('category-id');
        const itemId = $row.data('item-id');
        purchasePositions = purchasePositions.filter(pos => !(pos.categoryId == categoryId && pos.itemId == itemId));
        $row.remove();
    });
}); 