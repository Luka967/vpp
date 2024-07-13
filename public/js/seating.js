(() => {

    /**
     * @param {HTMLDivElement} elem
     * @param {HTMLInputElement} [inputRowsElem]
     * @param {HTMLInputElement} [inputColsElem]
     * @param {HTMLInputElement} outputElem
     * @param {string[]} seatTypes
     */
    function makeInteractive(elem, inputRowsElem, inputColsElem, outputElem) {
        if (outputElem == null)
            return;

        const allSeatTypes = elem.querySelectorAll('.seating-legend > *');
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
         * @param {number} row
         * @param {number} col
         */
        function setSeatEmpty(divSeat, row, col) {
            divSeat.className = 'seat';
            const seatType = data[row][col];
            divSeat.classList.add(seatType == null ? 'empty' : seatType);

            if (divSeat.children.length > 0 && seatType == null)
                while (divSeat.children.length > 0)
                    divSeat.removeChild(divSeat.lastElementChild);
            else if (divSeat.children.length === 0 && seatType != null) {
                const iSeatGlyph = document.createElement('i');
                iSeatGlyph.classList.add('fa-solid', 'fa-couch');

                const divIcon = document.createElement('div');
                divIcon.classList.add('icon');
                divIcon.appendChild(iSeatGlyph);

                const divNumber = document.createElement('div');
                divNumber.classList.add('number');

                divSeat.appendChild(divIcon);
                divSeat.appendChild(divNumber);
            }
        }

        function updateReservationForm() {
            const date = new Date(`${document.getElementById('reservation-date').innerText} ${new Date().getFullYear()}`);
            const dayOfWeek = date.getDay();
            const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;

            let priceTotal = 0;
            const pickedSeatIndices = [];
            elem.querySelectorAll('.seat.picked').forEach(seat => {
                const type = seat.className.replace('seat', '').replace('picked', '').trim();
                const seatType = document.querySelector(`.seating-legend > .item.${type}`);
                const pricesSplit = seatType.getAttribute('data-pricing').split(',').map(x => parseInt(x));
                priceTotal += isWeekend ? pricesSplit[1] : pricesSplit[0];
                const row = seat.parentElement.getAttribute('data-row');
                const col = seat.parentElement.getAttribute('data-col');
                pickedSeatIndices.push(`${row}-${col}`);
            });

            document.getElementById('reservation-form-price').innerText = priceTotal.toString();
            outputElem.value = pickedSeatIndices.join(',');
        }

        function updateDom() {
            const colCounts = data.map(row => row.length);
            const oneRowColCount = colCounts[0];
            if (colCounts.some(colCount => colCount !== oneRowColCount)) {
                alert('Inconsistent data: col count does not match on all rows');
                return;
            }

            elem.querySelector('td.seating-table-projector').colSpan = oneRowColCount;

            while (colgroup.children.length > 1 + oneRowColCount)
                colgroup.removeChild(colgroup.lastElementChild);
            while (colgroup.children.length < 1 + oneRowColCount)
                colgroup.appendChild(document.createElement('col'));

            const rowCount = data.length;

            while (tbody.children.length > rowCount)
                tbody.removeChild(tbody.lastElementChild);
            while (tbody.children.length < rowCount) {
                const tdRowIdx = document.createElement('td');
                tdRowIdx.classList.add('row-index');
                tdRowIdx.innerText = 1 + tbody.children.length;

                const tr = document.createElement('tr');
                tr.appendChild(tdRowIdx);
                tbody.appendChild(tr);
            }

            for (let row = 0; row < rowCount; row++) {
                const rowElem = tbody.children.item(row);
                while (rowElem.children.length > 1 + oneRowColCount)
                    rowElem.removeChild(rowElem.children.item(rowElem.children.length - 2));
                while (rowElem.children.length < 1 + oneRowColCount) {
                    const divSeat = document.createElement('div');
                    divSeat.classList.add('seat', 'empty');

                    const td = document.createElement('td');
                    td.setAttribute('data-row', row);
                    td.setAttribute('data-col', rowElem.children.length - 1);
                    td.appendChild(divSeat);

                    rowElem.insertBefore(td, rowElem.lastElementChild);

                    if (isEditable || data[row][rowElem.children.length - 2] != null)
                        td.style.cursor = 'pointer';
                    td.addEventListener('click', () => {
                        const row = parseInt(td.getAttribute('data-row'));
                        const col = parseInt(td.getAttribute('data-col'));
                        if (isEditable) {
                            data[row][col] = data[row][col] !== setTypeTo ? setTypeTo : null;
                            updateDom();
                        } else {
                            divSeat.classList.toggle('picked');
                            updateReservationForm();
                        }
                    });
                }

                for (let col = 0; col < oneRowColCount; col++) {
                    const td = rowElem.children.item(col);
                    setSeatEmpty(td.children.item(0), row, col);
                    let colIdx = 0;
                    for (let i = data[row].length - 1; i >= col; i--)
                        if (data[row][i] != null)
                            colIdx++;
                    if (data[row][col] != null)
                        td.querySelector('.number').innerText = colIdx.toString();
                }
            }

            const outputSegments = [];
            for (let row = 0; row < rowCount; row++)
                for (let col = 0; col < oneRowColCount; col++)
                    outputSegments.push(data[row][col] ?? '');
            if (isEditable)
                outputElem.value = `${rowCount};${oneRowColCount};${outputSegments.join(';')}`;
            else
                outputElem.value = '';
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
        }

        if (outputElem.value.length > 0) {
            const split = outputElem.value.split(';');
            const rows = parseInt(split[0]);
            const cols = parseInt(split[1]);
            updateDataSize(rows, cols);
            for (let i = 0; i < rows * cols; i++) {
                const seatType = split[i + 2];
                const col = i % cols;
                const row = Math.floor((i - col) / cols);
                data[row][col] = seatType === '' ? null : seatType;
            }
            updateDom();
            if (isEditable) {
                inputRowsElem.valueAsNumber = rows;
                inputColsElem.valueAsNumber = cols;
            }
        }

        if (isEditable) {
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
                updateDom();
            }

            inputRowsElem.addEventListener('change', onChange);
            inputColsElem.addEventListener('change', onChange);
            onChange();
        } else
            updateDataSize(10, 10);
        updateDataSize();
    }

    window.addEventListener('load', () => {
        const divs = loadIds(
            'theater-seating', 'theater-seating-rowcount', 'theater-seating-colcount', 'theater-seating-output',
            'theater-reservation-seating', 'reservation-form-output'
        );
        makeInteractive(
            divs['theater-seating'],
            divs['theater-seating-rowcount'],
            divs['theater-seating-colcount'],
            divs['theater-seating-output']
        );
        makeInteractive(
            divs['theater-reservation-seating'],
            null,
            null,
            divs['reservation-form-output']
        )
    });
})();
