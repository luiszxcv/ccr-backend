#Doutor Estrada - CCR Hackathon

Backend API CCR de gerencimanento de caminhoneiros.

## Getting Started

Uma API para gerenciamento (cadastro, exclusão, edição) de caminhoneiros na plataforma Doutor Estrada.

### Funcionalidades

Sistema de check'ins por geolocalização do usuário (pontos de checkins cadastrados manualmente, raio de 50 metros a 150km, gereciamento de conversas com WhatsApp boot e gestão usuários vinculados ao user master (transportadora ou concessionárias de rodovias);

### Installing

Para instalar basta clonar o repositório em rodar e subir em um servidor PHP juntamente com uma base de dados nova a partir do arquivo wp-config.php.

## Built With

* [Luís Santos]https://escalepro.com.br/- The web developer

## Detalhes e endpoints

Verificar quais são os checkins cadastrados na plataforma
get em http://enterprise.escalepro.com.br/wp-json/wp/v2/coordenadas


Verificar se a geolocalização está dentro da área de algum checkin
post em http://enterprise.escalepro.com.br/wp-json/wp/v2/coordenadas
com body
{
    "coordenadas":[-11.855673, 55.510861] //example
}

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Ver online
Você pode ver online em dash-adm.web.app.
