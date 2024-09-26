Refatorações:

# Arquivo: modules/asaas/asaas.php

- Removi funções do arquivo principal que não estavam sendo usadas para deixar o código mais limpo.
- Removi comentários também para deixar o código mais limpo, pois isso deixa código sujo.
- Definir uma constante para o nome do módulo, pois o nome do módulo é usado em vários lugares e se o nome mudar, teria que mudar em vários lugares.
- Movi todos os _hooks_ (ganchos) para o topo, pois isso deixa o código mais limpo e claro.
- Removi variáveis que não estavam sendo usadas.

1h30

# Arquivo: modules/asaas/controllers/Asaas.php
- Removi comentários também para deixar o código mais limpo, pois isso deixa código sujo.
- Criei duas opções, uma denominada: ambiente_url_sandbox; outra, ambiente_url_producao

Foram adicionados dois métodos à classe Asaas_gateway.php:
```php
public function getUrlBase()
{
    return $this->getSetting('sandbox')
        ? self::AMBIENTE_URL_SANDBOX
        : self::AMBIENTE_URL_PRODUCAO;
}

public function getApiKey()
{
    return $this->getSetting('sandbox')
        ? $this->decryptSetting('api_key_sandbox')
        : $this->decryptSetting('api_key');
}
```

# No arquivo: `modules/asaas/controllers/Main.php`, criei duas variáveis e defini os valores conforme o ambiente:
```php
Criei duas variáveis:
```php
protected $apiKey;
protected $apiUrl;
public function __construct()
{
    parent::__construct();
    $this->load->library('asaas_gateway');
    $this->load->helper('general');
    $this->apiKey  = $this->asaas_gateway->getApiKey();
    $this->apiUrl  = $this->asaas_gateway->getUrlBase();
}

```

# Mudanças signficativas 2024-02-13 16:51:14
- Alteração do arquivo de callback: Callback para Asaas_invoice_callback


Ao atualizar a fatura no portal do Asaas, atualiza no perfex.

[ ] - Adicionar o campo de invoice_parent (fatura mãe) à tabela de invoices.
[ ] - Salvar o id da fatura pai nas filhas.
