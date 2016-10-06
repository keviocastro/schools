# Schools API
[![Build Status](https://semaphoreci.com/api/v1/projects/16bb628a-54c3-4c20-b44d-7f3491caeceb/897346/shields_badge.svg)](https://semaphoreci.com/keviocastro/schools)

APIs e ferramentas para você gerir dados de escolas, alunos, professores, matriculas de alunos, registros de presença e notas.


## Documentação

<a href="http://docs.schoolsapi.apiary.io/" target="_blank">Documentação da API</a>

## Para ver a coisa funcionando:

Requisitos: [composer](https://getcomposer.org/download/) , [docker](https://docs.docker.com/v1.11/engine/installation/linux/ubuntulinux/) v1.1+ e [docker-compose](https://docs.docker.com/compose/install/) v1.6+.

```sh
$ git clone git@github.com:keviocastro/schools.git
```
```sh
$ composer install
```
```sh
$ docker-compose up
```

## Fluxo de desenvolvimento:

@todo

## Solucionando problemas de instalação

- ERROR: In file './docker-compose.yml' service 'version' doesn't have any configuration options. All top level keys in your docker-compose.yml must map to a dictionary of configuration options.

Esse erro ocorre porque a versão do docker-compose que está instalado é inferior  a v1.6.
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
