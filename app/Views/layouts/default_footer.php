        </div> <!-- Закрываем .container из header -->
    </div> <!-- Закрываем .content из header -->
    
    <footer class="bg-dark text-white py-4 mt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> ELDIR. Все права защищены.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">Версия 1.0.0</p>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
    $(document).ready(function() {
        // Подсветка активного пункта меню
        var currentPath = window.location.pathname;
        
        $('.navbar-nav .nav-link').each(function() {
            var linkPath = $(this).attr('href');
            if (linkPath && currentPath.indexOf(linkPath) !== -1 && linkPath !== '/') {
                $(this).addClass('active');
                
                // Если это выпадающее меню, также активируем родительский пункт
                if ($(this).closest('.dropdown-menu').length) {
                    $(this).closest('.dropdown').find('.dropdown-toggle').addClass('active');
                }
            }
        });
        
        // Инициализация всплывающих подсказок
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
    </script>
</body>
</html> 