<!DOCTYPE html>
<html>

<head>
    <title>Mise à jour de votre commande</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Bonjour {{ $commande->utilisateur->prenom }},</h2>
        </div>

        <p>Le statut de votre commande <strong>#{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}</strong> a été mis à jour.</p>

        <div style="text-align: center; margin: 30px 0;">
            <span class="status-badge">
                Nouveau statut : {{ $commande->statut }}
            </span>
        </div>

        <p>Vous pouvez consulter les détails de votre commande en cliquant sur le bouton ci-dessous :</p>

        <div style="text-align: center;">
            {{-- Adjust URL to match your frontend URL --}}
            <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/mes-commandes/{{ $commande->id }}"
                style="background-color: #4f46e5; 
                      color: #ffffff; 
                      padding: 14px 28px; 
                      text-decoration: none; 
                      border-radius: 8px; 
                      font-weight: bold; 
                      font-size: 16px; 
                      font-family: Helvetica, Arial, sans-serif; 
                      display: inline-block; 
                      box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);">
                Voir ma commande &rarr;
            </a>
        </div>

        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            Merci de votre confiance,<br>
            L'équipe Ecomsite
        </p>
    </div>
</body>

</html>