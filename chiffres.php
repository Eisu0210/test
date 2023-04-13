<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des ouvriers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .add-worker-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1rem;
        }

        .add-worker-button {
            cursor: pointer;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            font-size: 16px;
        }

        .add-worker-button:hover {
            background-color: #45a049;
        }

        .modal {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            width: 50%;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>
<body>
    <h1>Gestion des ouvriers</h1>
    <button id="add-worker-button">Ajouter un ouvrier</button>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Date de début</th>
                <th>Date de fin</th>
            </tr>
        </thead>
        <tbody id="worker-table">
        </tbody>
    </table>
    

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetchWorkerData();

            const addWorkerButton = document.getElementById('add-worker-button');
            if (addWorkerButton) {
                addWorkerButton.addEventListener('click', openAddWorkerDialog);
            }
        });

        async function fetchWorkerData() {
            const workerTable = document.getElementById('worker-table');
            if (!workerTable) return;

            const response = await fetch(window.location.href + '?action=fetch-data');
            const workerData = await response.json();

            workerTable.innerHTML = '';
            workerData.forEach(worker => {
                addWorkerRow(worker);
            });
        }

        function addWorkerRow(worker) {
            const workerTable = document.getElementById('worker-table');
            if (!workerTable) return;

            const row = document.createElement('tr');

            const nameCell = document.createElement('td');
            nameCell.textContent = worker.name;
            row.appendChild(nameCell);

            const startDateCell = document.createElement('td');
            startDateCell.textContent = worker.start_date;
            row.appendChild(startDateCell);

            const endDateCell = document.createElement('td');
            endDateCell.textContent = worker.end_date;
            row.appendChild(endDateCell);

            workerTable.appendChild(row);
        }

function openAddWorkerDialog() {
    const dialog = document.createElement('div');
    dialog.classList.add('modal');

    const content = document.createElement('div');
    content.classList.add('modal-content');
    dialog.appendChild(content);

    const header = document.createElement('div');
    header.classList.add('modal-header');
    header.innerHTML = '<h3>Ajouter un ouvrier</h3>';
    content.appendChild(header);

    const form = document.createElement('form');
    form.classList.add('modal-body');
    form.innerHTML = `
        <label for="worker-name">Nom de l'ouvrier</label>
        <input type="text" id="worker-name" name="worker-name" required>
        <label for="start-date">Date de début</label>
        <input type="date" id="start-date" name="start-date" required>
        <label for="end-date">Date de fin</label>
        <input type="date" id="end-date" name="end-date">
    `;
    content.appendChild(form);

    const footer = document.createElement('div');
    footer.classList.add('modal-footer');
    content.appendChild(footer);

    const cancelButton = document.createElement('button');
    cancelButton.textContent = 'Annuler';
    cancelButton.type = 'button';
    cancelButton.addEventListener('click', function() {
        document.body.removeChild(dialog);
    });
    footer.appendChild(cancelButton);

    const addButton = document.createElement('button');
    addButton.textContent = 'Ajouter';
    addButton.type = 'button';
    addButton.addEventListener('click', async function() {
        await submitAddWorkerForm();
        document.body.removeChild(dialog);
    });
    footer.appendChild(addButton);

    document.body.appendChild(dialog);
}


async function submitAddWorkerForm() {
    const workerName = document.getElementById('worker-name').value.trim();
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    if (workerName && startDate) {
        const response = await fetch(window.location.href + '?action=add-worker', {
            method: 'POST',
            body: JSON.stringify({ name: workerName, start_date: startDate, end_date: endDate }),
            headers: { 'Content-Type': 'application/json' },
        });
        const result = await response.json();

        if (result.success) {
            const worker = {
                name: workerName,
                start_date: startDate,
                end_date: endDate
            };
            addWorkerRow(worker);
            closeAddWorkerDialog(); // Ferme la boîte de dialogue après avoir cliqué sur "Ajouter"
        } else {
            alert("Une erreur s'est produite lors de l'ajout de l'ouvrier.");
        }
    }
}


    function closeAddWorkerDialog() {
        const dialog = document.querySelector('.modal');
        if (dialog) {
            document.body.removeChild(dialog);
        }
    }
</script>

<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'fetch-data') {
        header('Content-Type: application/json');

        $pdo = new PDO('mysql:host=localhost;dbname=Loadnet', 'root', 'root');

        $query = "SELECT * FROM ouvriers";
        $stmt = $pdo->query($query);
        $workerData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($workerData);
    } elseif ($_GET['action'] === 'add-worker' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (isset($data['name'], $data['start_date'])) {
            $name = $data['name'];
            $start_date = $data['start_date'];
            $end_date = isset($data['end_date']) ? $data['end_date'] : null;

            $pdo = new PDO('mysql:host=localhost;dbname=Loadnet', 'root', 'root');
            $query = "INSERT INTO ouvriers (nom, date_debut, date_fin) VALUES (:name, :start_date, :end_date)";
            $stmt = $pdo->prepare($query);
            $success = $stmt->execute([
                ':name' => $name,
                ':start_date' => $start_date,
                ':end_date' => $end_date,
            ]);

            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
        } else {
            header('HTTP/1.1 400 Bad Request');
        }
    }

    exit;
}
?>
</body>
</html>

