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

        const input = $('<input>', { type: 'text', value: value, size: 5 }); // Ajoutez cette ligne

        if (date.getDay() === 0 || date.getDay() === 6) {
            row.append($('<td>', { class: 'weekend' }).append(input)); // Modifiez cette ligne
        } else {
            row.append($('<td>').append(input)); // Modifiez cette ligne
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
