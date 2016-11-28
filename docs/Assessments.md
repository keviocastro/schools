# Group Assessments

`Assessments` são as definições das avaliações que um professor precisar aplicar
aos seus alunos durante uma fase do ano.
Exemplo: No 1º Bimestre o professor precisa avaliar seus alunos com duas provas (N1 e N2),
e a nota do aluno no 1º Bimestre será composta pela conta (N1*0,4)+(N2*0,6)=NOTA_DO_BIMESTRE.

## Assessments Collection [/assessments{?q,sort,_with
}]

### List assessments [GET]

+ Parameters
    + q (string, optional) - Fulltext search.
    + sort (string, optional) - Nome da coluna para ordenação. 
    + _with
 (string) - Nome da relação a ser incluída na resposta.
        + Members
            + schoolCalendarPhase
            + schoolCalendarPhase.schoolCalendar

+ Request 
    + Headers

            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + Include Paginator
        + data (array)
            + (Assessment)
