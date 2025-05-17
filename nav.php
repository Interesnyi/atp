<?
if(isset($_SESSION['id']))
{
    echo '<nav class="navbar navbar-expand-lg" style="background-color: #e3f2fd;">
        <div class="container">
            <a class="navbar-brand" href="#">ELDIR всему голова</a>
            <button class="navbar-toggler btn-outline-success me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/">Главная</a>
                    </li>
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Склад
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/maslosklad/">Главная</a></li>
                        <li><hr clasdivider"></li>
                        <li><a class="dropdown-item" href="/maslosklad/priemka/">Приёмка Имущества</a></li>
						<li><a class="dropdown-item" href="/maslosklad/vydacha/">Выдача Имущества</a></li>
                      </ul>
                    </li>
					<li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Остатки
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/maslosklad/car_parts_remains.php">Запчасти</a></li>
						<li><a class="dropdown-item" href="/maslosklad/barrel_remains.php">Розлив</a></li>
						<li><a class="dropdown-item" href="/maslosklad/canisters_remains.php">Канистры</a></li>
                      </ul>
                    </li>
                    ';
 
            echo  '
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Касса
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/balance/">Главная</a></li>
                        <li><hr clasdivider"></li>
                        <li><a class="dropdown-item" href="/balance/income/">Приход</a></li>
                        <li><a class="dropdown-item" href="/balance/expenses/">Расход</a></li>
                      </ul>
                    </li>
                  
                   ';
              

            echo    '<li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        '.$_SESSION['loginemail'].'
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#">Профиль</a></li>
                        <li><a class="dropdown-item" href="#">Настройки</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../exit.php">Выйти</a></li>
                      </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
}
?>