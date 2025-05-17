<?
echo '
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    
    <script>
    // Функция для определения пути к code.php
    function getCodePath() {
        var path = window.location.pathname;
        if (path === "/" || path === "") {
            return "/code.php";
        }
        return path.replace(/\/+$/, "") + "/code.php";
    }
    </script>';
?>