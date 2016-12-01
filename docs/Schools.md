# Group Schools

'Schools' são as escolas associadas a uma conta.
'School Class' são as turmas de uma escola que existem em um determinado ano letivo/calendario escolar.

## Schools Collection [/schools{?q,sort}]

### List Schools [GET]

+ Parameters
    + q (string, optional) - Fulltext search.
    + sort (string, optional) - Nome da coluna para ordenação acendente.
        
+ Request
    + Headers
            
            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + Include (Paginator)
        + data (array)
            + (School)

### Create a New School [POST]

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->

+ Request (application/json)

    + Attributes (SchoolFillable)

+ Response 201 (application/json)

    + Headers

            Location: /schools/1

    + Attributes 
        + school (School)
            
## School [/schools/{school_id}]

### View a school detail [GET]

+ Parameters
    + school_id: 1 (number) - ID of the School

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->

+ Response  200 (application/json)

    + Attributes 
        + school (School)
        

### Edit [PUT]

+ Parameters
    + school_id: 1 (number) - ID of the School

+ Request (application/json)

    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (SchoolFillable)
            
+ Response 200 (application/json)
    
    + Attributes 
        + school (School)


### Delete [DELETE]

+ Parameters
    + school_id: 1 (number) - ID of the School

+ Request (application/json)

    + Headers
            
            authorization: <!-- include(Token.md) -->

+ Response 204






## School Classes Collection [/school-classes{?q,sort,_with}]

### List School Classes [GET]

+ Parameters
    + q (string, optional) - Fulltext search.
    + sort (string, optional) - Nome da coluna para ordenação. 
    + _with (string, optional) - Nome da relação a ser incluída na resposta.
        + Members 
            + grade
            + shift

+ Request (application/json)

    + Headers
            
            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + Include Paginator
        + data (array) 
            + (SchoolClass)
    
### Create a new school class [POST]

+ Request (application/json)
    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (SchoolClassFillable)
            
+ Response 200 (application/json) 
    
    + Attributes 
        + school_class (SchoolClass)
            

## School Class [/school-classes/{school_class_id}{?_with}]

+ Parameters
    - school_class_id: 1 (number) - ID of the school class

### View a school class detail [GET]

+ Parameters
    + _with
 (string, optional) - Nome da relação a ser incluída na resposta.
        + Members 
            + grade
            + shift

+ Request 
    + Headers

            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + school_class (SchoolClass)


### Edit [PUT]

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (SchoolClassFillable)
            
+ Response 200 (application/json)
    
    + Attributes 
        + school_class (SchoolClass)

            
### Delete [DELETE]

+ Response 204



## School Calendar Collection [/school-calendars{?q,sort,_with}]

`School Calendar` é considerado nessa api como a definição do ano letivo,
contendo fases fases avaliativas do ano (Ex.: bimestres), feriados, férias,
inicio e fim do ano.

### List School Calendars [GET]

+ Parameters
    + q (string, optional) - `Fulltext search. 
        <!-- Existem mais parametros para filtragem de dados. -->
        <!-- Veja as possibilidades  -->
        <!-- [Filtering]#https://github.com/keviocastro/laravel-api-handler#filtering` -->

    + sort (string, optional) - Nome da coluna para ordenação. 
    + _with (string, optional) - Nome da relação a ser incluída na resposta.
        + Members 
            + schoolClasses - Turmas do ano letivo.
            + phases - Fases avaliativas do ano letivo. Recurso (SchoolCalendarPhase)

+ Request (application/json)

    + Headers
            
            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + Include Paginator
        + data (array) 
            + (SchoolCalendar)
    
### Create a new school calendar [POST]

+ Request (application/json)
    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (SchoolCalendarFillable)
            
+ Response 200 (application/json) 
    
    + Attributes 
        + school_calendar (SchoolCalendar)
            

## School Calendar [/school-calendars/{school_calendar_id}{?_with
}]

+ Parameters
    - school_calendar_id: 1 (number) - ID of the school class

### View a school calendar detail [GET]

+ Parameters
    + _with (string, optional) - Nome da relação a ser incluída na resposta.
        + Members 
            + schoolClasses - Turmas do ano letivo
            + schoolCalendarPhases - Fases avaliativas do ano letivo

+ Request 
    + Headers

            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + school_class (SchoolClass)


### Edit [PUT]

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (SchoolCalendarFillable)
            
+ Response 200 (application/json)
    
    + Attributes 
        + school_calendar (SchoolCalendar)

### Delete [DELETE]

+ Response 204

