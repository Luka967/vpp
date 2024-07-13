(() => {
    window.addEventListener('load', () => {
        document.querySelectorAll('select').forEach(inputElem => {
            const value = inputElem.getAttribute('data-value');
            if (value == null)
                return;
            inputElem.value = value;
            inputElem.removeAttribute('data-value');
        });
        document.querySelectorAll('.theater-timeline .entry').forEach(entryElem => {
            entryElem.addEventListener('click', () => {
                window.location.href = `/manage/repertoire/edit?id=${entryElem.getAttribute('data-id')}`;
            });
        });
        document.querySelectorAll('.theater-timeline .filler').forEach(timelineElem => {
            if (!timelineElem.classList.contains('pop'))
                return;
            timelineElem.addEventListener('click', () => {
                const inputs = loadIds('screening_start', 'theater_id');

                // toISOString pretvara u UTC, mora se ručno napraviti kompatibilna vrednost
                const date = new Date(parseInt(timelineElem.getAttribute('data-timestamp')) * 1000);
                const chunks = [
                    date.getFullYear().toString().padStart(4, '0'),
                    (1 + date.getMonth()).toString().padStart(2, '0'),
                    date.getDate().toString().padStart(2, '0'),
                    date.getHours().toString().padStart(2, '0'),
                    date.getMinutes().toString().padStart(2, '0'),
                ];

                inputs.screening_start.value = `${chunks[0]}-${chunks[1]}-${chunks[2]}T${chunks[3]}:${chunks[4]}`;
                inputs.theater_id.value = timelineElem.parentElement.getAttribute('data-theater');
                document.body.scrollTo({
                    top: 0,
                    left: 0,
                    behavior: 'smooth'
                });
            });
        });
    });
})();
