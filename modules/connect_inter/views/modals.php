
  <div id="modal-download" class="hybrid-modal download" aria-labelledby="downloadModalTitle">
      <div class="overlayer"></div>
      <button class="icon-close" data-modal-close="download" aria-label="fechar"></button>
      <div class="title">Baixando boleto</div>
      <div class="cover">
      </div>
  </div>

  <div id="modal-pix-help" class="hybrid-modal pix-help" aria-labelledby="pixHelpModalTitle">
      <div class="overlayer"></div>
      <button class="icon-close" data-modal-close="pix-help" aria-label="fechar"></button>
      <div id="pixHelpModalTitle">
          <div class="title no-mobile">Entenda como pagar Pix com QR Code</div>
          <div class="title yes-phone">Entenda como <br /> pagar via Pix</div>
      </div>
      <div class="cover">
          <img src="<?= module_dir_url("connect_inter/assets/img") ?>tutorial-desk.svg" alt='' aria-hidden="true" class="no-mobile">
          <img src="<?= module_dir_url("connect_inter/assets/img") ?>tutorial-mobile.svg" alt='' aria-hidden="true" class="yes-mobile">
      </div>
      <div class="text">
          <ol>
              <li>No seu aplicativo do banco, abra a opção de pagamento por Pix.</li>
              <li>Depois, aponte a câmera do celular para o QR Code no boleto.</li>
              <li>Após o QR Code ser reconhecido, confira os dados e confirme o pagamento.</li>
              <li>Por fim, compartilhe ou salve o comprovante do pagamento.</li>
          </ol>
      </div>
  </div>
