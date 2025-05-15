<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>

    <link href="css/custom.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <title>Agenda de Reuniões - Ceisc</title>
</head>

<body>

    <div class="container">

        <div class="card mb-4 border-light shadow">
            <div class="card-body">
                <h2 class="mt-0 me-3 ms-2 pb-2 border-bottom">
                    <center>Agendamento de Reuniões - CEISC</center>
                </h2><br>

                <span id="msg"></span>

                <form class="ms-2 me-2">

                    <div class="col-md-6 col-sm-12">
                        <label class="form-label" for="user_id">Pesquisar ocupação das salas</label>
                        <select name="user_id[]" id="user_id" class="form-select" multiple>
                            <option value="">Selecione</option>
                            <option value="1" data-color="#FF4500">Cabine 3ª andar</option>
                            <option value="2" data-color="#8B4513">Cabine Térreo</option>
                            <option value="3" data-color="#0071c5">Sala de Reuniões 01</option>
                            <option value="4" data-color="#228B22">Sala de Reuniões 02</option>
                            <option value="5" data-color="#FFD700">Sala de Reuniões 03</option>
                        </select>
                    </div>

                </form>

            </div>
        </div>

        <div class="card p-4 border-light shadow">
            <div class="card-body">
                <div id='calendar'></div>
            </div>
        </div>

    </div>

    <!-- Modal Visualizar -->
    <div class="modal fade" id="visualizarModal" tabindex="-1" aria-labelledby="visualizarModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <h1 class="modal-title fs-5" id="visualizarModalLabel">Visualizar Agendamento</h1>

                    <h1 class="modal-title fs-5" id="editarModalLabel" style="display: none;">Editar Agendamento</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <span id="msgViewEvento"></span>

                    <div id="visualizarEvento">

                        <dl class="row">

                            <dt class="col-sm-3">ID: </dt>
                            <dd class="col-sm-9" id="visualizar_id"></dd>

                            <dt class="col-sm-3">Descrição: </dt>
                            <dd class="col-sm-9" id="visualizar_title"></dd>

                            <dt class="col-sm-3">Autor(a) </dt>
                            <dd class="col-sm-9" id="visualizar_resp"></dd>

                            <dt class="col-sm-3">Início: </dt>
                            <dd class="col-sm-9" id="visualizar_start"></dd>

                            <dt class="col-sm-3">Fim: </dt>
                            <dd class="col-sm-9" id="visualizar_end"></dd>

                            <dt class="col-sm-3">ID da Sala: </dt>
                            <dd class="col-sm-9" id="visualizar_user_id"></dd>

                            <dt class="col-sm-3">Sala: </dt>
                            <dd class="col-sm-9" id="visualizar_sala"></dd>


                        </dl>

                        <button type="button" class="btn btn-warning" id="btnViewEditEvento">Editar Agendamento</button>

                        <button type="button" class="btn btn-danger" id="btnApagarEvento">Apagar Agendamento</button>

                    </div>

                    <div id="editarEvento" style="display: none;">

                        <span id="msgEditEvento"></span>

                        <form method="POST" id="formEditEvento">

                            <input type="hidden" name="edit_id" id="edit_id">

                            <div class="row mb-3">
                                <label for="edit_title" class="col-sm-2 col-form-label">Descrição</label>
                                <div class="col-sm-10">
                                    <input type="text" name="edit_title" class="form-control" id="edit_title"
                                        placeholder="Descrição do agendamento" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="edit_resp" class="col-sm-2 col-form-label">Autor(a)</label>
                                <div class="col-sm-10">
                                    <input type="text" name="edit_resp" class="form-control" id="edit_resp"
                                        placeholder="Responsável pela reserva da sala" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="edit_start" class="col-sm-2 col-form-label">Início</label>
                                <div class="col-sm-10">
                                    <input type="datetime-local" name="edit_start" class="form-control" id="edit_start"
                                        required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="edit_end" class="col-sm-2 col-form-label">Fim</label>
                                <div class="col-sm-10">
                                    <input type="datetime-local" name="edit_end" class="form-control" id="edit_end"
                                        required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="edit_user_id" class="col-sm-2 col-form-label">Sala</label>
                                <div class="col-sm-10">
                                    <select name="edit_user_id" class="form-control" id="edit_user_id" required>
                                        <option value="">Selecione</option>
                                    </select>
                                </div>
                            </div>

                            <button type="button" name="btnViewEvento" class="btn btn-primary"
                                id="btnViewEvento">Cancelar</button>

                            <button type="submit" name="btnEditEvento" class="btn btn-warning"
                                id="btnEditEvento">SalvarAgendamento</button>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cadastrar -->
    <div class="modal fade" id="cadastrarModal" tabindex="-1" aria-labelledby="cadastrarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="cadastrarModalLabel">Cadastrar Agendamento
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <span id="msgCadEvento"></span>

                    <form method="POST" id="formCadEvento">

                        <div class="row mb-3">
                            <label for="cad_title" class="col-sm-2 col-form-label">Descrição</label>
                            <div class="col-sm-10">
                                <input type="text" name="cad_title" class="form-control" id="cad_title"
                                    placeholder="Descrição do agendamento" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="cad_resp" class="col-sm-2 col-form-label">Autor(a)</label>
                            <div class="col-sm-10">
                                <input type="text" name="cad_resp" class="form-control" id="cad_resp"
                                    placeholder="Responsável pela reserva da sala" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="cad_start" class="col-sm-2 col-form-label">Início</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="cad_start" class="form-control" id="cad_start"
                                    required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="cad_end" class="col-sm-2 col-form-label">Fim</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="cad_end" class="form-control" id="cad_end" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="cad_user_id" class="col-sm-2 col-form-label">Sala</label>
                            <div class="col-sm-10">
                                <select name="cad_user_id" class="form-control" id="cad_user_id">
                                    <option value="">Selecione</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" name="btnCadEvento" class="btn btn-success"
                            id="btnCadEvento">Cadastrar</button>

                    </form>

                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script src='js/index.global.min.js'></script>
    <script src="js/bootstrap5/index.global.min.js"></script>
    <script src='js/core/locales-all.global.min.js'></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src='js/custom.js'></script>

    <script>
    $(document).ready(function() {
        $('#user_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Selecione as salas',
            allowClear: true,
            templateResult: formatOption,
            templateSelection: formatOption,
            language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        });

        function formatOption(option) {
            if (!option.id) return option.text;
            var color = $(option.element).data('color');
            if (!color) return option.text;
            return $('<span><span style="display: inline-block; width: 15px; height: 15px; background-color: ' +
                color + '; margin-right: 5px; border-radius: 3px; border: 1px solid #ccc;"></span>' + option
                .text + '</span>');
        }
    });
    </script>

    <style>
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        display: flex;
        align-items: center;
        padding: 2px 8px;
        background-color: #fff;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__display {
        display: flex;
        align-items: center;
    }

    .select2-container--bootstrap-5 .select2-results__option {
        display: flex;
        align-items: center;
    }
    </style>

</body>

</html>