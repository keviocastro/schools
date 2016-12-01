# Group People

## Students Collection [/students{?_q,_sort,_with}]

### List students [GET]

+ Parameters
    + _q (string, optional) - Fulltext search
    + _sort (string, optional) - Nome da coluna para ordenação. 
    + _with (string, optional) - Nome da relação a ser incluida na resposta
        + Members
            + studentResponsible
            + person
            + schoolClass
            + schoolClass.grade
            + schoolClass.shift

+ Request (application/json)
    + Headers
            
            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + Include Paginator 
        + data (array)
            + (Student)
