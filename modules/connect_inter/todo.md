
[ ] - Verificar status de pagamento.
[x] - Mostrar o Qr Code na tela de pagamento quando o gateway selecionado for PIX.
[x] - Testar Qr Code enviado no Email ao duplicar fatura
[x] - Testar Qr Code enviado no Email ao copiar fatura
[x] - Testar Qr Code enviado no Email ao cancelar fatura
[x] - Testar Qr Code enviado no Email ao marcar como cancelado
[x] - Estudar o código
[x] - Testar massivamente: duplicar, copiar, cancelar, editar, excluir, marcar como cancelado, botões Salvar.
[x] - Adicionar os itens da fatura ao boleto no Banco Inter
[x] - Multa
[x] - PDF
[x] - Juros
[x] - Atualizar cobrança ao atualizar no Perfex
[x] - Criar cobrança ao criar fatura
[x] - Cancelar cobrança no Banco Inter ao cancelar no Perfex
[x] - Faltar excluir o webhook
[x] - Criar lógica para adicionar o webhook manualmente.
[x] - Testar Qr Code enviado no Email ao criar fatura
[x] - Testar Qr Code enviado no Email ao atualizar fatura
[x] - Tesar envio de PDF

Webhook - CANCELADO
```json
[
  {
    "codigoSolicitacao": "12f13035-8bf4-4e0c-9ddb-d123b2f4810a",
    "seuNumero": "248_1008_83",
    "situacao": "CANCELADO",
    "dataHoraSituacao": "2024-09-12T14:13:41.918Z",
    "nossoNumero": "6386213760",
    "codigoBarras": "00000000638684117108613633642458213495287107",
    "linhaDigitavel": "00000000638684629534817183117230760298111057330",
    "txid": "6386841726150421000mzglhOavdm2lNjAV",
    "pixCopiaECola": "000201010212261010014BR.GOV.BCB.PIX2579cdpj-sandbox.partners.uatinter.co/pj-s/v2/cobv/b60627758cc243debdb5c1d2fbc298af52040000530398654045.565802BR5901*6013Belo Horizont61089999999962070503***6304173A"
  }
]
```

Webhook - RECEBIMENTO VIA PIX

```json
[
  {
    "codigoSolicitacao": "092ea5e5-0f0f-4f8a-a1b9-6af53a06324d",
    "seuNumero": "248_1007_82",
    "situacao": "RECEBIDO",
    "dataHoraSituacao": "2024-09-12T14:37:13.047Z",
    "valorTotalRecebido": "6.02",
    "origemRecebimento": "PIX",
    "nossoNumero": "6386252871",
    "codigoBarras": "00000000638684743047215170083499838836566935",
    "linhaDigitavel": "00000000638684166757471231070004153715560395363",
    "txid": "6386841726132479000wu09f2FnJ5fZ6IGC",
    "pixCopiaECola": "000201010212261010014BR.GOV.BCB.PIX2579cdpj-sandbox.partners.uatinter.co/pj-s/v2/cobv/cb999ca77d314db19b9676a1d77d816d52040000530398654046.025802BR5901*6013Belo Horizont61089999999962070503***63041278"
  }
]
```
