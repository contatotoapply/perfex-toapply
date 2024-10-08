/* -------------------------------------------------------------------------- */
/*                               copiar Pix                                */
/* -------------------------------------------------------------------------- */
function copyPix() {
  let tmpField = document.createElement("textarea");
  $("#qr-copied").addClass("copied");
  setTimeout(function () {
    $("#qr-copied").removeClass("copied");
  }, 750);
  tmpField.value = document.getElementById("pix-codigo").innerText;
  document.body.append(tmpField);
  tmpField.select();
  document.execCommand("copy");
  tmpField.remove();
}

document.querySelector("#copyQrPix").addEventListener("click", copyPix);

/* -------------------------------------------------------------------------- */
/*                scroll para seção de demonstrativo no mobile                */
/* -------------------------------------------------------------------------- */
$(".mais-informacoes").on("click", function (e) {
  if ($(this).hasClass("active")) {
    $("html, body").animate(
      {
        scrollTop: $("#demonstrativo").offset().top,
      },
      700
    );
    scrollTop: $("#demonstrativo").focus();
  } else {
    $("html, body").animate(
      {
        scrollTop: $("#demonstrativo").offset().top - 200,
      },
      700
    );
    scrollTop: $(".mais-informacoes").focus();
  }
});

/* -------------------------------------------------------------------------- */
/*                                 copiar url                                 */
/* -------------------------------------------------------------------------- */
function copyUrl() {
  return new Promise((resolve, reject) => {
    //create
    try {
      const copy = document.createElement("textarea");
      document.body.appendChild(copy);
      copy.value = document.location.href;
      //copy
      copy.select();
      document.execCommand("copy");
      copy.setSelectionRange(0, 0, "none");
      //remove
      document.body.removeChild(copy);
      resolve("copied");
    } catch (err) {
      reject(`There\'s an error during copy: ${err}`);
    }
  });
}

function resetAnimation(res) {
  $("#copy-title").removeClass(res);
  setTimeout(() => {
    $("#copy-title").html("Copiar Link");
  }, 300);
}

document.querySelector("#copy-link-mobile").addEventListener("click", (ev) => {
  ev.preventDefault();
  copyUrl().then((res) => {
    $("#copy-title").addClass(res).html("Link copiado");
    window.setTimeout(() => {
      resetAnimation(res);
    }, 3000);
  });
});
document.querySelector("#copy-link").addEventListener("click", (ev) => {
  alert('taffarel');
  ev.preventDefault();
  copyUrl().then((res) => {
    $("#copy-title").addClass(res).html("Link copiado");
    window.setTimeout(() => {
      resetAnimation(res);
    }, 3000);
  });
});

/* -------------------------------------------------------------------------- */
/*                                download PDF                                */
/* -------------------------------------------------------------------------- */
function downloadPdf(url) {
  let name = "";
  let dueDate = "";
  let chargeId = "";
  let filename = "";

  if (billet.tipoCobranca == "carne") {
    name = billet.parcelaAtual.partialDadosCliente.nome;
    dueDate = billet.parcelaAtual.partialCobranca.vencimento;
    chargeId = billet.parcelaAtual.partialCobranca.cobranca;
    filename = `Cobranca_${name}_${dueDate}_${chargeId}_parcela_${billet.parcelaSelecionada}.pdf`;
  } else {
    name = billet.data.partialDadosCliente.nome;
    dueDate = billet.data.partialCobranca.vencimento;
    chargeId = billet.data.partialCobranca.cobranca;
    filename = `Boleto_${name}_${dueDate}_${chargeId}.pdf`;
  }

  var a = document.createElement("a");

  /* safari doesn't support this yet */
  if (typeof a.download === "undefined") {
    window.location = url;
  } else {
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.target = "_blank";
    a.click();
    document.body.removeChild(a);
    billet.printAll = false;
  }
  setTimeout(function () {
    $('[data-modal="download"]').modalClose();
  }, 3000);
}

$("#download").click((ev) => {
  ev.preventDefault();
  const urlPdfDownload = billet.getUrlDownloadPdf();
  downloadPdf(urlPdfDownload);
});

/* -------------------------------------------------------------------------- */
/*                                IMPRIMIR                                    */
/* -------------------------------------------------------------------------- */
var billet = new Billet();

$("#confirmar-imprimir").on("click", function (e) {
  $(this).modalClose();
  setTimeout(function () {
    billet.printComCapa = $("#checkbox-print:checked").length > 0;
    billet.printBillet();
  }, 500);
});

/* -------------------------------------------------------------------------- */
/*                                                  */
/* -------------------------------------------------------------------------- */
function broadcastDisallowBilletEvent(status) {
  var event = new CustomEvent("disallowBillet", {
    detail: status,
  });

  document.dispatchEvent(event);
}

/** --------- Get dados do boleto ------- */
$(document).ready(function () {
  billet.getData().then(function () {
    if (billet.isBilletDisallowed())
      broadcastDisallowBilletEvent(billet.data.partialCobranca.status);
  });
});
