<?php

use chillerlan\QRCode\QRCode;

$company_name = get_option('companyname');
$paymentmethod_banco_inter_desativar_visualizacao_boleto_tela_pagamento =
    get_option('paymentmethod_connect_inter_desativar_visualizacao_boleto_tela_pagamento');

$pix_copia_e_cola = $invoice->cobranca->pix->pixCopiaECola ?? null;

$dados_boleto =$invoice->cobranca->boleto;

?>
<!doctype html>
<html lang="pt-br">

<head>
    <title><?= $company_name ?> - Boleto Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=9;IE=10;IE=Edge,chrome=1" />
    <meta name="description" content="">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?= module_dir_url("connect_inter/assets/css") . "style.css" ?>?v=4.0" media='all' async>
    <link rel="stylesheet" href="<?= module_dir_url("connect_inter/assets/css") . "modal.bootstrap.css" ?>?v=4.0">
    <style>
        .s-status {
            display: inline-block;
            padding: 6px 18px;
            text-transform: uppercase;
        }

        .label {
            font-size: 12px;
            font-weight: 400;
            padding: 0.4em 0.9em 0.4em;
        }

        .label-warning {
            background: 0 0;
            border: 1px solid #ff6f00;
            color: #ff6f00;
        }

        .label-danger {
            background: transparent;
            border: 1px solid #fc2d42;
            color: #fc2d42;
        }

        .label-default {
            background: transparent;
            border: 1px solid #d2d5dc;
            color: #63686f;
        }

        .label-success {
            background: transparent;
            border: 1px solid #84c529;
            color: #84c529;
        }

        .label-primary {
            background: transparent;
            border: 1px solid #28b8da;
            color: #28b8da;
        }
    </style>
    <meta />
</head>

<body class="body-loading">
    <div id="app" class="no-print">
        <main class="loading-content main-container">
            <div class="container billet-details">
                <section>
                    <div class="header">
                        <?php
                        echo "<img src='", $logo, "' alt='Logo' style='width:30%;' />";
                        ?>
                        <div class="action">
                            <div class="share yes-mobile">
                                <div class="copy-action-container">
                                    <button id="copy-link-mobile" @click="copyLink" role="button" data-bs-toggle="popover" data-bs-content="Link copiado com sucesso." aria-label="Copiar Link" class="copy">
                                        <img src="<?= module_dir_url("connect_inter/assets/img") ?>link.svg" alt='' aria-hidden="true">
                                    </button>
                                    <span class="copy-feedback">
                                        <i class="icon-check"></i>
                                        Link copiado!
                                    </span>
                                </div>
                                <button @click="shareToWhatsapp" aria-label="Compartilhar no Whatsapp">
                                    <i class="icon-whatsapp "></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="description">
                        <div class="billet-status">
                            <?= format_invoice_status($invoice->status); ?>
                            <div class="block">
                                <div class="share no-mobile" title="Copiar Link">
                                    <div class="copy-action-container">
                                        <button @click="copyLink" tabindex="0" class="btn btn-lg btn-danger" id="teste123" role="button" data-bs-toggle="popover" data-bs-content="Link copiado com sucesso.">
                                            <img src="<?= module_dir_url("connect_inter/assets/img") ?>link.svg" alt='' aria-hidden="true">
                                        </button>
                                        <span class="copy-feedback">
                                            <i class="icon-check"></i>
                                            Link copiado!
                                        </span>
                                        <button @click="shareToWhatsapp" aria-label="Compartilhar no Whatsapp">
                                            <i class="icon-whatsapp "></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- TAB -->
            <div class="billet-details payment-options unpaid" id="paymentTabs">
                <ul class="nav-tabs" role="tablist">
                    <?php
                    if ($pix_copia_e_cola) {
                    ?>
                        <li role="presentation" id="abaPix" @click="active=1" :class="{'active':active==1}">
                            <button id="qrCodeTab" data-tab="#qrCodeContent" role="tab" :class="{'active':active==1}">
                                <i class="icon-qrcode"></i>
                                QR Code Pix
                            </button>
                        </li>
                    <?php
                    }
                    if ($paymentmethod_banco_inter_desativar_visualizacao_boleto_tela_pagamento) {
                    ?>
                        <li role="presentation" id="abaBoleto" @click="active=2" :class="{'active':active==2}">
                            <button id="barCodeTab" data-tab="#codigoBarras" role="tab" :class="{'active':active==2}">
                                <i class="icon-barcode"></i>
                                Boleto
                            </button>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
            <section class="container billet-details bar-codes">
                <div class="tab-content">
                    <?php
                    if ($pix_copia_e_cola) {
                    ?>
                        <div role="tabpanel" v-show="active==1" id="qrCodeContent" class="tab-pane">

                            <div class="qr-code">
                                <div class="code">
                                    <div id="qr-copied"></div>
                                    <div id="qr-code-image">
                                        <?php
                                        echo '<img src="' . (new QRCode())->render($pix_copia_e_cola) . '" alt="QR Code" />';
                                        ?>
                                    </div>
                                </div>
                                <div class="info">
                                    <img src="<?= module_dir_url("connect_inter/assets/img") ?>logo-pix.svg" class="no-mobile">
                                    <div id="pix-codigo" class="pix-codigo sr-only" class="sr-only" aria-hidden="true">
                                    </div>
                                    <div class="pix-text">
                                        Com o QR Code Pix, você paga e recebe, com segurança, em segundos, a
                                        qualquer dia e
                                        hora.
                                    </div>
                                    <div class="action">
                                        <div class="copy-action-container">
                                            <button class="link--blue cursor-pointer" @click="copyPixCode" tabindex="0" class="btn btn-lg btn-danger" id="copiarPixCopiaECola" role="button" data-bs-toggle="popover" data-bs-content="Pix Copia e Cola copiado com sucesso." data-pix-copia-e-cola='<?= $pix_copia_e_cola ?>' style="background: #981aff !important; color: #fff;border:1px solid transparent;" id="copyQrPix">
                                                <i class="icon-copy-o"></i>
                                                Copiar Pix Copia e Cola
                                            </button>
                                            <span class="copy-feedback">
                                                <i class="icon-check"></i>
                                                QR Code Pix copiado!
                                            </span>
                                        </div>
                                        <div class="buttons">
                                            <i class="icon-question-circle"></i>
                                            <a data-bs-toggle="modal" class="cursor-pointer" data-bs-target="#exampleModal">
                                                Como pagar via Pix?
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex" style="text-align: center; margin-top:20px;">
                                <?php
                                if ($invoice->status == 2) { ?>
                                <?php
                                } else {
                                ?>
                                    <span id="aguardando_pagamento" class="text-danger-important">
                                        <div class="spinner-border text-success" role="status">
                                        </div>
                                        <span class="visually-hidden">Aguardando pagamento...</span>
                                    </span>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }
                    if ($paymentmethod_banco_inter_desativar_visualizacao_boleto_tela_pagamento) {
                    ?>
                        <div role="tabpanel" v-show="active==2" id="codigoBarras" class="tab-pane">
                            <div class="boleto-dados">
                                <div class="header justify-content-center">
                                    <div class="action text-center">
                                        <a href="<?= base_url("connect_inter/invoice/{$invoice->id}/{$invoice->hash}/baixar"); ?>" style="background: #981aff !important; color: #fff;border:1px solid transparent;">
                                            <i class="icon d-block icon-download-alt"></i>
                                            Baixar <span class="no-mobile">&nbsp;boleto</span>
                                        </a>
                                        <a class="btn-link print-billet w-100 no-mobile"
                                         style="background: #981aff !important; color: #fff;border:1px solid transparent;"
                                          id="imprimir" href="<?= base_url("connect_inter/invoice/{$invoice->id}/{$invoice->hash}/imprimir"); ?>">
                                            <i class="icon d-block icon-print-alt"></i>
                                            Imprimir boleto
                                        </a>
                                    </div>
                                </div>
                                <div style="display:flex">
                                    <canvas id="barcode" style="margin:0px auto;"></canvas>
                                </div>
                                <div id="boleto-codigo" class="boleto-codigo"><?= $dados_boleto->linhaDigitavel ?></div>
                                <div class="copy-action-container">
                                    <button class="link--blue copy" style="background: #981aff !important; color: #fff;border:1px solid transparent;" id="copyBoleto">
                                        <i class="icon-copy-o"></i>
                                        Copiar linha digitável
                                    </button>
                                    <span class="copy-feedback" style="background: #981aff !important; color: #fff;border:1px solid transparent;">
                                        <i class="icon-check"></i>
                                        Linha digitável copiada!!
                                    </span>
                                </div>
                                <div class="barcode-container no-mobile">
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="paid-container">
                    <div class="image">
                        <img src="<?= module_dir_url("connect_inter/assets/img") ?>icon-pago.svg" alt='' aria-hidden="true">
                    </div>
                    <div class="text">
                        <span class="title">Este boleto já está pago!</span>
                        <span class="description"></span>
                    </div>
                </div>
                <div class="canceled-container">
                    <div class="image">
                        <img src="<?= module_dir_url("connect_inter/assets/img") ?>icon-cancelado.svg" alt='' aria-hidden="true">
                    </div>
                    <div class="text">
                        <span class="title">Este boleto está cancelado</span>
                        <span class="description">Se o pagamento não foi feito, solicite um novo boleto ao
                            recebedor.</span>
                    </div>
                </div>
            </section>
            <div class="container billet-details">
                <section>
                    <div class="description">
                        <!-- DETALHES -->
                        <div>
                            <ul class="details-list">
                                <li class="partialCobranca">
                                    <div class="flex-between">
                                        <span class="label">Valor <span class="no-mobile">da cobrança</span></span>
                                        <span class="value value--orange valorTotal"><?= app_format_money($invoice->total, get_base_currency()->name); ?></span>
                                    </div>
                                    <div class="desconto"></div>
                                </li>
                                <li class="partialCobranca">
                                    <div class="flex-between">
                                        <span class="label">Vencimento</span>
                                        <span class="value vencimento"><?= _d($invoice->duedate); ?></span>
                                    </div>
                                </li>
                                <li class="partialCobranca">
                                    <div class="flex-between">
                                        <span class="label">Nº da cobrança</span>
                                        <span class="value billet-number"><?= format_invoice_number($invoice->id); ?></span>
                                    </div>
                                </li>
                                <li class="partialDadosCliente" title="<?= $invoice->client->company; ?>">
                                    <div class="flex-between">
                                        <span class="label">Pagador</span>
                                        <span class="value name"><?= $invoice->client->company; ?></span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
            <!-- ITENS -->
            <div class="container billet-details gray centered">
                <section class="section-2 bill-details" id="demonstrativo" tabindex="-1">
                    <!-- <div class="overlayer"></div> -->
                    <ul class="ticket-list order-lg-3" tabindex="-1">
                        <li class="list-header">
                            <div class="item-desc col-sm-6 label">Descrição</div>
                            <div class="item-unit col-sm-3 label text-right no-mobile">Valor unitário</div>
                            <div class="item-qty col-sm-1 label text-right no-mobile">Qtd</div>
                            <div class="item-subtotal col-sm-2 label text-right no-mobile">Subtotal</div>
                        </li>
                        <?php
                        foreach ($invoice->items as $item) {
                        ?>
                            <li class="no-mobile">
                                <div class="item-desc col-sm-6 value">
                                    <strong><?= $item['description']; ?><br /></strong>
                                    <?= $item['long_description']; ?>
                                </div>
                                <div class="item-subtotal col-sm-3 value text-right">
                                    <?= app_format_money($item['rate'], get_base_currency()->name); ?></div>
                                <div class="item-qty col-sm-1 value text-right"><?= $item['qty']; ?>
                                    <span class="yes-mobile-inline">x</span>
                                </div>
                                <div class="item-unit col-sm-2 value text-right">
                                    <?= app_format_money($item['qty'] * $item['rate'], get_base_currency()->name); ?></div>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                    <hr class="separator" />
                    <ul class="billet-values">
                        <li>
                            <div class="col-sm-6 label">Valor total</div>
                            <div class="col-sm-6 text-right total">
                                <?= app_format_money($invoice->total, get_base_currency()->name); ?></div>
                        </li>
                    </ul>
                    <hr class="separator" />
                </section>
            </div>
        </main>
        <footer class="billet-footer">
            <p><?= get_option('companyname') ?></p>
        </footer>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span class="title no-mobile">Entenda como pagar Pix com QR Code</span>
                        <span class="title yes-phone">Entenda como <br /> pagar via Pix</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="cover">
                        <img src="<?= module_dir_url("connect_inter/assets/img") ?>tutorial-desk.svg" alt='' aria-hidden="true" class="no-mobile">
                        <img src="<?= module_dir_url("connect_inter/assets/img") ?>tutorial-mobile.svg" alt='' aria-hidden="true" class="yes-mobile">
                    </div>
                    <div class="text mt-4">
                        <ol class="list-group list-group-numbered">
                            <li class="list-group-item">No seu aplicativo do banco, abra a opção de pagamento por Pix.
                            </li>
                            <li class="list-group-item">Depois, aponte a câmera do celular para o QR Code no boleto.
                            </li>
                            <li class="list-group-item">Após o QR Code ser reconhecido, confira os dados e confirme o
                                pagamento.</li>
                            <li class="list-group-item">Por fim, compartilhe ou salve o comprovante do pagamento.</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="link--blue" style="
                    background: rgb(152, 26, 255) !important; color: rgb(255, 255, 255); border: 1px solid transparent;
                    " data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="<?= module_dir_url("connect_inter/assets/js"); ?>jquery.min.js?v=3.0"></script>
    <script src="<?= module_dir_url("connect_inter/assets/js"); ?>billet.class.js?v=3.0"></script>
    <script src="<?= module_dir_url("connect_inter/assets/js"); ?>billet.modal.js?v=3.0"></script>
    <script src="<?= module_dir_url("connect_inter/assets/js"); ?>billet.polyfill.js?v=3.0"></script>
    <script src="<?= module_dir_url("connect_inter/assets/js"); ?>billet.active.class.js?v=3.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://static.cloudflareinsights.com/beacon.min.js/v52afc6f149f6479b8c77fa569edb01181681764108816">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                active: "<?= $pix_copia_e_cola ? 1 : 2 ?>",
                link: "<?= $_SERVER['HTTP_REFERER'] ?>",
                myPopover: null,
                copiarPixCopiaEColaPopover: null,
            },
            mounted: function() {


                var exampleEl = document.getElementById('teste123')
                this.myPopover = new bootstrap.Popover(exampleEl)

                var copiarPixCopiaECola = document.getElementById('copiarPixCopiaECola')
                this.copiarPixCopiaEColaPopover = new bootstrap.Popover(copiarPixCopiaECola)

                $("#barcode").JsBarcode("<?= $dados_boleto->linhaDigitavel ?>", {
                    displayValue: false
                });

                function copyBoleto() {
                    let tmpField = document.createElement("textarea");
                    $(".bar-code-number").addClass("copied");
                    setTimeout(function() {
                        $(".bar-code-number").removeClass("copied");
                    }, 750);
                    tmpField.value = document
                        .getElementById("boleto-codigo")
                        .innerText.match(/\d/g)
                        .join("");
                    document.body.append(tmpField);
                    tmpField.select();
                    document.execCommand("copy");
                    tmpField.remove();
                }

                document.querySelector("#copyBoleto").addEventListener("click", copyBoleto);

                this.verificarPagamento();
            },
            methods: {
                verificarPagamento() {
                    const invoiceId = "<?= $invoice->id ?>";
                    const invoiceHash = "<?= $invoice->hash ?>";
                    let invoiceStatus = "<?= $invoice->status ?>";
                    let intervalId;
                    if (invoiceStatus != 2) {
                        intervalId = setInterval(() => {
                            $.ajax({
                                'url': `/connect_inter/invoice/${invoiceId}/${invoiceHash}/verificar_pagamento_efetuado`
                            }).done(function(data) {
                                if (data == '1') {
                                    $('.s-status').html('PAGO').addClass(
                                        'invoice-status-2 label-success');
                                    clearInterval(intervalId); // Interrompe o setInterval
                                    $('#aguardando_pagamento').html('Pago com sucesso.')
                                        .removeClass('text-danger-important').addClass(
                                            'text-success-important');
                                }
                            });
                        }, 3000);
                    }
                },
                copyPixCode() {
                    const self = this;
                    let tmpField = document.createElement("textarea");
                    $("#qr-copied").addClass("copied");
                    setTimeout(function() {
                        $("#qr-copied").removeClass("copied");
                    }, 750);

                    tmpField.value = "<?= $pix_copia_e_cola ?>";
                    document.body.append(tmpField);
                    tmpField.select();
                    document.execCommand("copy");
                    tmpField.remove();


                    function giveFeedback(trigger) {
                        const container = trigger.parent();
                        const feedback = container.find('.copy-feedback');
                        const durationInSeconds = 2;

                        feedback.addClass('active');

                        setTimeout(() => {
                            feedback.removeClass('active');
                        }, durationInSeconds * 1000);
                    }

                    const thisTrigger = $(this);
                    giveFeedback(thisTrigger);

                    setTimeout(() => {
                        self.copiarPixCopiaEColaPopover.hide();
                    }, 2000);

                },
                copyLink() {
                    const self = this;
                    const copy = document.createElement("textarea");
                    document.body.appendChild(copy);
                    copy.value = document.location.href;
                    //copy
                    copy.select();
                    document.execCommand("copy");
                    copy.setSelectionRange(0, 0, "none");
                    //remove
                    document.body.removeChild(copy);
                    setTimeout(() => {
                        self.myPopover.show()
                    }, 100);
                    setTimeout(() => {
                        self.myPopover.hide()
                    }, 2000);
                },
                shareToWhatsapp() {

                    function detectar_mobile() {
                        var check = false; //wrapper no check
                        (function(a) {
                            if (
                                /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(
                                    a
                                ) ||
                                /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(
                                    a.substr(0, 4)
                                )
                            )
                                check = true;
                        })(navigator.userAgent || navigator.vendor || window.opera);
                        return check;
                    }

                    // Defina a URL que você deseja compartilhar
                    var url = document.location.href;

                    // Abra a janela de compartilhamento do WhatsApp
                    if (detectar_mobile()) {
                        window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(url));
                    } else {
                        window.open('https://web.whatsapp.com/send?text=' + encodeURIComponent(url));
                    }
                }
            },
        });
    </script>
</body>

</html>
