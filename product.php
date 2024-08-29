<?php
// On récupère le nom du produit dans l'URL
$productname = $_GET['productname'];

// On vérifie que productname est bien une chaîne non vide
if (empty($productname)) {
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Productname is missing or empty'));
    die();
}

// URL de l'API pour récupérer les informations du produit
$apiurl = "/api/productinfo?productname=" . urlencode($productname);

// Requête cURL pour récupérer les données du produit
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://arcade-tracker.snipeur060.fr/$apiurl");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);

// Décodage du JSON reçu
$products = json_decode($output, true);

if (isset($products['error'])) {
    die('Erreur : ' . $products['error']);
}

// On récupère le produit avec le prix le plus bas (le minimum des heures)
$bestProduct = null;
if (!empty($products)) {
    $bestProduct = array_reduce($products, function($carry, $item) {
        return ($carry === null || $item['hours'] < $carry['hours']) ? $item : $carry;
    });
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails des Produits</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Mode sombre minimaliste */
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        .table-dark {
            background-color: #333;
            color: #ffffff;
            border-color: #555;
        }

        .table-dark th,
        .table-dark td {
            border-color: #444;
        }

        .btn-refresh,
        .btn-back {
            background-color: #555;
            border-color: #777;
            color: #ffffff;
            margin: 20px 0;
        }

        .btn-back {
            background-color: #007bff; /* Couleur bleue pour le bouton retour */
            border-color: #0056b3;
        }

        .container {
            margin-top: 50px;
        }

        .error-message {
            color: #ff0000;
            background-color: #333;
            padding: 10px;
            border: 1px solid #ff0000;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none; /* Caché par défaut */
        }

        .card {
            background-color: #1f1f1f; /* Fond sombre pour la carte */
            color: #ffffff;
            border: 1px solid #333;
            border-radius: 8px;
        }

        .card img {
            max-width: 150px;
            height: auto;
            display: block; /* Centrer l'image */
            margin: 0 auto;
        }

        .card-body {
            text-align: center;.
        }

        .stock-unlimited {
            color: #00ff00; /* Couleur pour "Illimité" */
        }

        .stock-out {
            color: #ff0000; /* Couleur pour "Rupture de stock" */
        }

        .table img {
            max-width: 80px;
            height: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Bouton de retour -->
    <a href="javascript:history.back()" class="btn btn-back">Retour</a>

    <h1 class="text-center mb-4">Détails des Produits</h1>
    <!-- Message d'erreur -->
    <div id="error-message" class="error-message"></div>

    <!-- Informations sur le produit actuel -->
    <div id="product-card" class="card mb-4">
        <img src="" id="product-image" class="card-img-top" alt="">
        <div class="card-body">
            <h2 id="product-name" class="card-title"></h2>
            <p><strong>Prix actuel : </strong><span id="product-hours"></span> Ticket 🎟️</p>
            <p><strong>Date : </strong><span id="product-date"></span></p>
            <p><strong>Stock : </strong><span id="product-stock"></span></p>
        </div>
    </div>
    <button class="btn btn-primary btn-refresh" id="refresh-btn">Actualiser</button>

    <!-- Tableau des changements -->
    <h2 class="text-center mb-4">Historique des Changements</h2>
    <table class="table table-dark table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom du Produit</th>
            <th>Ticket 🎟️</th>
            <th>Date</th>
            <th>Stock</th>
            <th>Image</th>
        </tr>
        </thead>
        <tbody id="product-list">
        <!-- Les données seront chargées ici via AJAX -->
        </tbody>
    </table>
</div>

<!-- AJAX pour charger les informations au chargement et pour actualiser -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        function loadProductDetails() {
            $.get('/api/productinfo?productname=<?php echo urlencode($productname); ?>', function(data){
                if (data.error) {
                    $('#error-message').text(data.error).show();
                } else {
                    var product = data[0]; // Assuming the API returns an array with a single product object
                    $('#product-image').attr('src', product.imageURL);
                    $('#product-name').text(product.productname);
                    $('#product-hours').text(product.hours);
                    $('#product-date').text(product.date);
                    $('#product-stock').html(product.stock == 9999 ? '<span class="stock-unlimited">Illimité</span>' :
                        product.stock == 0 ? '<span class="stock-out">Rupture de stock</span>' :
                            product.stock);
                    $('#error-message').hide();
                }
            }).fail(function() {
                $('#error-message').text('Erreur lors de la récupération des données.').show();
            });
        }

        function loadProductChanges() {
            $.get('/api/productchange.php?productname=<?php echo urlencode($bestProduct['productname']); ?>', function(data){
                if (data.length > 0) {
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        var stockClass = data[i].stock == 0 ? 'stock-out' : '';
                        var stockText = data[i].stock == 9999 ? 'Illimité' : data[i].stock;
                        html += '<tr>';
                        html += '<td>' + data[i].id + '</td>';
                        html += '<td>' + data[i].productname + '</td>';
                        html += '<td>' + data[i].hours + '</td>';
                        html += '<td>' + data[i].date + '</td>';
                        html += '<td class="' + stockClass + '">' + stockText + '</td>';
                        html += '<td><img src="' + data[i].imageURL + '" alt="Image du produit"></td>';
                        html += '</tr>';
                    }
                    $('#product-list').html(html);
                    $('#error-message').hide(); // Hide error message if data is successfully updated
                } else {
                    $('#error-message').text('Aucune mise à jour trouvée.').show();
                }
            }).fail(function() {
                $('#error-message').text('Erreur lors de la récupération des données.').show();
            });
        }

        // Charger les détails du produit et les changements dès le chargement de la page
        loadProductDetails();
        loadProductChanges();

        // Actualiser les informations du produit et l'historique
        $('#refresh-btn').on('click', function(){
            loadProductDetails();
            loadProductChanges();
        });
    });
</script>

<!-- Bootstrap JS and dependencies -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
