# Group Occurrence 

`Occurrence` são registros de ocorrências registrar por professor em relação aos alunos.

## Occurrence Collection [/occurences{?_q,_sort,_with}]

### Create a new Occorence [POST]

+ Request (application/json)
    + Headers
            
            authorization: <!-- include(Token.md) -->
    
    + Attributes (OccurenceFillable)

+ Response 201 (application/json) 
    
    + Attributes 
        + occurence (Occurence)
        
    

### List occurences [GET]

+ Parameters
    + _q (string, optional) - Fulltext search.
        + Members
            +comment
    + _sort (string, optional) - Nome da coluna para ordenação. 
    + _with (string) - Nome da relação a ser incluída na resposta.
        + Members
            + aboutPerson
            + level

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
    
+ Response 200 (application/json)

    + Attributes 
        + Include Paginator
        + data (array)
            + (Occurence)

## Occurence [/occurences/{id}]

### View a occurence detail [GET]

+ Parameters
    + id: 1 (number) - ID of the occurence

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
    
+ Response 200 (application/json)
    
    + Attributes 
        + Occurence (Occurence)

### Edit [PUT]

+ Parameters
    + id: 1 (number) - ID of the occurence

+ Request (application/json)

    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (OccurenceFillable)
            
+ Response 200 (application/json)
    
    + Attributes 
        + Occurence (Occurence)

### Delete [DELETE]

+ Parameters
    + id: 1 (number) - ID of the occurence
    
+ Request (application/json)
    + Headers
    
            authorization: <!-- include(Token.md) -->
    

+ Response 204