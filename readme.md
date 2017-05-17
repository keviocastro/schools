# Schools API
[![Build Status](https://semaphoreci.com/api/v1/projects/16bb628a-54c3-4c20-b44d-7f3491caeceb/1311424/badge.svg)](https://semaphoreci.com/keviocastro/schools)

APIs e ferramentas para você gerir dados de escolas, alunos, professores, matriculas de alunos, registros de presença e notas.


## Documentação

<a href="http://docs.schoolsapidev.apiary.io/" target="_blank">Documentação da API</a>

## Para ver a coisa funcionando:

Requisitos: [docker](https://docs.docker.com/v1.11/engine/installation/linux/ubuntulinux/) v1.1+ e [docker-compose](https://docs.docker.com/compose/install/) v1.6+.

```sh
$ git clone git@github.com:keviocastro/schools.git
```
```sh
$ docker-compose up
```

## Fluxo de desenvolvimento:

O fluxo de desenvolvimento desse aplicativo foi bolado para focar na qualidade da API, sempre utilizando o conceito de Design-first e colaboração no desenvolvimento.
Nós utilizamos desenvolvimento orientado a testes (https://pt.wikipedia.org/wiki/Test_Driven_Development) com a inclusão de uma documentação viva e dinamica, porque acreditamos que quando decidimos sobre o CONTRATO antes de ser desenvolvido a solução, tende a levar a melhores designs API.
Consideramos a documentação como nosso contrato e como tal, ele ter ser seguido e testado.

Vamos ao fluxo:

1. Escreve seu contrato/documentação do recurso que vai desenvolver no arquivo apiary.apib, e compile sua documentação utilizando o comando ``` $ apiary preview```.
2. Escreva seu teste unitário no diretório testes/ para a ação do controllador que vai implementar seu recurso.
3. Desenvolva o recurso e atualize seu teste quando necessário. Se você precisar desenvolver outras classes/metodos escreva o teste unitário para eles também utilizando TDD.
4. Quando estiver pronto e seu teste unitário estiver VERDE, execute o comando para testar se sua implementação esta de acordo com o contrato que você escreveu. O comando para isso é este: ``` $ dredd```.
5. Agora você pode fazer seu commit e push. Depois de enviar seu código para esse repositório, verifique se o servidor de integração continua concluiu o build com sucesso Você pode vericar o status do build do seu commit no icone no alto desta documentação ou em https://semaphoreci.com/keviocastro/schools e também será notificado no canal code na nossa conta do chat Slack. 
6. Pronto, agora é só pegar outro café e assistir uma piada do Paulinho Gogó e Ceará no Youtube. 


Links úteis:
https://help.apiary.io/tools/automated-testing/testing-local/ Ferramenta de testes da documentação
https://apiblueprint.org/ Syntax da documentação
http://martinfowler.com/bliki/TestDrivenDevelopment.html Um artido do mestre falando sobre TDD.

## Debug

Primeiramente, você precisa definir qual o ip do seu computador (ou host onde a IDE está em execução), no arquivo docker-compose-override.yml na sessão "XDEBUG_CONFIG: remote_host=192.168.0.19".

Depois você precisa configurar sua IDE com o mapeamento de caminhos do seu computador e do container (servidor web) desse projeto. Nesse repositório existem 2 arquivos de IDEs, PhpStorm (no diretorio oculto .idea) e sublime text (no arquivo https://github.com/keviocastro/schools/blob/master/schools.sublime-project). Se você estiver utilizando alguma dessas IDEs, basta abrir seu projeto com esses arquivos, e seu ambiente para debug estará configurado.

No Sublime Text você precisa instalar o plugin: https://github.com/martomo/SublimeTextXdebug.

## Solucionando problemas de instalação

- ERROR: In file './docker-compose.yml' service 'version' doesn't have any configuration options. All top level keys in your docker-compose.yml must map to a dictionary of configuration options.

Esse erro porque a versão do docker-compose que está instalado é inferior  a v1.6.
Execute os comandos para atualizar:

```sh
$ sudo -i
$ apt-get autoremove docker-compose
$ rm -rf /usr/local/bin/docker-compose
$ curl -L https://github.com/docker/compose/releases/download/1.8.0/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
$ chmod +x /usr/local/bin/docker-compose
```

Pronto, agora você pode verificar se a versão do docker-compose esta correta:

```sh
$ docker-compose --version
```
