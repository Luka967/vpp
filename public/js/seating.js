(() => {

    /**
     * @param {HTMLDivElement} elem
     * @param {HTMLInputElement} [inputRowsElem]
     * @param {HTMLInputElement} [inputColsElem]
     * @param {string[]} seatTypes
     */
    function makeInteractive(elem, inputRowsElem, inputColsElem) {
        const isEditable = inputRowsElem != null && inputColsElem != null;
        /** @type {string} */
        let setTypeTo = null;
        /** @type {string} */
        let firstSeatType = null;

        /** @type {(string | null)[][]} */
        const data = [];
        const colgroup = elem.querySelector('colgroup');
        const tbody = elem.querySelector('tbody');

        /**
         * @param {Element} divSeat
         * @param {number} col
         * @param {string | null} data
         */
        function toggleSeatEmpty(divSeat, col, data) {
            const creating = divSeat.classList.contains('empty');
            divSeat.classList.toggle('empty');

            if (!creating)
                while (divSeat.children.length > 0)
                    divSeat.removeChild(divSeat.lastElementChild);
            else {
                divSeat.classList.add(data);

                const iSeatGlyph = document.createElement('i');
                iSeatGlyph.classList.add('fa-solid', 'fa-couch');

                const divIcon = document.createElement('div');
                divIcon.classList.add('icon');
                divIcon.appendChild(iSeatGlyph);

                const divNumber = document.createElement('div');
                divNumber.classList.add('number');
                divNumber.innerText = col.toString();

                divSeat.appendChild(divIcon);
                divSeat.appendChild(divNumber);
            }
        }

        function updateDom() {
            const colCounts = data.map(row => row.length);
            const oneRowColCount = colCounts[0];
            if (colCounts.some(colCount => colCount !== oneRowColCount)) {
                alert('Inconsistent data: col count does not match on all rows');
                return;
            }

            elem.querySelector('td.seating-table-projector').colSpan = oneRowColCount;

            while (colgroup.children.length > oneRowColCount)
                colgroup.removeChild(colgroup.lastElementChild);
            while (colgroup.children.length < oneRowColCount)
                colgroup.appendChild(document.createElement('col'));

            const rowCount = data.length;

            while (tbody.children.length > rowCount)
                tbody.removeChild(tbody.lastElementChild);
            while (tbody.children.length < rowCount)
                tbody.appendChild(document.createElement('tr'));

            for (let row = 0; row < rowCount; row++) {
                const rowElem = tbody.children.item(row);
                while (rowElem.children.length > oneRowColCount)
                    rowElem.removeChild(rowElem.lastElementChild);
                while (rowElem.children.length < oneRowColCount) {
                    const divSeat = document.createElement('div');
                    divSeat.classList.add('seat', 'empty');

                    const td = document.createElement('td');
                    td.setAttribute('data-row', row);
                    td.setAttribute('data-col', rowElem.children.length);
                    td.appendChild(divSeat);

                    rowElem.appendChild(td);

                    if (isEditable) {
                        td.style.cursor = 'pointer';
                        td.addEventListener('click', ev => {
                            const row = parseInt(td.getAttribute('data-row'));
                            const col = parseInt(td.getAttribute('data-col'));
                            if (isEditable) {
                                data[row][col] = setTypeTo;
                                updateDom();
                            } else {
                                divSeat.classList.toggle('picked');
                                console.log('pick row', row, 'col', col);
                            }
                        });
                    }
                }

                for (let col = 0; col < oneRowColCount; col++) {
                    const td = rowElem.children.item(col);
                    const divSeat = td.children.item(0);
                    const seatType = data[row][col];
                    if ((seatType == null) !== divSeat.classList.contains('empty'))
                        toggleSeatEmpty(divSeat, 1 + col, seatType);
                    else if (!divSeat.classList.contains(data[row][col])) {
                        divSeat.className = '';
                        divSeat.classList.add('seat', data[row][col]);
                    }
                }
            }
        }
        /**
         * @param {number} rowCount
         * @param {number} colCount
         */
        function updateDataSize(rowCount, colCount) {
            while (data.length > rowCount)
                data.pop();
            while (data.length < rowCount)
                data.push([]);

            for (let row = 0; row < data.length; row++) {
                while (data[row].length > colCount)
                    data[row].pop();
                while (data[row].length < colCount)
                    data[row].push(firstSeatType);
            }
            updateDom();
        }

        if (isEditable) {
            const allSeatTypes = elem.querySelectorAll('.seating-legend > *');
            allSeatTypes.forEach(typeElem => {
                typeElem.classList.add('button', 'secondary', 'pop');
                typeElem.addEventListener('click', () => {
                    const isSettingToThis = typeElem.classList.contains('secondary');
                    setTypeTo = isSettingToThis ? typeElem.querySelector('span').innerText : null;
                    allSeatTypes.forEach(v => v.classList.toggle('secondary', true));
                    if (isSettingToThis)
                        typeElem.classList.toggle('secondary', false);
                });
                if (firstSeatType == null)
                    firstSeatType = typeElem.querySelector('span').innerText;
            });

            function onChange() {
                updateDataSize(inputRowsElem.valueAsNumber, inputColsElem.valueAsNumber);
            }
            inputRowsElem.addEventListener('change', onChange);
            inputColsElem.addEventListener('change', onChange);
            onChange();
        } else
            updateDataSize(10, 10);
        updateDataSize();
    }

    window.addEventListener('load', () => {
        const divs = loadIds('theater-seating', 'theater-seating-rowcount', 'theater-seating-colcount');
        makeInteractive(divs['theater-seating'], divs['theater-seating-rowcount'], divs['theater-seating-colcount']);
    });
})();
