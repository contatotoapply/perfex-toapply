const paymentOptionsTriggers=$('.payment-options .list .item .title');const paymentOptionsItemList=$('.payment-options .list .item');const paymentOptionsListSize=paymentOptionsItemList.length;const paymentOptionsTabWidth=100/paymentOptionsListSize;function switchPaymentOptionTab(){const selectedItem=$(this).parent();paymentOptionsItemList.each(function(_,item){$(item).removeClass('active')});selectedItem.addClass('active');emphasizeTab();}
function disallowBillet(status){const messageByStatus={paid:{image:'../../../img/responsive-charges-paid.svg',title:'O boleto está pago!',message:'O pagamento já foi efetuado, caso tenha alguma'+
'<br class="no-mobile"> dúvida, entre em contato com o recebedor.'},canceled:{image:'../../../img/responsive-charges-canceled.svg',title:'O boleto está cancelado!',message:'Caso ainda não tenha efetuado o pagamento,'+
'<br class="no-mobile"> solicite um novo boleto ao recebedor.'}};const components=messageByStatus[status];const statusMessageTemplate=`
    <span class="billet-status">
      <img src='${components.image}'/>
      <h4>${components.title}</h4>
      <p>${components.message}</p>
    </span>
  `;const paymentOptions=$('.payment-options .list .item .content');paymentOptions.each(function(_,item){$(item).html(statusMessageTemplate);});const elementsToHide=$('.hide-if-billet-disallowed');elementsToHide.each(function(_,item){$(item).css('display','none');});}
$(document).ready(function(){paymentOptionsTriggers.click(switchPaymentOptionTab);paymentOptionsItemList.css('width',`${paymentOptionsTabWidth}%`);document.addEventListener('disallowBillet',function(event){disallowBillet(event.detail);});});