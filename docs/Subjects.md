# Group Subjects 

`Subjects` São regristro das disciplinas .

## Subjects Collection [/subjects{?_q,_sort,_with}]

### Create a new Occorence [POST]

+ Request (application/json)
    + Headers
            
            authorization: <!-- include(Token.md) -->
    
    + Attributes (SubjectFillable)

+ Response 201 (application/json) 
    
    + Attributes 
        + Subject (Subject)
        
### List Subjects [GET]

+ Parameters
    + _q (string, optional) - Fulltext search.
        + Members
            +name
    + _sort (string, optional) - Nome da coluna para ordenação.
    + _with (string) - Não possui relacao.

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
    
+ Response 200 (application/json)

    + Attributes 
        + Include Paginator
        + data (array)
            + (Subject)

## Subject [/subjects/{id}]

### View a Subject detail [GET]

+ Parameters
    + id: 1 (number) - ID of the Subject

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
    
+ Response 200 (application/json)
    
    + Attributes 
        + Subject (Subject)

### Edit [PUT]

+ Parameters
    + id: 1 (number) - ID of the Subject

+ Request (application/json)

    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (SubjectFillable)
            
+ Response 200 (application/json)
    
    + Attributes 
        + Subject (Subject)

### Delete [DELETE]

+ Parameters
    + id: 1 (number) - ID of the Subject

+ Request (application/json)
    + Headers
    
            authorization: <!-- include(Token.md) -->
    

+ Response 204