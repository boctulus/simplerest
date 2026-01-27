/*
    Logica de paginacion
*/

class Paginator {
    /*
        Calcula el offset dados el size la page y la page actual
    */
    static calcOffset(currentPage, pageSize) {
        return (pageSize * currentPage) - pageSize;
    }

    static human2SQL(page, pageSize) {
        const offset = this.calcOffset(page, pageSize);
        const limit = pageSize;

        return [offset, limit];
    }

    /*
        Calcula todo lo que debe tener el paginador 
        a excepción de la próxima url
    */
    static calc(currentPage, pageSize, rowCount) {
        const pageCount = Math.ceil(rowCount / pageSize);

        let count;
        if (currentPage < pageCount) {
            count = pageSize;
        } else if (currentPage > pageCount) {
            count = 0;
        } else {
            count = rowCount - (pageSize * (currentPage - 1));
        }

        return {
            total: rowCount,     // cantidad total de registros
            count: count,
            currentPage: currentPage,
            totalPages: pageCount,
            pageSize: pageSize
        };
    }
}
