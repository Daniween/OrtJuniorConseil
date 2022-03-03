const dataTable = new simpleDatatables.DataTable("#datatable", {
    searchable: true,
    fixedHeight: true
});

function exportDatatable(name) {
    if (dataTable) {
        document.querySelectorAll(".export").forEach(function(el) {
            el.addEventListener("click", function(e) {

                var type = el.dataset.type;
                var data = {
                    type: type,
                    filename: name,
                };

                if (type === "csv") {
                    data.columnDelimiter = ";";
                }

                dataTable.export(data);
            });
        });
    };
}
