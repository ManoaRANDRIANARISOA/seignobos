<?php
include 'layout_header.php';
require 'config.php';

// Check if project ID is provided
if (!isset($_GET['id'])) {
    echo "<div class='content'><h2>Erreur</h2><p>Projet non spécifié.</p><a href='projects_list_page.php' class='btn'>Retour à la liste</a></div>";
    include 'layout_footer.php';
    exit;
}

$project_id = intval($_GET['id']);

// Fetch project details
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "<div class='content'><h2>Erreur</h2><p>Projet introuvable.</p><a href='projects_list_page.php' class='btn'>Retour à la liste</a></div>";
    include 'layout_footer.php';
    exit;
}

// Decode form_data to get columns
$form_data = json_decode($project['form_data'], true);
if (!is_array($form_data)) $form_data = [];

// Get all possible columns from form_data
$columns = [];
foreach ($form_data as $field) {
    $col_name = preg_replace('/[^a-zA-Z0-9_]/', '_', $field['name']);
    $columns[] = $col_name;
    if (!empty($field['allowOther'])) {
        $columns[] = $col_name . "_other";
    }
}

// Handle reordering via GET parameter
$current_order = $columns;
if (isset($_GET['columns_order'])) {
    $decoded_order = json_decode($_GET['columns_order'], true);
    if (is_array($decoded_order)) {
        $valid_order = array_intersect($decoded_order, $columns);
        $missing = array_diff($columns, $valid_order);
        $current_order = array_merge($valid_order, $missing);
    }
}

// Handle Sorting
$sort_by = $_GET['sort'] ?? null;
$order = strtoupper($_GET['order'] ?? 'ASC');
if (!in_array($order, ['ASC', 'DESC'])) $order = 'ASC';

// Validate sort_by
if ($sort_by && !in_array($sort_by, $current_order) && $sort_by !== 'id' && $sort_by !== 'submitted_at') {
    $sort_by = null;
}

// Fetch data from project table
$table_name = "project_" . $project_id;
$data = [];
$error_msg = "";

try {
    $checkTable = $pdo->query("SHOW TABLES LIKE '$table_name'")->rowCount() > 0;
    if ($checkTable) {
        $sql = "SELECT * FROM `$table_name`";
        if ($sort_by) {
            $sql .= " ORDER BY `$sort_by` $order";
        } else {
            $sql .= " ORDER BY submitted_at DESC";
        }
        $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error_msg = "La table de données pour ce projet n'existe pas encore.";
    }
} catch (Exception $e) {
    $error_msg = "Erreur lors de la récupération des données : " . $e->getMessage();
}

?>

<style>
/* Custom Styles for this page to look professional */
.project-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.project-title h1 {
    margin: 0;
    font-size: 24px;
    background: linear-gradient(135deg, #f5a623, #f76b1c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.action-bar {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    justify-content: space-between; /* To push export buttons to right if added */
}

.action-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-action {
    background: #fff;
    border: 1px solid #ddd;
    color: #555;
    padding: 8px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 14px;
}

.btn-action:hover {
    background: #f9f9f9;
    border-color: #bbb;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.btn-primary {
    background: linear-gradient(135deg, #f5a623, #f76b1c);
    color: white;
    border: none;
}

.btn-primary:hover {
    filter: brightness(1.1);
    color: white;
}

.table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    overflow: hidden; /* For rounded corners */
}

.data-table-wrapper {
    overflow-x: auto;
    width: 100%;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px; /* Force scroll on small screens */
}

.data-table th {
    background: #f8f9fa;
    color: #444;
    font-weight: 600;
    padding: 15px;
    border-bottom: 2px solid #eee;
    text-align: center; /* Centered headers as requested implicitly by layout */
    vertical-align: top;
    position: sticky;
    top: 0;
    z-index: 10;
    min-width: 180px;
}

.th-content {
    display: flex;
    flex-direction: column;
    align-items: center; /* Center everything */
    gap: 8px;
}

.col-title {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 5px;
}

/* Visibility Toggle below title */
.visibility-toggle {
    font-size: 12px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    margin-bottom: 5px;
}

/* Controls Container */
.col-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #fff;
    padding: 6px 10px;
    border-radius: 20px; /* Rounded pill shape */
    border: 1px solid #eee;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 5px;
}

.control-btn {
    border: none;
    background: transparent;
    color: #999;
    cursor: pointer;
    padding: 2px 5px;
    transition: color 0.2s;
    font-size: 14px; /* Slightly larger icons */
}

.control-btn:hover {
    color: #f5a623;
}

.control-btn.active {
    color: #f5a623;
    font-weight: bold;
}

.separator {
    width: 1px;
    height: 16px;
    background: #ddd;
    margin: 0 2px;
}

/* Color Picker Centered and Slightly Larger */
.color-picker-wrapper {
    display: flex;
    justify-content: center;
    width: 100%;
}

.color-picker {
    width: 40px; /* Agrandit légèrement */
    height: 25px;
    padding: 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    background: none;
}

.data-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    color: #555;
    vertical-align: middle;
}

.data-table tr:hover td {
    background-color: #fcfcfc;
}

.data-table tr:last-child td {
    border-bottom: none;
}

</style>

<div class="project-header">
    <div class="project-title">
        <h1><?= htmlspecialchars($project['project_name']) ?></h1>
        <span style="font-size: 13px; color: #888;">ID: <?= $project['id'] ?> | Créé le: <?= date('d/m/Y', strtotime($project['created_at'])) ?></span>
    </div>
    <div class="action-group">
        <button type="submit" form="projectForm" class="btn-action">
            <i class="fas fa-file-word"></i> Exporter Word
        </button>
        <a href="edit_project.php?id=<?= $project_id ?>" class="btn-action btn-primary">
            <i class="fas fa-edit"></i> Modifier structure
        </a>
    </div>
</div>

<?php if ($error_msg): ?>
    <div style="background: #fee; color: #c0392b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>

<div class="action-bar">
    <div class="action-group">
        <button id="selectAllColsBtn" class="btn-action"><i class="far fa-check-square"></i> Tout sélectionner (Colonnes)</button>
        <button id="selectAllRowsBtn" class="btn-action"><i class="far fa-check-square"></i> Tout sélectionner (Lignes)</button>
    </div>
    <div class="action-group">
        <a href="projects_list_page.php" class="btn-action"><i class="fas fa-arrow-left"></i> Retour liste</a>
    </div>
</div>

<div class="search-bar-container" style="margin-bottom: 15px;">
    <div style="position: relative;">
        <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa;"></i>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Rechercher des mots clés..." 
        style="width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
    </div>
</div>

<!-- Hidden input to store current order for JS -->
<form id="projectForm" action="generate_draft.php" method="POST" target="_blank">
    <input type="hidden" name="project_id" value="<?= $project_id ?>">
    <input type="hidden" id="columns_order" name="columns_order" value="<?= htmlspecialchars(json_encode($current_order)) ?>">

    <div class="table-container">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px; min-width: 50px; text-align: center;">
                        <input type="checkbox" id="checkAllRows" style="transform: scale(1.2);">
                    </th>
                    <?php foreach ($current_order as $col): ?>
                        <th data-col="<?= $col ?>">
                            <div class="th-content">
                                <!-- 1. Nom de la colonne -->
                                <div class="col-title"><?= htmlspecialchars($col) ?></div>
                                
                                <!-- 2. Checkbox Visible -->
                                <label class="visibility-toggle" title="Inclure cette colonne dans l'exportation Word">
                                    <input type="checkbox" name="columns[]" value="<?= $col ?>" checked> Exporter
                                </label>

                                <!-- 3. Signes d'organisation (Tri / Déplacement) -->
                                <div class="col-controls">
                                    <!-- Sort Controls -->
                                    <a href="?id=<?= $project_id ?>&sort=<?= $col ?>&order=ASC&columns_order=<?= urlencode(json_encode($current_order)) ?>" 
                                       class="control-btn <?= ($sort_by === $col && $order === 'ASC') ? 'active' : '' ?>" title="Trier croissant">
                                        <i class="fas fa-arrow-up"></i>
                                    </a>
                                    <a href="?id=<?= $project_id ?>&sort=<?= $col ?>&order=DESC&columns_order=<?= urlencode(json_encode($current_order)) ?>" 
                                       class="control-btn <?= ($sort_by === $col && $order === 'DESC') ? 'active' : '' ?>" title="Trier décroissant">
                                        <i class="fas fa-arrow-down"></i>
                                    </a>
                                    
                                    <div class="separator"></div>

                                    <!-- Move Controls -->
                                    <button class="control-btn moveLeft" data-col="<?= $col ?>" title="Déplacer à gauche">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button class="control-btn moveRight" data-col="<?= $col ?>" title="Déplacer à droite">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>

                                <!-- 4. Couleur centrée et agrandie -->
                                <div class="color-picker-wrapper">
                                    <input type="color" name="color_<?= $col ?>" class="color-picker" title="Changer couleur texte">
                                </div>
                            </div>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="<?= count($current_order) + 1 ?>" style="text-align: center; padding: 40px; color: #888;">
                            <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
                            Aucune donnée enregistrée pour ce projet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="selected_rows[]" class="rowCheckbox" value="<?= $row['id'] ?>" style="transform: scale(1.2);">
                            </td>
                            <?php foreach ($current_order as $col): ?>
                                <td data-col="<?= $col ?>">
                                    <?php 
                                        $val = $row[$col] ?? '';
                                        // Detect file paths
                                        if (strpos($val, 'uploads/') === 0 || strpos(strtolower($col), 'upload') !== false || strpos(strtolower($col), 'image') !== false) {
                                            if ($val) {
                                                $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                    echo "<a href='" . htmlspecialchars($val) . "' target='_blank'><img src='" . htmlspecialchars($val) . "' alt='Image' style='max-width: 80px; max-height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;'></a>";
                                                } else {
                                                    echo "<a href='" . htmlspecialchars($val) . "' target='_blank' style='color: #3498db; text-decoration: none; font-weight: 500;'><i class='fas fa-file-download'></i> Voir fichier</a>";
                                                }
                                            }
                                        } else {
                                            $displayVal = strlen($val) > 100 ? substr($val, 0, 100) . '...' : $val;
                                            echo htmlspecialchars($displayVal); 
                                        }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Select All Columns Checkbox
    const selectAllColsBtn = document.getElementById('selectAllColsBtn');
    if (selectAllColsBtn) {
        selectAllColsBtn.onclick = () => {
            const c = [...document.querySelectorAll('input[name="columns[]"]')];
            if (c.length === 0) return;
            const a = c.every(x => x.checked);
            c.forEach(x => x.checked = !a);
        };
    }

    // Select All Rows Checkbox (Button)
    const selectAllRowsBtn = document.getElementById('selectAllRowsBtn');
    if (selectAllRowsBtn) {
        selectAllRowsBtn.onclick = () => {
            const c = [...document.querySelectorAll('.rowCheckbox')];
            if (c.length === 0) return;
            const a = c.every(x => x.checked);
            c.forEach(x => x.checked = !a);
        };
    }
    
    // Select All Rows (Header Checkbox)
    const checkAllRows = document.getElementById('checkAllRows');
    if (checkAllRows) {
        checkAllRows.onclick = () => {
            const c = [...document.querySelectorAll('.rowCheckbox')];
            c.forEach(x => x.checked = checkAllRows.checked);
        };
    }

    // Search Filter
    window.filterTable = function() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.querySelector('.data-table');
        const trs = table.getElementsByTagName('tr');

        // Start from 1 to skip header
        for (let i = 1; i < trs.length; i++) {
            let visible = false;
            const tds = trs[i].getElementsByTagName('td');
            for (let j = 0; j < tds.length; j++) {
                if (tds[j]) {
                    const txtValue = tds[j].textContent || tds[j].innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        visible = true;
                        break;
                    }
                }
            }
            trs[i].style.display = visible ? "" : "none";
        }
    };

    // Color Picker
    document.querySelectorAll('input[type=color]').forEach(p => {
        p.oninput = () => {
            const col = p.name.replace('color_', '');
            document.querySelectorAll(`td[data-col="${col}"]`).forEach(td => td.style.color = p.value);
            // Color title too
            const title = document.querySelector(`th[data-col="${col}"] .col-title`);
            if (title) title.style.color = p.value;
        }
    });

    // Export Validation
    const projectForm = document.getElementById('projectForm');
    if (projectForm) {
        projectForm.addEventListener('submit', function(e) {
            const checkedRows = document.querySelectorAll('input[name="selected_rows[]"]:checked').length;
            const checkedCols = document.querySelectorAll('input[name="columns[]"]:checked').length;
            
            if (checkedRows === 0) {
                e.preventDefault();
                alert("⚠️ Aucune ligne sélectionnée.\nVeuillez cocher les cases à gauche des lignes que vous souhaitez exporter.");
                return;
            }
            
            if (checkedCols === 0) {
                e.preventDefault();
                alert("⚠️ Aucune colonne visible.\nVeuillez cocher 'Visible' sur au moins une colonne pour l'exportation.");
                return;
            }
        });
    }

    // Column Reordering
    document.querySelectorAll('.moveLeft, .moveRight').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const colName = this.dataset.col;
            const direction = this.classList.contains('moveLeft') ? -1 : 1;
            
            // Get current order from hidden input
            let orderInput = document.getElementById('columns_order');
            if (!orderInput) return;
            
            let currentOrder = [];
            try {
                currentOrder = JSON.parse(orderInput.value);
            } catch(e) {
                console.error("Erreur parsing JSON", e);
                return;
            }
            
            const index = currentOrder.indexOf(colName);
            if (index === -1) return;
            
            const newIndex = index + direction;
            
            // Check bounds
            if (newIndex < 0 || newIndex >= currentOrder.length) return;
            
            // Swap
            [currentOrder[index], currentOrder[newIndex]] = [currentOrder[newIndex], currentOrder[index]];
            
            // Reload page with new order
            // We must preserve current sort params if any
            const url = new URL(window.location.href);
            url.searchParams.set('columns_order', JSON.stringify(currentOrder));
            window.location.href = url.toString();
        });
    });
});
</script>

<?php include 'layout_footer.php'; ?>
