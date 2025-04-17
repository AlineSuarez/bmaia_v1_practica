$(document).ready(function () {
    $("#apiariosTable").DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/es_es.json",
        },
        ordering: true,
        columnDefs: [{ className: "text-center", targets: "_all" }],
    });

    // Habilitar/deshabilitar botones
    $(".select-checkbox").on("change", function () {
        let selected = $(".select-checkbox:checked").length > 0;
        $("#multiDeleteButton").prop("disabled", !selected);
        $("#multiEditButton").prop("disabled", !selected);
    });

    $("#selectAll").on("click", function () {
        $(".select-checkbox").prop("checked", this.checked).trigger("change");
    });

    // Eliminar seleccionados
    $("#multiDeleteButton").on("click", function () {
        let selectedIds = $(".select-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        if (selectedIds.length > 0) {
            // Lógica para eliminación múltiple (ejemplo de modal de confirmación)
            $("#deleteModal").modal("show");
            $("#confirmDelete").on("click", function () {
                $.ajax({
                    url: '{{ route("apiarios.massDelete") }}',
                    type: "POST",
                    data: { ids: selectedIds, _token: "{{ csrf_token() }}" },
                    success: function () {
                        location.reload();
                    },
                });
            });
        }
    });
});
