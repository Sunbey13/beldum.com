<nav class="navbar navbar-expand-lg navbar-dark bg-lochinvar mb-3">
    <div class="container">
        <a class="navbar-brand" href="main.php">
            <img src="../img/logo.svg" height="31" width="141" alt="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="main.php" aria-current="page">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="drivers.php">Водители</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cars.php">Авто</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="organizations.php">Организации</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="history.php">История</a>
                </li>
            </ul>
            <form class="d-flex">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if ($_SESSION['role'] == 1) {
                        echo '
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Профиль
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="settings.php">Настройки</a></li>
                                    <li><a class="dropdown-item" href="../logout.php">Выйти</a></li>
                                </ul>
                            </li>
                        ';
                    } else {
                        echo '
                            <li class="nav-item">
                                <a class="nav-link" href="../logout.php">Выйти</a>
                            </li>
                        ';
                    } ?>
                </ul>
            </form>
        </div>
    </div>
</nav>