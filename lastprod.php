<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Derniers Changements de Produits</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            background-color: #1f1f1f;
            color: #ffffff;
            border: 1px solid #333;
            border-radius: 8px;
            margin-bottom: 20px;
            height: 100%;
        }

        .card img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.5em;
            margin-bottom: 15px;
            text-align: center;
        }

        .product-price {
            font-size: 1.2em;
            font-weight: bold;
            color: #00ff00;
            text-align: center;
        }

        .product-date {
            font-size: 0.9em;
            color: #999;
            margin-top: 10px;
            text-align: center;
        }

        .btn-refresh {
            background-color: #007bff;
            border-color: #0056b3;
            color: #ffffff;
            margin-bottom: 20px;
        }

        .btn-view-product {
            background-color: #28a745;
            border-color: #218838;
            color: #ffffff;
            margin-top: 15px;
            text-align: center;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .card-columns {
            column-count: 1;
        }

        @media (min-width: 576px) {
            .card-columns {
                column-count: 2;
            }
        }

        @media (min-width: 768px) {
            .card-columns {
                column-count: 3;
            }
        }

        @media (min-width: 992px) {
            .card-columns {
                column-count: 4;
            }
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
        ::-webkit-scrollbar {
            width: 20px;
        }

        ::-webkit-scrollbar-track {
            background-color: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #a8bbbf;
            border-radius: 20px;
            border: 6px solid transparent;
            background-clip: content-box;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(144, 164, 169, 0.51);
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Bouton pour actualiser les produits -->
    <button class="btn btn-primary btn-refresh" id="refresh-btn">Actualiser</button>

    <h1 class="text-center mb-4">Derniers Changements de Produits</h1>
    <div>
        <a href="/" class="btn btn-info">Retour</a>
    </div><br>
    <!-- Conteneur des cartes -->
    <div id="product-cards" class="row">
        <!-- Les cartes seront insérées ici via AJAX -->
    </div>
</div>

<!-- AJAX pour charger les changements de produits -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        function loadProductChanges() {
            $.get('/api/lastchange', function(data){
                var html = '';
                for (var i = 0; i < data.length; i++) {
                    html += '<div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">';
                    html += '<div class="card">';
                    html += '<img src="' + data[i].imageURL + '" alt="' + data[i].productname + '">';
                    html += '<div class="card-body">';
                    html += '<h2 class="card-title">' + data[i].productname + '</h2>';
                    html += '<p class="product-price">' + data[i].hours + ' Tickets 🎟️</p>';
                    html += '<p class="product-date">' + data[i].date + '</p>';
                    html += '<a href="product?productname=' + encodeURIComponent(data[i].productname) + '" class="btn btn-view-product">Voir le produit</a>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                }
                $('#product-cards').html(html);
            }).fail(function() {
                $('#product-cards').html('<p class="text-center">Erreur lors de la récupération des données.</p>');
            });
        }

        // Charger les changements de produits au chargement de la page
        loadProductChanges();

        // Actualiser les informations de produits
        $('#refresh-btn').on('click', function(){
            loadProductChanges();
        });
    });
</script>

<!-- Bootstrap JS and dependencies -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
