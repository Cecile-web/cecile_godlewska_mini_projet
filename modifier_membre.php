<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$error = '';
$success = '';

$mysqli = new mysqli('127.0.0.1', 'root', 'root', 'company_cecile');
if ($mysqli->connect_error) {
    $error = 'Impossible de se connecter à la base de données.';
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    $error = 'Identifiant de membre invalide.';
}

$prenom = $nom = $date_naissance = $lieu_naissance = $poste = '';

if ($error === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    $poste = trim($_POST['poste'] ?? '');

    if ($prenom === '' || $nom === '' || $date_naissance === '' || $lieu_naissance === '' || $poste === '') {
        $error = 'Veuillez remplir tous les champs du formulaire.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_naissance)) {
        $error = 'La date de naissance doit être au format YYYY-MM-DD.';
    } else {
        $stmt = $mysqli->prepare('UPDATE team SET prenom = ?, nom = ?, date_naissance = ?, lieu_naissance = ?, poste = ? WHERE id = ?');
        if ($stmt) {
            $stmt->bind_param('sssssi', $prenom, $nom, $date_naissance, $lieu_naissance, $poste, $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows >= 0) {
                    $success = 'Le membre a bien été mis à jour.';
                } else {
                    $error = 'Aucune modification enregistrée.';
                }
            } else {
                $error = 'Erreur lors de la mise à jour, veuillez réessayer.';
            }
            $stmt->close();
        } else {
            $error = 'Erreur interne. Impossible de préparer la requête.';
        }
    }
}

if ($error === '' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $mysqli->prepare('SELECT prenom, nom, date_naissance, lieu_naissance, poste FROM team WHERE id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($member = $result->fetch_assoc()) {
            $prenom = $member['prenom'];
            $nom = $member['nom'];
            $date_naissance = $member['date_naissance'];
            $lieu_naissance = $member['lieu_naissance'];
            $poste = $member['poste'];
        } else {
            $error = 'Membre introuvable.';
        }
        $stmt->close();
    } else {
        $error = 'Erreur interne. Impossible de préparer la requête.';
    }
}

if (!$mysqli->connect_error) {
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un membre - Team</title>
    <style>
        :root {
            --bg: #f3f6ff;
            --card: #ffffff;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --text: #111827;
            --muted: #6b7280;
            --border: rgba(15, 23, 42, 0.12);
            --shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%);
            color: var(--text);
        }

        .page-wrapper {
            width: 100%;
            max-width: 620px;
            margin: 0 auto;
            padding: 32px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            padding: 32px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 1.9rem;
        }

        .topbar a {
            display: inline-flex;
            align-items: center;
            padding: 12px 18px;
            border-radius: 14px;
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
        }

        .topbar a:hover {
            background: var(--primary-dark);
        }

        .form-grid {
            display: grid;
            gap: 18px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        label {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text);
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fafbff;
            font-size: 1rem;
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
        }

        button:hover {
            background: var(--primary-dark);
        }

        .message {
            padding: 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-weight: 600;
        }

        .message.success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #6ee7b7;
        }

        .message.error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="topbar">
            <h1>Modifier un membre</h1>
            <a href="tableau_de_bord.php">Retour au tableau de bord</a>
        </div>

        <div class="card">
            <?php if ($success !== ''): ?>
                <div class="message success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div class="message error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($error === '' || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <form action="modifier_membre.php?id=<?php echo intval($id); ?>" method="post" class="form-grid">
                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" id="prenom" name="prenom" required value="<?php echo htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" required value="<?php echo htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="date_naissance">Date de naissance</label>
                        <input type="date" id="date_naissance" name="date_naissance" required value="<?php echo htmlspecialchars($date_naissance, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="lieu_naissance">Lieu de naissance</label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" required value="<?php echo htmlspecialchars($lieu_naissance, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="poste">Poste</label>
                        <input type="text" id="poste" name="poste" required value="<?php echo htmlspecialchars($poste, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <button type="submit">Enregistrer les modifications</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
