function loadIds(...ids) {
    return ids
        .map(id => document.getElementById(id))
        .reduce((all, elem) => {
            if (elem == null)
                return all;
            all[elem.id] = elem;
            return all;
        }, {});
}

/**
 * @param {Date} dateObject
 */
function releaseDateToTag(dateObject) {
    const monthNames = ['JAN', 'FEB', 'MAR', 'APR', 'MAJ', 'JUN', 'JUL', 'AVG', 'SEP', 'OKT', 'NOV', 'DEC'];
    return `${monthNames[dateObject.getMonth()]} ${dateObject.getDate()}`;
}

/**
 * @param {Date} dateObject
 */
function timetableDayOfWeek(dateObject) {
    const names = ['PONEDELJAK', 'UTORAK', 'SREDA', 'ČETVRTAK', 'PETAK', 'SUBOTA', 'NEDELJA'];
    const day = dateObject.getDay();
    if (day === 0) return names[6]; // nedelja je uvek prva (?)
    else return names[day - 1];
}
