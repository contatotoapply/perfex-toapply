class Billet {
  disallowedBilletStatuses = ["paid", "canceled"];
  constructor() {
    this.loop = "";
    this.params = this._getParams();
    // this.url = "/v1/" + this.params[0] + "/details" + this.params[1];
    this.url = null;
    this.data = {};
    this.descontoCondicional = false;
    this.contLimitGetData = 5;
    this.contGetData = 0;
    this.pdfComCapa = 0;
    this.pdfSeparados = 0;
    this.printComCapa = false;
    this.divPrintCover = "";
    this.divPrintBillet = "";
    this.tipoCobranca = "";
    this.parcelaAtual = {};
    this.dadosIniciais = {};
    this.printAll = false;
    this.parcelaSelecionada = 0;
    this.hasPix = false;
  }
  _getParams() {
    const url = document.location.href;
    const params = url.split(/(?:\/v1\/|\?)+/).slice(1);
    params[1] = params[1] ? "?" + params[1] : "";
    return params;
  }
  _hasPrintCapa() {
    return $(document.body).hasClass("com-capa");
  }
  _getParamsForPrint() {
    let url = document.location.href;
    let paramsTemp = url.split("/v1/");
    let paramSplit = paramsTemp[1].split("/");
    if (this.tipoCobranca == "carne") {
      paramSplit = this.parcelaAtual.partialCobranca.urlImpressao.split("/");
      if (this.printAll) {
        return {
          boleto: [
            paramsTemp[0],
            "/emissao/",
            paramSplit[0],
            "/A4CL-",
            paramSplit[1],
          ].join(""),
          capa: [
            paramsTemp[0],
            "/emissao/",
            paramSplit[0],
            "/A4CX-",
            paramSplit[1],
          ].join(""),
        };
      }
      return {
        boleto: [
          paramsTemp[0],
          "/emissao/",
          paramSplit[0],
          "/A4CL-",
          paramSplit[1],
          "/",
          paramSplit[2],
        ].join(""),
        capa: [
          paramsTemp[0],
          "/emissao/",
          paramSplit[0],
          "/A4CX-",
          paramSplit[1],
          "/",
          paramSplit[2],
        ].join(""),
      };
    } else {
      return {
        boleto: [
          paramsTemp[0],
          "/emissao/",
          paramSplit[0],
          "/A4XB-",
          paramSplit[1],
        ].join(""),
        capa: [
          paramsTemp[0],
          "/emissao/",
          paramSplit[0],
          "/A4CX-",
          paramSplit[1],
        ].join(""),
        capaBoleto: [
          paramsTemp[0],
          "/emissao/",
          paramSplit[0],
          "/A4CB-",
          paramSplit[1],
        ].join(""),
      };
    }
  }
  _getUrlLocation() {
    let url = document.location.href;
    let paramsTemp = url.split("/v1/");
    let paramSplit = paramsTemp[1].split("/");
    return [paramsTemp[0], "/", paramSplit[0], "/", paramSplit[1]].join("");
  }
  _printCapaBillet() {
    var self = this;
    let urlTemp = this._getParamsForPrint();
    document.body.classList.add("print-start");
    $.get(urlTemp.capa)
      .done((data) => {
        if (data.indexOf("Página não encontrada") > 0) {
          setTimeout(function () {
            self._printCapaBillet();
          }, 1500);
          return;
        }
        this.divPrintCover = data;
        document.body.classList.remove("print-start");
        $('[data-modal="print"]').modalClose();
        self.newPrint();
      })
      .fail(function (err) {
        setTimeout(function () {
          self._printCapaBillet();
        }, 1500);
      });
  }
  printBillet() {
    var self = this;
    let urlTemp = this._getParamsForPrint();
    this._handleLoading(true);
    $.get(urlTemp.boleto)
      .done((data) => {
        if (data.indexOf("Página não encontrada") > 0) {
          setTimeout(function () {
            self.printBillet();
          }, 1500);
          return;
        }
        this.divPrintBillet = data;
        if (this.printComCapa) {
          self._printCapaBillet();
          this._handleLoading(false);
          return;
        }
        self.newPrint();
        this._handleLoading(false);
      })
      .fail(function (err) {
        setTimeout(function () {
          self.printBillet();
          this._handleLoading(false);
        }, 1500);
      });
  }
  newTemplatePrint() {
    if (!this.printComCapa) {
      this.divPrintCover = "";
    }
    let html = "";
    html +=
      "<style>@media screen {body{overflow: hidden} .capa {margin-top: 2000px}} @page {size: A4 portrait;margin: 0;padding: 0;}</style>";
    html += "<div class='capa print-only'>" + this.divPrintCover + "</div>";
    html +=
      "<div class='folha-boleto print-only'>" + this.divPrintBillet + "</div>";
    return html;
  }
  newPrint() {
    let template = this.newTemplatePrint();
    let win = window.open("", "_blank");
    win.document.write(template);
    setTimeout(() => {
      win.focus();
      win.stop();
      win.print();
      win.focus();
      win.close();
    }, 800);
    this.printAll = false;
  }
  viewBillet() {
    let uri = document.location.href.split("/v1/")[1];
    let params = uri.split("/");
    if (this.dadosIniciais.tipoCobranca == "carne") {
      params = this.parcelaAtual.partialCobranca.urlImpressao.split("/");
      window.open(
        `/emissao/${params[0]}/A4CL-${params[1]}/${params[2]}`,
        "blank"
      );
      return;
    }
    window.open(`/emissao/${params[0]}/A4XB-${params[1]}`, "blank");
  }
  getUrlDownloadPdf() {
    let uri = document.location.href.split("/v1/")[0];
    let downloadUrl = "";
    if (
      this.dadosIniciais &&
      this.dadosIniciais.tipoCobranca == "carne" &&
      !billet.printAll
    ) {
      downloadUrl = `${uri}/${this.parcelaAtual.partialCobranca.urlImpressao}`;
      return [downloadUrl, ".pdf"].join("");
    }
    downloadUrl = document.location.href.replace("/v1", "");
    return [downloadUrl, ".pdf"].join("");
  }
  verifyPdf() {
    document.body.classList.add("download-start");
    let self = this;
    let urlTemp = this.getUrlDownloadPdf();
    let urlPdf = this.pdfComCapa
      ? urlTemp.verifyPdfComCapa
      : urlTemp.verifyPdfSemCapa;
    $.get(urlPdf)
      .done((data) => {
        if (data.ready) {
          if (data.cover_size) {
            this.pdfSeparados = true;
          } else {
            this.pdfSeparados = false;
          }
          document.body.classList.remove("download-start");
        } else {
          setTimeout(function () {
            self.verifyPdf();
          }, 2000);
        }
      })
      .fail(() => {
        setTimeout(function () {
          self.verifyPdf();
        }, 2000);
      });
  }
  _temDescontoCondicional(data, verifica = false) {
    if (data.partialCobranca.inst1) {
      if (data.tipoCobranca == "boleto") {
        var moneyAndDate = this._getMoneyAndDate(data.partialCobranca.inst1);
      } else {
        var moneyAndDate = this._getMoneyAndDate(
          data.moneyAndDate.valor + " " + data.moneyAndDate.data
        );
      }
    } else if (data.tipoCobranca === "carne") {
      var moneyAndDate = data.moneyAndDate;
    }
    if (moneyAndDate && moneyAndDate.data) {
      var currentDate = new Date();
      currentDate.setHours(0, 0, 0, 0);
      var partesData = moneyAndDate.data.split("/");
      var dataDesconto = new Date(
        partesData[2],
        partesData[1] - 1,
        partesData[0]
      );
      dataDesconto.setHours(0, 0, 0, 0);
      if (dataDesconto < currentDate) {
        return;
      }
      let valoresSubtraidos;
      if (data.tipoCobranca == "boleto") {
        valoresSubtraidos =
          parseFloat(
            data.partialCobranca.valorDocumento
              .replace(".", "")
              .replace(",", ".")
          ) - parseFloat(moneyAndDate.valor.replace(",", "."));
      } else if (data.tipoCobranca === "carne") {
        valoresSubtraidos =
          parseFloat(
            data.partialCobranca.valorDocumento[0]
              .replace(".", "")
              .replace(",", ".")
          ) -
          parseFloat(moneyAndDate.valor.replace(",", ".").replace("R$ ", ""));
      } else {
        valoresSubtraidos =
          parseFloat(
            data.partialCobranca.valorDocumento[0]
              .replace(".", "")
              .replace(",", ".")
          ) - parseFloat(moneyAndDate.valor.replace(",", "."));
      }
      let valorCondicional = parseFloat(
        valoresSubtraidos.toFixed(2)
      ).toLocaleString("pt-BR", { currency: "BRL", minimumFractionDigits: 2 });
      let html =
        '<span>Pague <span class="orange-1"><strong>R$ ' +
        valorCondicional +
        "</span></strong> até dia <strong>" +
        moneyAndDate.data +
        "<strong></span>";
      $(".partialCobranca .desconto").html(html).show();
      this.descontoCondicional = true;
    }
  }
  _linkSegundaVia(string) {
    let strSearch = "https://gerencianet.com.br/segunda-via";
    if (string.indexOf(strSearch)) {
      let strReplace =
        "<a href='" +
        strSearch +
        "' target='_blank' rel='noopener'>" +
        strSearch +
        "</a>";
      return string.replace(strSearch, strReplace);
    }
    return false;
  }
  _partialObservacao(data) {
    let html =
      data.partialObservacao && data.partialObservacao.mensagem
        ? data.partialObservacao.mensagem
        : false;
    if (html) {
      $(".partialObservacao")
        .append('<li><div class="col-sm-12">' + html + "</div></li>")
        .show();
    }
  }
  _partialMultaJuros(data) {
    let html1 = data.partialCobranca.inst1;
    let html2 = data.partialCobranca.inst2;
    let html3 = data.partialCobranca.inst3;
    if ((html1 && !this.descontoCondicional) || data.tipoCobranca == "carne") {
      $(".partialMultaJuros")
        .append('<li class="fs-14 item"><p>' + html1 + "</p></li>")
        .show();
    }
    if (html2) {
      $(".partialMultaJuros")
        .append('<li class="fs-14 item"><p>' + html2 + "</p></li>")
        .show();
    }
    if (html3) {
      $(".partialMultaJuros")
        .append('<li class="fs-14 item"><p>' + html3 + "</p></li>")
        .show();
    }
    if (
      html1 &&
      html1.length == 0 &&
      html2 &&
      html2.length &&
      html3 &&
      html3.length
    ) {
      $(".partialMultaJuros").hide();
    }
  }
  _billetData(data) {
    let imgBanco = data.partialCobranca.imagemBanco;
    let imgBancoSrc = '<img src="' + imgBanco + '" alt="Nome Banco">';
    $(".boleto-dados .img-banco").html(imgBancoSrc);
    $(".boleto-dados .boleto-codigo").html(
      '<span class="bar-code-number">' +
        data.partialCobranca.digitavel +
        "</span>"
    );
    let imgBoleto = data.partialCobranca.linkCodigoBarras;
    let barCode =
      '<img src="' + imgBoleto + '" alt="código de barras" class="no-mobile">';
    $(".barcode").html(barCode);
    this._showPixTab(data);
  }
  _billetDescriptions(data) {
    let html = "";
    let valorFrete = "";
    let header = $("#demonstrativo .ticket-list .list-header");
    $("#demonstrativo .ticket-list").html(html).append(header);
    data.partialDemonstrativo.itens.forEach((item) => {
      if (item.descricao === "Frete") {
        valorFrete = item.valor;
      } else {
        var preco = item.preco;
        var valor = item.valor;
        if (data.tipoCobranca === "carne") {
          var preco = item.valor;
          var valor = item.preco;
        }
        html +=
          '<li class="no-mobile">' +
          '<div class="item-desc col-sm-6 value">' +
          item.descricao +
          "</div>" +
          '<div class="item-subtotal col-sm-3 value text-right">' +
          preco +
          "</div>" +
          '<div class="item-qty col-sm-1 value text-right">' +
          item.qtde +
          '<span class="yes-mobile-inline">x</span></div>' +
          '<div class="item-unit col-sm-2 value text-right">' +
          valor +
          "</div>" +
          "</li>" +
          '<li class="yes-mobile">' +
          '<div class="col-sm-6">' +
          item.descricao +
          "<br/> " +
          preco +
          " x " +
          item.qtde +
          '<span class-"label yes-mobile"> / Subtotal </span><strong>' +
          valor +
          "</strong></div>" +
          "</li>";
      }
    });
    $("#demonstrativo .ticket-list").append(html);
    this._billetDescriptionsFooter(data, valorFrete);
  }
  _getMoneyAndDate(string) {
    let returnString = {};
    if (!string) {
      return returnString;
    }
    let valorRegex = /[0-9]\d{0,2}(?:\.\d{3})*,\d{2}/;
    let dataRegex = /\d{2}\/\d{2}\/\d{4}/;
    if (string.search(valorRegex) > 0 && string.search(dataRegex) > 0) {
      let money = string.match(valorRegex);
      let data = string.match(dataRegex);
      Object.assign(returnString, { valor: money[0], data: data[0] });
      return returnString;
    }
    return false;
  }
  _billetDescriptionsFooter(data, frete) {
    let valorFrete = frete ? frete : null;
    let htmlFrete = valorFrete
      ? data.tipoCobranca == "boleto"
        ? '<li><div class="col-sm-6 label">Frete</div><div class="col-sm-6 text-right">' +
          valorFrete +
          "</div></li>"
        : ""
      : "";
    let valorDesconto = data.partialDemonstrativo.desconto
      ? data.partialDemonstrativo.desconto.valor
      : null;
    let moneyAndDate = data.partialCobranca.inst1
      ? this._getMoneyAndDate(data.partialCobranca.inst1)
      : "";
    let descontoMobile = moneyAndDate
      ? 'Desconto de <span class="orange-1">' +
        moneyAndDate.valor +
        '</span> para pagamento até <span class="orange-1">' +
        moneyAndDate.data +
        "</span>"
      : "";
    let valorTotal;
    if (data.tipoCobranca == "carne") {
      valorTotal =
        '<li><div class="col-sm-6 label">Valor total</div><div class="col-sm-6 text-right total">R$ ' +
        data.partialCobranca.valorTotalParcelado +
        '<br><div class="valor-parcelas">Parcelado em <strong>' +
        data.partialCobranca.qtdeParcelas +
        "x</strong> de R$ <strong>" +
        data.partialCobranca.valorDocumento;
      +"</strong></div>";
    } else {
      valorTotal =
        '<li><div class="col-sm-6 label">Valor total</div><div class="col-sm-6 text-right total">R$ ' +
        data.partialCobranca.valorDocumento;
    }
    let htmlDesconto =
      valorDesconto !== "0,00" && valorDesconto !== null
        ? "<li>" +
          '<div class="col-sm-6 label">Desconto</div><div class="col-sm-6 text-right">' +
          valorDesconto +
          "</div></li>"
        : "";
    let html = htmlFrete + htmlDesconto + valorTotal + "</div></li>";
    $(".billet-values").html("");
    $(".billet-values").prepend(html);
  }
  _escapeHtml(text) {
    var map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
      " ": "%20;",
    };
    return text.replace(/[&<>"']/g, function (m) {
      return map[m];
    });
  }
  _partialCobranca(data) {
    let params = this._getParams();
    this.baseKey = params[0].split("/")[1];
  }
  _partialStatus(data) {
    $(".status-description").html(
      '<i class="' +
        this._getStatusIcon(data.partialCobranca.status) +
        '"></i> ' +
        this._translateBilletStatus(data.partialCobranca.status) +
        "</div>"
    );
    $(".status-description")
      .removeClass()
      .addClass("status-description " + data.partialCobranca.status);
    $(".details-list")
      .removeClass()
      .addClass("details-list " + data.partialCobranca.status);
    if (
      data.partialCobranca.status === "canceled" ||
      data.partialCobranca.status === "paid" ||
      data.partialCobranca.status === "expired" ||
      data.partialCobranca.status === "refunded" ||
      data.partialCobranca.status === "contested"
    ) {
      $(".payment-options button").each(function (tab) {
        $(this).removeClass("active");
        $(this).attr("disabled");
      });
      $(".tab-content").hide();
      $(".bar-codes")
        .removeClass()
        .addClass(
          "container billet-details bar-codes " + data.partialCobranca.status
        );
      if (data.partialCobranca.status === "canceled") {
        $("#imprimir").attr("disabled", true);
        $(".dropdown-imprimir").attr("disabled", true);
        $("#download").attr("disabled", true);
        $(".dropdown-download").attr("disabled", true);
      } else {
        this._activeDownloadImprimir();
      }
    } else {
      $(".bar-codes")
        .removeClass()
        .addClass(
          "container billet-details bar-codes " + data.partialCobranca.status
        );
      $(".tab-content").show();
      $("#codigoBarras").show();
      this._resetTabs();
      this._activeDownloadImprimir();
    }
    let abaUnica = " ";
    if ($(".billet-details.payment-options").hasClass("aba-unica")) {
      abaUnica = "aba-unica ";
    }
    $(".billet-details.payment-options")
      .removeClass()
      .addClass(
        "billet-details payment-options " +
          abaUnica +
          data.partialCobranca.status
      );
  }
  _activeDownloadImprimir = function () {
    $("#imprimir").attr("disabled", false);
    $(".dropdown-imprimir").attr("disabled", false);
    $("#download").attr("disabled", false);
    $(".dropdown-download").attr("disabled", false);
  };
  shareMessage() {
    $.get("/share/messages/" + this.baseKey).done(function (response) {
      var message = encodeURI(response.message);
      var url = "https://api.whatsapp.com/send" + "?text=" + message;
      window.open(url, "_blank");
    });
  }
  _partialLogo(data) {
    let logoImagem = data.partialLogo.imagem;
    if (logoImagem) {
      let imgLogoImagem =
        '<img src="' +
        logoImagem +
        '" alt="' +
        data.partialDadosLojista.nomeLojista +
        '" />';
      $(".container-img").html(imgLogoImagem);
    } else {
      $(".container-img")
        .html("<span>" + data.partialDadosLojista.nomeLojista + "</span>")
        .addClass("name");
    }
  }
  _partialRecebedorEmissor(data) {
    let dadosRecebedor = '<div class="col-sm-12">';
    dadosRecebedor += data.partialDadosLojista.nomeLojista;
    dadosRecebedor +=
      " | " +
      data.partialDadosLojista.cpfCnpj +
      "</div>" +
      '<div class="col-sm-12">';
    if (data.partialDadosLojista.email.length > 0) {
      dadosRecebedor += data.partialDadosLojista.email;
    }
    if (data.partialDadosLojista.telefone) {
      dadosRecebedor += " | telefone: " + data.partialDadosLojista.telefone;
    }
    dadosRecebedor += "</div>";
    $(".partialDadosLojista .dadosRecebedor").html(dadosRecebedor);
    if (data.partialDadosLojista.email) {
      $(".partialDadosLojista .name").html(
        data.partialDadosLojista.nomeLojista +
          "<br/><span>" +
          data.partialDadosLojista.email +
          "</span>"
      );
    } else {
      $(".partialDadosLojista .name").html(
        data.partialDadosLojista.nomeLojista
      );
    }
    if (data.partialDadosLojista.endereco) {
      let lojistaLogradouro =
        '<div class="col-sm-8">' +
        data.partialDadosLojista.endereco +
        " - CEP: " +
        data.partialDadosLojista.cep +
        "</div>";
      $(".partialDadosLojistaEndereco .logradouro").html(lojistaLogradouro);
    } else {
      $(".partialDadosLojistaEndereco").css("display", "none");
    }
  }
  toTitleCase(str) {
    return str.replace(/(?:^|\s)\w/g, function (match) {
      return match.toUpperCase();
    });
  }
  _setViewBilletUrl() {
    let uri = document.location.href.split("/v1/")[1];
    let params = uri.split("/");
    if (this.dadosIniciais.tipoCobranca == "carne") {
      params = this.parcelaAtual.partialCobranca.urlImpressao.split("/");
      $("#viewBillet").attr(
        "href",
        `/emissao/${params[0]}/A4CL-${params[1]}/${params[2]}`
      );
      return;
    }
    $("#viewBillet").attr("href", `/emissao/${params[0]}/A4XB-${params[1]}`);
  }
  _populate(data) {
    if (data.tipoCobranca == "carne") {
      data = this._carneParser(data);
    } else {
      $(".parcelas").html("");
    }
    this._setViewBilletUrl();
    this._partialLogo(data);
    $(".partialCobranca .valorTotal").html(
      "R$  " + data.partialCobranca.valorDocumento
    );
    $(".partialCobranca .billet-number").html(data.partialCobranca.cobranca);
    $("#demonstrativo .billet-number").html(data.partialCobranca.cobranca);
    this._temDescontoCondicional(data);
    $(".partialDadosCliente .dadosPagador").html(
      '<div class="col-sm-8">' +
        data.partialDadosCliente.nome +
        " | " +
        data.partialDadosCliente.cpfCnpj +
        "</div>"
    );
    $(".partialDadosCliente .name").html(data.partialDadosCliente.nome);
    this._partialRecebedorEmissor(data);
    $(".partialCobranca .vencimento").html(data.partialCobranca.vencimento);
    this._billetData(data);
    this._billetDescriptions(data);
    this._partialObservacao(data);
    this._partialMultaJuros(data);
    this._partialCobranca(data);
    this._partialStatus(data);
    $(".loading-content").fadeIn(800);
    $(".loading-content-ico").fadeOut(800);
    $(document.body).removeClass("body-loading");
    if (
      data.partialCobranca.status !== "canceled" &&
      data.partialCobranca.status !== "paid"
    ) {
      this._tabsActivate();
    }
    this._setMaxHeight();
  }
  _tabsActivate = function () {
    $(".tab-content .tab-pane").each(function (tab) {
      if (tab > 0) {
        $(this).hide();
      }
    });
    $("#paymentTabs button").click(function (e) {
      e.preventDefault();
      $(".tab-content .tab-pane").each(function (tab) {
        $(this).hide();
      });
      $(".payment-options button").each(function (tab) {
        $(this).removeClass("active");
        $(this).attr("aria-selected", false);
      });
      $($(this).data("tab")).show();
      $(this).addClass("active");
      $(this).attr("aria-selected", true);
      $($(this).data("tab")).focus();
    });
  };
  _resetTabs = function () {
    $(".payment-options button").each(function () {
      $(this).removeClass("active");
      $(this).attr("aria-selected", false);
    });
    if (this.hasPix) {
      $("#qrCodeTab").addClass("active");
      $("#qrCodeTab").attr("aria-selected", true);
      $("#codigoBarras").hide();
      setTimeout(function () {
        $("#qrCodeContent").show();
      }, 200);
    } else {
      $("#codigoBarras").show();
      $("#qrCodeContent").hide();
      $("#barCodeTab").addClass("active");
      $("#barCodeTab").attr("aria-selected", true);
    }
  };
  _redirectError() {
    let urlLocation = this._getUrlLocation();
    window.location = urlLocation;
  }
  _redirectDataError() {
    let urlLocation =
      window.location.href.split("v1")[0] + "cobranca_nao_encontrada.html";
    window.location = urlLocation;
  }
  _againGetData() {
    var self = this;
    if (self.contGetData <= self.contLimitGetData) {
      setTimeout(function () {
        self.getData();
      }, 2000);
      self.contGetData++;
    } else {
      self._redirectError();
    }
  }
  isBilletDisallowed() {
    var billetStatus = this.data.partialCobranca.status;
    return this.disallowedBilletStatuses.indexOf(billetStatus) !== -1;
  }
  getData() {
    var self = this;
    if (this.params && this.url) {
      return $.when($.get(this.url))
        .done((data) => {
          if (data) {
            this.data = data;
            if (Object.keys(data).length > 0) {
              this._populate(data);
            } else {
              self._redirectDataError();
              return;
            }
          }
        })
        .fail(function (err) {
          if (err.status == 404 || err.status == 500) {
            self._redirectError();
            return;
          }
          self._againGetData();
        });
    } else {
      throw "Paramenter not found";
    }
  }
  _translateBilletStatus = function (status) {
    let statuses = {
      waiting: "Aguardando pagamento",
      settled: "Aguardando pagamento",
      nextdue: "Próximo vencimento",
      paid: "Pago",
      refunded: "Devolvido",
      canceled: "Cancelado",
      unpaid: "Inadimplente",
      contested: "Contestado",
      expired: "Expirado",
      identified: "Aguardando pagamento",
    };
    return statuses[status];
  };
  _getStatusIcon = function (status) {
    let statuses = {
      waiting: "icon-clock-at-4 waiting",
      settled: "icon-clock-at-4 settled",
      nextdue: "icon-clock-at-4 nextdue",
      paid: "icon-check-circle-light paid",
      refunded: "icon-minus-circle refunded",
      canceled: "icon-times-circle-light canceled",
      unpaid: "icon-exclamation-circle unpaid",
      contested: "icon-exclamation-circle contested",
      expired: "icon-times-circle-light expired",
      identified: "icon-clock-at-4 identified",
    };
    return statuses[status];
  };
  _setMaxHeight = function () {
    let elem;
    let el = $(".section-2.bill-details");
    elem = el.clone().css({ height: "auto", width: "auto" }).appendTo("body");
    let height = Number(elem.css("height").split("px")[0]) * 1.7 + "px";
    elem.remove();
    var style = $(
      "<style>.section-2.bill-details.active { max-height: " +
        height +
        " }</style>"
    );
    $("html > head").append(style);
  };
  _valoresParcela = function (data, carne, parcela) {
    carne.partialCobranca.valorTotalParcelado = 0;
    let valorRegex = /[0-9]\d{0,2}(?:\.\d{3})*,\d{2}/;
    for (let i = 0; i < data.installments.length; i++) {
      let money = data.installments[parcela].value.match(valorRegex);
      carne.partialCobranca.valorTotalParcelado += parseFloat(
        money[0].replace(".", "").replace(",", ".")
      );
    }
    carne.partialCobranca.valorTotalParcelado = parseFloat(
      carne.partialCobranca.valorTotalParcelado.toFixed(2)
    ).toLocaleString("pt-BR", { currency: "BRL", minimumFractionDigits: 2 });
    carne.partialCobranca.valorDocumento =
      data.installments[parcela].value.match(valorRegex);
    carne.partialCobranca.qtdeParcelas = data.installments.length;
    carne.partialCobranca.digitavel = data.installments[parcela].number_code;
    carne.partialCobranca.nossoNumero = data.installments[parcela].our_number;
    carne.partialCobranca.vencimento = data.installments[parcela].due_date;
    carne.partialCobranca.status = data.installments[parcela].status;
    carne.partialCobranca.cobranca = data.installments[parcela].billet_number;
    carne.partialCobranca.linkCodigoBarras = data.installments[parcela].barcode;
    carne.partialCobranca.urlImpressao =
      data.installments[parcela].installmentActive;
    carne.partialCobranca.imagemQrcode = data.installments[parcela].qrcodeImage;
    carne.partialCobranca.brcode = data.installments[parcela].brcode;
    if (data.installments[parcela].discount_value) {
      carne.moneyAndDate = {
        data: data.installments[parcela].discount_due_date,
        valor: data.installments[parcela].discount_value,
      };
    }
    this._showPixTab(carne);
  };
  _showPixTab = function (data) {
    if (
      data.partialCobranca.brcode == undefined ||
      data.partialCobranca.brcode == ""
    ) {
      $("#abaPix").hide();
      $("#abaBoleto").addClass("aba-unica");
      $(".payment-options").addClass("aba-unica");
    } else {
      this.hasPix = true;
      $("#pix-codigo").html(data.partialCobranca.brcode);
      $("#qr-code-image").html(data.partialCobranca.imagemQrcode);
      $("#abaPix").show();
      $("#abaBoleto").removeClass("aba-unica");
      $(".payment-options").removeClass("aba-unica");
      this._resetTabs();
    }
  };
  mudaParcela = function (parcela) {
    this.parcelaSelecionada = parcela;
    parcela--;
    this._handleLoading(true);
    this._valoresParcela(this.dadosIniciais, this.parcelaAtual, parcela);
    this._billetDescriptions(this.parcelaAtual);
    this._partialStatus(this.parcelaAtual);
    this._temDescontoCondicional(this.parcelaAtual);
    $(".partialCobranca .vencimento").html(
      this.parcelaAtual.partialCobranca.vencimento
    );
    $(".boleto-dados .boleto-codigo").html(
      '<span class="bar-code-number">' +
        this.parcelaAtual.partialCobranca.digitavel +
        "</span>"
    );
    $(".boleto-dados .barcode-container .barcode").html(
      '<img src="' +
        this.parcelaAtual.partialCobranca.linkCodigoBarras +
        '" alt="código de barras" class="no-mobile">'
    );
    $(".partialCobranca .billet-number").html(
      this.parcelaAtual.partialCobranca.cobranca
    );
    $(".parcela-selecionada").text(++parcela);
    this._handleLoading(false);
    if (
      this.parcelaAtual.status !== "canceled" &&
      this.parcelaAtual.status !== "paid"
    ) {
      this._tabsActivate();
    }
  };
  _handleLoading = function (show) {
    if (show) {
      $(".loading-content-ico").addClass("loading");
    } else {
      setTimeout(function () {
        $(".loading-content-ico").removeClass("loading");
        $(".parcela-atual ul").removeClass("active");
      }, 500);
    }
  };
  _botaoParcelas = function () {
    let parcelas = "";
    for (let i = 0; i < this.parcelaAtual.partialCobranca.qtdeParcelas; i++) {
      parcelas +=
        '<li><button title="' +
        this._translateBilletStatus(this.dadosIniciais.installments[i].status) +
        '" onclick=billet.mudaParcela(' +
        (i + 1) +
        ')><i  class="' +
        this._getStatusIcon(this.dadosIniciais.installments[i].status) +
        '"></i><strong>Parcela&nbsp;</strong><strong>' +
        (i + 1) +
        "&nbsp</strong>(" +
        this.dadosIniciais.installments[i].value +
        ")</button></li>";
    }
    $(".parcela-atual").on("click", function (e) {
      $(this).attr("aria-expanded", !!$(this).attr("aria-expanded"));
      $(".dropdown-imprimir")
        .parent()
        .find(".submenu.active")
        .removeClass("active");
      $(".dropdown-download")
        .parent()
        .find(".submenu.active")
        .removeClass("active");
      $(".parcela-atual ul").toggleClass("active");
      e.stopImmediatePropagation();
      billet._overlayer();
    });
    $(".parcela-atual ul").html(parcelas);
  };
  _overlayer = function () {
    $("body")
      .append(
        '<div class="dropdown-overlayer" id="dropdown-overlayer" style="' +
          "width: 100vw;" +
          "height: 100vh;" +
          "opacity: 0;" +
          "position: absolute;" +
          "display: none;" +
          '"></div>'
      )
      .on("click keydown", function (e) {
        if (e.type == "keydown" && e.key != "Escape") {
          return;
        }
        $(".submenu.active").removeClass("active");
        $(".parcela-atual .active").removeClass("active");
        $(".dropdown-overlayer").remove();
        $("body").off("click keydown");
        $(".parcela-atual").attr("aria-expanded", false);
        $(".dropdown-imprimir").attr("aria-expanded", false);
        $(".dropdown-download").attr("aria-expanded", false);
      });
  };
  _botaoImprimir = function () {
    let download = $("#download");
    let imprimir = $("#imprimir");
    $(
      '<div style="position: relative;">' +
        '<button class="dropdown-imprimir no-mobile" aria-label="mais opções para imprimir" aria-expanded="false">' +
        '<i class="icon-caret-down"></i></button><button class="submenu"' +
        ">Imprimir todas parcelas</button></div>"
    ).insertAfter(imprimir);
    $(
      '<div style="position: relative;">' +
        '<button class="dropdown-download" aria-label="mais opções para baixar" aria-expanded="false">' +
        '<i class="icon-caret-down"></i></button><button class="submenu"' +
        ">Baixar todas parcelas</button></div>"
    ).insertAfter(download);
    download.html(
      '<i class="icon d-block icon-download-alt"></i>Baixar <span class="no-mobile">&nbsp;parcela</span>'
    );
    imprimir.html(
      '<i class="icon d-block icon-print-alt"></i>Imprimir parcela'
    );
    $(".dropdown-imprimir").on("click", function (e) {
      $(".dropdown-download")
        .parent()
        .find(".submenu.active")
        .removeClass("active");
      $(".parcela-atual ul").removeClass("active");
      $(".submenu", $(this).parent()).toggleClass("active");
      $(this).attr("aria-expanded", !!$(this).attr("aria-expanded"));
      $(".dropdown-overlayer").show();
      e.stopImmediatePropagation();
      billet._overlayer();
      $(".submenu", $(this).parent()).on("click", function (e) {
        e.stopImmediatePropagation();
        billet.printAll = true;
        imprimir.trigger("click");
      });
    });
    $(".dropdown-download").on("click", function (e) {
      $(".dropdown-imprimir")
        .parent()
        .find(".submenu.active")
        .removeClass("active");
      $(".parcela-atual ul").removeClass("active");
      $(".submenu", $(this).parent()).toggleClass("active");
      $(this).attr("aria-expanded", !!$(this).attr("aria-expanded"));
      $(".dropdown-overlayer").show();
      e.stopImmediatePropagation();
      billet._overlayer();
      $(".submenu", $(this).parent()).off("click");
      $(".submenu", $(this).parent()).on("click", function (r) {
        e.stopImmediatePropagation();
        billet.printAll = true;
        download.trigger("click");
      });
    });
  };
  _carneParser = function (data) {
    this.dadosIniciais = data;
    var carne = {};
    carne.partialCobranca = {};
    carne.partialObservacao = {};
    carne.partialDadosCliente = {};
    carne.partialDadosLojista = {};
    carne.partialDemonstrativo = {};
    carne.partialLogo = {};
    let parcela = this._getParcela(data);
    this._valoresParcela(data, carne, parcela);
    carne.partialCobranca.imagemBanco = data.issuer.brand;
    carne.partialCobranca.inst1 = data.description.split("<br />")[0];
    carne.partialCobranca.inst2 = data.description.split("<br />")[1];
    carne.partialObservacao.mensagem = data.comments;
    carne.partialDadosCliente.cpfCnpj = data.payer.document;
    carne.partialDadosCliente.endereco = data.payer.addres;
    carne.partialDadosCliente.nome = data.payer.name;
    carne.partialDadosLojista.endereco = data.receiver.address;
    carne.partialDadosLojista.cep = data.receiver.postal_code;
    carne.partialDadosLojista.cpfCnpj = data.receiver.document;
    carne.partialDadosLojista.email = data.receiver.email;
    carne.partialDadosLojista.nomeLojista = data.receiver.name;
    carne.partialDadosLojista.telefone = data.receiver.phone_number;
    carne.partialDemonstrativo.desconto = { valor: data.discount };
    carne.partialDemonstrativo.itens = [];
    for (let i = 0; i < data.items.length; i++) {
      carne.partialDemonstrativo.itens.push({
        descricao: data.items[i].description,
        preco: data.items[i].price,
        qtde: data.items[i].qty,
        valor: data.items[i].unitary_price,
      });
    }
    carne.partialLogo.imagem = data.receiver.brand;
    this.tipoCobranca = carne.tipoCobranca = data.tipoCobranca;
    this.parcelaAtual = carne;
    $(".quantidade-parcelas").html(carne.partialCobranca.qtdeParcelas);
    $(".parcela-atual").html(
      'Parcela&nbsp;<span class="parcela-selecionada">' +
        Number(parcela + 1) +
        '<span class="sr-only">&nbsp;de&nbsp;' +
        carne.partialCobranca.qtdeParcelas +
        '</span></span><i class="icon-caret-down"></i><ul></ul>'
    );
    $("#downloadModalTitle").text("Baixando parcela");
    $("#impressaoBoleto").text("Parcela em capa");
    this._botaoParcelas();
    this._botaoImprimir();
    return carne;
  };
  _getParcela = function (data) {
    let params = document.location.href.split("/v1/")[1];
    for (let i = 0; data.installments.length; i++) {
      if (data.installments[i].installmentActive.includes(params)) {
        this.parcelaSelecionada = i + 1;
        return i;
      }
    }
    return 0;
  };
}
