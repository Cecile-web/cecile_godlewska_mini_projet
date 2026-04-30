<?php
session_start();
// sI L'UTILISATEUR N'EST PAS CONNECTÉ, REDIRIGER VERS LA PAGE DE CONNEXION
if (empty($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}
// CONNEXION À LA BASE DE DONNÉES ET RÉCUPÉRATION DES MEMBRES DE L'ÉQUIPE
$mysqli = new mysqli('127.0.0.1', 'root', 'root', 'company_cecile');
$teamMembers = [];
$error = '';
// GESTION DES ERREURS DE CONNEXION ET DE LECTURE DES DONNÉES
if ($mysqli->connect_error) {
    $error = 'Impossible de se connecter à la base de données.';
    //REQUETE POUR RÉCUPÉRER LES MEMBRES DE L'ÉQUIPE
} else {
    $result = $mysqli->query('SELECT id, prenom, nom, date_naissance, lieu_naissance, poste FROM team ORDER BY id');
// RECUPÉRATION DES DONNÉES ET GESTION DES ERREURS DE LECTURE
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $teamMembers[] = $row;
        }
        $result->free();
    } else {
        $error = 'Erreur lors de la lecture des données de l\'équipe.';
    }
// FIN DE LA CONNEXION À LA BASE DE DONNÉES
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
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
            background: radial-gradient(circle at top left, rgba(56, 189, 248, 0.15), transparent 25%),
                        radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.12), transparent 22%),
                        linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%);
            color: var(--text);
        }

        .page-wrapper {
            width: 100%;
            max-width: 1160px;
            margin: 0 auto;
            padding: 32px;
        }

        .topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 28px;
            padding: 24px 28px;
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: var(--shadow);
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .site-brand {
            display: grid;
            gap: 6px;
        }

        .site-brand .eyebrow {
            margin: 0;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #2563eb;
        }

        .site-brand h1 {
            margin: 0;
            font-size: 2.4rem;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: #2563eb;
        }

        .site-brand p {
            margin: 0;
            color: var(--muted);
            font-size: 1rem;
            max-width: 520px;
            line-height: 1.7;
        }

        .menu {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .menu a {
            padding: 10px 16px;
            border-radius: 14px;
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .menu a:hover {
            background: rgba(37, 99, 235, 0.08);
        }

        .menu a.active {
            background: var(--primary);
            color: #ffffff;
        }

        .card {
            background: var(--card);
            border: 1px solid rgba(37, 99, 235, 0.12);
            border-radius: 28px;
            box-shadow: 0 26px 65px rgba(15, 23, 42, 0.08);
            padding: 32px;
        }

        .table-wrapper {
            overflow-x: auto;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 680px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }

        th, td {
            padding: 16px 18px;
            text-align: left;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        }

        th {
            background: #eef4ff;
            color: #1e3a8a;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 0.95rem;
        }

        tr:hover {
            background: rgba(37, 99, 235, 0.06);
        }

        .empty-state,
        .error {
            margin: 16px 0 0;
            padding: 16px;
            border-radius: 16px;
            background: #f8fafc;
            color: #475569;
        }

        .table-footer {
            display: flex;
            justify-content: flex-start;
            padding: 16px 0 0;
            color: #475569;
            font-weight: 600;
        }

        .table-footer span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 14px;
            background: #eef2ff;
            border: 1px solid rgba(37, 99, 235, 0.18);
        }
# LOGOUT BUTTON
        .logout {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 14px;
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
        }

        .logout:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <header class="topbar">
            <div class="site-brand">
                <p class="eyebrow">Portail RH</p>
                <h1>Tableau de Bord RH</h1>
                <p>Bienvenue sur le Tableau de Bord RH de la Cécile Company. Gère les effectifs et suivez l'état de l'équipe depuis un seul endroit.</p>
            </div>
            <nav class="menu">
                <a href="#" class="active">Tableau de bord</a>
                <a href="ajouter_membre.php">Ajouter un membre</a>
                <a href="logout.php" class="logout">Déconnexion</a>
            </nav>
        </header>

        <div class="card">
            <?php if ($error !== ''): ?>
                <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Date de naissance</th>
                            <th>Lieu de naissance</th>
                            <th>Poste</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($teamMembers)): ?>
                            <tr>
                                <td colspan="6" class="empty-state">Aucun membre de l'équipe trouvé.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($teamMembers as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($member['prenom'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($member['nom'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($member['date_naissance'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($member['lieu_naissance'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($member['poste'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <span>Total effectifs : <?php echo count($teamMembers); ?></span>
            </div>
        </div>
    </div>
</body>
</html>
