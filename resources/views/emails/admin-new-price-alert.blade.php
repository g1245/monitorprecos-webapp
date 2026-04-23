<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo alerta de preço criado</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background-color: #16a34a; padding: 24px 32px; color: #ffffff; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 700; }
        .body { padding: 32px; color: #374151; }
        .body p { margin: 0 0 16px; line-height: 1.6; }
        .info-table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        .info-table th, .info-table td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .info-table th { background-color: #f9fafb; color: #6b7280; font-weight: 600; width: 35%; }
        .footer { background-color: #f9fafb; padding: 16px 32px; font-size: 12px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Novo alerta de preço criado</h1>
        </div>
        <div class="body">
            <p>Um usuário configurou um alerta de preço para um produto.</p>

            <table class="info-table">
                <tbody>
                    <tr>
                        <th>Produto</th>
                        <td>{{ $wish->product->name ?? 'Produto #' . $wish->product_id }}</td>
                    </tr>
                    <tr>
                        <th>Preço alvo</th>
                        <td>R$ {{ number_format($wish->target_price, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Usuário</th>
                        <td>{{ $wish->user->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>E-mail do usuário</th>
                        <td>{{ $wish->user->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Data do alerta</th>
                        <td>{{ $wish->created_at->format('d/m/Y \à\s H:i') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="footer">
            Esta mensagem foi gerada automaticamente por {{ config('app.name') }}.
        </div>
    </div>
</body>
</html>
