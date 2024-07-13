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
