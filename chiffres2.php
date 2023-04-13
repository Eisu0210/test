<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Chiffres</title>
    <style>
        /* Styles CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #333333;
            color: orange;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        .weekend {
            background-color: #555555;
        }

        .holiday {
            background-color: #ffcccc;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialiser le tableau et les événements
            generateMonthYearOptions();
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth() + 1;
            const currentYear = currentDate.getFullYear();
            $('#month-year').val(`${currentYear}-${currentMonth}`);
            generateTable(currentMonth, currentYear);

            $('#month-year').on('change', function () {
                const [year, month] = $(this).val().split('-');
                generateTable(parseInt(month), parseInt(year));
            });

            $('#add-worker').on('click', addWorker);
        });

        function generateMonthYearOptions() {
            // Générer les options pour les mois et les années
            const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
            const startYear = 2020;
            const endYear = new Date().getFullYear();

            for (let year = startYear; year <= endYear; year++) {
                for (let month = 0; month < 12; month++) {
                    const option = $('<option>', { value: `${year}-${month + 1}`, text: `${monthNames[month]} ${year}` });
                    $('#month-year').append(option);
                }
            }
        }

        function generateTable(month, year) {
            // Générer le tableau de production en fonction du mois et de l'année
            const startDate = new Date(year, month - 1, 1);
            const endDate = new Date(year, month, 0);
            const daysInMonth = endDate.getDate();
            let currentDay = startDate;

            // Entêtes de tableau
            const headerRow = $('<tr>');
            for (let i = 1; i <= daysInMonth; i++) {
                const th = $('<th>', { text: i });
                headerRow.append(th);
            }
            headerRow.append($('<th>', { text: 'Moyenne' }));
            headerRow.append($('<th>', { text: 'Total' }));
            $('#production-table').html(headerRow);

            // Lignes pour les ouvriers
            // Récupérer les ouvriers depuis la base de données et les afficher
            fetchData(month, year);
        }

        function fetchData(month, year) {
            // Récupérer les données de la base de données et mettre à jour le tableau
            // Pour l'instant, nous utiliserons des données factices
            // Ici, vous devrez écrire le code pour récupérer les données réelles des ouvriers depuis la base de données
            // Exemple d'objet ouvrier
            const workerData = [
                {
                    name: 'John Doe',
                    days: {
                        '2023-04-01': 100,
                        '2023-04-02': 150
                    }
                }
            ];

            workerData.forEach((worker) => {
                addWorkerRow(worker);
            });
        }

        function addWorkerRow(worker) {
            const row = $('<tr>');
            row.append($('<td>', { text: worker.name }));

            // Ajoutez les données de production journalière pour chaque ouvrier
            const monthYear = $('#month-year').val().split('-');
            const year = parseInt(monthYear[0]);
            const month = parseInt(monthYear[1]);
            const endDate = new Date(year, month, 0);
            const daysInMonth = endDate.getDate();

            let total = 0;

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month - 1, day);
                const dateString = `${year}-${month}-${day}`;
                const value = worker.days[dateString] || 0;

                if (date.getDay() === 0 || date.getDay() === 6) {
                    row.append($('<td>', { text: value, class: 'weekend' }));
                } else {
                    row.append($('<td>', { text: value }));
                }

                // TODO: Ajouter la gestion des jours fériés ici
                // Exemple: si (isHoliday(date)) { row.addClass('holiday'); }

                total += value;
            }

            const average = (total / daysInMonth).toFixed(2);
            row.append($('<td>', { text: average }));
            row.append($('<td>', { text: total }));

            $('#production-table').append(row);
        }

        function addWorker() {
            // Ajouter un ouvrier à la base de données et au tableau
            const workerName = prompt("Entrez le nom de l'ouvrier :");

            // Validez que le nom n'est pas vide et ajoutez-le à la base de données
            if (workerName) {
                // Ici, vous devrez écrire le code pour ajouter le nouvel ouvrier à la base de données

                // Ajoutez l'ouvrier au tableau avec des données vides
                const worker = {
                    name: workerName,
                    days: {}
                };
                addWorkerRow(worker);
            }
        }
    </script>
</head>
<body>
    <h1>Chiffres</h1>
    <label for="month-year">Mois et année :</label>
    <select id="month-year"></select>
    <table id="production-table"></table>
    <button id="add-worker">Ajouter un ouvrier</button>
</body>
</html>
