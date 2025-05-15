document.addEventListener("DOMContentLoaded", function () {
  // Receber o SELETOR calendar do atributo id
  var calendarEl = document.getElementById("calendar");

  // Receber o SELETOR da janela modal cadastrar
  const cadastrarModal = new bootstrap.Modal(
    document.getElementById("cadastrarModal")
  );

  // Receber o SELETOR da janela modal visualizar
  const visualizarModal = new bootstrap.Modal(
    document.getElementById("visualizarModal")
  );

  // Receber o SELETOR "msgViewEvento"
  const msgViewEvento = document.getElementById("msgViewEvento");

  function carregarEventos() {
    // Receber o SELETOR calendar do atributo id
    var calendarEl = document.getElementById("calendar");

    // Receber o id da sala do campo Select
    var user_id = document.getElementById("user_id").value;

    // Instanciar FullCalendar.Calendar e atribuir a variável calendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
      // Incluir o bootstrap 5
      themeSystem: "bootstrap5",

      // Definir o fuso horário para Brasília
      timeZone: "America/Sao_Paulo",

      // Configurações adicionais para lidar com datas
      eventTimeFormat: {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
        meridiem: false,
      },

      // Configurações para evitar conversão automática de timezone
      forceEventDuration: true,
      slotMinTime: "00:00:00",
      slotMaxTime: "24:00:00",
      slotDuration: "00:15:00",
      slotLabelInterval: "01:00",
      slotLabelFormat: {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
      },

      // Criar o cabeçalho do calendário
      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay,list",
      },

      // Definir o idioma usado no calendário
      locale: "pt-br",

      // Permitir clicar nos nomes dos dias da semana
      navLinks: true,

      // Permitir clicar e arrastar o mouse sobre um ou vários dias no calendário
      selectable: true,

      // Indicar visualmente a área que será selecionada antes que o usuário solte o botão do mouse para confirmar a seleção
      selectMirror: true,

      // Permitir arrastar e redimensionar os eventos diretamente no calendário.
      editable: true,

      // Número máximo de eventos em um determinado dia, se for true, o número de eventos será limitado à altura da célula do dia
      dayMaxEvents: true,

      // Chamar o arquivo PHP para recuperar os eventos
      events: {
        url: "listar_evento.php?user_id=" + user_id,
        timeZone: "America/Sao_Paulo",
        failure: function (error) {
          console.error("Erro ao carregar eventos:", error);
        },
      },

      eventDrop: async function (info) {
        console.log("Evento movido:", info.event);

        // Obter as informações originais do evento
        const eventoOriginal = {
          inicio: {
            data: info.oldEvent.start,
            hora: info.oldEvent.start.getHours(),
            minuto: info.oldEvent.start.getMinutes(),
          },
          fim: {
            data: info.oldEvent.end,
            hora: info.oldEvent.end.getHours(),
            minuto: info.oldEvent.end.getMinutes(),
          },
        };

        // Obter a nova data base
        const novaDataBase = new Date(info.event.start);

        // Criar as novas datas mantendo o horário original
        const novaDataInicio = new Date(
          novaDataBase.getFullYear(),
          novaDataBase.getMonth(),
          novaDataBase.getDate(),
          eventoOriginal.inicio.hora,
          eventoOriginal.inicio.minuto
        );

        const novaDataFim = new Date(
          novaDataBase.getFullYear(),
          novaDataBase.getMonth(),
          novaDataBase.getDate(),
          eventoOriginal.fim.hora,
          eventoOriginal.fim.minuto
        );

        // Se o evento termina no dia seguinte, ajustar a data de fim
        if (
          eventoOriginal.fim.data.getDate() !==
          eventoOriginal.inicio.data.getDate()
        ) {
          novaDataFim.setDate(novaDataFim.getDate() + 1);
        }

        console.log("Informações originais:", {
          inicioOriginal: eventoOriginal.inicio.data,
          fimOriginal: eventoOriginal.fim.data,
          horarioInicio: `${eventoOriginal.inicio.hora}:${eventoOriginal.inicio.minuto}`,
          horarioFim: `${eventoOriginal.fim.hora}:${eventoOriginal.fim.minuto}`,
        });

        console.log("Novas datas:", {
          inicio: novaDataInicio,
          fim: novaDataFim,
          inicioFormatado: novaDataInicio.toLocaleString("pt-BR", {
            timeZone: "America/Sao_Paulo",
          }),
          fimFormatado: novaDataFim.toLocaleString("pt-BR", {
            timeZone: "America/Sao_Paulo",
          }),
        });

        // Função para formatar a data no formato do banco de dados
        const formatarDataParaBanco = (data) => {
          const ano = data.getFullYear();
          const mes = String(data.getMonth() + 1).padStart(2, "0");
          const dia = String(data.getDate()).padStart(2, "0");
          const hora = String(data.getHours()).padStart(2, "0");
          const minuto = String(data.getMinutes()).padStart(2, "0");
          const segundo = String(data.getSeconds()).padStart(2, "0");

          return `${ano}-${mes}-${dia} ${hora}:${minuto}:${segundo}`;
        };

        const dadosForm = new FormData();
        dadosForm.append("edit_id", info.event.id);
        dadosForm.append("edit_title", info.event.title);
        dadosForm.append("edit_resp", info.event.extendedProps.resp);
        dadosForm.append("edit_user_id", info.event.extendedProps.user_id);
        dadosForm.append("edit_sala", info.event.extendedProps.sala);
        dadosForm.append("edit_color", info.event.backgroundColor);
        dadosForm.append("edit_start", formatarDataParaBanco(novaDataInicio));
        dadosForm.append("edit_end", formatarDataParaBanco(novaDataFim));

        try {
          const dados = await fetch("editar_evento.php", {
            method: "POST",
            body: dadosForm,
          });

          const resposta = await dados.json();
          console.log("Resposta do servidor:", resposta);

          if (!resposta["status"]) {
            msgEditEvento.innerHTML = `<div class="alert alert-danger" role="alert">${resposta["msg"]}</div>`;
            info.revert();
          } else {
            // Atualizar o evento mantendo o horário original
            info.event.setStart(novaDataInicio);
            info.event.setEnd(novaDataFim);
          }
        } catch (erro) {
          console.error("Erro ao mover evento:", erro);
          msgEditEvento.innerHTML = `<div class="alert alert-danger" role="alert">Erro ao mover evento. Por favor, tente novamente.</div>`;
          info.revert();
        }
      },

      // Identificar o clique do usuário sobre o evento
      eventClick: function (info) {
        // Apresentar os detalhes do evento
        document.getElementById("visualizarEvento").style.display = "block";
        document.getElementById("visualizarModalLabel").style.display = "block";

        // Ocultar o formulário editar do evento
        document.getElementById("editarEvento").style.display = "none";
        document.getElementById("editarModalLabel").style.display = "none";

        // Enviar para a janela modal os dados do evento
        document.getElementById("visualizar_id").innerText = info.event.id;
        document.getElementById("visualizar_title").innerText =
          info.event.title;
        document.getElementById("visualizar_resp").innerText =
          info.event.extendedProps.resp;
        document.getElementById("visualizar_user_id").innerText =
          info.event.extendedProps.user_id;
        document.getElementById("visualizar_sala").innerText =
          info.event.extendedProps.sala;
        document.getElementById("visualizar_start").innerText =
          info.event.start.toLocaleString();
        document.getElementById("visualizar_end").innerText =
          info.event.end !== null
            ? info.event.end.toLocaleString()
            : info.event.start.toLocaleString();

        // Enviar os dados do evento para o formulário editar
        document.getElementById("edit_id").value = info.event.id;
        document.getElementById("edit_title").value = info.event.title;
        document.getElementById("edit_resp").value =
          info.event.extendedProps.resp;
        document.getElementById("edit_start").value = converterData(
          info.event.start
        );
        document.getElementById("edit_end").value =
          info.event.end !== null
            ? converterData(info.event.end)
            : converterData(info.event.start);
        //document.getElementById("edit_color").value = info.event.backgroundColor;

        // Abrir a janela modal visualizar
        visualizarModal.show();
      },

      // Abrir a janela modal cadastrar quando clicar sobre o dia no calendário
      select: async function (info) {
        // Receber o SELETOR do campo usuário do formulário cadastrar
        var cadUserId = document.getElementById("cad_user_id");

        console.log("Carregando salas para o modal de cadastro...");

        // Chamar o arquivo PHP responsável em recuperar as salas do banco de dados
        const dados = await fetch("listar_salas.php");

        // Ler os dados
        const resposta = await dados.json();
        console.log("Resposta da listagem de salas:", resposta);

        // Acessar o IF quando encontrar sala no banco de dados
        if (resposta["status"]) {
          // Criar a opção selecione para o campo select usuários
          var opcoes = '<option value="">Selecione</option>';

          // Mapear as cores das salas - cores fixas para cada sala
          const coresSalas = {
            "Cabine 3ª andar": "#FF4500", // Laranja avermelhado
            "Cabine Térreo": "#8B4513", // Marrom
            "Sala de Reuniões 01": "#0071c5", // Azul
            "Sala de Reuniões 02": "#228B22", // Verde floresta
            "Sala de Reuniões 03": "#FFD700", // Dourado
          };

          // Percorrer a lista de salas
          for (var i = 0; i < resposta.dados.length; i++) {
            const sala = resposta.dados[i]["sala"];
            const cor = coresSalas[sala];
            console.log(`Processando sala: ${sala}, cor: ${cor}`);

            if (!cor) {
              console.warn(`Cor não encontrada para a sala: ${sala}`);
            }

            // Criar a lista de opções para o campo select salas com a cor definida
            opcoes += `<option value="${resposta.dados[i]["id"]}" data-color="${
              cor || "#CCCCCC"
            }">${sala}</option>`;
          }

          console.log("Opções de salas geradas:", opcoes);

          // Enviar as opções para o campo select no HTML
          cadUserId.innerHTML = opcoes;
        } else {
          console.error("Erro ao carregar salas:", resposta["msg"]);
          // Enviar a opção vazia para o campo select no HTML
          cadUserId.innerHTML = `<option value=''>${resposta["msg"]}</option>`;
        }

        // Chamar a função para converter a data selecionada para ISO8601 e enviar para o formulário
        document.getElementById("cad_start").value = converterData(info.start);
        document.getElementById("cad_end").value = converterData(info.start);

        console.log("Datas definidas:", {
          start: document.getElementById("cad_start").value,
          end: document.getElementById("cad_end").value,
        });

        // Abrir a janela modal cadastrar
        cadastrarModal.show();
      },
    });

    // Renderizar o calendário
    //calendar.render();

    // Retornar os dados do calendário
    return calendar;
  }

  // Chamar a função carregar eventos
  var calendar = carregarEventos();

  // Renderizar o calendário
  calendar.render();

  // Receber o seletor user_id do campo select
  var userId = document.getElementById("user_id");

  // Aguardar o usuário selecionar valor no campo selecionar usuário
  userId.addEventListener("change", function () {
    //console.log("Recuperar os eventos do usuário: " + userId.value);

    // Chamar a função carregar eventos
    calendar = carregarEventos();

    // Renderizar o calendário
    calendar.render();
  });

  // Converter a data
  function converterData(data) {
    // Converter a string em um objeto Date
    const dataObj = new Date(data);

    // Extrair o ano da data
    const ano = dataObj.getFullYear();

    // Obter o mês, mês começa de 0, padStart adiciona zeros à esquerda para garantir que o mês tenha dígitos
    const mes = String(dataObj.getMonth() + 1).padStart(2, "0");

    // Obter o dia do mês, padStart adiciona zeros à esquerda para garantir que o dia tenha dois dígitos
    const dia = String(dataObj.getDate()).padStart(2, "0");

    // Obter a hora, padStart adiciona zeros à esquerda para garantir que a hora tenha dois dígitos
    const hora = String(dataObj.getHours()).padStart(2, "0");

    // Obter minuto, padStart adiciona zeros à esquerda para garantir que o minuto tenha dois dígitos
    const minuto = String(dataObj.getMinutes()).padStart(2, "0");

    // Retornar a data
    return `${ano}-${mes}-${dia} ${hora}:${minuto}`;
  }

  // Receber o SELETOR do formulário cadastrar evento
  const formCadEvento = document.getElementById("formCadEvento");

  // Receber o SELETOR da mensagem genérica
  const msg = document.getElementById("msg");

  // Receber o SELETOR da mensagem cadastrar evento
  const msgCadEvento = document.getElementById("msgCadEvento");

  // Receber o SELETOR do botão da janela modal cadastrar evento
  const btnCadEvento = document.getElementById("btnCadEvento");

  // Somente acessa o IF quando existir o SELETOR "formCadEvento"
  if (formCadEvento) {
    // Aguardar o usuario clicar no botao cadastrar
    formCadEvento.addEventListener("submit", async (e) => {
      // Não permitir a atualização da pagina
      e.preventDefault();

      console.log("Iniciando cadastro de evento...");

      // Apresentar no botão o texto salvando
      btnCadEvento.textContent = "Salvando...";

      // Receber os dados do formulário
      const dadosForm = new FormData(formCadEvento);

      // Pegar a cor da sala selecionada
      const salaSelect = document.getElementById("cad_user_id");
      console.log("Select de sala:", salaSelect);
      console.log("Valor selecionado:", salaSelect.value);

      if (!salaSelect.value) {
        msgCadEvento.innerHTML = `<div class="alert alert-danger" role="alert">Por favor, selecione uma sala.</div>`;
        btnCadEvento.textContent = "Cadastrar";
        return;
      }

      const salaOption = salaSelect.options[salaSelect.selectedIndex];
      const salaColor = salaOption.getAttribute("data-color");
      console.log("Cor obtida automaticamente:", salaColor);

      if (!salaColor) {
        console.error("Cor não encontrada para a sala:", salaOption.text);
        msgCadEvento.innerHTML = `<div class="alert alert-danger" role="alert">Erro ao obter a cor da sala. Por favor, tente novamente.</div>`;
        btnCadEvento.textContent = "Cadastrar";
        return;
      }

      // Adicionar a cor ao FormData automaticamente
      dadosForm.set("cad_color", salaColor);

      // Log dos dados que serão enviados
      console.log("Dados completos do formulário:", {
        title: dadosForm.get("cad_title"),
        resp: dadosForm.get("cad_resp"),
        start: dadosForm.get("cad_start"),
        end: dadosForm.get("cad_end"),
        user_id: dadosForm.get("cad_user_id"),
        color: dadosForm.get("cad_color"),
      });

      try {
        // Chamar o arquivo PHP responsável em salvar o evento
        const dados = await fetch("cadastrar_evento.php", {
          method: "POST",
          body: dadosForm,
        });

        // Realizar a leitura dos dados retornados pelo PHP
        const resposta = await dados.json();
        console.log("Resposta do servidor:", resposta);

        if (!resposta["status"]) {
          console.error("Erro ao cadastrar:", resposta["msg"]);
          msgCadEvento.innerHTML = `<div class="alert alert-danger" role="alert">${resposta["msg"]}</div>`;
        } else {
          console.log("Evento cadastrado com sucesso!");
          msg.innerHTML = `<div class="alert alert-success" role="alert">${resposta["msg"]}</div>`;
          msgCadEvento.innerHTML = "";
          formCadEvento.reset();

          // Receber o id da sala do campo Select
          var user_id = document.getElementById("user_id").value;

          // Verificar se existe a pesquisa pelo sala, se o cadastro for para o mesma sala pesquisada, acrescenta no FullCalendar
          if (user_id == "" || resposta["user_id"] == user_id) {
            // Criar o objeto com os dados do evento
            const novoEvento = {
              id: resposta["id"],
              title: resposta["title"],
              color: salaColor, // Usar a cor definida automaticamente
              start: resposta["start"],
              end: resposta["end"],
              resp: resposta["resp"],
              user_id: resposta["user_id"],
              sala: resposta["sala"],
            };

            console.log("Adicionando evento ao calendário:", novoEvento);
            // Adicionar o evento ao calendário
            calendar.addEvent(novoEvento);
          }

          // Chamar a função para remover a mensagem após 3 segundo
          removerMsg();

          // Fechar a janela modal
          cadastrarModal.hide();
        }
      } catch (erro) {
        console.error("Erro ao processar cadastro:", erro);
        msgCadEvento.innerHTML = `<div class="alert alert-danger" role="alert">Erro ao processar cadastro. Por favor, tente novamente.</div>`;
      }

      // Apresentar no botão o texto Cadastrar
      btnCadEvento.textContent = "Cadastrar";
    });
  }

  // Função para remover a mensagem após 3 segundo
  function removerMsg() {
    setTimeout(() => {
      document.getElementById("msg").innerHTML = "";
    }, 3000);
  }

  // Receber o SELETOR ocultar detalhes do evento e apresentar o formulário editar evento
  const btnViewEditEvento = document.getElementById("btnViewEditEvento");

  // Somente acessa o IF quando existir o SELETOR "btnViewEditEvento"
  if (btnViewEditEvento) {
    // Aguardar o usuario clicar no botao editar
    btnViewEditEvento.addEventListener("click", async () => {
      // Ocultar os detalhes do evento
      document.getElementById("visualizarEvento").style.display = "none";
      document.getElementById("visualizarModalLabel").style.display = "none";

      // Apresentar o formulário editar do evento
      document.getElementById("editarEvento").style.display = "block";
      document.getElementById("editarModalLabel").style.display = "block";

      // Receber o id do usuário responsável pelo evento
      var userId = document.getElementById("visualizar_user_id").innerText;

      // Receber o SELETOR do campo usuário do formulário editar
      var editUserId = document.getElementById("edit_user_id");

      // Chamar o arquivo PHP responsável em recuperar as salas do banco de dados
      const dados = await fetch("listar_salas.php");

      // Ler os dados
      const resposta = await dados.json();
      //console.log(resposta);

      // Acessar o IF quando encontrar sala no banco de dados
      if (resposta["status"]) {
        // Criar a opção selecione para o campo select usuários
        var opcoes = '<option value="">Selecione</option>';

        // Mapear as cores das salas
        const coresSalas = {
          "Cabine 3ª andar": "#FF4500",
          "Cabine Térreo": "#8B4513",
          "Sala de Reuniões 01": "#0071c5",
          "Sala de Reuniões 02": "#228B22",
          "Sala de Reuniões 03": "#FFD700",
        };

        // Percorrer a lista de salas
        for (var i = 0; i < resposta.dados.length; i++) {
          const sala = resposta.dados[i]["sala"];
          const cor = coresSalas[sala] || "#CCCCCC"; // Cor padrão caso não encontre
          // Criar a lista de opções para o campo select salas
          opcoes += `<option value="${resposta.dados[i]["id"]}" data-color="${cor}">${sala}</option>`;
        }

        // Enviar as opções para o campo select no HTML
        editUserId.innerHTML = opcoes;

        // Selecionar a sala atual do evento
        editUserId.value = userId;
      } else {
        // Enviar a opção vazia para o campo select no HTML
        editUserId.innerHTML = `<option value=''>${resposta["msg"]}</option>`;
      }
    });
  }

  // Receber o SELETOR ocultar formulário editar evento e apresentar o detalhes do evento
  const btnViewEvento = document.getElementById("btnViewEvento");

  // Somente acessa o IF quando existir o SELETOR "btnViewEvento"
  if (btnViewEvento) {
    // Aguardar o usuario clicar no botao editar
    btnViewEvento.addEventListener("click", () => {
      // Apresentar os detalhes do evento
      document.getElementById("visualizarEvento").style.display = "block";
      document.getElementById("visualizarModalLabel").style.display = "block";

      // Ocultar o formulário editar do evento
      document.getElementById("editarEvento").style.display = "none";
      document.getElementById("editarModalLabel").style.display = "none";
    });
  }

  // Receber o SELETOR do formulário editar evento
  const formEditEvento = document.getElementById("formEditEvento");

  // Receber o SELETOR da mensagem editar evento
  const msgEditEvento = document.getElementById("msgEditEvento");

  // Receber o SELETOR do botão editar evento
  const btnEditEvento = document.getElementById("btnEditEvento");

  // Somente acessa o IF quando existir o SELETOR "formEditEvento"
  if (formEditEvento) {
    // Aguardar o usuario clicar no botao editar
    formEditEvento.addEventListener("submit", async (e) => {
      // Não permitir a atualização da pagina
      e.preventDefault();

      // Apresentar no botão o texto salvando
      btnEditEvento.value = "Salvando...";

      // Receber os dados do formulário
      const dadosForm = new FormData(formEditEvento);

      // Chamar o arquivo PHP responsável em editar o evento
      const dados = await fetch("editar_evento.php", {
        method: "POST",
        body: dadosForm,
      });

      // Realizar a leitura dos dados retornados pelo PHP
      const resposta = await dados.json();

      // Acessa o IF quando não editar com sucesso
      if (!resposta["status"]) {
        // Enviar a mensagem para o HTML
        msgEditEvento.innerHTML = `<div class="alert alert-danger" role="alert">${resposta["msg"]}</div>`;
      } else {
        // Enviar a mensagem para o HTML
        msg.innerHTML = `<div class="alert alert-success" role="alert">${resposta["msg"]}</div>`;

        // Enviar a mensagem para o HTML
        msgEditEvento.innerHTML = "";

        // Limpar o formulário
        formEditEvento.reset();

        // Recuperar o evento no FullCalendar pelo id
        const eventoExiste = calendar.getEventById(resposta["id"]);

        // Receber o id da sala do campo Select
        var user_id = document.getElementById("user_id").value;

        // Verificar se existe a pesquisa pela sala, se o editar for para a mesma sala pesquisada, manten no FullCalendar
        if (user_id == "" || resposta["user_id"] == user_id) {
          // Verificar se encontrou o evento no FullCalendar pelo id
          if (eventoExiste) {
            // Pegar a cor da sala selecionada
            const salaSelect = document.getElementById("edit_user_id");
            const salaOption = salaSelect.options[salaSelect.selectedIndex];
            const salaColor = salaOption.getAttribute("data-color");

            // Atualizar os atributos do evento com os novos valores do banco de dados
            eventoExiste.setProp("title", resposta["title"]);
            eventoExiste.setProp("color", salaColor); // Usar a cor da sala
            eventoExiste.setExtendedProp("resp", resposta["resp"]);
            eventoExiste.setExtendedProp("user_id", resposta["user_id"]);
            eventoExiste.setExtendedProp("sala", resposta["sala"]);
            eventoExiste.setStart(resposta["start"]);
            eventoExiste.setEnd(resposta["end"]);
          }
        } else {
          // Verificar se encontrou o evento no FullCalendar pelo id
          if (eventoExiste) {
            // Remover o evento do calendário
            eventoExiste.remove();
          }
        }

        // Chamar a função para remover a mensagem após 3 segundo
        removerMsg();

        // Fechar a janela modal
        visualizarModal.hide();
      }

      // Apresentar no botão o texto salvar
      btnEditEvento.value = "Salvar";
    });
  }

  // Receber o SELETOR apagar evento
  const btnApagarEvento = document.getElementById("btnApagarEvento");

  // Somente acessa o IF quando existir o SELETOR "formEditEvento"
  if (btnApagarEvento) {
    // Aguardar o usuario clicar no botao apagar
    btnApagarEvento.addEventListener("click", async () => {
      // Exibir uma caixa de diálogo de confirmação
      const confirmacao = window.confirm(
        "Deseja realmente apagar este evento?"
      );

      // Verificar se o usuário confirmou
      if (confirmacao) {
        // Receber o id do evento
        var idEvento = document.getElementById("visualizar_id").textContent;

        // Chamar o arquivo PHP responsável apagar o evento
        const dados = await fetch("excluir_evento.php?id=" + idEvento);

        // Realizar a leitura dos dados retornados pelo PHP
        const resposta = await dados.json();

        // Acessa o IF quando não cadastrar com sucesso
        if (!resposta["status"]) {
          // Enviar a mensagem para o HTML
          msgViewEvento.innerHTML = `<div class="alert alert-danger" role="alert">${resposta["msg"]}</div>`;
        } else {
          // Enviar a mensagem para o HTML
          msg.innerHTML = `<div class="alert alert-success" role="alert">${resposta["msg"]}</div>`;

          // Enviar a mensagem para o HTML
          msgViewEvento.innerHTML = "";

          // Recuperar o evento no FullCalendar
          const eventoExisteRemover = calendar.getEventById(idEvento);

          // Verificar se encontrou o evento no FullCalendar
          if (eventoExisteRemover) {
            // Remover o evento do calendário
            eventoExisteRemover.remove();
          }

          // Chamar a função para remover a mensagem após 3 segundo
          removerMsg();

          // Fechar a janela modal
          visualizarModal.hide();
        }
      }
    });
  }
});

// Receber o seletor do campo listar as salas
const user = document.getElementById("user_id");

// Verificar se existe o seletor user_id no HTML
if (user) {
  // Chamar a função
  listarUsuarios();
}

// Função para recuperar salas
async function listarUsuarios() {
  // Chamar o arquivo PHP para recuperar as salas
  const dados = await fetch("listar_salas.php");

  // Ler os dados retornado do PHP
  const resposta = await dados.json();
  //console.log(resposta);

  // Verificar se status é TRUE e acessa o IF, senão acessa o ELSE e retorna a mensagem de erro
  if (resposta["status"]) {
    // Criar a variável com as opções para o campo SELECT
    var opcoes = `<option value="">Selecionar ou limpar</option>`;

    // Percorrer o array de salas
    for (var i = 0; i < resposta.dados.length; i++) {
      // Atribuir o usuário como opção para o campo SELECT
      opcoes += `<option value="${resposta.dados[i]["id"]}">${resposta.dados[i]["sala"]}</option>`;
    }

    // Enviar para o HTML as opções para o campo SELECT
    user.innerHTML = opcoes;
  } else {
    // Enviar para o HTML as opções para o campo SELECT
    user.innerHTML = `<option value="">${resposta["msg"]}</option>`;
  }
}
