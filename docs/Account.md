# Group Auth and Account

## Request access [/auth/request-access]

`Request access` são solicitações de acesso que um usuário faz a uma conta.

Exemplo: considerando que a Escola Luz do Saber tem uma conta na sua aplicação, e o Professor Medeiros 
vai começar a utilizar o aplicativo para registrar presenças e notas dos alunos. Então o 
Professor Medeiros acessa seu aplicativo informando usuário e senha para se cadastrar. Com isso o cadastro do Professor Medeiros fica pendente até que um usuário da Escola Luz do Saber com permissão para liberação confirme seu cadastro. 

### Create a request access [POST]

+ Request
    + Headers
    
            authorization: <!-- include(Token.md) -->
    
    
+ Response 200 (application/json)
    
    + Attributes
        + request_access (object) 
            + id: 1 (number) - Identificador único da solicitação
            + user_id: auth0|57c0840deae095471aba4093 (string) - id do usuário no microservice de autentificação auth0.
            + status: 0 (number) - Situação da solicitação. 
                                0 pendente
                                1 negada
                                2 aprovada
            + created_at: `2016-08-15 20:26:39` (string) Data que a solicitação foi criada
            + updated_at: `2016-08-15 21:26:39` (string) Data da ultima alteração da solicitação



## Account settings [/account-configs]

Configurações da contado do usuário logado.

### View a account settings [GET]

+ Request
    + Headers
    
            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes
        + account_config (object)
            
            + percentage_absences_reprove (object) - `Percentual máximo de faltas que um aluno pode ter para não ser reprovado. Exemplo: Existem 200 aulas de Matématica no ano, e consideramos que percentage_absences_reprove == 15, então o aluno pode faltar no máximo 30 aulas (200*0,15).`
                + value: 20 (number)

            + grade_threshold_great (object) - `Se o aluno tiver média igual ou acima dessa nota, ele é considerado ótimo`
                + value: 9 (number)
            
            + grade_threshold_good (object) - `Se o aluno tiver média igual ou acima dessa nota, e menor do que "grade_threshold_great", ele é considerado bom.`
                + value:  6 (number)
            
            + passing_grade_threshold (object) - `Média mínima para o aluno ser aprovado em uma disciplina`
                + value: 6 (number)
