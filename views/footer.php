<!-- /div  -->
</div>

<!-- bootstrap include js -->
<script src="../public/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- font awesome include js -->
<script src="../public/fontawesome-free-6.7.2-web/js/all.js"></script>
<script src="../public/bootstrap/js/dashboard.js"></script>

<!-- script for the page  -->
<?php switch ($page) {
    case 'client':
        echo "<script src='../public/bootstrap/js/client.dashboard.js'></script>";
        break;
    case 'depot':
        echo "<script src='../public/bootstrap/js/depot.dashboard.js'></script>";
        break;
    case 'virement':
        echo "<script src='../public/bootstrap/js/virement.dashboard.js'></script>";
        break;
    case 'pret':
        echo "<script src='../public/bootstrap/js/pret.dashboard.js'></script>";
        break;
    case 'rendu':
        echo "<script src='../public/bootstrap/js/rendu.dashboard.js'></script>";
        break;
    default:
        header("Location: ../../../config/router.php?route=not_found");
        break;
} ?>

</body>

</html>