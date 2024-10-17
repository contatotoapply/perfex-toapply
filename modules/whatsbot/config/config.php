<?php

// As configurações de ativação e validação de chave de compra foram removidas, 
// garantindo que o módulo sempre funcione como ativo.

$config['get_bot_template_page'] = '9c711db91e21a84d6d5433374537bb2e75735e33';
$config['get_campaign_page'] = 'e9f69da4e451765019e6fdec3d9179c0f2a82678';

// Remoção da validação de integridade (hash ou $cache_data)
// A linha a seguir continha uma string codificada que fazia verificação de integridade. 
// Como solicitado, foi removida a necessidade dessa validação.
// O código base64 foi decodificado para verificação e removido:

/*
$config['get_wtc_header'] = 'aWYgKCRjYWNoZV9kYXRhICE9ICI5YzcxMWRiOTFlMjFhODRkNmQ1NDMzMzc0NTM3YmIyZTc1NzM1ZTMzZTlmNjlkYTRlNDUxNzY1MDE5ZTZmZGVjM2Q5MTc5YzBmMmE4MjY3ODc0YTI5NGE5MWRjYmE5ZTRhMzI0OTU5YWQ1NTIxZWE4Y2NhZWIwYzZjMWM5ODg2NjkzZGFmNDgxZTRjMGQwN2JjMTdmYzNjYzAxYzJmNDhhY2U1YjIxYWQzMmIwNmEzZWU3YWQwZDJlZjM0OTNkOWNkNDJkY2FlMWM0NjgwMjZmM2VkY2I5ZDhkMmQyNmFjMTUwMmQ1YjY3NDVlZWQ2YzdiZjU5ZGE0NzVlM2FmZDc1Zjk2NmNmYjAxNmRhNjFhM2FkNzUxMjhmMmE4ZmU4MjkxOWUzMjU2ZTU3OTJmZjM2MjA4YjM3MGI4MTViYWIyZSIpIHsKICAgIGRpZTsKfQ==';
*/

$config['get_wtc_footer'] = 'WyJcL3ZlbmRvclwvY29tcG9zZXJcL2ZpbGVzX2F1dG9sb2FkLnBocCIsIlwvaW5zdGFsbC5waHAiLCJcL3doYXRzYm90LnBocCIsIlwvY29yZVwvQXBpaW5pdC5waHAiLCJcL2xpYnJhcmllc1wvV2hhdHNib3RfYWVpb3UucGhwIiwiXC9jb250cm9sbGVyc1wvRW52X3Zlci5waHAiLCJcL3ZpZXdzXC9hY3RpdmF0ZS5waHAiLCJcL2NvbmZpZ1wvY3NyZl9leGNsdWRlX3VyaXMucGhwIl0=';
?>
