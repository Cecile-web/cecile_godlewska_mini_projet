<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $mysqli = new mysqli('127.0.0.1', 'root', 'root', 'company_cecile');

        if ($mysqli->connect_error) {
            $error = 'Erreur de connexion à la base de données.';
        } else {
            $query = 'SELECT id, email, password FROM utilisateurs WHERE email = ? LIMIT 1';
            $stmt = $mysqli->prepare($query);

            if ($stmt) {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();
               
                if ($user = $result->fetch_assoc()) {
                    $storedPassword = $user['password'];

                    // Comparaison directe pour mot de passe non crypté en base.
                    if ($storedPassword === $password) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        header('Location: tableau_de_bord.php');
                        exit;
                    }
                    
                }

                $error = 'Email ou mot de passe invalide.';
                $stmt->close();
            } else {
                $error = 'Erreur interne. Veuillez réessayer plus tard.';
            }

            $mysqli->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Cécile Company</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --text: #1f2937;
            --muted: #6b7280;
            --border: rgba(15, 23, 42, 0.08);
            --shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(180deg, #eef4ff 0%, #f8fafc 100%);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 32px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            padding: 32px;
        }

        h1 {
            margin: 0 0 16px;
            font-size: 1.75rem;
            line-height: 1.2;
        }

        p.subtitle {
            margin: 0 0 28px;
            color: var(--muted);
            line-height: 1.65;
        }

        form {
            display: grid;
            gap: 18px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        label {
            font-size: 0.95rem;
            color: var(--text);
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 14px;
            font-size: 1rem;
            background: #fafbff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        }

        button {
            width: 100%;
            padding: 14px 16px;
            border: none;
            border-radius: 14px;
            background: var(--primary);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .error-msg {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            font-size: 0.95rem;
        }

        .note {
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--muted);
            text-align: center;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <h1>Bienvenue sur le Portail</h1>
            <p class="subtitle">Connectez-vous pour accéder à votre espace Cécile Company en toute sécurité.</p>
            <?php if ($error !== ''): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form action="connexion.php" method="post">
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Votre adresse email">
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required placeholder="Votre mot de passe">
                </div>

                <button type="submit">Se connecter</button>
            </form>
            <p class="note">Vous n'avez pas encore de compte ? Contactez l'administrateur pour obtenir vos identifiants.</p>
        </div>
    </div>
</body>
</html>