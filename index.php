<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Produits</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Mode sombre par défaut */
        body {
            background-color: #121212;
            color: #ffffff;
        }

        .navbar,
        .btn-primary,
        .btn-info,
        .table {
            background-color: #333;
            color: #ffffff;
        }

        .table thead {
            background-color: #444;
        }

        .btn-primary,
        .btn-info {
            border-color: #555;
        }

        .alert {
            background-color: #333;
            color: #ffffff;
            border-color: #555;
        }

        .input-group-text {
            background-color: #444;
            color: #ffffff;
            border-color: #555;
        }

        .form-control {
            background-color: #222;
            color: #ffffff;
            border-color: #555;
        }
        .form-control:focus{
            background-color: #333;
            color: #ffffff;
            border-color: #555;
        }

        .table img {
            max-width: 100px; /* Ajuster la taille de l'image */
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Arcade Shop Tracker</h1>
    <div class="input-group mb-3">
        <input type="text" id="search" name="search" class="form-control" placeholder="Rechercher un produit..." autocomplete="off">
        <div class="input-group-append">
            <button class="btn btn-primary" type="button" id="search-btn">Rechercher</button>
        </div>
    </div>
    <div id="lastproduct">
        <a href="/lastprod" class="btn btn-info">Dernier Produit</a>
        <a href="/doc" class="btn btn-info">Documentation API</a>
    </div><br>
    <div id="result">
        <!-- Les résultats de la recherche seront affichés ici -->
    </div>
</div>

<script>
    $(document).ready(function(){
        function searchProducts() {
            var search = $('#search').val();
            $.get('/api/search.php?search=' + search, function(data){
                if(data.error){
                    $('#result').html('<div class="alert alert-danger">' + data.error + '</div>');
                } else {
                    var html = '<table class="table table-bordered">';
                    html += '<thead class="thead-dark"><tr><th>Nom du Produit</th><th>Action</th><th>Image</th></tr></thead>';
                    html += '<tbody>';
                    for(var i = 0; i < data.length; i++){
                        html += '<tr>';
                        html += '<td>' + data[i].productname + '</td>';
                        html += '<td><a href="/product?productname=' + encodeURIComponent(data[i].productname) + '" class="btn btn-info">Voir le Produit</a></td>';
                        html += '<td><img src="' + data[i].imageURL + '" alt="' + data[i].productname + '"></td>';
                        html += '</tr>';
                    }
                    html += '</tbody></table>';
                    $('#result').html(html);
                }
            });
        }

        $('#search').on('input', function(){
            searchProducts();
        });

        $('#search-btn').on('click', function(){
            searchProducts();
        });
    });
</script>

<!-- Bootstrap JS and dependencies -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
